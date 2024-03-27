import { useState, useEffect } from 'react'
import Input from '../../form/Input'
import styles from '../css/Pages.module.css'

// import styles from './ProjectForm.module.css'

function FormCategories({ handleSubmit, categoryData, buttonText, refreshFunction }) {
    const [category, setCategory] = useState(categoryData)

    const submit = (e) => {
        e.preventDefault()
        handleSubmit(category)
    }

    const refresh = (e) => {
        e.preventDefault()
        refreshFunction()
    }

    useEffect(() => {
        setCategory(categoryData)
    }, [categoryData])

    function handleChange(e) {
        if (['name','tax'].includes(e.target.id)) {
            setCategory({ ...category, [e.target.id]: e.target.value })
        } else {
            setCategory({ ...category, ['error']: e.target.value })
        }
    }

    return (<>
        <form onSubmit={submit}>
            <Input
                type="text"
                name="categoryName"
                id="name"
                placeholder="Category name"
                className = {styles.half}
                maxLength = '255'
                title = 'Names must start with Upper case and need to have 3 or more letters at start, maximum number of characters aceepted is 255.'
                pattern = '^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$'
                required = {true}
                onChange={handleChange}
                value={category.name}
            />
            <Input
                type="number"
                name="tax"
                id="tax"
                step='0.01'
                min='0'
                max='9999.99'
                placeholder='Tax'
                className = {styles.half}
                required = {true}
                onChange={handleChange}
                value={category.tax}
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
                    value='Return to add category'
                />
            </form>
        </>))}
    </>)
}

export default FormCategories