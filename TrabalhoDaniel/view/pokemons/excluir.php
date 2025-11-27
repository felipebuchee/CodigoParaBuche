<?php

require_once(__DIR__ . "/../../model/pokemon.php");
require_once(__DIR__ . "/../../controller/pokemonController.php");

$idPokemon = 0;

if (isset($_GET["id"])) 
{
    $idPokemon = $_GET["id"];
}

$pokemonControl = new PokemonController();
$pokemon = $pokemonControl->buscarPorId($idPokemon);

if ($pokemon) 
{
    $erros = $pokemonControl->excluir($pokemon);

    if($erros)
    {
        $msgErros = implode("<br>", $erros);
        print $msgErros;
    }else
    {
        header("location: listar.php");
    }
}
else
{
    print "Pokemon não encontrado!<br>";
    print "<a href='listar.php'>Voltar</a>";
}

