import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleHome from './css/Home.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'
import FormHome from './forms/FormHome'

function Home () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [orderItems, setOrderItems] = useState([])
    const [removeLoadingForm, setRemoveLoadingForm] = useState(false)
    const [refresh, setRefresh] = useState(false)
    const [refreshForm, setRefreshForm] = useState(false)
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
        setRefresh(false)
        setRemoveLoading(false)
        FetchSelect(selectValues,FinishFunctionFetchSelect)
    }, [refresh])

    function FinishFunctionFetchSelect(data){
        setOrderItems(data)
        setRemoveLoading(true)
    }

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
                    {removeLoadingForm ? (<>
                        {(code == 0) ? (<>
                            <FormHome
                                handleSubmit = {InsertItem}
                                productData = {{
                                    'name':'',
                                    'price':'',
                                    'amount':'',
                                    'category':'',
                                    'error':''
                                }}
                                categoriesData = {categories}
                                buttonText = 'Add Product'
                            />
                        </>) : (<>
                            <FormHome
                                handleSubmit = {AlterProduct}
                                productData = {{
                                    'name':DecodeHtml(products[code]['name']),
                                    'price':parseFloat((DecodeHtml(products[code]['price'])).slice(1)),
                                    'amount':DecodeHtml(products[code]['amount']),
                                    'category':products[code]['category_code'],
                                    'id':code,
                                    'error':''
                                }}
                                categoriesData = {categories}
                                buttonText = 'Alter Product'
                                refreshFunction = {TriggerRefresh}
                                placeHolderAmount = 'Amount'
                                maxAmount = '1'
                            />
                        </>)}
                    </>) : ( <Loading/> ) }
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