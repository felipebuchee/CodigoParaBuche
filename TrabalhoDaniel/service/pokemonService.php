<?php

require_once(__DIR__ . "/../model/pokemon.php");

//o service vai servir para fazer coisas adicionais, como as validacoes, já que a funcao das outras camadas nao bate com validacao

class PokemonService
{
    public function validarPokemon(Pokemon $pokemon)
    {
        $erros = array();

        // Validação do nome
        if($pokemon->getNome() == NULL || trim($pokemon->getNome()) == "") 
        {
            array_push($erros, "O nome do Pokémon é obrigatório!");
        }
        elseif(strlen(trim($pokemon->getNome())) < 2)
        {
            array_push($erros, "O nome do Pokémon deve ter pelo menos 2 caracteres!");
        }
        elseif(strlen(trim($pokemon->getNome())) > 70)
        {
            array_push($erros, "O nome do Pokémon deve ter no máximo 70 caracteres!");
        }

        // Validação do peso
        if($pokemon->getPeso() == NULL)
        {
            array_push($erros, "O peso do Pokémon é obrigatório!");
        }
        elseif($pokemon->getPeso() <= 0)
        {
            array_push($erros, "O peso deve ser maior que zero!");
        }
        elseif($pokemon->getPeso() > 999.99)
        {
            array_push($erros, "O peso deve ser menor que 1000 kg!");
        }

        // Validação da altura
        if($pokemon->getAltura() == NULL)
        {
            array_push($erros, "A altura do Pokémon é obrigatória!");
        }
        elseif($pokemon->getAltura() <= 0)
        {
            array_push($erros, "A altura deve ser maior que zero!");
        }
        elseif($pokemon->getAltura() > 99.99)
        {
            array_push($erros, "A altura deve ser menor que 100 metros!");
        }

        // Validação da cor
        if($pokemon->getCor() == NULL || trim($pokemon->getCor()) == "") 
        {
            array_push($erros, "A cor do Pokémon é obrigatória!");
        }
        elseif(strlen(trim($pokemon->getCor())) < 2)
        {
            array_push($erros, "A cor deve ter pelo menos 2 caracteres!");
        }
        elseif(strlen(trim($pokemon->getCor())) > 30)
        {
            array_push($erros, "A cor deve ter no máximo 30 caracteres!");
        }

        // Validação da região
        if($pokemon->getRegiao() == NULL) 
        {
            array_push($erros, "A região do Pokémon é obrigatória!");
        }
        elseif($pokemon->getRegiao()->getId() == NULL || $pokemon->getRegiao()->getId() <= 0)
        {
            array_push($erros, "Selecione uma região válida!");
        }

        // Validação dos tipos
        if($pokemon->getTipos() == NULL || count($pokemon->getTipos()) == 0) 
        {
            array_push($erros, "Selecione pelo menos um tipo para o Pokémon!");
        }
        else
        {
            foreach($pokemon->getTipos() as $tipo) {
                if($tipo->getId() == NULL || $tipo->getId() <= 0) {
                    array_push($erros, "Selecione tipos válidos!");
                    break;
                }
            }
        }

        return $erros;
    }
}