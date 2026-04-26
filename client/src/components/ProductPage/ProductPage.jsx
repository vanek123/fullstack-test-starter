import React, { useState } from 'react'
import { useParams } from 'react-router-dom'
import { gql } from '@apollo/client'
import { useQuery } from '@apollo/client/react'
import './ProductPage.css'
import { useCart } from '../../context/CartContext'
import parse from 'html-react-parser'

// Fetch all products, graphql query
const GET_ALL_PRODUCTS = gql`
  query GetAllProducts {
    products {
      id
      name
      inStock
      gallery
      description
      brand
      attributes {
        name
        type
        items {
          displayValue
          value
        }
      }
      prices {
        amount
        currency {
          symbol
        }
      }
    }
  }
`;

function ProductPage() {
    const { addToCart, setIsCartOpen } = useCart()

    const { id } = useParams();

    const [mainImage, setMainImage] = useState(0);
    
    const [selectedAttributes, setSelectedAttributes] = useState({});

    const { loading, error, data } = useQuery(GET_ALL_PRODUCTS);

    // Loading and error states
    if (loading) return <div className="container pdp-loading">Loading...</div>;
    if (error) return <div className="container">Error: {error.message}</div>;

    // Find current product by id
    const product = data?.products?.find(p => p.id === id);

    if (!product) return <div className="container">Product "{id}" not found</div>;

    // Check if all attributes are selected
    const allAttributesSelected = product.attributes.every(
        attr => selectedAttributes[attr.name]
    );

    // Handle attribute selection
    const handleAttributeSelect = (attributeName, itemValue) => {
        setSelectedAttributes(prev => ({ ...prev, [attributeName]: itemValue }));
    };

    // Show previous image
    const handlePrevImage = () => {
        setMainImage((prev) => (prev === 0 ? product.gallery.length - 1 : prev - 1));
    };

    // Show next image
    const handleNextImage = () => {
        setMainImage((prev) => (prev === product.gallery.length - 1 ? 0 : prev + 1));
    };

    // Add product to cart
    const handleAddToCart = () => {
        // Prevent adding if not all attributes are selected
        if (!allAttributesSelected) {
            alert('Please select all attributes (Size, Color, etc.) before adding to cart!')
            return
        }

    addToCart(product, selectedAttributes)
    console.log('Added to cart:', product.name, selectedAttributes)
    // Open the cart overlay
    setIsCartOpen(true)
    }

    return (
        <main className="container product-page-container">
            <div className="pdp-layout">
                {/* Thumbnails list */}
                <div className="pdp-thumbnails">
                    {product.gallery.map((img, idx) => (
                        <div 
                            key={idx} 
                            className={`thumb-wrapper ${mainImage === idx ? 'active' : ''}`}
                            onClick={() => setMainImage(idx)}
                        >
                            <img src={img} alt="thumb" />
                        </div>
                    ))}
                </div>

                {/* Main image with slider */}
                <div className="pdp-main-image-container" data-testid="product-gallery">
                    {product.gallery.length > 1 && (
                        <>
                            <button className="slider-arrow prev" onClick={handlePrevImage} aria-label="Previous image">
                                &#10094;
                            </button>
                            <button className="slider-arrow next" onClick={handleNextImage} aria-label="Next image">
                                &#10095;
                            </button>
                        </>
                    )}
                    <img 
                        src={product.gallery[mainImage]} 
                        alt={product.name} 
                        className="pdp-main-image" 
                    />
                </div>

                {/* Product details */}
                <div className="pdp-details">
                    <h1 className="pdp-name">{product.name}</h1>

                    {/* Product attributes (size, color, etc.) */}
                    <div className="pdp-attributes">
                        {product.attributes.map((attr) => {
                            const attrKebab = attr.name.toLowerCase().replace(/\s+/g, '-');
                            
                            return (
                                <div key={attr.name} className="pdp-attribute-block" data-testid={`product-attribute-${attrKebab}`}>
                                    <span className="attr-label">{attr.name.toUpperCase()}:</span>
                                    <div className="attr-items">
                                        {attr.items.map((item) => {
                                            const isSelected = selectedAttributes[attr.name] === item.value;
                                            
                                            const itemValue = item.value;
                                            
                                            return (
                                                <div
                                                    key={item.value}
                                                    className={`${attr.type === 'swatch' ? 'swatch-item' : 'text-item'} ${isSelected ? 'selected' : ''}`}
                                                    style={attr.type === 'swatch' ? { backgroundColor: item.value } : {}}
                                                    onClick={() => handleAttributeSelect(attr.name, item.value)}
                                                    data-testid={`product-attribute-${attrKebab}-${itemValue}`}
                                                >
                                                    {/* Show value only for text attributes */}
                                                    {attr.type !== 'swatch' && item.value}
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    <div className="pdp-price-block">
                        <span className="attr-label">PRICE:</span>
                        <div className="price-value">
                            {product.prices[0].currency.symbol}{(product.prices[0].amount).toFixed(2)}
                        </div>
                    </div>

                    {/* Add to cart button */}
                    <button 
                        className="pdp-add-btn" data-testid="add-to-cart" 
                        disabled={!product.inStock || !allAttributesSelected }
                        onClick={handleAddToCart}
                    >
                        {product.inStock ? 'ADD TO CART' : 'OUT OF STOCK'}
                    </button>

                    <div className="pdp-description" data-testid="product-description" >
                        {parse(product.description)}
                    </div>
                </div>
            </div>
        </main>
    );
}

export default ProductPage