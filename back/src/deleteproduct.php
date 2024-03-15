<?php
    declare(strict_types=1);
    header("Location: products.php");
    require_once "checkcode.php";
    if  ( (!empty($_POST['deleteconfirmedkey'])) ) {
        if (intval($_POST['deleteconfirmedkey']) !== 0){
            if ( checkvaliditycode(strval($_POST['deleteconfirmedkey']),'products') ){
                require "connect-localhost.php";
                $sql = "DELETE FROM products WHERE code = '".$_POST['deleteconfirmedkey']."';";
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