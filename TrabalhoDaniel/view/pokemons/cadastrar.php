
        <?php

        require_once(__DIR__ . "/../../model/pokemon.php");
        require_once(__DIR__ . "/../../controller/pokemonController.php");
        require_once(__DIR__ . "/../../model/regioes.php");
        require_once(__DIR__ . "/../../model/tipos.php");

        include_once(__DIR__ . "/../include/header.php");

        $msgErro = "";
        $pokemon = NULL;

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

        //receber os dados do formulário
        if (isset($_POST["nome"])) {
            // Capturar dados do formulário
            $nome = isset($_POST["nome"]) ? trim($_POST["nome"]) : "";
            $peso = isset($_POST["peso"]) && $_POST["peso"] !== "" ? $_POST["peso"] : NULL;
            $altura = isset($_POST["altura"]) && $_POST["altura"] !== "" ? $_POST["altura"] : NULL;
            $cor = isset($_POST["cor"]) ? trim($_POST["cor"]) : "";
            $tipos = isset($_POST["tipos"]) && is_array($_POST["tipos"]) ? $_POST["tipos"] : [];
            $regiao = isset($_POST["regiao"]) && $_POST["regiao"] !== "" ? $_POST["regiao"] : NULL;
            $imagem = isset($_POST["imagem"]) ? trim($_POST["imagem"]) : "";

            // Preservar dados para o formulário
            $dadosForm['nome'] = $nome;
            $dadosForm['peso'] = $_POST["peso"] ?? '';
            $dadosForm['altura'] = $_POST["altura"] ?? '';
            $dadosForm['cor'] = $cor;
            $dadosForm['tipos'] = $tipos;
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
            $pokemon->setId(0);
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
            $erros = $pokemonController->cadastrar($pokemon);

            if (!$erros) 
            {
            header("location: listar.php");
            exit;
            } 
            else 
            {
            $msgErro = implode("<br>", $erros);
            }
        }

        include_once(__DIR__ . "/form.php");
        ?>
