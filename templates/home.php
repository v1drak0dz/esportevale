<div class="container mb-5">



<div id="header_banner">
  <?php include 'components/advertisement.php'; ?>
</div>

<div class="row justify-content-center">


<style>
.img-shadow {
    filter: drop-shadow(0px 4px 4px rgba(255, 255, 255, 0.75));
    width: 100%;
}

@media (max-width: 768px) {
    .img-shadow {
        width: 50%;
    }
}



</style>

<section class="col-12 col-md-8">
    <div class="img-shadow mx-auto d-flex justify-content-center">
        <img src="/content/esportevale-removebg-preview.png" alt="Logo Esporte Vale" style="filter: drop-shadow(0px 4px 4px rgba(0, 0, 0, 0.25));">
    </div>
    
</section>

<div class="col-12 col-md-4">
    <div class="container bg-light py-2 mt-2 rounded shadow">
        <h3>Campeonatos</h3>
        <div class="my-2">
            <ul class="list-group">
                <?php foreach ($leagues as $league): ?>
                    <a class="list-group-item list-group-item-action my-2 rounded shadow" aria-current="true" href="/leagues/show/tabela?campeonato=<?php echo $league['campeonato']; ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1 has-text-uppercase w-100"><?php echo $league['campeonato']; ?></h5>
                        </div>
                    </a>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    $('#news-toggle').click(function() {
        $('#newslist').show();
        $('#videoslist').hide();
        if (!$(this).hasClass('active')) {
            $(this).addClass('active')
            $('#video-toggle').removeClass('active')
        } else {
            $(this).removeClass('active')
            $('#video-toggle').addClass('active')
        }
    });
    $('#video-toggle').click(function() {
        $('#newslist').hide();
        $('#videoslist').show();
        if (!$(this).hasClass('active')) {
            $(this).addClass('active')
            $('#news-toggle').removeClass('active')
        } else {
            $(this).removeClass('active')
            $('#news-toggle').addClass('active')
        }
    });
</script>

<div id="footer_banner">
  <?php include 'components/advertisement.php'; ?>
</div>

</div>
</div>