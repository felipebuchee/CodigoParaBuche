<?php

require_once(__DIR__ . "/../DAO/tiposDAO.php");

class TiposController 
{
    private TiposDAO $tiposDAO;

    public function __construct() 
    {
        $this->tiposDAO = new TiposDAO();
    }

    public function listar() 
    {
        return $this->tiposDAO->listar();
    }
}