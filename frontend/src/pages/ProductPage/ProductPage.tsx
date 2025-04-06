import React, { useState, useContext } from 'react';
import { useParams } from 'react-router-dom';
import { useQuery, gql } from '@apollo/client';
import parse from 'html-react-parser';
import { CartContext } from '../../store/CartContext';
import './ProductPage.css';
import galleryArrow from '../../assets/product-gallery-arrow.svg';
import { ProductData } from '../../types';

const GET_PRODUCT = gql`
  query GetProduct($id: String!) {
    product(id: $id) {
      id
      name
      inStock
      gallery
      description
      attributes {
        name
        type
        items {
          displayValue
          value
        }
      }
      prices {
        amount
        currency {
          label
          symbol
        }
      }
      brand
    }
  }
`;

const ProductPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const { data, loading } = useQuery<ProductData>(GET_PRODUCT, { variables: { id } });
  const { cartDispatch } = useContext(CartContext);
  const [currentImage, setCurrentImage] = useState<number>(0);
  const [chosenAttrs, setChosenAttrs] = useState<{ [key: string]: string }>({});

  if (loading) return <p>Loading...</p>;
  const product = data?.product;
  if (!product) return <p>Product not found</p>;

  const price = product.prices[0];
  const priceFormatted = `${price.currency.symbol}${price.amount.toFixed(2)}`;

  const handleSelectAttr = (attrName: string, value: string) => {
    setChosenAttrs((prev) => ({ ...prev, [attrName]: value }));
  };

  const allSelected = product.attributes.every((attr) => chosenAttrs[attr.name]) && product.inStock;

  const handlePrev = () => {
    setCurrentImage((prev) => (prev === 0 ? product.gallery.length - 1 : prev - 1));
  };

  const handleNext = () => {
    setCurrentImage((prev) => (prev === product.gallery.length - 1 ? 0 : prev + 1));
  };

  const addToCart = () => {
    if (!allSelected) return;
    cartDispatch({
      type: 'ADD_TO_CART',
      payload: {
        productId: product.id,
        productName: product.name,
        productImg: product.gallery[0],
        chosenAttributes: chosenAttrs,
        quantity: 1,
        price: price.amount,
        attributes: product.attributes || [],
      },
    });
  };

  return (
    <div className="product-page">
      <div className="product-page__thumbs">
        {product.gallery.map((imgUrl, idx) => (
          <img
            key={idx}
            src={imgUrl}
            alt={product.name}
            className="product-page__thumb"
            onClick={() => setCurrentImage(idx)}
          />
        ))}
      </div>
      <div className="product-page__gallery" data-testid="product-gallery">
        <button className="gallery-nav gallery-nav--left" onClick={handlePrev}>
          <img src={galleryArrow} alt="<" />
        </button>
        <img
          src={product.gallery[currentImage]}
          alt={product.name}
          className="product-page__main-img"
        />
        <button className="gallery-nav gallery-nav--right" onClick={handleNext}>
          <img src={galleryArrow} alt=">" />
        </button>
      </div>
      <div className="product-page__details">
        <div className="product-page__info">
          <h2 className="product-name">{product.name}</h2>
          <h3 className="product-brand">{product.brand}</h3>
          {product.attributes.map((attr) => {
            const kebabAttr = attr.name.toLowerCase().replace(/\s+/g, '-');
            return (
              <div
                key={attr.name}
                className="product-page__attribute"
                data-testid={`product-attribute-${kebabAttr}`}
              >
                <h4 className="attribute-name">{attr.name.toUpperCase()}:</h4>
                <div className="product-page__attribute-options">
                  {attr.items.map((item) => {
                    const isSelected = chosenAttrs[attr.name] === item.value;
                    const itemKebab = item.value.replace(/\s+/g, '-');
                    const testId = `product-attribute-${kebabAttr}-${itemKebab}${isSelected ? '-selected' : ''}`;
                    return (
                      <button
                        key={item.value}
                        data-testid={testId}
                        onClick={() => handleSelectAttr(attr.name, item.value)}
                        className={`product-page__option product-page__option--${attr.type} ${isSelected ? 'product-page__option--selected' : ''}`}
                        style={attr.type === 'swatch' ? { backgroundColor: item.value } : {}}
                      >
                        {attr.type === 'swatch' ? '' : item.displayValue}
                      </button>
                    );
                  })}
                </div>
              </div>
            );
          })}
          <h4>PRICE:</h4>
          <h3>{priceFormatted}</h3>
          <button
            data-testid="add-to-cart"
            onClick={addToCart}
            disabled={!allSelected}
            className="product-page__add-btn"
          >
            ADD TO CART
          </button>
          <div data-testid="product-description" className="product-page__description">
            {parse(product.description)}
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductPage;
