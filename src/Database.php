<?php

namespace Ivante2004\Progression;

use SQLite3;

class Database
{
    private SQLite3 $db;

    public function __construct(string $dbPath)
    {
        $this->db = new SQLite3($dbPath);
        $this->initializeDatabase();
    }

    private function initializeDatabase(): void
    {
        $this->db->exec("
        CREATE TABLE IF NOT EXISTS games (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            player_name TEXT NOT NULL,
            game_date DATETIME NOT NULL,
            result TEXT,
            progression TEXT NOT NULL,
            missed_number INTEGER NOT NULL,
            player_answer INTEGER
        )
    ");
    }


    public function createGame(string $playerName,array $progression, int $missedNumber): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO games (player_name, game_date, result, progression, missed_number)
            VALUES (:player_name, datetime('now'), :result, :progression, :missed_number)
        ");
        $stmt->bindValue(':player_name', $playerName, SQLITE3_TEXT);
        $stmt->bindValue(':progression', implode(',', $progression), SQLITE3_TEXT);
        $stmt->bindValue(':missed_number', $missedNumber, SQLITE3_INTEGER);
        $stmt->execute();

        return $this->db->lastInsertRowID();
    }

    public function getGames(): array
    {
        $result = $this->db->query("SELECT * FROM games ORDER BY game_date DESC");
        $games = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $games[] = $row;
        }
        return $games;
    }

    public function checkWin(int $gameId, int $answered): bool
    {
        $updateStmt = $this->db->prepare("UPDATE games SET player_answer = :player_answer WHERE id = :game_id");
        $updateStmt->bindValue(':player_answer', $answered, SQLITE3_INTEGER);
        $updateStmt->bindValue(':game_id', $gameId, SQLITE3_INTEGER);
        $updateStmt->execute();


        $stmt = $this->db->prepare("SELECT missed_number FROM games WHERE id = :game_id");
        $stmt->bindValue(':game_id', $gameId, SQLITE3_INTEGER);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($result) {
            $correctAnswer = $result['missed_number'];
            $gameResult = ($answered === $correctAnswer) ? 'win' : 'lose';


            $updateStmt = $this->db->prepare("UPDATE games SET result = :result WHERE id = :game_id");
            $updateStmt->bindValue(':result', $gameResult, SQLITE3_TEXT);
            $updateStmt->bindValue(':game_id', $gameId, SQLITE3_INTEGER);
            $updateStmt->execute();

            return $answered === $correctAnswer;
        }

        return false;
    }

    public function getGame(?int $gameId = null)
    {
        $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :game_id");
        $stmt->bindValue(':game_id', $gameId, SQLITE3_INTEGER);
        $result = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        return $result;
    }

}
