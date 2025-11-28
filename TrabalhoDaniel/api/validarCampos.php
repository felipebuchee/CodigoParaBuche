<?php

require_once(__DIR__ . "/../service/pokemonService.php");
require_once(__DIR__ . "/../model/pokemon.php");
require_once(__DIR__ . "/../model/tipos.php");
require_once(__DIR__ . "/../model/regioes.php");

header('Content-Type: application/json');

// Receber dados via POST
$campo = isset($_POST['campo']) ? trim($_POST['campo']) : '';
$valor = isset($_POST['valor']) ? $_POST['valor'] : '';

$response = ['valido' => false, 'mensagem' => ''];

// Criar instância do service para reutilizar as validações
$pokemonService = new PokemonService();

// Criar um pokemon temporário para validação
$pokemonTemp = new Pokemon();
$pokemonTemp->setId(0);

switch($campo) {
    case 'nome':
        $pokemonTemp->setNome($valor);
        $pokemonTemp->setPeso(1); // valores dummy para não gerar erros nas outras validações
        $pokemonTemp->setAltura(1);
        $pokemonTemp->setCor("dummy");
        
        $regiaoTemp = new Regioes();
        $regiaoTemp->setId(1);
        $pokemonTemp->setRegiao($regiaoTemp);
        
        $tipoTemp = new Tipos();
        $tipoTemp->setId(1);
        $pokemonTemp->setTipos([$tipoTemp]);
        
        $erros = $pokemonService->validarPokemon($pokemonTemp);
        
        // Filtrar apenas erros relacionados ao nome
        $errosNome = array_filter($erros, function($erro) {
            return strpos($erro, 'nome') !== false || strpos($erro, 'Nome') !== false;
        });
        
        if(count($errosNome) === 0) {
            $response['valido'] = true;
            $response['mensagem'] = 'Nome válido!';
        } else {
            $response['mensagem'] = implode(' ', $errosNome);
        }
        break;
        
    case 'peso':
        $valorNumerico = ($valor !== NULL && $valor !== "" && is_numeric($valor)) ? (float)$valor : NULL;
        
        $pokemonTemp->setNome("dummy");
        $pokemonTemp->setPeso($valorNumerico);
        $pokemonTemp->setAltura(1);
        $pokemonTemp->setCor("dummy");
        
        $regiaoTemp = new Regioes();
        $regiaoTemp->setId(1);
        $pokemonTemp->setRegiao($regiaoTemp);
        
        $tipoTemp = new Tipos();
        $tipoTemp->setId(1);
        $pokemonTemp->setTipos([$tipoTemp]);
        
        $erros = $pokemonService->validarPokemon($pokemonTemp);
        
        $errosPeso = array_filter($erros, function($erro) {
            return strpos($erro, 'peso') !== false || strpos($erro, 'Peso') !== false;
        });
        
        if(count($errosPeso) === 0) {
            $response['valido'] = true;
            $response['mensagem'] = 'Peso válido!';
        } else {
            $response['mensagem'] = implode(' ', $errosPeso);
        }
        break;
        
    case 'altura':
        $valorNumerico = ($valor !== NULL && $valor !== "" && is_numeric($valor)) ? (float)$valor : NULL;
        
        $pokemonTemp->setNome("dummy");
        $pokemonTemp->setPeso(1);
        $pokemonTemp->setAltura($valorNumerico);
        $pokemonTemp->setCor("dummy");
        
        $regiaoTemp = new Regioes();
        $regiaoTemp->setId(1);
        $pokemonTemp->setRegiao($regiaoTemp);
        
        $tipoTemp = new Tipos();
        $tipoTemp->setId(1);
        $pokemonTemp->setTipos([$tipoTemp]);
        
        $erros = $pokemonService->validarPokemon($pokemonTemp);
        
        $errosAltura = array_filter($erros, function($erro) {
            return strpos($erro, 'altura') !== false || strpos($erro, 'Altura') !== false;
        });
        
        if(count($errosAltura) === 0) {
            $response['valido'] = true;
            $response['mensagem'] = 'Altura válida!';
        } else {
            $response['mensagem'] = implode(' ', $errosAltura);
        }
        break;
        
    case 'cor':
        $pokemonTemp->setNome("dummy");
        $pokemonTemp->setPeso(1);
        $pokemonTemp->setAltura(1);
        $pokemonTemp->setCor($valor);
        
        $regiaoTemp = new Regioes();
        $regiaoTemp->setId(1);
        $pokemonTemp->setRegiao($regiaoTemp);
        
        $tipoTemp = new Tipos();
        $tipoTemp->setId(1);
        $pokemonTemp->setTipos([$tipoTemp]);
        
        $erros = $pokemonService->validarPokemon($pokemonTemp);
        
        $errosCor = array_filter($erros, function($erro) {
            return strpos($erro, 'cor') !== false || strpos($erro, 'Cor') !== false;
        });
        
        if(count($errosCor) === 0) {
            $response['valido'] = true;
            $response['mensagem'] = 'Cor válida!';
        } else {
            $response['mensagem'] = implode(' ', $errosCor);
        }
        break;
        
    case 'tipos':
        // $valor deve ser um array de IDs
        if(!is_array($valor)) {
            $valor = [$valor];
        }
        
        $tiposValidos = [];
        foreach($valor as $tipoId) {
            if(is_numeric($tipoId) && $tipoId > 0) {
                $tipoObj = new Tipos();
                $tipoObj->setId((int)$tipoId);
                $tiposValidos[] = $tipoObj;
            }
        }
        
        $pokemonTemp->setNome("dummy");
        $pokemonTemp->setPeso(1);
        $pokemonTemp->setAltura(1);
        $pokemonTemp->setCor("dummy");
        
        $regiaoTemp = new Regioes();
        $regiaoTemp->setId(1);
        $pokemonTemp->setRegiao($regiaoTemp);
        
        $pokemonTemp->setTipos($tiposValidos);
        
        $erros = $pokemonService->validarPokemon($pokemonTemp);
        
        $errosTipos = array_filter($erros, function($erro) {
            return strpos($erro, 'tipo') !== false || strpos($erro, 'Tipo') !== false;
        });
        
        if(count($errosTipos) === 0) {
            $response['valido'] = true;
            $response['mensagem'] = count($tiposValidos) > 1 ? 'Tipos válidos!' : 'Tipo válido!';
        } else {
            $response['mensagem'] = implode(' ', $errosTipos);
        }
        break;
        
    case 'regiao':
        $valorNumerico = ($valor !== NULL && $valor !== "" && is_numeric($valor)) ? (int)$valor : NULL;
        
        $regiaoTemp = NULL;
        if($valorNumerico !== NULL && $valorNumerico > 0) {
            $regiaoTemp = new Regioes();
            $regiaoTemp->setId($valorNumerico);
        }
        
        $pokemonTemp->setNome("dummy");
        $pokemonTemp->setPeso(1);
        $pokemonTemp->setAltura(1);
        $pokemonTemp->setCor("dummy");
        $pokemonTemp->setRegiao($regiaoTemp);
        
        $tipoTemp = new Tipos();
        $tipoTemp->setId(1);
        $pokemonTemp->setTipos([$tipoTemp]);
        
        $erros = $pokemonService->validarPokemon($pokemonTemp);
        
        $errosRegiao = array_filter($erros, function($erro) {
            return strpos($erro, 'região') !== false || strpos($erro, 'Região') !== false || strpos($erro, 'regiao') !== false;
        });
        
        if(count($errosRegiao) === 0) {
            $response['valido'] = true;
            $response['mensagem'] = 'Região válida!';
        } else {
            $response['mensagem'] = implode(' ', $errosRegiao);
        }
        break;
        
    default:
        $response['mensagem'] = 'Campo não reconhecido';
        break;
}

echo json_encode($response);
