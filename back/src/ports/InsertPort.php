<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/InsertSql.php";
    require_once __DIR__."/../security/CheckValidityCode.php";
    require_once __DIR__."/../security/CheckNameAvaliable.php";
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44'])) {
            if ($_SERVER['HTTP_FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44'] == 'Falw1qKPKZYufBz0r2S1avMZ16BeNHPn3/nqJzg2IyDHF+XtM4x9cBMTOvG++LTO3wCbTEJXEocIO+xfjPCEunNGKu8DvjQzXG29DSSiuQsPnwVV+/cHwnNh6MFLg3KvNC4k3v9uhXZkRMBaRIglt2FnKt3gLssn'){
                $json = trim(file_get_contents('php://input'));
                $data = json_decode($json,true);
                $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
                $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumbersamountvalue = "/^[1-9]{1}[0-9]{0,}$/";
                if ( !empty($data['type']) ) {
                    if( ($data['type'] == "categories") && ( !empty($data['name']) ) && ( !empty($data['tax']) ) ){
                        $name = html_entity_decode($data['name']);
                        $tax = html_entity_decode($data['tax']);
                        if ( (preg_match($regexname, $name)) && (preg_match($regexnumberstax, $tax)) && (CheckNameAvaliable($name,"categories")) ) {
                            echo( json_encode ( InsertSql( 'categories', ['name','tax'], [$data['name'],$data['tax']] ) ) );
                        } else {
                            echo(json_encode(false));
                        }
                    } else if( ($data['type'] == "products") && ( !empty($data['name']) ) && ( !empty($data['amount']) ) && ( !empty($data['price']) ) && ( !empty($data['category']) ) ){
                        $name = html_entity_decode($data['name']);
                        $price = html_entity_decode($data['price']);
                        $amount = html_entity_decode($data['amount']);
                        $category = $data['category'];
                        if ( (preg_match($regexname, $name)) && (preg_match($regexnumbersprice, $price)) && (preg_match($regexnumbersamountvalue, $amount)) && (preg_match($regexnumbersamountvalue, $category)) && (CheckNameAvaliable($name,"products")) ) {
                            if (CheckValidityCode($category,"categories")) {
                                echo( json_encode ( InsertSql( 'products', ['name','amount','price','category_code'], [$data['name'],$data['amount'],$data['price'],$data['category']] ) ) );
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