<?php
    declare(strict_types=1);
    require "connect-localhost.php";
    require_once "safedecrypto.php";
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    if ( (!empty($_POST['changeidhidden'])) ) {
        if (intval($_POST['changeidhidden']) !== 0){
            if ( checkvaliditycode(strval($_POST['changeidhidden']),'products') ){
                $changeidhidden = TRUE;
            } else {
                $changeidhidden = FALSE;
            }
        } else {
            $changeidhidden = FALSE;
        }
    } else {
        $changeidhidden = FALSE;
    }
    if ( (!empty($_POST['cancel'])) ) {
        echo ("<form action='deletecart.php' method='post' id='confirmfromdeletecart'></form>");
        echo ("<div id='alert'><div id='textalert'>Do you really want to empty your cart ?<br>");
        echo ("</div><button type='submit' name='deleteconfirmedkey' form='confirmfromdeletecart' value='YES' id='yesbutton'>YES</button><button id='nobutton'>NO 10s</button></div>");
    } else if ( (!empty($_POST['finish'])) ) {
        echo ("<form action='finish.php' method='post' id='confirmfrompurchase'></form>");
        echo ("<div id='alert'><div id='textalert' class='alertremovefortable' >");
        $sqlorder = "SELECT orders.code, orders.value_total, orders.value_tax FROM orders WHERE orders.code IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
        $executesecure = FALSE;
        try {
            $preporder = $conn->prepare($sqlorder);
            $preporder->execute();
            $resultorder = $preporder->fetch(PDO::FETCH_ASSOC);
            if ( checkvaliditycode(strval($resultorder['code']),'orders') ){
                $tax_total1 = safeDecrypt($resultorder['value_tax'], getkey());
                $value_total1 = safeDecrypt($resultorder['value_total'], getkey());
                $tax_total_orders = html_entity_decode($tax_total1);
                $value_total_orders = html_entity_decode($value_total1);
                if ( ($tax_total1 == 'FALSE') || ($value_total1 == 'FALSE') || (!(preg_match($regexnumbers, $tax_total_orders))) || (!(preg_match($regexnumbers, $value_total_orders))) ){
                    $executesecure = FALSE;
                    $sqldelete1 = "DELETE FROM orders WHERE orders.code = '".$resultorder['code']."' AND orders.code NOT IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqldelete1);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                    echo("<div class='refreshthepagenowindex'></div>");
                } else {
                    $executesecure = TRUE; 
                }
            }
        } catch(PDOException $e) {
            error_log($sqlorder . "<br>" . $e->getMessage());
        }
        if ($executesecure){
            echo ("<div id='dividetext'>");
            echo ("The total price value of the products is <span id='cart_total_price'></span>, the tax of the purchase is ".codifyhtml("$".number_format(floatval($tax_total_orders), 2, '.', '')).", totalizing ".codifyhtml("$".number_format(floatval($value_total_orders), 2, '.', '')).", do you want to confirm the purchase ?<br>Down bellow is the list of all products being purchased : ");
            echo ("</div>");
            echo ("<div id='dividetable'>");
            echo ("<table>");
            $names = ["Product","Price","Amount","Total"];
            echo("<tr>");
                for ($index = 0;$index < count($names);$index++) {
                    echo ("<th title='".$names[$index]."'>".codifyhtml($names[$index])."</th>");
                }
            echo("</tr>");
            $sql = "SELECT CASE WHEN ( SELECT order_item1.product_code FROM order_item AS order_item1 WHERE order_item1.code = order_item.code ) IS NOT NULL THEN ( SELECT products1.name FROM products AS products1 WHERE products1.code = order_item.product_code ) ELSE 'False' END AS products_name, CASE WHEN ( SELECT order_item2.product_code FROM order_item AS order_item2 WHERE order_item2.code = order_item.code ) IS NOT NULL THEN ( SELECT products2.amount FROM products AS products2 WHERE products2.code = order_item.product_code ) ELSE 'False' END AS products_amount, CASE WHEN ( SELECT order_item3.product_code FROM order_item AS order_item3 WHERE order_item3.code = order_item.code ) IS NOT NULL THEN ( SELECT products3.price FROM products AS products3 WHERE products3.code = order_item.product_code ) ELSE 'False' END AS products_price, CASE WHEN ( SELECT order_item4.product_code FROM order_item AS order_item4 WHERE order_item4.code = order_item.code ) IS NOT NULL THEN ( SELECT categories1.tax FROM products AS products4, categories AS categories1 WHERE products4.code = order_item.product_code AND categories1.code = products4.category_code ) ELSE 'False' END AS categories_tax, order_item.code AS order_item_code, order_item.product_name AS order_product_name, order_item.amount AS order_amount, order_item.price AS order_price, order_item.tax AS order_tax FROM order_item WHERE order_item.order_code IN ( SELECT MAX( orders.code ) FROM orders ) ORDER BY order_item.code;";
            $value_total_cart = 0;
            $tax_total_cart = 0;
            $cart_total_price = 0;
            $rederror = false;
            $executesecure = TRUE;
            try {
                $prep = $conn->prepare($sql);
                $prep->execute();
                if ($prep->rowCount() > 0) {
                    $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                    foreach($result as $row) {
                        if ($row['products_name'] != 'False') {
                            $products_name = safeDecrypt($row['products_name'], getkey());
                            $products_amount = safeDecrypt($row['products_amount'], getkey());
                            $products_price = safeDecrypt($row['products_price'], getkey());
                            $categories_tax = safeDecrypt($row['categories_tax'], getkey());
                            $decodeproducts_name = html_entity_decode($products_name);
                            $decodeproducts_amount = html_entity_decode($products_amount);
                            $decodeproducts_price = html_entity_decode($products_price);
                            $decodecategories_tax = html_entity_decode($categories_tax);
                            $products_amount_int = intval($decodeproducts_amount);
                            $floatdecodeproducts_price = floatval($decodeproducts_price);
                            if ( ($products_name == 'FALSE') || (!(preg_match($regexname, $decodeproducts_name))) || ($products_amount == 'FALSE') || ( (strval($decodeproducts_amount) !== '0') && ($products_amount_int === 0) ) || ($products_price == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeproducts_price))) || ($floatdecodeproducts_price < 0.01 ) || ($categories_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodecategories_tax))) ){
                                $executesecure = FALSE;
                                if ( ($categories_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodecategories_tax))) ) {
                                    $sqldelete2 = "DELETE FROM categories WHERE categories.code IN (SELECT products.category_code FROM products WHERE products.code IN (SELECT order_item.product_code FROM order_item WHERE order_item.code = '".$row['order_item_code']."')));";
                                } else {
                                    $sqldelete2 = "DELETE FROM products WHERE products.code IN (SELECT order_item.product_code FROM order_item WHERE order_item.code = '".$row['order_item_code']."');";
                                }
                                try {
                                    $conn->beginTransaction();
                                    $conn->exec($sqldelete2);
                                    $conn->commit();
                                } catch(PDOException $e) {
                                    $conn->rollback();
                                    error_log("Error: " . $e->getMessage() . "<br><br>");
                                }
                                echo("<div class='refreshthepagenowindex'></div>");
                            }
                        }
                        $order_product_name = safeDecrypt($row['order_product_name'], getkey());
                        $order_amount = safeDecrypt($row['order_amount'], getkey());
                        $order_price = safeDecrypt($row['order_price'], getkey());
                        $order_tax = safeDecrypt($row['order_tax'], getkey());
                        $decodeorder_product_name = html_entity_decode($order_product_name);
                        $decodeorder_amount = html_entity_decode($order_amount);
                        $decodeorder_price = html_entity_decode($order_price);
                        $decodeorder_tax = html_entity_decode($order_tax);
                        $order_amount_int = intval($decodeorder_amount);
                        $order_price_float = floatval($decodeorder_price);
                        $order_tax_float = floatval($decodeorder_tax);
                        if ( ($order_product_name == 'FALSE') || (!(preg_match($regexname, $decodeorder_product_name))) || ($order_amount == 'FALSE') || ($order_amount_int === 0) || ($order_price == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeorder_price))) || ($order_price_float < 0.01 ) || ($order_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodeorder_tax))) ){
                            $executesecure = FALSE;
                            $sqldelete3 = "DELETE FROM order_item WHERE order_item.code = '".$row['order_item_code']."';";
                            try {
                                $conn->beginTransaction();
                                $conn->exec($sqldelete3);
                                $conn->commit();
                            } catch(PDOException $e) {
                                $conn->rollback();
                                error_log("Error: " . $e->getMessage() . "<br><br>");
                            }
                            echo("<div class='refreshthepagenowindex'></div>");
                        }
                        if ($executesecure){
                            $order_total_price = number_format(round($order_amount_int * $order_price_float,2), 2, '.', '');
                            $newprice = $order_price_float * (1 + ($order_tax_float/100));
                            $newtax = $order_price_float * ($order_tax_float/100);
                            $totalprice = $newprice * $order_amount_int;
                            $totaltax = $newtax * $order_amount_int;
                            $value_total_cart += $totalprice;
                            $tax_total_cart += $totaltax;
                            $cart_total_price += floatval($order_total_price);
                            $format_order_price_float = number_format($order_price_float, 2, '.', '');
                            if ($row['products_name'] != 'False') {                                 
                                if ( ( $products_name == $order_product_name ) && ( $products_amount_int >= $order_amount_int ) && ( $products_price == $order_price ) && ( $categories_tax == $order_tax ) ) {
                                    echo("<tr>");
                                } else {
                                    echo("<tr class='rederror'>");
                                    $rederror = true;
                                }
                            } else {
                                echo("<tr class='rederror'>");
                                $rederror = true;
                            }
                                    echo ("<td title='".$decodeorder_product_name."'>".$order_product_name."</td>");
                                    echo ("<td title='$".$format_order_price_float."'>".codifyhtml("$".$format_order_price_float)."</td>");
                                    echo ("<td title='".$order_amount_int."'>".$order_amount."</td>");
                                    echo ("<td title='$".$order_total_price."'>".codifyhtml("$".$order_total_price)."</td>");
                               echo("</tr>");
                        } else {
                            echo("<div class='refreshthepagenowindex'></div>");
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
            echo("</table>");
            echo ("</div>");
            if ( ( round(floatval($value_total_orders),2) != round(floatval($value_total_cart),2) ) && ( round(floatval($tax_total_orders),2) != round(floatval($tax_total_cart),2) ) ){
                echo("<div class='rederror'></div>");
                echo("<div class='refreshthepagenowindex'></div>");
                $rederror = true;
            }
            if ($rederror){
                echo ("</div><button id='yesbutton'>YES</button><button id='nobutton'>NO</button></div>");
            } else {
                echo ("</div><button type='submit' name='purchaseconfirmedkey' form='confirmfrompurchase' value='$".number_format(floatval($cart_total_price), 2, '.', '')."' id='yesbutton'>YES</button><button id='nobutton'>NO</button></div>");
            }
        } else {
            echo("<div class='refreshthepagenowindex'></div>");
        }
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
                if ($changeidhidden){
                    echo ("<div class='textdropleft'><div title='View Purchase'>View Purchase</div><div class='icon hidden'>&#9776;</div></div>");
                    echo ("<div class='textdropright'><div title='View Cart'>View Cart</div><div class='icon'>&#9776;</div></div>");
                    echo ("<div class='left show'>");
                } else {
                    echo ("<div class='textdropleft'><div title='View Purchase'>View Purchase</div><div class='icon'>&#9776;</div></div>");
                    echo ("<div class='textdropright'><div title='View Cart'>View Cart</div><div class='icon hidden'>&#9776;</div></div>");
                    echo ("<div class='left'>");
                }
            ?>
                <form action='index.php' method='post' id='formchange'>
                    <input type='hidden' id='changeidhidden' name='changeidhidden' value='null'>
                </form>
                <form action='addcart.php' method='post' id='formcart'>                    
                    <?php
                        $executesecure = TRUE;
                        if ( $changeidhidden ) {                            
                            echo("<input type='hidden' id='productidhidden' name='productidhidden' value='".$_POST['changeidhidden']."'>");
                            $sqlid = "SELECT products.name, products.amount AS product_amount, products.price, categories.tax, CASE WHEN EXISTS ( SELECT order_item1.amount FROM order_item AS order_item1 WHERE order_item1.product_code = products.code AND order_item1.order_code  IN (SELECT MAX(orders1.code) FROM orders AS orders1) ) THEN (SELECT order_item2.amount FROM order_item AS order_item2 WHERE order_item2.product_code = products.code AND order_item2.order_code IN (SELECT MAX(orders2.code) FROM orders AS orders2) ) ELSE '0' END AS cart_amount FROM products, categories WHERE products.code = '".$_POST['changeidhidden']."' AND products.category_code = categories.code;";
                            $prepid = $conn->prepare($sqlid);
                            $prepid->execute();
                            $resultid = $prepid->fetch(PDO::FETCH_ASSOC);
                            $product_amount1 = safeDecrypt($resultid['product_amount'], getkey());
                            $price1 = safeDecrypt($resultid['price'], getkey());
                            $tax1 = safeDecrypt($resultid['tax'], getkey());
                            $name1 = safeDecrypt($resultid['name'], getkey());
                            $decodeproduct_amount1 = html_entity_decode($product_amount1);
                            $decodeprice1 = html_entity_decode($price1);
                            $decodetax1 = html_entity_decode($tax1);
                            $decodename1 = html_entity_decode($name1);
                            $intdecodeproduct_amount1 = intval($decodeproduct_amount1);
                            $oldprice = floatval($decodeprice1);
                            $oldtax = floatval($decodetax1);
                            if ( ($name1 == 'FALSE') || (!(preg_match($regexname, $decodename1))) || ($product_amount1 == 'FALSE') || ( (strval($decodeproduct_amount1) !== '0') && ($intdecodeproduct_amount1 === 0) ) || ($price1 == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeprice1))) || ($oldprice < 0.01 ) || ($tax1 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax1))) ){
                                $executesecure = FALSE;
                                if ( ($tax1 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax1))) ) {
                                    $sqldelete4 = "DELETE FROM categories WHERE categories.code IN (SELECT products.category_code FROM products WHERE products.code = '".$_POST['changeidhidden']."');";
                                } else {
                                    $sqldelete4 = "DELETE FROM products WHERE products.code = '".$_POST['changeidhidden']."';";
                                }
                                try {
                                    $conn->beginTransaction();
                                    $conn->exec($sqldelete4);
                                    $conn->commit();
                                } catch(PDOException $e) {
                                    $conn->rollback();
                                    error_log("Error: " . $e->getMessage() . "<br><br>");
                                }
                                echo("<div class='refreshthepagenowindex'></div>");
                            }
                            if ($executesecure){
                                if ( $resultid['cart_amount'] == "0"){
                                    $amountleft = $intdecodeproduct_amount1;
                                } else {
                                    $cart_amount1 = safeDecrypt($resultid['cart_amount'], getkey());
                                    $decodecart_amount1 = html_entity_decode($cart_amount1);
                                    $intdecodecart_amount1 = intval($decodecart_amount1);
                                    if ( ($cart_amount1 == 'FALSE') || ($intdecodecart_amount1 === 0) ) {
                                        $executesecure = FALSE;
                                        $sqldelete5 = "DELETE FROM order_item WHERE order_item.product_code = '".$_POST['changeidhidden']."' AND order_item.order_code IN (SELECT MAX(orders.code) FROM orders);";
                                        try {
                                            $conn->beginTransaction();
                                            $conn->exec($sqldelete5);
                                            $conn->commit();
                                        } catch(PDOException $e) {
                                            $conn->rollback();
                                            error_log("Error: " . $e->getMessage() . "<br><br>");
                                        }
                                        echo("<div class='refreshthepagenowindex'></div>");
                                    } else {
                                        $amountleft = $intdecodeproduct_amount1 - $intdecodecart_amount1;
                                    }
                                }
                                if ($executesecure){
                                    $newprice = $oldprice * (1 + ($oldtax/100));
                                    $newtax = $oldprice * ($oldtax/100);
                                    $newprice = round($newprice, 2);
                                    $newtax = round($newtax, 2);
                                } else {
                                    echo("<div class='refreshthepagenowindex'></div>");
                                }
                            } else {
                                echo("<div class='refreshthepagenowindex'></div>");
                            }
                        }
                        if ($executesecure){
                            $sql = "SELECT code, name FROM products ORDER BY code;";
                            try {
                                $prep = $conn->prepare($sql);
                                $prep->execute();
                                if ($prep->rowCount() > 0) {
                                    echo ("<div class='dropdown full'>");
                                        echo ("<div class='dropbtn'>");
                                            if ( $changeidhidden ) {
                                                echo ("<div class='dropdowntext dropdownselected' title='".$decodename1."'>".$name1."</div>");
                                            } else {
                                                echo ("<div class='dropdowntext' title='Product'>Product</div>");
                                            }
                                            echo ("<div class='positiondropright'>");
                                                echo ("<div class='arrow adown'></div>");
                                            echo ("</div>");
                                        echo ("</div>");
                                        echo ("<div id='product' class='dropdown-content'>");
                                            $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                                            foreach($result as $row) {
                                                $name2 = safeDecrypt($row['name'], getkey());
                                                $decodename2 = html_entity_decode($name2);
                                                if ( ($name2 == 'FALSE') || (!(preg_match($regexname, $decodename2))) ) {
                                                    $executesecure = FALSE;
                                                    $sqldelete6 = "DELETE FROM products WHERE products.code = '".$row['code']."';";
                                                    try {
                                                        $conn->beginTransaction();
                                                        $conn->exec($sqldelete6);
                                                        $conn->commit();
                                                    } catch(PDOException $e) {
                                                        $conn->rollback();
                                                        error_log("Error: " . $e->getMessage() . "<br><br>");
                                                    }
                                                    echo("<div class='refreshthepagenowindex'></div>");
                                                }else{
                                                    echo ("<div title='".$decodename2."' data-value='".$row['code']."'>".$name2."</div>");
                                                }
                                            }
                                        echo ("</div>");
                                    echo ("</div>");
                                } else if ($prep->rowCount() == 0) {
                                    echo ("<div class='dropdown full none'>");
                                        echo ("<div class='dropbtn'>");
                                            echo ("<div class='dropdowntext' title='No product avaliable at the moment'>No product avaliable at the moment</div>");
                                            echo ("<div class='positiondropright'>");
                                                echo ("<div class='arrow adown'></div>");
                                            echo ("</div>");
                                        echo ("</div>");
                                        echo ("<div id='product' class='dropdown-content'></div>");
                                    echo ("</div>");
                                }
                            } catch(PDOException $e) {
                                echo ("<div class='dropdown full none'>");
                                    echo ("<div class='dropbtn'>");
                                        echo ("<div class='dropdowntext' title='No product avaliable at the moment'>No product avaliable at the moment</div>");
                                        echo ("<div class='positiondropright'>");
                                            echo ("<div class='arrow adown'></div>");
                                        echo ("</div>");
                                    echo ("</div>");
                                    echo ("<div id='product' class='dropdown-content'></div>");
                                echo ("</div>");
                                error_log($sql . "<br>" . $e->getMessage());

                            }
                            if ($executesecure){
                                if ( $changeidhidden ) {
                                    if ($amountleft > 0){
                                        echo("<input type='number' id='amount' name='amount' placeholder='Amount' min='1' max='".$amountleft."' class='half' required>");
                                    } else {
                                        echo("<input type='number' id='amount' name='amount' placeholder='No stock left' class='half' disabled>");
                                    }
                                    echo("<input type='text' id='taxvalue' name='taxvalue' title='".$oldtax."%' value='".$oldtax."%' class='quarter' disabled>");
                                    echo("<input type='text' id='unitprice' name='unitprice' title='$".number_format($oldprice, 2, '.', '')."' value='$".number_format($oldprice, 2, '.', '')."' class='quarter' disabled>");
                                }else{
                                    echo("<input type='number' id='amount' name='amount' placeholder='Amount' min='1' max='1' class='half' disabled required>");
                                    echo("<input type='text' id='taxvalue' name='taxvalue' placeholder='Tax' class='quarter' disabled>");
                                    echo("<input type='text' id='unitprice' value='' name='unitprice' placeholder='Unit price' class='quarter' disabled>");
                                }
                            } else {
                                echo("<div class='refreshthepagenowindex'></div>");
                            }
                        } else {
                            echo("<div class='refreshthepagenowindex'></div>");
                        }
                    ?>
                    <input type="submit" value="Add Product" class="bluebold full">
                </form> 
            </div>
            <?php
                if ($changeidhidden){
                    echo ("<div class='right'>");
                } else {
                    echo ("<div class='right show'>");
                }
            ?>
                <form action="deletecartitem.php" method="post" id="deleteformcart"></form>
                <div class="eighty">
                    <div class="scroll">
                        <table id="tablecart" class="collapse lefttext">
                        <?php
                            $names = ["Product","Price","Amount","Total"];
                            echo("<tr>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<th title='".$names[$index]."'>".codifyhtml($names[$index])."</th>");
                                }
                            echo("</tr>");
                            $sql = "SELECT CASE WHEN ( SELECT order_item1.product_code FROM order_item AS order_item1 WHERE order_item1.code = order_item.code ) IS NOT NULL THEN ( SELECT products1.name FROM products AS products1 WHERE products1.code = order_item.product_code ) ELSE 'False' END AS products_name, CASE WHEN ( SELECT order_item2.product_code FROM order_item AS order_item2 WHERE order_item2.code = order_item.code ) IS NOT NULL THEN ( SELECT products2.amount FROM products AS products2 WHERE products2.code = order_item.product_code ) ELSE 'False' END AS products_amount, CASE WHEN ( SELECT order_item3.product_code FROM order_item AS order_item3 WHERE order_item3.code = order_item.code ) IS NOT NULL THEN ( SELECT products3.price FROM products AS products3 WHERE products3.code = order_item.product_code ) ELSE 'False' END AS products_price, CASE WHEN ( SELECT order_item4.product_code FROM order_item AS order_item4 WHERE order_item4.code = order_item.code ) IS NOT NULL THEN ( SELECT categories1.tax FROM products AS products4, categories AS categories1 WHERE products4.code = order_item.product_code AND categories1.code = products4.category_code ) ELSE 'False' END AS categories_tax, order_item.code AS order_item_code, order_item.product_name AS order_product_name, order_item.amount AS order_amount, order_item.price AS order_price, order_item.tax AS order_tax FROM order_item WHERE order_item.order_code IN ( SELECT MAX( orders.code ) FROM orders )  ORDER BY order_item.code;";
                            $value_total_cart = 0;
                            $tax_total_cart = 0;
                            $cart_total_price = 0;
                            $rederror = false;
                            $executesecure = TRUE;
                            try {
                                $prep = $conn->prepare($sql);
                                $prep->execute();
                                if ($prep->rowCount() > 0) {
                                    $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($result as $row) {
                                        if ($row['products_name'] != 'False') {
                                            $products_name = safeDecrypt($row['products_name'], getkey());
                                            $products_amount = safeDecrypt($row['products_amount'], getkey());
                                            $products_price = safeDecrypt($row['products_price'], getkey());
                                            $categories_tax = safeDecrypt($row['categories_tax'], getkey());
                                            $decodeproducts_name = html_entity_decode($products_name);
                                            $decodeproducts_amount = html_entity_decode($products_amount);
                                            $decodeproducts_price = html_entity_decode($products_price);
                                            $decodecategories_tax = html_entity_decode($categories_tax);
                                            $products_amount_int = intval($decodeproducts_amount);
                                            $floatdecodeproducts_price = floatval($decodeproducts_price);
                                            if ( ($products_name == 'FALSE') || (!(preg_match($regexname, $decodeproducts_name))) || ($products_amount == 'FALSE') || ( (strval($decodeproducts_amount) !== '0') && ($products_amount_int === 0) ) || ($products_price == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeproducts_price))) || ($floatdecodeproducts_price < 0.01 ) || ($categories_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodecategories_tax))) ){
                                                $executesecure = FALSE;
                                                if ( ($categories_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodecategories_tax))) ) {
                                                    $sqldelete7 = "DELETE FROM categories WHERE categories.code IN (SELECT products.category_code FROM products WHERE products.code IN (SELECT order_item.product_code FROM order_item WHERE order_item.code = '".$row['order_item_code']."')));";
                                                } else {
                                                    $sqldelete7 = "DELETE FROM products WHERE products.code IN (SELECT order_item.product_code FROM order_item WHERE order_item.code = '".$row['order_item_code']."');";
                                                }
                                                try {
                                                    $conn->beginTransaction();
                                                    $conn->exec($sqldelete7);
                                                    $conn->commit();
                                                } catch(PDOException $e) {
                                                    $conn->rollback();
                                                    error_log("Error: " . $e->getMessage() . "<br><br>");
                                                }
                                                echo("<div class='refreshthepagenowindex'></div>");
                                            }
                                        }
                                        $order_product_name = safeDecrypt($row['order_product_name'], getkey());
                                        $order_amount = safeDecrypt($row['order_amount'], getkey());
                                        $order_price = safeDecrypt($row['order_price'], getkey());
                                        $order_tax = safeDecrypt($row['order_tax'], getkey());
                                        $decodeorder_product_name = html_entity_decode($order_product_name);
                                        $decodeorder_amount = html_entity_decode($order_amount);
                                        $decodeorder_price = html_entity_decode($order_price);
                                        $decodeorder_tax = html_entity_decode($order_tax);
                                        $order_amount_int = intval($decodeorder_amount);
                                        $order_price_float = floatval($decodeorder_price);
                                        $order_tax_float = floatval($decodeorder_tax);
                                        if ( ($order_product_name == 'FALSE') || (!(preg_match($regexname, $decodeorder_product_name))) || ($order_amount == 'FALSE') || ($order_amount_int === 0) || ($order_price == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeorder_price))) || ($order_price_float < 0.01 ) || ($order_tax == 'FALSE') || (!(preg_match($regexnumberstax, $decodeorder_tax))) ){
                                            $executesecure = FALSE;
                                            $sqldelete3 = "DELETE FROM order_item WHERE order_item.code = '".$row['order_item_code']."';";
                                            try {
                                                $conn->beginTransaction();
                                                $conn->exec($sqldelete3);
                                                $conn->commit();
                                            } catch(PDOException $e) {
                                                $conn->rollback();
                                                error_log("Error: " . $e->getMessage() . "<br><br>");
                                            }
                                            echo("<div class='refreshthepagenowindex'></div>");
                                        }
                                        if ($executesecure){
                                            $order_total_price = number_format(round($order_amount_int * $order_price_float,2), 2, '.', '');
                                            $newprice = $order_price_float * (1 + ($order_tax_float/100));
                                            $newtax = $order_price_float * ($order_tax_float/100);
                                            $totalprice = $newprice * $order_amount_int;
                                            $totaltax = $newtax * $order_amount_int;
                                            $value_total_cart += $totalprice;
                                            $tax_total_cart += $totaltax;
                                            $cart_total_price += floatval($order_total_price);
                                            $format_order_price_float = number_format($order_price_float, 2, '.', '');
                                            if ($row['products_name'] != 'False') {
                                                if ( ( $products_name == $order_product_name ) && ( $products_amount_int >= $order_amount_int ) && ( $products_price == $order_price ) && ( $categories_tax == $order_tax ) ) {
                                                    echo("<tr>");
                                                } else {
                                                    echo("<tr class='rederror'>");
                                                    $rederror = true;
                                                }
                                            } else {
                                                echo("<tr class='rederror'>");
                                                $rederror = true;
                                            }
                                                echo ("<td title='".$decodeorder_product_name."'>".$order_product_name."</td>");
                                                echo ("<td title='$".$format_order_price_float."'>".codifyhtml("$".$format_order_price_float)."</td>");
                                                echo ("<td title='".$order_amount_int."'>".$order_amount."</td>");
                                                echo ("<td><div title='$".$order_total_price."'>".codifyhtml("$".$order_total_price)."</div><button type='submit' name='deletekey' form='deleteformcart' class='extrabt delete' value='".$row['order_item_code']."'>&#128465;</button></td>");
                                            echo("</tr>");
                                        } else {
                                            echo("<div class='refreshthepagenowindex'></div>");
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
                <div class="ten">
                    <table class="lefttext floatright">
                        <?php
                            if ($executesecure){
                                $sqlorder = "SELECT CASE WHEN EXISTS ( SELECT orders1.value_total FROM orders AS orders1 WHERE orders1.code IN (SELECT MAX(orders2.code) FROM orders AS orders2) ) THEN ( SELECT orders3.value_total FROM orders AS orders3 WHERE orders3.code IN (SELECT MAX(orders4.code) FROM orders AS orders4) ) ELSE '0' END AS value_total, CASE WHEN EXISTS ( SELECT orders9.code FROM orders AS orders9 WHERE orders9.code IN (SELECT MAX(orders10.code) FROM orders AS orders10) ) THEN ( SELECT orders11.code FROM orders AS orders11 WHERE orders11.code IN (SELECT MAX(orders12.code) FROM orders AS orders12) ) ELSE '0' END AS code, CASE WHEN EXISTS ( SELECT orders5.value_tax FROM orders AS orders5 WHERE orders5.code IN (SELECT MAX(orders6.code) FROM orders AS orders6) ) THEN ( SELECT orders7.value_tax FROM orders AS orders7 WHERE orders7.code IN (SELECT MAX(orders8.code) FROM orders AS orders8) ) ELSE '0' END AS value_tax;";
                                $tax_total_orders = 0;
                                $value_total_orders = 0;
                                try {
                                    $preporder = $conn->prepare($sqlorder);
                                    $preporder->execute();
                                    $resultorder = $preporder->fetch(PDO::FETCH_ASSOC);
                                    if ($resultorder['value_tax'] !== '0'){
                                        $tax2 = safeDecrypt($resultorder['value_tax'], getkey());
                                        $total2 = safeDecrypt($resultorder['value_total'], getkey());
                                        $tax_total_orders = html_entity_decode($tax2);
                                        $value_total_orders = html_entity_decode($total2);
                                        if ( ($tax2 == 'FALSE') || ($total2 == 'FALSE') || (!(preg_match($regexnumbers, $tax_total_orders))) || (!(preg_match($regexnumbers, $value_total_orders))) ){
                                            $executesecure = FALSE;
                                            $sqldelete9 = "DELETE FROM orders WHERE orders.code = '".$resultorder['code']."' AND orders.code NOT IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                                            try {
                                                $conn->beginTransaction();
                                                $conn->exec($sqldelete9);
                                                $conn->commit();
                                            } catch(PDOException $e) {
                                                $conn->rollback();
                                                error_log("Error: " . $e->getMessage() . "<br><br>");
                                            }
                                            echo("<div class='refreshthepagenowindex'></div>");
                                        }
                                    }
                                } catch(PDOException $e) {
                                    error_log($sqlorder . "<br>" . $e->getMessage());
                                }
                                if ( ( round(floatval($value_total_orders),2) != round(floatval($value_total_cart),2) ) && ( round(floatval($tax_total_orders),2) != round(floatval($tax_total_cart),2) ) ){
                                    $sqlupdord = "UPDATE orders SET value_total='".safeEncrypt(codifyhtml(strval(round($value_total_cart,2))), getkey())."', value_tax='".safeEncrypt(codifyhtml(strval(round($tax_total_cart,2))), getkey())."' WHERE orders.code IN (SELECT MAX(orders1.code) FROM orders AS orders1);";
                                    try {
                                        $conn->beginTransaction();
                                        $conn->exec($sqlupdord);
                                        $conn->commit();
                                    } catch(PDOException $e) {
                                        $conn->rollback();
                                        error_log("Error: " . $e->getMessage() . "<br><br>");
                                    }
                                }
                                echo("<tr>");
                                    echo("<th>Tax:</th>");
                                    if ($rederror){
                                        echo("<td class=redtext id='taxtotal'>".codifyhtml("$".number_format($tax_total_cart, 2, '.', ''))."</td>");
                                    } else {
                                        echo("<td id='taxtotal'>".codifyhtml("$".number_format($tax_total_cart, 2, '.', ''))."</td>");
                                    }
                                echo("</tr>");
                                echo("<tr>");
                                    echo("<th>Total:</th>");
                                    if ($rederror){
                                        echo("<td class=redtext id='valuetotalcart'>".codifyhtml("$".number_format($value_total_cart, 2, '.', ''))."</td>");
                                    } else {
                                        echo("<td id='valuetotalcart'>".codifyhtml("$".number_format($value_total_cart, 2, '.', ''))."</td>");
                                    }
                                echo("</tr>");
                                $conn = null;
                            } else {
                                echo("<div class='refreshthepagenowindex'></div>");
                            }
                        ?>   
                    </table>
                </div>
                <div class="ten">
                    <form action="index.php" method="post" id="formfinisher">
                        <input type="submit" value="Finish" name="finish" id="finish" class="bluebold quarter">
                        <input type="submit" value="Cancel" name="cancel" id="cancel" class="cancel quarter">
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>