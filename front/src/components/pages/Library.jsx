import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleLibrary from './css/Library.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'

function Library () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [library, setLibrary] = useState([])
    const selectValues =  {
        'type':['SimpleWhere'],
        'table':'orders',
        'code':'0',
        'camps':[['code'],['value_total'],['value_tax']],
        'campsAlias':['code','value_total','value_tax'],
        'innerCamps':[],
        'innerCampsAlias':[],
        'innerTables':[],
        'foreignKey':'none',
        'where':'orders.code NOT IN ( SELECT MAX( orders1.code ) FROM orders AS orders1 ) ORDER BY orders.code;'
    }
    useEffect(() => {
        FetchSelect(setLibrary,setRemoveLoading,selectValues)
    }, [])

    const [removeLoadingView, setRemoveLoadingView] = useState(false)
    const [libraryView, setLibraryView] = useState([])
    const selectValuesView =  {
        'type':['SimpleWhere'],
        'table':'orders',
        'code':'0',
        'camps':[['code'],['product_name'],['amount'],['price'],['order_code']],
        'campsAlias':['code','product_name','amount','price','order_code'],
        'innerCamps':[[['value_total'],['value_tax']]],
        'innerCampsAlias':[['value_total','value_tax']],
        'innerTables':['orders'],
        'foreignKey':'none',
        'where':'order_item.order_code = orders.code and order_item.order_code IN ( SELECT CASE WHEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) <>( SELECT MAX( orders2.code ) FROM orders AS orders2 ) THEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) ELSE 0 END AS codeverified ) ORDER BY order_item.code;'
    }
    useEffect(() => {
        FetchSelect(setLibraryView,setRemoveLoadingView,selectValuesView)
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
                    <div className={styles.scroll}>
                        {removeLoading ? (
                            <>
                                <Table 
                                    tableid = 'tablehistory'
                                    tableNames = {['Code','Tax','Total']}
                                    campsNames = {['code','value_total','value_tax']}
                                    table = {library}
                                    last = 'delete'
                                    lastButton = '&#128270;'
                                    tableStyle = {styleLibrary.library}
                                />
                            </>
                        ) : ( <Loading/> ) }
                    </div>
                </div>
                <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
                    <div className={styles.twenty}>
                            {removeLoadingView ? (
                                <>
                                    <Table 
                                        tableid = 'tableviewid'
                                        tableSize = 'half'
                                        tableNames = {['Code','Tax','Total']}
                                        campsNames = {['code','value_total','value_tax']}
                                        table = {libraryView}
                                        tableStyle = {styleLibrary.libraryViewID}
                                    />
                                </>
                            ) : ( <Loading/> ) }
                    </div>
                    <div className={styles.eighty}>
                        <div className={styles.scroll}>
                            {removeLoadingView ? (
                                <>
                                    <Table 
                                        tableid = 'tableview'
                                        tableNames = {['Code','Tax','Total']}
                                        campsNames = {['code','value_total','value_tax']}
                                        table = {libraryView}
                                        tableStyle = {styleLibrary.libraryView}
                                    />
                                </>
                            ) : ( <Loading/> ) }
                        </div>
                    </div>
                </div>
            </div>
        </>
    )
}
  
export default Library