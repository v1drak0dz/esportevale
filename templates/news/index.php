<div class="container">
    <div class="container shadow rounded bg-light m-2 p-2">
        <h2>Buscar Notícias</h2>
        <form action="/news/index" method="get">
            <div class="mb-3 d-flex">
                <input type="text" name="query" class="form-control" placeholder="Buscar notícias">
                <button type="submit" class="ms-2 btn btn-success d-flex"><i class="bi bi-search me-2"></i> Buscar</button>
            </div>
        </form>
    </div>
    <div class="container bg-light shadow rounded m-2 p-2">
        <h2>Notícias</h2>
        <ul class="list-group">
            <?php foreach ($newslist as $n): ?>
                <a href="/news/show?id=<?php echo $n->post_id; ?>" class="list-group-item list-group-item-action my-2 rounded shadow" aria-current="true" href="#">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1 has-text-uppercase border-bottom w-100"><?php echo $n->post_title; ?></h5>
                    </div>
                    
                    <small class="text-muted" style="font-size: .75rem;">
                        <?php echo 'Publicado em ' .date('d/m/Y H:i', strtotime($n->post_created)) . ', por ' . $n->post_author_name; ?>
                    </small>
                </a>
            <?php endforeach; ?>
        </ul>
    </div>
</div>