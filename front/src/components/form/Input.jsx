// import styles from './.module.css'

function Input({ type, name, id, placeholder, onChange, value, className, maxLength, title, pattern, required, disabled, step, min, max }) {
    return (
        <input
            type={type}
            name={name}
            id={id}
            placeholder={placeholder}
            onChange={onChange}
            value={value}
            className={className}
            maxLength={maxLength}
            title={title}
            pattern={pattern}
            required={required}
            disabled={disabled}
            step={step}
            min={min}
            max={max}
        />
    )
}

export default Input