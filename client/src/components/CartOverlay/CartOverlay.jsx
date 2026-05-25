import React, { useEffect } from 'react';
import useCart from '../../hooks/useCart';
import { gql } from '@apollo/client';
import './CartOverlay.css';
import { useMutation } from '@apollo/client/react';
import CartItem from './CartItem';

// Mutation to send order data to the backend database
const PLACE_ORDER = gql`
  mutation PlaceOrder($order: OrderInput!) {
    placeOrder(order: $order)
  }
`;

function CartOverlay({ onClose }) {
    // Access global cart state and methods from custom useCart hook
    const { cartItems, updateQuantity, totalQuantity, clearCart } = useCart();
    
    // Mutation hook to execute the order placement
    const [placeOrder, { loading }] = useMutation(PLACE_ORDER);

    // Lifecycle hook, blocks background scrolling when the cart overlay is active
    useEffect(() => {
        document.body.style.overflow = 'hidden';
        // Restores normal scrolling when overlay closes
        return () => { document.body.style.overflow = 'auto'; };
    }, []);

    // Calculate total order price by iterating through all cart proucts
    const totalPrice = cartItems.reduce((sum, item) => {
        return sum + (item.prices[0].amount * item.quantity);
    }, 0).toFixed(2);

    // Dynamic currency symbol extractor (defaults to '$' if the cart is empty)
    const currencySymbol = cartItems.length > 0 ? cartItems[0].prices[0].currency.symbol : '$';

    // Submit formatted cart items to the server
    const handlePlaceOrder = async () => {
        if (cartItems.length === 0) return;
        
        try {
            // Reformat cart items into the exact structure expected by GraphQL schema
            const productsInput = cartItems.map(item => ({
                id: item.id,
                quantity: item.quantity,
                // Transform attributes object (e.g., {Size: "M"}) into an array (e.g., [{name: "Size", value: "M"}])
                attributes: Object.entries(item.selectedAttributes).map(([name, value]) => ({
                    name,
                    value
                }))
            }));

            // Send the formatted order data to the server and wait for it to finish
            await placeOrder({ variables: { order: { products: productsInput } } });
            
            // Clear the cart and close the overlay if the order was successful
            clearCart();
            onClose();
            alert('Order placed successfully!');
        } catch (e) {
            console.error("Order mutation failed:", e);
        }
    };

    return (
        <div className="cart-overlay-wrapper" data-testid="cart-overlay">
            {/* Closes the cart overlay, when you click the dark bacgkround */}
            <div className="cart-overlay-backdrop" onClick={onClose} />
            <div className="cart-overlay-content">
                {/* Header with dynamic total items counter */}
                <h3 className="cart-title">
                    My Bag, <span className="cart-items-count">
                        {totalQuantity} {totalQuantity === 1 ? 'Item' : 'Items'}
                    </span>
                </h3>

                {/* Main scrollable area displaying list of added products */}
                <div className="cart-items-list">
                    {cartItems.map((item) => (
                        <CartItem 
                            key={item.cartId}
                            item={item}
                            onUpdateQuantity={updateQuantity}
                        />
                    ))}
                </div>
                
                {/* Order summary section */}
                <div className="cart-total" data-testid="cart-total">
                    <span>Total</span>
                    <span>{currencySymbol}{totalPrice}</span>
                </div>

                {/* Checkout button (disabled during loading states or if cart contains 0 items) */}
                <button 
                    className={`place-order-btn ${cartItems.length === 0 ? 'disabled' : ''}`}
                    disabled={cartItems.length === 0 || loading}
                    onClick={handlePlaceOrder}
                >
                    {loading ? 'PROCESSING...' : 'PLACE ORDER'}
                </button>
            </div>
        </div>
    );
}

export default CartOverlay;