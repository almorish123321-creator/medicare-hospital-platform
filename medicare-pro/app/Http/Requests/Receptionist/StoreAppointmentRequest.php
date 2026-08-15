<?php

namespace App\Http\Requests\Receptionist;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
                'before:' . now()->addMonths(3)->toDateString(),
            ],
            'appointment_time' => [
                'required',
                'date_format:H:i',
                'after:07:59',
                'before:21:00',
            ],
            'type' => ['required', 'string', 'in:booked,walk_in'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures doctor, department, and patient all belong to the same hospital,
     * and validates against scheduling conflicts.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $hospitalId = $this->getHospitalId();
            $doctorId = $this->input('doctor_id');
            $departmentId = $this->input('department_id');
            $patientId = $this->input('patient_id');
            $appointmentDate = $this->input('appointment_date');
            $appointmentTime = $this->input('appointment_time');

            // Verify doctor belongs to the hospital via department
            if ($doctorId && $departmentId) {
                $doctor = \App\Models\Doctor::where('id', $doctorId)
                    ->where('department_id', $departmentId)
                    ->whereHas('department', fn ($q) => $q->where('hospital_id', $hospitalId))
                    ->first();

                if (!$doctor) {
                    $validator->errors()->add('doctor_id', __('validation.doctor_not_in_hospital'));
                }
            }

            // Verify patient belongs to the hospital
            if ($patientId && $hospitalId) {
                $patient = \App\Models\Patient::find($patientId);
                if ($patient && $patient->user->hospital_id !== $hospitalId) {
                    $validator->errors()->add('patient_id', __('validation.patient_not_in_hospital'));
                }
            }

            // Check for scheduling conflicts (same doctor, same date/time, same department)
            if ($doctorId && $appointmentDate && $appointmentTime) {
                $conflictExists = \App\Models\Appointment::where('doctor_id', $doctorId)
                    ->where('appointment_date', $appointmentDate)
                    ->where('appointment_time', $appointmentTime)
                    ->whereNotIn('status', ['cancelled', 'no_show'])
                    ->exists();

                if ($conflictExists) {
                    $validator->errors()->add('appointment_time', __('validation.appointment_time_conflict'));
                }
            }

            // Check if the patient already has an appointment at the same time
            if ($patientId && $appointmentDate && $appointmentTime) {
                $patientConflict = \App\Models\Appointment::where('patient_id', $patientId)
                    ->where('appointment_date', $appointmentDate)
                    ->where('appointment_time', $appointmentTime)
                    ->whereNotIn('status', ['cancelled', 'no_show'])
                    ->exists();

                if ($patientConflict) {
                    $validator->errors()->add('appointment_time', __('validation.patient_appointment_conflict'));
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
            'patient_id.required' => __('validation.required', ['attribute' => __('validation.attributes.patient_id')]),
            'patient_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.patient_id')]),
            'doctor_id.required' => __('validation.required', ['attribute' => __('validation.attributes.doctor_id')]),
            'doctor_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.doctor_id')]),
            'department_id.required' => __('validation.required', ['attribute' => __('validation.attributes.department_id')]),
            'department_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.department_id')]),
            'appointment_date.required' => __('validation.required', ['attribute' => __('validation.attributes.appointment_date')]),
            'appointment_date.date' => __('validation.date', ['attribute' => __('validation.attributes.appointment_date')]),
            'appointment_date.after_or_equal' => __('validation.after_or_equal', ['attribute' => __('validation.attributes.appointment_date'), 'date' => __('common.today')]),
            'appointment_date.before' => __('validation.appointment_max_days', ['days' => 90]),
            'appointment_time.required' => __('validation.required', ['attribute' => __('validation.attributes.appointment_time')]),
            'appointment_time.date_format' => __('validation.date_format', ['attribute' => __('validation.attributes.appointment_time'), 'format' => 'HH:mm']),
            'appointment_time.after' => __('validation.appointment_time_range'),
            'appointment_time.before' => __('validation.appointment_time_range'),
            'type.required' => __('validation.required', ['attribute' => __('validation.attributes.type')]),
            'type.in' => __('validation.in', ['attribute' => __('validation.attributes.type')]),
            'notes.max' => __('validation.max.string', ['attribute' => __('validation.attributes.notes'), 'max' => 1000]),
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
            'patient_id' => __('validation.attributes.patient_id'),
            'doctor_id' => __('validation.attributes.doctor_id'),
            'department_id' => __('validation.attributes.department_id'),
            'appointment_date' => __('validation.attributes.appointment_date'),
            'appointment_time' => __('validation.attributes.appointment_time'),
            'type' => __('validation.attributes.type'),
            'notes' => __('validation.attributes.notes'),
        ];
    }
}
