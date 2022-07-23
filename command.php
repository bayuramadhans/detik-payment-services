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
        default:
            echo "No Command Found";
            break;
    }
}else{
    echo "No Argument Detected";
}