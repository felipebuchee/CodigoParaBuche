<?php

require_once(__DIR__ . "/../model/tipos.php");
require_once(__DIR__ . "/../util/conexao.php");

class TiposDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function listar()
    {
        $sql = "SELECT * FROM tipos ORDER BY nome";
        $stm = $this->conexao->prepare($sql);
        $stm->execute();
        $result = $stm->fetchAll();

        return $this->mapTipos($result);
    }

    private function mapTipos(array $result)
    {
        $Tipos = array();

        foreach($result as $r)
        {
            $Tipo = new Tipos();
            $Tipo->setId($r["id"]);
            $Tipo->setNome($r["nome"]);

            array_push($Tipos, $Tipo);
        }

        return $Tipos;
    }
}