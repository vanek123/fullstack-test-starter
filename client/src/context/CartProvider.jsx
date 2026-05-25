import React, { useState, useEffect } from 'react';
import { CartContext } from './CartContext';

// Cart Provider component that wraps the app and provides cart functionality
export const CartProvider = ({ children }) => {
    // Initialize cart from local storage (if exists)
    const [cartItems, setCartItems] = useState(() => {
        const localData = localStorage.getItem('cart');
        return localData ? JSON.parse(localData) : [];
    });

    // Save cart to local storage whenever it changes
    useEffect(() => {
        localStorage.setItem('cart', JSON.stringify(cartItems));
    }, [cartItems]);

    // Add product to cart
    // If same item with same attributes exists, then increase quantity
    const addToCart = (product, selectedAttributes) => {
        // Create unique cart ID based on product ID and selected attributes
        const attrString = Object.entries(selectedAttributes)
            .map(([key, val]) => `${key}:${val}`)
            .sort().join('-');
        const cartId = `${product.id}-${attrString}`;

        setCartItems(prev => {
            const existing = prev.find(item => item.cartId === cartId);

            // If item exists, increase quantity
            if (existing) {
                return prev.map(item => item.cartId === cartId 
                    ? { ...item, quantity: item.quantity + 1 } : item);
            }
            // Otherwise add new item to cart
            return [...prev, { ...product, selectedAttributes, cartId, quantity: 1 }];
        });
    };

    // Update quantity using + or - buttons, remove item if quantity reaches 0
    const updateQuantity = (cartId, delta) => {
        setCartItems(prevItems =>
            prevItems.map(item =>
                item.cartId === cartId
                    ? { ...item, quantity: item.quantity + delta }
                    : item      
            ).filter(item => item.quantity > 0)
        );
    };

    // Clear all items from cart
    const clearCart = () => {
        setCartItems([]);
    };

    // Remove single item from cart by cartId
    const removeFromCart = (cartId) => {
        setCartItems(prevItems => prevItems.filter(item => item.cartId !== cartId));
    };

    // Calculate total numbers of items in cart
    const totalQuantity = cartItems.reduce((total, item) => total + item.quantity, 0);

    // Cart overlay open/close state
    const [isCartOpen, setIsCartOpen] = useState(false);

    return (
        <CartContext.Provider value={{ 
            cartItems, addToCart, updateQuantity, removeFromCart, totalQuantity,
            isCartOpen, setIsCartOpen, clearCart
        }}>
            {children}
        </CartContext.Provider>
    );
};