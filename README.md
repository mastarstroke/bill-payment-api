
# **Bill Payment API**

A simple API for a bill payment application with the following features:
- User authentication (signup/signin).
- Wallet management (balance checking, funding, and transactions).
- Airtime purchase simulation.
- Transaction history.
- Concurrency-safe wallet operations.

---

## **Table of Contents**
1. [Features](#features)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Setup](#setup)
5. [Usage](#usage)
   - [API Endpoints](#api-endpoints)
6. [Running Tests](#running-tests)

---

## **Features**
- **Authentication**: Secure signup and login system.
- **Wallet System**:
  - Check wallet balance.
  - Fund wallet.
  - Deduct wallet balance for transactions.
  - Concurrency-safe operations to handle simultaneous requests.
- **Bill Payment**:
  - Purchase airtime (simulated).
- **Transaction History**:
  - View all wallet funding and airtime purchase transactions.

---

## **Requirements**
- PHP >= 8.1
- Laravel >= 10.x
- Composer
- MySQL or any compatible database
- Postman (for API testing)

---

## **Installation**
1. Clone the repository:
   ```bash
   git clone https://github.com/mastarstroke/bill-payment-api
   cd bill-payment-api
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Create a `.env` file:
   ```bash
   cp .env.example .env

   or

   copy .env.example .env
   ```

4. Set up your database in the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=billPayment
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. Generate an application key:
   ```bash
   php artisan key:generate
   ```

6. Run migrations to set up the database:
   ```bash
   php artisan migrate
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

---

## **Usage**

### **Authentication**
- Use the following endpoints to authenticate users:
  - **Signup**: `POST /api/register`
  - **Login**: `POST /api/login`
  - **Logout**: `POST /api/logout` (requires authentication)

### **Wallet**
- **Check Balance**: `GET /api/wallet/balance` (requires authentication)
- **Fund Wallet**: `POST /api/wallet/fund` (requires authentication)
  - Example payload:
    ```json
    {
      "amount": 1000
    }
    ```

### **Bill Payment**
- **Purchase Airtime**: `POST /api/bill/airtime` (requires authentication)
  - Example payload:
    ```json
    {
      "amount": 150,
      "phone_number": "1234567890"
    }
    ```

### **Transaction History**
- **Get All Transactions**: `GET /api/wallet/transactions` (requires authentication)

---

## **API Endpoints**

| Endpoint              | Method | Description              | Authentication |
|-----------------------|--------|--------------------------|----------------|
| `/api/register`       | POST   | User signup              | No             |
| `/api/login`          | POST   | User login               | No             |
| `/api/logout`         | POST   | User logout              | Yes            |
| `/api/wallet/balance` | GET    | Get wallet balance       | Yes            |
| `/api/wallet/fund`    | POST   | Fund wallet              | Yes            |
| `/api/bill/airtime`   | POST   | Purchase airtime         | Yes            |
| `/api/transactions`   | GET    | View transaction history | Yes            |

---

## **Running Tests**

This project uses PHPUnit for automated testing.

1. Run all tests:
   ```bash
   php artisan test
   ```

2. Run a specific test:
   ```bash
   php artisan test --filter test_name
   ```

3. Example test output:
   ```
   PASS  Tests\Feature\WalletTest
   ✓ it handles concurrent transactions safely
   ```

---

## **Concurrency Handling**
To ensure the wallet system is safe from concurrency issues:
- **Database Transactions**: Critical operations (like debiting the wallet) are wrapped in database transactions.
- **Row-Level Locking**: The `FOR UPDATE` SQL lock ensures no two requests can modify the wallet balance simultaneously.


## API Documentation
The API endpoints are documented in a Postman collection. Follow the steps below to import and view them:

1. Open Postman.
2. Click "Import" in the top-left corner.
3. Select the `api-docs.json` file located in the root of the project.
4. View all available endpoints and their descriptions.

The collection provides example requests and responses for easy testing.