<?php 

$isTabela = strpos($_SERVER['REQUEST_URI'], 'tabela') !== false;

?>

<div class="container d-flex flex-column align-items-center">

<div class="container bg-light p-2 m-2 shadow rounded">
    <h2><?php echo $currLeague->nome; ?></h2>
    <ul class="nav nav-tabs mb-2">
        <li class="nav-item">
            <a href="/leagues/show/tabela?id=<?php echo $currLeague->id; ?>" class="nav-link <?php echo $isTabela ? 'active' : ''; ?>">Classificação</a>
        </li>
        <div class="nav-item">
            <a href="/leagues/show/jogos?id=<?php echo $currLeague->id; ?>" class="nav-link <?php echo !$isTabela ? 'active' : ''; ?>">Rodadas</a>
        </div>
    </ul>

    <div class="table-classification--expansive-wrapper">
        <?php if ($isTabela): ?>

            <?php echo $currLeague->tabela_html; ?>

        <?php else: ?>

            <?php echo $currLeague->rodada_html; ?>

        <?php endif; ?>
    </div>
</div>

<script>

let links = document.querySelectorAll('a')

links.forEach(link => {
    if (link.href.includes('futebolinterior')) {
        link.parentNode.removeChild(link);
    }
})

</script>

</div>