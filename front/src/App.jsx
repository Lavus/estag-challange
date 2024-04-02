import './App.css'
import { BrowserRouter, Route, Routes } from 'react-router-dom'
import NavBar from './components/layout/NavBar'
import Home from './components/pages/Home'
import Products from './components/pages/Products'
import Categories from './components/pages/Categories'
import Library from './components/pages/Library'
// import NoPage from './components/pages/NoPage'
import { useState } from 'react'


function App() {
    const [pageCSS,setPageCSS] = useState({
        topnav : '',
        iconLeftPage : '',
        iconRightPage : 'hidden',
        leftPage : '',
        rightPage : 'show'
    })

    function FocusRight(){
        setPageCSS({ ...pageCSS,
            ['topnav'] : '',
            ['iconLeftPage'] : '',
            ['iconRightPage'] : 'hidden',
            ['leftPage'] : '',
            ['rightPage'] : 'show'
        })
    }

    function FocusLeft(){
        setPageCSS({ ...pageCSS,
            ['topnav'] : '',
            ['iconLeftPage'] : 'hidden',
            ['iconRightPage'] : '',
            ['leftPage'] : 'show',
            ['rightPage'] : ''
        })
    }

    function FocusNavBar(){
        setPageCSS({ ...pageCSS,
            ['topnav'] : ((pageCSS.topnav == 'responsive')?(''):('responsive')),
            ['iconLeftPage'] : '',
            ['iconRightPage'] : '',
            ['leftPage'] : '',
            ['rightPage'] : ''
        })
    }

    return (<>
        <BrowserRouter>
            <Routes>
                <Route path="/" element={<NavBar css={pageCSS} cssFunction={FocusNavBar}/>}>
                    <Route index element={<Home css={pageCSS} cssRightFunction={FocusRight} cssLeftFunction={FocusLeft}/>}/>
                    <Route path="history" element={<Library css={pageCSS} cssRightFunction={FocusRight} cssLeftFunction={FocusLeft}/>}/>
                    <Route path="products" element={<Products css={pageCSS} cssRightFunction={FocusRight} cssLeftFunction={FocusLeft}/>}/>
                    <Route path="categories" element={<Categories css={pageCSS} cssRightFunction={FocusRight} cssLeftFunction={FocusLeft}/>}/>
                    <Route path="*" element={<Home css={pageCSS} cssRightFunction={FocusRight} cssLeftFunction={FocusLeft}/>}/>
                    {/* <Route path="*" element={<NoPage/>}/> */}
                </Route>
            </Routes>
        </BrowserRouter>
    </>)
}

export default App
