<?php
require_once PROJECT_ROOT . '/Databases/Database.php';
require_once PROJECT_ROOT . '/Helpers/GeneralHelper.php';

class Transaction {
    public $id;
    public $references_id;
    public $invoice_id ;
    public $item_name;
    public $amount;
    public $payment_type;
    public $number_va;
    public $customer_name;
    public $merchant_id;
    public $status;

    private $table_name;
    private $db;
    public  $error_message;

    public function __construct(){
        $db = new Database;
        $this->db = $db->connect();
        $this->table_name = "transactions";
    }

    /**
     * Untuk melakukan proses create data transaksi
     *
     * @param POST data yang dikirim dengan method post
     * @return boolean status dari proses
     */
    public function create(){
        // validasi input dan persiapan eksekusi simpan database
        $process = $this->validateAndPrepareInput();

        // jika proses gagal maka return
        if (!$process) {
            return FALSE;
        }

        // persiapan syntax sql insert
        $sql = "INSERT INTO {$this->table_name} (references_id, invoice_id, item_name, amount, payment_type, number_va, customer_name, merchant_id, status)
        VALUES ('{$this->references_id}', '{$this->invoice_id}', '{$this->item_name}', '{$this->amount}', '{$this->payment_type}', '{$this->number_va}', '{$this->customer_name}', '{$this->merchant_id}', '{$this->status}');";

        // eksekusi syntax insert
        try {
            $save = $this->db->prepare($sql);
            if(!$save->execute()){
                $this->error_message = "Create data transaksi gagal :" . $save->errorInfo()[2];
                return FALSE;
            }else{
                return TRUE;
            }
        
        } catch (PDOException $e) {
            $this->error_message = "Create data transaksi gagal :" . $e->getMessage();
            return FALSE;
        }

    }

    /**
     * Untuk melakukan proses validasi dan persiapan sebelum create data transaksi
     *
     * @param POST data yang dikirim dengan method post
     * @return boolean status dari proses
     */
    private function validateAndPrepareInput(){
        // parameter yang dibutuhkan
        $required_params = [
            'invoice_id',
            'item_name',
            'amount',
            'payment_type',
            'customer_name',
            'merchant_id'
        ];

        // proses check parameter
        $errors = [];
        foreach ($required_params as $param){
            if (empty($_POST[$param])) {
                 array_push($errors, $param);
            } else {
                 // proses input parameter ke field model
                $this->{$param} = htmlspecialchars(strip_tags($_POST[$param]));
                // proses generate number_va jika payment type = virtual_account
                if($param == 'payment_type' &&  $_POST[$param] == 'virtual_account'){
                    $this->number_va = $this->generateNumberVirtualAccount();
                }
            }
        }

        // jika ada parameter yang kurang maka return false
        if(!empty($errors)){
            $this->error_message = "Field yang dibutuhkan belum lengkap : " . implode(", ",$errors);
            return FALSE;
        }

        // input field yang tidak ada di parameter
        $this->references_id = 'TRANS-'.generateRandomStringOrNumber(15);
        $this->status = 'Pending';

        // return true
        return TRUE;
    }

    /**
     * Untuk melakukan generate random virtual account
     *
     * @return string random number virtual account
     */
    private function generateNumberVirtualAccount(){
        return 'VA'.generateRandomStringOrNumber(10, 'number');
    }
}