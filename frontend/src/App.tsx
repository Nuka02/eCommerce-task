import React from 'react'
import { Routes, Route, Navigate } from 'react-router-dom'
import Header from './components/Header'
import CategoryPage from './pages/CategoryPage'
import ProductPage from './pages/ProductPage'

const App: React.FC = () => {
    return (
        <>
            <Header />
            <Routes>
                <Route path="/" element={<Navigate to="/all" />} />
                <Route path="/:name" element={<CategoryPage />} />
                <Route path="/product/:id" element={<ProductPage />} />
            </Routes>
        </>
    )
}

export default App
