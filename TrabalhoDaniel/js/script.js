function fetchPokemonCards(apiUrl, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // mostrar o carregamento, nao tinha essa parte antes. é aq q começa a dar b.o dps q mudei os treco
    container.innerHTML = '<div id="loading">Carregando pokémons...</div>';

    fetch(apiUrl, { cache: "no-store" })
        .then(response => {
            if (!response.ok) throw new Error("Network response was not ok");
            return response.json();
        })
        .then(data => {
            if (!Array.isArray(data)) {
                container.innerHTML = '<div class="alert alert-danger">Resposta inválida do servidor</div>';
                return;
            }

            if (data.length === 0) {
                container.innerHTML = '<div class="alert alert-info">Nenhum Pokémon cadastrado.</div>';
                return;
            }

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

            container.innerHTML = cards;
        })
        .catch(err => {
            container.innerHTML = `<div class="alert alert-danger">Erro ao carregar pokémons: ${escapeHtml(err.message)}</div>`;
            console.error(err);
        });
}

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}


// VALIDAÇÕES AJAX DE CAMPOS DO FORMULÁRIO


 //debounce é uma função que atrasa a execução de uma função para evitar múltiplas chamadas

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Valida um campo específico via AJAX
 * Envia o valor para o servidor e exibe feedback visual
 */
function validarCampoAjax(campo, valor) {
    const feedbackElement = document.getElementById(`feedback-${campo}`);
    const inputElement = document.getElementById(campo);
    
    if (!feedbackElement || !inputElement) return;
    
    let formData = new FormData();
    formData.append('campo', campo);
    
    // Tratamento especial para campos tipos
    if (campo === 'tipos') {
        const options = inputElement.selectedOptions;
        const valores = Array.from(options).map(opt => opt.value);
        
        if (valores.length === 0) {
            mostrarFeedback(feedbackElement, inputElement, false, 'Selecione pelo menos um tipo para o Pokémon!');
            return;
        }
        
        valores.forEach(v => formData.append('valor[]', v));
    } else {
        formData.append('valor', valor);
    }
    
    // Mostrar estado de "validando"
    feedbackElement.textContent = 'Validando...';
    feedbackElement.className = 'feedback-validando';
    inputElement.classList.remove('input-valido', 'input-invalido');
    
    // Enviar requisição AJAX para validação
    fetch('../api/validarCampos.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        mostrarFeedback(feedbackElement, inputElement, data.valido, data.mensagem);
    })
    .catch(err => {
        console.error('Erro na validação:', err);
        feedbackElement.textContent = '';
        feedbackElement.className = '';
    });
}

/**
 * Exibe feedback visual (sucesso/erro) no campo validado
 * Altera classes CSS para mostrar cores e mensagens
 */
function mostrarFeedback(feedbackElement, inputElement, valido, mensagem) {
    feedbackElement.textContent = mensagem;
    
    if (valido) {
        feedbackElement.className = 'feedback-sucesso';
        inputElement.classList.remove('input-invalido');
        inputElement.classList.add('input-valido');
    } else {
        feedbackElement.className = 'feedback-erro';
        inputElement.classList.remove('input-valido');
        inputElement.classList.add('input-invalido');
    }
}

const validarNomeDebounce = debounce((valor) => validarCampoAjax('nome', valor), 500);
const validarPesoDebounce = debounce((valor) => validarCampoAjax('peso', valor), 500);
const validarAlturaDebounce = debounce((valor) => validarCampoAjax('altura', valor), 500);
const validarCorDebounce = debounce((valor) => validarCampoAjax('cor', valor), 500);

/**
 * Inicializa todos os listeners de validação nos campos do formulário
 * Adiciona eventos de 'input' (durante digitação) e 'blur' (ao sair do campo)
 */
function inicializarValidacoes() {
    // Validação do nome
    const inputNome = document.getElementById('nome');
    if (inputNome) {
        inputNome.addEventListener('input', (e) => {
            validarNomeDebounce(e.target.value);
        });
        inputNome.addEventListener('blur', (e) => {
            validarCampoAjax('nome', e.target.value);
        });
    }
    
    // Validação do peso
    const inputPeso = document.getElementById('peso');
    if (inputPeso) {
        inputPeso.addEventListener('input', (e) => {
            validarPesoDebounce(e.target.value);
        });
        inputPeso.addEventListener('blur', (e) => {
            validarCampoAjax('peso', e.target.value);
        });
    }
    
    // Validação da altura
    const inputAltura = document.getElementById('altura');
    if (inputAltura) {
        inputAltura.addEventListener('input', (e) => {
            validarAlturaDebounce(e.target.value);
        });
        inputAltura.addEventListener('blur', (e) => {
            validarCampoAjax('altura', e.target.value);
        });
    }
    
    // Validação da cor
    const inputCor = document.getElementById('cor');
    if (inputCor) {
        inputCor.addEventListener('input', (e) => {
            validarCorDebounce(e.target.value);
        });
        inputCor.addEventListener('blur', (e) => {
            validarCampoAjax('cor', e.target.value);
        });
    }
    
    // Validação dos tipos (select múltiplo)
    const selectTipos = document.getElementById('tipos');
    if (selectTipos) {
        selectTipos.addEventListener('change', (e) => {
            validarCampoAjax('tipos', e.target.value);
        });
    }
    
    // Validação da região
    const selectRegiao = document.getElementById('regiao');
    if (selectRegiao) {
        selectRegiao.addEventListener('change', (e) => {
            validarCampoAjax('regiao', e.target.value);
        });
    }
}

// Inicializar validações quando o DOM estiver pronto
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarValidacoes);
} else {
    inicializarValidacoes();
}