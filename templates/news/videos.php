<div class="container">
    <div class="shadow rounded bg-light m-2 p-2">
        <h2>Buscar Vídeos</h2>
        <form action="/news/index" method="get">
            <div class="mb-3 d-flex">
                <input type="text" name="query" class="form-control" placeholder="Buscar notícias">
                <button type="submit" class="ms-2 btn btn-success d-flex">
                    <i class="bi bi-search me-2"></i> Buscar
                </button>
            </div>
        </form>
    </div>

    <div class="bg-light shadow rounded m-2 p-2">
        <h2>Vídeos</h2>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3">
            <?php foreach ($videoslist as $video): ?>
                <div class="col">
                    <a target="_blank" rel="noopener noreferrer" href="<?php echo $video->video_url; ?>" class="text-decoration-none text-dark">
                        <div class="card h-100">
                            <img src="<?php echo $video->video_capa ?: 'https://via.placeholder.com/320x180.png?text=Sem+Capa'; ?>" class="card-img-top" style="height: 140px; object-fit: contain; width: 100%">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1"><?php echo $video->video_title; ?></h6>
                                <p class="mb-1">
                                    <small class="bg-primary text-white px-2 py-1 rounded"><?php echo $video->video_category; ?></small>
                                </p>
                                <p class="card-text mb-0">
                                    <small class="text-muted">Publicado em <?php echo date('d/m/Y H:i', strtotime($video->video_created_at)); ?></small>
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
