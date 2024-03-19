import styles from './css/Table.module.css'
import { useState, useEffect } from 'react'
import Loading from '../layout/Loading'

function Table( { tableid, tablenames, valueid, tablesize } ) {

    const [categories, setCategories] = useState([])
    const [removeLoading, setRemoveLoading] = useState(false)

    useEffect(() => {
    // Para ver o loading
        setTimeout(
            () =>
                fetch('http://localhost/ports/SelectPort.php', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'I2s2zuzhGSbPsSKjmYN1DOO8T678wI6ZBKpE4oWtWN7vjpqgjZfbLs5h3Wy950o9K6nt':'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='
                    }
                })
                .then((resp) => resp.json())
                .then((data) => {
                    setCategories(data)
                    setRemoveLoading(true)
                }),
            100,
        )
    }, [])

    function ShowTh(){
        return (
            <tr key='first'>
                {tablenames.map((name, index) => (
                    <th key={index}>{name}</th>
                ))}
            </tr>
        )
    }

    function ShowContentTd(){
        return (
            <>
                {Object.keys(categories).map((key, indexkey) => (
                    <tr key={'middle'+indexkey}>
                        {Object.keys(categories[key]).map((camp, indexcamp) => (
                            // <td key={indexcamp} title={categories[key][camp][1]} dangerouslySetInnerHTML={{ __html: categories[key][camp][0] }}/>
                            <td key={indexcamp} title={categories[key][camp][1]}>
                                {(categories[key][camp][1])}
                            </td>
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

export default Table