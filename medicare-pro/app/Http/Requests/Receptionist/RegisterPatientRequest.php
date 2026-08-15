<?php

namespace App\Http\Requests\Receptionist;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class RegisterPatientRequest extends FormRequest
{
    use HasHospitalAccess;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isReceptionist();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $hospitalId = $this->getHospitalId();

        return [
            'user' => ['required', 'array'],
            'user.name' => ['required', 'string', 'min:2', 'max:255'],
            'user.email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'user.phone' => ['required', 'string', 'max:20', 'regex:/^[+]?[\d\s\-\(\)]{7,20}$/', 'unique:users,phone'],
            'user.password' => ['required', 'string', 'min:8', 'confirmed'],
            'user.password_confirmation' => ['required', 'string', 'min:8'],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
            'blood_type' => ['nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'address' => ['nullable', 'string', 'max:500'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:patients,national_id'],
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
            'user.required' => __('validation.required', ['attribute' => __('validation.attributes.user_data')]),
            'user.array' => __('validation.array', ['attribute' => __('validation.attributes.user_data')]),
            'user.name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'user.name.string' => __('validation.string', ['attribute' => __('validation.attributes.name')]),
            'user.name.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 2]),
            'user.name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => 255]),
            'user.email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'user.email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'user.email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            'user.phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'user.phone.unique' => __('validation.unique', ['attribute' => __('validation.attributes.phone')]),
            'user.phone.regex' => __('validation.phone_format'),
            'user.password.required' => __('validation.required', ['attribute' => __('validation.attributes.password')]),
            'user.password.min' => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
            'user.password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
            'user.password_confirmation.required' => __('validation.required', ['attribute' => __('validation.attributes.password_confirmation')]),
            'date_of_birth.date' => __('validation.date', ['attribute' => __('validation.attributes.date_of_birth')]),
            'date_of_birth.before' => __('validation.before', ['attribute' => __('validation.attributes.date_of_birth'), 'date' => 'today']),
            'date_of_birth.after' => __('validation.after', ['attribute' => __('validation.attributes.date_of_birth'), 'date' => '1900-01-01']),
            'gender.in' => __('validation.in', ['attribute' => __('validation.attributes.gender')]),
            'blood_type.in' => __('validation.in', ['attribute' => __('validation.attributes.blood_type')]),
            'address.max' => __('validation.max.string', ['attribute' => __('validation.attributes.address'), 'max' => 500]),
            'national_id.unique' => __('validation.unique', ['attribute' => __('validation.attributes.national_id')]),
            'national_id.max' => __('validation.max.string', ['attribute' => __('validation.attributes.national_id'), 'max' => 50]),
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
            'user' => __('validation.attributes.user_data'),
            'user.name' => __('validation.attributes.name'),
            'user.email' => __('validation.attributes.email'),
            'user.phone' => __('validation.attributes.phone'),
            'user.password' => __('validation.attributes.password'),
            'user.password_confirmation' => __('validation.attributes.password_confirmation'),
            'date_of_birth' => __('validation.attributes.date_of_birth'),
            'gender' => __('validation.attributes.gender'),
            'blood_type' => __('validation.attributes.blood_type'),
            'address' => __('validation.attributes.address'),
            'national_id' => __('validation.attributes.national_id'),
        ];
    }
}
