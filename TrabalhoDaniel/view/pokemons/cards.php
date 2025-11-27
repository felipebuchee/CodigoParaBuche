
<?php
include_once(__DIR__ . "/../include/header.php");
?>
<main class="container">
    <h2>Cards de Pokémons</h2>
    <div class="actions">
        <a class="btn btn-outline-secondary" href="listar.php">Voltar à Lista</a>
    </div>

    <div id="cards-container" style="display:flex;flex-wrap:wrap;gap:16px;margin-top:20px;">
        <!-- Cards serão injetados aqui via AJAX -->
        <div id="loading">Carregando pokémons...</div>
    </div>
</main>

<script src="../../js/script.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetchPokemonCards('../../api/pokemons.php', 'cards-container');
});
</script>

<?php include_once(__DIR__ . "/../include/footer.php"); ?>