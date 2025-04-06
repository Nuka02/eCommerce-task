export interface Currency {
  label: string;
  symbol: string;
}

export interface Price {
  amount: number;
  currency: Currency;
}

export interface Product {
  id: string;
  name: string;
  inStock: boolean;
  gallery: string[];
  description: string;
  attributes: AttributeSet[];
  prices: Price[];
  brand: string;
}

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

export interface CartItem {
  productId: string;
  productName: string;
  productImg: string;
  chosenAttributes: { [key: string]: string };
  quantity: number;
  price: number;
  attributes?: AttributeSet[];
}

export interface Category {
  name: string;
}

export interface ProductData {
  product: Product;
}

export interface ProductsData {
  products: Product[];
}
