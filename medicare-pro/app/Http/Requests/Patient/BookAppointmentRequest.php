<?php

namespace App\Http\Requests\Patient;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class BookAppointmentRequest extends FormRequest
{
    use HasHospitalAccess;

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
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'hospital_id' => ['required', 'integer', 'exists:hospitals,id'],
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
            'notes' => ['nullable', 'string', 'max:1000'],
            'type' => ['required', 'string', 'in:booked,walk_in'],
            'payment_method' => ['required', 'string', 'in:cash,credit_card,insurance,online'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            // Ensure doctor belongs to the specified hospital and department
            $doctorId = $this->input('doctor_id');
            $hospitalId = $this->input('hospital_id');
            $departmentId = $this->input('department_id');

            if ($doctorId && $hospitalId && $departmentId) {
                $doctor = \App\Models\Doctor::where('id', $doctorId)
                    ->whereHas('department', function ($q) use ($hospitalId, $departmentId) {
                        $q->where('id', $departmentId)->where('hospital_id', $hospitalId);
                    })
                    ->first();

                if (!$doctor) {
                    $validator->errors()->add('doctor_id', __('validation.doctor_not_in_department'));
                }
            }

            // Ensure appointment date is not on a past date in hospital's timezone
            $appointmentDate = $this->input('appointment_date');
            if ($appointmentDate && \Carbon\Carbon::parse($appointmentDate)->isWeekend()) {
                $validator->errors()->add('appointment_date', __('validation.weekend_not_allowed'));
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
            'doctor_id.required' => __('validation.required', ['attribute' => __('validation.attributes.doctor_id')]),
            'doctor_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.doctor_id')]),
            'hospital_id.required' => __('validation.required', ['attribute' => __('validation.attributes.hospital_id')]),
            'hospital_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.hospital_id')]),
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
            'notes.max' => __('validation.max.string', ['attribute' => __('validation.attributes.notes'), 'max' => 1000]),
            'type.required' => __('validation.required', ['attribute' => __('validation.attributes.type')]),
            'type.in' => __('validation.in', ['attribute' => __('validation.attributes.type')]),
            'payment_method.required' => __('validation.required', ['attribute' => __('validation.attributes.payment_method')]),
            'payment_method.in' => __('validation.in', ['attribute' => __('validation.attributes.payment_method')]),
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
            'doctor_id' => __('validation.attributes.doctor_id'),
            'hospital_id' => __('validation.attributes.hospital_id'),
            'department_id' => __('validation.attributes.department_id'),
            'appointment_date' => __('validation.attributes.appointment_date'),
            'appointment_time' => __('validation.attributes.appointment_time'),
            'notes' => __('validation.attributes.notes'),
            'type' => __('validation.attributes.type'),
            'payment_method' => __('validation.attributes.payment_method'),
        ];
    }
}
