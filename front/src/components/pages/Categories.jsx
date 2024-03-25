import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import styles from './css/Pages.module.css'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleCategories from './css/Categories.module.css'
import FetchSelect from './functions/FetchSelect'

function Categories () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [categories, setCategories] = useState([])
    const selectValues =  {
        'type':['FullSimple'],
        'table':'categories',
        'code':'0',
        'camps':[['code'],['name'],['tax']],
        'campsAlias':['code','name','tax']
    }
    useEffect(() => {
        FetchSelect(setCategories,setRemoveLoading,selectValues)
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
                    <form id='formCategories' action='addcategory.php' method='post'>
                        <input type='text' id='categoryName' name='categoryName' placeholder='Category name' className = 'half' maxLength='255' title='Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.' pattern='^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$' required/>
                        <input type='number' id='tax' name='tax' step='0.01' min='0' max='9999.99' placeholder='Tax' className = 'half' required/>
                        <input type='submit' value='Add Category' className = 'bluebold full'/>
                    </form>
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