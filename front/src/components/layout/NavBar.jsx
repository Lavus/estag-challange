import { Outlet, Link } from "react-router-dom";
import styles from './css/NavBar.module.css'

function NavBar() {
  return (
    <>
        <div className={styles.topnav} id="myTopnav">
            <Link className={styles.selectedpage} to="/">Suite Store</Link>
            <Link to="/products">Products</Link>
            <Link to="/categories">Categories</Link>
            <Link to="/history">History</Link>
            <Link to="#" className={styles.icon}>&#9776;</Link>
        </div>
        <Outlet />
    </>
  )
};

export default NavBar;