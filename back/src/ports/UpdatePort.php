<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/UpdateSql.php";
    require_once __DIR__."/../security/CheckValidityCode.php";
    require_once __DIR__."/../security/CheckNameAvaliable.php";
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_JPIZGRPSNRMFNYUWVPZ7RKFWLNMVUCNXSGO3FEQEVAOQUJRHEAONY4FGWEICD9KARVOKHHZYOC3PAQNZNRN6LDSMGNRMDNCAR0PPOPG6CCJ2UVRUBAQ'])) {            
            if ($_SERVER['HTTP_JPIZGRPSNRMFNYUWVPZ7RKFWLNMVUCNXSGO3FEQEVAOQUJRHEAONY4FGWEICD9KARVOKHHZYOC3PAQNZNRN6LDSMGNRMDNCAR0PPOPG6CCJ2UVRUBAQ'] == 'SlCo/rpvAFCWsfljh2VGhCCrt4CnBCuoZf5gobtIh7KFLH1Z+ZteqDc+ARImfH9M9B1cdlMje7UkqUXjpIKhazGkKyBD3Xebzr1yLsk4O6RGK0CRDMWgz9dmhZ77tNlr2oiwAyXVb8PX4EV+vi/VSD1Vj8SgE6I='){
                $json = trim(file_get_contents('php://input'));
                $data = json_decode($json,true);
                $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
                $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumbersamount = "/^[0-9]{0,}$/";
                $regexnumberscode = "/^[1-9]{1}[0-9]{0,}$/";
                if ( ( !empty($data['type']) ) && ( !empty($data['id']) ) ) {
                    if (!(preg_match($regexnumberscode, $data['id']))){
                        echo(json_encode(false));
                    } else if ( CheckValidityCode($data['id'],$data['type']) ){
                        if( ($data['type'] == "categories") && ( !empty($data['name']) ) && ( !empty($data['tax']) ) && ( !empty($data['oldName']) ) && ( !empty($data['oldTax']) ) ){
                            $name = html_entity_decode($data['name']);
                            $tax = html_entity_decode($data['tax']);
                            if ( (preg_match($regexname, $name)) && (preg_match($regexnumberstax, $tax)) && (CheckNameAvaliable($name,"categories",$data['id'])) ) {
                                echo( json_encode ( UpdateSql( ['categories'], [['code'],['name'],['tax']], ['code','name','tax'], [$data['name'],$data['tax']], [$data['oldName'],$data['oldTax']], $data['id'] )[0] ) );
                            } else {
                                echo(json_encode(false));
                            }
                        } else if( ($data['type'] == "products") && ( !empty($data['name']) ) && ( !empty($data['oldName']) ) && ( !empty($data['amount']) ) && ( !empty($data['oldAmount']) ) && ( !empty($data['price']) ) && ( !empty($data['oldPrice']) ) && ( !empty($data['category']) ) && ( !empty($data['oldCategory']) ) ){
                            $name = html_entity_decode($data['name']);
                            $amount = html_entity_decode($data['amount']);
                            $price = html_entity_decode($data['price']);
                            $category = $data['category'];
                            if ( (preg_match($regexname, $name)) && (preg_match($regexnumbersamount, $amount)) && (preg_match($regexnumbersprice, $price)) && (preg_match($regexnumberscode, $category)) && (CheckNameAvaliable($name,"products",$data['id'])) ) {
                                if (CheckValidityCode($category,"categories")) {
                                    echo( json_encode ( UpdateSql( ['products'], [['code'],['name'],['amount'],['price'],['category_code']], ['code','name','amount','price','category_code'], [$data['name'],$data['amount'],$data['price'],$data['category']], [$data['oldName'],$data['oldAmount'],$data['oldPrice'],$data['oldCategory']], $data['id'] )[0] ) );
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
    } else {
        echo(json_encode(false));
    }
?>