import React, { useState } from 'react';

function ImageGallery({ images, productName }) {
    // State to keep track of the currently displayed active image index
    const [mainImage, setMainImage] = useState(0);

    // Switch to previous image, goes to the last image if at the beginning
    const handlePrevImage = () => {
        setMainImage((prev) => (prev === 0 ? images.length - 1 : prev - 1));
    };

    // Switch to next image, goes to the first oone if at the end
    const handleNextImage = () => {
        setMainImage((prev) => (prev === images.length - 1 ? 0 : prev + 1));
    };

    return (
        <>
            {/* Left side thumbnails list*/}
            <div className="pdp-thumbnails">
                {images.map((img, idx) => (
                    <div 
                        key={img} 
                        className={`thumb-wrapper ${mainImage === idx ? 'active' : ''}`}
                        onClick={() => setMainImage(idx)}
                    >
                        <img src={img} alt="thumb" />
                    </div>
                ))}
            </div>

            {/* Main large product view, on the right side */}
            <div className="pdp-main-image-container" data-testid="product-gallery">
                {/* Render slider arrows if there are multiple images available */}
                {images.length > 1 && (
                    <>
                        <button 
                            className="slider-arrow prev" 
                            onClick={handlePrevImage} 
                            aria-label="Previous image"
                        >
                            &#10094; {/* Left slider */}
                        </button>
                        <button 
                            className="slider-arrow next" 
                            onClick={handleNextImage} 
                            aria-label="Next image"
                        >
                            &#10095; {/* Right slider */}
                        </button>
                    </>
                )}
                {/* Main active full-size image */}
                <img 
                    src={images[mainImage]} 
                    alt={productName} 
                    className="pdp-main-image" 
                />
            </div>
        </>
    );
}

export default ImageGallery;