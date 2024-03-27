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

function Categories () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [removeLoadingForm, setRemoveLoadingForm] = useState(false)
    const [categories, setCategories] = useState([])
    const [refresh, setRefresh] = useState(false)
    const [refreshForm, setRefreshForm] = useState(false)
    const [code, setCode] = useState(0)
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

    function AlterCategory(category){
        // alert(JSON.stringify(category))
        if ((category['name'])&&(category['tax'])&&(category['id'])) {
            if (categories.hasOwnProperty(category['id'])) {
                let oldName = DecodeHtml(categories[category['id']]['name'])
                let oldTax = (DecodeHtml(categories[category['id']]['tax'])).slice(0, -1)
                if ( (category['name'] != oldName) || (category['tax'] != oldTax) ){
                    let regexName = new RegExp("^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$")
                    let regexTax = new RegExp("^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$")
                    if ( (regexName.test(category['name'])) && (regexTax.test(category['tax']) ) ) {
                        alert('crime')
                        if ( ( (category['name'] != oldName) && ( (document.getElementById('tableCategories').innerHTML).indexOf('"'+category['name']+'"') == -1 ) ) || (category['name'] == oldName) ){
                            let updateValues = {'type':'categories','name':EncodeHtml(category['name']),'tax':EncodeHtml(category['tax']),'id':category['id'],'oldName':EncodeHtml(oldName),'oldTax':EncodeHtml(oldTax)}
                            fetch('http://localhost/ports/UpdatePort.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type' : 'application/json',
                                    'JPIZGRPSNRMFNYUWVPZ7RKFWLNMVUCNXSGO3FEQEVAOQUJRHEAONY4FGWEICD9KARVOKHHZYOC3PAQNZNRN6LDSMGNRMDNCAR0PPOPG6CCJ2UVRUBAQ' : 'SlCo/rpvAFCWsfljh2VGhCCrt4CnBCuoZf5gobtIh7KFLH1Z+ZteqDc+ARImfH9M9B1cdlMje7UkqUXjpIKhazGkKyBD3Xebzr1yLsk4O6RGK0CRDMWgz9dmhZ77tNlr2oiwAyXVb8PX4EV+vi/VSD1Vj8SgE6I='
                                },
                                body: JSON.stringify(updateValues)
                            })
                            .then((resp) => resp.json())
                            .then((data) => {
                                if (data == true){
                                    alert('Category inserted with success.')
                                    setCode(0)
                                    setRefresh(true)
                                    setRefreshForm(true)
                                    setRemoveLoadingForm(false)
                                } else {
                                    alert("There's some problem with the request, please try again.")
                                    setCode(0)
                                    setRefresh(true)
                                    setRefreshForm(true)
                                    setRemoveLoadingForm(false)
                                }
                            })
                        } else {
                            alert("There's already another category within this name, please add more information with the name or change the name or return the name to the same as before.");
                        }
                    } else {
                        alert("There's some problem with the request, please try again.")
                        setCode(0)
                        setRefresh(true)
                        setRefreshForm(true)
                        setRemoveLoadingForm(false)
                    }
                } else {
                    alert("Nothing was changed.")
                }
            }else{
                alert("There's some problem with the request, please try again.")
                setCode(0)
                setRefresh(true)
                setRefreshForm(true)
                setRemoveLoadingForm(false)
            }     
        }else{
            alert("There's some problem with the request, please try again.")
            setCode(0)
            setRefresh(true)
            setRefreshForm(true)
            setRemoveLoadingForm(false)
        }
    }

    function InsertCategory(category){
        if ((category['name'])&&(category['tax'])) {
            let regexName = new RegExp("^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$")
            let regexTax = new RegExp("^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$")
            if ( (regexName.test(category['name'])) && (regexTax.test(category['tax']) ) ) {
                alert('crime')
                if ( (document.getElementById('tableCategories').innerHTML).indexOf('"'+category['name']+'"') == -1 ){
                    let insertValues = {'type':'categories','name':EncodeHtml(category['name']),'tax':EncodeHtml(category['tax'])}
                    fetch('http://localhost/ports/InsertPort.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type' : 'application/json',
                            'FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44' : 'Falw1qKPKZYufBz0r2S1avMZ16BeNHPn3/nqJzg2IyDHF+XtM4x9cBMTOvG++LTO3wCbTEJXEocIO+xfjPCEunNGKu8DvjQzXG29DSSiuQsPnwVV+/cHwnNh6MFLg3KvNC4k3v9uhXZkRMBaRIglt2FnKt3gLssn'
                        },
                        body: JSON.stringify(insertValues)
                    })
                    .then((resp) => resp.json())
                    .then((data) => {
                        if (data == true){
                            alert('Category inserted with success.')
                            setRefresh(true)
                            setRefreshForm(true)
                            setRemoveLoadingForm(false)
                        } else {
                            alert("There's some problem with the request, please try again.")
                            setRefresh(true)
                            setRefreshForm(true)
                            setRemoveLoadingForm(false)
                        }
                    })
                } else {
                    alert("There's already a category within this name, please add more information with the name or change the name.")
                }
            } else {
                alert("There's some problem with the request, please try again.")
                setRefresh(true)
                setRefreshForm(true)
                setRemoveLoadingForm(false)
            }
        }else{
            alert("There's some problem with the request, please try again.")
            setRefresh(true)
            setRefreshForm(true)
            setRemoveLoadingForm(false)
        }
    }

    function ChangeInsert(e) {
        (e) => { e.preventDefault() }
        if (categories.hasOwnProperty(e.target.value)) {
            setCode(e.target.value)
        } else {
            setCode(0)
            setRefresh(true)
            setRefreshForm(true)
            setRemoveLoadingForm(false)
        }
    }

    function triggerRefresh() {
        setCode(0)
        setRefreshForm(true)
        setRemoveLoadingForm(false)
        setDeleteValues({ ...deleteValues, code:'0', where:`categories.code = '0';`})
    }

    function DeleteCategory(e) {
        (e) => { e.preventDefault() }
        if (categories.hasOwnProperty(e.target.value)) {
            setDeleteValues({ ...deleteValues, code:e.target.value, where:`categories.code = '${e.target.value}';`})
        } else {
            setCode(0)
            setRefresh(true)
            setRefreshForm(true)
            setRemoveLoadingForm(false)
        }
    }
    
    // useEffect(() => {
    //     alert(JSON.stringify(deleteValues))
    // }, [deleteValues])

    useEffect(() => {
        setRefreshForm(false)
        setRemoveLoadingForm(true)
    }, [refreshForm])

    useEffect(() => {
        setRefresh(false)
        setRemoveLoading(false)
        fetch('http://localhost/ports/SelectPort.php', {
            method: 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT' : 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='
            },
            body: JSON.stringify(selectValues)
        })
        .then((resp) => resp.json())
        .then((data) => {
            setCategories(data)
            setRemoveLoading(true)
        })
    }, [refresh])

    let leftDescriptionPage = 'View insert category'
    let rightDescriptionPage = 'View Categories'
    let iconLeftPage = ''
    let iconRightPage = 'hidden'
    let leftPage = ''
    let rightPage = 'show'

    return (<>
        {deleteValues.code != '0' && (<>
            <AlertScreen
                selectValues = {deleteValues}
                refreshFunction = {triggerRefresh}
            />
        </>)}

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
                        <FormCategories
                            handleSubmit = {InsertCategory}
                            categoryData = {{'name':'','tax':'','error':''}}
                            buttonText = 'Add Category'
                        />
                    </>) : (<>
                        <FormCategories
                            handleSubmit = {AlterCategory}
                            categoryData = {{'name':DecodeHtml(categories[code]['name']),'tax':(DecodeHtml(categories[code]['tax'])).slice(0, -1),'id':code,'error':''}}
                            buttonText = 'Alter Category'
                            refreshFunction = {triggerRefresh}
                        />
                    </>)}
                </>) : ( <Loading/> ) }
            </div>
            <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
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