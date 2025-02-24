<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        if (!$request->hasHeader('X-CSRF-TOKEN')) {
            return response()->json(['message' => 'CSRF token missing'], 419);
        }
        
        // Check honeypot
        if (!empty($request->input('website'))) {
            return response()->json(['message' => 'Message sent successfully']);
        }
        
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'message' => 'required|string|max:1000',
                'website' => 'string|max:0' // Honeypot validation
            ]);

            try {
                // Check mail configuration
                if (empty(config('mail.mailers.smtp.host')) || empty(config('mail.mailers.smtp.port'))) {
                    throw new \Exception('SMTP configuration is incomplete');
                }

                // Get recipient email from config, fallback to env
                $recipientEmail = config('mail.contact.recipient') ?? config('mail.from.address');
                if (empty($recipientEmail)) {
                    throw new \Exception('Recipient email not configured');
                }

                Mail::to($recipientEmail)->send(new ContactFormMail($validated));
            } catch (\Exception $e) {
                \Log::error('Mail sending failed: ' . $e->getMessage());
                throw new \Exception('Failed to send email: ' . $e->getMessage());
            }

            return response()->json(['message' => 'Message sent successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Contact form error: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while sending the message'], 500);
        }
    }
} 