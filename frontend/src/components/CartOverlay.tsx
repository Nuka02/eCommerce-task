import React, { useContext } from 'react';
import { gql, useMutation } from '@apollo/client';
import { CartContext, AttributeSet } from '../store/CartContext';
import './CartOverlay.css';

const CREATE_ORDER = gql`
  mutation CreateOrder($items: [OrderItemInput!]!) {
    createOrder(items: $items) {
      id
      items {
        productId
        quantity
      }
    }
  }
`;

interface CartOverlayProps {
    onClose: () => void;
}

const CartOverlay: React.FC<CartOverlayProps> = ({ onClose }) => {
    const { cartState, cartDispatch } = useContext(CartContext);
    const [createOrder] = useMutation(CREATE_ORDER);

    const totalItems = cartState.items.reduce((acc, item) => acc + item.quantity, 0);
    const totalPrice = cartState.items.reduce((acc, item) => acc + (item.price || 0) * item.quantity, 0);

    const handlePlaceOrder = async () => {
        if (cartState.items.length === 0) return;
        const orderItems = cartState.items.map(item => ({
            productId: item.productId,
            quantity: item.quantity,
            chosenAttributes: Object.entries(item.chosenAttributes).map(([name, value]) => ({ name, value })),
        }));
        await createOrder({ variables: { items: orderItems } });
        cartDispatch({ type: 'EMPTY_CART' });
        onClose();
    };

    return (
        <div
            className="cart-overlay"
            data-testid="cart-overlay"
            onClick={onClose}
        >
            <div
                className="cart-overlay__content"
                onClick={(e) => e.stopPropagation()}
            >
                <h3>My Bag,
                    <span className={"cart-item__product-count"}>{totalItems === 1 ? ' 1 item' : ` ${totalItems} items`}</span>
                </h3>
                <div className="cart-items">
                    {cartState.items.map((item, idx) => (
                        <div key={idx} className="cart-item">
                            <div className={"cart-item__options"}>
                                <h4 className={"cart-item__product-name"}>{item.productName}</h4>
                                <span className={"cart-item__product-price"}>${item.price.toFixed(2)}</span>
                                {/* Display attribute items if available */}
                                {item.attributes && item.attributes.map((attrSet: AttributeSet) => {
                                    const kebabAttr = attrSet.name.toLowerCase().replace(/\s+/g, '-');
                                    return (
                                        <div key={attrSet.id} className="cart-item__attribute" data-testid={`product-attribute-${kebabAttr}`}>
                                            <span>{attrSet.name}:</span>
                                            <div className="cart-item__attribute-options">
                                                {attrSet.items.map(option => {
                                                    const isSelected = item.chosenAttributes[attrSet.name] === option.value;
                                                    const kebabOption = option.value.replace(/\s+/g, '-');
                                                    return (
                                                        <button
                                                            key={option.value}
                                                            data-testid={`product-attribute-${kebabAttr}-${kebabOption}${isSelected ? '-selected' : ''}`}
                                                            className={`cart-item__option ${attrSet.type === 'swatch'
                                                                ? 'cart-item__option--swatch'
                                                                : 'cart-item__option--text'} ${isSelected
                                                                ? (attrSet.type === 'swatch'
                                                                    ? 'cart-item__option--swatch-selected'
                                                                    : 'cart-item__option--text-selected')
                                                                : ''}`}
                                                            style={attrSet.type === 'swatch' ? { backgroundColor: option.value} : {}}
                                                            onClick={(e) => {
                                                                e.stopPropagation();
                                                                const updatedAttributes = { ...item.chosenAttributes, [attrSet.name]: option.value };
                                                                cartDispatch({
                                                                    type: 'UPDATE_CART_ITEM_ATTRIBUTES',
                                                                    payload: { index: idx, chosenAttributes: updatedAttributes },
                                                                });
                                                            }}
                                                        >
                                                            {attrSet.type === 'swatch' ? '' : option.displayValue}
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                            <div className="cart-item__controls">
                                <button
                                    className={"cart-item__count-btn"}
                                    data-testid="cart-item-amount-increase"
                                    onClick={() => cartDispatch({ type: 'INCREASE', payload: idx })}
                                >
                                    +
                                </button>
                                <span data-testid="cart-item-amount">{item.quantity}</span>
                                <button
                                    className={"cart-item__count-btn"}
                                    data-testid="cart-item-amount-decrease"
                                    onClick={() => cartDispatch({ type: 'REMOVE_OR_DECREASE', payload: idx })}
                                >
                                    -
                                </button>
                            </div>
                            <div className={"cart-item__img-container"}>
                                <img src={item.productImg} alt={item.productName} className="cart-item__img" />
                            </div>
                        </div>
                    ))}
                </div>
                <div className="cart-overlay__total">
                    <span data-testid="cart-total">Total</span>
                    <span>${totalPrice.toFixed(2)}</span>
                </div>
                <button
                    onClick={handlePlaceOrder}
                    className="cart-overlay__order-btn"
                    disabled={cartState.items.length === 0}
                >
                    PLACE ORDER
                </button>
            </div>
        </div>
    );
};

export default CartOverlay;
