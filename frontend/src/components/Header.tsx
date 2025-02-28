import React, { useContext } from 'react';
import {NavLink, NavLinkRenderProps} from 'react-router-dom';
import { useQuery, gql } from '@apollo/client';
import CartOverlay from './CartOverlay';
import { CartContext } from '../store/CartContext';
import './Header.css';
import logo from '../assets/logo.svg';
import cart from '../assets/cart.svg';
import CategoryLink from "./CategoryLink";

interface Category {
    name: string;
}

const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      name
    }
  }
`;

const Header: React.FC = () => {
    const { data } = useQuery<{ categories: Category[] }>(GET_CATEGORIES);
    const { cartState, cartDispatch } = useContext(CartContext);
    const itemsCount = cartState.items.reduce((acc, item) => acc + item.quantity, 0);

    const toggleOverlay = () => {
        if (cartState.isOverlayOpen) {
            cartDispatch({ type: 'CLOSE_CART_OVERLAY' });
        } else {
            cartDispatch({ type: 'OPEN_CART_OVERLAY' });
        }
    };

    return (
        <header className="header">
            <div style={{ width: '0', height: '100%' }}>
                <nav className="header__nav">
                    {data?.categories.map(cat => (
                        <CategoryLink key={cat.name} category={cat} />
                    ))}
                </nav>
            </div>
            <img src={logo} alt="Logo" />
            <button
                className="header__cart-btn"
                data-testid="cart-btn"
                onClick={toggleOverlay}
            >
                <img src={cart} alt="Cart" />
                {itemsCount > 0 && (
                    <span className="header__badge">
                        {itemsCount}
                    </span>
                )}
            </button>
            {cartState.isOverlayOpen && (
                <CartOverlay onClose={() => cartDispatch({ type: 'CLOSE_CART_OVERLAY' })} />
            )}
        </header>
    );
};

export default Header;
