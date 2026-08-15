<?php

namespace App\Http\Requests\Patient;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
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
            'doctor_id' => ['required', 'integer', 'exists:doctors,id'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'min:5', 'max:1000'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures the patient has a completed appointment with the doctor
     * and hasn't already reviewed this doctor.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $patient = $this->user()->patient;
            $doctorId = $this->input('doctor_id');

            if (!$patient) {
                $validator->errors()->add('patient', __('validation.patient_profile_required'));
                return;
            }

            // Check if patient has any completed appointment with this doctor
            $hasCompletedAppointment = \App\Models\Appointment::where('patient_id', $patient->id)
                ->where('doctor_id', $doctorId)
                ->where('status', 'completed')
                ->exists();

            if (!$hasCompletedAppointment) {
                $validator->errors()->add('doctor_id', __('validation.no_completed_appointment'));
            }

            // Check for duplicate review (one review per appointment)
            $existingReview = \App\Models\Review::where('patient_id', $patient->id)
                ->where('doctor_id', $doctorId)
                ->exists();

            if ($existingReview) {
                $validator->errors()->add('doctor_id', __('validation.review_already_exists'));
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
            'rating.required' => __('validation.required', ['attribute' => __('validation.attributes.rating')]),
            'rating.integer' => __('validation.integer', ['attribute' => __('validation.attributes.rating')]),
            'rating.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.rating'), 'min' => 1]),
            'rating.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.rating'), 'max' => 5]),
            'comment.string' => __('validation.string', ['attribute' => __('validation.attributes.comment')]),
            'comment.min' => __('validation.min.string', ['attribute' => __('validation.attributes.comment'), 'min' => 5]),
            'comment.max' => __('validation.max.string', ['attribute' => __('validation.attributes.comment'), 'max' => 1000]),
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
            'rating' => __('validation.attributes.rating'),
            'comment' => __('validation.attributes.comment'),
        ];
    }
}
