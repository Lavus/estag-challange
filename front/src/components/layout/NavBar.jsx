import { Outlet, Link } from "react-router-dom";
import styles from './css/NavBar.module.css'

function NavBar({ css, cssFunction }) {
    function changeCss(e){
        e.preventDefault()
        cssFunction()
    }
    return (<>
        <div className={((css.topnav == 'responsive')?(`${styles.topnav} ${styles.responsive}`):(`${styles.topnav}`))}>
            <Link className={styles.selectedpage} to="/">Suite Store</Link>
            <Link to="/products">Products</Link>
            <Link to="/categories">Categories</Link>
            <Link to="/history">History</Link>
            <Link to="#" className={styles.icon} onClick={changeCss}>&#9776;</Link>
        </div>
        <Outlet />
    </>)
};

export default NavBar