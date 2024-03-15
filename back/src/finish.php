<?php
    declare(strict_types=1);
    header("Location: index.php");
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    if (!empty($_POST['purchaseconfirmedkey'])) {
        if (floatval($_POST['purchaseconfirmedkey']) !== 0) {
            require "connect-localhost.php";
            require_once "safedecrypto.php";
            $executesecure = TRUE;
            $sqlorder = "SELECT orders.value_total, orders.value_tax FROM orders WHERE orders.code IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
            try {
                $preporder = $conn->prepare($sqlorder);
                $preporder->execute();
                $resultorder = $preporder->fetch(PDO::FETCH_ASSOC);
                $value_total1 = safeDecrypt($resultorder['value_total'], getkey());
                $tax_total1 = safeDecrypt($resultorder['value_tax'], getkey());
                $tax_total_orders = html_entity_decode($tax_total1);
                $value_total_orders = html_entity_decode($value_total1);
                if ( ($value_total1 == 'FALSE') || ($tax_total1 == 'FALSE') ){
                    $tax_total_orders = 0;
                    $value_total_orders = 0;
                    $executesecure = FALSE;
                }
            } catch(PDOException $e) {
                $executesecure = FALSE;
                error_log($sqlorder . "<br>" . $e->getMessage());
            }
            $sql = "SELECT CASE WHEN ( SELECT order_item1.product_code FROM order_item AS order_item1 WHERE order_item1.code = order_item.code ) IS NOT NULL THEN ( SELECT products1.name FROM products AS products1 WHERE products1.code = order_item.product_code ) ELSE 'False' END AS products_name, CASE WHEN ( SELECT order_item2.product_code FROM order_item AS order_item2 WHERE order_item2.code = order_item.code ) IS NOT NULL THEN ( SELECT products2.amount FROM products AS products2 WHERE products2.code = order_item.product_code ) ELSE 'False' END AS products_amount, CASE WHEN ( SELECT order_item3.product_code FROM order_item AS order_item3 WHERE order_item3.code = order_item.code ) IS NOT NULL THEN ( SELECT products3.price FROM products AS products3 WHERE products3.code = order_item.product_code ) ELSE 'False' END AS products_price, CASE WHEN ( SELECT order_item4.product_code FROM order_item AS order_item4 WHERE order_item4.code = order_item.code ) IS NOT NULL THEN ( SELECT categories1.tax FROM products AS products4, categories AS categories1 WHERE products4.code = order_item.product_code AND categories1.code = products4.category_code ) ELSE 'False' END AS categories_tax, CASE WHEN ( SELECT order_item5.product_code FROM order_item AS order_item5 WHERE order_item5.code = order_item.code ) IS NOT NULL THEN ( SELECT order_item6.product_code FROM order_item AS order_item6 WHERE order_item6.code = order_item.code ) ELSE 0 END AS products_code, order_item.product_name AS order_product_name, order_item.amount AS order_amount, order_item.price AS order_price, order_item.tax AS order_tax, order_item.code AS order_item_code FROM order_item WHERE order_item.order_code IN ( SELECT MAX( orders.code ) FROM orders ) ORDER BY order_item.code;";
            $value_total_cart = 0;
            $tax_total_cart = 0;
            $cart_total_price = 0;
            $rederror = false;
            $listofproducts = array();
            $listofamountleft = array();
            try {
                $prep = $conn->prepare($sql);
                $prep->execute();
                if ($prep->rowCount() > 0) {
                    $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                    foreach($result as $row) {
                        if ($row['products_name'] != 'False') {
                            $products_name = safeDecrypt($row['products_name'], getkey());
                            $products_amount = safeDecrypt($row['products_amount'], getkey());
                            $products_price = safeDecrypt($row['products_price'], getkey());
                            $categories_tax = safeDecrypt($row['categories_tax'], getkey());
                            $decodeproducts_name = html_entity_decode($products_name);
                            $decodeproducts_amount = html_entity_decode($products_amount);
                            $decodeproducts_price = html_entity_decode($products_price);
                            $decodecategories_tax = html_entity_decode($categories_tax);
                            $products_amount_int = intval($decodeproducts_amount);
                            $floatdecodeproducts_price = floatval($decodeproducts_price);
                            if ( ($products_name == 'FALSE') || (!(preg_match($regexname, $decodeproducts_name))) || ($products_amount == 'FALSE') || ( (strval($decodeproducts_amount) !== '0') && ($products_amount_int === 0) ) || ($products_price == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeproducts_price))) || ($floatdecodeproducts_price < 0.01 ) || ($categories_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodecategories_tax))) ){
                                $executesecure = FALSE;
                                if ( ($categories_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodecategories_tax))) ) {
                                    $sqldelete2 = "DELETE FROM categories WHERE categories.code IN (SELECT products.category_code FROM products WHERE products.code IN (SELECT order_item.product_code FROM order_item WHERE order_item.code = '".$row['order_item_code']."')));";
                                } else {
                                    $sqldelete2 = "DELETE FROM products WHERE products.code IN (SELECT order_item.product_code FROM order_item WHERE order_item.code = '".$row['order_item_code']."');";
                                }
                                try {
                                    $conn->beginTransaction();
                                    $conn->exec($sqldelete2);
                                    $conn->commit();
                                } catch(PDOException $e) {
                                    $executesecure = FALSE;
                                    $conn->rollback();
                                    error_log("Error: " . $e->getMessage() . "<br><br>");
                                }
                            }
                        }
                        $order_product_name = safeDecrypt($row['order_product_name'], getkey());
                        $order_amount = safeDecrypt($row['order_amount'], getkey());
                        $order_price = safeDecrypt($row['order_price'], getkey());
                        $order_tax = safeDecrypt($row['order_tax'], getkey());
                        $decodeorder_product_name = html_entity_decode($order_product_name);
                        $decodeorder_amount = html_entity_decode($order_amount);
                        $decodeorder_price = html_entity_decode($order_price);
                        $decodeorder_tax = html_entity_decode($order_tax);
                        $order_amount_int = intval($decodeorder_amount);
                        $order_price_float = floatval($decodeorder_price);
                        $order_tax_float = floatval($decodeorder_tax);
                        if ( ($order_product_name == 'FALSE') || (!(preg_match($regexname, $decodeorder_product_name))) || ($order_amount == 'FALSE') || ($order_amount_int === 0) || ($order_price == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeorder_price))) || ($order_price_float < 0.01 ) || ($order_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodeorder_tax))) ){
                            $executesecure = FALSE;
                            $sqldelete3 = "DELETE FROM order_item WHERE order_item.code = '".$row['order_item_code']."';";
                            try {
                                $conn->beginTransaction();
                                $conn->exec($sqldelete3);
                                $conn->commit();
                            } catch(PDOException $e) {
                                $conn->rollback();
                                error_log("Error: " . $e->getMessage() . "<br><br>");
                            }
                        }
                        if ($executesecure){
                            $order_total_price = number_format(round($order_amount_int * $order_price_float,2), 2, '.', '');
                            $newprice = $order_price_float * (1 + ($order_tax_float/100));
                            $newtax = $order_price_float * ($order_tax_float/100);
                            $totalprice = $newprice * $order_amount_int;
                            $totaltax = $newtax * $order_amount_int;
                            $value_total_cart += $totalprice;
                            $tax_total_cart += $totaltax;
                            $cart_total_price += floatval($order_total_price);
                            if ($row['products_name'] != 'False') {                                 
                                if ( ( $products_name == $order_product_name ) && ( $products_amount_int >= $order_amount_int ) && ( $products_price == $order_price ) && ( $categories_tax == $order_tax ) ) {
                                    array_push($listofproducts, intval($row['products_code']));
                                    array_push($listofamountleft, intval($products_amount_int - $order_amount_int));
                                } else {
                                    $rederror = true;
                                }
                            } else {
                                $rederror = true;
                            }
                        }
                    }
                } else {
                    $executesecure = FALSE;
                }
            } catch(PDOException $e) {
                $executesecure = FALSE;
                error_log($sql . "<br>" . $e->getMessage());
            }
            if ($executesecure){
                if ( ( round(floatval($value_total_orders),2) != round(floatval($value_total_cart),2) ) && ( round(floatval($tax_total_orders),2) != round(floatval($tax_total_cart),2) ) && ( round(floatval($_POST['purchaseconfirmedkey']),2) != round(floatval($$cart_total_price),2) ) && ( count($listofproducts) != count($listofamountleft) ) ){
                    $rederror = true;
                }
                if ($rederror){
                    $executesecure = FALSE;
                    echo ("ERROR");
                } else {
                    for ($index = 0;$index < count($listofproducts);$index++){
                        $sqlupdateamount = "UPDATE products SET amount = '".safeEncrypt(codifyhtml(strval($listofamountleft[$index])), getkey())."' WHERE products.code = '".$listofproducts[$index]."';";
                        try {
                            $conn->beginTransaction();
                            $conn->exec($sqlupdateamount);
                            $conn->commit();
                        } catch(PDOException $e) {
                            error_log($sqlupdateamount . "<br>" . $e->getMessage());
                        }
                    }
                    $sqlinsert = "INSERT INTO orders(value_total, value_tax) VALUES ('".safeEncrypt(codifyhtml('0'), getkey())."', '".safeEncrypt(codifyhtml('0'), getkey())."');";
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqlinsert);
                        $conn->commit();
                    } catch(PDOException $e) {
                        error_log($sqlinsert . "<br>" . $e->getMessage());
                    }
                }
                $conn = null;
            }
        }
    }
?>