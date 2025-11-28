
<?php

require_once(__DIR__ . "/../../model/pokemon.php");
require_once(__DIR__ . "/../../model/regioes.php");
require_once(__DIR__ . "/../../model/tipos.php");
require_once(__DIR__ . "/../../controller/pokemonController.php");

include_once(__DIR__ . "/../include/header.php");

$pokemon = NULL;
$msgErro = "";

// Variáveis para manter os dados no formulário
$dadosForm = [
    'nome' => '',
    'peso' => '',
    'altura' => '',
    'cor' => '',
    'tipos' => [],
    'regiao' => '',
    'imagem' => ''
];

//testa se o usuario ja clicou no gravar
if(isset($_POST['nome']))
{
    // Capturar dados do formulário - buscar pokemon existente primeiro
    $id = isset($_POST["id"]) ? (int)$_POST["id"] : 0;
    $pokemonExistente = null;
    if($id > 0) {
        $pokemonController = new PokemonController();
        $pokemonExistente = $pokemonController->buscarPorId($id);
    }

    // Usar dados do formulário OU manter dados existentes se campo estiver vazio
    $nome = isset($_POST["nome"]) && trim($_POST["nome"]) !== "" ? 
            trim($_POST["nome"]) : 
            ($pokemonExistente ? $pokemonExistente->getNome() : "");
            
    $peso = isset($_POST["peso"]) && $_POST["peso"] !== "" ? 
            $_POST["peso"] : 
            ($pokemonExistente ? $pokemonExistente->getPeso() : NULL);
            
    $altura = isset($_POST["altura"]) && $_POST["altura"] !== "" ? 
              $_POST["altura"] : 
              ($pokemonExistente ? $pokemonExistente->getAltura() : NULL);
              
    $cor = isset($_POST["cor"]) && trim($_POST["cor"]) !== "" ? 
           trim($_POST["cor"]) : 
           ($pokemonExistente ? $pokemonExistente->getCor() : "");
           
            $tipos = isset($_POST["tipos"]) && is_array($_POST["tipos"]) && count($_POST["tipos"]) > 0 ? 
             $_POST["tipos"] : 
             ($pokemonExistente ? array_map(function($t) { return $t->getId(); }, $pokemonExistente->getTipos()) : []);
             
            $regiao = isset($_POST["regiao"]) && $_POST["regiao"] !== "" ? 
              $_POST["regiao"] : 
              ($pokemonExistente && $pokemonExistente->getRegiao() ? $pokemonExistente->getRegiao()->getId() : NULL);
              
            $imagem = isset($_POST["imagem"]) && trim($_POST["imagem"]) !== "" ?
              trim($_POST["imagem"]) :
              ($pokemonExistente && $pokemonExistente->getImagem() ? $pokemonExistente->getImagem() : "");

            // Preservar dados para o formulário (usar dados submetidos ou existentes)
    $dadosForm['nome'] = $nome;
    $dadosForm['peso'] = $_POST["peso"] ?? ($pokemonExistente ? $pokemonExistente->getPeso() : '');
    $dadosForm['altura'] = $_POST["altura"] ?? ($pokemonExistente ? $pokemonExistente->getAltura() : '');
    $dadosForm['cor'] = $cor;
    $dadosForm['tipos'] = is_array($tipos) ? $tipos : [];
    $dadosForm['regiao'] = $regiao;
    $dadosForm['imagem'] = $imagem;

    // Validações básicas antes de criar o objeto
    $peso = ($peso !== NULL && is_numeric($peso)) ? (float)$peso : NULL;
    $altura = ($altura !== NULL && is_numeric($altura)) ? (float)$altura : NULL;
    $regiao = ($regiao !== NULL && is_numeric($regiao)) ? (int)$regiao : NULL;
    
    // Validar tipos selecionados
    $tiposValidos = [];
    foreach($tipos as $tipoId) {
        if(is_numeric($tipoId)) {
            $tiposValidos[] = (int)$tipoId;
        }
    }

    $pokemon = new Pokemon();
    $pokemon->setId($id);
    $pokemon->setNome($nome !== "" ? $nome : NULL);
    $pokemon->setPeso($peso);
    $pokemon->setAltura($altura);
    $pokemon->setCor($cor !== "" ? $cor : NULL);
    $pokemon->setImagem($imagem !== "" ? $imagem : NULL);

    // Adicionar tipos
    if(count($tiposValidos) > 0) {
        $tiposObjetos = [];
        foreach($tiposValidos as $tipoId) {
            $tipoObj = new Tipos();
            $tipoObj->setId($tipoId);
            $tiposObjetos[] = $tipoObj;
        }
        $pokemon->setTipos($tiposObjetos);
    } else {
        $pokemon->setTipos([]);
    }

    if($regiao !== NULL) {
        $regiaoObj = new Regioes();
        $regiaoObj->setId($regiao);
        $pokemon->setRegiao($regiaoObj);
    } else {
        $pokemon->setRegiao(NULL);
    }

    $pokemonController = new PokemonController();
    $erros = $pokemonController->editar($pokemon);

    if (! $erros) 
    {
        header("location: listar.php");
        exit;
    } 
    else 
    {
        $msgErro = implode("<br>", $erros);
    }

}else
{
    $id = 0;

    if(isset($_GET["id"]))
    {
        $id = $_GET["id"];
    }

    $pokemonControl = new PokemonController();
    $pokemon = $pokemonControl->buscarPorId($id);

    if(!$pokemon)
    {
        print "ID do pokemon é inválido<br>";
        print "<a href='Listar.php'>Voltar</a>";
        exit;
    }

    // Pré-carregar dados existentes nos campos do formulário
    $dadosForm['nome'] = $pokemon->getNome();
    $dadosForm['peso'] = $pokemon->getPeso();
    $dadosForm['altura'] = $pokemon->getAltura();
    $dadosForm['cor'] = $pokemon->getCor();
    $dadosForm['tipos'] = array_map(function($tipo) { return $tipo->getId(); }, $pokemon->getTipos());
    $dadosForm['regiao'] = $pokemon->getRegiao() ? $pokemon->getRegiao()->getId() : '';
    $dadosForm['imagem'] = $pokemon->getImagem() ?? '';
}

include_once(__DIR__ . "/form.php");

?>
