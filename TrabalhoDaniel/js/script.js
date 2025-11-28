/**
 * Escapa caracteres HTML para prevenir XSS
 * Substitui caracteres especiais por suas entidades HTML
 */
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

/**
 * Busca e exibe cards de Pokémons via AJAX
 * Carrega dados da API e monta os cards dinamicamente na tela
 * @param {string} apiUrl - URL da API que retorna os dados dos pokémons
 * @param {string} containerId - ID do elemento HTML onde os cards serão inseridos
 */
function fetchPokemonCards(apiUrl, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Exibir mensagem de carregamento
    container.innerHTML = '<div id="loading">Carregando pokémons...</div>';

    // Buscar dados via AJAX
    fetch(apiUrl, { cache: "no-store" })
        .then(response => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.json();
        })
        .then(data => {
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
            const cards = data.map(p => {
                const tipos = (p.tipos && p.tipos.length) ? p.tipos.join(', ') : '—';
                const imgTag = p.imagem ? `<div style="text-align:center;margin-bottom:8px;"><img src="${escapeHtml(p.imagem)}" alt="${escapeHtml(p.nome||'pokemon')}" style="max-width:160px;height:auto;"></div>` : '';
                return `
                    <div style="background:#fff;color:#000;border-radius:8px;padding:16px;width:260px;box-shadow:0 6px 12px rgba(0,0,0,0.12);">
                        ${imgTag}
                        <h3 style="margin:0 0 8px 0;">${escapeHtml(p.nome || '—')}</h3>
                        <p style="margin:4px 0;"><strong>Peso:</strong> ${escapeHtml(p.peso ?? '—')} kg</p>
                        <p style="margin:4px 0;"><strong>Altura:</strong> ${escapeHtml(p.altura ?? '—')} m</p>
                        <p style="margin:4px 0;"><strong>Cor:</strong> ${escapeHtml(p.cor || '—')}</p>
                        <p style="margin:4px 0;"><strong>Tipos:</strong> ${escapeHtml(tipos)}</p>
                        <p style="margin:4px 0;"><strong>Região:</strong> ${escapeHtml(p.regiao || '—')}</p>
                        <div style="margin-top:8px;text-align:right;">
                            <a class="btn btn-warning" href="editar.php?id=${encodeURIComponent(p.id)}">Editar</a>
                            <a class="btn btn-danger" href="excluir.php?id=${encodeURIComponent(p.id)}" onclick="return confirm('Confirma a exclusão do ${escapeHtml(p.nome)}?')">Excluir</a>
                        </div>
                    </div>
                `;
            }).join('');

            // Inserir cards no container
            container.innerHTML = cards;
        })
        .catch(err => {
            container.innerHTML = `<div class="alert alert-danger">Erro ao carregar pokémons: ${escapeHtml(err.message)}</div>`;
            console.error(err);
        });
}