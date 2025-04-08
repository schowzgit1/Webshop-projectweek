# Webshop API Documentation

This document provides documentation for the Webshop API endpoints.

## Database Setup

First, run the database setup script to create the necessary tables:

```
php server/db/setup.php
```

This will create all required tables and insert sample data.

## Authentication Endpoints

### Register User

- **URL:** `/server/api/auth/register.php`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
    "username": "example_user",
    "email": "user@example.com",
    "password": "securepassword"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Registratie succesvol. Je kunt nu inloggen.",
    "user": {
      "id": 1,
      "username": "example_user",
      "email": "user@example.com"
    }
  }
  ```

### Login

- **URL:** `/server/api/auth/login.php`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
    "username": "example_user",
    "password": "securepassword"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Inloggen succesvol",
    "user": {
      "id": 1,
      "username": "example_user",
      "email": "user@example.com",
      "role": "user"
    }
  }
  ```

### Logout

- **URL:** `/server/api/auth/logout.php`
- **Method:** `POST`
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Je bent succesvol uitgelogd."
  }
  ```

### Check Session

- **URL:** `/server/api/auth/check-session.php`
- **Method:** `GET`
- **Success Response (Logged in):**
  ```json
  {
    "success": true,
    "loggedIn": true,
    "user": {
      "id": 1,
      "username": "example_user",
      "email": "user@example.com",
      "role": "user"
    }
  }
  ```
- **Success Response (Not logged in):**
  ```json
  {
    "success": true,
    "loggedIn": false
  }
  ```

## Product Endpoints

### Get Products

- **URL:** `/server/api/products/get-products.php`
- **Method:** `GET`
- **Query Parameters:**
  - `category` (optional) - Filter by category
  - `search` (optional) - Search in name and description
  - `limit` (optional) - Number of items per page (default 10)
  - `page` (optional) - Page number (default 1)
- **Success Response:**
  ```json
  {
    "success": true,
    "products": [
      {
        "id": 1,
        "name": "Product Name",
        "description": "Product Description",
        "price": 9.99,
        "category": "category",
        "image": "/path/to/image.jpg",
        "stock": 100
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 5,
      "totalItems": 50,
      "itemsPerPage": 10
    }
  }
  ```

### Get Product Details

- **URL:** `/server/api/products/get-product.php`
- **Method:** `GET`
- **Query Parameters:**
  - `id` (required) - Product ID
- **Success Response:**
  ```json
  {
    "success": true,
    "product": {
      "id": 1,
      "name": "Product Name",
      "description": "Product Description",
      "price": 9.99,
      "category": "category",
      "image": "/path/to/image.jpg",
      "stock": 100
    },
    "relatedProducts": [
      {
        "id": 2,
        "name": "Related Product",
        "description": "Related Product Description",
        "price": 19.99,
        "category": "category",
        "image": "/path/to/image.jpg",
        "stock": 50
      }
    ]
  }
  ```

## Shopping Cart Endpoints

### Get Cart

- **URL:** `/server/api/cart/get-cart.php`
- **Method:** `GET`
- **Success Response:**
  ```json
  {
    "success": true,
    "cart": [
      {
        "product_id": 1,
        "name": "Product Name",
        "price": 9.99,
        "quantity": 2,
        "image": "/path/to/image.jpg"
      }
    ],
    "summary": {
      "total_price": 19.98,
      "total_items": 2
    }
  }
  ```

### Add to Cart

- **URL:** `/server/api/cart/add-to-cart.php`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
    "product_id": 1,
    "quantity": 2
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Product toegevoegd aan winkelwagen",
    "cart": [
      {
        "product_id": 1,
        "name": "Product Name",
        "price": 9.99,
        "quantity": 2,
        "image": "/path/to/image.jpg"
      }
    ]
  }
  ```

### Update Cart

- **URL:** `/server/api/cart/update-cart.php`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
    "product_id": 1,
    "quantity": 3
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Winkelwagen bijgewerkt",
    "cart": [
      {
        "product_id": 1,
        "name": "Product Name",
        "price": 9.99,
        "quantity": 3,
        "image": "/path/to/image.jpg"
      }
    ]
  }
  ```

### Clear Cart

- **URL:** `/server/api/cart/clear-cart.php`
- **Method:** `POST`
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Winkelwagen is geleegd",
    "cart": []
  }
  ```

## Order Endpoints

### Checkout

- **URL:** `/server/api/orders/checkout.php`
- **Method:** `POST`
- **Note:** User must be logged in and have items in cart
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Bestelling succesvol geplaatst",
    "order_id": 123
  }
  ```

### Get User Order History

- **URL:** `/server/api/user/order-history.php`
- **Method:** `GET`
- **Query Parameters:**
  - `id` (optional) - Order ID to get specific order details
  - `page` (optional) - Page number (default 1)
  - `limit` (optional) - Number of items per page (default 10)
- **Success Response (Order List):**
  ```json
  {
    "success": true,
    "orders": [
      {
        "id": 123,
        "user_id": 1,
        "total_price": 29.97,
        "status": "completed",
        "created_at": "2023-05-15 14:30:00"
      }
    ],
    "pagination": {
      "currentPage": 1,
      "totalPages": 1,
      "totalItems": 1,
      "itemsPerPage": 10
    }
  }
  ```
- **Success Response (Single Order):**
  ```json
  {
    "success": true,
    "order": {
      "id": 123,
      "user_id": 1,
      "total_price": 29.97,
      "status": "completed",
      "created_at": "2023-05-15 14:30:00",
      "items": [
        {
          "id": 1,
          "order_id": 123,
          "product_id": 1,
          "quantity": 3,
          "price": 9.99,
          "name": "Product Name",
          "image": "/path/to/image.jpg"
        }
      ]
    }
  }
  ```

## Admin Endpoints

### Get Statistics

- **URL:** `/server/api/admin/get-statistics.php`
- **Method:** `GET`
- **Note:** User must be logged in with admin role
- **Success Response:**
  ```json
  {
    "success": true,
    "statistics": {
      "total_revenue": 1299.85,
      "total_orders": 45,
      "total_products": 20,
      "total_users": 100,
      "total_visits": 5000,
      "total_tickets": 25,
      "open_tickets": 10,
      "recent_orders": [],
      "popular_products": [],
      "recent_tickets": [],
      "monthly_revenue": []
    }
  }
  ```

### Manage Products

- **URL:** `/server/api/admin/manage-products.php`
- **Method:** `GET`, `POST`, `PUT`, `DELETE`
- **Note:** User must be logged in with admin role

#### GET (Get products)
- **Query Parameters:**
  - `id` (optional) - Product ID to get specific product
  - `page` (optional) - Page number (default 1)
  - `limit` (optional) - Number of items per page (default 10)

#### POST (Create product)
- **Request Body:**
  ```json
  {
    "name": "New Product",
    "description": "Product Description",
    "price": 29.99,
    "category": "category",
    "image": "/path/to/image.jpg",
    "stock": 50
  }
  ```

#### PUT (Update product)
- **Request Body:**
  ```json
  {
    "id": 1,
    "name": "Updated Product Name",
    "price": 39.99
  }
  ```

#### DELETE (Delete product)
- **Query Parameters:**
  - `id` (required) - Product ID to delete

### Manage Orders

- **URL:** `/server/api/admin/manage-orders.php`
- **Method:** `GET`, `PUT`
- **Note:** User must be logged in with admin role

#### GET (Get orders)
- **Query Parameters:**
  - `id` (optional) - Order ID to get specific order
  - `page` (optional) - Page number (default 1)
  - `limit` (optional) - Number of items per page (default 10)
  - `status` (optional) - Filter by order status

#### PUT (Update order status)
- **Request Body:**
  ```json
  {
    "id": 123,
    "status": "completed"
  }
  ```

### Manage Tickets (Customer Support)

- **URL:** `/server/api/admin/manage-tickets.php`
- **Method:** `GET`, `PUT`
- **Note:** User must be logged in with admin role

#### GET (Get tickets)
- **Query Parameters:**
  - `id` (optional) - Ticket ID to get specific ticket
  - `page` (optional) - Page number (default 1)
  - `limit` (optional) - Number of items per page (default 10)
  - `status` (optional) - Filter by ticket status ('open', 'in_progress', 'closed')
  - `sort_by` (optional) - Field to sort by (default 'created_at')
  - `sort_order` (optional) - Order to sort ('asc' or 'desc', default 'desc')
- **Success Response (Ticket List):**
  ```json
  {
    "success": true,
    "tickets": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "subject": "Vraag over medicatie",
        "message": "Hallo, ik heb een vraag over de paracetamol...",
        "status": "open",
        "created_at": "2023-05-15 14:30:00",
        "updated_at": "2023-05-15 14:30:00"
      }
    ],
    "status_counts": {
      "all": 25,
      "open": 10,
      "in_progress": 8,
      "closed": 7
    },
    "pagination": {
      "currentPage": 1,
      "totalPages": 3,
      "totalItems": 25,
      "itemsPerPage": 10
    }
  }
  ```
- **Success Response (Single Ticket):**
  ```json
  {
    "success": true,
    "ticket": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "subject": "Vraag over medicatie",
      "message": "Hallo, ik heb een vraag over de paracetamol...",
      "status": "open",
      "created_at": "2023-05-15 14:30:00",
      "updated_at": "2023-05-15 14:30:00"
    }
  }
  ```

#### PUT (Update ticket status)
- **Request Body:**
  ```json
  {
    "id": 1,
    "status": "in_progress"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Ticket status succesvol bijgewerkt"
  }
  ```

## Other Endpoints

### Contact Form (Create Support Ticket)

- **URL:** `/server/api/contact.php`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Inquiry",
    "message": "This is a test message"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Uw bericht is verzonden! We nemen zo snel mogelijk contact met u op."
  }
  ```

### Track Visit

- **URL:** `/server/api/track-visit.php`
- **Method:** `POST`
- **Request Body:**
  ```json
  {
    "page": "home"
  }
  ```
- **Success Response:**
  ```json
  {
    "success": true,
    "message": "Bezoek geregistreerd"
  }
  ``` 