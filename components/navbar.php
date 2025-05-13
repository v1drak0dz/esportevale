<?php
function isActive($url) {
  return $_SERVER['REQUEST_URI'] == $url ? 'active' : '';
}
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary d-flex justify-content-between">
  <div class="container align-items-center justify-content-around">
    <a class="navbar-brand" href="/">
      <img src="/content/esportevale.png" alt="Esporte Vale" width="72">
      Esporte Vale
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
                <a href="/league/tabela?id=<?php echo $league->id; ?>" class="nav-link"><?php echo $league->nome; ?></a>
              </li>

            <?php endforeach; ?>
          </ul>
        </li>
        <li class="nav-item">
          <a href="/news_index" class="nav-link">Notícias</a>
        </li>
        <?php if(Session::getInstance()->has('user')): ?>
          <li class="nav-item">
            <a class="nav-link <?php echo isActive('/dashboard/news'); ?>" href="/dashboard/news">Minhas Notícias</a>
          </li>
          <?php if(Session::getInstance()->isAdmin()): ?>
            <li class="nav-item">
              <a id="minhas-ligas" class="nav-link <?php echo isActive('/dashboard/leagues'); ?>" href="/dashboard/leagues">Minhas Ligas</a>
            </li>
          <?php endif; ?>
          <li class="nav-item">
            <a class="nav-link" href="/logout">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a id="login" href="/login" class="nav-link">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>