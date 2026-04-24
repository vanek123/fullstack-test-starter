import React from 'react'
import './ProductList.css'
import { useQuery } from '@apollo/client/react'
import { gql } from '@apollo/client'
import { Link, useParams } from 'react-router-dom';

import cartIcon from '../../assets/Vector.svg';

const GET_PRODUCTS = gql`
  query GetProducts {
    products {
      id
      name
      inStock
      gallery
      brand
      category 
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

    if (loading) return <div className="container" style={{marginTop: '100px'}}> Loading products...</div>
    if (error) return <div className="container" style={{marginTop: '100px'}}>Error! {error.message}</div>

    const displayTitle = categoryName.charAt(0).toUpperCase() + categoryName.slice(1)

    const allProducts = data?.products || [];

    const filteredProducts = categoryName === 'all'
        ? allProducts
        : allProducts.filter(p => p.category === categoryName);

    return (
        <main className="container product-list-page">
            <h2 className="category-title">{displayTitle}</h2>
            
            <div className="product-grid">
                {filteredProducts.map((product) => (
                    <div key={product.id} className={`product-card ${!product.inStock ? 'out-of-stock' : ''}`}>
                        <Link to={`/product/${product.id}`} style={{ textDecoration: 'none', color: 'inherit' }}>
                        
                            <div className="image-container">
                                <img src={product.gallery[0]} alt={product.name} />

                                {!product.inStock && (
                                    <div className="out-of-stock-overlay">OUT OF STOCK</div>
                                )}

                            </div>

                            <div className="product-info">
                                <p className="product-name">{product.brand} {product.name}</p>
                                <p className="product-price">
                                    {product.prices[0].currency.symbol}{product.prices[0].amount}
                                </p>
                            </div>

                        </Link>

                        {product.inStock && (
                            <button className="quick-add-btn">
                                <div className="icon-wrapper">
                                    <img src={cartIcon} alt="Add to cart" className="cart-icon-img" />
                                </div>
                            </button>
                        )}

                    </div>
                ))}
            </div>
        </main>
    )
}

export default ProductList;