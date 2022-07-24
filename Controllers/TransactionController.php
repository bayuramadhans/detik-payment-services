<?php

    require_once PROJECT_ROOT . '/Models/Transaction.php';

    class TransactionController{

        public function __construct(){
            header('Content-type: application/json');
        }

        /**
         * Controller untuk proses create data transaksi
         *
         * @param post data yang dibutuhkan dari proses create dengan method post
         * @return json status dan data / message dari proses create
         */
        public function create(){
            $transaction = new Transaction;
            $create = $transaction->create();

            $response = [];
            if (!$create){
                // response jika proses gagal
                $response = [
                    'status'  => FALSE,
                    'message' => $transaction->error_message
                ];
            } else {
                // response jika proses berhasil
                $response = [
                    'status'  => TRUE,
                    'data'    => [
                        'references_id' => $transaction->references_id,
                        'number_va'     => $transaction->number_va,
                        'status'        => $transaction->status
                    ]
                ];
            }
            echo json_encode($response);
        }
    }