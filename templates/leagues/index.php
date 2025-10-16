<?php

$isTabela = strpos($_SERVER['REQUEST_URI'], 'tabela') !== false;

?>

<div class="container d-flex flex-column align-items-center">

<div class="container bg-light p-2 m-2 shadow rounded">
    <h2><?php echo $currLeague[0]['campeonato']; ?></h2>
    <ul class="nav nav-tabs mb-2">
        <li class="nav-item">
            <a href="/leagues/show/tabela?campeonato=<?php echo $currLeague[0]['campeonato']; ?>" class="nav-link <?php echo $isTabela ? 'active' : ''; ?>">Classificação</a>
        </li>
        <div class="nav-item">
            <a href="/leagues/show/jogos?campeonato=<?php echo $currLeague[0]['campeonato']; ?>&rodada=<?php echo $rodada_atual_header; ?>" class="nav-link <?php echo !$isTabela ? 'active' : ''; ?>">Rodadas</a>
        </div>
    </ul>

    <div class="table-classification--expansive-wrapper">
        <?php if ($isTabela): ?>

            <div class="row rounded container">
            <?php if (!empty($groupedLeague)): ?>
                <div class="row">
                <?php foreach($groupedLeague as $key => $group): ?>
                    <div class="col-12">
                        <table class="table rounded border mb-4">
                            <thead>
                                <tr><h3><?php echo $key; ?></h3></tr>
                                <tr class="table-dark">
                                    <th scope="col" class="text-center">#</th>
                                    <th scope="col">Time</th>
                                    <th scope="col" class="text-center">Pontos</th>
                                    <th scope="col" class="text-center">J</th>
                                    <th scope="col" class="text-center">V</th>
                                    <th scope="col" class="text-center">E</th>
                                    <th scope="col" class="text-center">D</th>
                                    <th scope="col" class="text-center">GP</th>
                                    <th scope="col" class="text-center">GC</th>
                                    <th scope="col" class="text-center">SG</th>
                                    <th scope="col" class="text-center">Aproveitamento</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($group as $index => $linha): ?>
                                    <tr>
                                        <td class="text-center"><?php echo $index + 1; ?></td>
                                        <td>
                                            <img class="img-fluid me-2" style="width: 24px;" src="<?php echo $linha['brasao']; ?>" alt="">
                                            <?php echo $linha['time_nome']; ?>
                                        </td>
                                        <td class="text-center"><?php echo $linha['pontos']; ?></td>
                                        <td class="text-center"><?php echo $linha['jogos']; ?></td>
                                        <td class="text-center"><?php echo $linha['vitorias']; ?></td>
                                        <td class="text-center"><?php echo $linha['empates']; ?></td>
                                        <td class="text-center"><?php echo $linha['derrotas']; ?></td>
                                        <td class="text-center"><?php echo $linha['gols_pro']; ?></td>
                                        <td class="text-center"><?php echo $linha['gols_contra']; ?></td>
                                        <td class="text-center"><?php echo $linha['saldo_gols']; ?></td>
                                        <td class="text-center"><?php echo $linha['jogos'] > 0 ? round((($linha['pontos'] / ($linha['jogos'] * 3))*100), 2) . ' %' : '00.00 %'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            </div>

            <div class="row rounded container">
            <table class="table rounded border">
                <thead>
                    <tr class="table-dark">
                        <th scope="col" class="text-center">#</th>
                        <th scope="col">Time</th>
                        <th scope="col" class="text-center">Pontos</th>
                        <th scope="col" class="text-center">J</th>
                        <th scope="col" class="text-center">V</th>
                        <th scope="col" class="text-center">E</th>
                        <th scope="col" class="text-center">D</th>
                        <th scope="col" class="text-center">GP</th>
                        <th scope="col" class="text-center">GC</th>
                        <th scope="col" class="text-center">SG</th>
                        <th scope="col" class="text-center">Aproveitamento</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($currLeague as $index => $linha): ?>
                    <?php
                        if ($index < 4) $division = 'success';
                        else if ($index >= 4 && $index < ($rows_count-4)) $division = '';
                        else $division = 'danger';
                    ?>
                        <tr class="table-<?php echo $division; ?>">
                            <td class="text-center"><?php echo $index + 1; ?></td>
                            <td>
                                <img class="img-fluid me-2" style="width: 24px;" src="<?php echo $linha['brasao']; ?>" alt="">
                                <?php echo $linha['time_nome']; ?>
                            </td>
                            <td class="text-center"><?php echo $linha['pontos']; ?></td>
                            <td class="text-center"><?php echo $linha['jogos']; ?></td>
                            <td class="text-center"><?php echo $linha['vitorias']; ?></td>
                            <td class="text-center"><?php echo $linha['empates']; ?></td>
                            <td class="text-center"><?php echo $linha['derrotas']; ?></td>
                            <td class="text-center"><?php echo $linha['gols_pro']; ?></td>
                            <td class="text-center"><?php echo $linha['gols_contra']; ?></td>
                            <td class="text-center"><?php echo $linha['saldo_gols']; ?></td>
                            <td class="text-center"><?php echo $linha['jogos'] > 0 ? round((($linha['pontos'] / ($linha['jogos'] * 3))*100), 2) . ' %' : '00.00 %'; ?></td>
                        </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>

        <?php else: ?>

            <div class="row container-fluid justify-content-center">
                <?php
                // Garante que $rodadasRaw esteja ordenado
                sort($rodadasRaw);
                $rodada_min = $rodadasRaw[0];
                $rodada_max = end($rodadasRaw);
                ?>
                <nav class="d-flex justify-content-center my-4">
                    <ul class="pagination">
                        <li class="page-item <?php echo ($rodada_atual <= $rodada_min) ? 'disabled' : ''; ?>">
                            <a class="page-link"
                            href="<?php echo ($rodada_atual <= $rodada_min) ? '#' : '/leagues/show/jogos?campeonato=' . $currLeague[0]['campeonato'] . '&rodada=' . ($rodada_atual - 1); ?>">
                            Anterior
                            </a>
                        </li>

                        <?php foreach ($rodadas as $_): ?>
                            <li class="page-item <?php echo ($_ == $rodadas_filtradas[0]['rodada']) ? 'active' : ''; ?>">
                                <a class="page-link" href="/leagues/show/jogos?campeonato=<?php echo $currLeague[0]['campeonato']; ?>&rodada=<?php echo $_; ?>"><?php echo $_; ?></a>
                            </li>
                        <?php endforeach; ?>

                        <li class="page-item <?php echo ($rodada_atual >= $rodada_max) ? 'disabled' : ''; ?>">
                            <a class="page-link"
                            href="<?php echo ($rodada_atual >= $rodada_max) ? '#' : '/leagues/show/jogos?campeonato=' . $currLeague[0]['campeonato'] . '&rodada=' . ($rodada_atual + 1); ?>">
                            Próximo
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php foreach($rodadas_filtradas as $index => $linha): ?>
                    <div class="col-12 col-lg-6 col-md-12 col-sm-12 justify-content-center">
                        <div class="card mb-3">
                            <style>
                                .text-rodada {
                                    font-size: 14pt !important;
                                }
                                @media (max-width: 768px) {
                                    .text-rodada {
                                        font-size: 8pt !important;
                                    }
                                }
                            </style>
                            <div class="bg-primary text-white card-header d-flex justify-content-between align-items-center">
                                <p class="mb-0 text-rodada"><?php echo $linha['data_partida']; ?></p>
                                <p class="mb-0 text-rodada"><?php echo "Rodada " . $linha['rodada']; ?></p>
                                <p class="mb-0 text-rodada"><?php echo $linha['finalizada'] == 1 ? "Finalizada" : "Pendente"; ?></p>
                            </div>
                            <div class="card-body d-flex justify-content-evenly align-items-center">
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <img class="img-fluid me-2" style="width: 32px;" src="<?php echo $linha['brasao_casa']; ?>" alt="">
                                    <span class="fw-bold text-center text-rodada"><?php echo $linha['time_casa']; ?></span>
                                </div>
                                <div class="text-center mx-5 d-flex align-items-center justify-content-center" style="font-size: 1.25rem;">
                                    <p class="fw-bold"><?php echo $linha['finalizada'] == 1 ? $linha['gols_casa'] : ''; ?></p>
                                    <p class="fw-bold mx-2" style="font-size: 2rem">X</p>
                                    <p class="fw-bold"><?php echo $linha['finalizada'] == 1 ? $linha['gols_fora'] : ''; ?></p>
                                </div>
                                <div class="d-flex flex-column justify-content-center align-items-center">
                                    <img class="img-fluid me-2" style="width: 32px;" src="<?php echo $linha['brasao_fora']; ?>" alt="">
                                    <span class="fw-bold text-center text-rodada"><?php echo $linha['time_fora']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

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