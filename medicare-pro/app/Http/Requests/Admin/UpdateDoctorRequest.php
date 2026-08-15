<?php

namespace App\Http\Requests\Admin;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorRequest extends FormRequest
{
    use HasHospitalAccess;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isHospitalAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $hospitalId = $this->getHospitalId();
        $doctor = $this->route('doctor');

        $userId = $doctor ? $doctor->user_id : null;

        return [
            'user' => ['sometimes', 'array'],
            'user.name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'user.email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'user.phone' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[+]?[\d\s\-\(\)]{7,20}$/', 'unique:users,phone,' . $userId],
            'user.password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
            'user.password_confirmation' => ['sometimes', 'nullable', 'string', 'min:8'],
            'specialty' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'department_id' => ['sometimes', 'required', 'integer', 'exists:departments,id,hospital_id,' . $hospitalId],
            'license_number' => ['sometimes', 'required', 'string', 'max:100', 'unique:doctors,license_number,' . ($doctor ? $doctor->id : null)],
            'bio' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'consultation_fee' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999.99'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures the department belongs to the same hospital when updated.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $hospitalId = $this->getHospitalId();
            $departmentId = $this->input('department_id');

            // Verify department belongs to the hospital
            if ($departmentId && $hospitalId) {
                $department = \App\Models\Department::where('id', $departmentId)
                    ->where('hospital_id', $hospitalId)
                    ->first();

                if (!$department) {
                    $validator->errors()->add('department_id', __('validation.department_not_in_hospital'));
                }
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
            'user.password.min' => __('validation.min.string', ['attribute' => __('validation.attributes.password'), 'min' => 8]),
            'user.password.confirmed' => __('validation.confirmed', ['attribute' => __('validation.attributes.password')]),
            'specialty.required' => __('validation.required', ['attribute' => __('validation.attributes.specialty')]),
            'specialty.string' => __('validation.string', ['attribute' => __('validation.attributes.specialty')]),
            'specialty.min' => __('validation.min.string', ['attribute' => __('validation.attributes.specialty'), 'min' => 2]),
            'specialty.max' => __('validation.max.string', ['attribute' => __('validation.attributes.specialty'), 'max' => 255]),
            'department_id.required' => __('validation.required', ['attribute' => __('validation.attributes.department_id')]),
            'department_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.department_id')]),
            'license_number.required' => __('validation.required', ['attribute' => __('validation.attributes.license_number')]),
            'license_number.unique' => __('validation.unique', ['attribute' => __('validation.attributes.license_number')]),
            'bio.max' => __('validation.max.string', ['attribute' => __('validation.attributes.bio'), 'max' => 2000]),
            'consultation_fee.required' => __('validation.required', ['attribute' => __('validation.attributes.consultation_fee')]),
            'consultation_fee.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.consultation_fee')]),
            'consultation_fee.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.consultation_fee'), 'min' => 0]),
            'consultation_fee.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.consultation_fee'), 'max' => 99999.99]),
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
            'specialty' => __('validation.attributes.specialty'),
            'department_id' => __('validation.attributes.department_id'),
            'license_number' => __('validation.attributes.license_number'),
            'bio' => __('validation.attributes.bio'),
            'consultation_fee' => __('validation.attributes.consultation_fee'),
        ];
    }
}
