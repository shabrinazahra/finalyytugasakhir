<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Semua user boleh mengakses request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi form login.
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Pesan error validasi dalam Bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Form email harus diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Form password harus diisi.',
        ];
    }

    /**
     * Cek rate limiter (throttle) untuk mencegah brute force.
     * Dipanggil dari controller jika diperlukan.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Key unik untuk rate limiter berdasarkan email + IP.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')) . '|' . $this->ip());
    }
}
