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

    <div id="loading-pokemons" style="text-align: center; padding: 20px;">
        <p>Carregando pokémons...</p>
    </div>

    <table class="table table-striped table-bordered" id="tabela-pokemons" style="display: none;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Editar</th>
                <th>Excluir</th>
            </tr>
        </thead>
        <tbody id="tbody-pokemons">
            <!-- Linhas serão carregadas via AJAX -->
        </tbody>
    </table>
</main>

<?php include_once(__DIR__ . "/../include/footer.php"); ?>

<script>
/**
 * Escapa caracteres HTML para prevenir XSS
 */
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

/**
 * CARREGA POKÉMONS VIA AJAX COM XMLHttpRequest
 * Busca direto do banco de dados com filtro opcional por região
 * 
 * Como funciona:
 * 1. Cria uma requisição XMLHttpRequest para o endpoint PHP
 * 2. O PHP consulta o banco de dados com filtro SQL
 * 3. Quando recebe os dados, monta as linhas da tabela dinamicamente
 * 4. Insere o HTML gerado no tbody
 */
function carregarPokemons(regiaoFiltro) {
    const loadingDiv = document.getElementById('loading-pokemons');
    const tabela = document.getElementById('tabela-pokemons');
    const tbody = document.getElementById('tbody-pokemons');
    
    // Mostrar loading
    loadingDiv.style.display = 'block';
    tabela.style.display = 'none';
    
    // Criar requisição XMLHttpRequest
    var xhr = new XMLHttpRequest();
    
    // Configurar URL com filtro opcional
    var url = '../../api/filtrarPokemons.php';
    if (regiaoFiltro) {
        url += '?regiao=' + encodeURIComponent(regiaoFiltro);
    }
    
    // Configurar a requisição: método GET, URL, assíncrono
    xhr.open('GET', url, true);
    
    // Definir o que fazer quando a requisição mudar de estado
    xhr.onreadystatechange = function() {
        // readyState 4 = requisição concluída
        // status 200 = resposta OK
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    // Parse da resposta JSON
                    var pokemons = JSON.parse(xhr.responseText);
                    
                    // Verificar se há pokémons
                    if (!Array.isArray(pokemons) || pokemons.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px;">Nenhum Pokémon encontrado.</td></tr>';
                        loadingDiv.style.display = 'none';
                        tabela.style.display = 'table';
                        return;
                    }
                    
                    // Renderizar pokémons
                    renderizarPokemons(pokemons);
                    
                    // Ocultar loading e mostrar tabela
                    loadingDiv.style.display = 'none';
                    tabela.style.display = 'table';
                    
                } catch (e) {
                    console.error('Erro ao processar JSON:', e);
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: red;">Erro ao processar dados.</td></tr>';
                    loadingDiv.style.display = 'none';
                    tabela.style.display = 'table';
                }
            } else {
                // Erro na requisição
                tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: red;">Erro ao carregar pokémons (Status: ' + xhr.status + ').</td></tr>';
                loadingDiv.style.display = 'none';
                tabela.style.display = 'table';
            }
        }
    };
    
    // Enviar a requisição
    xhr.send();
}

/**
 * Renderiza os pokémons na tabela
 */
function renderizarPokemons(pokemons) {
    const tbody = document.getElementById('tbody-pokemons');
    
    // Limpar tbody
    tbody.innerHTML = '';
    
    if (pokemons.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--accent-yellow);">🔍 Nenhum Pokémon encontrado com o filtro aplicado.</td></tr>';
        return;
    }
    
    // Montar HTML de cada pokémon
    pokemons.forEach(function(pokemon) {
        
        var tr = document.createElement('tr');
        tr.setAttribute('data-regiao', pokemon.regiao || '');
        
        // Preparar tipos
        var tipos = (pokemon.tipos && pokemon.tipos.length) ? pokemon.tipos.join(', ') : '—';
        
        // Preparar imagem
        var imgHtml = '';
        if (pokemon.imagem) {
            imgHtml = '<div class="card-image-box"><img src="' + escapeHtml(pokemon.imagem) + '" alt="' + escapeHtml(pokemon.nome) + '" class="card-image" onerror="this.style.display=\'none\';console.error(\'Erro ao carregar imagem:\', this.src);"></div>';
        }
        
        tr.innerHTML = 
            '<td>' + escapeHtml(pokemon.id) + '</td>' +
            '<td class="pokemon-hover-container">' +
                '<span class="pokemon-name-trigger">' + escapeHtml(pokemon.nome) + '</span>' +
                '<div class="pokemon-hover-card">' +
                    imgHtml +
                    '<div class="card-details">' +
                        '<h4>' + escapeHtml(pokemon.nome) + ' (#' + escapeHtml(pokemon.id) + ')</h4>' +
                        '<p><strong>Peso:</strong> ' + escapeHtml(pokemon.peso) + ' kg</p>' +
                        '<p><strong>Altura:</strong> ' + escapeHtml(pokemon.altura) + ' m</p>' +
                        '<p><strong>Cor:</strong> ' + escapeHtml(pokemon.cor) + '</p>' +
                        '<p><strong>Tipos:</strong> ' + escapeHtml(tipos) + '</p>' +
                        '<p><strong>Região:</strong> ' + escapeHtml(pokemon.regiao) + '</p>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td><a href="editar.php?id=' + encodeURIComponent(pokemon.id) + '" class="btn btn-warning">Editar</a></td>' +
            '<td><a href="excluir.php?id=' + encodeURIComponent(pokemon.id) + '" class="btn btn-danger" onclick="return confirm(\'Confirma a exclusão do ' + escapeHtml(pokemon.nome) + '?\')">Excluir</a></td>';
        
        tbody.appendChild(tr);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Carregar todos os pokémons ao iniciar
    carregarPokemons('');
    
    // Pegar o elemento select do filtro
    const filtroRegiao = document.getElementById('filtro-regiao');
    
    // Adicionar listener para detectar quando o usuário mudar a seleção
    filtroRegiao.addEventListener('change', function() {
        var regiaoSelecionada = this.value;
        
        // Recarregar pokémons com filtro do banco de dados
        carregarPokemons(regiaoSelecionada);
    });
});
</script>