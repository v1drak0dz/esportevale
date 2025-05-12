<?php
// LeagueController.php

class LeagueController
{
    private $league;

    public function __construct()
    {
        $this->league = new League();
    }

    public function atualizar()
    {
        if (!isset($_GET['id'])) {
            header('Location: /unauthorized');
            exit();
        }

        $league_found = $this->league->getLeagueById($_GET['id']);

        if (!$league_found) {
            header('Location: /404');
            exit();
        }

        $url = $league_found->url;
        $nome = $league_found->nome;

        echo htmlspecialchars($url);

        $html = $this->fetch_page($url);
        $tabela_html = $this->extrair_tabela($html);
        $tabela_processada = $this->processar_celulas_time($tabela_html);

        $rodada = $this->extrair_rodada($html);
        $rodada_html = '<h3>' . htmlspecialchars($rodada['titulo']) . '</h3>' . $rodada['html'];

        // Salva dados completos no banco
        $this->league->salvarLiga($nome, $url, $tabela_processada, $rodada_html);

        echo "Liga '" . htmlspecialchars($nome) . "' atualizada com sucesso.";
    }
}
