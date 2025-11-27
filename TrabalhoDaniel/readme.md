# 🎮 CRUD de Pokémons - Sistema com Múltiplos Tipos

## Descrição do Projeto
Este projeto implementa um sistema CRUD (**Create, Read, Update, Delete**) em **PHP** utilizando a arquitetura **MVC**.  
O tema escolhido foi **Pokémons**, onde é possível cadastrar, listar, editar e excluir registros de Pokémons, com vínculos às tabelas de **Tipos** e **Regiões**.  

**🆕 NOVIDADE**: Sistema agora suporta **múltiplos tipos** por Pokémon (ex: Charizard = Fogo + Voador)!

A aplicação utiliza **MySQL** como banco de dados e **Bootstrap** para estilização das interfaces.  

## Funcionalidades do CRUD
- **Inserção** de Pokémon com formulário contendo todos os campos necessários
- **Múltiplos tipos** por Pokémon com seleção múltipla (Ctrl + Click)  
- **Listagem** de todos os Pokémons com exibição das informações completas  
- **Alteração** de registros existentes com preservação de dados em caso de erro
- **Exclusão** de registros com confirmação
- **Validações robustas** no backend substituindo validações HTML

## ✅ Requisitos Atendidos
- [x] **Entidade com ID auto incremento**  
- [x] **CRUD completo** (inserção, listagem, alteração e exclusão)  
- [x] **Formulário com no mínimo 5 campos** (Nome, Peso, Altura, Cor, Tipos[], Região)  
- [x] **Duas relações com outras tabelas** (`pokemons` → `tipos` (many-to-many) e `regioes`)  
- [x] **Validação no back-end** (campos obrigatórios + limites + tipos válidos)  
- [x] **Estilização com Bootstrap**  
- [x] **Entrega até 26/09/2025**  

## 🚀 Funcionalidades Extras Implementadas
- ✅ **Múltiplos tipos** por Pokémon (relacionamento many-to-many)
- ✅ **Validações avançadas**: peso > 0, altura > 0, limites de caracteres
- ✅ **Preservação de dados** no formulário durante erros de validação
- ✅ **Transações seguras** no banco de dados
- ✅ **Interface intuitiva** com instruções visuais
- ✅ **Dados de exemplo** incluídos no script SQL

## 📁 Arquivos SQL

### `ScriptBanco.sql` ⭐ (PRINCIPAL)
- **Para projetos novos**: Execute este arquivo para criar o banco completo
- **Inclui**: Estrutura + dados de exemplo + suporte a múltiplos tipos
- **Pokémons de exemplo**: Pikachu, Charizard, Blastoise, Venusaur, Alakazam

## Tecnologias Utilizadas
- **PHP 8+** (MVC, Type Hints, Nullable Types)  
- **MySQL** (Relacionamentos, Transações, Foreign Keys)  
- **Bootstrap 5** (CSS Framework, Componentes Responsivos)  
