<?php
define('PROJECT_ROOT', __DIR__); 

$request    = strtok($_SERVER["REQUEST_URI"], '?');
$method     = $_SERVER["REQUEST_METHOD"];

// deteksi route endpoint
switch ($request) {
    case '/':
        $data = [
            'status'    => true,
            'message'   =>'welcome to api detik payment services'
        ];
        header('Content-type: application/json');
        echo json_encode($data);
        break;
    case '/transaction' :
        require __DIR__ . '/Controllers/TransactionController.php';
        $transaction = new TransactionController;
        switch ($method) {
            // endpoint get status transaksi pembayaran 
            case 'GET' :
                $transaction->read();
                break;
            // endpoint create data transaksi pembayaran
            case 'POST' :
                $transaction->create();
                break;
            default:
                http_response_code(404);
                break;
        }
        break;
    default:
        http_response_code(404);
        break;
}