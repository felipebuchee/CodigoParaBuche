<?php

require_once(__DIR__ . "/../model/pokemon.php");
require_once(__DIR__ . "/../model/regioes.php");
require_once(__DIR__ . "/../model/tipos.php");
require_once(__DIR__ . "/../util/conexao.php");

class PokemonDAO
{
    private PDO $conexao;

    public function __construct()
    {
        $this->conexao = Conexao::getConnection();
    }

    public function cadastrar(Pokemon $pokemon)
    {
        try
        {
            $this->conexao->beginTransaction();
            
            $sql = "INSERT INTO pokemons (nome, peso, altura, imagem, cor, id_regiao) VALUES (?,?,?,?,?,?)";
            $stm = $this->conexao->prepare($sql);
            $stm->execute([
                $pokemon->getNome(), 
                $pokemon->getPeso(), 
                $pokemon->getAltura(), 
                $pokemon->getImagem(),
                $pokemon->getCor(), 
                $pokemon->getRegiao()->getId()
            ]);
            
            $pokemonId = $this->conexao->lastInsertId();
            $this->inserirTiposPokemon($pokemonId, $pokemon->getTipos());
            
            $this->conexao->commit();
            return NULL;
        } 
        catch(PDOException $e)
        {
            $this->conexao->rollBack();
            return $e;
        }
    }

    public function editar(Pokemon $pokemon)
    {
        try
        {
            $this->conexao->beginTransaction();
            
            $sql = "UPDATE pokemons SET nome = ?, peso = ?, altura = ?, imagem = ?, cor = ?, id_regiao = ? WHERE id = ?";
            $stm = $this->conexao->prepare($sql);
            $stm->execute([
                $pokemon->getNome(), 
                $pokemon->getPeso(), 
                $pokemon->getAltura(), 
                $pokemon->getImagem(),
                $pokemon->getCor(), 
                $pokemon->getRegiao()->getId(), 
                $pokemon->getId()
            ]);
            
            $this->removerTiposPokemon($pokemon->getId());
            $this->inserirTiposPokemon($pokemon->getId(), $pokemon->getTipos());
            
            $this->conexao->commit();
            return NULL;
        } 
        catch(PDOException $e)
        {
            $this->conexao->rollBack();
            return $e;
        }
    }

    public function excluir(Pokemon $pokemon)
    {
        try
        {
            $sql = "DELETE FROM pokemons WHERE id = :id";
            $stm = $this->conexao->prepare($sql);
            $stm->bindValue("id", $pokemon->getId());
            $stm->execute();
            return NULL;
        } 
        catch(PDOException $e)
        {
            return $e;
        }
    }

    public function listar()
    {
        // SELECT p.* inclui a coluna 'imagem'
        $sql = "SELECT p.*, r.nome as nome_regiao FROM pokemons p 
                JOIN regioes r ON r.id = p.id_regiao 
                ORDER BY p.nome";
        $stm = $this->conexao->prepare($sql);
        $stm->execute();
        $result = $stm->fetchAll();

        return $this->mapPokemons($result);
    }

    public function buscarPorId($id)
    {
        // SELECT p.* inclui a coluna 'imagem'
        $sql = "SELECT p.*, r.nome as nome_regiao FROM pokemons p 
                JOIN regioes r ON r.id = p.id_regiao 
                WHERE p.id = ?";
        $stm = $this->conexao->prepare($sql);
        $stm->execute([$id]);
        $result = $stm->fetchAll();

        $pokemons = $this->mapPokemons($result);

        if(count($pokemons)>0)
        {
           return $pokemons[0]; 
        }

        return NULL;
    }

    private function mapPokemons(array $result)
    {
        $pokemons = array();

        foreach($result as $r)
        {
            $pokemon = new Pokemon();
            $pokemon->setId($r["id"]);
            $pokemon->setNome($r["nome"]);
            $pokemon->setPeso($r["peso"]);
            $pokemon->setAltura($r["altura"]);
            $pokemon->setCor($r["cor"]);
            
            // Mapear imagem (pode ser NULL)
            if(isset($r["imagem"]) && !empty($r["imagem"])) {
                $pokemon->setImagem($r["imagem"]);
            } else {
                $pokemon->setImagem(null);
            }

            $regiao = new Regioes();
            $regiao->setId($r["id_regiao"]);
            $regiao->setNome($r["nome_regiao"]);
            $pokemon->setRegiao($regiao);
            
            $tipos = $this->carregarTiposPokemon($r["id"]);
            $pokemon->setTipos($tipos);

            array_push($pokemons,$pokemon);
        }

        return $pokemons;
    }
    
    private function inserirTiposPokemon($pokemonId, array $tipos)
    {
        if(count($tipos) == 0) return;
        
        $sql = "INSERT INTO pokemon_tipos (id_pokemon, id_tipo) VALUES (?, ?)";
        $stm = $this->conexao->prepare($sql);
        
        foreach($tipos as $tipo) {
            $stm->execute([$pokemonId, $tipo->getId()]);
        }
    }
    
    private function removerTiposPokemon($pokemonId)
    {
        $sql = "DELETE FROM pokemon_tipos WHERE id_pokemon = ?";
        $stm = $this->conexao->prepare($sql);
        $stm->execute([$pokemonId]);
    }
    
    private function carregarTiposPokemon($pokemonId)
    {
        $sql = "SELECT t.* FROM tipos t 
                 JOIN pokemon_tipos pt ON t.id = pt.id_tipo 
                 WHERE pt.id_pokemon = ?";
        $stm = $this->conexao->prepare($sql);
        $stm->execute([$pokemonId]);
        $result = $stm->fetchAll();
        
        $tipos = array();
        foreach($result as $r) {
            $tipo = new Tipos();
            $tipo->setId($r["id"]);
            $tipo->setNome($r["nome"]);
            $tipos[] = $tipo;
        }
        
        return $tipos;
    }
}