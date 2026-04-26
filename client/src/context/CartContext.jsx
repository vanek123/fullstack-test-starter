import React, { createContext, useContext, useState, useEffect } from 'react';

// Create cart context
const CartContext = createContext();

export const CartProvider = ({ children }) => {
    // Initialize cart from localStorage (if it exists)
    const [cartItems, setCartItems] = useState(() => {
        const localData = localStorage.getItem('cart');
        return localData ? JSON.parse(localData) : [];
    });

    // Save cart to localStorage whenever it changes
    useEffect(() => {
        localStorage.setItem('cart', JSON.stringify(cartItems));
    }, [cartItems]);

    // Add product to cart or increase quantity if same item exists
    const addToCart = (product, selectedAttributes) => {
        // Creation of unique cart ID based on product and attributes
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
            // Otherwise add new item
            return [...prev, { ...product, selectedAttributes, cartId, quantity: 1 }];
        });
    };

    // Update quantity using +/-, remove if quantity reaches 0
    const updateQuantity = (cartId, delta) => {
        setCartItems(prevItems =>
            prevItems.map(item =>
                item.cartId === cartId
                    ? { ...item, quantity: item.quantity + delta }
                    : item      
            ).filter(item => item.quantity > 0)
        );
    };

    // Clear the cart
    const clearCart = () => {
        setCartItems([]);
    };

    // Remove single item from cart
    const removeFromCart = (cartId) => {
        setCartItems(prevItems => prevItems.filter(item => item.cartId !== cartId));
    };

    // Total number of items in cart
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

export const useCart = () => useContext(CartContext);