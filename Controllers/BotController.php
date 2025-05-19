<?php

class BotController extends BaseController {
    public function saveLeague()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(array('success' => false, 'message' => 'error!'));
            exit;
        }

        $nome = isset($_POST['name']) ? $_POST['name'] : null;
        $tabela = isset($_POST['tabela']) ? $_POST['tabela'] : null;
        $rodada = isset($_POST['rodada']) ? $_POST['rodada'] : null;

        if (!$nome || !$tabela || !$rodada) {
            echo json_encode(array('success' => false, 'message' => 'Campos obrigatórios não informados.'));
            exit;
        }

        $response = $this->league->atualizarLiga($nome, $tabela, $rodada);
        if ($response === true) {
            echo json_encode(array('success' => true, 'message' => 'Liga ' . $nome . ' atualizada com sucesso!'));
            exit;
        } else {
            echo json_encode(array('success' => false, 'message' => 'Erro ao atualizar liga ' . $nome . '!\n' . $response));
            exit;
        }
    }
}