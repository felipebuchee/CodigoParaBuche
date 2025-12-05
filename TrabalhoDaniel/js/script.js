
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}


function fetchPokemonCards(apiUrl, containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    // Exibir mensagem de carregamento
    container.innerHTML = '<div id="loading">Carregando pokémons...</div>';

    // Criar requisição XMLHttpRequest
    var xhr = new XMLHttpRequest();
    
    // Adicionar timestamp para evitar cache
    var urlComTimestamp = apiUrl + (apiUrl.indexOf('?') === -1 ? '?' : '&') + '_t=' + new Date().getTime();
    
    // Configurar a requisição: método GET, URL, assíncrono
    xhr.open('GET', urlComTimestamp, true);
    
    // Desabilitar cache
    xhr.setRequestHeader('Cache-Control', 'no-cache');
    xhr.setRequestHeader('Pragma', 'no-cache');
    
    // Definir o que fazer quando a requisição mudar de estado
    xhr.onreadystatechange = function() {
        // readyState 4 = requisição concluída
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                try {
                    // Parse da resposta JSON
                    var data = JSON.parse(xhr.responseText);
                    
                    // Log para debug
                    console.log('Dados recebidos:', data);
                    
                    // Validar resposta
                    if (!Array.isArray(data)) {
                        container.innerHTML = '<div class="alert alert-danger">Resposta inválida do servidor</div>';
                        return;
                    }

                    if (data.length === 0) {
                        container.innerHTML = '<div class="alert alert-info">Nenhum Pokémon cadastrado.</div>';
                        return;
                    }

                    // Montar HTML dos cards
                    var cardsHtml = '';
                    
                    data.forEach(function(p) {
                        // Log individual para cada pokemon
                        console.log('Pokemon:', p.nome, 'Imagem:', p.imagem);
                        
                        var tipos = (p.tipos && p.tipos.length) ? p.tipos.join(', ') : '—';
                        var imgTag = '';
                        
                        if (p.imagem) {
                            imgTag = '<div style="text-align:center;margin-bottom:8px;"><img src="' + escapeHtml(p.imagem) + '" alt="' + escapeHtml(p.nome || 'pokemon') + '" style="max-width:160px;height:auto;" onerror="this.style.display=\'none\';console.error(\'Erro ao carregar imagem:\', this.src);"></div>';
                        }
                        
                        cardsHtml += 
                            '<div style="background:#fff;color:#000;border-radius:8px;padding:16px;width:260px;box-shadow:0 6px 12px rgba(0,0,0,0.12);">' +
                                imgTag +
                                '<h3 style="margin:0 0 8px 0;">' + escapeHtml(p.nome || '—') + '</h3>' +
                                '<p style="margin:4px 0;"><strong>Peso:</strong> ' + escapeHtml(p.peso !== null && p.peso !== undefined ? p.peso : '—') + ' kg</p>' +
                                '<p style="margin:4px 0;"><strong>Altura:</strong> ' + escapeHtml(p.altura !== null && p.altura !== undefined ? p.altura : '—') + ' m</p>' +
                                '<p style="margin:4px 0;"><strong>Cor:</strong> ' + escapeHtml(p.cor || '—') + '</p>' +
                                '<p style="margin:4px 0;"><strong>Tipos:</strong> ' + escapeHtml(tipos) + '</p>' +
                                '<p style="margin:4px 0;"><strong>Região:</strong> ' + escapeHtml(p.regiao || '—') + '</p>' +
                                '<div style="margin-top:8px;text-align:right;">' +
                                    '<a class="btn btn-warning" href="editar.php?id=' + encodeURIComponent(p.id) + '">Editar</a> ' +
                                    '<a class="btn btn-danger" href="excluir.php?id=' + encodeURIComponent(p.id) + '" onclick="return confirm(\'Confirma a exclusão do ' + escapeHtml(p.nome) + '?\')">Excluir</a>' +
                                '</div>' +
                            '</div>';
                    });

                    // Inserir cards no container
                    container.innerHTML = cardsHtml;
                    
                } catch (e) {
                    container.innerHTML = '<div class="alert alert-danger">Erro ao processar dados: ' + escapeHtml(e.message) + '</div>';
                    console.error('Erro ao processar JSON:', e);
                }
            } else {
                // Erro na requisição
                container.innerHTML = '<div class="alert alert-danger">Erro ao carregar pokémons (Status: ' + xhr.status + ')</div>';
                console.error('Erro na requisição:', xhr.status);
            }
        }
    };
    
    // Tratar erros de rede
    xhr.onerror = function() {
        container.innerHTML = '<div class="alert alert-danger">Erro de rede ao carregar pokémons</div>';
        console.error('Erro de rede');
    };
    
    // Enviar a requisição
    xhr.send();
}


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
        url += '?regiao=' + regiaoFiltro;
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
            imgHtml = '<div class="card-image-box"><img src="' + pokemon.imagem + '" alt="' + pokemon.nome + '" class="card-image" onerror="this.style.display=\'none\';console.error(\'Erro ao carregar imagem:\', this.src);"></div>';
        }
        
        tr.innerHTML = 
            '<td>' + pokemon.id + '</td>' +
            '<td class="pokemon-hover-container">' +
                '<span class="pokemon-name-trigger">' + pokemon.nome + '</span>' +
                '<div class="pokemon-hover-card">' +
                    imgHtml +
                    '<div class="card-details">' +
                        '<h4>' + pokemon.nome + ' (#' + pokemon.id + ')</h4>' +
                        '<p><strong>Peso:</strong> ' + pokemon.peso + ' kg</p>' +
                        '<p><strong>Altura:</strong> ' + pokemon.altura + ' m</p>' +
                        '<p><strong>Cor:</strong> ' + pokemon.cor + '</p>' +
                        '<p><strong>Tipos:</strong> ' + tipos + '</p>' +
                        '<p><strong>Região:</strong> ' + pokemon.regiao + '</p>' +
                    '</div>' +
                '</div>' +
            '</td>' +
            '<td><a href="editar.php?id=' + pokemon.id + '" class="btn btn-warning">Editar</a></td>' +
            '<td><a href="excluir.php?id=' + pokemon.id + '" class="btn btn-danger" onclick="return confirm(\'Confirma a exclusão do ' + pokemon.nome + '?\')">Excluir</a></td>';
        
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
