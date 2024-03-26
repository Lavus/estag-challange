import styles from './css/Table.module.css'
import PropTypes from 'prop-types'
import DecodeHtml from './DecodeHtml'
import { useState } from 'react'

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

    function ShowSingleTd(simpleKey,value,type = 'none',buttonText = 'none',id = 'none', buttonFunction = 'none'){
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
                    <button type='button' name={type+'key'} className={(`${styles.extrabt} ${styles[type]}`)} value={id} onClick={buttonFunction}>
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
                                    ((last!='none' && indexcamp==((Object.keys(table[keyvalue]).length)-1)) ?
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

    function ShowEmptyTd(empty){
        return (
            <tr key='last' className={!(empty) ? styles.last : undefined}>
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
                    {((table.length == 0)&&(tableid == 'tableviewid')) ? (ShowEmptyTd('True')) : (!(tableid == 'tableviewid')  &&  ShowEmptyTd())}
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
    first : 'none',
    firstButton : 'none',
    last : 'none',
    lastButton : 'none'
}

export default Table