# Contributor Guide for TravelShare-Web

Thank you for your interest in contributing to TravelShare-Web! We welcome contributions of all kinds, whether you're fixing a bug, adding a feature, or improving documentation. This guide will help you get started and ensure that your contributions align with the project's goals and standards.

---

## Table of Contents

- [Contributor Guide for TravelShare-Web](#contributor-guide-for-travelshare-web)
  - [Table of Contents](#table-of-contents)
  - [Introduction](#introduction)
  - [Code of Conduct](#code-of-conduct)
  - [Getting Started](#getting-started)
  - [Project Structure \& Dependencies](#project-structure--dependencies)
  - [2. **CSS \& Styling**](#2-css--styling)
  - [Templates \& Components](#templates--components)
  - [Environment Variables](#environment-variables)
  - [Branching and Commit Guidelines](#branching-and-commit-guidelines)
  - [Writing and Running Tests](#writing-and-running-tests)
  - [How to Open a Pull Request](#how-to-open-a-pull-request)
  - [Code Review Process](#code-review-process)
  - [Best Practices](#best-practices)
  - [Reporting Violations](#reporting-violations)
  - [Quick Reference](#quick-reference)

---

## Introduction

TravelShare-Web is a collaborative project aimed at creating a robust platform for travelers to share experiences and manage reservations. We value contributions from developers of all skill levels and backgrounds. Whether you're fixing a typo or implementing a major feature, your input is appreciated.

## Code of Conduct

By participating in this project, you agree to abide by our [Code of Conduct](CODE_OF_CONDUCT.md). Please treat everyone with respect and professionalism.

## Getting Started

1. **Fork the Repository**: Start by forking the repository to your GitHub account.
2. **Clone the Repository**: Clone your forked repository to your local machine:

   ```sh
   git clone https://github.com/your-username/TravelShare-Symfony.git
   cd TravelShare-Symfony
   ```

3. **Set Up the Environment**:
   - Copy the `.env` file:

     ```sh
     cp .env.example .env
     ```

   - Update the `.env` file with your environment variables (see [README.md](README.md) for details).

4. **Install Dependencies**:
   - Install PHP dependencies:

     ```sh
     composer install
     ```

   - Install front-end dependencies:

     ```sh
     npm install
     # or if you prefer Bun
     bun install
     ```

5. **Run the Application**:
   - Start the Symfony server:

     ```sh
     symfony server:start
     ```

   - Start the asset watcher:

     ```sh
     npm run watch
     # or if you prefer Bun
     bun run watch
     ```

## Project Structure & Dependencies

- **JavaScript Dependencies**:
  - Declare all JavaScript and front-end dependencies in [`package.json`](package.json).
- Do not use standalone or downloaded `.js` files except for the single main `app.js` file required by Tailwind CSS.
  - Use **CDN imports** only when absolutely necessary.
  - Use Symfony’s recommended asset builder (**Webpack Encore** or **Vite**) to bundle assets.
- **JavaScript Placement**:
  - Place small JavaScript snippets directly in the Twig templates.
  - For larger scripts, import them into `app.js` and manage them through the asset pipeline.

---

## 2. **CSS & Styling**

- **Single CSS File**:
  - Only use the `app.css` file for all CSS-related configurations.
  - Do not create additional `.css` files under [`assets`](assets) or [`public`](public).
- **Inline TailwindCSS**:
  - Use **Tailwind CSS utility classes** directly in the templates (Twig, React/Vue, or plain JS).
  - Avoid using SASS, SCSS, LESS, styled-components, or any CSS-in-JS library.
- **Tailwind Directives**:
  - Tailwind directives like `@apply` and `@layer` are allowed but must only be used in `app.css`.
- **Minimal `<style>` Tags**:
  - If a `<style>` tag is necessary, ensure that it contains only minimal and file-specific styles.
  - These styles must be used exclusively within the file where the `<style>` tag is defined.

## Templates & Components

- **Twig Templates**:
  - Use inline Tailwind CSS classes for styling (e.g., `<div class="flex items-center space-x-4">...</div>`).
  - Do not use `<link href="*.css">` or `<style>` blocks in templates unless they follow the minimal and file-specific rule mentioned above.
  - Avoid importing SASS/SCSS modules or external stylesheets.
- **Component Styling**:
  - Ensure all components are styled using Tailwind utility classes directly in the markup.

## Environment Variables

Ensure the following environment variables are set in your `.env` file:

```env
APP_SECRET=your_secret_key
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/travelshare
MAILER_DSN=smtp://localhost
MAILER_PASSWORD=your_mailer_password
WKHTMLTOPDF_PATH=/path/to/wkhtmltopdf
WKHTMLTOIMAGE_PATH=/path/to/wkhtmltoimage
IMAGE_PROFANITY_USER=your_image_profanity_user
IMAGE_PROFANITY_API_KEY=your_image_profanity_api_key
GEMINI_API_KEY=your_gemini_api_key
COUNTRY_CURRENCY_API_TOKEN=your_country_currency_api_token
EXCHANGE_API_KEY=your_exchange_api_key
```

Map these variables in `services.yaml`:

```yaml
parameters:
    app.exchange_api_key: '%env(EXCHANGE_API_KEY)%'
    app.country_currency_api_token: '%env(COUNTRY_CURRENCY_API_TOKEN)%'
    app.gemini_api_key: '%env(GEMINI_API_KEY)%'
    app.image_profanity_api_user: '%env(IMAGE_PROFANITY_USER)%'
    app.image_profanity_api_key: '%env(IMAGE_PROFANITY_API_KEY)%'
    app.mailer_password: '%env(MAILER_PASSWORD)%'
```

Access them in your code using:

```php
$this->getParameter('app.mailer_password');
```

## Branching and Commit Guidelines

- **Branching**:
  - Use the `main` branch for production-ready code.
  - Create feature branches for new features (e.g., `feature/add-user-authentication`).
  - Use bugfix branches for fixes (e.g., `bugfix/fix-login-error`).

- **Commit Messages**:
  - Use clear and descriptive commit messages.
  - Follow this format:

    ```
    [Type] Short description

    Detailed explanation (if necessary).
    ```

    Examples:
    - `[Feature] Add user authentication`
    - `[Bugfix] Fix login error on mobile devices`

## Writing and Running Tests

- **Writing Tests**:
  - Add tests for any new features or bug fixes.
  - Place test files in the `tests/` directory.

- **Running Tests**:
  - Use PHPUnit to run tests:

    ```sh
    ./bin/phpunit
    ```

  - Ensure all tests pass before submitting your changes.

## How to Open a Pull Request

1. **Push Your Changes**:
   - Push your feature or bugfix branch to your forked repository:

     ```sh
     git push origin feature/your-branch-name
     ```

2. **Open a Pull Request**:
   - Go to the original repository on GitHub.
   - Click on the "Pull Requests" tab and open a new pull request.
   - Provide a clear title and description of your changes.

3. **Link Issues**:
   - If your pull request addresses an open issue, link it using keywords like `Fixes #123`.

## Code Review Process

- Pull requests will be reviewed by maintainers or collaborators.
- Feedback will be provided for improvements or changes.
- Once approved, your pull request will be merged into the `main` branch.

## Best Practices

- **Do Not Commit Sensitive Data**:
  - Ensure no sensitive data (e.g., API keys, passwords) is committed.

- **Follow Coding Standards**:
  - Use PSR-12 coding standards for PHP.
  - Run linters and formatters before committing.

- **Keep Changes Small**:
  - Submit focused pull requests with a single purpose.

## Reporting Violations

For any rule violations, report the following:

- **File**: Specify the file name and path.
- **Line Number**: Indicate the line number where the violation occurs.
- **Suggestion**: Provide a brief suggestion for fixing the issue.
  - Example: *"This component is using a `.scss` import. Replace it with inline Tailwind classes."*

## Quick Reference

- **Allowed**:
  - Inline Tailwind CSS classes in templates.
  - Tailwind directives (`@apply`, `@layer`) in `app.css`.
  - JavaScript managed via [`package.json`](package.json) and `app.js`.
  - Symfony’s asset builder (Webpack Encore or Vite).

- **Not Allowed**:
  - Additional `.css` files.
  - SASS, SCSS, LESS, or CSS-in-JS libraries.
  - Standalone or downloaded `.js` files (except `app.js`).

---

By following these guidelines, we ensure that the project remains consistent, maintainable, and welcoming to all contributors. Thank you for your contributions!
