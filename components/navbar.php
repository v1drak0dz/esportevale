<?php
function isActive($url)
{
  return $_SERVER['REQUEST_URI'] == $url ? 'active' : '';
}
?>

<nav class="navbar navbar-expand-lg bg-body-tertiary d-flex justify-content-between">
  <div class="container align-items-center justify-content-around">
    <a class="navbar-brand" href="/">
      <i>
        EsporteVale
      </i>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a href="/" class="nav-link <?php echo isActive('/') || isActive(''); ?>">Página Inicial</a>
        </li>
        <li class="nav-item">
          <a href="/videos/lista" class="nav-link">Videos</a>
        </li>
        <?php if (Session::getInstance()->has('user')): ?>
          <li class="nav-item">
            <a data-bs-toggle="modal" data-bs-target="#postModal" href="#" class="nav-link">Publicar</a>
          </li>
          <li class="nav-item">
            <a href="/ligas/lista" class="nav-link">Ligas</a>
          </li>
          <li class="nav-item">
            <a href="/auth/logout" class="nav-link">Sair</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a data-bs-toggle="modal" data-bs-target="#loginModal" href="#" class="nav-link">Login</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="loginModalLabel">Login</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/auth/login" method="post">
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="text" class="form-control" name="email" id="email">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input type="password" class="form-control" name="password" id="password">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Entrar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Post Modal -->
<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="postModalLabel">Postar</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="/videos/postar" method="post">
          <div class="mb-3">
            <label for="title" class="form-label">Título</label>
            <input type="text" class="form-control" name="title" id="title">
          </div>
          <div class="mb-3">
            <label for="link" class="form-label">Link</label>
            <input type="text" class="form-control" name="link" id="link">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Postar</button>
      </div>
    </div>
  </div>
</div>
