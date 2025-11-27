-- ====================================================================
-- BANCO DE DADOS POKEMON - VERSÃO COM MÚLTIPLOS TIPOS
-- ====================================================================
-- Este script cria um banco completo para gerenciar Pokémons
-- com suporte a múltiplos tipos por Pokémon
-- ====================================================================

CREATE DATABASE Pokemons;

USE Pokemons;

/* =========================
TABELA regioes (estática)
========================= */
CREATE TABLE regioes (
    id INT AUTO_INCREMENT NOT NULL,
    nome VARCHAR(70) NOT NULL,
    CONSTRAINT pk_regioes PRIMARY KEY (id)
);

/* Inserts iniciais */
INSERT INTO regioes (nome) VALUES ('Kanto');

INSERT INTO regioes (nome) VALUES ('Johto');

INSERT INTO regioes (nome) VALUES ('Hoenn');

INSERT INTO regioes (nome) VALUES ('Sinnoh');

INSERT INTO regioes (nome) VALUES ('Outra');

/* =========================
TABELA tipos (estática)
========================= */
CREATE TABLE tipos (
    id INT AUTO_INCREMENT NOT NULL,
    nome VARCHAR(50) NOT NULL,
    CONSTRAINT pk_tipos PRIMARY KEY (id)
);

/* Inserts iniciais */
INSERT INTO tipos (nome) VALUES ('Fogo');

INSERT INTO tipos (nome) VALUES ('Água');

INSERT INTO tipos (nome) VALUES ('Planta');

INSERT INTO tipos (nome) VALUES ('Elétrico');

INSERT INTO tipos (nome) VALUES ('Psíquico');

INSERT INTO tipos (nome) VALUES ('Outro');

/* =========================
TABELA pokemons (principal)
========================= */
CREATE TABLE pokemons (
    id INT AUTO_INCREMENT NOT NULL,
    nome VARCHAR(70) NOT NULL,
    peso DECIMAL(5, 2) NOT NULL, -- Ex: 85.60 kg
    altura DECIMAL(4, 2) NOT NULL, -- Ex: 1.70 m
    imagem VARCHAR(255) NOT NULL,
    cor VARCHAR(30) NOT NULL,
    id_regiao INT NOT NULL,
    CONSTRAINT pk_pokemons PRIMARY KEY (id),
    CONSTRAINT fk_regiao FOREIGN KEY (id_regiao) REFERENCES regioes (id)
);

/* =======================================
TABELA pokemon_tipos (múltiplos tipos)

Esta tabela implementa um relacionamento 
many-to-many entre pokémons e tipos,
permitindo que um Pokémon tenha múltiplos 
tipos (ex: Charizard = Fogo + Voador)
======================================= */
CREATE TABLE pokemon_tipos (
    id INT AUTO_INCREMENT NOT NULL,
    id_pokemon INT NOT NULL,
    id_tipo INT NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_pokemon_tipos_pokemon FOREIGN KEY (id_pokemon) REFERENCES pokemons (id) ON DELETE CASCADE,
    CONSTRAINT fk_pokemon_tipos_tipo FOREIGN KEY (id_tipo) REFERENCES tipos (id),
    UNIQUE KEY unique_pokemon_tipo (id_pokemon, id_tipo)
);

/* =======================================
DADOS DE EXEMPLO
======================================= */

INSERT INTO pokemons (nome,peso,altura,imagem,cor,id_regiao) VALUES ('Pikachu',6.00,0.41,'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/25.png','Amarelo',1), ('Charizard',90.50,1.70,'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/6.png','Laranja',1), ('Blastoise',85.50,1.60,'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/9.png','Azul',1), ('Venusaur',100.00,2.00,'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/3.png','Verde',1), ('Alakazam',48.00,1.50,'https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/official-artwork/65.png','Amarelo',1);