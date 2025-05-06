import "./bootstrap.js";
/*
 *  * Welcome to your app's main JavaScript file!
 *   *
 *    * We recommend including the built version of this JavaScript file
 *     * (and its CSS file) in your base layout (base.html.twig).
 *      */

// any CSS you import will output into a single css file (app.css in this case)
import "./styles/app.css";

// Import Bootstrap JS
import 'bootstrap';

// Import Font Awesome
import '@fortawesome/fontawesome-free/css/all.min.css';

// Initialize Bootstrap color mode
document.addEventListener('DOMContentLoaded', () => {
  // The main color mode functionality is in the Navbar component
  // This ensures Bootstrap components are properly initialized
  const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
  [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
  
  const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
  [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
});
