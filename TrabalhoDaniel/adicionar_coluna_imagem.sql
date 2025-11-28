-- Script para adicionar a coluna 'imagem' na tabela 'pokemons'

USE pokemons;

ALTER TABLE pokemons 
ADD COLUMN imagem VARCHAR(255) NULL 
AFTER altura;

DESCRIBE pokemons;

-- Mensagem de sucesso
SELECT 'Coluna imagem adicionada com sucesso!' AS status;
