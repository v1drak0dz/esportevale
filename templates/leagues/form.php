<div class="container bg-light mt-2 py-2">
    <form action="/leagues/save" method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Titulo da Notícia" value="<?php echo $league != null ? $league->nome : ''; ?>">
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Tabela</label>
            <textarea class="form-control" id="league_content_table" name="table_content"></textarea>
        </div>
        <div class="mb-3">
            <label for="content" class="form-label">Rodadas</label>
            <textarea class="form-control" id="league_content_rounds" name="round_content"></textarea>
        </div>
        <div class="mb-3 buttons d-flex justify-content-end">
            <button name="action" type="submit" value="cancel" class="btn btn-danger mx-2">Cancelar</button>
            <button name="action" id="submit" type="submit" value="save" class="btn btn-success mx-2">Salvar</button>
        </div>
    </form>
</div>