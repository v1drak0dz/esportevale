<div class="d-flex h-100">

<section class="container my-5">
    <header class="container bg-light mb-2 rounded shadow d-flex justify-content-between align-items-center">
        <h1>Minhas notícias</h1>
        <a href="/news/form" class="btn btn-primary">Adicionar Notícia</a>
    </header>

    
    <main class="container bg-light rounded shadow my-2 py-2">
        <?php include_once('components/alert.php'); ?>
        <ul class="list-group list-group-flush">
            <?php foreach ($newslist as $news):?>
                <li class="list-group-item d-flex justify-content-between my-2 shadow align-items-start">
                    <div class="px-2">

                    <h5 class="mb-1"><?php echo $news->post_title; ?></h5>
                    
                    <p class="mb-1 text-muted"><?php echo limitarTexto($news->post_content, 250); ?></p>

                    <?php if (Session::getInstance()->isAdmin()): ?>
                        <small class="text-secondary">
                            <?php echo 'Publicado por:  ' .  $news->post_author_name . ' | '; ?>
                        </small>
                    <?php endif; ?>

                    <small class="text-secondary">
                        <?php echo 'Atualizado em:  ' . $news->post_modified; ?>
                    </small>
                    </div>
                    <div class="buttons d-flex">
                        <a href="/news/show?id=<?php echo $news->post_id; ?>" class="btn btn-info mx-2">Ver</a>
                        <a href="/news/form?id=<?php echo $news->post_id; ?>" class="btn btn-primary mx-2">Editar</a>
                        <a href="/news/delete?id=<?php echo $news->post_id; ?>" class="btn btn-danger mx-2">Deletar</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </main>
</section>

</div>