<div class="container bg-light mt-2 py-2">
    <form action="/news/add" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Titulo da Notícia" value="<?php echo $news != null ? $news->post_title : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="tags" class="form-">Tags</label>
            <input type="text" class="form-control" id="tags" name="tags" placeholder="Tags da Notícia: série A, Amador, Flamengo...">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Conteúdo</label>
            <textarea class="form-control" id="content" name="content"><?php echo $news != null ? htmlspecialchars($news->post_content) : ''; ?></textarea>
        </div>
        <div class="mb-3 buttons d-flex justify-content-end">
            <button name="action" type="submit" value="cancel" class="btn btn-danger mx-2">Cancelar</button>
            <button name="action" type="submit" value="save" class="btn btn-success mx-2">Salvar</button>
        </div>
    </form>
</div>