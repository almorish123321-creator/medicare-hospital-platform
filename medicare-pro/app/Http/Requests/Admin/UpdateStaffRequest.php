<?php

namespace App\Http\Requests\Admin;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
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
        $staff = $this->route('staff');
        $userId = $staff ? $staff->id : null;
        $hospitalId = $this->getHospitalId();

        return [
            'name' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $userId],
            'phone' => ['sometimes', 'required', 'string', 'max:20', 'regex:/^[+]?[\d\s\-\(\)]{7,20}$/', 'unique:users,phone,' . $userId],
            'role' => ['sometimes', 'required', 'string', 'in:receptionist,nurse,pharmacist,hospital_admin'],
            'department_id' => ['sometimes', 'nullable', 'integer', 'exists:departments,id,hospital_id,' . $hospitalId],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures staff member belongs to the same hospital
     * and prevents admin from deactivating themselves.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $hospitalId = $this->getHospitalId();
            $staff = $this->route('staff');
            $isActive = $this->input('is_active');

            // Verify staff belongs to the same hospital
            if ($staff && $staff->hospital_id !== $hospitalId) {
                $validator->errors()->add('staff', __('validation.staff_not_in_hospital'));
            }

            // Prevent admin from deactivating themselves
            if ($staff && $staff->id === $this->user()->id && $isActive === false) {
                $validator->errors()->add('is_active', __('validation.cannot_deactivate_self'));
            }

            // Prevent changing role of the admin themselves
            if ($staff && $staff->id === $this->user()->id && $this->input('role')) {
                $validator->errors()->add('role', __('validation.cannot_change_own_role'));
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
            'name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
            'name.string' => __('validation.string', ['attribute' => __('validation.attributes.name')]),
            'name.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name'), 'min' => 2]),
            'name.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name'), 'max' => 255]),
            'email.required' => __('validation.required', ['attribute' => __('validation.attributes.email')]),
            'email.email' => __('validation.email', ['attribute' => __('validation.attributes.email')]),
            'email.unique' => __('validation.unique', ['attribute' => __('validation.attributes.email')]),
            'phone.required' => __('validation.required', ['attribute' => __('validation.attributes.phone')]),
            'phone.unique' => __('validation.unique', ['attribute' => __('validation.attributes.phone')]),
            'phone.regex' => __('validation.phone_format'),
            'role.required' => __('validation.required', ['attribute' => __('validation.attributes.role')]),
            'role.in' => __('validation.in', ['attribute' => __('validation.attributes.role')]),
            'department_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.department_id')]),
            'is_active.required' => __('validation.required', ['attribute' => __('validation.attributes.is_active')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.is_active')]),
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
            'email' => __('validation.attributes.email'),
            'phone' => __('validation.attributes.phone'),
            'role' => __('validation.attributes.role'),
            'department_id' => __('validation.attributes.department_id'),
            'is_active' => __('validation.attributes.is_active'),
        ];
    }
}
