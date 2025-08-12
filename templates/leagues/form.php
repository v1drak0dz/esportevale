<div class="container-lg bg-light mt-2 py-2">
    <label >Selecione a rodada!</label>
    <select name="leagues" class="form-select mb-3" id="league-match">
        <option value="0">Selecione uma rodada</option>
    </select>
    <div class="d-flex justify-content-center flex-column align-items-center">
        <h3 class="text-center">Partidas</h3>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target=".modal">Criar Partida</button>
        </div>
        <div style="text-align: center;      font-size: 24px;">
  <span style="letter-spacing: 0;">───────────────</span>
  <span style="color: black;">●</span>
  <span style="letter-spacing: 0;">───────────────</span>
</div>
        <div id="cards-container" class="row col-12 col-lg-6 col-md-6 col-sm-12 w-100"></div>
        <!-- Toast container -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

<div class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Criar Partida</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/leagues/add" method="POST" class="row">
                    <div class="mb-3 col-md-6">
                        <label for="date" class="form-label">Data</label>
                        <input type="text" name="date" value="<?php echo date('d-m-Y'); ?>" class="form-control">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="round" class="form-label">Rodada</label>
                        <input type="number" name="round" min="1" value='1' class="form-control">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="home-team" class="form-label">Time Casa</label>
                            <select name="home-team" id="home-team" class="form-select">
                                <?php $seleacted_team = array_rand($teams); ?>
                                <?php foreach($teams as $team): ?>
                                    <option data-img="<?php echo $team['brasao_url']; ?>" <?php echo $team['time_nome'] == $teams[$seleacted_team]['time_nome'] ? 'selected' : ''; ?> value="<?php echo $team['time_nome']; ?>">
                                        <?php echo $team['time_nome']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="outer-team" class="form-label">Time Fora</label>
                            <select name="outer-team" id="outer-team" class="form-select">
                                <?php $seleacted_team = array_rand($teams); ?>
                                <?php foreach($teams as $team): ?>
                                    <option data-img="<?php echo $team['brasao_url']; ?>" <?php echo $team['time_nome'] == $teams[$seleacted_team]['time_nome'] ? 'selected' : ''; ?> value="<?php echo $team['time_nome']; ?>">
                                        <?php echo $team['time_nome']; ?>
                                    </option>

                                <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="home-goals" class="form-label">Gols Casa</label>
                        <input type="number" name="home-goals" value='0' class="form-control">
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="outer-goals" class="form-label">Gols Fora</label>
                        <input type="number" name="outer-goals" value='0' class="form-control">
                    </div>
                    <div class="mb-3">
                        <input type="checkbox" class="form-check-input" name="finalizada">
                        <label for="finalizada" class="form-check-label">Finalizada</label>
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
        z-index: 9999!important;
    }
</style>


<script>
    $(document).ready(function() {

        $("#home-team").select2({
            theme: 'bootstrap-5',
            templateResult: function (data) {
                if (!data.id) return data.text;
                var img = $(data.element).data('img');
                return $("<span style='display: flex; align-items:center;'><img src='" + img + "' style='width: 24px;'>" + data.text + "</span>");
            },
            templateSelection: function (data) {
                var img = $(data.element).data('img');
                return $("<span style='display: flex; align-items:center;'><img src='" + img + "' style='width: 24px;'>" + data.text + "</span>");
            }
        })
        $("#outer-team").select2({
            theme: 'bootstrap-5',
            templateResult: function (data) {
                if (!data.id) return data.text;
                var img = $(data.element).data('img');
                return $("<span style='display: flex; align-items:center;'><img src='" + img + "' style='width: 24px;'>" + data.text + "</span>");
            },
            templateSelection: function (data) {
                var img = $(data.element).data('img');
                return $("<span style='display: flex; align-items:center;'><img src='" + img + "' style='width: 24px;'>" + data.text + "</span>");
            }
        })
    })
</script>

<script>
    let rodadas = <?php echo json_encode($currLeague); ?>;
    function criarCard(partida, index) {
        if ($('#league-match').val() != 0 && (partida.rodada != $('#league-match').val())) return;
    var card = `
    <form action="/leagues/update" class="col-md-6" method="POST">
    <div class="justify-content-center">
        <div class="card mb-3 p-0 mx-3">
            <div class="bg-primary text-white card-header d-flex justify-content-between align-items-center">
                <p class="mb-0">${partida.data_partida}</p>
                <p class="mb-0">Rodada ${partida.rodada}</p>
                <select class="form-select form-select-sm w-auto" name="finalizada">
                    <option value="0" ${partida.finalizada == 0 ? 'selected' : ''}>Pendente</option>
                    <option value="1" ${partida.finalizada == 1 ? 'selected' : ''}>Finalizada</option>
                </select>
            </div>
            <div class="card-body d-flex justify-content-evenly align-items-center">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <img class="img-fluid me-2" style="width: 32px;" src="${partida.brasao_casa}" alt="">
                    <span class="fw-bold text-center">${partida.time_casa}</span>
                </div>
                <div class="text-center mx-5 d-flex justify-content-center" style="font-size: 1.25rem;">
                    <input type="hidden" name="id" value="${partida.id}" />
                    <input type="text" class="form-control text-center" name="gols_casa" value="${partida.finalizada == 1 ? partida.gols_casa : ''}" style="width: 50px;" />
                    <p class="fw-bold mx-2" style="font-size: 2rem">X</p>
                    <input type="text" class="form-control text-center" name="gols_fora" value="${partida.finalizada == 1 ? partida.gols_fora : ''}" style="width: 50px;" />
                </div>
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <img class="img-fluid me-2" style="width: 32px;" src="${partida.brasao_fora}" alt="">
                    <span class="fw-bold text-center">${partida.time_fora}</span>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end align-items-center">
            <button class='btn btn-success'>Salvar</button>
            </div>
    </div>
        </div>
        </form>
    `;

    $("#cards-container").append(card);
    console.log("Funcionei?")
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
     .done(function (response) {
         showToast('success', 'Rodada atualizada com sucesso!')
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

    $toast.on('hidden.bs.toast', function () {
        $(this).remove()
    })
}
</script>
