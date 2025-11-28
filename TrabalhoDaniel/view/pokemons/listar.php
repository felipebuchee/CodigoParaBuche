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
 * Filtro de Pokémons por Região em tempo real
 * Quando o usuário seleciona uma região, a tabela é filtrada via JavaScript
 * sem recarregar a página
 */
document.addEventListener('DOMContentLoaded', function() {
    const filtroRegiao = document.getElementById('filtro-regiao');
    const linhasTabela = document.querySelectorAll('#tbody-pokemons tr');
    
    // Listener para quando o usuário mudar a região selecionada
    filtroRegiao.addEventListener('change', function() {
        const regiaoSelecionada = this.value.toLowerCase();
        let contadorVisiveis = 0;
        
        // Percorrer todas as linhas da tabela
        linhasTabela.forEach(linha => {
            const regiaoLinha = linha.getAttribute('data-regiao').toLowerCase();
            
            // Se "Todas as Regiões" OU a região corresponde
            if (regiaoSelecionada === '' || regiaoLinha === regiaoSelecionada) {
                linha.style.display = ''; // Mostrar linha
                contadorVisiveis++;
            } else {
                linha.style.display = 'none'; // Ocultar linha
            }
        });
        
        // Feedback visual caso nenhum pokémon seja encontrado
        mostrarMensagemFiltro(contadorVisiveis, regiaoSelecionada);
    });
});

/**
 * Exibe mensagem quando o filtro não encontra resultados
 * Cria uma linha temporária na tabela informando o usuário
 */
function mostrarMensagemFiltro(quantidade, regiao) {
    // Remove mensagem anterior se existir
    const mensagemAnterior = document.getElementById('mensagem-filtro');
    if (mensagemAnterior) {
        mensagemAnterior.remove();
    }
    
    // Se não encontrou nenhum pokémon
    if (quantidade === 0) {
        const tbody = document.getElementById('tbody-pokemons');
        const tr = document.createElement('tr');
        tr.id = 'mensagem-filtro';
        tr.innerHTML = `
            <td colspan="4" style="text-align: center; padding: 20px; color: var(--accent-yellow);">
                🔍 Nenhum Pokémon encontrado na região "${regiao}"
            </td>
        `;
        tbody.appendChild(tr);
    }
}
</script>