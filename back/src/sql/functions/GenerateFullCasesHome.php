<?php
declare(strict_types=1);
    function GenerateFullCasesHome( array $row ): array {
        require_once __DIR__."/../../security/SafeCrypto.php";
        require_once __DIR__."/../../security/CheckValidityCamp.php";
        require_once __DIR__."/../Delete.php";
        $deleted = FALSE;
        $temporary = array();
        $name = SafeCrypto($row["product_name"],"Decrypt");
        $decoded_name = html_entity_decode($name);
        $amount = SafeCrypto($row["amount"],"Decrypt");
        $decoded_amount = html_entity_decode($amount);
        $int_decoded_amount = intval($decoded_amount);
        $price = SafeCrypto($row["price"],"Decrypt");
        $decoded_price = html_entity_decode($price);
        $float_decoded_price = floatval($decoded_price);
        $tax = SafeCrypto($row["tax"],"Decrypt");
        $decoded_tax = html_entity_decode($tax);
        $float_decoded_tax = floatval($decoded_tax);
        if ( ( CheckValidityCamp($name,$decoded_name,"product_name") ) && ( CheckValidityCamp($amount,$decoded_amount,"amount") ) && ( CheckValidityCamp($price,$decoded_price,"price") ) && ( CheckValidityCamp($tax,$decoded_tax,"tax") ) ){
            if ($row["products_amount"] == 'False'){
                $temporary['code'] = [$row["code"],"Broken"];//content
            } else {
                $products_name = SafeCrypto($row["products_name"],"Decrypt");
                $decoded_products_name = html_entity_decode($products_name);
                $products_amount = SafeCrypto($row["products_amount"],"Decrypt");
                $decoded_products_amount = html_entity_decode($products_amount);
                $int_decoded_products_amount = intval($decoded_products_amount);
                $products_price = SafeCrypto($row["products_price"],"Decrypt");
                $decoded_products_price = html_entity_decode($products_price);
                $float_decoded_products_price = floatval($decoded_products_price);
                if ( ( CheckValidityCamp($products_name,$decoded_products_name,"products_name") ) && ( CheckValidityCamp($products_amount,$decoded_products_amount,"products_amount") ) && ( CheckValidityCamp($products_price,$decoded_products_price,"products_price") ) ){
                    $products_tax = SafeCrypto($row["categories_tax"],"Decrypt");
                    $decoded_products_tax = html_entity_decode($products_tax);
                    $float_decoded_products_tax = floatval($decoded_products_tax);
                    if ( CheckValidityCamp($products_tax,$decoded_products_tax,"categories_tax") ) {
                        if ( ($decoded_name != $decoded_products_name) || ($int_decoded_amount > $int_decoded_products_amount) || ($float_decoded_price != $float_decoded_products_price) || ($float_decoded_tax != $float_decoded_products_tax) ) {
                            $temporary['code'] = [$row["code"],"Broken"];//amount
                        } else {
                            $temporary['code'] = [$row["code"],"Good"];
                        }
                    } else {
                        DeleteSql('DoubleForeign',$table,$row['code'],["products","categories"],["product_code","category_code"]);
                        $deleted = TRUE;
                    }
                } else {
                    DeleteSql('SimpleForeign',$table,$row['code'],["products"],["product_code"]);
                    $deleted = TRUE;
                }
            }
            if ($deleted == FALSE){
                $temporary['product_name'] = $name;
                $temporary['price'] = SafeCrypto("$".number_format($float_decoded_price, 2, '.', ''),'Html');
                $temporary['amount'] = $amount;
                $temporary['tax'] = $tax.'%';
                $temporary['total'] = SafeCrypto("$".number_format($float_decoded_price*$int_decoded_amount, 2, '.', ''),'Html');
            }
        } else {
            DeleteSql('Simple',$table,$row['code']);
            $deleted = TRUE;
        }
        return (array($deleted,$temporary));
    }
?>