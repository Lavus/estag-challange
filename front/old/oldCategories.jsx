import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import styles from './css/Pages.module.css'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import './css/Categories.module.css'

function Categories () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [categories, setCategories] = useState([])
    const selectValues =  {'type':'FullSimple', 'table':'categories', 'code':'0'}

    useEffect(() => {
    // Para ver o loading
        setTimeout(
            () =>
                fetch('http://localhost/ports/SelectPort.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT':'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='
                    },
                    body: JSON.stringify(selectValues)
                })
                .then((resp) => resp.json())
                .then((data) => {
                    setCategories(data)
                    setRemoveLoading(true)
                }),
            10,
        )
    }, [])

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
                    <form id='formcategories' action='addcategory.php' method='post'>
                        <input type='text' id='categoryname' name='categoryname' placeholder='Category name' className = 'half' maxLength='255' title='Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.' pattern='^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$' required/>
                        <input type='number' id='tax' name='tax' step='0.01' min='0' max='9999.99' placeholder='Tax' className = 'half' required/>
                        <input type='submit' value='Add Category' className = 'bluebold full'/>
                    </form>
                </div>
                <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
                    <div className={styles.scroll}>
                        {removeLoading ? (
                            <>
                                <Table 
                                    tableid = 'tablecategories'
                                    tableNames = {["Code","Category","Tax"]}
                                    table = {categories}
                                    first = 'alter'
                                    last = 'delete'
                                    firstButton = '&#9997;'
                                    lastButton = '&#128465;'
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