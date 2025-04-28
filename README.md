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

## Getting Started

### Prerequisites

Ensure you have the following installed:

- PHP 8.1+
- Composer
- MySQL
- Node.js with npm or Bun (for managing front-end dependencies)

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

3. Install front-end dependencies:

   ```sh
   npm install
   # or if you prefer Bun
   bun install
   ```

4. Configure the environment variables:
   - Copy the `.env` file:

     ```sh
     cp .env.example .env
     ```

   - Update the database credentials in the `.env` file:

     ```sh
     DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/travelshare
     ```

5. Generate the database schema:

   ```sh
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   ```

6. Start the asset watcher to compile styling:

   ```sh
   npm run watch
   # or if you prefer Bun
   bun run watch
   ```

7. Start the Symfony server:

   ```sh
   symfony server:start
   ```

8. Access the application in your browser:

   ```sh
   http://127.0.0.1:8000
   ```

## Contributing

We welcome contributions to TravelShare! Please make sure to read the [Contributor Guide](CONTRIBUTING.md) before starting. The guide outlines the rules and best practices for maintaining consistency and quality in the project.

### Reporting Issues

If you encounter any issues or have suggestions for improvements, feel free to open an issue in the repository.

### License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for more information.
