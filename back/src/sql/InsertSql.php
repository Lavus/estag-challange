<?php
    declare(strict_types=1);
    function InsertSql(string $table = 'none', array $camps = [], array $values = [], array $secondaryValues = [] ): bool {
        require_once "ConnectLocalHost.php";
        require_once "UpdateSql.php";
        require_once __DIR__."/../security/SafeCrypto.php";
        $continue = TRUE;
        if ($table == 'order_item'){
            $orderId = strval($values[0]);
            $orderItemId = strval($secondaryValues[3]);
            $amount = html_entity_decode($values[3]);
            $price = html_entity_decode($values[4]);
            $tax = html_entity_decode($values[5]);
            $cartValue = html_entity_decode($secondaryValues[0]);
            $cartTax = html_entity_decode($secondaryValues[1]);
            $cartAmount = html_entity_decode($secondaryValues[2]);
            $newprice = $price * (1 + ($tax/100));
            $newtax = $price * ($tax/100);
            $newprice = round(($newprice * $amount), 2);
            $newtax = round(($newtax * $amount), 2);
            $totalPrice = $newprice + $cartValue;
            $totalPrice = SafeCrypto(strval($totalPrice),'Html');
            $totalTax = $newtax + $cartTax;
            $totalTax = SafeCrypto(strval($totalTax),'Html');
            $totalAmount = $amount + $cartAmount;
            $totalAmount = SafeCrypto(strval($totalAmount),'Html');
            $resultOrders =  UpdateSql(
                'orders',
                [['code'],['value_total'],['value_tax']],
                ['code','value_total','value_tax'],
                [$totalPrice,$totalTax],
                [$secondaryValues[0],$secondaryValues[1]],
                $orderId
            );
            if (!($resultOrders)){
                $continue = FALSE;
            }
            if ($continue){
                if ($cartAmount > 0){
                    $resultItem =  UpdateSql(
                        'order_item',
                        [['code'],['order_code'],['product_code'],['product_name'],['amount'],['price'],['tax']],
                        ['code','order_code','product_code','product_name','amount','price','tax'],
                        [$orderId,strval($values[1]),$values[2],$totalAmount,$values[4],$values[5]],
                        [$orderId,strval($values[1]),$values[2],$secondaryValues[2],$values[4],$values[5]],
                        $orderItemId
                    );
                    if ($resultItem){
                        return (TRUE);
                    } else {
                        return (FALSE);
                    }
                }
            }
        }
        $stringCamps = "";
        $stringValues = "";
        foreach($camps as $indexCamp => $camp) {
            $stringCamps .= $camp.",";
            if ( str_contains($camp,"code") ){
                $stringValues .= "'".$values[$indexCamp]."',";
            } else {
                $stringValues .= "'".SafeCrypto($values[$indexCamp],'Encrypt')."',";
            }
        }
        $stringCamps = rtrim($stringCamps, ",");
        $stringValues = rtrim($stringValues, ",");
        $sql = "INSERT INTO ".$table." ( ".$stringCamps." ) VALUES ( ".$stringValues." );";
        // error_log($sql);
        if ($continue){
            $connection  = ConnectLocalHost();
            try {
                $connection->beginTransaction();
                $connection->exec($sql);
                $connection->commit();
            } catch(PDOException $e) {
                $connection->rollback();
                error_log("Error: " . $e->getMessage() . "<br><br>");
                $connection = null;
                return (FALSE);
            }
            $connection = null;
            return (TRUE);
        }
        return (FALSE);
    }
?>