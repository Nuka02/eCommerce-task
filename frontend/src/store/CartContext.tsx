import React, { createContext, useReducer, ReactNode, useEffect } from 'react';
import { CartItem } from '../types/index';

export interface CartState {
  items: CartItem[];
  isOverlayOpen: boolean;
}

export type CartAction =
  | { type: 'ADD_TO_CART'; payload: CartItem }
  | { type: 'REMOVE_OR_DECREASE'; payload: number }
  | { type: 'INCREASE'; payload: number }
  | { type: 'EMPTY_CART' }
  | {
      type: 'UPDATE_CART_ITEM_ATTRIBUTES';
      payload: { index: number; chosenAttributes: { [key: string]: string } };
    }
  | { type: 'OPEN_CART_OVERLAY' }
  | { type: 'CLOSE_CART_OVERLAY' };

interface CartContextProps {
  cartState: CartState;
  cartDispatch: React.Dispatch<CartAction>;
}

export const CartContext = createContext<CartContextProps>({
  cartState: { items: [], isOverlayOpen: false },
  cartDispatch: () => null,
});

function cartReducer(state: CartState, action: CartAction): CartState {
  switch (action.type) {
    case 'ADD_TO_CART': {
      const existingIndex = state.items.findIndex(
        (item) =>
          item.productId === action.payload.productId &&
          JSON.stringify(item.chosenAttributes) === JSON.stringify(action.payload.chosenAttributes),
      );
      const newItems = [...state.items];
      if (existingIndex >= 0) {
        newItems[existingIndex].quantity += action.payload.quantity;
      } else {
        newItems.push(action.payload);
      }
      return { ...state, items: newItems, isOverlayOpen: true };
    }
    case 'REMOVE_OR_DECREASE': {
      const idx = action.payload;
      const newItems = [...state.items];
      if (newItems[idx].quantity > 1) {
        newItems[idx].quantity -= 1;
      } else {
        newItems.splice(idx, 1);
      }
      return { ...state, items: newItems };
    }
    case 'INCREASE': {
      const idx = action.payload;
      const newItems = [...state.items];
      newItems[idx].quantity += 1;
      return { ...state, items: newItems };
    }
    case 'EMPTY_CART': {
      return { ...state, items: [], isOverlayOpen: false };
    }
    case 'UPDATE_CART_ITEM_ATTRIBUTES': {
      const { index, chosenAttributes } = action.payload;
      const newItems = [...state.items];
      newItems[index].chosenAttributes = chosenAttributes;
      return { ...state, items: newItems };
    }
    case 'OPEN_CART_OVERLAY': {
      return { ...state, isOverlayOpen: true };
    }
    case 'CLOSE_CART_OVERLAY': {
      return { ...state, isOverlayOpen: false };
    }
    default:
      return state;
  }
}

interface CartProviderProps {
  children: ReactNode;
}

export function CartProvider({ children }: CartProviderProps) {
  const [state, dispatch] = useReducer(cartReducer, { items: [] }, (initial) => {
    const stored = localStorage.getItem('cartState');
    return stored ? JSON.parse(stored) : initial;
  });

  useEffect(() => {
    localStorage.setItem('cartState', JSON.stringify(state));
  }, [state]);

  return (
    <CartContext.Provider value={{ cartState: state, cartDispatch: dispatch }}>
      {children}
    </CartContext.Provider>
  );
}
