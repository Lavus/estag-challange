import Table from '../View/Table'
import TextDrop from '../layout/TextDrop'
import Loading from '../layout/Loading'
import { useState, useEffect } from 'react'
import styleLibrary from './css/Library.module.css'
import styles from './css/Pages.module.css'
import FetchSelect from './functions/FetchSelect'

function Library ({css, cssRightFunction, cssLeftFunction}) {
    const [removeLoading, setRemoveLoading] = useState(false)
    const [library, setLibrary] = useState([])
    const [refresh, setRefresh] = useState(false)
    const [removeLoadingView, setRemoveLoadingView] = useState(false)
    const [libraryView, setLibraryView] = useState([])
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

    function FinishFunctionFetchSelect(data){
        setLibrary(data)
        setRemoveLoading(true)
    }

    useEffect(() => {
        setRefresh(false)
        setRemoveLoading(false)
        FetchSelect(selectValues,FinishFunctionFetchSelect)
    }, [refresh])

    function FinishFunctionFetchSelectView(data){
        setLibraryView(data)
        setRemoveLoadingView(true)
    }

    useEffect(() => {
        setRemoveLoadingView(false)
        FetchSelect(selectValuesView,FinishFunctionFetchSelectView)
    }, [selectValuesView])

    function ChangeView(e) {
        (e) => { e.preventDefault() }
        ExecuteRight()
        setSelectValuesView({...selectValuesView, code: e.target.value})
    }

    function ExecuteLeft(){
        cssLeftFunction()
    }

    function ExecuteRight(){
        cssRightFunction()
    }

    return (<>
        <div className = {styles.main}>
            <TextDrop 
                leftDescription = 'View Purchase History'
                rightDescription = 'View Purchase Info'
                iconLeft = {css.iconLeftPage}
                iconRight = {css.iconRightPage}
                functionLeft = {ExecuteLeft}
                functionRight = {ExecuteRight}
            />
            <div className = {css.leftPage ? (`${styles.left} ${styles[css.leftPage]}`) : styles.left}>
                <div className={styles.scroll}>
                    {removeLoading ? (<>
                        {library['broken'] && (
                            alert("There's some problem with the request, please try again."),
                            setLibrary([]),
                            setSelectValuesView({...selectValuesView, code: '0'}),
                            setRefresh(true)
                        )}
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
                    </>) : ( <Loading/> ) }
                </div>
            </div>
            <div className = {css.rightPage ? (`${styles.right} ${styles[css.rightPage]}`) : styles.right}>
                {removeLoadingView ? (<>
                    {((libraryView['broken'])||((Object.keys(libraryView).length == 0)&&(Object.keys(library).length > 0))) && (
                        alert("There's some problem with the request, please try again."),
                        setLibraryView([]),
                        setLibrary([]),
                        setSelectValuesView({...selectValuesView, code: '0'}),
                        setRefresh(true)
                    )}
                    <div className={styles.twenty}>
                        <Table 
                            tableid = 'tableviewid'
                            tableSize = 'half'
                            tableNames = {['Code','Tax','Total']}
                            campsNames = {['order_code','value_tax','value_total']}
                            table = {libraryView['orders'] ? libraryView['orders'] : libraryView}
                            tableStyle = {styleLibrary.libraryViewID}
                        />
                    </div>
                    <div className={styles.eighty}>
                        <div className={styles.scroll}>
                            <Table 
                                tableid = 'tableview'
                                tableNames = {['Product','Price','Amount','Total']}
                                campsNames = {['product_name','price','amount','total']}
                                table = {libraryView['rows'] ? libraryView['rows'] : libraryView}
                                tableStyle = {styleLibrary.libraryView}
                            />
                        </div>
                    </div>
                </> ) : ( <Loading/> ) }
            </div>
        </div>
    </>)
}
  
export default Library