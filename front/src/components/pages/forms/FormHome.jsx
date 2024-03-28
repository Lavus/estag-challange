import { useState, useEffect } from 'react'
import Input from '../../form/Input'
import styles from '../css/Pages.module.css'
import DropDown from '../../form/Dropdown'

function FormHome({ handleSubmit, productData, categoriesData, buttonText, refreshFunction, placeHolderAmount, maxAmount }) {
    const [product, setProduct] = useState(productData)
    const [categories, setCategories] = useState(categoriesData)

    const submit = (e) => {
        e.preventDefault()
        handleSubmit(product)
    }

    const refresh = (e) => {
        e.preventDefault()
        refreshFunction()
    }

    useEffect(() => {
        setProduct(productData)
    }, [productData])

    useEffect(() => {
        setCategories(categoriesData)
    }, [categoriesData])

    function handleChangeDropdown(value) {
        setProduct({ ...product, ['category']: value })
    }

    function handleChange(e) {
        if (['amount'].includes(e.target.id)) {
            setProduct({ ...product, [e.target.id]: e.target.value })
        } else {
            setProduct({ ...product, ['error']: e.target.value })
        }
    }

    return (<>
        <form onSubmit={submit}>
            <DropDown
                defaultTextNone = 'No categories avaliable'
                defaultText = 'Product'
                tableValues = {categories}
                valueFunction = {handleChangeDropdown}
                sizeStyle = 'full'
                code={product.category}
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
                disabled = {true}
                onChange={handleChange}
                title = {product.amount}
                value = {product.amount}
            />
            <Input
                type="text"
                name="taxvalue"
                id="tax"
                placeholder="Tax"
                className = {styles.quarter}
                disabled = {true}
                onChange={handleChange}
                title = {product.tax}
                value = {product.tax}
            />
            <Input
                type="text"
                name="unitprice"
                id="price"
                placeholder="Unit price"
                className = {styles.quarter}
                disabled = {true}
                onChange={handleChange}
                value={product.price}
            />
            <Input
                type="submit"
                className = {(`${styles.bluebold} ${styles.full}`)}
                value={buttonText}
            />
        </form>
        {((refreshFunction) && (<>
            <form onSubmit={refresh}>
                <Input
                    type = "submit"
                    className = {(`${styles.bluebold} ${styles.full}`)}
                    value='Return to add product'
                />
            </form>
        </>))}
    </>)
}

export default FormHome