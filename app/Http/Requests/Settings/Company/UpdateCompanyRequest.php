<?php

namespace App\Http\Requests\Settings\Company;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'company_name'      => ['required', 'string', 'max:255'],
            'legal_name'        => ['nullable', 'string', 'max:255'],
            'registration_no'   => ['nullable', 'string', 'max:100'],
            'tax_id'            => ['nullable', 'string', 'max:100'],
            'industry'          => ['nullable', 'string', 'max:100'],
            'currency'          => ['required', 'string', 'max:10'],
            'fiscal_year_start' => ['required', 'string'],
            'email'             => ['nullable', 'email', 'max:255'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'website'           => ['nullable', 'url', 'max:255'],
            'address'           => ['nullable', 'string', 'max:500'],
            'city'              => ['nullable', 'string', 'max:100'],
            'state'             => ['nullable', 'string', 'max:100'],
            'country'           => ['nullable', 'string', 'max:100'],
            'postal_code'       => ['nullable', 'string', 'max:20'],
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
            'company_name'      => 'company name',
            'legal_name'        => 'legal name',
            'registration_no'   => 'registration number',
            'tax_id'            => 'tax ID',
            'fiscal_year_start' => 'fiscal year start',
            'postal_code'       => 'postal code',
        ];
    }
}
