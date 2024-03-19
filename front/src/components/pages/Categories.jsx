import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import styles from './css/Pages.module.css'
import './css/Categories.module.css'

function Categories () {
    let leftdescriptionpage = 'View insert category'
    let rightdescriptionpage = 'View Categories'
    let iconleftpage = ''
    let iconrightpage = 'hidden'
    let leftpage = ''
    let rightpage = 'show'
    return (
        <>
            <div className = {styles.main}>
                <TextDrop 
                    leftdescription = {leftdescriptionpage}
                    rightdescription = {rightdescriptionpage}
                    iconleft = {iconleftpage}
                    iconright = {iconrightpage}
                />
                <div className = {leftpage ? (`${styles.left} ${styles[leftpage]}`) : styles.left}>
                    <form id='formcategories' action='addcategory.php' method='post'>
                        <input type='text' id='categoryname' name='categoryname' placeholder='Category name' className = 'half' maxLength='255' title='Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.' pattern='^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$' required/>
                        <input type='number' id='tax' name='tax' step='0.01' min='0' max='9999.99' placeholder='Tax' className = 'half' required/>
                        <input type='submit' value='Add Category' className = 'bluebold full'/>
                    </form>
                </div>
                <div className = {rightpage ? (`${styles.right} ${styles[rightpage]}`) : styles.right}>
                    <div className={styles.scroll}>
                        <Table 
                            tableid = 'tablecategories'
                            tablenames = {["Code","Category","Tax"]}
                        />
                    </div>
                </div>
            </div>
        </>
    )
}
  
export default Categories