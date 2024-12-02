# Online Event Ticketing System

An online event ticketing platform built with Laravel to manage events, bookings, and ticket purchases.

## Features
- Event listing and management
- User registration and authentication
- Booking system for events
- Admin panel to manage users and events
- Payment integration (if applicable)

## Prerequisites
Ensure you have the following installed on your machine:
- PHP >= 8.0
- Composer
- MySQL or any other supported database
- Laravel 9+ (already included in the repo)

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Hardik4296/online-event-ticketing-system.git
   cd online-event-ticketing-system
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   npm run dev
   ```

3. **Set up the `.env` file**
   - Copy `.env.example` to `.env`
     ```bash
     cp .env.example .env
     ```
   - Update the database credentials in the `.env` file:
     ```dotenv
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_database_user
     DB_PASSWORD=your_database_password
     ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Create a symbolic link for the storage directory**
   ```bash
   php artisan storage:link
   ```

   > This command creates a symbolic link between the `storage/app/public` directory and the `public/storage` directory, allowing publicly accessible files to be stored securely.

   Ensure proper permissions for the `storage` and `bootstrap/cache` directories:
   ```bash
   chmod -R 775 storage
   chmod -R 775 bootstrap/cache
   ```

6. **Run migrations and seed the database**
   ```bash
   php artisan migrate --seed
   ```

   > This will create the database schema and populate it with seed data.

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   - Open your browser and navigate to [http://localhost:8000](http://localhost:8000)

## Database Seeding
- The project includes database seeders for populating initial data.
- You can modify or add additional seeders in the `database/seeders` directory.

## Additional Notes
- To customize events or tickets, check the models and controllers in the `app/Models` and `app/Http/Controllers` directories.
- Use the `routes/web.php` file to update or add new routes.

## Contribution
Feel free to fork the repository and submit a pull request if you want to contribute.
