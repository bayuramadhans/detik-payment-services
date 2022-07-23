<?php

include_once PROJECT_ROOT . '/Databases/Database.php';

class Migration {

    public $db;

    public function __construct(){
        $db = new Database;
        $this->db = $db->connect();
    }
}