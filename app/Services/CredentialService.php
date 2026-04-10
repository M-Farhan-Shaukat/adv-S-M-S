<?php

namespace App\Services;

use App\Mail\WelcomeCredentialsMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CredentialService
{
    /**
     * Generate a random readable password
     */
    public static function generatePassword(int $length = 10): string
    {
        return Str::password($length, letters: true, numbers: true, symbols: false);
    }

    /**
     * Send welcome credentials email
     */
    public static function sendCredentials(
        string $email,
        string $name,
        string $password,
        string $role,
        string $schoolName,
        string $loginUrl,
        ?string $portalNote = null
    ): bool {
        try {
            Mail::to($email)->send(new WelcomeCredentialsMail(
                recipientName: $name,
                email: $email,
                password: $password,
                role: $role,
                schoolName: $schoolName,
                loginUrl: $loginUrl,
                portalNote: $portalNote,
            ));
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to send credentials to {$email}: " . $e->getMessage());
            return false;
        }
    }
}
