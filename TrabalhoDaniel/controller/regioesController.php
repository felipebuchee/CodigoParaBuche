<?php

require_once(__DIR__ . "/../DAO/regioesDAO.php");

class RegioesController 
{
    private RegioesDAO $regiaoDAO;

    public function __construct() 
    {
        $this->regiaoDAO = new RegioesDAO();
    }

    public function listar() 
    {
        return $this->regiaoDAO->listar();
    }
}