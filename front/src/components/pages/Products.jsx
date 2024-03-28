import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleProducts from './css/Products.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'
import FormProducts from './forms/FormProducts'

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
                                productData = {{
                                    'name':'',
                                    'price':'',
                                    'amount':'',
                                    'category':'',
                                    'error':''
                                }}
                                categoriesData = {categories}
                                buttonText = 'Add Category'
                            />
                        </>) : (<>
                            <FormProducts
                                productData = {{
                                    'name':DecodeHtml(products[code]['name']),
                                    'price':(DecodeHtml(products[code]['price'])).slice(0, 1),
                                    'amount':DecodeHtml(products[code]['amount']),
                                    'category':products[code]['category_code'],
                                    'id':code,
                                    'error':''
                                }}
                                categoriesData = {categories}
                                buttonText = 'Alter Category'
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