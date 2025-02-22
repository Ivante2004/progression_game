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
                result TEXT NOT NULL,
                progression TEXT NOT NULL,
                missed_number INTEGER NOT NULL
            )
        ");
    }

    public function saveGame(string $playerName, string $result, array $progression, int $missedNumber): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO games (player_name, game_date, result, progression, missed_number)
            VALUES (:player_name, datetime('now'), :result, :progression, :missed_number)
        ");
        $stmt->bindValue(':player_name', $playerName, SQLITE3_TEXT);
        $stmt->bindValue(':result', $result, SQLITE3_TEXT);
        $stmt->bindValue(':progression', implode(',', $progression), SQLITE3_TEXT);
        $stmt->bindValue(':missed_number', $missedNumber, SQLITE3_INTEGER);
        $stmt->execute();
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
}
