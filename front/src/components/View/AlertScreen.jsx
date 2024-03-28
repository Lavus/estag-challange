import styles from './css/AlertScreen.module.css'
import DecodeHtml from '../functions/DecodeHtml'
import { useState, useEffect } from 'react'
import Loading from '../layout/Loading'
import Button from '../form/Button'
import FetchSelect from '../pages/functions/FetchSelect'

function AlertScreen( { selectValues, refreshFunction, yesFunction, changeCode, type, table } ) {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [categoryItems, setCategoryItems] = useState([])
    const [refresh, setRefresh] = useState(false)
    const [noButtonText,setNoButtonText] = useState('No')

    useEffect(() => {
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

    function executeDelete(e) {
        (e) => { e.preventDefault() }
        yesFunction(changeCode)
    }

    useEffect(() => {
        setRefresh(true)
    }, [changeCode])

    return (<>
        <div className={styles.alert}>
            {removeLoading ? (<>
                {((type == 'categories') ? (<>
                    {(((categoryItems['broken']) || (categoryItems.length == 0)) ? (<>
                        {alert("There's some problem with the request, please try again.")}
                        {refreshFunction()}
                    </>):(<>
                        {/* {alert(JSON.stringify(categoryItems))} */}
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
                    </>))}
                </>))}
                <Button
                    text='YES'
                    className={styles.yesbutton}
                    onClick={executeDelete}
                />
                <Button
                    text={noButtonText}
                    className={styles.nobutton}
                    onClick={refreshFunction}
                />
            </>) : ( <Loading/> ) }
        </div>
    </>)
}

export default AlertScreen