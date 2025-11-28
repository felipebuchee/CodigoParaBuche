/**
 * Escapa caracteres HTML para prevenir XSS
 * Substitui caracteres especiais por suas entidades HTML
 */
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var div = document.createElement('div');
    div.textContent = String(text);
    return div.innerHTML;
}

/**
 * Busca e exibe cards de Pokémons via AJAX usando XMLHttpRequest
 * Carrega dados da API e monta os cards dinamicamente na tela
 * @param {string} apiUrl - URL da API que retorna os dados dos pokémons
 * @param {string} containerId - ID do elemento HTML onde os cards serão inseridos
 */
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