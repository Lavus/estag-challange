<?php
    declare(strict_types=1);
    header("Location: categories.php");
    require_once "checkname.php";
    if (isset($_POST['tax'])){
        if ( (!empty($_POST['categoryname'])) && ((!empty($_POST['tax'])) || $_POST['tax'] == 0 ) ){
            $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
            $regexnumbers = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
            if ( (preg_match($regexname, $_POST['categoryname'])) && (preg_match($regexnumbers, $_POST['tax'])) && ($_POST['tax'] >= 0 ) && (checknameavaliable($_POST['categoryname'],"categories")) ) {
                require "connect-localhost.php";
                require_once "safedecrypto.php";
                $sql = "INSERT INTO categories (name, tax) VALUES ('".safeEncrypt(codifyhtml($_POST['categoryname']), getkey())."', '".safeEncrypt(codifyhtml($_POST['tax']), getkey())."');";
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