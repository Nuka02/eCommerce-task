import React, { useState, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import { CartContext } from '../store/CartContext';
import './ProductCard.css';

export interface AttributeItem {
    displayValue: string;
    value: string;
}

export interface AttributeSet {
    id: string;
    name: string;
    type: string;
    items: AttributeItem[];
}

interface Currency {
    label: string;
    symbol: string;
}

interface Price {
    amount: number;
    currency: Currency;
}

interface Product {
    id: string;
    name: string;
    inStock: boolean;
    gallery: string[];
    prices: Price[];
    brand: string;
    attributes?: AttributeSet[];
}

interface ProductCardProps {
    product: Product;
    price: string;
}

const ProductCard: React.FC<ProductCardProps> = ({ product, price }) => {
    const { id, name, inStock, gallery, attributes } = product;
    const [hovered, setHovered] = useState<boolean>(false);
    const navigate = useNavigate();
    const { cartDispatch } = useContext(CartContext);

    const kebabName = name.toLowerCase().replace(/\s+/g, '-');

    const defaultAttributes: { [key: string]: string } = {};
    if (attributes && attributes.length > 0) {
        attributes.forEach(attrSet => {
            if (attrSet.items && attrSet.items.length > 0) {
                defaultAttributes[attrSet.name] = attrSet.items[0].value;
            }
        });
    }

    const handleQuickShop = (e: React.MouseEvent<HTMLButtonElement>) => {
        e.stopPropagation();
        cartDispatch({
            type: 'ADD_TO_CART',
            payload: {
                productId: id,
                productName: name,
                productImg: gallery[0],
                chosenAttributes: defaultAttributes,
                quantity: 1,
                price: parseFloat(price.replace(/^\D+/, '')),
                attributes: attributes || [],
            },
        });
    };

    return (
        <div
            data-testid={`product-${kebabName}`}
            className={`product-card ${inStock ? '' : 'product-card--out-of-stock'}`}
            onMouseEnter={() => setHovered(true)}
            onMouseLeave={() => setHovered(false)}
            onClick={() => navigate(`/product/${id}`)}
        >
            {!inStock && <span className="product-card__overlay">OUT OF STOCK</span>}
            <div className="product-card__img">
                <img src={gallery[0]} alt={name} style={{ maxHeight: 330, maxWidth: 354 }} />
            </div>
            <h4 className="product-card__name">{name}</h4>
            <p className="product-card__price">{price}</p>
            {inStock && hovered && (
                <button onClick={handleQuickShop} className="product-card__quickshop" />
            )}
        </div>
    );
};

export default ProductCard;
