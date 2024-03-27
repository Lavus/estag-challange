import './App.css'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import NavBar from './components/layout/NavBar'
import Home from './components/pages/Home'
import Products from './components/pages/Products'
import Categories from './components/pages/Categories'
import Library from './components/pages/Library'
import NoPage from './components/pages/NoPage'


function App() {
    return (<>
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<NavBar/>}>
                    <Route index element={<Home/>}/>
                    <Route path="history" element={<Library/>}/>
                    <Route path="products" element={<Products/>}/>
                    <Route path="categories" element={<Categories/>}/>
                    <Route path="*" element={<NoPage/>}/>
                </Route>
            </Routes>
        </BrowserRouter>
    </>)
}

export default App
