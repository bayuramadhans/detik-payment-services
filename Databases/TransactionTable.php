<?php

require_once PROJECT_ROOT . '/Databases/Database.php';
require_once PROJECT_ROOT . '/Helpers/GeneralHelper.php';

class TransactionTable {
    private $table_name;
    private $valid_payment_type;
    private $valid_status;
    private $db;

    public function __construct(){
        $db = new Database;
        $this->db = $db->connect();
        $this->table_name = "transactions";
        $this->valid_payment_type = ['virtual_account', 'credit_card'];
        $this->valid_status = ['pending', 'paid', 'failed'];
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

    public function seed($total = 5){
        // membuat sql syntax seeder tabel transactions
        for($i=0; $i<$total; $i++){
            $sql = "INSERT INTO {$this->table_name} (references_id, invoice_id, item_name, amount, payment_type, number_va, customer_name, merchant_id, status)
            VALUES (:references_id, :invoice_id, :item_name, :amount, :payment_type, :number_va, :customer_name, :merchant_id, :status);";
            
            $data = [
                'references_id' =>'TRANS-' . generateRandomStringOrNumber(15),
                'invoice_id'    =>'INV-' . generateRandomStringOrNumber(5),
                'item_name'     =>'ITEM-' . time() . '-' . generateRandomStringOrNumber(10),
                'amount'        =>generateRandomStringOrNumber(2, 'number'),
                'payment_type'  =>$this->valid_payment_type[array_rand($this->valid_payment_type)],
                'number_va'     => null,
                'customer_name' =>'CUST-' . time() . '-' . generateRandomStringOrNumber(10),
                'merchant_id'   =>generateRandomStringOrNumber(5, 'number'),
                'status'        =>$this->valid_status[array_rand($this->valid_status)]
            ];

            if($data['payment_type'] == 'virtual_account'){
                $data['number_va'] = 'VA'.generateRandomStringOrNumber(10, 'number');
            }

            // eksekusi syntax seeder
            try {
                $save = $this->db->prepare($sql);
                if(!$save->execute($data)){
                    echo "Seeder {$this->table_name} gagal \n";
                    echo $save->errorInfo()[2];
                }else{
                    echo "Seeder berhasil : reference_id = " . $data['references_id'] . " merchant_id = " . $data['merchant_id'] ."\n";
                }
            
            } catch (PDOException $e) {
                echo "Seeder gagal :" . $e->getMessage();
            }
        }
    }
} 