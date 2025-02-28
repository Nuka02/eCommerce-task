import React from 'react'
import { useParams } from 'react-router-dom'
import { useQuery, gql } from '@apollo/client'
import ProductCard from '../components/ProductCard'
import './CategoryPage.css'

interface Currency {
    label: string
    symbol: string
}

interface Price {
    amount: number
    currency: Currency
}

interface Product {
    id: string
    name: string
    inStock: boolean
    gallery: string[]
    prices: Price[]
    brand: string
}

interface ProductsData {
    products: Product[]
}

const GET_PRODUCTS = gql`
  query GetProducts($cat: String) {
  products(category: $cat) {
    id
    name
    inStock
    gallery
    attributes {
      id
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
`

const CategoryPage: React.FC = () => {
    const { name } = useParams<{ name: string }>()
    const { data, loading } = useQuery<ProductsData>(GET_PRODUCTS, { variables: { cat: name } })

    if (loading) return <p>Loading...</p>
    const products = data?.products || []

    return (
        <div className="category-page">
            <h2 className={"category-page__title"}>{(name ?? '').charAt(0).toUpperCase() + (name ?? '').slice(1).toLowerCase()}</h2>
            <div className="category-page__products">
                {products.map(p => {
                    const price = p.prices[0]
                    const priceFormatted = `${price.currency.symbol}${price.amount.toFixed(2)}`
                    return <ProductCard key={p.id} product={p} price={priceFormatted} />
                })}
            </div>
        </div>
    )
}

export default CategoryPage
