<?php

namespace App\Http\Requests\Pharmacist;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicationRequest extends FormRequest
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
        $medication = $this->route('medication');
        $hospitalId = $this->getHospitalId();

        return [
            'name_ar' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'name_en' => ['sometimes', 'required', 'string', 'min:2', 'max:255'],
            'description_ar' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'description_en' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'category' => ['sometimes', 'required', 'string', 'max:100'],
            'dosage_form' => ['sometimes', 'required', 'string', 'in:tablet,capsule,syrup,injection,cream,drop,spray,inhaler,suppository,powder,ointment,patch,lozenge,gel,solution,suspension'],
            'strength' => ['sometimes', 'required', 'string', 'max:50'],
            'stock_quantity' => ['sometimes', 'required', 'integer', 'min:0', 'max:999999'],
            'min_stock' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:999999'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999.99'],
            'manufacturer' => ['sometimes', 'nullable', 'string', 'max:255'],
            'expiry_date' => ['sometimes', 'nullable', 'date', 'after:today'],
            'is_active' => ['sometimes', 'required', 'boolean'],
            'side_effects' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'contraindications' => ['sometimes', 'nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures medication belongs to the pharmacist's hospital.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $medication = $this->route('medication');
            $hospitalId = $this->getHospitalId();

            if ($medication && $medication->hospital_id !== $hospitalId) {
                $validator->errors()->add('medication', __('validation.medication_not_in_hospital'));
            }

            // Validate min_stock vs stock_quantity logical consistency
            $stockQuantity = $this->input('stock_quantity');
            $minStock = $this->input('min_stock');

            if ($stockQuantity !== null && $minStock !== null && $stockQuantity < $minStock) {
                $validator->errors()->add('stock_quantity', __('validation.stock_below_minimum'));
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
            'name_ar.required' => __('validation.required', ['attribute' => __('validation.attributes.name_ar')]),
            'name_ar.string' => __('validation.string', ['attribute' => __('validation.attributes.name_ar')]),
            'name_ar.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name_ar'), 'min' => 2]),
            'name_ar.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name_ar'), 'max' => 255]),
            'name_en.required' => __('validation.required', ['attribute' => __('validation.attributes.name_en')]),
            'name_en.string' => __('validation.string', ['attribute' => __('validation.attributes.name_en')]),
            'name_en.min' => __('validation.min.string', ['attribute' => __('validation.attributes.name_en'), 'min' => 2]),
            'name_en.max' => __('validation.max.string', ['attribute' => __('validation.attributes.name_en'), 'max' => 255]),
            'description_ar.max' => __('validation.max.string', ['attribute' => __('validation.attributes.description_ar'), 'max' => 2000]),
            'description_en.max' => __('validation.max.string', ['attribute' => __('validation.attributes.description_en'), 'max' => 2000]),
            'category.required' => __('validation.required', ['attribute' => __('validation.attributes.category')]),
            'category.max' => __('validation.max.string', ['attribute' => __('validation.attributes.category'), 'max' => 100]),
            'dosage_form.required' => __('validation.required', ['attribute' => __('validation.attributes.dosage_form')]),
            'dosage_form.in' => __('validation.in', ['attribute' => __('validation.attributes.dosage_form')]),
            'strength.required' => __('validation.required', ['attribute' => __('validation.attributes.strength')]),
            'strength.max' => __('validation.max.string', ['attribute' => __('validation.attributes.strength'), 'max' => 50]),
            'stock_quantity.required' => __('validation.required', ['attribute' => __('validation.attributes.stock_quantity')]),
            'stock_quantity.integer' => __('validation.integer', ['attribute' => __('validation.attributes.stock_quantity')]),
            'stock_quantity.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.stock_quantity'), 'min' => 0]),
            'stock_quantity.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.stock_quantity'), 'max' => 999999]),
            'min_stock.integer' => __('validation.integer', ['attribute' => __('validation.attributes.min_stock')]),
            'min_stock.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.min_stock'), 'min' => 0]),
            'price.required' => __('validation.required', ['attribute' => __('validation.attributes.price')]),
            'price.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.price')]),
            'price.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.price'), 'min' => 0]),
            'price.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.price'), 'max' => 999999.99]),
            'manufacturer.max' => __('validation.max.string', ['attribute' => __('validation.attributes.manufacturer'), 'max' => 255]),
            'expiry_date.date' => __('validation.date', ['attribute' => __('validation.attributes.expiry_date')]),
            'expiry_date.after' => __('validation.after', ['attribute' => __('validation.attributes.expiry_date'), 'date' => __('common.today')]),
            'is_active.required' => __('validation.required', ['attribute' => __('validation.attributes.is_active')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('validation.attributes.is_active')]),
            'side_effects.max' => __('validation.max.string', ['attribute' => __('validation.attributes.side_effects'), 'max' => 2000]),
            'contraindications.max' => __('validation.max.string', ['attribute' => __('validation.attributes.contraindications'), 'max' => 2000]),
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
            'name_ar' => __('validation.attributes.name_ar'),
            'name_en' => __('validation.attributes.name_en'),
            'description_ar' => __('validation.attributes.description_ar'),
            'description_en' => __('validation.attributes.description_en'),
            'category' => __('validation.attributes.category'),
            'dosage_form' => __('validation.attributes.dosage_form'),
            'strength' => __('validation.attributes.strength'),
            'stock_quantity' => __('validation.attributes.stock_quantity'),
            'min_stock' => __('validation.attributes.min_stock'),
            'price' => __('validation.attributes.price'),
            'manufacturer' => __('validation.attributes.manufacturer'),
            'expiry_date' => __('validation.attributes.expiry_date'),
            'is_active' => __('validation.attributes.is_active'),
            'side_effects' => __('validation.attributes.side_effects'),
            'contraindications' => __('validation.attributes.contraindications'),
        ];
    }
}
