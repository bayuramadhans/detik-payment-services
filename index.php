<?php

$request    = $_SERVER['REQUEST_URI'];

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
    default:
        http_response_code(404);
        break;
}