import styles from './css/Table.module.css'
import PropTypes from 'prop-types'
import DecodeHtml from './DecodeHtml'

function Table( { tableid, tableNames, table, tableSize, first, firstButton, last, lastButton} ) {

    function ShowTh(){
        return (
            <tr key='first'>
                {tableNames.map((name, index) => (
                    <th key={index}>{name}</th>
                ))}
            </tr>
        )
    }

    function ShowSingleTd(simpleKey,value,type = 'none',buttonText = 'none',id = 'none'){
        return (
            ((type == 'none') ?
                <td key={simpleKey} title={value}>
                    {value}
                </td>
            :
                <td key={simpleKey}>
                    <div title={value}>
                        {value}
                    </div>
                    <button type='submit' name={type+'key'} form={type+'form'+tableid} className={(`${styles.extrabt} ${styles[type]}`)} value={id}>
                        {buttonText}
                    </button>
                </td>
            )
        )
    }

    function ShowContentTd(){
        return (
            <>
                {Object.keys(table).map((keyvalue, indexkey) => (
                    <tr key={'middle'+indexkey}>
                        {Object.keys(table[keyvalue]).map((camp, indexcamp) => (
                            // <td key={indexcamp} title={DecodeHtml(table[key][camp])} dangerouslySetInnerHTML={{ __html: table[key][camp] }}/>
                            ((first=='none' && last=='none') ? 
                                ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]))
                            :
                                ((first!='none' && indexcamp==0) ?
                                    ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]),first,DecodeHtml(firstButton),table[keyvalue]["code"])
                                :
                                    ((last!='none' && indexcamp==((Object.keys(table[keyvalue]).length)-1)) ?
                                        ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]),last,DecodeHtml(lastButton),table[keyvalue]["code"])
                                    :
                                        ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]))
                                    )
                                )
                            )
                        ))}
                    </tr>
                ))}
            </>
        )
    }

    function ShowLastTd(){
        return (
            <tr key='last' className={styles.last}>
                {tableNames.map((name, index) => (
                    <td key={index}></td>
                ))}
            </tr>
        )
    }

    return (
        <>
            <table id={tableid} className = {tableSize ? (`${styles.collapse} ${styles.half}`) : (`${styles.collapse} ${styles.lefttext}`)}>
                <tbody>
                    {ShowTh()}
                    {ShowContentTd()}
                    {ShowLastTd()}
                </tbody>
            </table>
        </>
    )
}

Table.propsTypes = {
    first : PropTypes.string,
    last : PropTypes.string
}

Table.defaultProps = {
    first : 'None',
    last : 'None'
}

export default Table