import React, { useEffect } from 'react';
import { useCart } from '../../context/CartContext';
import { gql } from '@apollo/client';
import './CartOverlay.css';
import { useMutation } from '@apollo/client/react'

// GraphQL mutation to place an order
const PLACE_ORDER = gql`
  mutation PlaceOrder($order: OrderInput!) {
    placeOrder(order: $order)
  }
`;

function CartOverlay({ onClose }) {
    // Cart state and actions from context
    const { cartItems, updateQuantity, totalQuantity, clearCart } = useCart();

    // Mutation for placing order
    const [placeOrder, { loading }] = useMutation(PLACE_ORDER);

    // Prevent background scroll when cart is open
    useEffect(() => {
        document.body.style.overflow = 'hidden';
        return () => { document.body.style.overflow = 'auto'; };
    }, []);

    // Get the total price of cart items
    const totalPrice = cartItems.reduce((sum, item) => {
        return sum + (item.prices[0].amount * item.quantity);
    }, 0).toFixed(2);

    // Currency symbol (if cart is empty then $)
    const currencySymbol = cartItems.length > 0 ? cartItems[0].prices[0].currency.symbol : '$';

    // Handle order placement
    const handlePlaceOrder = async () => {
        if (cartItems.length === 0) return;
        
        try {
            // Format cart items into expected structure
            const productsInput = cartItems.map(item => ({
                id: item.id,
                quantity: item.quantity,
                attributes: Object.entries(item.selectedAttributes).map(([name, value]) => ({
                    name,
                    value
                }))
            }));

            // Send order mutation
            await placeOrder({ variables: { order: { products: productsInput } } });
            
            // Clear cart and close overlay after success
            clearCart();
            onClose();
            alert('Order placed successfully!');
        } catch (e) {
            console.error("Order mutation failed:", e);
        }
    };

    return (
        <div className="cart-overlay-wrapper" data-testid="cart-overlay">
            <div className="cart-overlay-backdrop" onClick={onClose} />
            <div className="cart-overlay-content">
                {/* Cart header */}
                <h3 className="cart-title">
                    My Bag, <span className="cart-items-count">
                        {}
                        {totalQuantity} {totalQuantity === 1 ? 'Item' : 'Items'}
                    </span>
                </h3>

                {/* Cart items list*/}
                <div className="cart-items-list">
                    {cartItems.map((item) => (
                        <div key={item.cartId} className="cart-item">
                            
                            {/* Product info */}
                            <div className="cart-item-info">
                                <p className="item-brand">{item.brand}</p>
                                <p className="item-name">{item.name}</p>
                                <p className="item-price">
                                    {item.prices[0].currency.symbol}{item.prices[0].amount.toFixed(2)}
                                </p>

                                {/* Attributes (size, color, etc.) */}
                                <div className="item-attributes">
                                    {item.attributes.map(attr => {
                                        const attrKebab = attr.name.toLowerCase().replace(/\s+/g, '-');
                                        return (
                                            <div 
                                                key={attr.id} 
                                                className="attr-group"
                                                data-testid={`cart-item-attribute-${attrKebab}`}
                                            >
                                                <p className="mini-attr-label">{attr.name}:</p>
                                                <div className="mini-attr-items">
                                                    {attr.items.map(option => {
                                                        const isSelected = item.selectedAttributes[attr.name] === option.value;
                                                        const valKebab = option.value.toLowerCase().replace(/\s+/g, '-');
                                                        
                                                        return (
                                                            <div 
                                                                key={option.id}
                                                                className={`mini-box ${isSelected ? 'active' : ''} ${attr.type === 'swatch' ? 'swatch' : ''}`}
                                                                style={attr.type === 'swatch' ? { backgroundColor: option.value } : {}}
                                                                data-testid={`cart-item-attribute-${attrKebab}-${valKebab}${isSelected ? '-selected' : ''}`}
                                                            >
                                                                {attr.type !== 'swatch' && option.value}
                                                            </div>
                                                        );
                                                    })}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                            
                            {/* Quantity control */}
                            <div className="cart-item-qty">
                                <button 
                                    data-testid="cart-item-amount-increase" 
                                    onClick={() => updateQuantity(item.cartId, 1)}
                                >+</button>
                                <span data-testid="cart-item-amount">{item.quantity}</span>
                                <button 
                                    data-testid="cart-item-amount-decrease" 
                                    onClick={() => updateQuantity(item.cartId, -1)}
                                >-</button>
                            </div>
                            
                            {/* Product image */}
                            <div className="cart-item-image">
                                <img src={item.gallery[0]} alt={item.name} />
                            </div>
                        </div>
                    ))}
                </div>

                {/* Cart total */}
                <div className="cart-total" data-testid="cart-total">
                    <span>Total</span>
                    <span>{currencySymbol}{totalPrice}</span>
                </div>

                {/* Place order button */}
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