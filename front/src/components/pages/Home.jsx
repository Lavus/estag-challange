import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleHome from './css/Home.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'

function Home () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [orderItems, setOrderItems] = useState([])
    const selectValues =  {'type':'FullSimple', 'table':'order_item', 'code':'0'}
    useEffect(() => {
        FetchSelect(setOrderItems,setRemoveLoading,selectValues)
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
                                    tableid = 'tablecart'
                                    tableNames = {['Code','Product','Amount','Price','Category']}
                                    table = {orderItems}
                                    last = 'delete'
                                    lastButton = '&#128465;'
                                    tableStyle = {styleHome.home}
                                />
                            </>
                        ) : ( <Loading/> ) }
                    </div>
                </div>
            </div>
        </>
    )
}
  
export default Home