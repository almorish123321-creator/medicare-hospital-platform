<?php

namespace App\Http\Requests\Doctor;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class CreatePrescriptionRequest extends FormRequest
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
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'diagnosis' => ['required', 'string', 'min:2', 'max:2000'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.medication_id' => ['required', 'integer', 'exists:medications,id'],
            'items.*.dosage' => ['required', 'string', 'min:1', 'max:100'],
            'items.*.frequency' => ['required', 'string', 'in:once_daily,twice_daily,three_times_daily,four_times_daily,every_4_hours,every_6_hours,every_8_hours,every_12_hours,weekly,as_needed,before_meals,after_meals,at_bedtime'],
            'items.*.duration' => ['required', 'string', 'min:1', 'max:50'],
            'items.*.instructions' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures patient belongs to the doctor's hospital and medications are available.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $patientId = $this->input('patient_id');
            $hospitalId = $this->getHospitalId();

            // Verify patient belongs to the same hospital
            if ($patientId && $hospitalId) {
                $patient = \App\Models\Patient::find($patientId);
                if ($patient && $patient->user->hospital_id !== $hospitalId) {
                    $validator->errors()->add('patient_id', __('validation.patient_not_in_hospital'));
                }
            }

            // Verify medications belong to the hospital and are not expired
            $items = $this->input('items', []);
            if ($items && $hospitalId) {
                $medicationIds = array_column($items, 'medication_id');
                $medications = \App\Models\Medication::whereIn('id', $medicationIds)
                    ->where('hospital_id', $hospitalId)
                    ->get()
                    ->keyBy('id');

                foreach ($items as $index => $item) {
                    $medId = $item['medication_id'] ?? null;
                    if ($medId && !isset($medications[$medId])) {
                        $validator->errors()->add("items.{$index}.medication_id", __('validation.medication_not_in_hospital'));
                    }

                    // Check medication availability
                    if ($medId && isset($medications[$medId]) && $medications[$medId]->status === 'expired') {
                        $validator->errors()->add("items.{$index}.medication_id", __('validation.medication_expired'));
                    }

                    // Check medication availability stock
                    if ($medId && isset($medications[$medId]) && $medications[$medId]->status === 'out_of_stock') {
                        $validator->errors()->add("items.{$index}.medication_id", __('validation.medication_out_of_stock'));
                    }
                }
            }

            // Check for duplicate medications in the same prescription
            if ($items) {
                $medicationIds = array_column($items, 'medication_id');
                $duplicates = array_diff_assoc($medicationIds, array_unique($medicationIds));
                if (!empty($duplicates)) {
                    $validator->errors()->add('items', __('validation.duplicate_medication'));
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
            'diagnosis.required' => __('validation.required', ['attribute' => __('validation.attributes.diagnosis')]),
            'diagnosis.string' => __('validation.string', ['attribute' => __('validation.attributes.diagnosis')]),
            'diagnosis.min' => __('validation.min.string', ['attribute' => __('validation.attributes.diagnosis'), 'min' => 2]),
            'diagnosis.max' => __('validation.max.string', ['attribute' => __('validation.attributes.diagnosis'), 'max' => 2000]),
            'items.required' => __('validation.required', ['attribute' => __('validation.attributes.prescription_items')]),
            'items.array' => __('validation.array', ['attribute' => __('validation.attributes.prescription_items')]),
            'items.min' => __('validation.min.array', ['attribute' => __('validation.attributes.prescription_items'), 'min' => 1]),
            'items.max' => __('validation.max.array', ['attribute' => __('validation.attributes.prescription_items'), 'max' => 20]),
            'items.*.medication_id.required' => __('validation.required', ['attribute' => __('validation.attributes.medication_id')]),
            'items.*.medication_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.medication_id')]),
            'items.*.dosage.required' => __('validation.required', ['attribute' => __('validation.attributes.dosage')]),
            'items.*.dosage.string' => __('validation.string', ['attribute' => __('validation.attributes.dosage')]),
            'items.*.frequency.required' => __('validation.required', ['attribute' => __('validation.attributes.frequency')]),
            'items.*.frequency.in' => __('validation.in', ['attribute' => __('validation.attributes.frequency')]),
            'items.*.duration.required' => __('validation.required', ['attribute' => __('validation.attributes.duration')]),
            'items.*.duration.string' => __('validation.string', ['attribute' => __('validation.attributes.duration')]),
            'items.*.instructions.string' => __('validation.string', ['attribute' => __('validation.attributes.instructions')]),
            'items.*.instructions.max' => __('validation.max.string', ['attribute' => __('validation.attributes.instructions'), 'max' => 500]),
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
            'diagnosis' => __('validation.attributes.diagnosis'),
            'items' => __('validation.attributes.prescription_items'),
            'items.*.medication_id' => __('validation.attributes.medication_id'),
            'items.*.dosage' => __('validation.attributes.dosage'),
            'items.*.frequency' => __('validation.attributes.frequency'),
            'items.*.duration' => __('validation.attributes.duration'),
            'items.*.instructions' => __('validation.attributes.instructions'),
        ];
    }
}
