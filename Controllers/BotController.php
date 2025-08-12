<?php

class BotController extends BaseController {
	public function save()
	{
		if (!isset($_POST['data'])) {
			error_log("Nenhum dado recebido.");
			return;
		}

		$payload = json_decode($_POST['data'], true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			error_log("Erro ao decodificar JSON: " . json_last_error_msg());
			return;
		}

		foreach ($payload as $data) {
			$this->league->save(
				$data['gcasa'],
				$data['gfora'],
				$data['finalizada'],
				$data['league'],
				$data['data'],
				$data['rodada'],
				$data['tcasa'],
				$data['tfora']
			);
		}
	}
	
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
        $response = $this->league->atualizarTimes($name, $brasao, $league);
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
        // Atualiza os times
        error_log(print_r($data));
        $this->league->atualizarTimes($data["homeTeam"], $data["shieldHome"], $data["league"]);
        $this->league->atualizarTimes($data["outerTeam"], $data['shieldOuter'], $data["league"]);

        // Atualiza as partidas
        $this->league->saveMatch(
            $data["league"], $data["group"], $data["match"],
            $data["date"], $data["outerTeam"], $data["homeTeam"],
            $data["goalsHome"], $data["goalsOuter"], $data["ended"]
        );

        echo json_encode(array('success' => true, 'message' => 'Partidas atualizadas com sucesso!'));
        exit;
    }

    private function saveShield($content) {
        $uploadDir = dirname(__DIR__) . '/../public/img/shields/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = uniqid('img_', true) . '.webp';
        $filepath = $uploadDir . $filename;
        if (file_put_contents($filepath, base64_decode($content))) {
            $publicPath = '/img/shields/' . $filename;
            return $publicPath;
        } else {
            return false;
        }
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