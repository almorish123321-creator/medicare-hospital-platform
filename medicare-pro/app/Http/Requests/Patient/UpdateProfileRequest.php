<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $userId = $this->user()->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'phone' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[+]?[\d\s\-\(\)]{7,20}$/', 'unique:users,phone,' . $userId],
            'date_of_birth' => ['sometimes', 'nullable', 'date', 'before:today', 'after:1900-01-01'],
            'gender' => ['sometimes', 'nullable', 'string', 'in:male,female,other'],
            'blood_type' => ['sometimes', 'nullable', 'string', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'allergies' => ['sometimes', 'nullable', 'array'],
            'allergies.*' => ['string', 'max:255'],
            'chronic_diseases' => ['sometimes', 'nullable', 'array'],
            'chronic_diseases.*' => ['string', 'max:255'],
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
            'name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'name.string' => __('validation.string', ['attribute' => __('validation.attributes.name')]),
            'name.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 2]),
            'name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => 255]),
            'phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'phone.unique' => __('validation.unique', ['attribute' => __('validation.attributes.phone')]),
            'phone.regex' => __('validation.phone_format'),
            'date_of_birth.date' => __('validation.date', ['attribute' => __('validation.attributes.date_of_birth')]),
            'date_of_birth.before' => __('validation.before', ['attribute' => __('validation.attributes.date_of_birth'), 'date' => 'today']),
            'date_of_birth.after' => __('validation.after', ['attribute' => __('validation.attributes.date_of_birth'), 'date' => '1900-01-01']),
            'gender.in' => __('validation.in', ['attribute' => __('validation.attributes.gender')]),
            'blood_type.in' => __('validation.in', ['attribute' => __('validation.attributes.blood_type')]),
            'address.max' => __('validation.max.string', ['attribute' => __('validation.attributes.address'), 'max' => 500]),
            'allergies.array' => __('validation.array', ['attribute' => __('validation.attributes.allergies')]),
            'allergies.*.string' => __('validation.string', ['attribute' => __('validation.attributes.allergy')]),
            'allergies.*.max' => __('validation.max.string', ['attribute' => __('validation.attributes.allergy'), 'max' => 255]),
            'chronic_diseases.array' => __('validation.array', ['attribute' => __('validation.attributes.chronic_diseases')]),
            'chronic_diseases.*.string' => __('validation.string', ['attribute' => __('validation.attributes.chronic_disease')]),
            'chronic_diseases.*.max' => __('validation.max.string', ['attribute' => __('validation.attributes.chronic_disease'), 'max' => 255]),
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
            'name' => __('validation.attributes.name'),
            'phone' => __('validation.attributes.phone'),
            'date_of_birth' => __('validation.attributes.date_of_birth'),
            'gender' => __('validation.attributes.gender'),
            'blood_type' => __('validation.attributes.blood_type'),
            'address' => __('validation.attributes.address'),
            'allergies' => __('validation.attributes.allergies'),
            'chronic_diseases' => __('validation.attributes.chronic_diseases'),
        ];
    }
}
