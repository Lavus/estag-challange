import { useEffect, useState } from 'react'
import DecodeHtml from '../functions/DecodeHtml'
import styles from './css/Dropdown.module.css'

function DropDown({ defaultTextNone, defaultText, tableValues, valueFunction, sizeStyle, code }) {
    const [dropdownText,setDropdownText] = useState(defaultText)
    const [dropdownSelected,setDropdownSelected] = useState(false)
    useEffect(() => {
        ((code) && (
            setDropdownText(DecodeHtml(tableValues[code]['name'])),
            setDropdownSelected(true)
         ))
    }, [code])

    function ChangeTextPlusValue(e){
        e.preventDefault()
        setDropdownText(e.target.title)
        setDropdownSelected(true)
        valueFunction(e.target.id)
    }

    return (<>
        <div className={((Object.keys(tableValues).length > 0) ? (`${styles.dropdown} ${styles[sizeStyle]}`) : (`${styles.dropdown} ${styles[sizeStyle]} ${styles.none}`) )}>
            <div className={styles.dropbtn}>
                <div className={((dropdownSelected) ? (`${styles.dropdowntext} ${styles.dropdownselected}`) : (`${styles.dropdowntext}`))} title={((Object.keys(tableValues).length > 0) ? dropdownText : defaultTextNone )}>{((Object.keys(tableValues).length > 0) ? dropdownText : defaultTextNone )}</div>
                <div className={styles.positiondropright}>
                    <div className={(`${styles.arrow} ${styles.adown}`)}></div>
                </div>
            </div>
            <div className={styles.dropdownContent}>
                {((Object.keys(tableValues).length > 0) && 
                    Object.keys(tableValues).map(keyValue => (
                        <div title={DecodeHtml(tableValues[keyValue]['name'])} onClick={ChangeTextPlusValue} key={tableValues[keyValue]['code']} id={tableValues[keyValue]['code']} className={styles.object}>
                            {DecodeHtml(tableValues[keyValue]['name'])}
                        </div>
                    ))
                )}
            </div>
        </div>
    </>)
}

export default DropDown