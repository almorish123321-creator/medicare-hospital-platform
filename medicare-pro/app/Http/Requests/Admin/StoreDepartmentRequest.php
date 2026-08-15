<?php

namespace App\Http\Requests\Admin;

use App\Traits\HasHospitalAccess;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
{
    use HasHospitalAccess;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isHospitalAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $hospitalId = $this->getHospitalId();

        return [
            'name_ar' => ['required', 'string', 'min:2', 'max:255'],
            'name_en' => ['required', 'string', 'min:2', 'max:255'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'description_en' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Ensures unique department names within the hospital
     * and checks subscription plan department limits.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $hospitalId = $this->getHospitalId();
            $nameAr = $this->input('name_ar');
            $nameEn = $this->input('name_en');

            // Check unique department name within hospital (using translation-friendly approach)
            if ($nameAr) {
                $nameExists = \App\Models\Department::where('hospital_id', $hospitalId)
                    ->where('name', $nameAr)
                    ->exists();

                if ($nameExists) {
                    $validator->errors()->add('name_ar', __('validation.department_name_exists'));
                }
            }

            // Check subscription plan department limit
            $hospital = \App\Models\Hospital::find($hospitalId);
            if ($hospital && $hospital->subscriptionPlan) {
                $currentDepartmentCount = \App\Models\Department::where('hospital_id', $hospitalId)->count();

                if ($hospital->subscriptionPlan->max_departments && $currentDepartmentCount >= $hospital->subscriptionPlan->max_departments) {
                    $validator->errors()->add('name_en', __('validation.department_limit_reached'));
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
        ];
    }
}
