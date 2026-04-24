import { useState } from 'react'
import './App.css'
import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom'
import Header from './components/Header/Header'
import ProductList from './components/ProductList/ProductList'
import ProductPage from './components/ProductPage/ProductPage'
import { CartProvider } from './context/CartContext'

function App() {

  return (
    <CartProvider>
      <BrowserRouter>
        <Header />
        <main className="container">
          <Routes>
            <Route path="/" element={<Navigate to="/category/all"  replace/>}></Route>
            <Route path="/category/:categoryName" element={<ProductList />}></Route>
            
            <Route path="/product/:id" element={<ProductPage />} />
          </Routes>
        </main>
      </BrowserRouter>
    </CartProvider>
    
  )
}

export default App
