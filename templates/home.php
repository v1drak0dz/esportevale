<div class="container mb-5">



<div id="top_ad">
  <?php include 'components/advertisement.php'; ?>
</div>

<div class="row justify-content-center">



<section class="col-12 col-md-8">
    <div class="container bg-light py-2 mt-2 rounded shadow">
        <h3>Notícias</h3>
        <div class="my-2">
            <ul class="list-group list-group-flush">
                <?php foreach ($newslist as $news): ?>
                    <a class="list-group-item list-group-item-action my-1 shadow-sm rounded bg-white" href="/news/show?id=<?php echo $news->post_id; ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1 has-text-uppercase border-bottom"><?php echo $news->post_title; ?></h5>
                        </div>

                        <p class="my-1" style="font-size: .75rem;"><?php echo limitarTexto($news->post_content, 250); ?></p>

                        <small style="font-size: 8pt;"><?php echo 'Publicado em ' . date('d/m/Y H:i', strtotime($news->post_created)) ?></small>
                    </a>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php include_once 'components/alert.php'; ?>
    </div>
</section>

<div class="col-12 col-md-4">
    <div class="container bg-light py-2 mt-2 rounded shadow">
        <h3>Campeonatos</h3>
        <div class="my-2">
            <ul class="list-group">
    <?php foreach ($leagues as $league): ?>
                    <a class="list-group-item list-group-item-action my-2 rounded shadow" aria-current="true" href="/leagues/show/tabela?campeonato=<?php echo $league->nome; ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 has-text-uppercase w-100"><?php echo $league->nome; ?></h5>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
</div>

<div id="bottom_ad">
  <?php include 'components/advertisement.php'; ?>
</div>

</div>
</div>