<div class="container my-5">

<div class="row justify-content-center">

<section class="col-12 col-md-8">
    <div class="container bg-light py-2 mt-2 rounded shadow">
        <header>
        <h2><?php echo $news->post_title; ?></h2>
        <div class="d-flex justify-content-between">
            <div>
            <?php foreach ($tags as $tag): ?>
                <span class="badge text-bg-info rounded-pill"><?php echo $tag['nome']; ?></span>
            <?php endforeach; ?>
            </div>
            <small class="text-muted" style="font-size: .75rem;">
                <p>
                <?php echo 'Publicado em ' . date('d/m/Y H:i', strtotime($news->post_created)) . ', por ' . $news->post_author_name; ?>
                </p>
            </small>
        </div>
        </header>
        <hr class="mt-0">
        <main class="my-2"><?php echo $news->post_content; ?></main>
        <footer>
            <?php if ($isLiked): ?>
                <button class="btn btn-outline-danger" id="like-btn" data-post-id="<?php echo $news->post_id;?>"><i id="like-icon" class="bi bi-hand-thumbs-up-fill"></i> <span id="like-text">Descurtir</span></button> <span id="like-qtt" style="font-size: .75rem;">(<?php echo $likeCount; ?>)</span>
            <?php else: ?>
                <button class="btn btn-outline-success" id="like-btn" data-post-id="<?php echo $news->post_id;?>"><i id="like-icon" class="bi bi-hand-thumbs-up"></i> <span id="like-text">Curtir</span></button> <span id="like-qtt" style="font-size: .75rem;">(<?php echo $likeCount; ?>)</span>
            <?php endif; ?>
            <script>
                $('#like-btn').on('click', function() {
                    var postId = $(this).data('post-id');

                    $.get("/news/like?id=" + postId, function(response) {
                        var resposta = JSON.parse(response);

                        if (resposta.msg) {
                            $('#like-icon').addClass("bi-hand-thumbs-up-fill");
                            $('#like-icon').removeClass("bi-hand-thumbs-up");
                            $('#like-btn').removeClass("btn-outline-success");
                            $('#like-btn').addClass("btn-outline-danger");
                            $('#like-text').text(' Descurtir');
                        } else {
                            $('#like-icon').removeClass("bi-hand-thumbs-up-fill");
                            $('#like-icon').addClass("bi-hand-thumbs-up");
                            $('#like-btn').addClass("btn-outline-success");
                            $('#like-btn').removeClass("btn-outline-danger");
                            $('#like-text').text(' Curtir');
                        }

                        $('#like-qtt').text('(' + resposta.qtt + ')');

                    });
                })
            </script>
        </footer>
    </div>

    <div class="container bg-light py-2 mt-2 rounded shadow">
        <h3>Comentarios <span style="font-size: .75rem;">(<?php echo count($news_commentary); ?>)</span></h3>
        <form class="reply-form mt-2" method="post" action="/news/comment">
            <div class="mb-2">
                <textarea class="form-control" name="reply" rows="2" placeholder="Escreva sua resposta..."></textarea>
            </div>
            <input type="hidden" name="parent_id" value="<?php echo $news->post_id ; ?>">
            <button type="submit" class="btn btn-sm btn-primary">Enviar</button>
            <button type="button" class="btn btn-sm btn-secondary cancel-reply">Cancelar</button>
        </form>

        <hr class="my-2">

        <div class="container">
            <?php echo $commentaries; ?>
        </div>
    </div>
</section>

<section class="col-12 col-md-4">
    <div class="container bg-light py-2 mt-2 rounded shadow">
        <h3>Mais Notícias</h3>
        <div class="my-2">
            <ul class="list-group">
                <?php 
                    $total = count($relatedNews);
                    foreach ($relatedNews as $index => $news):
                        $isLast = ($index === $total - 1);
                ?>
                    <a class="list-group-item list-group-item-action my-2 rounded shadow" aria-current="true" href="/news/show?id=<?php echo $news->post_id; ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 has-text-uppercase border-bottom w-100"><?php echo $news->post_title; ?></h5>
                        </div>
                        
                        <small class="text-muted" style="font-size: .75rem;">
                            <?php echo 'Publicado em ' .date('d/m/Y H:i', strtotime($news->post_created)) . ', por ' . $news->post_author_name; ?>
                        </small>
                    </a>
                    <?php if (!$isLast): ?>
                        <hr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<script>

$(document).ready(function() {
    $('.comentario').on('click', '.reply-btn', function(e) {
        e.preventDefault();

        $('.reply-form').remove();

        var commentId = $(this).data('comment-id');

        var replyFormHtml = `
            <form class="reply-form mt-2" method="post" action="/news/comment">
                <div class="mb-2">
                    <textarea class="form-control" name="reply" rows="2" placeholder="Escreva sua resposta..."></textarea>
                </div>
                <input type="hidden" name="parent_id" value="` + commentId + `">
                <button type="submit" class="btn btn-sm btn-primary">Enviar</button>
                <button type="button" class="btn btn-sm btn-secondary cancel-reply">Cancelar</button>
            </form>
        `;

        $(this).closest('.comentario').append(replyFormHtml);
    });

    $(document).on('click', '.cancel-reply', function(e) {
        e.preventDefault();
        $(this).closest('.reply-form').remove();
    });
});

</script>

</div>

</div>