# Contributor Guide for TravelShare-Web

This guide outlines the rules and best practices for maintaining consistency and quality in the TravelShare-Web project. Please follow these guidelines when contributing to the project.

---

## 1. **Project Structure & Dependencies**

- **JavaScript Dependencies**:
  - Declare all JavaScript and front-end dependencies in [`package.json`](package.json ).
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
  - Do not create additional `.css` files under [`assets`](assets ) or [`public`](public ).
- **Inline TailwindCSS**:
  - Use **Tailwind CSS utility classes** directly in the templates (Twig, React/Vue, or plain JS).
  - Avoid using SASS, SCSS, LESS, styled-components, or any CSS-in-JS library.
- **Tailwind Directives**:
  - Tailwind directives like `@apply` and `@layer` are allowed but must only be used in `app.css`.
- **Minimal `<style>` Tags**:
  - If a `<style>` tag is necessary, ensure that it contains only minimal and file-specific styles.
  - These styles must be used exclusively within the file where the `<style>` tag is defined.

---

## 3. **Templates & Components**

- **Twig Templates**:
  - Use inline Tailwind CSS classes for styling (e.g., `<div class="flex items-center space-x-4">...</div>`).
  - Do not use `<link href="*.css">` or `<style>` blocks in templates unless they follow the minimal and file-specific rule mentioned above.
  - Avoid importing SASS/SCSS modules or external stylesheets.
- **Component Styling**:
  - Ensure all components are styled using Tailwind utility classes directly in the markup.

---

## 4. **Reporting Violations**

For any rule violations, report the following:

- **File**: Specify the file name and path.
- **Line Number**: Indicate the line number where the violation occurs.
- **Suggestion**: Provide a brief suggestion for fixing the issue.
  - Example: *"This component is using a `.scss` import. Replace it with inline Tailwind classes."*

---

## 5. **Best-Practice Checks**

- **Dependencies**:

  - Ensure [`package.json`](package.json ) contains only the necessary packages.
  - Keep lockfiles (`package-lock.json` or `yarn.lock`) up to date.
- **Asset Build Commands**:
  - Verify that asset build commands in `scripts` follow Symfony conventions.
  - Example: `"build": "encore production"` or `"dev": "encore dev-server"`.
- **Codebase Goals**:
  - Keep the codebase lean and consistent.
  - Ensure all styling is managed through [`package.json`](package.json ) and inline Tailwind classes.
  - Avoid standalone stylesheets or CSS preprocessors except for `app.css`.

---

## 6. **Quick Reference**

- **Allowed**:

  - Inline Tailwind CSS classes in templates.
  - Tailwind directives (`@apply`, `@layer`) in `app.css`.
  - JavaScript managed via [`package.json`](package.json ) and `app.js`.
  - Symfony’s asset builder (Webpack Encore or Vite).
  - Minimal `<style>` tags with file-specific and exclusive styles.
- **Not Allowed**:
  - Additional `.css` files.
  - SASS, SCSS, LESS, or CSS-in-JS libraries.
  - Standalone or downloaded `.js` files (except `app.js`).
  - `<style>` blocks or `<link href="*.css">` in templates unless minimal and file-specific.

---

By following these guidelines, we ensure that the project remains consistent, maintainable, and adheres to best practices. Thank you for your contributions!
