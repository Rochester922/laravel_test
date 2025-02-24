import { createApp } from "vue/dist/vue.esm-bundler";
import TextBlock from "./components/TextBlock.vue";
import ContactForm from "./components/ContactForm.vue";

import.meta.glob(["../../images/**"]);

import * as bootstrap from "bootstrap";
window.bootstrap = bootstrap;

const app = createApp({});
app.component("TextBlock", TextBlock);
app.component("ContactForm", ContactForm);
app.mount("#app");