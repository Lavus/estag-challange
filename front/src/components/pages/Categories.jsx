import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import styles from './css/Pages.module.css'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleCategories from './css/Categories.module.css'
import FormCategories from './forms/FormCategories'
import DecodeHtml from '../functions/DecodeHtml'
import EncodeHtml from '../functions/EncodeHtml'
import AlertScreen from '../View/AlertScreen'
import FetchSelect from './functions/FetchSelect'
import FetchDelete from './functions/FetchDelete'
import FetchUpdate from './functions/FetchUpdate'
import FetchInsert from './functions/FetchInsert'
import ValidateCamps from './functions/ValidateCamps'

function Categories ({css, cssRightFunction, cssLeftFunction}) {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [removeLoadingForm, setRemoveLoadingForm] = useState(false)
    const [categories, setCategories] = useState([])
    const [refresh, setRefresh] = useState(false)
    const [refreshForm, setRefreshForm] = useState(false)
    const [code, setCode] = useState(0)
    const [leftDescriptionPage, setLeftDescriptionPage] = useState('View Insert Category')
    const selectValues =  {
        'type':['FullSimple'],
        'table':'categories',
        'code':'0',
        'camps':[['code'],['name'],['tax']],
        'campsAlias':['code','name','tax']
    }
    const [deleteValues,setDeleteValues] = useState({
        'type' : ['FullCases'],
        'table' : 'categories',
        'code' : '0',
        'camps' : [['code'],['name']],
        'campsAlias' : ['code','name'],
        'innerCamps' : [],
        'innerCampsAlias' : [],
        'innerTables' : [],
        'foreignKey' : 'none',
        'where' : `categories.code = '0';`,
        'caseVerifications' : [[['code']]],
        'caseVerificationTables' : [['products']],
        'caseVerificationTablesAlias' : [['products1']],
        'caseVerificationWheres' : ['products1.category_code = categories.code LIMIT 1'],
        'caseVerificationParameters' : ['IS NOT NULL'],
        'caseVerificationValues' : [[['code','COUNT']]],
        'caseVerificationValueTables' : [['products']],
        'caseVerificationValueTablesAlias' : [['products2']],
        'caseVerificationValueWheres' : ['products2.category_code = categories.code'],
        'caseVerificationElse' : [0],
        'caseVerificationAlias' : ['count_products_code']
    })

    function TriggerResponse(verification,message){
        if (verification == true){
            alert(`Category ${message}, executed with success.`)
            RefreshAll()
        } else {
            alert(`There's some problem with the request of ${message}, please try again.`)
            RefreshAll()
        }
        ExecuteRight()
    }

    function TriggerDelete(codeDelete){
        setDeleteValues({ ...deleteValues, code:'0', where:`categories.code = '0';`})
        let deleteCamp = {
            'type' : ['Simple'],
            'table' : 'categories',
            'code' : codeDelete
        }
        FetchDelete(deleteCamp,TriggerResponse)
    }

    function RefreshAll(){
        setLeftDescriptionPage('View Insert Category')
        setCode(0)
        setRefresh(true)
        setRefreshForm(true)
        setRemoveLoadingForm(false)
    }

    function AlterCategory(category){
        if ((category['name'])&&(category['tax'])&&(category['id'])) {
            let validatedCampsAlter = ValidateCamps('Alter',category,['name','tax'],categories)
            if (validatedCampsAlter[0] == 'true'){
                let oldName = DecodeHtml(categories[category['id']]['name'])
                let oldTax = (DecodeHtml(categories[category['id']]['tax'])).slice(0, -1)
                let updateValues = {
                    'type':'categories',
                    'name':EncodeHtml(category['name']),
                    'tax':EncodeHtml(category['tax']),
                    'id':String(category['id']),
                    'oldName':EncodeHtml(oldName),
                    'oldTax':EncodeHtml(oldTax)
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
    }

    function InsertCategory(category){
        if ((category['name'])&&(category['tax'])) {
            let validatedCampsInsert = ValidateCamps('Insert',category,['name','tax'],categories)
            if (validatedCampsInsert[0] == 'true'){
                let insertValues = {
                    'type':'categories',
                    'name':EncodeHtml(category['name']),
                    'tax':EncodeHtml(category['tax'])
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
    }

    function ChangeInsert(e) {
        (e) => { e.preventDefault() }
        if (categories.hasOwnProperty(e.target.value)) {
            ExecuteLeft()
            setLeftDescriptionPage('View Alter Category')
            setCode(e.target.value)
        } else {
            RefreshAll()
        }
    }

    function TriggerRefresh() {
        RefreshAll()
        setDeleteValues({ ...deleteValues, code:'0', where:`categories.code = '0';`})
    }

    function DeleteCategory(e) {
        (e) => { e.preventDefault() }
        if (categories.hasOwnProperty(e.target.value)) {
            setDeleteValues({ ...deleteValues, code:e.target.value, where:`categories.code = '${e.target.value}';`})
        } else {
            RefreshAll()
        }
    }

    useEffect(() => {
        setRefreshForm(false)
        setRemoveLoadingForm(true)
    }, [refreshForm])

    useEffect(() => {
        setRefresh(false)
        setRemoveLoading(false)
        FetchSelect(selectValues,FinishFunctionFetchSelect)
    }, [refresh])

    function FinishFunctionFetchSelect(data){
        setCategories(data)
        setRemoveLoading(true)
    }

    function ExecuteLeft(){
        cssLeftFunction()
    }

    function ExecuteRight(){
        cssRightFunction()
    }

    return (<>
        {((deleteValues.code != '0') && (<>
            <AlertScreen
                selectValues = {deleteValues}
                refreshFunction = {TriggerRefresh}
                yesFunction = {TriggerDelete}
                changeCode = {deleteValues.code}
                type = 'categories'
            />
        </>))}

        <div className = {styles.main}>
            <TextDrop 
                leftDescription = {leftDescriptionPage}
                rightDescription = 'View Categories'
                iconLeft = {css.iconLeftPage}
                iconRight = {css.iconRightPage}
                functionLeft = {ExecuteLeft}
                functionRight = {ExecuteRight}
            />
            <div className = {css.leftPage ? (`${styles.left} ${styles[css.leftPage]}`) : styles.left}>
                {removeLoadingForm ? (<>
                    {(code == 0) ? (<>
                        <FormCategories
                            handleSubmit = {InsertCategory}
                            categoryData = {{
                                'name':'',
                                'tax':'',
                                'error':''
                            }}
                            buttonText = 'Add Category'
                        />
                    </>) : (<>
                        <FormCategories
                            handleSubmit = {AlterCategory}
                            categoryData = {{
                                'name':DecodeHtml(categories[code]['name']),
                                'tax':(DecodeHtml(categories[code]['tax'])).slice(0, -1),
                                'id':code,
                                'error':''
                            }}
                            buttonText = 'Alter Category'
                            refreshFunction = {TriggerRefresh}
                        />
                    </>)}
                </>) : ( <Loading/> ) }
            </div>
            <div className = {css.rightPage ? (`${styles.right} ${styles[css.rightPage]}`) : styles.right}>
                <div className={styles.scroll}>
                    {removeLoading ? (<>
                        <Table 
                            tableid = 'tableCategories'
                            tableNames = {["Code","Category","Tax"]}
                            campsNames = {['code','name','tax']}
                            table = {categories}
                            first = 'alter'
                            last = 'delete'
                            firstButton = '&#9997;'
                            lastButton = '&#128465;'
                            firstButtonFunction = {ChangeInsert}
                            lastButtonFunction = {DeleteCategory}
                            tableStyle = {styleCategories.categories}
                        />
                    </>) : ( <Loading/> ) }
                </div>
            </div>
        </div>
        
    </>)
}
  
export default Categories