import React from 'react'
import ReactDOM from 'react-dom/client'
import { ApolloProvider } from '@apollo/client'
import { BrowserRouter } from 'react-router-dom'
import App from './App'
import { client } from './graphql/client'
import { CartProvider } from './store/CartContext'
import './global.css'

ReactDOM.createRoot(document.getElementById('root') as HTMLElement).render(
    <ApolloProvider client={client}>
        <CartProvider>
            <BrowserRouter>
                <App />
            </BrowserRouter>
        </CartProvider>
    </ApolloProvider>
)
