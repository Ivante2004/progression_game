<?php

namespace Ivante2004\Progression;

use cli;

class View
{
    public function __construct()
    {
        cli\Colors::enable();
    }

    public function startScreen(): void
    {
        cli\line("%G=============================================================%n");
        cli\line("%Y    Добро пожаловать в игру \"Арифметическая прогрессия\"!   %n");
        cli\line("%G=============================================================%n");
        cli\line("%C                       Начинаем...                           %n");
        cli\line("%G=============================================================%n");
    }

    public function showProgression(array $progression, int $hiddenIndex): void
    {
        $displayedProgression = $progression;
        $displayedProgression[$hiddenIndex] = "..";
        cli\line("%C" . implode(" ", $displayedProgression) . "%n");
    }

    public function promptUser(): string
    {
        return cli\prompt("Введите пропущенное число");
    }

    public function showResult(bool $isCorrect, array $progression, int $correctAnswer): void
    {
        if ($isCorrect) {
            cli\line("%GВерно!%n");
        } else {
            cli\line("%RОшибка! Правильный ответ: %Y{$correctAnswer}%n");
            cli\line("%CВся прогрессия: " . implode(" ", $progression) . "%n");
        }
    }
}
