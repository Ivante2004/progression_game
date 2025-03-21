<?php

namespace Ivante2004\Progression;

use Ivante2004\Progression\Database;

class Controller
{
    public function __construct(private Database $db)
    {
    }


    public function playRound(string $playerName): array
    {
        $start = rand(1, 50);
        $step = rand(2, 10);
        $length = 10;

        $progression = [];
        for ($i = 0; $i < $length; $i++) :
            $progression[] = $start + $i * $step;
        endfor;

        $hiddenIndex = rand(0, $length - 1);
        $correctAnswer = $progression[$hiddenIndex];

        $gameId =$this->db->createGame($playerName, $progression, $correctAnswer);

        $progressionByPlayer = $progression;
        $progressionByPlayer[$hiddenIndex] = '...';
        return ['progression' => $progressionByPlayer, 'game_id' => $gameId];
    }

    public function checkAnswer(int $gameId, int $answered): array
    {
        $status = $this->db->checkWin($gameId, $answered);
        $status = $status ? 'win' : 'lose';
        return ['status' => $status];
    }

    public function showGameHistory(?int $gameId = null): array
    {
        if ($gameId !== null) {
            $result = $this->db->getGame($gameId);

            return ['game' => $result];
        }

        $games = $this->db->getGames();
        return ['games' => $games];
    }

}
