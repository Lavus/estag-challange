import styles from './css/AlertScreen.module.css'
import DecodeHtml from '../functions/DecodeHtml'
import { useState, useEffect } from 'react'
import Loading from '../layout/Loading'
import Button from '../form/Button'
import FetchSelect from '../pages/functions/FetchSelect'
import Table from './Table'
import CheckSafe from '../pages/functions/CheckSafe'

function AlertScreen( { selectValues, refreshFunction, yesFunction, changeCode, type, table } ) {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [categoryItems, setCategoryItems] = useState([])
    const [refresh, setRefresh] = useState(false)
    const [noButtonText,setNoButtonText] = useState('NO')

    useEffect(() => {
        if (!(type == 'Finish')){
            setTimeout(() => setNoButtonText("NO 10s"), 1000)
            setTimeout(() => setNoButtonText("NO 9s"), 2000)
            setTimeout(() => setNoButtonText("NO 8s"), 3000)
            setTimeout(() => setNoButtonText("NO 7s"), 4000)
            setTimeout(() => setNoButtonText("NO 6s"), 5000)
            setTimeout(() => setNoButtonText("NO 5s"), 6000)
            setTimeout(() => setNoButtonText("NO 4s"), 7000)
            setTimeout(() => setNoButtonText("NO 3s"), 8000)
            setTimeout(() => setNoButtonText("NO 2s"), 9000)
            setTimeout(() => setNoButtonText("NO 1s"), 10000)
            let timerClose = setTimeout(() => refreshFunction(), 11000)
            return () => {
                clearTimeout(timerClose)
            }
        }
    }, [refresh])

    useEffect(() => {
        ((selectValues) ? (
            setRefresh(false),
            setRemoveLoading(false),
            FetchSelect(selectValues,FinishFunctionFetchSelect)
        ):(
            setRefresh(false),
            setRemoveLoading(true)
        ))
    }, [refresh])

    function FinishFunctionFetchSelect(data){
        setCategoryItems(data)
        setRemoveLoading(true)
    }

    function executeCommand(e) {
        (e) => { e.preventDefault() }
        ((changeCode)?(yesFunction(changeCode)):yesFunction())
    }

    function executeRefresh(e) {
        (e) => { e.preventDefault() }
        refreshFunction()
    }

    if (changeCode){
        useEffect(() => {
            setRefresh(true)
        }, [changeCode])
    } else {
        useEffect(() => {
            setRefresh(true)
        }, [type])
    }

    return (<>
        <div className={styles.alert}>
            {removeLoading ? (<>
                {((type == 'categories') ? (<>
                    {(((categoryItems['broken']) || (categoryItems.length == 0)) ? (<>
                        {alert("There's some problem with the request, please try again.")}
                        {refreshFunction()}
                    </>):(<>
                        <div className={styles.textalert}>
                            Do you really want to delete the category '{DecodeHtml(categoryItems[changeCode]['name'])}' ?<br/>
                            {((categoryItems[changeCode]['count_products_code'] != 0) && (<>
                                There's {categoryItems[changeCode]['count_products_code']} product(s) within this category '{DecodeHtml(categoryItems[changeCode]['name'])}', by deleting the category, those products will be deleted together, do you really want to continue?
                            </>))}
                        </div>
                    </>))}
                </>):(<>
                    {((type == 'products') ? (<>
                        <div className={styles.textalert}>
                            Do you really want to delete the product '{DecodeHtml(table[changeCode].name)}' ?<br/>
                        </div>
                    </>):(<>
                        {((type == 'Cancel') ? (<>
                            <div className={styles.textalert}>
                                Do you really want to empty your cart ?<br/>
                            </div>
                        </>):(<>
                            {((type == 'Finish') ? (<>
                                {((!(CheckSafe(table['rows'])))&&(<>
                                    {alert("There's some problem with the request, please try again.")}
                                    {executeRefresh()}
                                </>))}
                                <div className={(`${styles.textalert} ${styles.alertremovefortable}`)}>
                                    <div className={styles.dividetext}>
                                        The total price value of the products is ${(parseFloat((DecodeHtml(table['totalValues'][0]['value_total'])).slice(1)) - parseFloat((DecodeHtml(table['totalValues'][0]['value_tax'])).slice(1))).toFixed(2)}, the tax of the purchase is {DecodeHtml(table['totalValues'][0]['value_tax'])}, totalizing {DecodeHtml(table['totalValues'][0]['value_total'])}, do you want to confirm the purchase ?<br/>Down bellow is the list of all products being purchased :
                                    </div>
                                    <div className={styles.dividetable}>
                                        <Table 
                                            tableid = 'tableFinish'
                                            tableNames = {['Product','Price','Amount','Total']}
                                            campsNames = {['product_name','price','amount','total']}
                                            table = {table['rows']}
                                        />
                                    </div>
                                </div>
                            </>):(<>
                            </>))}
                        </>))}
                    </>))}
                </>))}
                <Button
                    text='YES'
                    className={styles.yesbutton}
                    onClick={executeCommand}
                />
                <Button
                    text={noButtonText}
                    className={styles.nobutton}
                    onClick={executeRefresh}
                />
            </>) : ( <Loading/> ) }
        </div>
    </>)
}

export default AlertScreen