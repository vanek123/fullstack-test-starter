import React from 'react';
import './ProductList.css';
import { useQuery } from '@apollo/client/react';
import { gql } from '@apollo/client';
import { useParams } from 'react-router-dom';
import useCart from '../../hooks/useCart';
import ProductCard from './ProductCard';

// Fetch filtered products by category name
const GET_PRODUCTS = gql`
  query GetProducts($category: String) {
    products(category: $category) {
      id
      name
      inStock
      gallery
      brand
      category 
      attributes {
        name
        type
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
`;

function ProductList() {
    // Get the active category name directly from the route URL path
    const { categoryName } = useParams();

    // Fetch products dynamically. If 'all' is active, pass null to fetch the entire catalog
    const { loading, error, data } = useQuery(GET_PRODUCTS, {
        variables: { category: categoryName === 'all' ? null : categoryName }
    });

    // Connect to the global cart context methods
    const { addToCart, setIsCartOpen } = useCart();

    // Loading and error states
    if (loading) return <div className="container status-message">Loading products...</div>;
    if (error) return <div className="container status-message">Error! {error.message}</div>;

    // Capitalize the first letter of the category name for the page heading display
    const displayTitle = categoryName.charAt(0).toUpperCase() + categoryName.slice(1);
    const products = data?.products || [];

    // Process 'Quick Add' icon click from individual product cards
    const handleQuickAdd = (e, product) => {
        // Stop React Router from navigating to the Product Details Page (PDP) upon clicking the cart icon
        e.preventDefault();
        e.stopPropagation();

        // Safely map out default attributes (auto-select the very first option for each attribute group, e.g., first size, first color)
        const defaultAttributes = product.attributes.reduce((acc, attr) => {
            acc[attr.name] = attr.items[0].value;
            return acc;
        }, {});

        // Direct adding to cart state followed by opening the sidebar cart overlay
        addToCart(product, defaultAttributes);
        setIsCartOpen(true);
    };

    return (
        <main className="container product-list-page">
            {/* Dynamic page title header */}
            <h2 className="category-title">{displayTitle}</h2>
            
            {/* Grid showing collection of available items */}
            <div className="product-grid">
                {products.map((product) => (
                    <ProductCard 
                        key={product.id}
                        product={product}
                        onQuickAdd={handleQuickAdd}
                    />
                ))}
            </div>
        </main>
    );
}

export default ProductList;