import { useState, useEffect } from 'react'
import Input from '../../form/Input'
import styles from '../css/Pages.module.css'
import DropDown from '../../form/Dropdown'

function FormProducts({ handleSubmit, productData, categoriesData, buttonText, refreshFunction }) {
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
        if (['name','price','amount'].includes(e.target.id)) {
            setProduct({ ...product, [e.target.id]: e.target.value })
        } else {
            setProduct({ ...product, ['error']: e.target.value })
        }
    }

    return (<>
        <form onSubmit={submit}>
            <Input
                type="text"
                name="productName"
                id="name"
                placeholder="Product name"
                className = {styles.full}
                maxLength = '255'
                title = 'Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.'
                pattern = '^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$'
                required = {true}
                onChange={handleChange}
                value={product.name}
            />
            <DropDown
                defaultTextNone = 'No categories avaliable'
                defaultText = 'Category'
                tableValues = {categories}
                valueFunction = {handleChangeDropdown}
                sizeStyle = 'half'
                code={product.category}
            />
            <Input
                type="number"
                name="unitPrice"
                id="price"
                step='0.01'
                min='0.01'
                max='9999999999.99'
                placeholder='Price'
                className = {styles.quarter}
                required = {true}
                onChange={handleChange}
                value={product.price}
            />
            <Input
                type="number"
                name="amount"
                id="amount"
                step='1'
                min={((refreshFunction) ? ('0') : ('1'))}
                placeholder='Amount'
                className = {styles.quarter}
                required = {true}
                onChange={handleChange}
                value={product.amount}
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

export default FormProducts