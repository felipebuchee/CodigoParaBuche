<?php
// sem saída antes daqui
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/pokemons_api_errors.log');
error_reporting(E_ALL);

// limpa buffers que possam ter gerado HTML
while (ob_get_level()) ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

require_once(__DIR__ . "/../controller/pokemonController.php");

function fetchImageForPokemon($idOrName) {
    $idOrName = urlencode($idOrName);
    $url = "https://pokeapi.co/api/v2/pokemon/{$idOrName}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 6);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_USERAGENT, 'TrabalhoPokemon/1.0');
    $resp = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false || $http !== 200) {
        error_log("PokeAPI fetch failed for {$idOrName}: HTTP {$http} {$err}");
        return null;
    }

    $json = json_decode($resp, true);
    if (!$json) return null;

    if (!empty($json['sprites']['other']['official-artwork']['front_default'])) {
        return $json['sprites']['other']['official-artwork']['front_default'];
    }
    if (!empty($json['sprites']['front_default'])) {
        return $json['sprites']['front_default'];
    }
    return null;
}

try {
    $controller = new PokemonController();
    $lista = $controller->listar();

    $data = [];
    foreach ($lista as $p) {
        $tipos = [];
        if (method_exists($p, 'getTipos')) {
            foreach ($p->getTipos() as $t) {
                $tipos[] = method_exists($t, 'getNome') ? $t->getNome() : (string)$t;
            }
        }

        $identifier = null;
        if (method_exists($p, 'getId') && $p->getId()) $identifier = $p->getId();
        elseif (method_exists($p, 'getNome') && $p->getNome()) $identifier = strtolower(str_replace(' ', '-', $p->getNome()));

        $imagem = $identifier ? fetchImageForPokemon($identifier) : null;

        $data[] = [
            "id" => method_exists($p, 'getId') ? $p->getId() : null,
            "nome" => method_exists($p, 'getNome') ? $p->getNome() : null,
            "peso" => method_exists($p, 'getPeso') ? $p->getPeso() : null,
            "altura" => method_exists($p, 'getAltura') ? $p->getAltura() : null,
            "cor" => method_exists($p, 'getCor') ? $p->getCor() : null,
            "tipos" => $tipos,
            "regiao" => (method_exists($p, 'getRegiao') && $p->getRegiao()) ? (method_exists($p->getRegiao(), 'getNome') ? $p->getRegiao()->getNome() : (string)$p->getRegiao()) : null,
            "imagem" => $imagem
        ];
    }

    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    error_log("API error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo json_encode(["error" => "Internal server error"]);
}