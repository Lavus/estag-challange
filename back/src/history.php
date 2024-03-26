<?php
    declare(strict_types=1);
    require "connect-localhost.php";
    require_once "safedecrypto.php";
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    $executesecure = TRUE;
    if ( (!empty($_POST['viewkey'])) ) {
        if (intval($_POST['viewkey']) !== 0){
            if ( checkvaliditycode(strval($_POST['viewkey']),'orders') ){
                $viewkey = TRUE;
            } else {
                $viewkey = FALSE;
            }
        } else {
            $viewkey = FALSE;
        }
    } else {
        $viewkey = FALSE;
    }
?>
<!DOCTYPE html>
<html>
    <p id="test" hidden></p>
    <header>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="store.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous"></script>
        <script src="store.js"></script>
        <title>Suite Store</title>
    </header>
    <body>
        <div class="topnav" id="myTopnav">
            <a id="selectedpage" href="index.php">Suite Store</a>
            <a href="products.php">Products</a>
            <a href="categories.php">Categories</a>
            <a href="history.php">History</a>
            <a href="#" class="icon">&#9776;</a>
        </div>
        <div class="main">
            <?php
                echo (safeEncrypt(codifyhtml('InsertKey'),getkey()));
                echo ('<br>');
                echo (safeEncrypt(codifyhtml('InsertKeyValue'),getkey()));
                if ($viewkey){
                    echo ("<div class='textdropleft'><div title='View Purchase History'>View Purchase History</div><div class='icon'>&#9776;</div></div>");
                    echo ("<div class='textdropright'><div title='View Purchase info'>View Purchase info</div><div class='icon hidden'>&#9776;</div></div>");
                    echo ("<div class='left'>");
                } else {
                    echo ("<div class='textdropleft'><div title='View Purchase History'>View Purchase History</div><div class='icon hidden'>&#9776;</div></div>");
                    echo ("<div class='textdropright'><div title='View Purchase info'>View Purchase info</div><div class='icon'>&#9776;</div></div>");
                    echo ("<div class='left show'>");
                }
            ?>
                <form action="history.php" method="post" id="viewfrompurchase"></form>
                <div class="scroll">
                    <table id="tablehistory" class="collapse lefttext">
                        <?php
                            $names = ["Code","Tax","Total"];
                            echo("<tr>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<th title='".$names[$index]."'>".codifyhtml($names[$index])."</th>");
                                }
                            echo("</tr>");
                            $sql = "SELECT orders.code, orders.value_total, orders.value_tax FROM orders WHERE orders.code NOT IN ( SELECT MAX( orders1.code ) FROM orders AS orders1 ) ORDER BY orders.code;";
                            try {
                                $prep = $conn->prepare($sql);
                                $prep->execute();
                                if ($prep->rowCount() > 0) {
                                    $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($result as $row) {
                                        $valuetax1 = safeDecrypt($row['value_tax'], getkey());
                                        $valuetotal1 = safeDecrypt($row['value_total'], getkey());
                                        $decodevaluetax1 = html_entity_decode($valuetax1);
                                        $decodevaluetotal1 = html_entity_decode($valuetotal1);
                                        $floatdecodevaluetax1 = floatval($decodevaluetax1);
                                        $floatdecodevaluetotal1 = floatval($decodevaluetotal1);                                        
                                        $formartdecodevaluetax1 = number_format($floatdecodevaluetax1 , 2, '.', '');
                                        $formartdecodevaluetotal1 = number_format($floatdecodevaluetotal1, 2, '.', '');
                                        if ( ($valuetax1 == 'FALSE') || ($valuetotal1 == 'FALSE') || (!(preg_match($regexnumbers, $decodevaluetax1))) || (!(preg_match($regexnumbers, $decodevaluetotal1))) || ($floatdecodevaluetotal1 == 0) || ($floatdecodevaluetax1 == 0) ) {
                                            $executesecure = FALSE;
                                            $sqldelete1 = "DELETE FROM orders WHERE orders.code = '".$row['code']."' AND orders.code NOT IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                                            try {
                                                $conn->beginTransaction();
                                                $conn->exec($sqldelete1);
                                                $conn->commit();
                                            } catch(PDOException $e) {
                                                $conn->rollback();
                                                error_log("Error: " . $e->getMessage() . "<br><br>");
                                            }
                                            echo("<div class='refreshthepagenowhistory'></div>");
                                        } else {
                                            echo("<tr>");
                                                echo ("<td title='".$row['code']."'>".codifyhtml(strval($row['code']))."</td>");
                                                echo ("<td title='$".$formartdecodevaluetax1."'>".codifyhtml("$".$formartdecodevaluetax1)."</td>");
                                                echo ("<td><div title='$".$formartdecodevaluetotal1."'>".codifyhtml("$".$formartdecodevaluetotal1)."</div><button type='submit' name='viewkey' form='viewfrompurchase' class='extrabt view' value='".$row['code']."'>&#128270;</button></td>");
                                            echo("</tr>");
                                        }
                                    }
                                }
                            } catch(PDOException $e) {
                                error_log($sql . "<br>" . $e->getMessage());
                            }
                            echo("<tr class='last'>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<td></td>");
                                }
                            echo("</tr>");
                        ?>
                    </table>
                </div>
            </div>
            <?php
                if ($viewkey){
                    echo ("<div class='right show'>");
                } else {
                    echo ("<div class='right'>");
                }
            ?>
                <div class="twenty">
                    <table id="tableviewid" class="collapse half">
                        <?php
                            if ($executesecure){
                                $names = ["Code","Tax","Total"];
                                echo("<tr>");
                                    for ($index = 0;$index < count($names);$index++) {
                                        echo ("<th title='".$names[$index]."'>".codifyhtml($names[$index])."</th>");
                                    }
                                echo("</tr>");
                                if ( $viewkey ) {
                                    $sqlord = "SELECT orders.code, orders.value_total, orders.value_tax FROM orders WHERE orders.code = '".$_POST['viewkey']."';";
                                } else {
                                    $sqlord = "SELECT orders.code, orders.value_total, orders.value_tax FROM orders WHERE orders.code IN ( SELECT CASE WHEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) <> ( SELECT MAX( orders2.code ) FROM orders AS orders2 ) THEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) ELSE 0 END AS codeverified );";
                                }
                                try {
                                    $prepord = $conn->prepare($sqlord);
                                    $prepord->execute();
                                    if ($prepord->rowCount() > 0) {
                                        $resultord = $prepord->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($resultord as $row) {
                                            $valuetax2 = safeDecrypt($row['value_tax'], getkey());
                                            $valuetotal2 = safeDecrypt($row['value_total'], getkey());
                                            $decodevaluetax2 = html_entity_decode($valuetax2);
                                            $decodevaluetotal2 = html_entity_decode($valuetotal2);
                                            $floatdecodevaluetax2 = floatval($decodevaluetax2);
                                            $floatdecodevaluetotal2 = floatval($decodevaluetotal2);                                        
                                            $formartdecodevaluetax2 = number_format($floatdecodevaluetax2 , 2, '.', '');
                                            $formartdecodevaluetotal2 = number_format($floatdecodevaluetotal2, 2, '.', '');
                                            if ( ($valuetax2 == 'FALSE') || ($valuetotal2 == 'FALSE') || (!(preg_match($regexnumbers, $decodevaluetax2))) || (!(preg_match($regexnumbers, $decodevaluetotal2))) || ($floatdecodevaluetotal2 == 0) || ($floatdecodevaluetax2 == 0) ) {
                                                $executesecure = FALSE;
                                                $sqldelete2 = "DELETE FROM orders WHERE orders.code = '".$row['code']."' AND orders.code NOT IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                                                try {
                                                    $conn->beginTransaction();
                                                    $conn->exec($sqldelete2);
                                                    $conn->commit();
                                                } catch(PDOException $e) {
                                                    $conn->rollback();
                                                    error_log("Error: " . $e->getMessage() . "<br><br>");
                                                }
                                                echo("<div class='refreshthepagenowhistory'></div>");
                                            } else {
                                                echo("<tr>");
                                                    echo ("<td title='".$row['code']."'>".codifyhtml(strval($row['code']))."</td>");
                                                    echo ("<td title='$".$formartdecodevaluetax2."'>".codifyhtml("$".$formartdecodevaluetax2)."</td>");
                                                    echo ("<td title='$".$formartdecodevaluetotal2."'>".codifyhtml("$".$formartdecodevaluetotal2)."</td>");
                                                echo("</tr>");
                                            }
                                        }
                                    } else {
                                        echo ("<tr><td></td><td></td><td></td></tr>");
                                    }
                                } catch(PDOException $e) {
                                    echo ("<tr><td></td><td></td><td></td></tr>");
                                    error_log($sqlord . "<br>" . $e->getMessage());
                                }
                            } else {
                                echo("<div class='refreshthepagenowhistory'></div>");
                            }
                        ?>
                    </table>
                </div>
                <div class="eighty">
                    <div class="scroll">
                        <table id="tableview" class="collapse lefttext">
                            <?php
                                if ($executesecure){
                                    $names = ["Product","Price","Amount","Total"];
                                    echo("<tr>");
                                        for ($index = 0;$index < count($names);$index++) {
                                            echo ("<th title='".$names[$index]."'>".codifyhtml($names[$index])."</th>");
                                        }
                                    echo("</tr>");
                                    if ( $viewkey ) {
                                        $sqlord = "SELECT order_item.code, order_item.product_name, order_item.amount, order_item.price FROM order_item WHERE order_item.order_code = '".$_POST['viewkey']."' ORDER BY order_item.code;";
                                    } else {
                                        $sqlord = "SELECT order_item.code, order_item.product_name, order_item.amount, order_item.price FROM order_item WHERE order_item.order_code IN ( SELECT CASE WHEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) <> ( SELECT MAX( orders2.code ) FROM orders AS orders2 ) THEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) ELSE 0 END AS codeverified ) ORDER BY order_item.code;";
                                    }
                                    try {
                                        $prepord = $conn->prepare($sqlord);
                                        $prepord->execute();
                                        if ($prepord->rowCount() > 0) {
                                            $resultord = $prepord->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($resultord as $row) {
                                                $product_name = safeDecrypt($row['product_name'], getkey());
                                                $order_amount = safeDecrypt($row['amount'], getkey());
                                                $price = safeDecrypt($row['price'], getkey());
                                                $decodeproduct_name = html_entity_decode($product_name);
                                                $decodeorder_amount = html_entity_decode($order_amount);
                                                $decodeprice = html_entity_decode($price);
                                                $order_amount_int = intval($decodeorder_amount);
                                                $order_price = floatval($decodeprice);
                                                if ( ($product_name == 'FALSE') || (!(preg_match($regexname, $decodeproduct_name))) || ($order_amount == 'FALSE') || ($order_amount_int === 0) || ($price == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeprice))) || ($order_price < 0.01 ) ){
                                                    $executesecure = FALSE;
                                                    $sqldelete3 = "DELETE FROM orders WHERE orders.code IN ( SELECT order_item.order_code FROM order_item WHERE order_item.code = '".$row['code']."' ) AND orders.code NOT IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                                                    try {
                                                        $conn->beginTransaction();
                                                        $conn->exec($sqldelete3);
                                                        $conn->commit();
                                                    } catch(PDOException $e) {
                                                        $conn->rollback();
                                                        error_log("Error: " . $e->getMessage() . "<br><br>");
                                                    }
                                                    echo("<div class='refreshthepagenowhistory'></div>");
                                                }
                                                if ($executesecure){
                                                    $order_price_str = "$".number_format($order_price, 2, '.', '');
                                                    $order_total_price = "$".number_format(floatval($order_price * $order_amount_int), 2, '.', '');
                                                    echo("<tr>");
                                                        echo ("<td title='".$decodeproduct_name."'>".$product_name."</td>");
                                                        echo ("<td title='".$order_price_str."'>".codifyhtml($order_price_str)."</td>");
                                                        echo ("<td title='".$order_amount_int."'>".$order_amount."</td>");
                                                        echo ("<td title='".$order_total_price."'>".codifyhtml($order_total_price)."</td>");
                                                    echo("</tr>");
                                                } else {
                                                    echo("<div class='refreshthepagenowhistory'></div>");
                                                }
                                            }
                                        }
                                    } catch(PDOException $e) {
                                        error_log($sqlord . "<br>" . $e->getMessage());
                                    }
                                    echo("<tr class='last'>");
                                    for ($index = 0;$index < count($names);$index++) {
                                        echo ("<td></td>");
                                    }
                                    echo("</tr>");
                                } else {
                                    echo("<div class='refreshthepagenowhistory'></div>");
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>