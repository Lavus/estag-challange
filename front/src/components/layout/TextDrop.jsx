import styles from './css/TextDrop.module.css'

function TextDrop({leftdescription, rightdescription, iconleft, iconright}) {
  return (
    <>
      <div className = {styles.textdropleft}>
        <div title = {leftdescription}>
          {leftdescription}
        </div>
        <div className = {iconleft ? (`${styles.icon} ${styles[iconleft]}`) : styles.icon}>
          &#9776;
        </div>
      </div>
      <div className = {styles.textdropright}>
        <div title = {rightdescription}>
          {rightdescription}
        </div>
        <div className = {iconright ? (`${styles.icon} ${styles[iconright]}`) : styles.icon}>
          &#9776;
        </div>
      </div>
    </>
  )
};

export default TextDrop;