<?php

namespace Ivante2004\Progression;

class View
{
    public function renderStartScreen(): void
    {
        echo '
            <h1 class="title">Добро пожаловать в игру "Арифметическая прогрессия"!</h1>
            <form method="POST" class="form">
                <label for="player_name" class="label">Введите ваше имя:</label>
                <input type="text" id="player_name" name="player_name" class="input" required>
                <button type="submit" class="button">Начать игру</button>
            </form>
             <a href="/index.php?action=history"  class="button"><button>Просмотреть историю игр</button></a>
        ';
    }

    public function renderGameScreen(array $progression, int $hiddenIndex): void
    {
        $displayedProgression = $progression;
        $displayedProgression[$hiddenIndex] = "..";
        $progressionString = implode(" ", $displayedProgression);

        echo '
            <h1 class="title">Арифметическая прогрессия</h1>
            <p class="progression">Прогрессия: ' . $progressionString . '</p>
            <form method="POST" class="form">
                <label for="user_answer" class="label">Введите пропущенное число:</label>
                <input type="number" id="user_answer" name="user_answer" class="input" required>
                <button type="submit" class="button">Проверить</button>
            </form>
        ';
    }

    public function renderResult(bool $isCorrect, array $progression, int $correctAnswer): void
    {
        $progressionString = implode(" ", $progression);

        echo '
            <h1 class="title">Результат</h1>';
        if ($isCorrect) {
            echo '<p class="result success">Верно! Правильный ответ: ' . $correctAnswer . '</p>';
        } else {
            echo '<p class="result error">Ошибка! Правильный ответ: ' . $correctAnswer . '</p>';
        }
        echo '
            <p class="progression">Вся прогрессия: ' . $progressionString . '</p>
            <form method="POST" action="/" class="form">
                <button type="submit" class="button">Следующий раунд</button>
            </form>
        ';
    }

    public function renderGameHistory(array $games): void
    {
        echo '
            <h1 class="title">История игр</h1>
            <table class="table">
                <tr>
                    <th>Игрок</th>
                    <th>Дата</th>
                    <th>Результат</th>
                    <th>Прогрессия</th>
                    <th>Пропущенное число</th>
                </tr>';
        foreach ($games as $game) {
            echo '
                <tr>
                    <td>' . htmlspecialchars($game['player_name']) . '</td>
                    <td>' . htmlspecialchars($game['game_date']) . '</td>
                    <td>' . htmlspecialchars($game['result']) . '</td>
                    <td>' . htmlspecialchars($game['progression']) . '</td>
                    <td>' . htmlspecialchars($game['missed_number']) . '</td>
                </tr>';
        }
        echo '
            </table>
            <a href="/" class="button">Начать новую игру</a>
        ';
    }
}