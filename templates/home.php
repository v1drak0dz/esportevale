<div class="container my-5">

<div class="row justify-content-center">
  <div class="col-12">
    <div class="container bg-light py-2 mt-2 rounded shadow">
      <h3>Conheça também!</h3>
      <div class="row justify-content-evenly gy-3">
        <div class="col-12 col-md-5 bg-white rounded shadow d-flex align-items-center p-2">
          <a href="https://radiotvaki.com.br/" class="d-flex flex-column flex-sm-row align-items-center text-decoration-none text-dark w-100">
            <div class="me-sm-2 mb-2 mb-sm-0 text-center">
              <img src="/content/radiotvaki.png" alt="RadioTvAki" class="img-fluid" style="max-width: 100px;">
            </div>
            <div>
              <h5>RadioAki</h5>
              <p class="mb-0">Fique por dentro das partidas de futebol, mesmo se não puder assistir ao vivo.</p>
            </div>
          </a>
        </div>

        <div class="col-12 col-md-5 bg-white rounded shadow d-flex align-items-center p-2">
          <a href="https://carroaki.com.br/" class="d-flex flex-column flex-sm-row align-items-center text-decoration-none text-dark w-100">
            <div class="me-sm-2 mb-2 mb-sm-0 text-center">
              <img src="/content/carroaki.png" alt="CarroAki" class="img-fluid" style="max-width: 100px;">
            </div>
            <div>
              <h5>CarroAki</h5>
              <p class="mb-0">Procurando um carro? Aqui você pode encontrar carros para todos os gostos.</p>
            </div>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="row justify-content-center">

<section class="col-12 col-md-8">
    <div class="container bg-light py-2 mt-2 rounded shadow table-classification--expansive-wrapper">
        <h2>Brasileirão Série A</h2>
        <?php echo $leaguetable->tabela_html; ?>
    </div>
</section>

<section class="col-12 col-md-4">
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
                    </a>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php include_once('components/alert.php'); ?>
    </div>
</section>

</div>
</div>