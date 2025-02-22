<?php

namespace Ivante2004\Progression;

use Ivante2004\Progression\View;

class Controller
{
    public function __construct(public View $view, private Database $db)
    {
    }

    public function showStartScreen(): void
    {
        $this->view->renderStartScreen();
    }

    public function startGame(): void
    {
        $this->playRound($_SESSION['player_name']);
    }

    public function playRound(string $playerName): void
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

        $_SESSION['progression'] = $progression;
        $_SESSION['hiddenIndex'] = $hiddenIndex;
        $_SESSION['correctAnswer'] = $correctAnswer;

        $this->view->renderGameScreen($progression, $hiddenIndex);
    }

    public function checkAnswer(string $playerName, int $userAnswer): void
    {
        $progression = $_SESSION['progression'];
        $correctAnswer = $_SESSION['correctAnswer'];

        $isCorrect = ($userAnswer == $correctAnswer);
        $result = $isCorrect ? 'win' : 'lose';

        $this->db->saveGame($playerName, $result, $progression, $correctAnswer);

        $this->view->renderResult($isCorrect, $progression, $correctAnswer);
    }

    public function showGameHistory(): void
    {
        $games = $this->db->getGames();
        $this->view->renderGameHistory($games);
    }
}