import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import Header from './components/Header/Header'
import ProductList from './components/ProductList/ProductList'
import ProductPage from './components/ProductPage/ProductPage'
import { CartProvider } from './context/CartProvider'

/* Main App component (routing and global state)*/

function App() {

  return (
    /* CartProvider - global state management for the shopping cart */
    <CartProvider>
      <BrowserRouter>
        <Header />
        <main className="container">
          <Routes>
            {/* Website's default route - /all  */}
            <Route path="/" element={<Navigate to="/all"  replace/>}/>

            {/* Route for PLP to filter by category dynamically  */}
            <Route path="/:categoryName" element={<ProductList />} />
            
            {/* Route for PDP to show product based by product id  */}
            <Route path="/product/:id" element={<ProductPage />} />
          </Routes>
        </main>
      </BrowserRouter>
    </CartProvider>
    
  )
}

export default App
