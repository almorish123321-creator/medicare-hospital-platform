<?php

namespace App\Http\Requests\Pharmacist;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class DispensePrescriptionRequest extends FormRequest
{
    use HasHospitalAccess;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isPharmacist();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'prescription_id' => ['required', 'integer', 'exists:prescriptions,id'],
            'dispensed_items' => ['required', 'array', 'min:1'],
            'dispensed_items.*.prescription_item_id' => ['required', 'integer', 'exists:prescription_items,id'],
            'dispensed_items.*.quantity_dispensed' => ['required', 'integer', 'min:1', 'max:9999'],
            'dispensed_items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures prescription belongs to the pharmacist's hospital,
     * is in 'pending' status, and validates stock availability.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $prescriptionId = $this->input('prescription_id');
            $hospitalId = $this->getHospitalId();

            $prescription = \App\Models\Prescription::find($prescriptionId);

            if (!$prescription) {
                $validator->errors()->add('prescription_id', __('validation.exists', ['attribute' => __('validation.attributes.prescription')]));
                return;
            }

            // Verify prescription belongs to the same hospital
            if ($prescription->patient->user->hospital_id !== $hospitalId) {
                $validator->errors()->add('prescription_id', __('validation.prescription_not_in_hospital'));
                return;
            }

            // Only pending prescriptions can be dispensed
            if ($prescription->status !== 'pending') {
                $validator->errors()->add('prescription_id', __('validation.prescription_not_pending'));
                return;
            }

            // Validate dispensed items match prescription items
            $dispensedItems = $this->input('dispensed_items', []);
            $prescriptionItemIds = $prescription->items->pluck('id')->toArray();

            $dispensedItemIds = array_column($dispensedItems, 'prescription_item_id');

            // Check all dispensed item IDs belong to this prescription
            foreach ($dispensedItemIds as $itemId) {
                if (!in_array($itemId, $prescriptionItemIds)) {
                    $validator->errors()->add('dispensed_items', __('validation.item_not_in_prescription'));
                    break;
                }
            }

            // Check that all prescription items are being dispensed
            $missingItems = array_diff($prescriptionItemIds, $dispensedItemIds);
            if (!empty($missingItems)) {
                $validator->errors()->add('dispensed_items', __('validation.all_items_must_be_dispensed'));
            }

            // Check for duplicate prescription items
            if (count($dispensedItemIds) !== count(array_unique($dispensedItemIds))) {
                $validator->errors()->add('dispensed_items', __('validation.duplicate_dispensed_item'));
            }

            // Validate medication stock availability for each dispensed item
            foreach ($dispensedItems as $index => $item) {
                $prescriptionItem = $prescription->items->find($item['prescription_item_id']);
                if ($prescriptionItem) {
                    // Find the medication by name from the prescription item
                    $medication = \App\Models\Medication::where('hospital_id', $hospitalId)
                        ->where('name', $prescriptionItem->medication_name)
                        ->first();

                    if ($medication) {
                        if ($medication->stock_quantity < $item['quantity_dispensed']) {
                            $validator->errors()->add(
                                "dispensed_items.{$index}.quantity_dispensed",
                                __('validation.insufficient_stock', [
                                    'medication' => $medication->name,
                                    'available' => $medication->stock_quantity,
                                ])
                            );
                        }

                        if ($medication->status === 'expired') {
                            $validator->errors()->add(
                                "dispensed_items.{$index}.prescription_item_id",
                                __('validation.medication_expired')
                            );
                        }
                    }
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
            'prescription_id.required' => __('validation.required', ['attribute' => __('validation.attributes.prescription_id')]),
            'prescription_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.prescription_id')]),
            'dispensed_items.required' => __('validation.required', ['attribute' => __('validation.attributes.dispensed_items')]),
            'dispensed_items.array' => __('validation.array', ['attribute' => __('validation.attributes.dispensed_items')]),
            'dispensed_items.min' => __('validation.min.array', ['attribute' => __('validation.attributes.dispensed_items'), 'min' => 1]),
            'dispensed_items.*.prescription_item_id.required' => __('validation.required', ['attribute' => __('validation.attributes.prescription_item_id')]),
            'dispensed_items.*.prescription_item_id.exists' => __('validation.exists', ['attribute' => __('validation.attributes.prescription_item_id')]),
            'dispensed_items.*.quantity_dispensed.required' => __('validation.required', ['attribute' => __('validation.attributes.quantity_dispensed')]),
            'dispensed_items.*.quantity_dispensed.integer' => __('validation.integer', ['attribute' => __('validation.attributes.quantity_dispensed')]),
            'dispensed_items.*.quantity_dispensed.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.quantity_dispensed'), 'min' => 1]),
            'dispensed_items.*.quantity_dispensed.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.quantity_dispensed'), 'max' => 9999]),
            'dispensed_items.*.notes.string' => __('validation.string', ['attribute' => __('validation.attributes.notes')]),
            'dispensed_items.*.notes.max' => __('validation.max.string', ['attribute' => __('validation.attributes.notes'), 'max' => 500]),
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
            'prescription_id' => __('validation.attributes.prescription_id'),
            'dispensed_items' => __('validation.attributes.dispensed_items'),
            'dispensed_items.*.prescription_item_id' => __('validation.attributes.prescription_item_id'),
            'dispensed_items.*.quantity_dispensed' => __('validation.attributes.quantity_dispensed'),
            'dispensed_items.*.notes' => __('validation.attributes.notes'),
        ];
    }
}
