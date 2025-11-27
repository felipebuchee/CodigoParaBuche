<?php

require_once(__DIR__ . "/../model/regioes.php");
require_once(__DIR__ . "/../util/conexao.php");

class RegioesDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function listar()
    {
        $sql = "SELECT * FROM regioes ORDER BY nome";
        $stm = $this->conexao->prepare($sql);
        $stm->execute();
        $result = $stm->fetchAll();

        return $this->mapRegioes($result);
    }

    private function mapRegioes(array $result)
    {
        $regioes = array();

        foreach($result as $r)
        {
            $regiao = new Regioes();
            $regiao->setId($r["id"]);
            $regiao->setNome($r["nome"]);

            array_push($regioes, $regiao);
        }

        return $regioes;
    }
}