import styles from './css/Table.module.css'

function Table( { tableid, tablenames, valueid, tablesize } ) {

    function ShowTh(){
        return (
            <tr key='first'>
                {tablenames.map((name, index) => (
                    <th key={index}>{name}</th>
                ))}
            </tr>
        )
    }

    function ShowLastTd(){
        return (
            <tr key='last' className={styles.last}>
                {tablenames.map((name, index) => (
                    <td key={index}></td>
                ))}
            </tr>
        )
    }

    return (
        <>
            <table id={tableid} className = {tablesize ? (`${styles.collapse} ${styles.half}`) : (`${styles.collapse} ${styles.lefttext}`)}>
                <tbody>
                    {ShowTh()}
                    {ShowLastTd()}
            {/* 
            $sql = "SELECT categories.code, categories.name, categories.tax FROM categories ORDER BY code;";
            try {
                $prep = $conn->prepare($sql);
                $prep->execute();
                    if ($prep->rowCount() > 0) {
                    $result = $prep->fetchAll(PDO::FETCH_ASSOC);
                    foreach($result as $row) {
                        $name2 = safeDecrypt($row['name'], getkey());
                        $tax2 = safeDecrypt($row['tax'], getkey());
                        $decodename2 = html_entity_decode($name2);
                        $decodetax2 = html_entity_decode($tax2);
                        if ( ($name2 == 'FALSE') || (!(preg_match($regexname, $decodename2))) || ($tax2 == 'FALSE') || (!(preg_match($regexnumberstax, $decodetax2))) ){
                            $executesecure = FALSE;
                            $sqldelete1 = "DELETE FROM categories WHERE code = '".$row['code']."';";
                            try {
                                $conn->beginTransaction();
                                $conn->exec($sqldelete1);
                                $conn->commit();
                            } catch(PDOException $e) {
                                $conn->rollback();
                                error_log("Error: " . $e->getMessage() . "<br><br>");
                            }
                            echo("<div class='refreshthepagenowcategories'></div>");
                        }
                        if ($executesecure){
                            echo("<tr>");
                                echo ("<td><div title='".$row['code']."'>".codifyhtml(strval($row['code']))."</div><button type='submit' name='alterkey' form='alterformcategory' class='extrabt alter' value='".$row['code']."'>&#9997;</button></td>");
                                echo ("<td title='".$decodename2."'>".$name2."</td>");
                                echo ("<td><div title='".$decodetax2."%'>".$tax2."%</div><button type='submit' name='deletekey' form='deleteformcategory' class='extrabt delete' value='".$row['code']."'>&#128465;</button></td>");
                            echo("</tr>");
                        } else {
                            echo("<div class='refreshthepagenowcategories'></div>");
                        }
                    }
                }
            } catch(PDOException $e) {
                error_log($sql . "<br>" . $e->getMessage());
            }
            $conn = null;
        ?> */}
                </tbody>
            </table>
        </>
    )
}

export default Table