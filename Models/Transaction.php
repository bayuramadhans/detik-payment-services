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
    private $valid_payment_type;
    private $valid_status;
    public  $error_message;

    public function __construct(){
        $db = new Database;
        $this->db = $db->connect();
        $this->table_name = "transactions";
        $this->valid_payment_type = ['virtual_account', 'credit_card'];
        $this->valid_status = ['pending', 'paid', 'failed'];
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
        VALUES (:references_id, :invoice_id, :item_name, :amount, :payment_type, :number_va, :customer_name, :merchant_id, :status);";

        $data = [
            'references_id' =>$this->references_id,
            'invoice_id'    =>$this->invoice_id,
            'item_name'     =>$this->item_name,
            'amount'        =>$this->amount,
            'payment_type'  =>$this->payment_type,
            'number_va'     =>$this->number_va,
            'customer_name' =>$this->customer_name,
            'merchant_id'   =>$this->merchant_id,
            'status'        =>$this->status
        ];

        // eksekusi syntax insert
        try {
            $save = $this->db->prepare($sql);
            if(!$save->execute($data)){
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

        // s: proses check parameter
        $errors = [];
        //cek valid payment type
        if(!empty($_POST['payment_type']) && !in_array($_POST['payment_type'], $this->valid_payment_type)){
            $this->error_message = "payment_type tidak valid";
            return FALSE;
        }

        //cek iterasi setiap parameter yang dibutuhkan
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
        // e: proses check parameter

        // jika ada parameter yang kurang maka return false
        if(!empty($errors)){
            $this->error_message = "Field yang dibutuhkan belum lengkap : " . implode(", ",$errors);
            return FALSE;
        }

        // input field yang tidak ada di parameter
        $this->references_id = 'TRANS-'.generateRandomStringOrNumber(15);
        $this->status = 'pending';

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

    /**
     * Untuk melakukan ambil data status transaksi
     *
     * @param POST data yang dikirim dengan method post
     * @return boolean status dari proses
     */
    public function getStatusTransaksi(){
        // validasi params
        $empty_params = [];
        if(empty($_GET['references_id'])){
            array_push($empty_params, 'references_id');
        }
        if(empty($_GET['merchant_id'])){
            array_push($empty_params, 'merchant_id');
        }
        if(!empty($empty_params)){
            $this->error_message = "Params yang dibutuhkan belum lengkap : " . implode(", ",$empty_params);
            return FALSE;
        }

        $this->references_id = htmlspecialchars(strip_tags($_GET['references_id']));
        $this->merchant_id   = htmlspecialchars(strip_tags($_GET['merchant_id']));

        // persiapan syntax sql get data transaksi
        $sql  = "SELECT invoice_id, status FROM {$this->table_name} WHERE references_id = :references_id AND merchant_id = :merchant_id;";
        $data = [
            'references_id' =>$this->references_id,
            'merchant_id'   =>$this->merchant_id,
        ];

        // eksekusi syntax get data transaksi
        try {
            $get = $this->db->prepare($sql);
            if(!$get->execute($data)){
                $this->error_message = "Get data transaksi gagal :" . $get->errorInfo()[2];
                return FALSE;
            }else{
                $result = $get->fetch();
                if(empty($result)){
                    $this->error_message = "Data tidak ditemukan";
                    return FALSE;
                }else{
                    $this->invoice_id = $result['invoice_id'];
                    $this->status = $result['status'];
                }
                return TRUE;
            }
        
        } catch (PDOException $e) {
            $this->error_message = "Get data transaksi gagal :" . $e->getMessage();
            return FALSE;
        }

    }

    /**
     * Untuk melakukan aksi ganti status
     *
     * @param references_id identifier dari transaksi
     * @param status status baru
     * @return boolean status dari proses
     */
    public function changeStatus($references_id, $status){

        $status = htmlspecialchars(strip_tags(strtolower($status)));
        // validasi status
        if (!in_array($status, $this->valid_status)){
            $this->error_message = "status tidak valid";
            return FALSE;
        }

        $this->references_id = htmlspecialchars(strip_tags($references_id));

        // persiapan syntax sql get data transaksi
        $sql  = "UPDATE {$this->table_name} SET status = :status WHERE references_id = :references_id RETURNING status;";
        $data = [
            'status' => $status,
            'references_id' => $this->references_id
        ];

        // eksekusi syntax get data transaksi
        try {
            $update = $this->db->prepare($sql);
            if(!$update->execute($data)){
                $this->error_message = "Update status data transaksi gagal :" . $get->errorInfo()[2];
                return FALSE;
            }else{
                return TRUE;
            }
        
        } catch (PDOException $e) {
            $this->error_message = "Get data transaksi gagal :" . $e->getMessage();
            return FALSE;
        }

    }
}