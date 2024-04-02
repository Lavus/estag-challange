import styles from './css/Table.module.css'
import PropTypes from 'prop-types'
import DecodeHtml from '../functions/DecodeHtml'

function Table( { tableid, tableNames, campsNames, table, tableSize, first, firstButton, firstButtonFunction, last, lastButton, lastButtonFunction, tableStyle} ) {
    function ShowTh(){
        return (
            <tr key='first'>
                {tableNames.map((name, index) => (
                    <th key={index} title={name}>{name}</th>
                ))}
            </tr>
        )
    }

    function ShowSingleTd(simpleKey,value,type = 'none',buttonText = 'none',id = 'none', buttonFunction = undefined){
        return (
            ((type == 'none') ? (
                <td key={simpleKey} title={value} className={(((tableid == 'tableValues')&&(table[0])) ? ((table[0]['broken'] == "TRUE") ? (styles.redtext) : undefined) : undefined)}>
                    {value}
                </td>
            ) : (
                ((type == 'Th') ? (
                    <th key={simpleKey} title={value}>
                        {value}
                    </th>
                ):(
                    <td key={simpleKey}>
                        <div title={value}>
                            {value}
                        </div>
                        <button type='button' name={type+'key'} className={(`${styles.extrabt} ${styles[type]}`)} value={id} onClick={buttonFunction}>
                            {buttonText}
                        </button>
                    </td>
                ))
            ))
        )
    }

    function ShowContentTd(){
        return (
            <>
                {Object.keys(table).map((keyvalue, indexkey) => (
                    <tr key={'middle'+indexkey} className={((tableid == 'tablecart') && table[keyvalue]['code'][1] == "Broken") ? (styles.rederror) : undefined}>
                        {campsNames.map((camp, indexcamp) => (
                            ((first=='none' && last=='none') ?
                                ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]))
                            :
                                ((first!='none' && indexcamp==0) ?
                                    ((tableid == 'tablecart') ?
                                        ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]),first,DecodeHtml(firstButton),table[keyvalue]["code"][0],firstButtonFunction)
                                    :
                                        ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]),first,DecodeHtml(firstButton),table[keyvalue]["code"],firstButtonFunction)
                                    )
                                :
                                    ((last!='none' && indexcamp==((campsNames.length)-1)) ?
                                        ((tableid == 'tablecart') ?
                                            ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]),last,DecodeHtml(lastButton),table[keyvalue]["code"][0],lastButtonFunction)
                                        :
                                            ShowSingleTd(indexcamp,DecodeHtml(table[keyvalue][camp]),last,DecodeHtml(lastButton),table[keyvalue]["code"],lastButtonFunction)
                                        )
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

    function ShowContentValues(){
        return (<>
            {campsNames.map((camp, indexcamp) => (
                <tr key={indexcamp}>
                    {ShowSingleTd('th'+indexcamp,DecodeHtml(tableNames[indexcamp]),'Th')}
                    {ShowSingleTd( indexcamp, ( (table[0]) ? (DecodeHtml(table[0][camp])) : ('$0.00') ) )}
                </tr>
            ))}
        </>)
    }

    function ShowEmptyTd(empty){
        return (
            <tr key='last' className={!(empty) ? styles.last : undefined}>
                {tableNames.map((name, index) => (
                    <td key={index}></td>
                ))}
            </tr>
        )
    }

    return (<>
        {(tableid == 'tableValues') ? (<>
            <table id={tableid} className = {(`${styles.lefttext} ${styles.floatright}`)}>
                <tbody>
                    {ShowContentValues()}
                </tbody>
            </table>
        </>) : (<>
            <table id={tableid} className = {tableSize ? (`${styles.collapse} ${styles.half} ${tableStyle}`) : ((tableid != 'tableFinish')? (`${styles.collapse} ${styles.lefttext} ${tableStyle}`):undefined)}>
                <tbody>
                    {ShowTh()}
                    {ShowContentTd()}
                    {((table.length == 0)&&(tableid == 'tableviewid')) ? (ShowEmptyTd('True')) : (!(tableid == 'tableviewid')  &&  ShowEmptyTd())}
                </tbody>
            </table>
        </>)}
    </>)
}

Table.propsTypes = {
    first : PropTypes.string,
    last : PropTypes.string
}

Table.defaultProps = {
    first : 'none',
    firstButton : 'none',
    last : 'none',
    lastButton : 'none'
}

export default Table