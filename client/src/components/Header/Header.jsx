import React from 'react'
import './Header.css'
import { useQuery } from '@apollo/client/react'
import { gql } from '@apollo/client'
import { NavLink, useLocation } from 'react-router-dom'
import useCart from '../../hooks/useCart'
import CartOverlay from '../CartOverlay/CartOverlay'

import logo from '../../assets/VSF.svg'
import cartIcon from '../../assets/Vector.svg'

// Fetch product categories using GraphQL query
const GET_CATEGORIES = gql`
    query GetCategories {
        categories {
            name
        }
    }
`

function Header() {
    // Fetch categories from API
    const { loading, error, data } = useQuery(GET_CATEGORIES);
    // Cart state and actions from context
    const { totalQuantity, isCartOpen, setIsCartOpen } = useCart()
    // Current route location (to highlight the active link, data-testid)
    const location = useLocation()

    return (
        <header className="header">
            <div className="container header-content">

                {/* Nav with categories */}
                <nav className="navigation">
                    {loading && <span>Loading...</span>}
                    {error && <span>Error loading categories</span>}

                    {/* Render category links */}
                    {data && data.categories.map((category) => {
                        const isActive = location.pathname === `/${category.name}`
                        
                        return (
                            <NavLink
                                key={category.name}
                                to={`/${category.name}`}
                                className="nav-link"
                                data-testid={isActive ? 'active-category-link' : 'category-link'}
                            >
                                {category.name.toUpperCase()}
                            </NavLink>
                        )
                    })}
                </nav>

                {/* Logo in the middle of navbar */}
                <div className="logo">
                    <img src={logo} alt="Store Logo" />
                </div>
                    
                {/* Cart icon actions*/}
                <div className="actions">
                    <button 
                    data-testid='cart-btn' 
                    className="cart-icon"
                    onClick={() => setIsCartOpen(!isCartOpen)}
                    >
                        <img src={cartIcon} alt="Cart" />
                        {totalQuantity > 0 && (
                            <span className="cart-count">{totalQuantity}</span>
                        )}
                    </button>
                    
                    {/* Cart overlay */}
                    {isCartOpen && <CartOverlay onClose={() => setIsCartOpen(false)} />}
                </div>
            
            </div>
        </header>
    )
}

export default Header;