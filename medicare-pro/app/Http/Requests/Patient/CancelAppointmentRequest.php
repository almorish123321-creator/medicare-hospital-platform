<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class CancelAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isPatient();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'reason' => ['required', 'string', 'min:3', 'max:500'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures the appointment belongs to the patient and can be cancelled.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $appointment = $this->route('appointment');

            if (!$appointment) {
                $validator->errors()->add('appointment', __('validation.exists', ['attribute' => __('validation.attributes.appointment')]));
                return;
            }

            // Ensure the appointment belongs to the authenticated patient
            if ($appointment->patient_id !== $this->user()->patient->id) {
                $validator->errors()->add('appointment', __('validation.not_owned_appointment'));
                return;
            }

            // Only allow cancellation for certain statuses
            $allowedStatuses = ['pending', 'confirmed', 'checked_in'];
            if (!in_array($appointment->status, $allowedStatuses)) {
                $validator->errors()->add('appointment', __('validation.cannot_cancel_appointment'));
                return;
            }

            // Prevent cancellation too close to appointment time (less than 2 hours)
            if (
                $appointment->appointment_date->isToday() &&
                $appointment->appointment_time &&
                now()->diffInHours($appointment->appointment_time) < 2
            ) {
                $validator->errors()->add('appointment', __('validation.cancel_too_late'));
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
            'reason.required' => __('validation.required', ['attribute' => __('validation.attributes.reason')]),
            'reason.string' => __('validation.string', ['attribute' => __('validation.attributes.reason')]),
            'reason.min' => __('validation.min.string', ['attribute' => __('validation.attributes.reason'), 'min' => 3]),
            'reason.max' => __('validation.max.string', ['attribute' => __('validation.attributes.reason'), 'max' => 500]),
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
            'reason' => __('validation.attributes.reason'),
        ];
    }
}
