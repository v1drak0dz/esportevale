<div class="container-lg bg-light mt-2 py-2">
    <label >Selecione a rodada!</label>
    <select name="leagues" class="form-select mb-3" id="league-match">
        <option value="0">Selecione uma rodada</option>
    </select>
        <h3 class="text-center">Partidas</h3>
        <div style="text-align: center;      font-size: 24px;">
  <span style="letter-spacing: 2px;">───────────────</span>
  <span style="color: black;">●</span>
  <span style="letter-spacing: 2px;">───────────────</span>
</div>
        <div id="cards-container" class="row col-12 col-lg-6 col-md-6 col-sm-12 w-100"></div>
        <!-- Toast container -->
<div id="toast-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

</div>

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
                    <input type="text" class="form-control text-center" name="gols_casa" value="${partida.gols_casa}" style="width: 50px;" />
                    <p class="fw-bold mx-2" style="font-size: 2rem">X</p>
                    <input type="text" class="form-control text-center" name="gols_fora" value="${partida.gols_fora}" style="width: 50px;" />
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
    criarCard(rodada, index);
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
