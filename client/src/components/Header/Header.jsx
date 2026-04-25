import React from 'react'
import './Header.css'
import { useQuery } from '@apollo/client/react'
import { gql } from '@apollo/client'
import { NavLink } from 'react-router-dom';
import { useCart } from '../../context/CartContext'
import CartOverlay from '../CartOverlay/CartOverlay'

import logo from '../../assets/VSF.svg'
import cartIcon from '../../assets/Vector.svg'

const GET_CATEGORIES = gql`
    query GetCategories {
        categories {
            name
        }
    }
`


function Header() {
    const { loading, error, data } = useQuery(GET_CATEGORIES);
    const { totalQuantity, isCartOpen, setIsCartOpen } = useCart()

    return (
        <header className="header">
            <div className="container header-content">
                <nav className="navigation">
                    {loading && <span>Loading...</span>}
                    {error && <span>Error loading categories</span>}

                    {data && data.categories.map((category) => (
                        <NavLink
                            key={category.name}
                            to={`/category/${category.name}`}
                            className="nav-link"
                            data-testid={category.name === data.categories[0].name ? 'active-category-link' : 'category-link'}
                        >
                            {category.name.toUpperCase()}
                        </NavLink>
                    ))}
                </nav>

                <div className="logo">
                    <img src={logo} alt="Store Logo" />
                </div>
            
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

                    {isCartOpen && <CartOverlay onClose={() => setIsCartOpen(false)} />}
                </div>
            
            </div>
        </header>
    )
}

export default Header;