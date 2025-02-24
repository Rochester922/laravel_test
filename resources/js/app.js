import { createApp } from "vue";
import axios from 'axios';
import TextBlock from "./components/TextBlock.vue";
import ContactForm from "./components/ContactForm.vue";
import * as bootstrap from "bootstrap";

// Import images
import.meta.glob(["../../images/**"]);

// Make axios available globally
window.axios = axios;

// Make bootstrap available globally
window.bootstrap = bootstrap;

// Create and mount Vue app
const app = createApp({});
app.component("text-block", TextBlock);  // Use kebab-case for consistency
app.component("contact-form", ContactForm);
app.mount("#app");