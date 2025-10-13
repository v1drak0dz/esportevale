<div class="container bg-light mt-2 py-2 shadow rounded col-4">
    <h2>Criar Campeonato</h2>
    <hr />
    <form action="/leagues/createLeague" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Nome da Liga</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Ex.: São Paulo 2 x 0 Corinthias" value="<?php echo (isset($video) && $video != null) ? $video->video_title : ''; ?>" />
        </div>
        <div class="mb-3 buttons d-flex justify-content-end">
            <button name="action" type="submit" value="cancel" class="btn btn-danger mx-2">Cancelar</button>
            <button name="action" type="submit" value="save" class="btn btn-success mx-2">Salvar</button>
        </div>
    </form>
</div>
