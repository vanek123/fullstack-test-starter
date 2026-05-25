import { useContext } from 'react';
import { CartContext } from '../context/CartContext';

// Custom hook to access cart context
// Example: const { cartItems, addToCart, ... } = useCart();
const useCart = () => useContext(CartContext);

export default useCart;