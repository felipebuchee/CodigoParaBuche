<?php
require_once(__DIR__ . "/../../controller/tiposController.php");
require_once(__DIR__ . "/../../controller/regioesController.php");

// Carregar lista de tipos e regiões para os selects
$tiposController = new TiposController();
$listaTipos = $tiposController->listar();

$regioesController = new RegioesController();
$listaRegioes = $regioesController->listar();

// Incluir header
include_once(__DIR__ . "/../include/header.php");
?>

<div class="container">

    <h2><?= $pokemon && $pokemon->getId() > 0 ? "Editar" : "Cadastrar" ?> Pokémon</h2>

    <?php if ($msgErro): ?>
        <div class="alert alert-danger" role="alert">
            <?= $msgErro ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-8">
            <form method="post">
                <?php if ($pokemon && $pokemon->getId()): ?>
                    <input type="hidden" name="id" value="<?= $pokemon->getId() ?>">
                <?php endif; ?>

                <div class="mb-3">
                    <label for="nome" class="form-label">Nome do Pokémon:</label>
                    <input type="text"
                        class="form-control"
                        id="nome"
                        name="nome"
                        placeholder="<?= ($pokemon && $pokemon->getId()) ? 'Atual: ' . $pokemon->getNome() : 'Digite o nome do Pokémon' ?>"
                        value="<?= isset($dadosForm) ? $dadosForm['nome'] : ($pokemon ? $pokemon->getNome() : '') ?>">
                </div>

                <div class="mb-3">
                    <label for="peso" class="form-label">Peso (kg):</label>
                    <input type="number"
                        step="0.01"
                        min="0.01"
                        class="form-control"
                        id="peso"
                        name="peso"
                        placeholder="<?= ($pokemon && $pokemon->getId()) ? 'Atual: ' . $pokemon->getPeso() . 'kg' : 'Digite o peso em kg' ?>"
                        value="<?= isset($dadosForm) ? $dadosForm['peso'] : ($pokemon ? $pokemon->getPeso() : '') ?>">
                </div>

                <div class="mb-3">
                    <label for="altura" class="form-label">Altura (m):</label>
                    <input type="number"
                        step="0.01"
                        min="0.01"
                        class="form-control"
                        id="altura"
                        name="altura"
                        placeholder="<?= ($pokemon && $pokemon->getId()) ? 'Atual: ' . $pokemon->getAltura() . 'm' : 'Digite a altura em metros' ?>"
                        value="<?= isset($dadosForm) ? $dadosForm['altura'] : ($pokemon ? $pokemon->getAltura() : '') ?>">
                </div>

                <div class="mb-3">
                    <label for="cor" class="form-label">Cor:</label>
                    <input type="text"
                        class="form-control"
                        id="cor"
                        name="cor"
                        placeholder="<?= ($pokemon && $pokemon->getId()) ? 'Atual: ' . $pokemon->getCor() : 'Digite a cor do Pokémon' ?>"
                        value="<?= isset($dadosForm) ? $dadosForm['cor'] : ($pokemon ? $pokemon->getCor() : '') ?>">
                </div>

                <div class="mb-3">
                    <label for="tipos" class="form-label">Tipos: <small class="text-muted">(Mantenha Ctrl pressionado para selecionar múltiplos)</small></label>
                    <select class="form-select" id="tipos" name="tipos[]" multiple size="6">
                        <?php foreach ($listaTipos as $tipo): ?>
                            <option value="<?= $tipo->getId() ?>" 
                                    <?php 
                                    $tiposSelecionados = isset($dadosForm) ? $dadosForm['tipos'] : [];
                                    if($pokemon && $pokemon->getTipos()) {
                                        $tiposSelecionados = array_map(function($t) { return $t->getId(); }, $pokemon->getTipos());
                                    }
                                    echo (in_array($tipo->getId(), $tiposSelecionados)) ? 'selected' : '';
                                    ?>>
                                <?= $tipo->getNome() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Selecione um ou mais tipos para este Pokémon.</div>
                </div>

                <div class="mb-3">
                    <label for="regiao" class="form-label">Região:</label>
                    <select class="form-select" id="regiao" name="regiao">
                        <option value="">Selecione uma região</option>
                        <?php foreach ($listaRegioes as $regiao): ?>
                            <option value="<?= $regiao->getId() ?>" 
                                    <?php 
                                    $selectedRegiao = isset($dadosForm) ? $dadosForm['regiao'] : ($pokemon && $pokemon->getRegiao() ? $pokemon->getRegiao()->getId() : '');
                                    echo ($selectedRegiao == $regiao->getId()) ? 'selected' : '';
                                    ?>>
                                <?= $regiao->getNome() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>            <div class="mb-3">
                    <button type="submit" class="btn btn-success">
                        <?= ($pokemon && $pokemon->getId()) ? 'Atualizar' : 'Cadastrar' ?>
                    </button>
                    <a href="listar.php" class="btn btn-outline-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Incluir footer
include_once(__DIR__ . "/../include/footer.php");
?>
