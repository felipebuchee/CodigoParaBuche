<?php

require_once(__DIR__ . "/../service/pokemonService.php");
require_once(__DIR__ . "/../DAO/pokemonDAO.php");
require_once(__DIR__ . "/../model/pokemon.php");
require_once(__DIR__ . "/../util/config.php"); // Necessário para VERBOUSE

class PokemonController
{
    private PokemonDAO $pokeDAO;
    private PokemonService $pokeService;

    public function __construct()
    {
        $this->pokeDAO = new PokemonDAO();
        $this->pokeService = new PokemonService();
    }

    public function listar()
    {
        return $this->pokeDAO->listar();
    }

    public function listarPorRegiao($regiao)
    {
        return $this->pokeDAO->listarPorRegiao($regiao);
    }

    public function buscarPorId($id)
    {
        return $this->pokeDAO->buscarPorId($id);
    }

    public function cadastrar(Pokemon $pokemon)
    {
        $erros = $this->pokeService->validarPokemon($pokemon);

        if (count($erros) > 0) {
            return $erros;
        }

        $erro = $this->pokeDAO->cadastrar($pokemon);

        if ($erro) {
            array_push($erros, "Erro ao salvar um pokemon!");
            if (VERBOUSE) {
                array_push($erros, $erro->getMessage());
            }
        }

        return $erros;
    }

    public function excluir(Pokemon $pokemon)
    {
        $erros = array();

        $erro = $this->pokeDAO->excluir($pokemon);

        if ($erro) {
            array_push($erros, "Erro ao excluir um pokemon!");
            if (VERBOUSE) {
                array_push($erros, $erro->getMessage());
            }
        }

        return $erros;
    }

    public function retornarJsonPorId(int $id)
    {
        $pokemon = $this->pokeDAO->buscarPorId($id);

        if ($pokemon) {
            $pokemonData = [
                'id'     => $pokemon->getId(),
                'nome'   => $pokemon->getNome(),
                'peso'   => $pokemon->getPeso(),
                'altura' => $pokemon->getAltura(),
                'cor'    => $pokemon->getCor(),

                'urlImagem' => $pokemon->getImagem(),

                'tipos'  => array_map(function ($tipo) {
                    return $tipo->getNome();
                }, $pokemon->getTipos()),

                'regiao' => $pokemon->getRegiao()->getNome(),
            ];

            return $pokemonData;
        }
        return null;
    }

    public function editar(Pokemon $pokemon)
    {
        $erros = $this->pokeService->validarPokemon($pokemon);

        if (count($erros) > 0) {
            return $erros;
        }

        $erro = $this->pokeDAO->editar($pokemon);

        if ($erro) {
            array_push($erros, "Erro ao editar um pokemon!");
            if (VERBOUSE) {
                array_push($erros, $erro->getMessage());
            }
        }

        return $erros;
    }
}



if (isset($_GET['action']) && $_GET['action'] == 'getCardDetails' && isset($_GET['id'])) {

    $pokemonId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($pokemonId) {
        $controller = new PokemonController();
        $data = $controller->retornarJsonPorId($pokemonId);

        header('Content-Type: application/json');

        if ($data) {
            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Pokémon não encontrado']);
        }
        exit;
    }
}
