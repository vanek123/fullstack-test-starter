import React from 'react'
import './ProductList.css'
import { useQuery } from '@apollo/client/react'
import { gql } from '@apollo/client'
import { Link, useParams } from 'react-router-dom'
import { useCart } from '../../context/CartContext'

import cartIcon from '../../assets/Vector.svg'

// Fetch all products
const GET_PRODUCTS = gql`
  query GetProducts {
    products {
      id
      name
      inStock
      gallery
      brand
      category 
      attributes {
        name
        items {
          value
        }  
      }
      prices {
        amount
        currency {
          symbol
          label
        }
      }
    }
  }
`

function ProductList() {
    const { categoryName } = useParams()
    const { loading, error, data } = useQuery(GET_PRODUCTS)
    const { addToCart, setIsCartOpen } = useCart()

    // Loading and error states
    if (loading) return <div className="container" style={{marginTop: '100px'}}> Loading products...</div>
    if (error) return <div className="container" style={{marginTop: '100px'}}>Error! {error.message}</div>

    // Format category title, first letter capitalized
    const displayTitle = categoryName.charAt(0).toUpperCase() + categoryName.slice(1)

    const allProducts = data?.products || [];

    // Filter products by category or show all
    const filteredProducts = categoryName === 'all'
        ? allProducts
        : allProducts.filter(p => p.category === categoryName);

    // Quick add to cart with default attributes (for round green button)
    const handleQuickAdd = (e, product) => {
        e.preventDefault()
        e.stopPropagation()

        // Select first available attribute values by default
        const defaultAttributes = product.attributes.reduce((acc, attr) => {
            acc[attr.name] = attr.items[0].value
            return acc
        }, {})

        addToCart(product, defaultAttributes)
        setIsCartOpen(true)
    }

    return (
        <main className="container product-list-page">
            {/* Display category title */}
            <h2 className="category-title">{displayTitle}</h2>
            
            <div className="product-grid">
                {filteredProducts.map((product) => {
                    const productSlug = product.name.toLowerCase().replace(/\s+/g, '-');
                    
                    return (
                        <div 
                            key={product.id} 
                            className={`product-card ${!product.inStock ? 'out-of-stock' : ''}`}
                            data-testid={`product-${productSlug}`}
                        >
                            <Link to={`/product/${product.id}`} style={{ textDecoration: 'none', color: 'inherit' }}>
                            
                                <div className="image-container">
                                    <img src={product.gallery[0]} alt={product.name} />

                                    {!product.inStock && (
                                        <div className="out-of-stock-overlay">OUT OF STOCK</div>
                                    )}

                                </div>
                                
                                {/* Product info */}
                                <div className="product-info">
                                    <p className="product-name">{product.brand} {product.name}</p>
                                    <p className="product-price">
                                        {product.prices[0].currency.symbol}{product.prices[0].amount.toFixed(2)}
                                    </p>
                                </div>

                            </Link>

                            {/* Quick add button (only for products that are in stock) */}
                            {product.inStock && (
                                <button className="quick-add-btn"
                                    className="quick-add-btn"
                                    onClick={(e) => handleQuickAdd(e, product)}
                                >
                                    <div className="icon-wrapper">
                                        <img src={cartIcon} alt="Add to cart" className="cart-icon-img" />
                                    </div>
                                </button>
                            )}

                        </div>
                    );
                })}
            </div>
        </main>
    )
}

export default ProductList;