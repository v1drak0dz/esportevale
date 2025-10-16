<section class="container my-3">
    <header class="container bg-light mb-2 rounded shadow d-flex justify-content-between align-items-center">
        <h1>Minhas Ligas</h1>
        <a data-bs-toggle="modal" data-bs-target="#leagueModal" href="#" class="btn btn-primary">Adicionar Liga</a>
    </header>

    <?php include_once('components/alert.php'); ?>

    <main class="container bg-light shadow rounded py-2">
        <ul class="list-group list-group-flush">
            <?php foreach ($ligas as $liga): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <h4 class="m-0"><?= $liga['nome']; ?></h4>
                    <div class="btns">
                      <a id="<?= str_replace(' ', '-',$liga['nome']); ?>" href="/jogos/lista?campeonato=<?= $liga['id']; ?>" class="btn btn-success">Atualizar</a>
                      <a id="<?= str_replace(' ', '-',$liga['nome']); ?>" href="/ligas/deletar?id=<?= $liga['id']; ?>" class="btn btn-danger">Deletar</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</section>

<!-- Post Modal -->
<div class="modal fade" id="leagueModal" tabindex="-1" aria-labelledby="leagueModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="leagueModalLabel">Liga</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/ligas/criar" method="post">
          <div class="mb-3">
            <label for="title" class="form-label">Nome da Liga</label>
            <input type="text" class="form-control" name="title" id="title">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            <button type="submit" class="btn btn-primary">Criar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
