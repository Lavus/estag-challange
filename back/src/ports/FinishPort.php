<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/FinishSql.php";
    require_once __DIR__."/../security/CheckValidityCode.php";
    require_once __DIR__."/../security/CheckNameAvaliable.php";
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_U6LFWUJVWRYZUBEMSZGTTCNZW6CSHKXN2M6S9SPC8GMFE3F4QYZGVRKQ9HVVFAELXP8NH2OYJI7WAZ6EE1PHZXTKFDEACSU2BYPZEEJLD2NGFLK2JQH0OJKJ50GAUBBF0T1ASNOQIDH64MLDXBQLHW57ORHKV7GBC2FDVLVINYKOXRY'])) {
            if ($_SERVER['HTTP_U6LFWUJVWRYZUBEMSZGTTCNZW6CSHKXN2M6S9SPC8GMFE3F4QYZGVRKQ9HVVFAELXP8NH2OYJI7WAZ6EE1PHZXTKFDEACSU2BYPZEEJLD2NGFLK2JQH0OJKJ50GAUBBF0T1ASNOQIDH64MLDXBQLHW57ORHKV7GBC2FDVLVINYKOXRY'] == 'XGp0h9o3wh+tsdH5k8pI0qC0lCECxE9vkSlMclBf6JaEFypK1jd4TbdodmSKQmS6h7nP2WLG8QNIPd1Ul3sFSSzefWmogWmXy4revR/lnzdACQ1wByBmbco1tZ6vWonxazTgF5M+Xcw3smT4nsEwUX7Ddzeqj2dcgONIUZHtn46fj3M61CaRRHneznDuq+GMZ5Qk6Xv6Nu2qbKFABca8WydA2Wi+'){
                $json = trim(file_get_contents('php://input'));
                $data = json_decode($json,true);
                if ( ( !empty($data['value_total']) ) && ( !empty($data['value_tax']) ) ) {
                    echo( json_encode ( FinishSql( strval($data['value_total']), strval($data['value_tax']) ) ) );
                } else {
                    echo(json_encode(false));
                }
            } else {
                echo(json_encode(false));
            }
        } else {
            echo(json_encode(false));
        }
    } else {
        echo(json_encode(false));
    }
?>