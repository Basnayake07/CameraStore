# Web Application

## Introduction

This web-based application is designed to manage the warehouse operations of a CameraShop. It provides functionalities for user authentication, managing products, suppliers, and purchases, as well as generating CSV reports, real-time updates, and tracking purchase orders, dispatch orders, and inventory. The application facilitates efficient inventory management and product issuance to relevant shops.

## Project Structure

- **PHP Files**: Contain server-side logic for user authentication, data processing, and CRUD operations.
- **JavaScript Files**: Handle client-side interactions, validations, and dynamic content updates.
- **CSS Files**: Style the user interface, including layouts, colors, and fonts.
- **Images**: Includes graphical elements used throughout the application.
- **CSV Handling**: Provides functionality to export data from the application to CSV files.

## Prerequisites

Before you begin, ensure you have the following installed on your machine:

- **Web Server** (e.g., Apache)
- **PHP** (version 7.4 or higher)
- **MySQL** (or another compatible database system)
- **Composer** (for managing PHP dependencies)
- **Git** (optional, for version control)

## Setup Instructions

1. **Clone the repository** (if you have access to version control):
    ```bash
    git clone https://github.com/SenudaJK/Web-Development-Project
    ```

2. **Navigate to the project directory**:
    ```bash
    cd your-project-folder
    ```

3. **Set up the database**:
    - Create a new MySQL database (e.g., `camerashop_db`).
    - Import the SQL file (`database.sql`) using a MySQL client or command line.

4. **Configure the database connection**:
    - Open `config.php`.
    - Update the following settings with your database details:
      ```php
      $dbHost = 'localhost'; // Database host
      $dbUser = 'username';  // Database username
      $dbPass = 'password';  // Database password
      $dbName = 'camerashop_db'; // Database name
      $dbPort = '3306'; // Default MySQL port number, change if different
      ```

    If your MySQL server is running on a port other than the default `3306`, specify the port number in the `config.php` file:
      ```php
      $dbPort = 'localhost:your-port-number'; // e.g., '3307'
      ```

    Ensure that the port number is included in the database connection string if it differs from the default. For example:
      ```php
      $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
      ```

5. **Install PHP dependencies** (if you use Composer):
    ```bash
    composer install
    ```

6. **Run the application**:
    - Move the project folder to your web server's root directory (e.g., `htdocs` for XAMPP).
    - Start the web server and visit `http://localhost/your-folder-name` in your browser.

## Usage

- **Login**: Access the login page via `login.php`.
- **Dashboard**: The main dashboard is accessible after logging in.
- **CRUD Operations**: Manage products, suppliers, inventory, and purchases through the respective pages.
- **CSV Export**: Export inventory, purchase orders, and dispatch orders to CSV format using the provided links or buttons.

## User Roles & Permissions

- **Admin**: The admin has full access to all features of the application, including creating, updating, and deleting products, suppliers, inventory, purchase orders, and dispatch orders.
- **Worker**: Workers have view-only access to the application. They can view and add products, suppliers, inventory, purchase orders, and dispatch orders but cannot make any changes.

## Troubleshooting

- **Database Connection Errors**: Ensure that your `config.php` file has the correct database credentials and port number. Verify that the MySQL server is running.
- **CSS/JS Not Loading**: Check the file paths in your HTML files. Ensure that the CSS and JavaScript files are correctly placed in the project directory and accessible by the web server.
- **Permissions Issues**: Make sure that your web server has read and write permissions for the project files.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details on usage and permissions.
