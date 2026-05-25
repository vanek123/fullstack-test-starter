import React from 'react';

function CartItem({ item, onUpdateQuantity }) {
    return (
        <div className="cart-item">
            {/* Product main information (Brand, Name, Price), the left side */}
            <div className="cart-item-info">
                <p className="item-brand">{item.brand}</p>
                <p className="item-name">{item.name}</p>
                <p className="item-price">
                    {item.prices[0].currency.symbol}{item.prices[0].amount.toFixed(2)}
                </p>

                {/* Section to display all attributes of this item */}
                <div className="item-attributes">
                    {item.attributes.map(attr => {
                        // Create kebab-case ids for the automated test
                        const attrKebab = attr.name.toLowerCase().replace(/\s+/g, '-');
                        return (
                            <div 
                                key={attr.name} 
                                className="attr-group"
                                data-testid={`cart-item-attribute-${attrKebab}`}
                            >
                                <p className="mini-attr-label">{attr.name}:</p>
                                <div className="mini-attr-items">
                                    {attr.items.map(option => {
                                        // Check if this specific option was the one selected by the user
                                        const isSelected = item.selectedAttributes[attr.name] === option.value;
                                        const valKebab = option.value.toLowerCase().replace(/\s+/g, '-');
                                        
                                        return (
                                            <div 
                                                key={option.value}
                                                // Highlight the box if it's active and apply color styles if it's a swatch
                                                className={`mini-box ${isSelected ? 'active' : ''} ${attr.type === 'swatch' ? 'swatch' : ''}`}
                                                style={attr.type === 'swatch' ? { backgroundColor: option.value } : {}}
                                                data-testid={`cart-item-attribute-${attrKebab}-${valKebab}${isSelected ? '-selected' : ''}`}
                                            >
                                                {/* Only show the text label if this attribute is not a color swatch */}
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
            
            {/* + and - buttons to control quantity */}
            <div className="cart-item-qty">
                {/* Increase quantity by 1 */}
                <button 
                    data-testid="cart-item-amount-increase" 
                    onClick={() => onUpdateQuantity(item.cartId, 1)}
                >
                    +
                </button>
                {/* Current amount of this item in the cart */}
                <span data-testid="cart-item-amount">{item.quantity}</span>
                {/* Decrease quantity by 1 (Item gets removed if quantity hits 0) */}
                <button 
                    data-testid="cart-item-amount-decrease" 
                    onClick={() => onUpdateQuantity(item.cartId, -1)}
                >
                    -
                </button>
            </div>
            
            {/* Product image preview */}
            <div className="cart-item-image">
                <img src={item.gallery[0]} alt={item.name} />
            </div>
        </div>
    );
}

export default CartItem;