<?php
    declare(strict_types=1);
    header("Location: products.php");
    require_once "checkname.php";
    require_once "checkcode.php";
    if ( (!empty($_POST['productname'])) && (!empty($_POST['categoryidhidden'])) && (!empty($_POST['unitprice'])) && (!empty($_POST['amount'])) && (!empty($_POST['alterid'])) && (!empty($_POST['oldname'])) && (!empty($_POST['oldamount'])) && (!empty($_POST['oldprice'])) && (!empty($_POST['oldcategory'])) ){
        if ($_POST['categoryidhidden'] !== 'null') {
            $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
            $regexnumbers = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
            $intval_amount = intval($_POST['amount']);
            $floatval_price = floatval($_POST['unitprice']);
            $intval_newcategory = intval($_POST['categoryidhidden']);
            if ( (preg_match($regexname, $_POST['productname'])) && (preg_match($regexnumbers, $_POST['unitprice'])) && ($floatval_price >= 0.01 ) && ($intval_newcategory !== 0) && ($intval_amount !== 0) && (checknameavaliable($_POST['productname'],"products",strval($_POST['alterid']))) && (checkvaliditycode(strval($_POST['alterid']),"products")) && (checkvaliditycode(strval($_POST['categoryidhidden']),"categories")) ) {
                require "connect-localhost.php";
                require_once "safedecrypto.php";
                $sqlid = "SELECT products.name, products.amount, products.price, products.category_code FROM products WHERE products.code = '".$_POST['alterid']."';";
                $prepid = $conn->prepare($sqlid);
                $prepid->execute();
                $resultid = $prepid->fetch(PDO::FETCH_ASSOC);
                $name1 = safeDecrypt($resultid['name'], getkey());
                $amount1 = safeDecrypt($resultid['amount'], getkey());
                $price1 = safeDecrypt($resultid['price'], getkey());
                $decodename1 = html_entity_decode($name1);
                $decodeamount1 = html_entity_decode($amount1);
                $decodeprice1 = html_entity_decode($price1);
                $intdecodeamount1 = intval($decodeamount1);
                $floatdecodeprice1 = floatval($decodeprice1);
                $intval_oldcategory = intval($resultid['category_code']);
                if ( ($name1 == 'FALSE') || (!(preg_match($regexname, $decodename1))) || ($amount1 == 'FALSE') || ( (strval($decodeamount1) !== '0') && ($intdecodeamount1 === 0) ) || ($price1 == 'FALSE') || (!(preg_match($regexnumbers, $decodeprice1))) || ($floatdecodeprice1 < 0.01 ) ){
                    $sqldelete1 = "DELETE FROM public.products WHERE products.code = '".$_POST['alterid']."';";
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqldelete1);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                } else if ( ($decodename1 == $_POST['oldname']) && ($intdecodeamount1 == intval($_POST['oldamount'])) && ($floatdecodeprice1 == floatval($_POST['oldprice'])) && ($intval_oldcategory == intval($_POST['oldcategory'])) ){
                    $continue = TRUE;
                    $sql = "UPDATE products SET ";
                    if ( ($_POST['productname'] == $decodename1) && ($intval_amount == $intdecodeamount1) && ($floatval_price == $floatdecodeprice1) && ($intval_newcategory == $intval_oldcategory) ){
                        $continue = FALSE;
                    } else {
                        if ( ($_POST['productname'] != $decodename1) ){
                            $setname = safeEncrypt(codifyhtml($_POST['productname']), getkey());
                            $sql = $sql."name = '".$setname."',";
                        }
                        if ( ($intval_amount != $intdecodeamount1) ){
                            $setamount = safeEncrypt(codifyhtml(strval($intval_amount)), getkey());
                            $sql = $sql."amount = '".$setamount."',";
                        }
                        if ( ($floatval_price != $floatdecodeprice1) ){
                            $setprice = safeEncrypt(codifyhtml(strval($floatval_price)), getkey());
                            $sql = $sql."price = '".$setprice."',";
                        }
                        if ( ($intval_newcategory != $intval_oldcategory) ){
                            $sql = $sql."category_code = '".$intval_newcategory."',";
                        }
                    }
                    $sql = rtrim($sql, ",");
                    $sql = $sql." WHERE code = '".$_POST['alterid']."';";
                    if ($continue){
                        try {
                            $conn->beginTransaction();
                            $conn->exec($sql);
                            $conn->commit();
                        } catch(PDOException $e) {
                            $conn->rollback();
                            error_log("Error: " . $e->getMessage() . "<br><br>");
                        }                        
                    }
                }
                $conn = null;
            }
        }
    }
?>