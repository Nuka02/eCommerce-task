# Scandiweb Full-Stack E-Commerce Site

This repository contains a full-stack e-commerce website built as part of a technical test. The solution consists of a PHP backend with a GraphQL API and a React frontend (built with Vite). The project demonstrates the use of modern PHP and a React single-page application that communicates with the backend via Apollo Client.

## Table of Contents

- [Overview](#overview)
- [Technologies](#technologies)
- [Project Structure](#project-structure)
- [Setup](#setup)
    - [Backend Setup](#backend-setup)
    - [Frontend Setup](#frontend-setup)
- [Running the Application](#running-the-application)
- [Deployment](#deployment)

## Overview

This project implements an e-commerce site with the following features:

- **Product Listing:** Browse products by category.
- **Product Details:** View detailed product information including an image carousel, attributes (e.g., capacity and color), and pricing.
- **Cart Functionality:** Add products to a shopping cart via a quick shop option (with default attribute selections) or through the product page. Users can update their selected options in a cart overlay.
- **GraphQL API:** The PHP backend exposes a GraphQL API for querying categories, products, and inserting orders.
- **Responsive Frontend:** A React single-page application (SPA) built with Vite and Apollo Client handles client-side routing and data fetching.

## Technologies

**Backend:**
- PHP (8.1.31)
- MySQL
- GraphQL (webonyx/graphql-php)
- Dotenv (vlucas/phpdotenv)
- FastRoute
- Object-Oriented Programming & PSR-4 autoloading

**Frontend:**
- React (with functional components and hooks)
- Vite
- Apollo Client (GraphQL)
- CSS

## Project Structure

The repository uses a monorepo structure containing both the frontend and backend:

```
my-fullstack-project/
├─ frontend/          # React/Vite SPA
│    ├─ public/       # Public files (index.html, assets)
│    ├─ src/          # Source code (components, styles, etc.)
│    ├─ package.json
│    ├─ vite.config.ts
│    └─ ...
├─ backend/           # PHP backend code
│    ├─ public/       # Public directory served by Apache (index.php)
│    ├─ src/          # Source code (Controller, Models, Database, GraphQL, Seed, etc.)
│    ├─ vendor/       # Composer dependencies
│    ├─ composer.json
│    ├─ composer.lock
│    └─ .env          # Environment variables
└─ .gitignore
```

## Setup

### Backend Setup

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/Nuka02/eCommerce-task.git
   cd eCommerce-task/backend
   ```

2. **Install Composer Dependencies:**
   Make sure you have [Composer](https://getcomposer.org/) installed, then run:
   ```bash
   composer install
   ```

3. **Configure Environment Variables:**
   Create a `.env` file in the backend folder (next to `composer.json`) with content similar to:
   ```ini
   DB_HOST=localhost
   DB_NAME=your_database_name
   DB_USER=your_database_user
   DB_PASS=your_database_password
   ```
   Adjust as necessary.

4. **Set Up the Database:**
    - Use phpMyAdmin (or the MySQL CLI) to run the migration SQL file (e.g., `src/Migrations/001_create_tables.sql`) and create your database schema.
    - Run the seed script to populate data. You can use a `Seed.php` script placed in the backend folder. Navigate to `http://localhost/src/Seed/Seed.php` to run it.

### Frontend Setup

1. **Navigate to the Frontend Directory:**
   ```bash
   cd ../frontend
   npm install
   ```

2. **Build the Frontend:**
   ```bash
   npm run build
   ```
   The production build will be in the `dist/` folder.

3. **Integrate Frontend with Backend:**
   Copy the contents of the `dist/` folder into your backend public folder (e.g., `backend/public/`), or configure Apache to serve your frontend assets from `frontend/public` as needed.  
   Update your Apollo Client URI in your frontend code to point to your backend GraphQL endpoint (e.g., `http://your-domain/backend/graphql`).

## Running the Application

### Locally

- **Backend:**  
  Configure your local web server (e.g., Apache) so that its DocumentRoot is set to `backend/public`.

- **Frontend:**  
  You can either run the Vite dev server from `frontend` with `npm run dev` or serve the built static files from the backend's public folder.

- **GraphQL Endpoint:**  
  Use a tool like Postman or a GraphQL client to test:
  ```graphql
  query Categories {
    categories {
        name
        id
    }
  }
  ```

### On the Server

- Ensure your Apache DocumentRoot is set to the public folder (e.g., `/var/www/html/public`).
- Verify that your `.htaccess` file routes client-side routes to `index.html` while API endpoints (e.g., `/graphql`) are handled by your PHP router.
- Visit your domain to see the React SPA and use Postman to test the GraphQL API.

## Deployment

- You can deploy to a VPS (e.g. AWS EC2) or a shared hosting provider that supports PHP and MySQL.
- Ensure that environment variables are set on the production server (via the host’s configuration or by uploading the `.env` file securely).


