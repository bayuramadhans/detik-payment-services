<?php

define('PROJECT_ROOT', __DIR__); 

if (isset($argv[1])) {
    switch ($argv[1]){
        case 'migrate' :
            if (isset($argv[2])){
                switch ($argv[2]) {
                    case 'TransactionTable':
                        require PROJECT_ROOT . '/Databases/TransactionTable.php';
                        $transaction = new TransactionTable;
                        $transaction->migrate();
                        break;
                    default:
                        echo "No Migration Found";
                        break;
                }
            }else{
                echo "Migration Name Required"; 
            }
            break;
        case 'seed' :
            if (isset($argv[2])){
                switch ($argv[2]) {
                    case 'TransactionTable':
                        // set jumlah data seeder
                        $total = 5;
                        if(isset($argv[3]) && is_numeric($argv[3])){
                            $total = $argv[3];
                        }

                        require PROJECT_ROOT . '/Databases/TransactionTable.php';
                        $transaction = new TransactionTable;
                        $transaction->seed($total);
                        break;
                    default:
                        echo "No Seeder Found";
                        break;
                }
            }else{
                echo "Seeder Name Required"; 
            }
            break;
        case 'transactions' :
            if (isset($argv[2])){
                switch ($argv[2]) {
                    case 'change-status':
                        if(empty($argv[3])){
                            echo "Aksi gagal : references_id dibutuhkan";
                            break;
                        }else if(empty($argv[4])) {
                            echo "Aksi gagal : status baru dibutuhkan";
                            break;
                        }
                        require PROJECT_ROOT . '/Models/Transaction.php';
                        $transaction = new Transaction;
                        $update = $transaction->changeStatus($argv[3], $argv[4]);
                        // beri info hasil proses ke cli
                        if ($update){
                            echo "Update status transaksi " . $argv[3] . " menjadi " . $argv[4] . " berhasil";
                        }else{
                            echo "Update status transaksi gagal : " . $transaction->error_message;
                        }
                        break;
                    default:
                        echo "No Command Found";
                        break;
                }
            }else{
                echo "Seeder Name Required"; 
            }
            break;
        default:
            echo "No Command Found";
            break;
    }
}else{
    echo "No Argument Detected";
}