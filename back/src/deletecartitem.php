<?php
    declare(strict_types=1);
    header("Location: index.php");
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    if  ( (!empty($_POST['deletekey'])) ) {
        if (intval($_POST['deletekey']) !== 0){
            if ( checkvaliditycode(strval($_POST['deletekey']),'order_item') ){
                require "connect-localhost.php";
                require_once "safedecrypto.php";
                $executesecure = TRUE;
                $sqlsel = "SELECT order_item.price, order_item.amount, order_item.tax FROM order_item WHERE order_item.code = '".$_POST['deletekey']."';";
                try {
                    $prepsel = $conn->prepare($sqlsel);
                    $prepsel->execute();
                    $resultsel = $prepsel->fetch(PDO::FETCH_ASSOC);
                    $price1 = safeDecrypt($resultsel['price'], getkey());
                    $tax1 = safeDecrypt($resultsel['tax'], getkey());
                    $amount1 = safeDecrypt($resultsel['amount'], getkey());
                    $decodeprice1 = html_entity_decode($price1);
                    $decodetax1 = html_entity_decode($tax1);
                    $decodeamount1 = html_entity_decode($amount1);
                    $price = floatval($decodeprice1);
                    $tax = floatval($decodetax1);
                    $amount = intval($decodeamount1);
                    if ( ($amount1 == 'FALSE') || ($amount === 0) || ($price1 == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeprice1))) || ($price < 0.01 ) || (!(preg_match($regexnumberstax, $decodetax1))) ){
                        $executesecure = FALSE;
                    }
                    if ($executesecure) {
                        $valuetotaldeleted = round(($price * (1 + ($tax/100))) * $amount,2);
                        $valuetaxdeleted = round(($price * ($tax/100)) * $amount,2);
                    }
                    $sqlord = "SELECT orders.value_total, orders.value_tax FROM orders,order_item WHERE orders.code = order_item.order_code AND order_item.code = '".$_POST['deletekey']."';";
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
                            $executesecure = FALSE;
                        }
                        if ($executesecure) {
                            $newvalue_tax = safeEncrypt(codifyhtml(strval(round($value_tax - $valuetaxdeleted,2))), getkey());
                            $newvalue_total = safeEncrypt(codifyhtml(strval(round($value_total - $valuetotaldeleted,2))), getkey());
                        } else {
                            $newvalue_tax = safeEncrypt(codifyhtml('0'));
                            $newvalue_total = safeEncrypt(codifyhtml('0'));
                        }
                        $sqlupdord = "UPDATE orders SET value_total='".$newvalue_total."', value_tax='".$newvalue_tax."' WHERE orders.code IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                        try {
                            $conn->beginTransaction();
                            $conn->exec($sqlupdord);
                            $conn->commit();
                            $sql = "DELETE FROM order_item WHERE order_item.code = '".$_POST['deletekey']."';";
                            try {
                                $conn->beginTransaction();
                                $conn->exec($sql);
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
                        error_log($sql . "<br>" . $e->getMessage());
                    }
                } catch(PDOException $e) {
                    error_log($sql . "<br>" . $e->getMessage());
                }
                $conn = null;
            }
        }
    }
?>