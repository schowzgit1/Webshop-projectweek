# Webshop Project

This is a webshop project with a PHP backend API and frontend.

## Setup Instructions

### 1. Database Setup

First, run the database setup script to create the necessary tables:

```bash
php server/db/setup.php
```

This will create all required tables and insert sample data.

### 2. Create Admin Account

To create an admin account or reset the admin password, run:

```bash
php server/db/create-admin.php
```

This will give you the login credentials for the admin account.

Default admin credentials:
- Username: admin
- Password: admin123

Make sure to change the admin password immediately after logging in.

### 3. API Documentation

For detailed API documentation, see [server/README.md](server/README.md).

## Features

- **User Authentication:** Register, login, and session management
- **Product Management:** Browse, search, and filter products
- **Shopping Cart:** Add, update, and remove items from your cart
- **Checkout System:** Place orders and manage order history
- **Admin Dashboard:** Manage products, orders, and view statistics
- **Support Ticket System:** Handle customer inquiries and support tickets

## Backend Features

- **RESTful API:** Well-structured API endpoints
- **Database Integration:** MySQL database for data storage
- **Authentication System:** Secure user authentication and authorization
- **Session Management:** PHP session handling
- **Admin Dashboard:** Statistics, product management, order management, ticket management

## Dashboard Statistics

The admin dashboard provides various statistics, including:
- Total revenue
- Number of orders
- Number of products
- Number of users
- Page visits
- Support tickets (total and open)
- Recent orders
- Popular products
- Recent support tickets
- Monthly revenue charts

## Support Ticket System

The support ticket system allows customers to submit inquiries via the contact form. Admins can:
- View all tickets
- Filter tickets by status (open, in progress, closed)
- Change ticket status
- Sort tickets by various criteria

## Security Features

- Password hashing
- Prepared SQL statements to prevent SQL injection
- Session-based authentication
- CORS headers for API security
- Input validation and sanitization
