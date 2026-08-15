<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHospitalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2', 'max:255'],
            'name_en' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:hospitals,email'],
            'phone' => ['required', 'string', 'max:50', 'regex:/^[+]?[\d\s\-\(\)]{7,50}$/'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            'subscription_plan_id' => ['required', 'integer', 'exists:subscription_plans,id'],
            'admin_user' => ['required', 'array'],
            'admin_user.name' => ['required', 'string', 'min:2', 'max:255'],
            'admin_user.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'admin_user.phone' => ['required', 'string', 'max:20', 'regex:/^[+]?[\d\s\-\(\)]{7,20}$/', 'unique:users,phone'],
            'admin_user.password' => ['required', 'string', 'min:8', 'confirmed'],
            'admin_user.password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures the subscription plan is active.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $subscriptionPlanId = $this->input('subscription_plan_id');

            if ($subscriptionPlanId) {
                $plan = \App\Models\SubscriptionPlan::find($subscriptionPlanId);
                if ($plan && $plan->status !== 'active') {
                    $validator->errors()->add('subscription_plan_id', __('validation.subscription_plan_inactive'));
                }
            }

            // Ensure hospital email and admin email are different
            $hospitalEmail = $this->input('email');
            $adminEmail = $this->input('admin_user.email');

            if ($hospitalEmail && $adminEmail && $hospitalEmail === $adminEmail) {
                $validator->errors()->add('admin_user.email', __('validation.admin_email_same_as_hospital'));
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name_ar.required' => __('validation.required', ['attribute' => __('validation.attributes.name_ar')]),
            'name_ar.string' => __('validation.string', ['attribute' => __('validation.attributes.name_ar')]),
            'name_ar.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name_ar'), 'min' => 2]),
            'name_ar.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name_ar'), 'max' => 255]),
            'name_en.required' => __('validation.required', ['attribute' => __('validation.attributes.name_en')]),
            'name_en.string' => __('validation.string', ['attribute' => __('validation.attributes.name_en')]),
            'name_en.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name_en'), 'min' => 2]),
            'name_en.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name_en'), 'max' => 255]),
            'email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            'phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'phone.regex' => __('validation.phone_format'),
            'address.required' => __('validation.required', ['attribute' => __('validation.attributes.address')]),
            'address.string' => __('validation.string', ['attribute' => __('validation.attributes.address')]),
            'address.min' => __('validation.min.string', ['attribute' => __('validation.attributes.address'), 'min' => 5]),
            'address.max' => __('validation.max.string', ['attribute' => __('validation.attributes.address'), 'max' => 500]),
            'logo.image' => __('validation.image', ['attribute' => __('validation.attributes.logo')]),
            'logo.mimes' => __('validation.mimes', ['attribute' => __('validation.attributes.logo'), 'values' => 'JPEG, PNG, JPG, SVG, WebP']),
            'logo.max' => __('validation.max.file', ['attribute' => __('validation.attributes.logo'), 'max' => 2048]),
            'subscription_plan_id.required' => __('validation.required', ['attribute' => __('validation.attributes.subscription_plan_id')]),
            'subscription_plan_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.subscription_plan_id')]),
            'admin_user.required' => __('validation.required', ['attribute' => __('validation.attributes.admin_user')]),
            'admin_user.array' => __('validation.array', ['attribute' => __('validation.attributes.admin_user')]),
            'admin_user.name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'admin_user.name.string' => __('validation.string', ['attribute' => __('validation.attributes.name')]),
            'admin_user.name.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 2]),
            'admin_user.name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => 255]),
            'admin_user.email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'admin_user.email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'admin_user.email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            'admin_user.phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'admin_user.phone.unique' => __('validation.unique', ['attribute' => __('validation.attributes.phone')]),
            'admin_user.phone.regex' => __('validation.phone_format'),
            'admin_user.password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'admin_user.password.min' => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
            'admin_user.password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
            'admin_user.password_confirmation.required' => __('validation.required', ['attribute' => __('validation.attributes.password_confirmation')]),
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
            'name_ar' => __('validation.attributes.name_ar'),
            'name_en' => __('validation.attributes.name_en'),
            'email' => __('validation.attributes.email'),
            'phone' => __('validation.attributes.phone'),
            'address' => __('validation.attributes.address'),
            'logo' => __('validation.attributes.logo'),
            'subscription_plan_id' => __('validation.attributes.subscription_plan_id'),
            'admin_user' => __('validation.attributes.admin_user'),
            'admin_user.name' => __('validation.attributes.name'),
            'admin_user.email' => __('validation.attributes.email'),
            'admin_user.phone' => __('validation.attributes.phone'),
            'admin_user.password' => __('validation.attributes.password'),
            'admin_user.password_confirmation' => __('validation.attributes.password_confirmation'),
        ];
    }
}
