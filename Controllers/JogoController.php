<?php

class JogoController extends BaseController
{
  public function lista()
  {
    if (isset($_GET['campeonato']))
    {
      $query = "
      select p.id, p.grupo, p.rodada, p.data_partida,
             l.nome as campeonato, tcasa.id as time_casa_id, tcasa.nome as time_casa_nome, tcasa.brasao as time_casa_brasao, tfora.id as time_fora_id, tfora.nome as time_fora_nome,
             tfora.brasao as time_fora_brasao, p.gols_casa, p.gols_fora, p.finalizada
        from partidas p
        join ligas l on p.campeonato_id = l.id
        join times tcasa on p.time_casa_id = tcasa.id
        join times tfora on p.time_fora_id = tfora.id
       where p.campeonato_id = " . $_GET['campeonato'] . "
       order by p.data_partida desc";
      $jogos = $this->mysqli->query($query)->fetch_all(MYSQLI_ASSOC);
      if (empty($jogos))
      {
        $jogos = array();
      } else
      {
        $times = array();
        foreach($jogos as $jogo)
        {
          if (!in_array($jogo['time_casa_nome'], $times))
          {
            $times[] = $jogo['time_casa_nome'];
          }
          if (!in_array($jogo['time_fora_nome'], $times))
          {
            $times[] = $jogo['time_fora_nome'];
          }
        }

        $times_select = array();
        foreach ($times as $nome)
        {
          $times_select[] = array('id' => $nome, 'text' => $nome);
        }
      }
    }
    include_once('components/header.php');
    include_once('views/jogos.php');
    include_once('components/footer.php');
  }

  public function criar()
  {

  }
}