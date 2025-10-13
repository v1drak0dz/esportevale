<section class="container my-5">
    <header class="container bg-light mb-2 rounded shadow d-flex justify-content-between align-items-center">
        <h1>Minhas Ligas</h1>
        <a href="/leagues/create" class="btn btn-primary">Adicionar Liga</a>
    </header>

    <?php include_once('components/alert.php'); ?>

    <main class="container bg-light shadow rounded py-2">
        <ul class="list-group list-group-flush">
            <?php foreach ($leagueslist as $league): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <p class="m-0"><?php echo $league['campeonato']; ?></p>
                    <div class="btns">
                    <a id="<?php echo str_replace(' ', '-',$league['campeonato']); ?>" href="/leagues/add?campeonato=<?php echo $league['campeonato']; ?>" class="btn btn-info">Atualizar</a>
                    <a id="<?php echo str_replace(' ', '-',$league['campeonato']); ?>" href="/leagues/list?campeonato=<?php echo $league['campeonato']; ?>" class="btn btn-success">Nova Edição</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</section>

<div class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
          <form action="/league/create" method="post">
              <label class="form-label" for="campeonato">Insira o nome do campeonato</label>
              <input type="text" name="campeonato" class="form-control" />
          </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
