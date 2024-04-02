import { useState, useEffect } from 'react'
import Input from '../../form/Input'
import styles from '../css/Pages.module.css'
import DropDown from '../../form/Dropdown'
import DecodeHtml from '../../functions/DecodeHtml'

function FormHome({ handleSubmit, cartItemData, productsData, buttonText, refreshFunction, refreshTriggerFunction }) {
    const [cartItem, setCartItem] = useState(cartItemData)
    const [products, setProducts] = useState(productsData)
    const [placeHolderAmount,setPlaceHolderAmount] = useState('Amount')
    const [maxAmount,setMaxAmount] = useState('')
    const [amountValue,setAmountValue] = useState('')
    const [disabledAmount,setDisabledAmount] = useState(true)
    const [price,setPrice] = useState('')
    const [tax,setTax] = useState('')

    const submit = (e) => {
        e.preventDefault()
        handleSubmit(cartItem)
    }

    const refresh = (e) => {
        e.preventDefault()
        refreshFunction()
    }

    useEffect(() => {
        setCartItem(cartItemData)
    }, [cartItemData])

    useEffect(() => {
        setProducts(productsData)
    }, [productsData])


    function handleChangeDropdown(value) {
        if (products.hasOwnProperty(value)) {
            setPrice(DecodeHtml(products[value].price))
            setTax(DecodeHtml(products[value].tax))
            let maxAmountAvaliable = ((DecodeHtml(products[value].amount)) - (DecodeHtml(products[value].products_amount)))
            setMaxAmount(maxAmountAvaliable)
            if (maxAmountAvaliable > 0){
                setPlaceHolderAmount('Amount')
                setDisabledAmount(false)
                setAmountValue('')
                setCartItem({ ...cartItem, ['product'] : value })
            } else {
                setPlaceHolderAmount('No stock left')
                setDisabledAmount(true)
                setAmountValue('')
                setCartItem({ ...cartItem, ['amount']: 'False', ['product'] : value  })
            }
        } else {
            refreshFunction()
        }
    }

    function handleChange(e) {
        if (['amount'].includes(e.target.id)) {
            setCartItem({ ...cartItem, [e.target.id]: e.target.value })
            setAmountValue(e.target.value)
        } else {
            setCartItem({ ...cartItem, ['error']: e.target.value })
        }
    }

    return (<>
        <form onSubmit={submit}>
            <DropDown
                defaultTextNone = 'No product avaliable at the moment'
                defaultText = 'Product'
                tableValues = {products}
                valueFunction = {handleChangeDropdown}
                sizeStyle = 'full'
                code={cartItem.product}
            />
            <Input
                type="number"
                name="amount"
                id="amount"
                placeholder = {placeHolderAmount}
                className = {styles.half}
                min='1'
                max = {maxAmount}
                required = {true}
                disabled = {disabledAmount}
                onChange={handleChange}
                value = {amountValue}
            />
            <Input
                type="text"
                name="taxvalue"
                id="tax"
                placeholder="Tax"
                className = {styles.quarter}
                disabled = {true}
                title = {tax}
                value = {tax}
            />
            <Input
                type="text"
                name="unitprice"
                id="price"
                placeholder="Unit price"
                className = {styles.quarter}
                disabled = {true}
                title={price}
                value={price}
            />
            <Input
                type="submit"
                className = {(`${styles.bluebold} ${styles.full}`)}
                value={buttonText}
            />
        </form>
        {((refreshTriggerFunction) && (<>
            <form onSubmit={refresh}>
                <Input
                    type = "submit"
                    className = {(`${styles.bluebold} ${styles.full}`)}
                    value='Return to add cartItem'
                />
            </form>
        </>))}
    </>)
}

export default FormHome