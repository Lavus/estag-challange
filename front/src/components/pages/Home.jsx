import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleHome from './css/Home.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'

function Home () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [orderItems, setOrderItems] = useState([])
    const selectValues = {
        'type' : 'FullCases',
        'table' : 'order_item',
        'code' : '0',
        'camps' : ['code','product_name','amount','price','tax'],
        'innerCamps' : [],
        'innerCampsAlias' : [],
        'innerTable' : 'none',
        'foreignKey' : 'none',
        'where' : 'order_item.order_code IN ( SELECT MAX( orders1.code ) FROM orders as orders1 ) ORDER BY order_item.code;',
        'caseVerifications' : ['product_code','product_code','product_code','product_code'],
        'caseVerificationTables' : ['order_item','order_item','order_item','order_item'],
        'caseVerificationTablesAlias' : ['order_item1','order_item2','order_item3','order_item4',],
        'caseVerificationWheres' : ['order_item1.code = order_item.code','order_item2.code = order_item.code','order_item3.code = order_item.code','order_item4.code = order_item.code'],
        'caseVerificationParameters' : ['IS NOT NULL','IS NOT NULL','IS NOT NULL','IS NOT NULL'],
        'caseVerificationValues' : ['name','amount','price','tax'],
        'caseVerificationValueTables' : [['products'],['products'],['products'],['products','categories']],
        'caseVerificationValueTablesAlias' : [['products1'],['products2'],['products3'],['products4','categories1']],
        'caseVerificationValueWheres' : ['products1.code = order_item.product_code','products2.code = order_item.product_code','products3.code = order_item.product_code','products4.code = order_item.product_code AND categories1.code = products4.category_code'],
        'caseVerificationElse' : ['False','False','False','False'],
        'caseVerificationAlias' : ['products_name','products_amount','products_price','categories_tax']
    }
    //      order_item.code AS order_item_code, order_item.product_name AS order_product_name, order_item.amount AS order_amount, order_item.price AS order_price, order_item.tax AS order_tax FROM order_item WHERE order_item.order_code IN ( SELECT MAX( orders.code ) FROM orders ) ORDER BY order_item.code;";

    // function SelectSql(string $type, string $table, string $code = '0', array $camps = [], array $innerCamps = [], array $innerCampsAlias = [], string $innerTable = 'none', string $foreignKey = '0', string $where = '1=0', array $caseVerifications =[], array $caseVerificationTables =[], array $caseVerificationTablesAlias =[], array $caseVerificationWheres =[], array $caseVerificationParameters =[], array $caseVerificationValues =[], array $caseVerificationValueTables =[], array $caseVerificationValueTablesAlias =[], array $caseVerificationValueWheres =[], array $caseVerificationElse =[], array $caseVerificationAlias =[] ): array {
    //     $sasql = "SELECT 
    //     caseVerification, caseVerificationTable, caseVerificationTableAlias, caseVerificationWhere  caseVerificationAlias   caseVerificationParameters caseVerificationElse
    //     product_code        order_item                       order_item1      order_item.code       products_name,products_amount,products_price

    //     caseVerificationValue,                          caseVerificationTableAlias                caseVerificationValueTables, caseVerificationValueWhere
    //     products.name,products.amount ,products.price               products1                                      products                     order_item.product_code

    //     CASE WHEN ( SELECT order_item1.product_code FROM order_item AS order_item1 WHERE order_item1.code = order_item.code ) IS NOT NULL THEN ( SELECT products1.name FROM products AS products1 WHERE products1.code = order_item.product_code ) ELSE 'False' END AS products_name,
    //     CASE WHEN ( SELECT order_item2.product_code FROM order_item AS order_item2 WHERE order_item2.code = order_item.code ) IS NOT NULL THEN ( SELECT products2.amount FROM products AS products2 WHERE products2.code = order_item.product_code ) ELSE 'False' END AS products_amount,
    //     CASE WHEN ( SELECT order_item3.product_code FROM order_item AS order_item3 WHERE order_item3.code = order_item.code ) IS NOT NULL THEN ( SELECT products3.price FROM products AS products3 WHERE products3.code = order_item.product_code ) ELSE 'False' END AS products_price,
    //     CASE WHEN ( SELECT order_item4.product_code FROM order_item AS order_item4 WHERE order_item4.code = order_item.code ) IS NOT NULL THEN ( SELECT categories1.tax FROM products AS products4, categories AS categories1 WHERE products4.code = order_item.product_code AND categories1.code = products4.category_code ) ELSE 'False' END AS categories_tax,
        




    useEffect(() => {
        FetchSelect(setOrderItems,setRemoveLoading,selectValues)
    }, [])

    let leftDescriptionPage = 'View insert category'
    let rightDescriptionPage = 'View Categories'
    let iconLeftPage = ''
    let iconRightPage = 'hidden'
    let leftPage = ''
    let rightPage = 'show'

    return (
        <>
            <div className = {styles.main}>
                <TextDrop 
                    leftDescription = {leftDescriptionPage}
                    rightDescription = {rightDescriptionPage}
                    iconLeft = {iconLeftPage}
                    iconRight = {iconRightPage}
                />
                <div className = {leftPage ? (`${styles.left} ${styles[leftPage]}`) : styles.left}>
                    <form id='formcategories' action='addcategory.php' method='post'>
                        <input type='text' id='categoryname' name='categoryname' placeholder='Category name' className = 'half' maxLength='255' title='Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.' pattern='^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$' required/>
                        <input type='number' id='tax' name='tax' step='0.01' min='0' max='9999.99' placeholder='Tax' className = 'half' required/>
                        <input type='submit' value='Add Category' className = 'bluebold full'/>
                    </form>
                </div>
                <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
                    <div className={styles.scroll}>
                        {removeLoading ? (
                            <>
                                <Table 
                                    tableid = 'tablecart'
                                    tableNames = {['Product','Price','Amount','Total']}
                                    table = {orderItems}
                                    last = 'delete'
                                    lastButton = '&#128465;'
                                    tableStyle = {styleHome.home}
                                />
                            </>
                        ) : ( <Loading/> ) }
                    </div>
                </div>
            </div>
        </>
    )
}
  
export default Home