# Buckaroo Calculator 

This project includes authentication features using Laravel Breeze and a calculator that supports basic arithmetic operations. The calculator also keeps track of your previous calculations.

## Setup Instructions

Follow these steps to set up and run the project on your local machine.

### 1. Clone the Repository

Use the following command to clone the project to your local machine:
### 1. Clone project from GitHub

```bash
git clone "project_url"
```

### 2. Navigate to the Project Folder

```bash
cd buckaroo_calculator
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Copy Environment File

```bash
cp .env.example .env
```

### 5. Generate App Key

```bash
php artisan key:generate
```

### 6. Optimize the Application

```bash
php artisan optimize
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Start the Application

```bash
php artisan serve
```

The application should now be accessible at `http://127.0.0.1:8000` in your web browser.

## Using the Application

1. Register a new account or log in using the provided authentication features.
2. Navigate to the calculator section to perform basic arithmetic operations.
3. Previous calculations will be displayed, allowing you to track your history.
