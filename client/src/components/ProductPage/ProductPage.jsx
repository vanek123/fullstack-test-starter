import React, { useState } from 'react';
import { useParams } from 'react-router-dom';
import { gql } from '@apollo/client';
import { useQuery } from '@apollo/client/react';
import './ProductPage.css';
import useCart from '../../hooks/useCart';
import parse from 'html-react-parser';
import ImageGallery from './ImageGallery';
import AttributeSelector from './AttributeSelector';

// Fetch a single product details by id (avoid fetching all products)
const GET_PRODUCT = gql`
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      gallery
      description
      brand
      category
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
          label
        }
      }
    }
  }
`;

function ProductPage() {
    // Get cart methods and state from custom hook
    const { addToCart, setIsCartOpen } = useCart();
    
    // Extract product id from the router URL parameters
    const { id } = useParams();
    
    // State to keep track of selected attributes (for example { Size: "M", Color: "Red"})
    const [selectedAttributes, setSelectedAttributes] = useState({});

    // Fetch product data using the extracted id
    const { loading, error, data } = useQuery(GET_PRODUCT, {
        variables: { id }
    });

    // Loading and error states
    if (loading) return <div className="container pdp-loading">Loading...</div>;
    if (error) return <div className="container">Error: {error.message}</div>;

    
    const product = data?.product;
    // If product was not found in the database
    if (!product) return <div className="container">Product "{id}" not found</div>;

    // Check if user selected every available attribute for this product
    const allAttributesSelected = product.attributes.every(
        attr => selectedAttributes[attr.name]
    );

    // Update the selected attributes state when user clicks on a size/color box
    const handleAttributeSelect = (attributeName, itemValue) => {
        setSelectedAttributes(prev => ({ ...prev, [attributeName]: itemValue }));
    };

    // Handle adding the item to the global cart context
    const handleAddToCart = () => {
        // Check if all atributes are picked before processing further
        if (!allAttributesSelected) {
            alert('Please select all attributes (Size, Color, etc.) before adding to cart!');
            return;
        }

        // Add to cart state and automatically open the cart overlay
        addToCart(product, selectedAttributes);
        console.log('Added to cart:', product.name, selectedAttributes);
        setIsCartOpen(true);
    };

    return (
        <main className="container product-page-container">
            <div className="pdp-layout">
                {/* Product images component, left side  */}
                <ImageGallery images={product.gallery} productName={product.name} />

                {/* Product details, right side  */}
                <div className="pdp-details">
                    <h1 className="pdp-name">{product.name}</h1>

                    {/* Component to render sizes, colors, swatches etc. */}
                    <AttributeSelector 
                        attributes={product.attributes}
                        selectedAttributes={selectedAttributes}
                        onAttributeSelect={handleAttributeSelect}
                    />

                    {/* Pricing details */}
                    <div className="pdp-price-block">
                        <span className="attr-label">PRICE:</span>
                        <div className="price-value">
                            {product.prices[0].currency.symbol}{(product.prices[0].amount).toFixed(2)}
                        </div>
                    </div>

                    {/* Add to Cart button (if out of stock or not all attributes selected, then diasbled) */}
                    <button 
                        className="pdp-add-btn" 
                        data-testid="add-to-cart" 
                        disabled={!product.inStock || !allAttributesSelected}
                        onClick={handleAddToCart}
                    >
                        {product.inStock ? 'ADD TO CART' : 'OUT OF STOCK'}
                    </button>

                    {/* Product description (parses description (HTML strings) from DB safely) */}
                    <div className="pdp-description" data-testid="product-description">
                        {parse(product.description)}
                    </div>
                </div>
            </div>
        </main>
    );
}

export default ProductPage;