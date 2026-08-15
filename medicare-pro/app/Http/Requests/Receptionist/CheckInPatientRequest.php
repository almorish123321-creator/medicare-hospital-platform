<?php

namespace App\Http\Requests\Receptionist;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class CheckInPatientRequest extends FormRequest
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
        return [
            'appointment_id' => ['required', 'integer', 'exists:appointments,id'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures the appointment belongs to the receptionist's hospital
     * and is in a valid state for check-in.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $appointmentId = $this->input('appointment_id');
            $hospitalId = $this->getHospitalId();

            $appointment = \App\Models\Appointment::find($appointmentId);

            if (!$appointment) {
                $validator->errors()->add('appointment_id', __('validation.exists', ['attribute' => __('validation.attributes.appointment')]));
                return;
            }

            // Verify appointment belongs to the same hospital
            if ($appointment->hospital_id !== $hospitalId) {
                $validator->errors()->add('appointment_id', __('validation.appointment_not_in_hospital'));
                return;
            }

            // Only confirmed appointments can be checked in
            if ($appointment->status !== 'confirmed') {
                $validator->errors()->add('appointment_id', __('validation.appointment_not_confirmed'));
                return;
            }

            // Cannot check in appointments from past days
            if ($appointment->appointment_date->isPast() && !$appointment->appointment_date->isToday()) {
                $validator->errors()->add('appointment_id', __('validation.appointment_date_past'));
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
            'appointment_id.required' => __('validation.required', ['attribute' => __('validation.attributes.appointment_id')]),
            'appointment_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.appointment_id')]),
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
            'appointment_id' => __('validation.attributes.appointment_id'),
        ];
    }
}
