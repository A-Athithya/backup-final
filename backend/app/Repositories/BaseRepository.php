<?php
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Config/database.php';

class BaseRepository {
    protected $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function getDb() {
        return $this->db;
    }
}
