import React from 'react';

function AttributeSelector({ attributes, selectedAttributes, onAttributeSelect }) {
    return (
        <div className="pdp-attributes">
            {/* Loop through each attribute group of the product (e.g., Size, Color, Capacity) */}
            {attributes.map((attr) => {
                {/* Convert attribute name to kebab-case for automated test */}
                const attrKebab = attr.name.toLowerCase().replace(/\s+/g, '-');
                
                return (
                    <div 
                        key={attr.name} 
                        className="pdp-attribute-block" 
                        data-testid={`product-attribute-${attrKebab}`}
                    >
                        {/* Attribute section heading in uppercase */}
                        <span className="attr-label">{attr.name.toUpperCase()}:</span>
                        <div className="attr-items">
                            {/* Loop through individual option items within the current attribute group */}
                            {attr.items.map((item) => {
                                // Determine if this specific item is currently selected by checking the state
                                const isSelected = selectedAttributes[attr.name] === item.value;
                                const itemValue = item.value;
                                
                                return (
                                    <div
                                        // Dynamically apply class names based on attribute type (swatch vs text) and selection state
                                        key={item.value}
                                        className={`${attr.type === 'swatch' ? 'swatch-item' : 'text-item'} ${isSelected ? 'selected' : ''}`}
                                        style={attr.type === 'swatch' ? { backgroundColor: item.value } : {}}
                                        onClick={() => onAttributeSelect(attr.name, item.value)}
                                        data-testid={`product-attribute-${attrKebab}-${itemValue}`}
                                    >
                                        {/* Render the label (like 'S', 'M', '256GB') only if it is NOT a color swatch */}
                                        {attr.type !== 'swatch' && item.value}
                                    </div>
                                );
                            })}
                        </div>
                    </div>
                );
            })}
        </div>
    );
}

export default AttributeSelector;