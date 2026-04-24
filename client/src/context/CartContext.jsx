import React, { createContext, useContext, useState, useEffect } from 'react';

const CartContext = createContext();

export const CartProvider = ({ children }) => {
    const [cartItems, setCartItems] = useState(() => {
        const localData = localStorage.getItem('cart');
        return localData ? JSON.parse(localData) : [];
    });

    useEffect(() => {
        localStorage.setItem('cart', JSON.stringify(cartItems));
    }, [cartItems]);

    const addToCart = (product, selectedAttributes) => {
        const attrString = Object.entries(selectedAttributes)
            .map(([key, val]) => `${key}:${val}`)
            .sort().join('-');
        const cartId = `${product.id}-${attrString}`;

        setCartItems(prev => {
            const existing = prev.find(item => item.cartId === cartId);
            if (existing) {
                return prev.map(item => item.cartId === cartId 
                    ? { ...item, quantity: item.quantity + 1 } : item);
            }
            return [...prev, { ...product, selectedAttributes, cartId, quantity: 1 }];
        });
    };

    const updateQuantity = (cartId, delta) => {
        setCartItems(prevItems =>
            prevItems.map(item =>
                item.cartId === cartId
                    ? { ...item, quantity: item.quantity + delta }
                    : item      
            ).filter(item => item.quantity > 0)
        );
    };

    const clearCart = () => {
        setCartItems([]);
    };

    const removeFromCart = (cartId) => {
        setCartItems(prevItems => prevItems.filter(item => item.cartId !== cartId));
    };

    const totalQuantity = cartItems.reduce((total, item) => total + item.quantity, 0);

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