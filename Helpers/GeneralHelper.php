<?php

    /**
     * Untuk melakukan proses generate random string / number
     *
     * @param {int} length panjang dari random string / number
     * @param {string} type tipe dari generate (string / number)
     * @return string random string/number
     */
    function generateRandomStringOrNumber($length = 10, $type = 'string') {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if($type == 'number'){
            $characters = '0123456789';
        }
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }