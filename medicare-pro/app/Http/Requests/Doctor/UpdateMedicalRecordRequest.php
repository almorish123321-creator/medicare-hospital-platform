<?php

namespace App\Http\Requests\Doctor;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicalRecordRequest extends FormRequest
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
            'diagnosis' => ['sometimes', 'nullable', 'string', 'min:2', 'max:2000'],
            'symptoms' => ['sometimes', 'nullable', 'string', 'min:2', 'max:2000'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'vital_signs' => ['sometimes', 'nullable', 'array'],
            'vital_signs.temperature' => ['sometimes', 'nullable', 'numeric', 'min:30', 'max:45'],
            'vital_signs.blood_pressure_systolic' => ['sometimes', 'nullable', 'integer', 'min:60', 'max:250'],
            'vital_signs.blood_pressure_diastolic' => ['sometimes', 'nullable', 'integer', 'min:30', 'max:150'],
            'vital_signs.heart_rate' => ['sometimes', 'nullable', 'integer', 'min:30', 'max:220'],
            'vital_signs.weight' => ['sometimes', 'nullable', 'numeric', 'min:1', 'max:300'],
            'vital_signs.height' => ['sometimes', 'nullable', 'numeric', 'min:20', 'max:250'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Validates vital sign logical consistency and ensures
     * at least one field is provided for update.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            // Ensure at least one field is provided
            if (empty($this->only(['diagnosis', 'symptoms', 'notes', 'vital_signs']))) {
                $validator->errors()->add('fields', __('validation.at_least_one_field'));
            }

            // Validate blood pressure consistency (systolic should be > diastolic)
            $systolic = $this->input('vital_signs.blood_pressure_systolic');
            $diastolic = $this->input('vital_signs.blood_pressure_diastolic');

            if ($systolic !== null && $diastolic !== null && $systolic <= $diastolic) {
                $validator->errors()->add('vital_signs.blood_pressure_systolic', __('validation.blood_pressure_invalid'));
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
            'diagnosis.string' => __('validation.string', ['attribute' => __('validation.attributes.diagnosis')]),
            'diagnosis.min' => __('validation.min.string', ['attribute' => __('validation.attributes.diagnosis'), 'min' => 2]),
            'diagnosis.max' => __('validation.max.string', ['attribute' => __('validation.attributes.diagnosis'), 'max' => 2000]),
            'symptoms.string' => __('validation.string', ['attribute' => __('validation.attributes.symptoms')]),
            'symptoms.min' => __('validation.min.string', ['attribute' => __('validation.attributes.symptoms'), 'min' => 2]),
            'symptoms.max' => __('validation.max.string', ['attribute' => __('validation.attributes.symptoms'), 'max' => 2000]),
            'notes.max' => __('validation.max.string', ['attribute' => __('validation.attributes.notes'), 'max' => 5000]),
            'vital_signs.array' => __('validation.array', ['attribute' => __('validation.attributes.vital_signs')]),
            'vital_signs.temperature.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.temperature')]),
            'vital_signs.temperature.min' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.temperature')]),
            'vital_signs.temperature.max' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.temperature')]),
            'vital_signs.blood_pressure_systolic.integer' => __('validation.integer', ['attribute' => __('validation.attributes.blood_pressure_systolic')]),
            'vital_signs.blood_pressure_systolic.min' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.blood_pressure_systolic')]),
            'vital_signs.blood_pressure_systolic.max' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.blood_pressure_systolic')]),
            'vital_signs.blood_pressure_diastolic.integer' => __('validation.integer', ['attribute' => __('validation.attributes.blood_pressure_diastolic')]),
            'vital_signs.blood_pressure_diastolic.min' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.blood_pressure_diastolic')]),
            'vital_signs.blood_pressure_diastolic.max' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.blood_pressure_diastolic')]),
            'vital_signs.heart_rate.integer' => __('validation.integer', ['attribute' => __('validation.attributes.heart_rate')]),
            'vital_signs.heart_rate.min' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.heart_rate')]),
            'vital_signs.heart_rate.max' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.heart_rate')]),
            'vital_signs.weight.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.weight')]),
            'vital_signs.weight.min' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.weight')]),
            'vital_signs.weight.max' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.weight')]),
            'vital_signs.height.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.height')]),
            'vital_signs.height.min' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.height')]),
            'vital_signs.height.max' => __('validation.vital_sign_range', ['attribute' => __('validation.attributes.height')]),
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
            'diagnosis' => __('validation.attributes.diagnosis'),
            'symptoms' => __('validation.attributes.symptoms'),
            'notes' => __('validation.attributes.notes'),
            'vital_signs' => __('validation.attributes.vital_signs'),
            'vital_signs.temperature' => __('validation.attributes.temperature'),
            'vital_signs.blood_pressure_systolic' => __('validation.attributes.blood_pressure_systolic'),
            'vital_signs.blood_pressure_diastolic' => __('validation.attributes.blood_pressure_diastolic'),
            'vital_signs.heart_rate' => __('validation.attributes.heart_rate'),
            'vital_signs.weight' => __('validation.attributes.weight'),
            'vital_signs.height' => __('validation.attributes.height'),
        ];
    }
}
