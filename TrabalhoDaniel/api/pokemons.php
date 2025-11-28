<?php
// sem saída antes daqui
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/pokemons_api_errors.log');
error_reporting(E_ALL);

// limpa buffers que possam ter gerado HTML
while (ob_get_level()) ob_end_clean();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once(__DIR__ . "/../controller/pokemonController.php");

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

        // Usar apenas a imagem do banco de dados
        $imagem = method_exists($p, 'getImagem') ? $p->getImagem() : null;

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