<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once(__DIR__ . "/../controller/pokemonController.php");

try {
    // Pegar parâmetro de região
    $regiao = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';
    
    $controller = new PokemonController();
    
    // Usar método do controller/DAO para filtrar
    if ($regiao !== '') {
        $lista = $controller->listarPorRegiao($regiao);
    } else {
        $lista = $controller->listar();
    }
    
    $data = [];
    
    foreach ($lista as $p) {
        $tipos = [];
        if (method_exists($p, 'getTipos')) {
            foreach ($p->getTipos() as $t) {
                $tipos[] = method_exists($t, 'getNome') ? $t->getNome() : (string)$t;
            }
        }
        
        $data[] = [
            "id" => method_exists($p, 'getId') ? $p->getId() : null,
            "nome" => method_exists($p, 'getNome') ? $p->getNome() : null,
            "peso" => method_exists($p, 'getPeso') ? $p->getPeso() : null,
            "altura" => method_exists($p, 'getAltura') ? $p->getAltura() : null,
            "cor" => method_exists($p, 'getCor') ? $p->getCor() : null,
            "tipos" => $tipos,
            "regiao" => (method_exists($p, 'getRegiao') && $p->getRegiao()) ? (method_exists($p->getRegiao(), 'getNome') ? $p->getRegiao()->getNome() : (string)$p->getRegiao()) : null,
            "imagem" => method_exists($p, 'getImagem') ? $p->getImagem() : null
        ];
    }
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erro ao filtrar pokémons: " . $e->getMessage());
    echo json_encode(["error" => "Erro ao filtrar pokémons"]);
} catch (Throwable $e) {
    http_response_code(500);
    error_log("Erro geral: " . $e->getMessage());
    echo json_encode(["error" => "Erro interno do servidor"]);
} 
