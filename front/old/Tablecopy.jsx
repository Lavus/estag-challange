import styles from './css/Table.module.css'
import PropTypes from 'prop-types'
import DecodeHtml from './DecodeHtml'

function Table( { tableid, tablenames, table, tablesize, first, last} ) {

    function ShowTh(){
        return (
            <tr key='first'>
                {tablenames.map((name, index) => (
                    <th key={index}>{name}</th>
                ))}
            </tr>
        )
    }

    function ShowSimpleTd(simpleKey,value){
        return (
            <td key={simpleKey} title={value}>
                {value}
            </td>
        )
    }

    function ShowContentTd(){
        return (
            <>
                {Object.keys(table).map((keyvalue, indexkey) => (
                    <tr key={'middle'+indexkey}>
                        {Object.keys(table[keyvalue]).map((camp, indexcamp) => (
                            // <td key={indexcamp} title={DecodeHtml(table[keyvalue][camp])} dangerouslySetInnerHTML={{ __html: table[keyvalue][camp] }}/>
                            ((first=='none' && last=='none') ? 
                                <>
                                    {ShowSimpleTd(indexcamp,DecodeHtml(table[keyvalue][camp]))}
                                </> 
                            :
                                ((first!='none' && indexcamp==0) ?
                                    <>
                                        {/* <td key={indexcamp}>
                                            <div title={DecodeHtml(table[keyvalue][camp])}>
                                                {DecodeHtml(table[keyvalue][camp])}
                                            </div>
                                            <button type='submit' name='alterkey' form='alterformcategory' class={(`${styles.extrabt} ${styles[first]}`)} value='code'>
                                                &#9997;
                                            </button>
                                        </td> */}
                                        {ShowSimpleTd(indexcamp,'first')}
                                    </>
                                :
                                    ((last!='none' && indexcamp==((Object.keys(table[keyvalue]).length)-1)) ?
                                        <>
                                            {ShowSimpleTd(indexcamp,'last')}
                                        </>
                                    :
                                        <>
                                            {ShowSimpleTd(indexcamp,DecodeHtml(table[keyvalue][camp]))}
                                        </>
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
                {tablenames.map((name, index) => (
                    <td key={index}></td>
                ))}
            </tr>
        )
    }

    return (
        <>
            <table id={tableid} className = {tablesize ? (`${styles.collapse} ${styles.half}`) : (`${styles.collapse} ${styles.lefttext}`)}>
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
    first : 'none',
    last : 'none'
}

export default Table