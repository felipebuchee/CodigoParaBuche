-- Script para adicionar a coluna 'imagem' na tabela 'pokemons'
-- Execute este script no phpMyAdmin ou MySQL Workbench

USE pokemons;

-- Adicionar coluna imagem (se não existir)
ALTER TABLE pokemons 
ADD COLUMN IF NOT EXISTS imagem VARCHAR(255) NULL 
AFTER altura;

-- Verificar estrutura da tabela
DESCRIBE pokemons;

-- Mensagem de sucesso
SELECT 'Coluna imagem adicionada com sucesso!' AS status;
