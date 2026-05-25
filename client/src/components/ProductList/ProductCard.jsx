import React from 'react';
import { Link } from 'react-router-dom';
import cartIcon from '../../assets/Vector.svg';

function ProductCard({ product, onQuickAdd }) {
    // Generate kebab-case slug from the product name used for data-testid
    const productSlug = product.name.toLowerCase().replace(/\s+/g, '-');

    return (
        <div 
            className={`product-card ${!product.inStock ? 'out-of-stock' : ''}`}
            data-testid={`product-${productSlug}`}
        >
            {/* A link (card) that navigates to the PDP */}
            <Link to={`/product/${product.id}`} className="product-link">
                <div className="image-container">
                    {/* Render the primary product image from the gallery array */}
                    <img src={product.gallery[0]} alt={product.name} />

                    {/* Shows OUT OF STOCK on the card if the product is sold out */}
                    {!product.inStock && (
                        <div className="out-of-stock-overlay">OUT OF STOCK</div>
                    )}
                </div>
                
                {/* Product text details containing brand name, product name, and current price */}
                <div className="product-info">
                    <p className="product-name">{product.brand} {product.name}</p>
                    <p className="product-price">
                        {product.prices[0].currency.symbol}{product.prices[0].amount.toFixed(2)}
                    </p>
                </div>
            </Link>
            
            {/* Quick Add button trigger, shows if the product is in stock */}
            {product.inStock && (
                <button 
                    className="quick-add-btn"
                    onClick={(e) => onQuickAdd(e, product)}
                >
                    <div className="icon-wrapper">
                        <img src={cartIcon} alt="Add to cart" className="cart-icon-img" />
                    </div>
                </button>
            )}
        </div>
    );
}

export default ProductCard;