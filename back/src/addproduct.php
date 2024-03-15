<?php
    declare(strict_types=1);
    header("Location: products.php");
    require_once "checkname.php";
    require_once "checkcode.php";
    if ( (!empty($_POST['productname'])) && (!empty($_POST['categoryidhidden'])) && (!empty($_POST['unitprice'])) && (!empty($_POST['amount'])) ){
        if ($_POST['categoryidhidden'] !== 'null') {
            $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
            $regexnumbers = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
            if ( (preg_match($regexname, $_POST['productname'])) && (preg_match($regexnumbers, $_POST['unitprice'])) && ($_POST['unitprice'] >= 0.01 ) && (intval($_POST['categoryidhidden']) !== 0) && (intval($_POST['amount']) !== 0) && (checknameavaliable($_POST['productname'],"products")) && (checkvaliditycode(strval($_POST['categoryidhidden']),"categories")) ) {
                require "connect-localhost.php";
                require_once "safedecrypto.php";
                $sql = "INSERT INTO products (name, amount, price, category_code) VALUES ('".safeEncrypt(codifyhtml($_POST['productname']), getkey())."', '".safeEncrypt(codifyhtml($_POST['amount']), getkey())."', '".safeEncrypt(codifyhtml($_POST['unitprice']), getkey())."', '".$_POST['categoryidhidden']."');";
                try {
                    $conn->beginTransaction();
                    $conn->exec($sql);
                    $conn->commit();
                } catch(PDOException $e) {
                    $conn->rollback();
                    error_log("Error: " . $e->getMessage() . "<br><br>");
                }
                $conn = null;
            }
        }
    }
?>