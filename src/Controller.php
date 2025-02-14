<?php

namespace Ivante2004\Progression;

use Ivante2004\Progression\View;

class Controller
{
    public function __construct(public View $view)
    {
    }

    public function startGame(): void
    {
        $this->view->startScreen();
        $this->playRound();
    }

    private function playRound(): void
    {
        $start = rand(1, 50);
        $step = rand(2, 10);
        $length = 10;

        $progression = [];
        for ($i = 0; $i < $length; $i++) {
            $progression[] = $start + $i * $step;
        }

        $hiddenIndex = rand(0, $length - 1);
        $correctAnswer = $progression[$hiddenIndex];

        $this->view->showProgression($progression, $hiddenIndex);

        $userAnswer = $this->view->promptUser();
        $isCorrect = ($userAnswer == $correctAnswer);

        $this->view->showResult($isCorrect, $progression, $correctAnswer);
    }
}
