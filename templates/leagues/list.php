<div class="container bg-white my-3">
    <h2>Editar Partidas</h2>
    <form action="/leagues/list" method="POST">
        <table id="matchesTable" class="table table-bordered table-hover align-middle text-center" style="overflow-x: scroll;">
            <thead class="table-light">
                <tr>
                    <th>Rodada</th>
                    <th>Data</th>
                    <th>Time Casa</th>
                    <th>Gols Casa</th>
                    <th>Gols Fora</th>
                    <th>Time Fora</th>
                    <th>Finalizada</th>
                </tr>
                <tr>
                    <th><input type="text" class="form-control form-control-sm" placeholder="Filtrar..." /></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="Filtrar..." /></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="Filtrar..." /></th>
                    <th></th>
                    <th></th>
                    <th><input type="text" class="form-control form-control-sm" placeholder="Filtrar..." /></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($currLeague as $match): ?>
                    <tr>
                        <td><?= $match['rodada'] ?></td>
                        <td><?= $match['data_partida'] ?></td>
                        <td class="text-start"><?= $match['time_casa'] ?></td>
                        <td><input type="number" class="form-control text-center" name="match[<?= $match['id'] ?>][gols_casa]" value="<?= $match['finalizada'] ? $match['gols_casa'] : '' ?>" min="0" /></td>
                        <td><input type="number" class="form-control text-center" name="match[<?= $match['id'] ?>][gols_fora]" value="<?= $match['finalizada'] ? $match['gols_fora'] : '' ?>" min="0" /></td>
                        <td class="text-start"><?= $match['time_fora'] ?></td>
                        <td>
                            <input type="hidden" name="match[<?= $match['id'] ?>][id]" value="<?= $match['id'] ?>" />
                            <div class="form-check d-flex justify-content-center">
                                <input class="form-check-input" type="checkbox" name="match[<?= $match['id'] ?>][finalizada]" value="1" <?= $match['finalizada'] ? 'checked' : '' ?> />
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="d-flex justify-content-end my-3">
            <button type="submit" class="btn btn-primary">Salvar Partidas</button>
        </div>
    </form>

    <script>
        $(document).ready(function () {
            let table = $('#matchesTable').DataTable({
                orderCellsTop: true,
                fixedHeader: true,
                language: {
                    "decimal":        "",
                    "emptyTable":     "Nenhum dado disponível na tabela",
                    "info":           "Mostrando _START_ a _END_ de _TOTAL_ registros",
                    "infoEmpty":      "Mostrando 0 a 0 de 0 registros",
                    "infoFiltered":   "(filtrado de _MAX_ registros no total)",
                    "lengthMenu":     "Mostrar _MENU_ registros",
                    "loadingRecords": "Carregando...",
                    "processing":     "Processando...",
                    "search":         "Pesquisar:",
                    "zeroRecords":    "Nenhum registro encontrado",
                    "paginate": {
                        "first":      "Primeiro",
                        "last":       "Último",
                        "next":       "Próximo",
                        "previous":   "Anterior"
                    },
                    "aria": {
                        "sortAscending":  ": ativar para ordenar a coluna em ordem crescente",
                        "sortDescending": ": ativar para ordenar a coluna em ordem decrescente"
                    }
                }
            });

            $('#matchesTable thead tr:eq(1) th').each(function (i) {
                $('input', this).on('keyup change', function () {
                    if (table.column(i).search() !== this.value) {
                        table.column(i).search(this.value).draw();
                    }
                });
            });
        });
    </script>
</div>
