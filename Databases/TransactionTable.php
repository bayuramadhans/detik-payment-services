<?php

include_once PROJECT_ROOT . '/Databases/Database.php';

class TransactionTable {
    private $table_name;
    private $db;

    public function __construct(){
        $db = new Database;
        $this->db = $db->connect();
        $this->table_name = "transactions";
    }

    public function migrate(){
        // membuat sql syntax tabel transactions apabila tidak ditemukan di database
        $sql = "CREATE TABLE IF NOT EXISTS {$this->table_name} (
            id SERIAL PRIMARY KEY,
            references_id VARCHAR(255) UNIQUE NOT NULL,
            invoice_id VARCHAR(255) NOT NULL,
            item_name TEXT NOT NULL,
            amount BIGINT NOT NULL,
            payment_type VARCHAR(50) NOT NULL,
            number_va VARCHAR(50) DEFAULT NULL,
            customer_name VARCHAR(255) NOT NULL,
            merchant_id VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT NULL
        )";

        try {
            $save = $this->db->prepare($sql);
            if(!$save->execute()){
                echo "Migration {$this->table_name} gagal \n";
                echo $save->errorInfo()[2];
            }else{
                echo "Migration {$this->table_name} telah berhasil";
            }
        
        } catch (PDOException $e) {
            echo "Migration gagal :" . $e->getMessage();
        }
    }

    public function seed(){
        // membuat sql syntax seeder tabel transactions
        $time = time();
        $sql = "INSERT INTO {$this->table_name} (references_id, invoice_id, item_name, amount, payment_type, number_va, customer_name, merchant_id, status)
        VALUES ('TRANS-{$time}', 'INV-{$time}', 'Item', '10', 'virtual_account', 'VA{$time}', 'Bayu RS', '{$time}', 'pending');";

        // eksekusi syntax seeder
        try {
            $save = $this->db->prepare($sql);
            if(!$save->execute()){
                echo "Seeder {$this->table_name} gagal \n";
                echo $save->errorInfo()[2];
            }else{
                echo "Seeder {$this->table_name} telah berhasil";
            }
        
        } catch (PDOException $e) {
            echo "Seeder gagal :" . $e->getMessage();
        }
    }
} 