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

<script src="../../js/script.js"></script>
