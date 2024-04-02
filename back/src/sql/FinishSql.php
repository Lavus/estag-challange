<?php
    declare(strict_types=1);
    function FinishSql(string $finishValueTotal = '', string $finishValueTax = ''): bool {
        require_once "ConnectLocalHost.php";
        require_once "UpdateSql.php";
        require_once "SelectSql.php";
        require_once "InsertSql.php";
        require_once __DIR__."/../security/SafeCrypto.php";
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
        $valueTotal = floatval(html_entity_decode($orderSelectValues[$orderCode]['value_total_nope']));
        $valueTax = floatval(html_entity_decode($orderSelectValues[$orderCode]['value_tax_nope']));
        if ( ($valueTotal != floatval($finishValueTotal)) || ($valueTax != floatval($finishValueTax)) ){
            return(FALSE);
        }
        $orderItemSelectValues = SelectSql(
            ['SimpleWhere'],
            'order_item',
            '0',
            [['code'],['product_code'],['product_name'],['amount'],['price'],['tax']],
            ['code','product_code','name','amount','price_nope','tax_nope'],
            [[['name'],['amount'],['price'],['category_code']],[['tax']]],
            [['product_name','product_amount','product_price_nope','category_code'],['product_tax_nope']],
            ['products','categories'],
            'none',
            'order_item.order_code IN ( SELECT MAX( orders1.code ) FROM orders as orders1 ) AND order_item.product_code = products.code AND products.category_code = categories.code;'
        );
        if (count($orderItemSelectValues) == 0){
            return(FALSE);
        }
        $connections = array();
        $continue = TRUE;
        $totalCartValue = 0;
        $totalCartTax = 0;
        
        $resultInsert = InsertSql( ['orders', 'true'], ['value_total','value_tax'], [SafeCrypto('0','Html'),SafeCrypto('0','Html')] );
        if (!($resultInsert[0])){
            return(FALSE);
        } else {
            array_push($connections,$resultInsert[1]);
        }
        foreach($orderItemSelectValues as $item) {
            $name = $item['name'];
            $amount = $item['amount'];
            $price = $item['price_nope'];
            $tax = $item['tax_nope'];
            $productName = $item['product_name'];
            $productAmount = $item['product_amount'];
            $productPrice = $item['product_price_nope'];
            $productTax = $item['product_tax_nope'];
            $intAmount = intval(html_entity_decode($amount));
            $intProductAmount = intval(html_entity_decode($productAmount));
            if ( ($name != $productName) || ($price != $productPrice) || ($tax != $productTax) || ($intAmount > $intProductAmount) ){
                $continue = FALSE;
            } else {
                $category = strval($item['category_code']);
                $code = strval($item['product_code']);
                $floatProductPrice = floatval(html_entity_decode($productPrice));
                $floatProductTax = floatval(html_entity_decode($productTax));
                $newAmount = SafeCrypto((strval($intProductAmount - $intAmount)),'Html');
    
                $newprice = $floatProductPrice * (1 + ($floatProductTax/100));
                $newprice = round(($newprice * $intAmount), 2);
                $totalCartValue = round(($totalCartValue + $newprice), 2);
    
                $newtax = $floatProductPrice * ($floatProductTax/100);
                $newtax = round(($newtax * $intAmount), 2);
                $totalCartTax = round(($totalCartTax + $newtax), 2);
    
                $resultUpdateProducts =  UpdateSql(
                    ['products', 'true'],
                    [['code'],['name'],['amount'],['price'],['category_code']],
                    ['code','name','amount','price','category_code'],
                    [$name,$newAmount,$price,$category],
                    [$name,$productAmount,$price,$category],
                    $code
                );
                if (!($resultUpdateProducts[0])){
                    $continue = FALSE;
                } else {
                    array_push($connections,$resultUpdateProducts[1]);
                }
            }
        }
        if ( (!($continue)) || ($totalCartValue != $valueTotal) || ($totalCartTax != $valueTax) ){
            for ($index = 0;$index < count($connections);$index++) {
                $connections[$index]->rollback();
                $connections[$index] = null;
            }
            return(FALSE);
        }
        for ($index = 0;$index < count($connections);$index++) {
            $connections[$index]->commit();
            $connections[$index] = null;
        }
        return(TRUE);
    }
?>