import styles from './css/Table.module.css'
import PropTypes from 'prop-types'
import DecodeHtml from './DecodeHtml'

function Table( { tableid, tableNames, table, tableSize, first, firstButton, last, lastButton, tableStyle} ) {

    function ShowTh(){
        return (
            <tr key='first'>
                {tableNames.map((name, index) => (
                    <th key={index} title={name}>{name}</th>
                ))}
            </tr>
        )
    }

    function ShowSingleTd(simpleKey,value,type = 'None',buttonText = 'None',id = 'None'){
        return (
            ((type == 'None') ?
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
                            ((first=='None' && last=='None') ? 
                                ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]))
                            :
                                ((first!='None' && indexcamp==0) ?
                                    ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]),first,DecodeHtml(firstButton),table[keyvalue]["code"])
                                :
                                    ((last!='None' && indexcamp==((Object.keys(table[keyvalue]).length)-1)) ?
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
            <table id={tableid} className = {tableSize ? (`${styles.collapse} ${styles.half} ${tableStyle}`) : (`${styles.collapse} ${styles.lefttext} ${tableStyle}`)}>
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
    firstButton : 'None',
    last : 'None',
    lastButton : 'None'
}

export default Table