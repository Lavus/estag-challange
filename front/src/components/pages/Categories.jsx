import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import styles from './css/Pages.module.css'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleCategories from './css/Categories.module.css'
import FormCategories from './forms/FormCategories'
import FetchInsert from './functions/FetchInsert'

function Categories () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [categories, setCategories] = useState([])
    const [refresh, setRefresh] = useState(false)
    const selectValues =  {
        'type':['FullSimple'],
        'table':'categories',
        'code':'0',
        'camps':[['code'],['name'],['tax']],
        'campsAlias':['code','name','tax']
    }

    function InsertCategories(category){
        if ((category['name'])&&(category['tax'])) {
            let regexName = new RegExp("^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$")
            let regexTax = new RegExp("^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$")
            if ( (regexName.test(category['name'])) && (regexTax.test(category['tax']) ) ) {
                if ( (document.getElementById('tableCategories').innerHTML).indexOf('"'+category['name']+'"') == -1 ){
                    alert("all right")
                    FetchInsert({'type':'categories','name':category['name'],'tax':category['tax']})
                    // let fet = FetchInsert({'name':category['name'],'tax':category['tax']})
                    // alert(fet)
                    alert('error')

                } else {
                    alert("There's already a category within this name, please add more information with the name or change the name.")
                }
            } else {
                alert("There's some problem with the request, please try again.");
                setRefresh(true)
            }
        }else{
            alert("There's some problem with the request, please try again.");
            setRefresh(true)
        }
    }

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
                    {removeLoading ? (
                        <>
                            <FormCategories
                                handleSubmit = {InsertCategories}
                            />
                        </>
                    ) : ( <Loading/> ) }
                </div>
                <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
                    <div className={styles.scroll}>
                        {removeLoading ? (
                            <>
                                <Table 
                                    tableid = 'tableCategories'
                                    tableNames = {["Code","Category","Tax"]}
                                    campsNames = {['code','name','tax']}
                                    table = {categories}
                                    first = 'alter'
                                    last = 'delete'
                                    firstButton = '&#9997;'
                                    lastButton = '&#128465;'
                                    tableStyle = {styleCategories.categories}
                                />
                            </>
                        ) : ( <Loading/> ) }
                    </div>
                </div>
            </div>
        </>
    )
}
  
export default Categories