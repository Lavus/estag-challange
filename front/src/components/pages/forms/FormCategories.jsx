import { useState, useEffect } from 'react'
import Input from '../../form/Input'
import styles from '../css/Pages.module.css'

// import styles from './ProjectForm.module.css'

function FormCategories({ handleSubmit, categoryData }) {
    const [category, setCategory] = useState(categoryData || {'name':'','tax':''})

    const submit = (e) => {
        e.preventDefault()
        handleSubmit(category)
    }

    function handleChange(e) {
        setCategory({ ...category, [e.target.id]: e.target.value })
    }

    return (
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
                value='Add Category'
            />
        </form>
    )
}

export default FormCategories