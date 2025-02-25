<template>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Contact Us</div>
                <div class="card-body">
                    <form @submit.prevent="submitForm" aria-label="Contact Form">
                        <!-- Honeypot field -->
                        <div style="display:none">
                            <input type="text" v-model="form.website" />
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="name" 
                                v-model="form.name"
                                :class="{ 'is-invalid': errors.name }"
                                required
                                aria-required="true"
                                aria-describedby="name-error"
                            >
                            <div class="invalid-feedback" v-if="errors.name" id="name-error">
                                {{ errors.name[0] }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="email" 
                                v-model="form.email"
                                :class="{ 'is-invalid': errors.email }"
                                required
                                aria-required="true"
                                aria-describedby="email-error"
                            >
                            <div class="invalid-feedback" v-if="errors.email" id="email-error">
                                {{ errors.email[0] }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea 
                                class="form-control" 
                                id="message" 
                                rows="5" 
                                v-model="form.message"
                                :class="{ 'is-invalid': errors.message }"
                                required
                                maxlength="1000"
                            ></textarea>
                            <small class="text-muted">{{ form.message.length }}/1000 characters</small>
                            <div class="invalid-feedback" v-if="errors.message">
                                {{ errors.message[0] }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary" :disabled="loading">
                                <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status"></span>
                                {{ loading ? 'Sending...' : 'Send Message' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div ref="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">{{ successMessage }}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
            
            <div ref="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">{{ errorMessage }}</div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import * as bootstrap from 'bootstrap';

export default {
    name: 'ContactForm',
    data() {
        return {
            form: {
                name: '',
                email: '',
                message: '',
                website: ''
            },
            errors: {},
            loading: false,
            successMessage: '',
            errorMessage: '',
            successToast: null,
            errorToast: null,
            loadingTimeout: null,
            lastSubmissionTime: null,
            minimumSubmissionInterval: 60000, // 1 minute in milliseconds
        }
    },
    created() {
        if (!window.Laravel?.csrfToken) {
            console.error('CSRF token not found');
            this.errorMessage = 'Application configuration error';
            return;
        }
        axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;

        // Load saved form data if exists
        const savedForm = localStorage.getItem('contactFormDraft');
        if (savedForm) {
            this.form = JSON.parse(savedForm);
        }
    },
    mounted() {
        this.successToast = new bootstrap.Toast(this.$refs.successToast);
        this.errorToast = new bootstrap.Toast(this.$refs.errorToast);
        
        if (this.errorMessage) {
            this.errorToast.show();
        }
    },
    watch: {
        form: {
            handler(newVal) {
                // Save form data as draft
                localStorage.setItem('contactFormDraft', JSON.stringify(newVal));
            },
            deep: true
        }
    },
    methods: {
        sanitizeInput(input) {
            return input.trim().replace(/[<>]/g, '');
        },

        validateForm() {
            let isValid = true;
            this.errors = {};

            // Name validation
            if (this.form.name.trim().length < 2) {
                this.errors.name = ['Name must be at least 2 characters'];
                isValid = false;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.form.email)) {
                this.errors.email = ['Please enter a valid email address'];
                isValid = false;
            }

            // Message validation
            if (this.form.message.trim().length < 10) {
                this.errors.message = ['Message must be at least 10 characters'];
                isValid = false;
            }

            return isValid;
        },

        async submitForm() {
            // Check rate limiting
            if (this.lastSubmissionTime && Date.now() - this.lastSubmissionTime < this.minimumSubmissionInterval) {
                this.errorMessage = 'Please wait a minute before sending another message';
                this.errorToast.show();
                return;
            }

            if (!this.validateForm()) {
                return;
            }
            this.loading = true;
            this.errors = {};
            
            // Set timeout to prevent infinite loading state
            this.loadingTimeout = setTimeout(() => {
                this.loading = false;
                this.errorMessage = 'Request timed out. Please try again.';
                this.errorToast.show();
            }, 10000); // 10 seconds timeout

            try {
                const sanitizedForm = {
                    name: this.sanitizeInput(this.form.name),
                    email: this.sanitizeInput(this.form.email),
                    message: this.sanitizeInput(this.form.message),
                    website: this.form.website // honeypot field
                };
                
                const response = await axios.post('/api/contact', sanitizedForm);
                clearTimeout(this.loadingTimeout);
                this.successMessage = 'Message sent successfully!';
                this.successToast.show();
                this.form = { name: '', email: '', message: '', website: '' };
                this.lastSubmissionTime = Date.now();
                this.clearSavedData(); // Clear saved data after successful submission
            } catch (error) {
                clearTimeout(this.loadingTimeout);
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    this.errorMessage = error.response?.data?.message || 'An error occurred';
                    this.errorToast.show();
                }
            } finally {
                this.loading = false;
            }
        },
        resetForm() {
            if (this.form.name || this.form.email || this.form.message) {
                if (confirm('Are you sure you want to clear the form?')) {
                    this.form = { name: '', email: '', message: '', website: '' };
                    this.errors = {};
                }
            }
        },
        clearSavedData() {
            localStorage.removeItem('contactFormDraft');
        }
    },
    beforeUnmount() {
        if (this.loadingTimeout) {
            clearTimeout(this.loadingTimeout);
        }
    }
}
</script>

<style scoped>
.toast {
    min-width: 250px;
}
.toast-header {
    padding: 0.5rem 1rem;
}
.btn-close {
    filter: brightness(0) invert(1);
}
</style>