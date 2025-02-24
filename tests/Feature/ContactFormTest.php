<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactFormTest extends TestCase
{
    public function test_contact_form_submission(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message'
        ]);

        $response->assertStatus(200);
        Mail::assertSent(ContactFormMail::class);
        
        // Assert the submission was saved to database
        $this->assertDatabaseHas('contact_submissions', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message'
        ]);
    }

    public function test_contact_form_validation(): void
    {
        $response = $this->postJson('/api/contact', [
            'name' => '',
            'email' => 'not-an-email',
            'message' => ''
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name', 'email', 'message']);
    }

    public function test_honeypot_validation(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
            'website' => 'spam' // Honeypot should be empty
        ]);

        $response->assertStatus(200); // Should return success to fool spammers
        Mail::assertNotSent(ContactFormMail::class); // Email should not be sent
    }

    public function test_csrf_token_validation(): void
    {
        $response = $this->postJson('/api/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message'
        ], ['X-CSRF-TOKEN' => '']); // Missing CSRF token

        $response->assertStatus(419);
        $response->assertJson(['message' => 'CSRF token missing']);
    }

    public function test_mail_configuration_error(): void
    {
        Mail::fake();
        config(['mail.mailers.smtp.host' => null]); // Simulate missing config

        $response = $this->postJson('/api/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message'
        ]);

        $response->assertStatus(500);
        $response->assertJson(['message' => 'An error occurred while sending the message']);
        Mail::assertNotSent(ContactFormMail::class);
    }
} 