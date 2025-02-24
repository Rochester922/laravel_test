<template>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Contact Us</div>
                <div class="card-body">
                    <form @submit.prevent="submitForm">
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
                            >
                            <div class="invalid-feedback" v-if="errors.name">
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
                            >
                            <div class="invalid-feedback" v-if="errors.email">
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
                            ></textarea>
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
            errorToast: null
        }
    },
    created() {
        if (!window.Laravel?.csrfToken) {
            console.error('CSRF token not found');
            this.errorMessage = 'Application configuration error';
            return;
        }
        axios.defaults.headers.common['X-CSRF-TOKEN'] = window.Laravel.csrfToken;
    },
    mounted() {
        this.successToast = new bootstrap.Toast(this.$refs.successToast);
        this.errorToast = new bootstrap.Toast(this.$refs.errorToast);
        
        if (this.errorMessage) {
            this.errorToast.show();
        }
    },
    methods: {
        async submitForm() {
            this.loading = true;
            this.errors = {};
            
            try {
                const response = await axios.post('/api/contact', this.form);
                this.successMessage = 'Message sent successfully!';
                this.successToast.show();
                this.form = { name: '', email: '', message: '', website: '' };
            } catch (error) {
                if (error.response?.data?.errors) {
                    this.errors = error.response.data.errors;
                } else {
                    this.errorMessage = error.response?.data?.message || 'An error occurred';
                    this.errorToast.show();
                }
            } finally {
                this.loading = false;
            }
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