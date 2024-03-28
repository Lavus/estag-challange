// import styles from './.module.css'

function DropDown({ type, name, id, placeholder, onChange, value, className, maxLength, title, pattern, required, step, min, max }) {
    return (
        <div/>
    )
}

export default DropDown

// $sql = "SELECT code, name FROM categories ORDER BY code;";
// try {
//     $prep = $conn->prepare($sql);
//     $prep->execute();
//     if ($prep->rowCount() > 0) {
//         echo ("<div class='dropdown half'>");
//             echo ("<div class='dropbtn'>");
//                 echo ("<div class='".$dropdowntext."' title='".$form_category_name_decoded."'>".$form_category_name."</div>");
//                 echo ("<div class='positiondropright'>");
//                     echo ("<div class='arrow adown'></div>");
//                 echo ("</div>");
//             echo ("</div>");
//             echo ("<div id='category' class='dropdown-content'>");
//                 $result = $prep->fetchAll(PDO::FETCH_ASSOC);
//                 foreach($result as $row) {
//                     $name2 = safeDecrypt($row['name'], getkey());
//                     $decodename2 = html_entity_decode($name2);
//                     if ( ($name2 == 'FALSE') || (!(preg_match($regexname, $decodename2))) ){
//                         $sqldelete2 = "DELETE FROM products WHERE products.code = '".$row['code']."';";
//                         try {
//                             $conn->beginTransaction();
//                             $conn->exec($sqldelete2);
//                             $conn->commit();
//                         } catch(PDOException $e) {
//                             $conn->rollback();
//                             error_log("Error: " . $e->getMessage() . "<br><br>");
//                         }
//                         echo("<div class='refreshthepagenowproducts'></div>");
//                     }else{
//                         echo ("<div title='".$decodename2."' data-value='".$row['code']."'>".$name2."</div>");
//                     }
//                 }
//             echo ("</div>");
//         echo ("</div>");
//     } else if ($prep->rowCount() == 0) {
//         echo ("<div class='dropdown half none'>");
//             echo ("<div class='dropbtn'>");
//                 echo ("<div class='dropdowntext' title='No categories avaliable'>No categories avaliable</div>");
//                 echo ("<div class='positiondropright'>");
//                     echo ("<div class='arrow adown'></div>");
//                 echo ("</div>");
//             echo ("</div>");
//             echo ("<div id='category' class='dropdown-content'></div>");
//         echo ("</div>");
//     }
// } catch(PDOException $e) {
//     echo ("<div class='dropdown half none'>");
//         echo ("<div class='dropbtn'>");
//             echo ("<div class='dropdowntext' title='No categories avaliable'>No categories avaliable</div>");
//             echo ("<div class='positiondropright'>");
//                 echo ("<div class='arrow adown'></div>");
//             echo ("</div>");
//         echo ("</div>");
//         echo ("<div id='category' class='dropdown-content'></div>");
//     echo ("</div>");
//     error_log($sql . "<br>" . $e->getMessage());
// }