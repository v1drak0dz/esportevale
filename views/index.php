<div class="container mb-5">



  <div id="header_banner">
    <?php include 'components/advertisement.php'; ?>
  </div>

  <div class="row justify-content-center">


    <style>
      .img-shadow {
        filter: drop-shadow(0px 4px 4px rgba(255, 255, 255, 0.75));
        width: 100%;
      }

      @media (max-width: 768px) {
        .img-shadow {
          width: 50%;
        }
      }
    </style>

    <section class="col-12 col-md-8">
      <div class="img-shadow mx-auto d-flex justify-content-center">
        <img src="/content/esportevale-removebg-preview.png" alt="Logo Esporte Vale" style="filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));">
      </div>

    </section>

    <div class="col-12 col-md-4">
      <div class="container bg-light py-2 mt-2 rounded shadow">
        <h3>Campeonatos</h3>
        <div class="my-2">
          <ul class="list-group">
            <?php foreach ($ligas as $liga): ?>
              <li class="list-group-item shadow-sm">
                <div class="d-flex w-100 justify-content-between align-items-center">
                  <h6 class="mb-0 has-text-uppercase w-100"><?= $liga['nome'] ?></h5>
                  <div class="btn-group" role="group" aria-label="nav">
                    <a class="btn btn-primary" href="/ligas/tabela?campeonato=<?= $liga['nome'] ?>">Tabela</a>
                    <a class="btn btn-secondary" href="/ligas/jogos?campeonato=<?= $liga['nome'] ?>">Jogos</a>
                  </div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <div id="footer_banner">
      <?php include 'components/advertisement.php'; ?>
    </div>

  </div>
</div>