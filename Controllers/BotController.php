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

    public function saveTeams()
    {
        // $this->validate();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(array('success' => false, 'message' => 'error!'));
            exit;
        }

        $name = isset($_POST['name']) ? $_POST['name'] : null;
        $brasao = isset($_POST['brasao']) ? $_POST['brasao'] : null;
        $league = isset($_POST['campeonato']) ? $_POST['campeonato'] : null;
        error_log("nome: " . $name . "\nbrasao: " . $brasao . "\ncampeonato: " . $league);
        $response = $this->league->atualizarTimes($name, $brasao, $league);
        error_log("response: " . $response);
        if ($response === true) {
            echo json_encode(array('success' => true, 'message' => 'Times atualizados com sucesso!'));
            exit;
        } else {
            echo json_encode(array('success' => false, 'message' => 'Erro ao atualizar times!\n' . $response));
            exit;
        }
    }

    public function saveMatch()
    {
        // $this->validate();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(array('success' => false, 'message' => 'error!'));
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        $league = $data["league"];
        $group = $data["group"];
        $match = $data["match"];
        $date = $data["date"];
        $outerTeam = $data["outerTeam"];
        $homeTeam = $data["homeTeam"];
        $goalsOuter = $data["goalsOuter"];
        $goalsHome = $data["goalsHome"];
        $ended = $data["ended"];
        // $league = $this->receivePostKey("league");
        // $group = $this->receivePostKey("group");
        // $match = $this->receivePostKey("match");
        // $date = $this->receivePostKey("date");
        // $outerTeam = $this->receivePostKey("outerTeam");
        // $homeTeam = $this->receivePostKey("homeTeam");
        // $goalsOuter = $this->receivePostKey("goalsOuter");
        // $goalsHome = $this->receivePostKey("goalsHome");
        // $ended = $this->receivePostKey("ended");

        $this->league->saveMatch(
            $league, $group, $match,
            $date, $outerTeam, $homeTeam,
            $goalsHome, $goalsOuter, $ended
        );
    }

    private function validate()
    {
        $reqValidation = $this->validateRequest();
        if (!$reqValidation['success'])
        {
            echo json_encode($reqValidation);
            exit;
        }
    }

    private function validateRequest()
    {
        header('Content-Type: application/json');

        $headers = array();
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            return array('success' => false, 'message' => 'Token nao fornecido');
        }

        $authHeader = $headers['Authorization'];
        $parts = explode(' ', $authHeader);
        if (count($parts) != 2 || $parts[0] !== 'Bearer') {
            return array('success' => false, 'message' => 'Token invalido');
        }

        $token = $parts[1];
        $userId = $this->users->validateToken($token);
        if (!$userId) {
            return array('success' => false, 'message' => 'Usuário nao autorizado');
            exit;
        }

        return array('success' => true);
    }
}