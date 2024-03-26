import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleLibrary from './css/Library.module.css'
import styles from './css/Pages.module.css'

function Library () {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [library, setLibrary] = useState([])
    const [refresh, setRefresh] = useState(false)
    const [selectValues, setSelectValues] =  useState({
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
    })
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
            setLibrary(data)
            setRemoveLoading(true)
        })
    }, [refresh])

    const [removeLoadingView, setRemoveLoadingView] = useState(false)
    const [libraryView, setLibraryView] = useState([])
    const [selectValuesView, setSelectValuesView] =  useState({
        'type':['SimpleWhere','tableview'],
        'table':'order_item',
        'code':'0',
        'camps':[['code'],['product_name'],['amount'],['price'],['order_code']],
        'campsAlias':['code','product_name','amount','price','order_code'],
        'innerCamps':[[['value_total'],['value_tax']]],
        'innerCampsAlias':[['value_total','value_tax']],
        'innerTables':['orders'],
        'foreignKey':'order_code',
        'where':'order_item.order_code = orders.code and order_item.order_code IN ( SELECT CASE WHEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) <>( SELECT MAX( orders2.code ) FROM orders AS orders2 ) THEN ( SELECT MIN( orders1.code ) FROM orders AS orders1 ) ELSE 0 END AS codeverified ) ORDER BY order_item.code;'
    })

    useEffect(() => {
        setRemoveLoadingView(false) 
        fetch('http://localhost/ports/SelectPort.php', {
            method: 'POST',
            headers: {
                'Content-Type' : 'application/json',
                'I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT' : 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='
            },
            body: JSON.stringify(selectValuesView)
        })
        .then((resp) => resp.json())
        .then((data) => {
            setLibraryView(data)
            setRemoveLoadingView(true)
        })
    }, [selectValuesView])

    function ChangeView(e) {
        (e) => { e.preventDefault() }
        setSelectValuesView({...selectValuesView, code: e.target.value})
    }

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
                                    campsNames = {['code','value_tax','value_total']}
                                    table = {library}
                                    last = 'view'
                                    lastButton = '&#128270;'
                                    lastButtonFunction = {ChangeView}
                                    tableStyle = {styleLibrary.library}
                                />
                            </>
                        ) : ( <Loading/> ) }
                    </div>
                </div>
                <div className = {rightPage ? (`${styles.right} ${styles[rightPage]}`) : styles.right}>
                    {removeLoadingView ? (<>
                        {libraryView['broken'] && (
                            setLibraryView([]),
                            setSelectValuesView({...selectValuesView, code: '0'}),
                            setRefresh(true)
                        )}
                        <div className={styles.twenty}>
                                <>
                                    <Table 
                                        tableid = 'tableviewid'
                                        tableSize = 'half'
                                        tableNames = {['Code','Tax','Total']}
                                        campsNames = {['order_code','value_tax','value_total']}
                                        table = {libraryView['orders'] ? libraryView['orders'] : libraryView}
                                        tableStyle = {styleLibrary.libraryViewID}
                                    />
                                </>
                        </div>
                        <div className={styles.eighty}>
                            <div className={styles.scroll}>
                                <>
                                    <Table 
                                        tableid = 'tableview'
                                        tableNames = {['Product','Price','Amount','Total']}
                                        campsNames = {['product_name','price','amount','total']}
                                        table = {libraryView['rows'] ? libraryView['rows'] : libraryView}
                                        tableStyle = {styleLibrary.libraryView}
                                    />
                                </>
                            </div>
                        </div>
                    </> ) : ( <Loading/> ) }
                </div>
            </div>
        </>
    )
}
  
export default Library