# Sistema de Validações AJAX - Pokémons

## 📋 Visão Geral

Este sistema adiciona validações em tempo real aos formulários de cadastro e edição de Pokémons, utilizando AJAX para fornecer feedback imediato ao usuário **SEM MODIFICAR** as validações PHP existentes no servidor.

## 🔧 Arquivos Modificados/Criados

### 1. **api/validarCampos.php** (NOVO)
Endpoint PHP que processa as requisições AJAX de validação. Reutiliza o `PokemonService` existente para manter consistência com as regras de negócio.

**Campos validados:**
- `nome` - Nome do Pokémon
- `peso` - Peso em kg
- `altura` - Altura em metros
- `cor` - Cor do Pokémon
- `tipos` - Tipos selecionados
- `regiao` - Região selecionada

### 2. **js/script.js** (ATUALIZADO)
Adicionadas funções JavaScript para:
- Validação AJAX com debounce (evita requisições excessivas)
- Feedback visual em tempo real
- Inicialização automática dos event listeners

**Principais funções:**
- `validarCampoAjax()` - Envia requisição para validar campo
- `debounce()` - Atrasa execução para evitar muitas requisições
- `mostrarFeedback()` - Exibe mensagens de sucesso/erro
- `inicializarValidacoes()` - Configura listeners nos campos

### 3. **css/style.css** (ATUALIZADO)
Novos estilos para feedback visual:
- `.feedback-sucesso` - Mensagem verde para campos válidos
- `.feedback-erro` - Mensagem vermelha para campos inválidos
- `.feedback-validando` - Mensagem amarela durante validação
- `.input-valido` - Borda verde no input
- `.input-invalido` - Borda vermelha no input
- Animações suaves (fadeIn e shake)

### 4. **view/pokemons/form.php** (ATUALIZADO)
Adicionados elementos `<span>` para exibir feedback abaixo de cada campo:
```html
<span id="feedback-nome" class=""></span>
<span id="feedback-peso" class=""></span>
<span id="feedback-altura" class=""></span>
<span id="feedback-cor" class=""></span>
<span id="feedback-tipos" class=""></span>
<span id="feedback-regiao" class=""></span>
```

## ⚙️ Como Funciona

### Fluxo de Validação:

1. **Usuário digita** em um campo do formulário
2. **JavaScript detecta** o evento (input/change/blur)
3. **Debounce aguarda** 500ms (para campos de texto)
4. **Requisição AJAX** é enviada para `api/validarCampos.php`
5. **PHP valida** usando as mesmas regras do `PokemonService`
6. **Resposta JSON** é retornada: `{valido: true/false, mensagem: "..."}`
7. **JavaScript exibe** feedback visual com cores e animações

### Exemplo de Requisição AJAX:
```javascript
FormData:
  campo: "nome"
  valor: "Pikachu"

Resposta:
  {
    "valido": true,
    "mensagem": "Nome válido!"
  }
```

## 🎨 Feedback Visual

### Estados dos Inputs:
- **Vazio/Neutro**: Borda padrão
- **Validando**: Texto amarelo "Validando..."
- **Válido**: Borda verde + mensagem verde
- **Inválido**: Borda vermelha + mensagem vermelha + animação shake

### Mensagens de Erro (exemplos):
- "O nome do Pokémon é obrigatório!"
- "O peso deve ser maior que zero!"
- "Selecione pelo menos um tipo para o Pokémon!"

## 🔒 Segurança

✅ **Validações PHP mantidas intactas** - O servidor continua validando todos os dados
✅ **Validações AJAX são extras** - Melhoram UX mas não substituem segurança
✅ **Mesmo service reutilizado** - Garante regras consistentes
✅ **Dados sanitizados** - FormData e JSON seguros

## 📝 Validações Implementadas

### Nome
- Obrigatório
- Mínimo 2 caracteres
- Máximo 70 caracteres

### Peso
- Obrigatório
- Maior que zero
- Menor que 1000 kg

### Altura
- Obrigatória
- Maior que zero
- Menor que 100 metros

### Cor
- Obrigatória
- Mínimo 2 caracteres
- Máximo 30 caracteres

### Tipos
- Pelo menos 1 tipo selecionado
- IDs válidos

### Região
- Obrigatória
- ID válido

## 🚀 Uso

As validações são **automáticas**! Basta:

1. Acessar `cadastrar.php` ou `editar.php`
2. Começar a preencher o formulário
3. O feedback aparecerá em tempo real

Não é necessário configuração adicional.

## 🔄 Compatibilidade

- ✅ Funciona em `cadastrar.php`
- ✅ Funciona em `editar.php` (usa o mesmo form.php)
- ✅ Mantém validações PHP originais
- ✅ Não quebra funcionalidade existente
- ✅ Progressive enhancement - se JS falhar, PHP valida

## 📊 Performance

- **Debounce de 500ms** reduz requisições durante digitação
- **Validação on blur** garante verificação ao sair do campo
- **Feedback instantâneo** melhora experiência do usuário
- **Requisições leves** - apenas campo e valor enviados

## 🎯 Próximos Passos (Opcionais)

- [ ] Validação antes do submit (prevenir envio se houver erros)
- [ ] Indicador visual de "todos os campos válidos"
- [ ] Validações adicionais customizadas
- [ ] Suporte a outros formulários do projeto
