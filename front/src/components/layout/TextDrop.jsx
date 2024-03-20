import styles from './css/TextDrop.module.css'

function TextDrop({leftDescription, rightDescription, iconLeft, iconRight}) {
    return (
        <>
            <div className = {styles.textdropleft}>
                <div title = {leftDescription}>
                    {leftDescription}
                </div>
                <div className = {iconLeft ? (`${styles.icon} ${styles[iconLeft]}`) : styles.icon}>
                    &#9776;
                </div>
            </div>
            <div className = {styles.textdropright}>
                <div title = {rightDescription}>
                    {rightDescription}
                </div>
                <div className = {iconRight ? (`${styles.icon} ${styles[iconRight]}`) : styles.icon}>
                    &#9776;
                </div>
            </div>
        </>
    )
};

export default TextDrop