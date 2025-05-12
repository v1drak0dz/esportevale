<div class="container my-5">

    <?php if(Session::getInstance()->has('error')): ?>
        <div class="alert alert-danger">
            <?php echo Session::getInstance()->get('error'); ?>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-md-6">
        <form class="form bg-body-tertiary p-3 rounded shadow my-2" method="post" action="/execute_register">
            <!-- Name input -->
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Seu nome aqui...">
            </div>
            <!-- Username input -->
            <div class="mb-3">
                <label for="username" class="form-label">Usuário</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Nome de usuario (sem espaços)">
            </div>
            <!-- Email input -->
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="nome@example.com">
            </div>
            <!-- Password input -->
            <div class="mb-3">
                <label for="password" class="form-label">Senha</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="**********">
            </div>

            <!-- Submit button -->
            <button type="submit" id="cadastrar" class="btn btn-primary w-100">Cadastrar</button>
        </form>
        </div>
    </div>
</div>

<!-- <a href="https://br.freepik.com/imagem-ia-gratis/jogo-de-futebol-noturno_414899582.htm#fromView=keyword&page=1&position=31&uuid=2da1acb3-49e1-47de-9447-86f1f17d50fb&query=Estadio">Imagem de freepik</a> -->