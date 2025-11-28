# 🧪 Guia de Testes - Validações AJAX

## Como Testar o Sistema de Validações

### 1️⃣ Preparar Ambiente
```
1. Certifique-se que o XAMPP está rodando (Apache + MySQL)
2. Acesse: http://localhost/CodigoParaBuche/TrabalhoDaniel/view/pokemons/cadastrar.php
```

### 2️⃣ Testar Campo NOME

**Teste 1: Nome vazio**
- Deixe o campo vazio e clique fora
- ❌ Esperado: "O nome do Pokémon é obrigatório!"

**Teste 2: Nome muito curto**
- Digite: "P"
- ❌ Esperado: "O nome do Pokémon deve ter pelo menos 2 caracteres!"

**Teste 3: Nome válido**
- Digite: "Pikachu"
- ✅ Esperado: "Nome válido!" (mensagem verde)

**Teste 4: Nome muito longo**
- Digite: "A".repeat(71) // mais de 70 caracteres
- ❌ Esperado: "O nome do Pokémon deve ter no máximo 70 caracteres!"

### 3️⃣ Testar Campo PESO

**Teste 1: Peso vazio**
- Deixe o campo vazio
- ❌ Esperado: "O peso do Pokémon é obrigatório!"

**Teste 2: Peso zero ou negativo**
- Digite: 0 ou -5
- ❌ Esperado: "O peso deve ser maior que zero!"

**Teste 3: Peso válido**
- Digite: 6.5
- ✅ Esperado: "Peso válido!"

**Teste 4: Peso muito alto**
- Digite: 1000
- ❌ Esperado: "O peso deve ser menor que 1000 kg!"

### 4️⃣ Testar Campo ALTURA

**Teste 1: Altura vazia**
- Deixe o campo vazio
- ❌ Esperado: "A altura do Pokémon é obrigatória!"

**Teste 2: Altura zero**
- Digite: 0
- ❌ Esperado: "A altura deve ser maior que zero!"

**Teste 3: Altura válida**
- Digite: 0.4
- ✅ Esperado: "Altura válida!"

**Teste 4: Altura muito alta**
- Digite: 100
- ❌ Esperado: "A altura deve ser menor que 100 metros!"

### 5️⃣ Testar Campo COR

**Teste 1: Cor vazia**
- Deixe o campo vazio
- ❌ Esperado: "A cor do Pokémon é obrigatória!"

**Teste 2: Cor muito curta**
- Digite: "A"
- ❌ Esperado: "A cor deve ter pelo menos 2 caracteres!"

**Teste 3: Cor válida**
- Digite: "Amarelo"
- ✅ Esperado: "Cor válida!"

**Teste 4: Cor muito longa**
- Digite: "A".repeat(31) // mais de 30 caracteres
- ❌ Esperado: "A cor deve ter no máximo 30 caracteres!"

### 6️⃣ Testar Campo TIPOS

**Teste 1: Nenhum tipo selecionado**
- Não selecione nada
- ❌ Esperado: "Selecione pelo menos um tipo para o Pokémon!"

**Teste 2: Um tipo selecionado**
- Selecione "Elétrico"
- ✅ Esperado: "Tipo válido!"

**Teste 3: Múltiplos tipos**
- Selecione "Elétrico" e "Normal" (Ctrl + Click)
- ✅ Esperado: "Tipos válidos!"

### 7️⃣ Testar Campo REGIÃO

**Teste 1: Nenhuma região selecionada**
- Deixe "Selecione uma região"
- ❌ Esperado: "A região do Pokémon é obrigatória!"

**Teste 2: Região válida**
- Selecione "Kanto"
- ✅ Esperado: "Região válida!"

## 🎨 Comportamentos Visuais a Observar

### Durante a Digitação:
- ⏱️ Aparecer "Validando..." (amarelo) enquanto digita
- ⏱️ Após 500ms de pausa, a validação acontece

### Feedback de Sucesso:
- ✅ Borda verde no campo
- ✅ Mensagem verde abaixo do campo
- ✅ Animação suave (fade in)

### Feedback de Erro:
- ❌ Borda vermelha no campo
- ❌ Mensagem vermelha abaixo do campo
- ❌ Animação de "shake" (tremida)

## 🔍 Verificar Console do Navegador

Abra o DevTools (F12) e vá na aba Console:

### Verificar Requisições:
1. Vá na aba "Network" (Rede)
2. Digite em um campo
3. Veja a requisição POST para `validarCampos.php`
4. Clique na requisição e veja:
   - **Request**: campo e valor enviados
   - **Response**: JSON com {valido, mensagem}

### Exemplo de Response:
```json
{
  "valido": true,
  "mensagem": "Nome válido!"
}
```

## 🐛 Troubleshooting

### Validações não funcionam?
1. Verifique o Console (F12) por erros JavaScript
2. Confirme que o arquivo `script.js` está sendo carregado
3. Verifique se o caminho `../api/validarCampos.php` está correto

### Mensagens não aparecem?
1. Inspecione o elemento `<span id="feedback-nome">` no HTML
2. Verifique se as classes CSS estão sendo aplicadas
3. Confirme que o arquivo `style.css` tem os novos estilos

### Validação duplicada no submit?
- ✅ CORRETO! As validações PHP continuam funcionando
- Isso é intencional para segurança

## ✨ Teste Completo de Fluxo

**Cenário: Cadastrar Pikachu**

1. Nome: "Pikachu" → ✅ "Nome válido!"
2. Peso: "6" → ✅ "Peso válido!"
3. Altura: "0.4" → ✅ "Altura válida!"
4. Cor: "Amarelo" → ✅ "Cor válida!"
5. Tipos: Selecionar "Elétrico" → ✅ "Tipo válido!"
6. Região: Selecionar "Kanto" → ✅ "Região válida!"
7. Clicar em "Cadastrar"
8. ✅ Pokémon cadastrado com sucesso!

**Cenário: Tentar cadastrar com erros**

1. Nome: "P" → ❌ Erro
2. Peso: "0" → ❌ Erro
3. Altura: "-1" → ❌ Erro
4. Cor: "" → ❌ Erro
5. Tipos: (nenhum) → ❌ Erro
6. Região: (nenhuma) → ❌ Erro
7. Clicar em "Cadastrar"
8. ❌ PHP também valida e mostra erros no topo

## 📊 Verificação de Performance

### Testar Debounce:
1. Digite rapidamente: "Pikachu"
2. Observe o console de rede
3. ✅ Deve ter apenas 1 requisição (após 500ms de pausa)
4. ❌ Se tiver 7 requisições (uma por letra), debounce não funcionou

### Testar Validação on Blur:
1. Digite "Pika" e pressione Tab (sair do campo)
2. ✅ Validação acontece imediatamente
3. Não precisa esperar 500ms

## 🎯 Checklist Final

- [ ] Todas as mensagens de erro aparecem corretamente
- [ ] Todas as mensagens de sucesso aparecem em verde
- [ ] Bordas mudam de cor (verde/vermelho)
- [ ] Animações funcionam (shake nos erros)
- [ ] Debounce está funcionando (máx 1 req por campo)
- [ ] Validação on blur funciona
- [ ] Validações PHP ainda funcionam no submit
- [ ] Console sem erros JavaScript
- [ ] Funciona tanto em cadastrar.php quanto editar.php
