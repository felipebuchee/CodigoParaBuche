<?php

//Mostrar erros do PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);

//Configurar essas variáveis de acordo com o seu ambiente
define("DB_HOST", "localhost");
define("DB_NAME", "Pokemons8");
define("DB_USER", "root");
define("DB_PASSWORD", "bancodedados");

//configurações adicionais
define("VERBOUSE", true);
//define("VERBOUSE",  false); Esse não deve aparecer para o usuário final!