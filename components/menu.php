<?php

$uri = $_SERVER['REQUEST_URI'];

$isNews = strpos($uri, 'news') !== false;
$isTables = strpos($uri, 'tables') !== false;
$isRounds = strpos($uri, 'rounds') !== false;

if ($isNews) {
    $newsCurrent = array('active', 'true');
    $tablesCurrent = array('', 'false');
    $roundsCurrent = array('', 'false');
}
else if ($isTables) {
    $tablesCurrent = array('active', 'true');
    $newsCurrent = array('', 'false');
    $roundsCurrent = array('', 'false');
}
else if ($isRounds) {
    $roundsCurrent = array('active', 'true');
    $newsCurrent = array('', 'false');
    $tablesCurrent = array('', 'false');
}

?>

<div class="d-flex justify-content-center h-100">
    <ul class="list-group">
        <a href="/dashboard/news" class="list-group-item list-group-item-action <?php echo $newsCurrent[0]; ?>" aria-current="<?php echo $newsCurrent[1] ?>">Notícias</a>
        <a href="/dashboard/tables" class="list-group-item list-group-item-action <?php echo $tablesCurrent[0]; ?>" aria-current="<?php echo $tablesCurrent[1] ?>">Tabelas</a>
        <a href="/dashboard/rounds" class="list-group-item list-group-item-action <?php echo $roundsCurrent[0]; ?>" aria-current="<?php echo $roundsCurrent[1] ?>">Rodadas</a>
    </ul>
</div>