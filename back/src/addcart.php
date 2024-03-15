<?php
    declare(strict_types=1);
    header("Location: index.php");
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    if ( (!empty($_POST['productidhidden'])) && (!empty($_POST['amount'])) ){
        if ( (intval($_POST['productidhidden']) !== 0) && (intval($_POST['amount']) !== 0) && (checkvaliditycode(strval($_POST['productidhidden']),"products")) ) {
            require "connect-localhost.php";
            require_once "safedecrypto.php";
            $executesecure = TRUE;
            $sqlid = "SELECT products.name, products.amount AS product_amount, products.price, categories.tax, CASE WHEN EXISTS ( SELECT order_item1.amount FROM order_item AS order_item1 WHERE order_item1.product_code = products.code AND order_item1.order_code  IN (SELECT MAX(orders1.code) FROM orders AS orders1) ) THEN (SELECT order_item2.amount FROM order_item AS order_item2 WHERE order_item2.product_code = products.code AND order_item2.order_code IN (SELECT MAX(orders2.code) FROM orders AS orders2) ) ELSE '0' END AS cart_amount FROM products, categories WHERE products.code = '".$_POST['productidhidden']."' AND products.category_code = categories.code;";
            try {
                $prepid = $conn->prepare($sqlid);
                $prepid->execute();
                $resultid = $prepid->fetch(PDO::FETCH_ASSOC);
                $product_amount1 = safeDecrypt($resultid['product_amount'], getkey());
                $price1 = safeDecrypt($resultid['price'], getkey());
                $tax1 = safeDecrypt($resultid['tax'], getkey());
                $name1 = safeDecrypt($resultid['name'], getkey());
                $decodeproduct_amount1 = html_entity_decode($product_amount1);
                $decodeprice1 = html_entity_decode($price1);
                $decodetax1 = html_entity_decode($tax1);
                $decodename1 = html_entity_decode($name1);
                $intdecodeproduct_amount1 = intval($decodeproduct_amount1);
                $oldprice = floatval($decodeprice1);
                $oldtax = floatval($decodetax1);
                if ( ($name1 == 'FALSE') || (!(preg_match($regexname, $decodename1))) || ($product_amount1 == 'FALSE') || ( (strval($decodeproduct_amount1) !== '0') && ($intdecodeproduct_amount1 === 0) ) || ($price1 == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeprice1))) || ($oldprice < 0.01 ) || ($tax1 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax1))) ){
                    $executesecure = FALSE;
                    if ( ($tax1 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax1))) ) {
                        $sqldelete1 = "DELETE FROM public.categories WHERE categories.code IN (SELECT products.category_code FROM public.products WHERE products.code = '".$_POST['productidhidden']."');";
                    } else {
                        $sqldelete1 = "DELETE FROM public.products WHERE products.code = '".$_POST['productidhidden']."';";
                    }
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqldelete1);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                }
                if ($executesecure){
                    if ( $resultid['cart_amount'] == "0" ){
                        $amountleft = $intdecodeproduct_amount1;
                    } else {
                        $cart_amount1 = safeDecrypt($resultid['cart_amount'], getkey());
                        $decodecart_amount1 = html_entity_decode($cart_amount1);
                        $intdecodecart_amount1 = intval($decodecart_amount1);
                        if ( ($cart_amount1 == 'FALSE') || ($intdecodecart_amount1 === 0) ) {
                            $executesecure = FALSE;
                            $sqldelete2 = "DELETE FROM public.order_item WHERE order_item.product_code = '".$_POST['productidhidden']."' AND order_item.order_code IN (SELECT MAX(orders.code) FROM orders);";
                            try {
                                $conn->beginTransaction();
                                $conn->exec($sqldelete2);
                                $conn->commit();
                            } catch(PDOException $e) {
                                $conn->rollback();
                                error_log("Error: " . $e->getMessage() . "<br><br>");
                            }
                        } else {
                            $amountleft = $intdecodeproduct_amount1 - $intdecodecart_amount1;
                        }
                    }
                    if ($executesecure){
                        if ($amountleft >= $_POST['amount']){
                            $newprice = $oldprice * (1 + ($oldtax/100));
                            $newtax = $oldprice * ($oldtax/100);
                            $totalprice = $newprice * $_POST['amount'];
                            $totaltax = $newtax * $_POST['amount'];
                            $totalprice = round($totalprice, 2);
                            $totaltax = round($totaltax, 2);
                            $sql1 = "DO ".'$do$'." BEGIN IF NOT EXISTS (SELECT code FROM orders) THEN INSERT INTO public.orders(value_total, value_tax) VALUES ('".safeEncrypt(codifyhtml('0'), getkey())."', '".safeEncrypt(codifyhtml('0'), getkey())."'); END IF; END ".'$do$';
                            try {
                                $conn->beginTransaction();
                                $conn->exec($sql1);
                                $conn->commit();
                                if ( $resultid['cart_amount'] == "0" ){
                                    $sql = "INSERT INTO public.order_item(order_code, product_code, product_name, amount, price, tax) SELECT (SELECT MAX(orders.code) FROM orders) AS order_code, products.code AS product_code, products.name AS product_name, ('".safeEncrypt(codifyhtml(strval($_POST['amount'])), getkey())."') AS amount, products.price AS price, categories.tax AS tax FROM products, categories WHERE products.code = '".$_POST['productidhidden']."' AND products.category_code = categories.code;";
                                } else {
                                    $updated_amount = intval($_POST['amount']) + $intdecodecart_amount1;
                                    $sql = "UPDATE public.order_item SET amount = '".safeEncrypt(codifyhtml(strval($updated_amount)), getkey())."' WHERE order_item.product_code = '".$_POST['productidhidden']."';";
                                }
                                try {
                                    $conn->beginTransaction();
                                    $conn->exec($sql);
                                    $conn->commit();
                                    $sqlord = "SELECT orders.value_total, orders.value_tax FROM orders WHERE orders.code IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                                    try {
                                        $prepord = $conn->prepare($sqlord);
                                        $prepord->execute();
                                        $resultord = $prepord->fetch(PDO::FETCH_ASSOC);
                                        $valuetax1 = safeDecrypt($resultord['value_tax'], getkey());
                                        $valuetotal1 = safeDecrypt($resultord['value_total'], getkey());
                                        $decodevaluetax1 = html_entity_decode($valuetax1);
                                        $decodevaluetotal1 = html_entity_decode($valuetotal1);
                                        $value_tax = floatval($decodevaluetax1);
                                        $value_total = floatval($decodevaluetotal1);
                                        if ( ($valuetax1 == 'FALSE') || ($valuetotal1 == 'FALSE') || (!(preg_match($regexnumbers, $decodevaluetax1))) || (!(preg_match($regexnumbers, $decodevaluetotal1))) || ($value_total == 0) || ($value_tax == 0) ) {
                                            $ordtotalprice = $totalprice;
                                            $ordtotaltax = $totaltax;
                                        }else{
                                            $ordtotalprice = $value_total + $totalprice;
                                            $ordtotaltax = $value_tax + $totaltax;
                                        }
                                        $ordtotalprice = safeEncrypt(codifyhtml(strval(round($ordtotalprice,2))), getkey());
                                        $ordtotaltax = safeEncrypt(codifyhtml(strval(round($ordtotaltax,2))), getkey());
                                        $sqlupdord = "UPDATE orders SET value_total='".$ordtotalprice."', value_tax='".$ordtotaltax."' WHERE orders.code IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                                        try {
                                            $conn->beginTransaction();
                                            $conn->exec($sqlupdord);
                                            $conn->commit();
                                        } catch(PDOException $e) {
                                            $conn->rollback();
                                            error_log("Error: " . $e->getMessage() . "<br><br>");
                                        }
                                    } catch(PDOException $e) {
                                        $conn->rollback();
                                        error_log("Error: " . $e->getMessage() . "<br><br>");
                                    }
                                } catch(PDOException $e) {
                                    $conn->rollback();
                                    error_log("Error: " . $e->getMessage() . "<br><br>");
                                }
                            } catch(PDOException $e) {
                                $conn->rollback();
                                error_log("Error: " . $e->getMessage() . "<br><br>");
                            }
                        } else {
                            error_log("Error: Amount defined is bigger then stock<br><br>");
                        }
                    }
                }
            } catch(PDOException $e) {
                $conn->rollback();
                error_log("Error: " . $e->getMessage() . "<br><br>");
            }
            $conn = null;
        }
    }
?>