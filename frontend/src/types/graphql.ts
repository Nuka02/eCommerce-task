import { Product } from './index';

export interface GetProductResponse {
  product: Product;
}

export interface GetProductsResponse {
  products: Product[];
}

export interface GetCategoriesResponse {
  categories: {
    id: string;
    name: string;
  }[];
}

export interface CreateOrderResponse {
  createOrder: {
    id: string;
    items: {
      productId: string;
      quantity: number;
    }[];
  };
}
