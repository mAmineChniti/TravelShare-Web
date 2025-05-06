# TravelShare Website

![Build Status](https://img.shields.io/badge/build-passing-brightgreen) ![License](https://img.shields.io/badge/license-MIT-blue)

## Table of Contents

- [TravelShare Website](#travelshare-website)
  - [Table of Contents](#table-of-contents)
  - [Overview](#overview)
  - [Features](#features)
  - [Tech Stack](#tech-stack)
  - [Installation](#installation)
    - [Prerequisites](#prerequisites)
    - [Steps](#steps)
    - [Installing wkhtmltopdf and wkhtmltoimage](#installing-wkhtmltopdf-and-wkhtmltoimage)
  - [Environment Variables](#environment-variables)
  - [Development Setup](#development-setup)
  - [Database Configuration](#database-configuration)
  - [Running Tests](#running-tests)
  - [Usage Scenarios](#usage-scenarios)
  - [Deployment](#deployment)
  - [Folder Structure](#folder-structure)
  - [Contributing](#contributing)
  - [License](#license)
  - [Contact](#contact)

## Overview

TravelShare is a web application designed for travelers to share their experiences, book trips, and manage reservations. It provides a platform for users to connect, explore travel opportunities, and generate insights from their travel activities. This project is ideal for travel enthusiasts, agencies, and anyone looking to streamline travel planning and sharing.

## Features

- **User Management**: Registration, authentication, and profile updates.
- **Reservations**: Book, modify, and cancel reservations.
- **Content Sharing**: Share travel experiences through posts and media.
- **Reporting**: Generate reports on user activity and travel trends.
- **Admin Controls**: Manage users, reservations, and system settings.

## Tech Stack

- **Languages**: PHP 8.1+, JavaScript
- **Frameworks**: Symfony 6+, Tailwind CSS
- **Database**: MySQL
- **Tools**: Webpack Encore, Composer, Node.js, wkhtmltopdf

## Installation

### Prerequisites

Ensure you have the following installed:

- PHP 8.1+
- Composer
- MySQL
- Node.js with npm or Bun

### Steps

1. Clone the repository:

   ```sh
   git clone https://github.com/mAmineChniti/TravelShare-Symfony.git
   cd TravelShare-Symfony
   ```

2. Install PHP dependencies:

   ```sh
   composer install
   ```

3. Install front-end dependencies:

   ```sh
   npm install
   # or if you prefer Bun
   bun install
   ```

4. Configure environment variables:
   - Copy the `.env` file:

     ```sh
     cp .env.example .env
     ```

   - Update the `.env` file with the following variables:

     ```env
     APP_SECRET=your_secret_key
     DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/travelshare
     MAILER_DSN=smtp://localhost
     MAILER_PASSWORD=your_mailer_password
     WKHTMLTOPDF_PATH=/path/to/wkhtmltopdf
     WKHTMLTOIMAGE_PATH=/path/to/wkhtmltoimage
     IMAGE_PROFANITY_USER="your_image_profanity_user"
     IMAGE_PROFANITY_API_KEY="your_image_profanity_api_key"
     GEMINI_API_KEY="your_gemini_api_key"
     COUNTRY_CURRENCY_API_TOKEN="your_country_currency_api_token"
     EXCHANGE_API_KEY="your_exchange_api_key"
     ```

5. Add environment variables to `services.yaml`:

   Open the `config/services.yaml` file and ensure the environment variables are mapped to parameters:

   ```yaml
   parameters:
       app.exchange_api_key: '%env(EXCHANGE_API_KEY)%'
       app.country_currency_api_token: '%env(COUNTRY_CURRENCY_API_TOKEN)%'
       app.gemini_api_key: '%env(GEMINI_API_KEY)%'
       app.image_profanity_api_user: '%env(IMAGE_PROFANITY_USER)%'
       app.image_profanity_api_key: '%env(IMAGE_PROFANITY_API_KEY)%'
       app.mailer_password: '%env(MAILER_PASSWORD)%'
   ```

6. Access environment variables in your code:

   Use the `getParameter` method to fetch the values in your Symfony services or controllers. For example:

   ```php
   $imageProfanityUser = $this->getParameter('app.image_profanity_api_user');
   $exchangeApiKey = $this->getParameter('app.exchange_api_key');
   ```

7. Set up the database:

   ```sh
   symfony console doctrine:database:create
   symfony console doctrine:migrations:migrate
   ```

8. Start the asset watcher:

   ```sh
   npm run watch
   # or if you prefer Bun
   bun run watch
   ```

9. Start the Symfony server:

   ```sh
   symfony server:start
   ```

10. Access the application in your browser:

    ```sh
    http://127.0.0.1:8000
    ```

### Installing wkhtmltopdf and wkhtmltoimage

To generate PDFs, the application relies on `wkhtmltopdf` and `wkhtmltoimage`. Follow these steps to install them:

1. **Linux**:

   ```sh
   sudo apt-get install -y wkhtmltopdf
   ```

2. **MacOS**:

   ```sh
   brew install wkhtmltopdf
   ```

3. **Windows**:
   - Download the installer from [wkhtmltopdf.org](https://wkhtmltopdf.org/).
   - Follow the installation instructions.

After installation, ensure the binaries are accessible in your system's PATH or update the `.env` file with their paths:

```env
WKHTMLTOPDF_PATH=/path/to/wkhtmltopdf
WKHTMLTOIMAGE_PATH=/path/to/wkhtmltoimage
```

## Environment Variables

The following environment variables are required:

- `DATABASE_URL`: Database connection string (e.g., `mysql://user:password@host:port/dbname`).
- `WKHTMLTOPDF_PATH`: Path to the `wkhtmltopdf` binary.
- `WKHTMLTOIMAGE_PATH`: Path to the `wkhtmltoimage` binary.
- `MAILER_PASSWORD`: Password for the mailer service (e.g., `your_mailer_password`).
- `IMAGE_PROFANITY_USER`: User ID for the image profanity API (e.g., `your_image_profanity_user`).
- `IMAGE_PROFANITY_API_KEY`: API key for the image profanity service (e.g., `your_image_profanity_api_key`).
- `GEMINI_API_KEY`: API key for the Gemini service (e.g., `your_gemini_api_key`).
- `COUNTRY_CURRENCY_API_TOKEN`: API token for the country currency service (e.g., `your_country_currency_api_token`).
- `EXCHANGE_API_KEY`: API key for the exchange rate service (e.g., `your_exchange_api_key`).

Define these variables in the `.env` file.

## Development Setup

To start the development server:

```sh
symfony server:start
```

To watch and compile assets:

```sh
npm run watch
# or if you prefer Bun
bun run watch
```

## Database Configuration

Ensure your database is running and accessible. Update the `DATABASE_URL` in the `.env` file with your database credentials. Use the following commands to set up the database:

```sh
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

## Running Tests

To run the test suite:

```sh
./bin/phpunit
```

Ensure you have PHPUnit installed and configured.

## Usage Scenarios

- **Booking a Trip**: Users can browse available trips, select one, and make a reservation.
- **Sharing Experiences**: Users can post travel stories and upload photos.
- **Admin Management**: Admins can manage users, reservations, and system settings.

## Deployment

To deploy the application:

1. Ensure the production environment is configured in `.env`.
2. Build assets for production:

   ```sh
   npm run build
   ```

3. Use a web server like Apache or Nginx to serve the application.

## Folder Structure

- **assets/**: Front-end assets (JavaScript, CSS).
- **config/**: Application configuration files.
- **migrations/**: Database migration files.
- **public/**: Publicly accessible files (e.g., index.php, images).
- **src/**: Application source code (controllers, entities, etc.).
- **templates/**: Twig templates for rendering views.
- **tests/**: Test cases.
- **translations/**: Translation files.
- **var/**: Cache and logs.
- **vendor/**: Composer dependencies.

## Contributing

We welcome contributions! Please read the [Contributor Guide](CONTRIBUTING.md) for guidelines.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

Developed by Amine Chniti. For inquiries, reach out via [GitHub](https://github.com/mAmineChniti).
