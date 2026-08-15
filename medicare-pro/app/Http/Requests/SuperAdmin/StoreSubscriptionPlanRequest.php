<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPlanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'min:2', 'max:255'],
            'name_en' => ['required', 'string', 'min:2', 'max:255'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
            'price_monthly' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'price_yearly' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'max_doctors' => ['required', 'integer', 'min:1', 'max:9999'],
            'max_patients' => ['required', 'integer', 'min:1', 'max:999999'],
            'max_departments' => ['required', 'integer', 'min:1', 'max:999'],
            'features' => ['required', 'array', 'min:1'],
            'features.*' => ['string', 'max:255'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures yearly price is less than 12x monthly price (discount incentive)
     * and validates feature uniqueness.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $priceMonthly = (float) $this->input('price_monthly');
            $priceYearly = (float) $this->input('price_yearly');
            $features = $this->input('features', []);

            // Validate yearly price offers a discount compared to monthly
            if ($priceMonthly > 0 && $priceYearly >= ($priceMonthly * 12)) {
                $validator->errors()->add('price_yearly', __('validation.yearly_price_discount'));
            }

            // Validate features array has no duplicates
            if (!empty($features) && count($features) !== count(array_unique($features))) {
                $validator->errors()->add('features', __('validation.duplicate_features'));
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
            'price_monthly.required' => __('validation.required', ['attribute' => __('validation.attributes.price_monthly')]),
            'price_monthly.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.price_monthly')]),
            'price_monthly.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.price_monthly'), 'min' => 0]),
            'price_monthly.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.price_monthly'), 'max' => 999999.99]),
            'price_yearly.required' => __('validation.required', ['attribute' => __('validation.attributes.price_yearly')]),
            'price_yearly.numeric' => __('validation.numeric', ['attribute' => __('validation.attributes.price_yearly')]),
            'price_yearly.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.price_yearly'), 'min' => 0]),
            'price_yearly.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.price_yearly'), 'max' => 999999.99]),
            'max_doctors.required' => __('validation.required', ['attribute' => __('validation.attributes.max_doctors')]),
            'max_doctors.integer' => __('validation.integer', ['attribute' => __('validation.attributes.max_doctors')]),
            'max_doctors.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.max_doctors'), 'min' => 1]),
            'max_doctors.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.max_doctors'), 'max' => 9999]),
            'max_patients.required' => __('validation.required', ['attribute' => __('validation.attributes.max_patients')]),
            'max_patients.integer' => __('validation.integer', ['attribute' => __('validation.attributes.max_patients')]),
            'max_patients.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.max_patients'), 'min' => 1]),
            'max_patients.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.max_patients'), 'max' => 999999]),
            'max_departments.required' => __('validation.required', ['attribute' => __('validation.attributes.max_departments')]),
            'max_departments.integer' => __('validation.integer', ['attribute' => __('validation.attributes.max_departments')]),
            'max_departments.min' => __('validation.min.numeric', ['attribute' => __('validation.attributes.max_departments'), 'min' => 1]),
            'max_departments.max' => __('validation.max.numeric', ['attribute' => __('validation.attributes.max_departments'), 'max' => 999]),
            'features.required' => __('validation.required', ['attribute' => __('validation.attributes.features')]),
            'features.array' => __('validation.array', ['attribute' => __('validation.attributes.features')]),
            'features.min' => __('validation.min.array', ['attribute' => __('validation.attributes.features'), 'min' => 1]),
            'features.*.string' => __('validation.string', ['attribute' => __('validation.attributes.feature')]),
            'features.*.max' => __('validation.max.string', ['attribute' => __('validation.attributes.feature'), 'max' => 255]),
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
            'price_monthly' => __('validation.attributes.price_monthly'),
            'price_yearly' => __('validation.attributes.price_yearly'),
            'max_doctors' => __('validation.attributes.max_doctors'),
            'max_patients' => __('validation.attributes.max_patients'),
            'max_departments' => __('validation.attributes.max_departments'),
            'features' => __('validation.attributes.features'),
            'features.*' => __('validation.attributes.feature'),
        ];
    }
}
