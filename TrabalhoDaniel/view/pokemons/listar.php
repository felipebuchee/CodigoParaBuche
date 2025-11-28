<?php
    require_once(__DIR__ . "/../../controller/pokemonController.php");
    require_once(__DIR__ . "/../../controller/regioesController.php");

    $pokemonController = new PokemonController();
    $lista = $pokemonController->listar();
    
    $regioesController = new RegioesController();
    $regioes = $regioesController->listar();

    include_once(__DIR__ . "/../include/header.php");
?>

<main class="container">
    <h2>⚪ Gerenciar Pokémons 🔴</h2>

    <div class="actions">
        <a class="btn btn-primary" href="cadastrar.php">Cadastrar Pokémon</a>
    </div>

    <h3>Listagem de Pokémons</h3>

    <!-- Filtro por Região -->
    <div class="mb-3" style="max-width: 300px;">
        <label for="filtro-regiao" class="form-label">🔍 Filtrar por Região:</label>
        <select class="form-select" id="filtro-regiao">
            <option value="">Todas as Regiões</option>
            <?php foreach ($regioes as $regiao): ?>
                <option value="<?= $regiao->getNome() ?>"><?= $regiao->getNome() ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <table class="table table-striped table-bordered" id="tabela-pokemons">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Editar</th>
                <th>Excluir</th>
            </tr>
        </thead>
        <tbody id="tbody-pokemons">
            <?php foreach ($lista as $pokemon): ?>
                <tr data-regiao="<?= $pokemon->getRegiao()->getNome() ?>">
                    <td><?= $pokemon->getId() ?></td>
                    
                    <td class="pokemon-hover-container">
                        <span class="pokemon-name-trigger">
                            <?= $pokemon->getNome() ?>
                        </span>
                        
                        <div class="pokemon-hover-card">
                            <?php if($pokemon->getImagem()): ?>
                            <div class="card-image-box">
                                <img src="<?= $pokemon->getImagem() ?>" alt="<?= $pokemon->getNome() ?>" class="card-image">
                            </div>
                            <?php endif; ?>
                            <div class="card-details">
                                <h4><?= $pokemon->getNome() ?> (#<?= $pokemon->getId() ?>)</h4>
                                <p><strong>Peso:</strong> <?= $pokemon->getPeso() ?> kg</p>
                                <p><strong>Altura:</strong> <?= $pokemon->getAltura() ?> m</p>
                                <p><strong>Cor:</strong> <?= $pokemon->getCor() ?></p>
                                <p>
                                    <strong>Tipos:</strong> 
                                    <?php 
                                    $nomesTipos = array_map(function($tipo) { return $tipo->getNome(); }, $pokemon->getTipos());
                                    echo implode(', ', $nomesTipos);
                                    ?>
                                </p>
                                <p><strong>Região:</strong> <?= $pokemon->getRegiao()->getNome() ?></p>
                            </div>
                        </div>
                    </td>
                    
                    <td><a href="editar.php?id=<?= $pokemon->getId() ?>" class="btn btn-warning">Editar</a></td>
                    <td><a href="excluir.php?id=<?= $pokemon->getId() ?>" class="btn btn-danger" onclick="return confirm('Confirma a exclusão do <?= $pokemon->getNome() ?>?')">Excluir</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>

<?php include_once(__DIR__ . "/../include/footer.php"); ?>

<script>
/**
 * FILTRO DE POKÉMONS POR REGIÃO EM TEMPO REAL
 * 
 * Como funciona:
 * 1. O usuário seleciona uma região no dropdown
 * 2. JavaScript captura essa mudança instantaneamente
 * 3. Percorre todas as linhas da tabela comparando a região
 * 4. Mostra apenas as linhas que correspondem à região selecionada
 * 5. Oculta as demais linhas (sem excluir do HTML)
 * 
 * Vantagens:
 * - Não precisa recarregar a página
 * - Não faz requisição ao servidor
 * - Rápido e responsivo
 */
document.addEventListener('DOMContentLoaded', function() {
    // Pegar o elemento select do filtro
    const filtroRegiao = document.getElementById('filtro-regiao');
    
    // Pegar todas as linhas (<tr>) da tabela que contêm os pokémons
    const linhasTabela = document.querySelectorAll('#tbody-pokemons tr');
    
    // Adicionar listener para detectar quando o usuário mudar a seleção
    filtroRegiao.addEventListener('change', function() {
        // Pegar o valor selecionado e converter para minúsculas (facilita comparação)
        const regiaoSelecionada = this.value.toLowerCase();
        
        // Contador para saber quantos pokémons ficaram visíveis
        let contadorVisiveis = 0;
        
        // Percorrer cada linha da tabela
        linhasTabela.forEach(linha => {
            // Pegar o atributo "data-regiao" da linha (definido no PHP)
            const regiaoLinha = linha.getAttribute('data-regiao').toLowerCase();
            
            // Verificar se deve mostrar ou ocultar a linha
            // Mostra se: "Todas as Regiões" (valor vazio) OU região corresponde
            if (regiaoSelecionada === '' || regiaoLinha === regiaoSelecionada) {
                linha.style.display = ''; // Mostrar linha (display padrão)
                contadorVisiveis++; // Incrementar contador
            } else {
                linha.style.display = 'none'; // Ocultar linha
            }
        });
        
        // Exibir mensagem caso nenhum pokémon seja encontrado
        mostrarMensagemFiltro(contadorVisiveis, regiaoSelecionada);
    });
});

/**
 * Exibe mensagem quando o filtro não encontra resultados
 * Cria uma linha temporária na tabela informando o usuário
 * 
 * @param {number} quantidade - Número de pokémons visíveis após filtro
 * @param {string} regiao - Nome da região filtrada
 */
function mostrarMensagemFiltro(quantidade, regiao) {
    // Remover mensagem anterior (se existir) para não duplicar
    const mensagemAnterior = document.getElementById('mensagem-filtro');
    if (mensagemAnterior) {
        mensagemAnterior.remove();
    }
    
    // Se não encontrou nenhum pokémon (quantidade = 0)
    if (quantidade === 0) {
        const tbody = document.getElementById('tbody-pokemons');
        
        // Criar uma nova linha <tr>
        const tr = document.createElement('tr');
        tr.id = 'mensagem-filtro'; // ID para poder remover depois
        
        // Inserir HTML da mensagem (colspan="4" faz ocupar todas as colunas)
        tr.innerHTML = `
            <td colspan="4" style="text-align: center; padding: 20px; color: var(--accent-yellow);">
                🔍 Nenhum Pokémon encontrado na região "${regiao}"
            </td>
        `;
        
        // Adicionar a linha ao corpo da tabela
        tbody.appendChild(tr);
    }
}
</script>