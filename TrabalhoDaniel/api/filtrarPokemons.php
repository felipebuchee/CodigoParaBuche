<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// limpador de buffers
while (ob_get_level()) ob_end_clean();

// Headers JSON e cache
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

require_once(__DIR__ . "/../util/conexao.php");

try {
    // Pegar parâmetro de região
    $regiao = isset($_GET['regiao']) ? trim($_GET['regiao']) : '';
    
    $conexao = Conexao::getConnection();
    
    if ($regiao !== '') {
        $sql = "SELECT p.*, r.nome as nome_regiao 
                FROM pokemons p 
                JOIN regioes r ON r.id = p.id_regiao 
                WHERE r.nome = :regiao
                ORDER BY p.nome";
        $stmt = $conexao->prepare($sql);
        $stmt->bindValue(':regiao', $regiao, PDO::PARAM_STR);
    } else {
        $sql = "SELECT p.*, r.nome as nome_regiao 
                FROM pokemons p 
                JOIN regioes r ON r.id = p.id_regiao 
                ORDER BY p.nome";
        $stmt = $conexao->prepare($sql);
    }
    
    $stmt->execute();
    $pokemons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    
    foreach ($pokemons as $p) {
        // Buscar tipos do pokemon
        $sqlTipos = "SELECT t.nome FROM tipos t 
                     JOIN pokemon_tipos pt ON t.id = pt.id_tipo 
                     WHERE pt.id_pokemon = :pokemon_id";
        $stmtTipos = $conexao->prepare($sqlTipos);
        $stmtTipos->bindValue(':pokemon_id', $p['id'], PDO::PARAM_INT);
        $stmtTipos->execute();
        $tipos = $stmtTipos->fetchAll(PDO::FETCH_COLUMN);
        
        $data[] = [
            "id" => $p['id'],
            "nome" => $p['nome'],
            "peso" => $p['peso'],
            "altura" => $p['altura'],
            "cor" => $p['cor'],
            "tipos" => $tipos,
            "regiao" => $p['nome_regiao'],
            "imagem" => $p['imagem']
        ];
    }
    
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    http_response_code(500);
    error_log("Erro ao filtrar pokémons: " . $e->getMessage());
    echo json_encode(["error" => "Erro ao filtrar pokémons"]);
} 
