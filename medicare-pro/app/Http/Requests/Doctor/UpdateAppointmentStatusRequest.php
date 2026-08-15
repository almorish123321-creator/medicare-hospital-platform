<?php

namespace App\Http\Requests\Doctor;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentStatusRequest extends FormRequest
{
    use HasHospitalAccess;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isDoctor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:completed,no_show,cancelled'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures the appointment belongs to the doctor and validates
     * status transition rules.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $appointment = $this->route('appointment');
            $newStatus = $this->input('status');

            if (!$appointment) {
                $validator->errors()->add('appointment', __('validation.exists', ['attribute' => __('validation.attributes.appointment')]));
                return;
            }

            // Ensure the appointment belongs to the authenticated doctor
            $doctor = $this->user()->doctor;
            if ($appointment->doctor_id !== $doctor->id) {
                $validator->errors()->add('appointment', __('validation.not_owned_appointment'));
                return;
            }

            // Validate allowed status transitions
            $currentStatus = $appointment->status;
            $allowedTransitions = [
                'in_progress' => ['completed', 'no_show'],
                'confirmed' => ['no_show'],
                'checked_in' => ['no_show'],
                'pending' => ['no_show'],
            ];

            $allowedForCurrent = $allowedTransitions[$currentStatus] ?? [];

            // Cancelled appointments can't be re-opened
            if ($currentStatus === 'completed') {
                $validator->errors()->add('status', __('validation.appointment_already_completed'));
                return;
            }

            if ($currentStatus === 'cancelled') {
                $validator->errors()->add('status', __('validation.appointment_already_cancelled'));
                return;
            }

            if (!empty($allowedForCurrent) && !in_array($newStatus, $allowedForCurrent)) {
                $validator->errors()->add('status', __('validation.invalid_status_transition', [
                    'from' => $currentStatus,
                    'to' => $newStatus,
                ]));
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
            'status.required' => __('validation.required', ['attribute' => __('validation.attributes.status')]),
            'status.string' => __('validation.string', ['attribute' => __('validation.attributes.status')]),
            'status.in' => __('validation.in', ['attribute' => __('validation.attributes.status')]),
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
            'status' => __('validation.attributes.status'),
        ];
    }
}
