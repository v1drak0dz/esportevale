<div class="container bg-light mt-2 py-2 shadow rounded col-4">
    <h2>Criar Campeonato</h2>
    <hr />
    <form action="/leagues/createLeague" method="POST">
        <div class="mb-3"><input type="hidden" name="id" value="<?php echo (isset($video) && $video != null) ? $video->video_id : ''; ?>"></div>
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Ex.: São Paulo 2 x 0 Corinthias" value="<?php echo (isset($video) && $video != null) ? $video->video_title : ''; ?>" />
        </div>
        <div class="mb-3">
            <label for="url" class="form-label">Link do vídeo</label>
            <input type="url" name="url" class="form-control" id="url" placeholder="https://www.youtube.com/video" value="<?php echo (isset($video) && $video != null) ? $video->video_url : ''; ?>" />
        </div>
        <div class="mb-3">
            <label for="capa" class="form-label">Capa pro Vídeo</label>
            <input type="file" name="capa" class="form-control" value="<?php echo (isset($video) && $video != null) ? $video->video_capa : ''; ?>"/>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Categoria</label>
            <select name="category" class="form-select">
                <?php $selection = (isset($video) && $video != null) ? $video->video_category : ''; ?>
                <option value="" <?php echo $selection == '' ? 'selected' : ''; ?>>Selecione uma categoria</option>
                <option value="entrevista" <?php echo $selection == 'entrevista' ? 'selected' : ''; ?>>Entrevista</option>
                <option value="jogo" <?php echo $selection == 'jogo' ? 'selected' : ''; ?>>Jogo</option>
            </select>
        </div>
        <div class="mb-3 buttons d-flex justify-content-end">
            <button name="action" type="submit" value="cancel" class="btn btn-danger mx-2">Cancelar</button>
            <button name="action" type="submit" value="save" class="btn btn-success mx-2">Salvar</button>
        </div>
    </form>
</div>
