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
        'type' : ['FullCasesHome'],
        'table' : 'order_item',
        'code' : '0',
        'camps' : [['code'],['product_name'],['amount'],['price'],['tax']],
        'campsAlias' : ['code','product_name','amount','price','tax'],
        'innerCamps' : [],
        'innerCampsAlias' : [],
        'innerTables' : [],
        'foreignKey' : 'none',
        'where' : 'order_item.order_code IN ( SELECT MAX( orders1.code ) FROM orders as orders1 ) ORDER BY order_item.code;',
        'caseVerifications' : [[['product_code']],[['product_code']],[['product_code']],[['product_code']]],
        'caseVerificationTables' : [['order_item'],['order_item'],['order_item'],['order_item']],
        'caseVerificationTablesAlias' : [['order_item1'],['order_item2'],['order_item3'],['order_item4']],
        'caseVerificationWheres' : ['order_item1.code = order_item.code','order_item2.code = order_item.code','order_item3.code = order_item.code','order_item4.code = order_item.code'],
        'caseVerificationParameters' : ['IS NOT NULL','IS NOT NULL','IS NOT NULL','IS NOT NULL'],
        'caseVerificationValues' : [[['name']],[['amount']],[['price']],[['tax']]],
        'caseVerificationValueTables' : [['products'],['products'],['products'],['categories','products']],
        'caseVerificationValueTablesAlias' : [['products1'],['products2'],['products3'],['categories1','products4']],
        'caseVerificationValueWheres' : ['products1.code = order_item.product_code','products2.code = order_item.product_code','products3.code = order_item.product_code','products4.code = order_item.product_code AND categories1.code = products4.category_code'],
        'caseVerificationElse' : ['False','False','False','False'],
        'caseVerificationAlias' : ['products_name','products_amount','products_price','categories_tax']
    }

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
                                    campsNames = {['product_name','price','amount','total']}
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