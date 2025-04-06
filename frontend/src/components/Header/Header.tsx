import React, { useContext, useRef, useEffect } from 'react';
import { useQuery, gql } from '@apollo/client';
import CartOverlay from '../CartOverlay/CartOverlay';
import { CartContext } from '../../store/CartContext';
import './Header.css';
import logo from '../../assets/logo.svg';
import cart from '../../assets/cart.svg';
import CategoryLink from '../CategoryLink';
import { Category } from '../../types';

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
  const cartButtonRef = useRef<HTMLButtonElement>(null);
  const overlayContentRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      const target = event.target as Node;

      if (
        overlayContentRef.current &&
        !overlayContentRef.current.contains(target) &&
        cartButtonRef.current &&
        !cartButtonRef.current.contains(target)
      ) {
        cartDispatch({ type: 'CLOSE_CART_OVERLAY' });
      }
    };

    if (cartState.isOverlayOpen) {
      document.addEventListener('mousedown', handleClickOutside);
    }

    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [cartState.isOverlayOpen, cartDispatch]);

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
          {data?.categories.map((cat) => <CategoryLink key={cat.name} category={cat} />)}
        </nav>
      </div>
      <img src={logo} alt="Logo" />
      <button
        ref={cartButtonRef}
        className="header__cart-btn"
        data-testid="cart-btn"
        onClick={toggleOverlay}
      >
        <img src={cart} alt="Cart" />
        {itemsCount > 0 && <span className="header__badge">{itemsCount}</span>}
      </button>
      {cartState.isOverlayOpen && (
        <CartOverlay
          onClose={() => cartDispatch({ type: 'CLOSE_CART_OVERLAY' })}
          contentRef={overlayContentRef}
        />
      )}
    </header>
  );
};

export default Header;
