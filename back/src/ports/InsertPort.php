<?php
    declare(strict_types=1);
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    require_once __DIR__."/../sql/InsertSql.php";
    require_once __DIR__."/../sql/SelectSql.php";
    require_once __DIR__."/../security/CheckValidityCode.php";
    require_once __DIR__."/../security/CheckNameAvaliable.php";
    require_once __DIR__."/../security/SafeCrypto.php";
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method == 'POST'){
        if (!empty($_SERVER['HTTP_FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44'])) {
            if ($_SERVER['HTTP_FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44'] == 'Falw1qKPKZYufBz0r2S1avMZ16BeNHPn3/nqJzg2IyDHF+XtM4x9cBMTOvG++LTO3wCbTEJXEocIO+xfjPCEunNGKu8DvjQzXG29DSSiuQsPnwVV+/cHwnNh6MFLg3KvNC4k3v9uhXZkRMBaRIglt2FnKt3gLssn'){
                $json = trim(file_get_contents('php://input'));
                $data = json_decode($json,true);
                $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
                $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
                $regexnumbersamountvalue = "/^[1-9]{1}[0-9]{0,}$/";
                if ( !empty($data['type']) ) {
                    if( ($data['type'] == "categories") && ( !empty($data['name']) ) && ( !empty($data['tax']) ) ){
                        $name = html_entity_decode($data['name']);
                        $tax = html_entity_decode($data['tax']);
                        if ( (preg_match($regexname, $name)) && (preg_match($regexnumberstax, $tax)) && (CheckNameAvaliable($name,"categories")) ) {
                            echo( json_encode ( InsertSql( 'categories', ['name','tax'], [$data['name'],$data['tax']] ) ) );
                        } else {
                            echo(json_encode(false));
                        }
                    } else if( ($data['type'] == "products") && ( !empty($data['name']) ) && ( !empty($data['amount']) ) && ( !empty($data['price']) ) && ( !empty($data['category']) ) ){
                        $name = html_entity_decode($data['name']);
                        $price = html_entity_decode($data['price']);
                        $amount = html_entity_decode($data['amount']);
                        $category = $data['category'];
                        if ( (preg_match($regexname, $name)) && (preg_match($regexnumbersprice, $price)) && (preg_match($regexnumbersamountvalue, $amount)) && (preg_match($regexnumbersamountvalue, $category)) && (CheckNameAvaliable($name,"products")) ) {
                            if (CheckValidityCode($category,"categories")) {
                                echo( json_encode ( InsertSql( 'products', ['name','amount','price','category_code'], [$data['name'],$data['amount'],$data['price'],$data['category']] ) ) );
                            } else {
                                echo(json_encode(false));
                            }
                        } else {
                            echo(json_encode(false));
                        }
                    } else if( ($data['type'] == "order_item") && ( !empty($data['amount']) ) && ( !empty($data['product']) ) ){
                        $amount = html_entity_decode($data['amount']);
                        $product = $data['product'];
                        if ( (preg_match($regexnumbersamountvalue, $amount)) && (preg_match($regexnumbersamountvalue, $product))) {
                            if (CheckValidityCode($product,"products")) {
                                $productSelectValues = SelectSql(
                                    ['FullCases'],
                                    'products',
                                    $product,
                                    [['code'],['name'],['amount'],['price']],
                                    ['code','name','amount','price_nope'],
                                    [[['tax']]],
                                    [['tax_nope']],
                                    ['categories'],
                                    'category_code',
                                    'products.category_code = categories.code AND products.code = '.$product.';',
                                    [[['code']],[['code']],[['code']],[['code']],[['code']]],
                                    [['orders'],['orders'],['orders'],['order_item'],['order_item']],
                                    [['orders1'],['orders2'],['orders3'],['order_item1'],['order_item3']],
                                    ['orders1.code IN ( SELECT MAX( orders4.code ) FROM orders as orders4 )','orders2.code IN ( SELECT MAX( orders5.code ) FROM orders as orders5 )','orders3.code IN ( SELECT MAX( orders6.code ) FROM orders as orders6 )','order_item1.product_code = products.code AND order_item1.order_code IN ( SELECT MAX( orders13.code ) FROM orders as orders13 )','order_item3.product_code = products.code AND order_item3.order_code IN ( SELECT MAX( orders15.code ) FROM orders as orders15 )'],
                                    ['IS NOT NULL','IS NOT NULL','IS NOT NULL','IS NOT NULL','IS NOT NULL'],
                                    [[['code']],[['value_total']],[['value_tax']],[['amount']],[['code']]],
                                    [['orders'],['orders'],['orders'],['order_item'],['order_item']],
                                    [['orders7'],['orders8'],['orders9'],['order_item2'],['order_item4']],
                                    ['orders7.code IN ( SELECT MAX( orders10.code ) FROM orders as orders10 )','orders8.code IN ( SELECT MAX( orders11.code ) FROM orders as orders11 )','orders9.code IN ( SELECT MAX( orders12.code ) FROM orders as orders12 )','order_item2.product_code = products.code AND order_item2.order_code IN ( SELECT MAX( orders14.code ) FROM orders as orders14 )','order_item4.product_code = products.code AND order_item4.order_code IN ( SELECT MAX( orders16.code ) FROM orders as orders16 )'],
                                    [0,'False','False','0','0'],
                                    ['order_code','value_total_nope','value_tax_nope','order_amount','order_item_code']
                                );
                                if ($productSelectValues[$product]['order_code'] == 0){
                                    $resultInsert = InsertSql( 'orders', ['value_total','value_tax'], [SafeCrypto('0','Html'),SafeCrypto('0','Html')] );
                                    if (!($resultInsert)){
                                        echo(json_encode(false));
                                    }
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
                                    $orderCode = array_keys($orderSelectValues)[0];
                                    $valueTotal = $orderSelectValues[$orderCode]['value_total_nope'];
                                    $valueTax = $orderSelectValues[$orderCode]['value_tax_nope'];
                                } else {
                                    $orderCode = $productSelectValues[$product]['order_code'];
                                    $valueTotal = $productSelectValues[$product]['value_total_nope'];
                                    $valueTax = $productSelectValues[$product]['value_tax_nope'];
                                }
                                $cartAmount = $productSelectValues[$product]['order_amount'];
                                $orderItemId = $productSelectValues[$product]['order_item_code'];
                                echo( json_encode ( InsertSql( 'order_item', ['order_code','product_code','product_name','amount','price','tax'], [$orderCode,$data['product'],$productSelectValues[$product]['name'],$data['amount'],$productSelectValues[$product]['price_nope'],$productSelectValues[$product]['tax_nope']], [$valueTotal,$valueTax,$cartAmount, $orderItemId] ) ) );
                            } else {
                                echo(json_encode(false));
                            }
                        } else {
                            echo(json_encode(false));
                        }
                    } else {
                        echo(json_encode(false));
                    }
                } else {
                    echo(json_encode(false));
                }
            } else {
                echo(json_encode(false));
            }
        } else {
            echo(json_encode(false));
        }
    } else {
        echo(json_encode(false));
    }
?>