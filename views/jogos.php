<div class="container-lg bg-light mt-2 py-2">
  <label>Selecione a rodada!</label>
  <select name="leagues" class="form-select mb-3" id="league-match">
    <option value="0">Selecione uma rodada</option>
  </select>
  <div class="d-flex justify-content-center flex-column align-items-center">
    <h3 class="text-center">Partidas</h3>
    <?php if (Session::getInstance()->has('user')): ?>
      <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#createMatch">Criar Partida</button>
    <?php endif; ?>
  </div>
  <div style="text-align: center; font-size: 24px;">
    <span style="letter-spacing: 0;">───────────────</span>
    <span style="color: black;">●</span>
    <span style="letter-spacing: 0;">───────────────</span>
  </div>
  <div id="cards-container" class="row col-12 col-lg-6 col-md-6 col-sm-12 w-100"></div>
  <!-- Toast container -->
  <div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

  <div class="modal" tabindex="-1" id="createMatch" aria-labelledby="createMatchLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="createMatchLabel" class="modal-title">Criar Partida</h5>
          <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/leagues/createMatch" method="POST" class="row">
            <input type="hidden" name="campeonato" value="<?= $_GET['campeonato'] ?>">

            <div class="">
              <p class="mt-0 text-muted">Detalhes da Partida</p>
            </div>

            <div class="row">
              <div class="mb-3 col-md-6 col-12">
                <label for="date" class="form-label">Data</label>
                <input type="datetime-local" name="date" id="date" class="form-control" value="<?= date('d/m/Y H:i') ?>">
              </div>
            </div>

            <div class="row">
              <div class="mb-3 col-md-6 col-12">
                <label for="grupo" class="form-label">Grupo</label>
                <input type="text" name="grupo" id="grupo" class="form-control">
              </div>

              <div class="mb-3 col-md-6 col-12">
                <label for="rodada" class="form-label">Rodada</label>
                <input type="text" name="rodada" id="rodada" class="form-control">
              </div>
            </div>

            <div class="mb-3">
              <input type="checkbox" class="form-check-input" name="finalizada">
              <label for="finalizada" class="form-check-label">Finalizada</label>
            </div>

            <div class="">
              <hr class="mb-0">
              <p class="mt-0 text-muted">Time de Casa</p>
            </div>

            <div class="mb-3 col-md-6 col-12">
              <label for="home-team" class="form-label">Time Casa</label>
              <input type="text" name="home-team" id="home-team" class="form-control">
            </div>
            <div class="mb-3 col-md-6 col-12">
              <label for="home-goals" class="form-label">Gols Casa</label>
              <input type="number" name="home-goals" value='' class="form-control">
            </div>
            <div class="mb-3 col-md-6 col-12">
              <label for="brasao_casa" class="form-label">Brasão Casa</label>
              <input type="text" name="brasao_casa" id="brasao_casa" class="form-control">
            </div>

            <div class="">
              <hr class="mb-0">
              <p class="mt-0 text-muted">Time de Fora</p>
            </div>

            <div class="mb-3 col-md-6 col-12">
              <label for="outer-team" class="form-label">Time Fora</label>
              <input type="text" name="outer-team" id="outer-team" class="form-control">
            </div>
            <div class="mb-3 col-md-6 col-12">
              <label for="outer-goals" class="form-label">Gols Fora</label>
              <input type="number" name="outer-goals" value='' class="form-control">
            </div>
            <div class="mb-3 col-md-6 col-12">
              <label for="brasao_fora" class="form-label">Brasão Fora</label>
              <input type="text" name="brasao_fora" id="brasao_fora" class="form-control">
            </div>

            <div class="d-flex justify-content-end">
              <button type="submit" class="btn btn-primary">Criar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .select2-container {
    z-index: 9999 !important;
  }
</style>


<script>
  $(document).ready(function() {

    $("#home-team").select2({
      theme: 'bootstrap-5',
      allowClear: true,
      tags: true,
      data: <?= json_encode($times); ?>
    })
    $("#outer-team").select2({
      theme: 'bootstrap-5',
      allowClear: true,
      tags: true,
      data: <?= json_encode($times); ?>
    })
  })
</script>

<script>
  let rodadas = <?php echo json_encode($jogos); ?>;
  const isAdmin = Boolean(<?= isset($_SESSION['user']); ?>);

  function criarCard(partida, index) {
    if ($('#league-match').val() != 0 && (partida.rodada != $('#league-match').val())) return;

    var card = `
    <form action="/leagues/update" class="col-md-6" method="POST">
      <div class="justify-content-center">
        <div class="card mb-3 shadow-sm border-0">

          <!-- Cabeçalho -->
          <div class="bg-primary text-white card-header d-flex justify-content-between align-items-center">
            <div>
              <p class="mb-0 fw-bold">${partida.campeonato}</p>
              <small>${partida.data_partida.replace('T', ' ')}</small>
            </div>
            <div class="text-end">
              <p class="mb-0">Rodada ${partida.rodada}</p>
              <p class="mb-0">Grupo ${partida.grupo ?? '-'}</p>
            </div>
          </div>

          <!-- Corpo -->
          <div class="card-body d-flex justify-content-evenly align-items-center">
            <!-- Time da casa -->
            <div class="d-flex flex-column justify-content-center align-items-center">
              <img class="img-fluid mb-2" style="width: 40px;" src="${partida.time_casa_brasao}" alt="">
              <span class="fw-bold text-center">${partida.time_casa_nome}</span>
            </div>

            <!-- Placar -->
            <div class="text-center mx-5 d-flex justify-content-center align-items-center" style="font-size: 1.25rem;">
              <input type="hidden" name="id" value="${partida.id}" />
              <p>${partida.finalizada == 1 ? partida.gols_casa : ''}</p>
              <p class="fw-bold mx-2 mb-0" style="font-size: 2rem">X</p>
              <p>${partida.finalizada == 1 ? partida.gols_fora : ''}</p>
            </div>

            <!-- Time de fora -->
            <div class="d-flex flex-column justify-content-center align-items-center">
              <img class="img-fluid mb-2" style="width: 40px;" src="${partida.time_fora_brasao}" alt="">
              <span class="fw-bold text-center">${partida.time_fora_nome}</span>
            </div>
          </div>`;

    if (isAdmin) {
      card += `
              <div class="card-footer d-flex justify-content-end align-items-center">
                <a href="/leagues/delete?id=${partida.id}&campeonato=${partida.campeonato}"
                  class="btn btn-outline-danger me-2">Excluir</a>
                <a href="/leagues/edit?id=${partida.id}" class="btn btn-primary me-2">Editar</a>
              </div>
          `
    }

    card += `
      </div>
    </form>
    `;

    $("#cards-container").append(card);
  }

  let rodadas_unicas = []
  rodadas.forEach((rodada, index) => {
    if ((rodadas_unicas.indexOf(rodada.rodada) === -1)) {
      rodadas_unicas.push(rodada.rodada);
    }
    if (rodada.rodada == 1) {
      criarCard(rodada, index);
    }
    // criarCard(rodada, index);
  })
  rodadas_unicas.forEach((rodada, index) => {
    let option = `<option value="${rodada}" ${index === 0 ? 'selected' : ''}>Rodada ${rodada}</option>`;
    $("#league-match").append(option);
  })


  $("#league-match").on('change', function() {
    $("#cards-container").empty();
    rodadas.forEach((rodada, index) => criarCard(rodada, index));
  })

  $('#cards-container').on('submit', 'form', function(e) {
    e.preventDefault();

    var form = $(this)
    var url = form.attr('action')
    var data = form.serialize()

    $.post(url, data)
      .done(function(response) {
        showToast('success', 'Rodada atualizada com sucesso!')
        location.reload()
      })
      .fail(function() {
        showToast('error', 'Erro ao atualizar rodada!')
      })

  })

  function showToast(type, message) {
    let toastHTML = `
    <div class="toast align-items-center text-bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
    `

    let $toast = $(toastHTML)
    $('#toast-container').append($toast)
    let toast = new bootstrap.Toast($toast[0])
    toast.show()

    $toast.on('hidden.bs.toast', function() {
      $(this).remove()
    })
  }
</script>