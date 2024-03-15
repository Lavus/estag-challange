<?php
    declare(strict_types=1);
    header("Location: index.php");
    if  ( (!empty($_POST['deleteconfirmedkey'])) ) {
        if ($_POST['deleteconfirmedkey'] === 'YES'){
            require "connect-localhost.php";
            require_once "safedecrypto.php";
            $sqlupdord = "UPDATE orders SET value_total = '".safeEncrypt(codifyhtml('0'), getkey())."', value_tax = '".safeEncrypt(codifyhtml('0'), getkey())."' WHERE orders.code IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
            try {
                $conn->beginTransaction();
                $conn->exec($sqlupdord);
                $conn->commit();
                $sql = "DELETE FROM order_item WHERE order_item.order_code IN (SELECT MAX(orders.code) FROM orders);";
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
            $conn = null;
        }
    }
?>