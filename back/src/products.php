<?php
    declare(strict_types=1);
    require "connect-localhost.php";
    require_once "safedecrypto.php";
    require_once "checkcode.php";
    $regexname = "/^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$/";
    $regexnumbers = "/^[0-9]{1,}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumberstax = "/^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$/";
    $regexnumbersprice = "/^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$/";
    if ( (!empty($_POST['deletekey'])) ) {
        if (intval($_POST['deletekey']) !== 0){
            if ( checkvaliditycode(strval($_POST['deletekey']),'products') ){
                $statement1 = $conn->query("SELECT name FROM products WHERE code = '".$_POST['deletekey']."';");
                $data = $statement1->fetch();
                $name1 = safeDecrypt($data['name'], getkey());
                $decodename1 = html_entity_decode($name1);
                if ( ($name1 == 'FALSE') || (!(preg_match($regexname, $decodename1))) ){
                    $sqldelete1 = "DELETE FROM public.products WHERE products.code = '".$_POST['deletekey']."';";
                    try {
                        $conn->beginTransaction();
                        $conn->exec($sqldelete1);
                        $conn->commit();
                    } catch(PDOException $e) {
                        $conn->rollback();
                        error_log("Error: " . $e->getMessage() . "<br><br>");
                    }
                    echo("<div class='refreshthepagenowproducts'></div>");
                } else {
                    echo("<form action='deleteproduct.php' method='post' id='confirmfromdeleteproduct'></form>");
                    echo("<div id='alert'><div id='textalert'>Do you really want to delete the product '".$name1."' ?<br>");
                    echo ("</div><button type='submit' name='deleteconfirmedkey' form='confirmfromdeleteproduct' value='".$_POST['deletekey']."' id='yesbutton'>YES</button><button id='nobutton'>NO 10s</button></div>");
                }
            }
        }
    }
    $alterkey = FALSE;
    if ( (!empty($_POST['alterkey'])) ) {
        if (intval($_POST['alterkey']) !== 0){
            if ( checkvaliditycode(strval($_POST['alterkey']),'products') ){
                $alterkey = TRUE;
            }
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
                if ($alterkey){
                    $statement2 = $conn->query("SELECT products.name, products.amount, products.price, products.category_code, categories.name AS category_name FROM products, categories WHERE products.code = '".$_POST['alterkey']."' AND products.category_code = categories.code;");
                    $data1 = $statement2->fetch();
                    $name4 = safeDecrypt($data1['name'], getkey());
                    $amount4 = safeDecrypt($data1['amount'], getkey());
                    $price4 = safeDecrypt($data1['price'], getkey());
                    $category_name4 = safeDecrypt($data1['category_name'], getkey());
                    $decodename4 = html_entity_decode($name4);
                    $decodeamount4 = html_entity_decode($amount4);
                    $decodeprice4 = html_entity_decode($price4);
                    $decodecategory_name4 = html_entity_decode($category_name4);
                    $floatdecodeprice4 = floatval($decodeprice4);
                    $intdecodeamount4 = intval($decodeamount4);
                    if ( ($name4 == 'FALSE') || (!(preg_match($regexname, $decodename4))) || ($amount4 == 'FALSE') || ( (strval($decodeamount4) !== '0') && ($intdecodeamount4 === 0) ) || ($price4 == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeprice4))) || ($floatdecodeprice4 < 0.01 ) || ($category_name4 == 'FALSE') || (!(preg_match($regexname, $decodecategory_name4))) ){
                        $alterkey = FALSE;
                        if ( ($category_name4 == 'FALSE') || (!(preg_match($regexname, $decodecategory_name4))) ) {
                            $sqldelete4 = "DELETE FROM public.categories WHERE categories.code IN ( SELECT category_code FROM products WHERE code = '".$_POST['alterkey']."');";
                        } else {
                            $sqldelete4 = "DELETE FROM public.products WHERE products.code = '".$_POST['alterkey']."';";
                        }
                        try {
                            $conn->beginTransaction();
                            $conn->exec($sqldelete4);
                            $conn->commit();
                        } catch(PDOException $e) {
                            $conn->rollback();
                            error_log("Error: " . $e->getMessage() . "<br><br>");
                        }
                        echo("<div class='refreshthepagenowproducts'></div>");
                    }
                }
                if ($alterkey){
                    $leftdescription = 'View alter Product';
                    $iconleft = 'icon hidden';
                    $iconright = 'icon';
                    $divleft = 'left show';
                    $divright = 'right';
                    $form_name = $decodename4;
                    $form_category = $data1['category_code'];
                    $form_amount = $intdecodeamount4;
                    $form_price = $floatdecodeprice4;
                    $form_category_name = $category_name4;
                    $form_category_name_decoded = $decodecategory_name4;
                    $dropdowntext = 'dropdowntext dropdownselected';
                    $typeofaction = 'Alter Product';
                    $form_action = 'alterproduct.php';
                    $form_id = 'formalterproduct';
                } else {
                    $leftdescription = 'View insert Product';
                    $iconleft = 'icon';
                    $iconright = 'icon hidden';
                    $divleft = 'left';
                    $divright = 'right show';
                    $form_name = '';
                    $form_category = '';
                    $form_amount = '';
                    $form_price = '';
                    $form_category_name = codifyhtml('Category');
                    $form_category_name_decoded = 'Category';
                    $dropdowntext = 'dropdowntext';
                    $typeofaction = 'Add Product';
                    $form_action = 'addproduct.php';
                    $form_id = 'formproduct';
                }
                echo("<div class='textdropleft'><div title='".$leftdescription."'>".$leftdescription."</div><div class='".$iconleft."'>&#9776;</div></div>");
                echo("<div class='textdropright'><div title='View Products'>View Products</div><div class='".$iconright."'>&#9776;</div></div>");
                echo("<div class='".$divleft."'>");
                echo("<form id='".$form_id."' action='".$form_action."' method='post'>");
                if ($alterkey){
                    echo("<input type='hidden' id='alterid' name='alterid' value='".$_POST['alterkey']."'>");
                    echo("<input type='hidden' id='oldname' name='oldname' value='".$decodename4."'>");
                    echo("<input type='hidden' id='oldamount' name='oldamount' value='".$intdecodeamount4."'>");
                    echo("<input type='hidden' id='oldprice' name='oldprice' value='".$floatdecodeprice4."'>");
                    echo("<input type='hidden' id='oldcategory' name='oldcategory' value='".$data1['category_code']."'>");
                }
                echo("<input type='text' id='productname' value = '".$form_name."' name='productname' maxlength='255' title='Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.' pattern='^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$' placeholder='Product name' class='full' required>");
                echo("<input type='hidden' id='categoryidhidden' name='categoryidhidden' value='".$form_category."'>");
                        $sql = "SELECT code, name FROM categories ORDER BY code;";
                        try {
                            $prep = $conn->prepare($sql);
                            $prep->execute();
                            if ($prep->rowCount() > 0) {
                                echo ("<div class='dropdown half'>");
                                    echo ("<div class='dropbtn'>");
                                        echo ("<div class='".$dropdowntext."' title='".$form_category_name_decoded."'>".$form_category_name."</div>");
                                        echo ("<div class='positiondropright'>");
                                            echo ("<div class='arrow adown'></div>");
                                        echo ("</div>");
                                    echo ("</div>");
                                    echo ("<div id='category' class='dropdown-content'>");
                                        $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                                        foreach($result as $row) {
                                            $name2 = safeDecrypt($row['name'], getkey());
                                            $decodename2 = html_entity_decode($name2);
                                            if ( ($name2 == 'FALSE') || (!(preg_match($regexname, $decodename2))) ){
                                                $sqldelete2 = "DELETE FROM public.products WHERE products.code = '".$row['code']."';";
                                                try {
                                                    $conn->beginTransaction();
                                                    $conn->exec($sqldelete2);
                                                    $conn->commit();
                                                } catch(PDOException $e) {
                                                    $conn->rollback();
                                                    error_log("Error: " . $e->getMessage() . "<br><br>");
                                                }
                                                echo("<div class='refreshthepagenowproducts'></div>");
                                            }else{
                                                echo ("<div title='".$decodename2."' data-value='".$row['code']."'>".$name2."</div>");
                                            }
                                        }
                                    echo ("</div>");
                                echo ("</div>");
                            } else if ($prep->rowCount() == 0) {
                                echo ("<div class='dropdown half none'>");
                                    echo ("<div class='dropbtn'>");
                                        echo ("<div class='dropdowntext' title='No categories avaliable'>No categories avaliable</div>");
                                        echo ("<div class='positiondropright'>");
                                            echo ("<div class='arrow adown'></div>");
                                        echo ("</div>");
                                    echo ("</div>");
                                    echo ("<div id='category' class='dropdown-content'></div>");
                                echo ("</div>");
                            }
                        } catch(PDOException $e) {
                            echo ("<div class='dropdown half none'>");
                                echo ("<div class='dropbtn'>");
                                    echo ("<div class='dropdowntext' title='No categories avaliable'>No categories avaliable</div>");
                                    echo ("<div class='positiondropright'>");
                                        echo ("<div class='arrow adown'></div>");
                                    echo ("</div>");
                                echo ("</div>");
                                echo ("<div id='category' class='dropdown-content'></div>");
                            echo ("</div>");
                            error_log($sql . "<br>" . $e->getMessage());
                        }
                        echo ("<input type='number' id='unitprice' value = '".$form_price."' name='unitprice' step='0.01' min='0.01' max='9999999999.99' placeholder='Price' class='quarter' required>");
                        echo ("<input type='number' id='amount' value = '".$form_amount."' name='amount' placeholder='Amount' step='1' min='1' class='quarter' required>");
                        echo ("<input type='submit' value='".$typeofaction."' class='bluebold full'>");
                        echo ("</form>");
                        echo ("</div>");
                        echo("<div class='".$divright."'>");
                    ?>
                <form action="products.php" method="post" id="deleteformproduct"></form>
                <form action="products.php" method="post" id="alterformproduct"></form>
                <div class="scroll">
                    <table id="tableproduct" class="collapse lefttext">
                        <?php
                            $names = ["Code","Product","Amount","Price","Category"];
                            echo("<tr>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<th title='".$names[$index]."'>".codifyhtml($names[$index])."</th>");
                                }
                            echo("</tr>");
                            $sql = "SELECT products.code, products.name, products.amount, products.price, categories.name AS category_name FROM products, categories WHERE products.category_code = categories.code ORDER BY products.code;";
                            try {
                                $prep = $conn->prepare($sql);
                                $prep->execute();
                                if ($prep->rowCount() > 0) {
                                    $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                                    foreach($result as $row) {
                                        $name3 = safeDecrypt($row['name'], getkey());
                                        $amount1 = safeDecrypt($row['amount'], getkey());
                                        $price1 = safeDecrypt($row['price'], getkey());
                                        $category_name1 = safeDecrypt($row['category_name'], getkey());

                                        $decodename3 = html_entity_decode($name3);
                                        $decodeamount1 = html_entity_decode($amount1);
                                        $decodeprice1 = html_entity_decode($price1);
                                        $decodecategory_name1 = html_entity_decode($category_name1);
                                        $floatdecodeprice1 = floatval($decodeprice1);
                                        if ( ($name3 == 'FALSE') || (!(preg_match($regexname, $decodename3))) || ($amount1 == 'FALSE') || ( (strval($decodeamount1) !== '0') && (intval($decodeamount1) === 0) ) || ($price1 == 'FALSE') || (!(preg_match($regexnumbersprice, $decodeprice1))) || ($floatdecodeprice1 < 0.01 ) || ($category_name1 == 'FALSE') || (!(preg_match($regexname, $decodecategory_name1))) ){
                                            if ( ($category_name1 == 'FALSE') || (!(preg_match($regexname, $decodecategory_name1))) ) {
                                                $sqldelete3 = "DELETE FROM public.categories WHERE categories.code IN ( SELECT category_code FROM products WHERE code = '".$row['code']."');";
                                            } else {
                                                $sqldelete3 = "DELETE FROM public.products WHERE products.code = '".$row['code']."';";
                                            }
                                            try {
                                                $conn->beginTransaction();
                                                $conn->exec($sqldelete3);
                                                $conn->commit();
                                            } catch(PDOException $e) {
                                                $conn->rollback();
                                                error_log("Error: " . $e->getMessage() . "<br><br>");
                                            }
                                            echo("<div class='refreshthepagenowproducts'></div>");
                                        }else{
                                            $formatfloatdecodeprice1 = number_format($floatdecodeprice1, 2, '.', '');
                                            echo("<tr>");
                                                echo ("<td><div title='".$row['code']."'>".codifyhtml(strval($row['code']))."</div><button type='submit' name='alterkey' form='alterformproduct' class='extrabt alter' value='".$row['code']."'>&#9997;</button></td>");
                                                echo ("<td title='".$decodename3."'>".$name3."</td>");
                                                echo ("<td title='".$decodeamount1."'>".$amount1."</td>");
                                                echo ("<td title='$".$formatfloatdecodeprice1."'>".codifyhtml("$".$formatfloatdecodeprice1)."</td>");
                                                echo ("<td><div title='".$decodecategory_name1."'>".$category_name1."</div><button type='submit' name='deletekey' form='deleteformproduct' class='extrabt delete' value='".$row['code']."'>&#128465;</button></td>");
                                            echo("</tr>");
                                        }
                                    }
                                }
                            } catch(PDOException $e) {
                                error_log($sql . "<br>" . $e->getMessage());
                            }
                            $conn = null;
                            echo("<tr class='last'>");
                                for ($index = 0;$index < count($names);$index++) {
                                    echo ("<td></td>");
                                }
                            echo("</tr>");
                        ?>
                    </table>
                </div>
            </div>
        </div>
    </body>
</html>