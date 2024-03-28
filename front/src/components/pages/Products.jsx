import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleProducts from './css/Products.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'
import FormProducts from './forms/FormProducts'
import DecodeHtml from '../functions/DecodeHtml'
import EncodeHtml from '../functions/EncodeHtml'
import ValidateCamps from './functions/ValidateCamps'
import FetchInsert from './functions/FetchInsert'
import FetchUpdate from './functions/FetchUpdate'

function Products () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [removeLoadingForm, setRemoveLoadingForm] = useState(false)
    const [products, setProducts] = useState([])
    const [categories, setCategories] = useState([])
    const [refresh, setRefresh] = useState(false)
    const [refreshForm, setRefreshForm] = useState(false)
    const [refreshCategories, setRefreshCategories] = useState(false)
    const [code, setCode] = useState(0)
    const selectValues =  {
        'type':['SimpleForeign'],
        'table':'products',
        'code':'0',
        'camps':[['code'],['name'],['amount'],['price']],
        'campsAlias':['code','name','amount','price'],
        'innerCamps':[[['code'],['name']]],
        'innerCampsAlias':[['category_code','category_name']],
        'innerTables':['categories'],
        'foreignKey':'category_code'
    }
    const selectValuesCategories =  {
        'type':['FullSimple'],
        'table':'categories',
        'code':'0',
        'camps':[['code'],['name']],
        'campsAlias':['code','name']
    }

    function AlterProduct(product){
        if ((product['name'])&&(product['amount'])&&(product['price'])&&(product['category'])&&(product['id'])) {
            if (categories.hasOwnProperty(product['category'])) {
                let validatedCampsAlter = ValidateCamps('Alter',product,['name','amount','price'],products)
                if ( (validatedCampsAlter[0] == 'true') || ((validatedCampsAlter[0] == 'none') && (product['category'] != products[product['id']]['category_code'])) ) {
                    let oldName = DecodeHtml(products[product['id']]['name'])
                    let oldAmount = DecodeHtml(products[product['id']]['amount'])
                    let oldPrice = parseFloat((DecodeHtml(products[product['id']]['price'])).slice(1))
                    let oldCategory = products[product['id']]['category_code']
                    let updateValues = {
                        'type':'products',
                        'name':EncodeHtml(product['name']),
                        'amount':EncodeHtml(product['amount']),
                        'price':EncodeHtml(product['price']),
                        'category':EncodeHtml(product['category']),
                        'id':product['id'],
                        'oldName':EncodeHtml(oldName),
                        'oldAmount':EncodeHtml(oldAmount),
                        'oldPrice':EncodeHtml(oldPrice),
                        'oldCategory':oldCategory
                    }
                    FetchUpdate(updateValues,TriggerResponse)
                } else if (validatedCampsAlter[0] == 'false'){
                    alert(validatedCampsAlter[1])
                    RefreshAll()
                } else if ( (validatedCampsAlter[0] == 'check') || (validatedCampsAlter[0] == 'none') ){
                    alert(validatedCampsAlter[1])
                }
            }else{
                alert("There's some problem with the request, please try again.")
                RefreshAll()
            }
        }else{
            alert("There's some problem with the request, please try again.")
            RefreshAll()
        }
    }

    function InsertProduct(product){
        if (product['category']) {
            if ((product['name'])&&(product['amount'])&&(product['price'])) {
                if (categories.hasOwnProperty(product['category'])) {
                    let validatedCampsInsert = ValidateCamps('Insert',product,['name','amount','price'],products)
                    if (validatedCampsInsert[0] == 'true'){
                        let insertValues = {
                            'type':'products',
                            'name':EncodeHtml(product['name']),
                            'amount':EncodeHtml(product['amount']),
                            'price':EncodeHtml(product['price']),
                            'category':product['category']
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
            }else{
                alert("There's some problem with the request, please try again.")
                RefreshAll()
            }
        } else {
            alert("Before adding the product, please select a category.")
        }
    }

    function TriggerResponse(verification,message){
        if (verification == true){
            alert(`Product ${message}, executed with success.`)
            RefreshAll()
        } else {
            alert(`There's some problem with the request of ${message}, please try again.`)
            RefreshAll()
        }
    }

    function FinishFunctionFetchSelectCategories(data){
        setCategories(data)
    }

    useEffect(() => {
        setRefreshCategories(false)
        FetchSelect(selectValuesCategories,FinishFunctionFetchSelectCategories)
    }, [refreshCategories])

    function FinishFunctionFetchSelect(data){
        setProducts(data)
        setRemoveLoading(true)
    }

    useEffect(() => {
        setRefresh(false)
        setRemoveLoading(false)
        FetchSelect(selectValues,FinishFunctionFetchSelect)
    }, [refresh])

    useEffect(() => {
        setRefreshForm(false)
        setRemoveLoadingForm(true)
    }, [refreshForm])

    function ChangeInsert(e) {
        (e) => { e.preventDefault() }
        if (products.hasOwnProperty(e.target.value)) {
            setCode(e.target.value)
        } else {
            RefreshAll()
        }
    }

    function TriggerRefresh() {
        RefreshAll()
        setDeleteValues({ ...deleteValues, code:'0', where:`categories.code = '0';`})
    }

    function DeleteProduct(e) {
        (e) => { e.preventDefault() }
        if (products.hasOwnProperty(e.target.value)) {
            setDeleteValues({ ...deleteValues, code:e.target.value, where:`categories.code = '${e.target.value}';`})
        } else {
            RefreshAll()
        }
    }

    function RefreshAll(){
        setCode(0)
        setRefresh(true)
        setRefreshForm(true)
        setRemoveLoadingForm(false)
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
                            <FormProducts
                                handleSubmit = {InsertProduct}
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
                            <FormProducts
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
                            />
                        </>)}
                    </>) : ( <Loading/> ) }
                </div>
                <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
                    <div className={styles.scroll}>
                        {removeLoading ? (
                            <>
                                <Table
                                    tableid = 'tableproducts'
                                    tableNames = {['Code','Product','Amount','Price','Category']}
                                    campsNames = {['code','name','amount','price','category_name']}
                                    table = {products}
                                    first = 'alter'
                                    last = 'delete'
                                    firstButton = '&#9997;'
                                    lastButton = '&#128465;'
                                    firstButtonFunction = {ChangeInsert}
                                    lastButtonFunction = {DeleteProduct}
                                    tableStyle = {styleProducts.products}
                                />
                            </>
                        ) : ( <Loading/> ) }
                    </div>
                </div>
            </div>
        </>
    )
}
  
export default Products