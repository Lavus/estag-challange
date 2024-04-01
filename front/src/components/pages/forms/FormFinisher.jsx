import Input from '../../form/Input'
import styles from '../css/Pages.module.css'

function FormFinisher({ handleFinish, handleCancel }) {

    const submit = (e) => {
        e.preventDefault()
        handleSubmit(cartItem)
    }

    const refresh = (e) => {
        e.preventDefault()
        refreshFunction()
    }

    return (<>
        <form onSubmit={handleFinish}>
            <Input
                type="submit"
                className = {(`${styles.bluebold} ${styles.quarter}`)}
                value="Finish"
            />
        </form>
        <form onSubmit={handleCancel}>
            <Input
                type="submit"
                className = {(`${styles.cancel} ${styles.quarter}`)}
                value="Cancel"
            />
        </form>

    </>)
}

export default FormFinisher