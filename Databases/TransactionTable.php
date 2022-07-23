<?php

require PROJECT_ROOT . '/Databases/Migration.php';

class TransactionTable extends Migration {
    private $table_name = "transactions";

    public function migrate() {
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
            updated_at TIMESTAMP
        )";

        try {
            $this->db->exec($sql);
            echo "Migration {$this->table_name} telah berhasil";
        
        } catch (PDOException $e) {
            echo "Migration gagal :" . $e->getMessage();
        }
    }
} 