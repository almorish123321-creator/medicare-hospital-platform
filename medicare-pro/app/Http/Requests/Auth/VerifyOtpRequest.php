<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class VerifyOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
            'otp' => [
                'required',
                'string',
                'size:6',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $key = 'otp:' . strtolower($this->input('email'));
                    if (!RateLimiter::tooManyAttempts($key, 5)) {
                        // Attempt is available, just decrement
                        RateLimiter::hit($key, 300); // 5-minute window per attempt

                        // Check stored OTP
                        $stored = cache()->get($key . ':code');
                        if (!$stored || $stored !== $value) {
                            $fail(__('validation.invalid_otp'));
                        }
                    } else {
                        $fail(__('auth.throttle', [
                            'seconds' => RateLimiter::availableIn($key),
                            'minutes' => ceil(RateLimiter::availableIn($key) / 60),
                        ]));
                    }
                },
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.exists' => __('validation.exists', ['attribute' => __('validation.attributes.email')]),
            'otp.required' => __('validation.required', ['attribute' => __('validation.attributes.otp')]),
            'otp.size' => __('validation.size.string', ['attribute' => __('validation.attributes.otp'), 'size' => 6]),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => __('validation.attributes.email'),
            'otp' => __('validation.attributes.otp'),
        ];
    }
}
