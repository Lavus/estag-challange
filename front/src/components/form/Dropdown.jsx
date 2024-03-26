// import styles from './.module.css'

function DropDown({ type, name, id, placeholder, onChange, value, className, maxLength, title, pattern, required, step, min, max }) {
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
            step={step}
            min={min}
            max={max}
        />
    )
}

export default DropDown