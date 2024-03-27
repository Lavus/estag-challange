// import styles from './SubmitButton.module.css'

function Button({ text, className, onClick }) {
    return (
        <button
            className={className}
            onClick={onClick}
        >
            {text}
        </button>
    )
}

export default Button
