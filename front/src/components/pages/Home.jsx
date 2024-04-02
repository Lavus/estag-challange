import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleHome from './css/Home.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'
import FormHome from './forms/FormHome'
import FetchDelete from './functions/FetchDelete'
import ValidateCamps from './functions/ValidateCamps'
import FetchInsert from './functions/FetchInsert'
import EncodeHtml from '../functions/EncodeHtml'
import FormFinisher from './forms/FormFinisher'
import AlertScreen from '../View/AlertScreen'

function Home () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [orderItems, setOrderItems] = useState([])
    const [products, setProducts] = useState([])
    const [removeLoadingForm, setRemoveLoadingForm] = useState(false)
    const [refresh, setRefresh] = useState(false)
    const [refreshForm, setRefreshForm] = useState(false)
    const [refreshProducts, setRefreshProducts] = useState(false)
    const [formConfirm, setFormConfirm] = useState('0')
    const [code, setCode] = useState(0)
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
    const selectValuesProducts =  {
        'type':['FullCases'],
        'table':'products',
        'code':'0',
        'camps':[['code'],['name'],['amount'],['price']],
        'campsAlias':['code','name','amount','price'],
        'innerCamps':[[['tax']]],
        'innerCampsAlias':[['tax']],
        'innerTables':['categories'],
        'foreignKey':'category_code',
        'where' : 'products.category_code = categories.code;',
        'caseVerifications' : [[['code']]],
        'caseVerificationTables' : [['order_item']],
        'caseVerificationTablesAlias' : [['order_item1']],
        'caseVerificationWheres' : ['order_item1.product_code = products.code AND order_item1.order_code IN ( SELECT MAX( orders1.code ) FROM orders as orders1 )'],
        'caseVerificationParameters' : ['IS NOT NULL'],
        'caseVerificationValues' : [[['amount']]],
        'caseVerificationValueTables' : [['order_item']],
        'caseVerificationValueTablesAlias' : [['order_item2']],
        'caseVerificationValueWheres' : ['order_item2.product_code = products.code AND order_item2.order_code IN ( SELECT MAX( orders2.code ) FROM orders as orders2 )'],
        'caseVerificationElse' : ['0'],
        'caseVerificationAlias' : ['products_amount']
    }

    function InsertOrderItem(orderItem){
        if (products != 0){
            if (orderItem['product']) {
                if (orderItem['amount']) {
                    if (orderItem['amount'] != 'False') {
                        if (products.hasOwnProperty(orderItem['product'])) {
                            let validatedCampsInsert = ValidateCamps('Insert',orderItem,['amount'],orderItems)
                            if (validatedCampsInsert[0] == 'true'){
                                let insertValues = {
                                    'type':'order_item',
                                    'amount':EncodeHtml(orderItem['amount']),
                                    'product':String(orderItem['product'])
                                }
                                FetchInsert(insertValues,TriggerResponse)
                            } else if (validatedCampsInsert[0] == 'false'){
                                alert(validatedCampsInsert[1])
                                RefreshAll()
                            } else if ( (validatedCampsInsert[0] == 'check') || (validatedCampsInsert[0] == 'none') ){
                                alert(validatedCampsInsert[1])
                            }
                        }else{
                            alert("There's some problem with the request, please try again.")
                            RefreshAll()
                        }
                    } else {
                        alert("There's no stock left for the product, please select another product, or wait until the product is avaliable.")
                    }
                }else{
                    alert("There's some problem with the request, please try again.")
                    RefreshAll()
                }
            } else {
                alert("Please select a product and the amount.")
            }
        } else {
            alert("Please wait for a product to be avaliable.")
        }
    }

    function TriggerResponse(verification,message){
        if (verification == true){
            alert(`Cart product(s) ${message}, executed with success.`)
            RefreshAll()
        } else {
            alert(`There's some problem with the request of ${message}, please try again.`)
            RefreshAll()
        }
    }

    function FinishFunctionFetchSelectProducts(data){
        setProducts(data)
    }

    useEffect(() => {
        setRefreshProducts(false)
        FetchSelect(selectValuesProducts,FinishFunctionFetchSelectProducts)
    }, [refreshProducts])

    useEffect(() => {
        setRefresh(false)
        setRemoveLoading(false)
        FetchSelect(selectValues,FinishFunctionFetchSelect)
    }, [refresh])

    function FinishFunctionFetchSelect(data){
        setOrderItems(data)
        setRemoveLoading(true)
    }

    useEffect(() => {
        setRefreshForm(false)
        setRemoveLoadingForm(true)
    }, [refreshForm])

    function ChangeInsert(e) {
        (e) => { e.preventDefault() }
        if (orderItems.hasOwnProperty(e.target.value)) {
            setCode(e.target.value)
        } else {
            RefreshAll()
        }
    }

    function TriggerRefresh() {
        RefreshAll()
        setFormConfirm('0')
    }

    function TriggerFinish() {
        ((orderItems['rows'])&&(setFormConfirm('Finish')))
    }

    function TriggerCancel() {
        ((orderItems['rows'])&&(setFormConfirm('Cancel')))
    }

    function TriggerEmptyCart(){
        setFormConfirm('0')
        let deleteCamp = {
            'type' : 'SimpleWhere',
            'table' : 'order_item',
            'code' : '0',
            'foreignTables' : [],
            'foreignKeys' : [],
            'where' : 'order_item.order_code IN (SELECT MAX(orders1.code) FROM orders AS orders1);'
        }
        FetchDelete(deleteCamp,TriggerResponse)
    }

    function DeleteProduct(e) {
        (e) => { e.preventDefault() }
        if (orderItems['rows'].hasOwnProperty(e.target.value)) {
            let deleteCamp = {
                'type' : 'Simple',
                'table' : 'order_item',
                'code' : e.target.value
            }
            FetchDelete(deleteCamp,TriggerResponse)
        } else {
            RefreshAll()
        }
    }

    function RefreshAll(){
        setCode(0)
        setRefresh(true)
        setRefreshProducts(true)
        setRefreshForm(true)
        setRemoveLoadingForm(false)
    }
    // set array, to make functions of css
    // need to fix delete, to auto update cart
    // change update and insert to array return, for rollback in error
    let leftDescriptionPage = 'View insert category'
    let rightDescriptionPage = 'View Products'
    let iconLeftPage = ''
    let iconRightPage = 'hidden'
    let leftPage = ''
    let rightPage = 'show'

    return (<>
        {((formConfirm == 'Cancel') ? (<>
            <AlertScreen
                refreshFunction = {TriggerRefresh}
                yesFunction = {TriggerEmptyCart}
                type='Cancel'
            />
        </>) : (<>
            {((formConfirm == 'Finish') && (<>
                <AlertScreen
                    refreshFunction = {TriggerRefresh}
                    yesFunction = {TriggerDelete}
                    changeCode={deleteConfirm}
                    type='products'
                    table={orderItems['rows'] ? orderItems['rows'] : orderItems}
                />
            </>))}
        </>))}
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
                            handleSubmit = {InsertOrderItem}
                            cartItemData = {{
                                'product':'',
                                'amount':'',
                                'error':''
                            }}
                            productsData = {products}
                            buttonText = 'Add Product'
                            refreshFunction = {TriggerRefresh}
                        />
                    </>) : (<>
                        {/* <FormHome
                            handleSubmit = {AlterProduct}
                            productData = {{
                                'name':DecodeHtml(products[code]['name']),
                                'price':parseFloat((DecodeHtml(products[code]['price'])).slice(1)),
                                'amount':DecodeHtml(products[code]['amount']),
                                'category':products[code]['category_code'],
                                'id':code,
                                'error':''
                            }}
                            buttonText = 'Alter Product'
                            refreshFunction = {TriggerRefresh}
                            refreshTriggerFunction = {true}
                            placeHolderAmount = 'Amount'
                            maxAmount = '1'
                        /> */}
                    </>)}
                </>) : ( <Loading/> ) }
            </div>
            <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
                {removeLoading ? (<>
                    <div className={styles.eighty}>
                        <div className={styles.scroll}>
                            <Table 
                                tableid = 'tablecart'
                                tableNames = {['Product','Price','Amount','Total']}
                                campsNames = {['product_name','price','amount','total']}
                                table = {orderItems['rows'] ? orderItems['rows'] : orderItems}
                                last = 'delete'
                                lastButton = '&#128465;'
                                lastButtonFunction = {DeleteProduct}
                                tableStyle = {styleHome.home}
                            />
                        </div>
                    </div>
                    <div className={styles.ten}>
                        <Table 
                            tableid = 'tableValues'
                            tableNames = {['Tax: ','Total: ']}
                            campsNames = {['value_tax','value_total']}
                            table = {orderItems['totalValues'] ? orderItems['totalValues'] : orderItems}
                            tableStyle = {styleHome.home}
                        />
                    </div>
                    <div className={styles.ten}>
                        <FormFinisher
                            handleFinish = {TriggerFinish}
                            handleCancel = {TriggerCancel}
                        />
                    </div>
                </>) : ( <Loading/> ) }
            </div>
        </div>
    </>)
}
  
export default Home