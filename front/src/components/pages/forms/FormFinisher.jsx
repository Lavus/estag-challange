import Input from '../../form/Input'
import styles from '../css/Pages.module.css'

function FormFinisher({ handleFinish, handleCancel }) {

    const finish = (e) => {
        e.preventDefault()
        handleFinish()
    }

    const cancel = (e) => {
        e.preventDefault()
        handleCancel()
    }

    return (<>
        <form onSubmit={finish}>
            <Input
                type="submit"
                className = {(`${styles.bluebold} ${styles.quarter}`)}
                value="Finish"
            />
        </form>
        <form onSubmit={cancel}>
            <Input
                type="submit"
                className = {(`${styles.cancel} ${styles.quarter}`)}
                value="Cancel"
            />
        </form>

    </>)
}

export default FormFinisher