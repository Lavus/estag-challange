<?php
    declare(strict_types=1);
    function DeleteSql(array $type = [], string $table = '', string $code = '0', array $foreignTables = [], array $foreignKeys = [], string $where = '1=0'): bool {
        require_once "ConnectLocalHost.php";
        require_once "UpdateSql.php";
        require_once "SelectSql.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        if ($type[0] == "Simple") {
            $sql_delete = "DELETE FROM ".$table." WHERE code = '".$code."';";
        } else if ($type[0] == "SimpleForeign") {
            $sql_delete = "DELETE FROM ".$foreignTables[0]." WHERE ".$table.".code = '".$code."' AND ".$table.".".$foreignKeys[0]." = ".$foreignTables[0].".code;";
        } else if ($type[0] == "DoubleForeign") {
            $sql_delete = "DELETE FROM ".$foreignTables[1]." WHERE ".$table.".code = '".$code."' AND ".$table.".".$foreignKeys[0]." = ".$foreignTables[0].".code AND ".$table.".".$foreignKeys[1]." = ".$foreignTables[1].".code;";
        } else if ($type[0] == "SimpleWhere") {
            $sql_delete = "DELETE FROM ".$table." WHERE ".$where;
        } else {
            return (FALSE);
        }
        if (($table == 'order_item')&&(count($type) == 1)){
            $orderSelectValues = SelectSql(
                ['SimpleWhere'],
                'orders',
                '0',
                [['code'],['value_total'],['value_tax']],
                ['code','value_total_nope','value_tax_nope'],
                [],
                [],
                [],
                'none',
                'orders.code IN ( SELECT MAX( orders1.code ) FROM orders as orders1 );'
            );
            $orderCode = strval(array_keys($orderSelectValues)[0]);
            $valueTotal = $orderSelectValues[$orderCode]['value_total_nope'];
            $valueTax = $orderSelectValues[$orderCode]['value_tax_nope'];
            if ($type[0] == "Simple") {
                $totalPrice = SafeCrypto('0','Html');
                $totalTax = SafeCrypto('0','Html');
                $orderitemSelectValues = SelectSql(
                    ['SingleSimpleNone'],
                    'order_item',
                    $code,
                    [['code'],['amount'],['price'],['tax']],
                    ['code','amount','price','tax']
                );
                $amount = html_entity_decode($orderitemSelectValues[$code]['amount']);
                $price = html_entity_decode($orderitemSelectValues[$code]['price']);
                $tax = html_entity_decode($orderitemSelectValues[$code]['tax']);
                $cartValue = html_entity_decode($valueTotal);
                $cartTax = html_entity_decode($valueTax);
                $newprice = $price * (1 + ($tax/100));
                $newtax = $price * ($tax/100);
                $newprice = round(($newprice * $amount), 2);
                $newtax = round(($newtax * $amount), 2);
                $totalPrice = round(($cartValue - $newprice),2);
                $totalPrice = SafeCrypto(strval($totalPrice),'Html');
                $totalTax = round(($cartTax - $newtax),2);
                $totalTax = SafeCrypto(strval($totalTax),'Html');
            } else if ($type[0] == "SimpleWhere") {
                $totalPrice = SafeCrypto('0','Html');
                $totalTax = SafeCrypto('0','Html');
            }
            $resultOrders =  UpdateSql(
                ['orders', 'true'],
                [['code'],['value_total'],['value_tax']],
                ['code','value_total','value_tax'],
                [$totalPrice,$totalTax],
                [$valueTotal,$valueTax],
                $orderCode
            );
            if (!($resultOrders[0])){
                return (FALSE);
            }
        }
        if (($table == 'order_item')&&(count($type) == 2)){
            $ordersCodes = SelectSql(
                ['SimpleWhere'],
                'order_item',
                '0',
                [['code']],
                ['code'],
                [[['code']]],
                [['order_code']],
                ['orders'],
                'none',
                'order_item.code = '.$code.' AND order_item.order_code = orders.code AND order_item.order_code NOT IN ( SELECT MAX( orders1.code ) FROM orders as orders1 ) ;'
            );
            if (count($ordersCodes) > 0){
                $sql_delete = "DELETE FROM orders WHERE code = '".$ordersCodes[$code]['order_code']."';";
            }
        }
        $connection  = ConnectLocalHost();
        try {
            $connection ->beginTransaction();
            $connection ->exec($sql_delete);
            $connection ->commit();
        } catch(PDOException $e) {
            $connection ->rollback();
            error_log("Error: " . $e->getMessage() . "<br><br>");
            $connection  = null;
            if (($table == 'order_item')&&(count($type) == 1)){
                $resultOrders[1]->rollback();
                $resultOrders[1] = null;
            }
            return (FALSE);
        }
        $connection  = null;
        if (($table == 'order_item')&&(count($type) == 1)){
            $resultOrders[1]->commit();
            $resultOrders[1] = null;
        }
        return (TRUE);
    }
?>