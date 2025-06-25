<?php
function isActive($url) {
  return $_SERVER['REQUEST_URI'] == $url ? 'active' : '';
}
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary d-flex justify-content-between">
  <div class="container align-items-center justify-content-around">
    <a class="navbar-brand" href="/">
      <i>
      <!-- <img src="/content/esportevale-removebg-preview.png" alt="Esporte Vale" width="72"> -->
      EsporteVale
      </i>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a href="/" class="nav-link <?php echo isActive('/') || isActive(''); ?>">Página Inicial</a>
        </li>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Campeonatos
          </a>
          <ul class="dropdown-menu">
            <?php foreach($leagues as $league): ?>

              <li class="nav-item">
                <a href="/leagues/show/tabela?campeonato=<?php echo $league->campeonato; ?>" class="nav-link"><?php echo $league->campeonato; ?></a>
              </li>

            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a href="/news/index" class="nav-link">Notícias</a>
        </li>
        <li class="nav-item">
          <a href="/news/videos" class="nav-link">Videos</a>
        </li>
        <?php if(Session::getInstance()->has('user')): ?>

          <li class="nav-item dropdown">
            <a href="#" role="button" class="nav-link dropdown-toggle" id="pubDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              Publicar
            </a>
            <ul class="dropdown-menu" aria-labelledby="pubDropdown">
              <li>
                <a href="/news/form" class="dropdown-item">Notícias</a>
              </li>
              <li>
                <a href="/news/video" class="dropdown-item">Videos</a>
              </li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a href="#" role="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false" class="nav-link dropdown-toggle">
              Usuário
            </a>
            <ul class="dropdown-menu" aria-labelledby="profileDropdown">
              <li>
                <a href="/news/dashboard" class="dropdown-item">Minhas Notícias</a>
              </li>
              <li>
                <a href="/news/dashboardVideos" class="dropdown-item">Meus Videos</a>
              </li>
              <?php if(Session::getInstance()->isAdmin()): ?>
                <li>
                  <a id="minhas-ligas" class="dropdown-item" href="/leagues/dashboard">Minhas Ligas</a>
                </li>
              <?php endif; ?>
              <li>
                <a href="/auth/logout" class="dropdown-item">Logout</a>
              </li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a id="login" href="/auth/login" class="nav-link">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>