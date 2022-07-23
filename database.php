<?php
    class Database {

        // pengaturan database (sesuaikan dengan environment masing-masing)
        private $host = "127.0.0.1";
        private $db_type = "pgsql";
        private $db_port = "5432";
        private $db_name = "detik_payment";
        private $username = "postgres";
        private $password = "example_pass";

        public $conn;

        public function connect(){
            $this->conn = null;
            try{
                $this->conn = new PDO($this->db_type . ":host=" . $this->host . ";port=" . $this->db_port . ";dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->exec("set names utf8");
            }catch(PDOException $exception){
                $data = [
                    'status'    => false, 
                    'message'   => "Database gagal terkoneksi: " . $exception->getMessage()
                ];
                echo json_encode($data);
            }
            return $this->conn;
        }
    } 