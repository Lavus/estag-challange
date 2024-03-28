import styles from './css/AlertScreen.module.css'
import DecodeHtml from '../functions/DecodeHtml'
import { useState, useEffect } from 'react'
import Loading from '../layout/Loading'
import Button from '../form/Button'

function AlertScreen( { selectValues, refreshFunction, yesFunction } ) {
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
        setRemoveLoading(false)
        setRefresh(false)
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
            setCategoryItems(data)
            setRemoveLoading(true)
        })
    }, [refresh])

    function executeDelete(e) {
        (e) => { e.preventDefault() }
        yesFunction(selectValues.code)
    }

    useEffect(() => {
        setRefresh(true)
    }, [selectValues])

    return (<>
        <div className={styles.alert}>
            {removeLoading ? (<>
                {( ((categoryItems['broken']) || (categoryItems.length == 0)) ? (<>
                    {alert("There's some problem with the request, please try again.")}
                    {refreshFunction()}
                </>):(<>
                    {/* {alert(JSON.stringify(categoryItems))} */}
                    <div className={styles.textalert}>Do you really want to delete the category '{DecodeHtml(categoryItems[selectValues.code]['name'])}' ?<br/>
                        {((categoryItems[selectValues.code]['count_products_code'] != 0) && (<>
                            There's {categoryItems[selectValues.code]['count_products_code']} product(s) within this category '{DecodeHtml(categoryItems[selectValues.code]['name'])}', by deleting the category, those products will be deleted together, do you really want to continue?
                        </>))}
                    </div>
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
                </>))}
            </>) : ( <Loading/> ) }
        </div>
    </>)
}

export default AlertScreen