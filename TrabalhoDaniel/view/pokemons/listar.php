<?php
    require_once(__DIR__ . "/../../controller/pokemonController.php");

    $pokemonController = new PokemonController();
    $lista = $pokemonController->listar();

    include_once(__DIR__ . "/../include/header.php");
?>

<main class="container">
    <h2>⚪ Gerenciar Pokémons 🔴</h2>

    <div class="actions">
        <a class="btn btn-primary" href="cadastrar.php">Cadastrar Pokémon</a>
    </div>

    <h3>Listagem de Pokémons</h3>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Editar</th>
                <th>Excluir</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $pokemon): ?>
                <tr>
                    <td><?= $pokemon->getId() ?></td>
                    
                    <td class="pokemon-hover-container">
                        <span class="pokemon-name-trigger">
                            <?= $pokemon->getNome() ?>
                        </span>
                        
                        <div class="pokemon-hover-card">
                            <div class="card-image-box">
                                <img src="<?= $pokemon->getImagem() ?>" alt="<?= $pokemon->getNome() ?>" class="card-image">
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    // Agora 'cardBody' e 'modalElement' serão encontrados
    const botoesVisualizar = document.querySelectorAll('.btn-visualizar');
    const cardBody = document.getElementById('card-body');
    const modalElement = document.getElementById('pokemonModal');
    
    // Se estiver usando Bootstrap 5, o modal é aberto via data-bs-target no botão.
    // Se precisar abrir manualmente (opcional): const bsModal = new bootstrap.Modal(modalElement);

    botoesVisualizar.forEach(botao => {
        botao.addEventListener('click', function() {

            const pokemonId = this.getAttribute('data-id');

            cardBody.innerHTML = '<div class="text-center"><p>Carregando dados do Pokémon...</p><div class="spinner-border text-primary" role="status"></div></div>';

            const xhr = new XMLHttpRequest();
            const url = '../../controller/pokemonController.php?action=getCardDetails&id=' + encodeURIComponent(pokemonId);

            xhr.open('GET', url, true);
            
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const data = JSON.parse(xhr.responseText);
                        
                        if (data && data.id) {
                            const tipos = data.tipos.join(', ');
                            const cardHtml = `
                                <div class="pokemon-card-content text-center">
                                    <h3>${data.nome} (#${data.id})</h3>
                                    <img src="${data.urlImagem}" alt="Imagem do ${data.nome}" style="max-width: 150px; margin: 10px;">
                                    <p><strong>Peso:</strong> ${data.peso} kg</p>
                                    <p><strong>Altura:</strong> ${data.altura} m</p>
                                    <p><strong>Cor:</strong> ${data.cor}</p>
                                    <p><strong>Tipos:</strong> ${tipos}</p>
                                    <p><strong>Região:</strong> ${data.regiao}</p>
                                </div>
                            `;
                            cardBody.innerHTML = cardHtml;
                            
                            
                        } else {
                            cardBody.innerHTML = '<p class="text-danger">Erro: Dados do Pokémon inválidos ou não encontrados.</p>';
                        }
                    } catch (e) {
                        cardBody.innerHTML = '<p class="text-danger">Erro ao processar a resposta do servidor (JSON inválido).</p>';
                        console.error('JSON Parse Error:', e);
                    }
                } else {
                    cardBody.innerHTML = `<p class="text-danger">Ocorreu um erro (${xhr.status}) ao buscar os detalhes do Pokémon.</p>`;
                }
            };

            xhr.onerror = function() {
                cardBody.innerHTML = '<p class="text-danger">Ocorreu um erro de rede durante a requisição.</p>';
            };

            xhr.send();
        });
    });
});
</script>