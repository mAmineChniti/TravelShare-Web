
# TravelShare Website

## Overview
TravelShare is a web application built with Symfony that provides travel-related services, including user management, reservations, reporting, and content sharing. This platform allows users to share their travel experiences, book trips, and generate insights from their travel activities.

## Features
- **User Management**: Registration, authentication, and profile updates  
- **Reservations**: Booking, modifying, and canceling reservations  
- **Reporting**: Generating reports on user activity and travel trends  
- **Content Sharing**: Users can share travel experiences through posts and media  
- **Admin Controls**: Manage users, reservations, and system settings  

## Technology Stack
- **Symfony 6+**  
- **PHP 8.1+**  
- **MySQL** (as the primary database)  
- **JWT Authentication**  

## Getting Started

### Prerequisites
Ensure you have the following installed:
- PHP 8.1+  
- Composer  
- MySQL  

### Installation & Setup
1. Clone the repository:  
   ```sh
   git clone https://github.com/mAmineChniti/TravelShare-Symfony.git
   cd TravelShare-Symfony
   ```
2. Install dependencies:  
   ```sh
   composer install
   ```
3. Configure the environment variables:  
   - Copy the `.env` file:  
     ```sh
     cp .env.example .env
     ```
   - Update the database credentials in the `.env` file:  
     ```sh
     DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/travelshare
     ```
4. Generate the database schema:  
   ```sh
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   ```
5. Start the Symfony server:  
   ```sh
   symfony server:start
   ```
6. Access the application in your browser:  
   ```
   http://127.0.0.1:8000
   ```

### License
This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.
