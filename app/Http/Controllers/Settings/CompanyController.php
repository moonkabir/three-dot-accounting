<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\Company\UpdateCompanyRequest;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    /**
     * Show the company setup form.
     */
    public function edit(Request $request)
    {
        $company = Company::first();
        return view('settings.company', compact('company'));
    }

    /**
     * Save company settings.
     */
    public function update(UpdateCompanyRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        $company = Company::first();

        if ($company) {
            $company->update($validated);
        } else {
            Company::create($validated);
        }

        return redirect()->route('settings.company')
            ->with('success', 'Company settings saved successfully.');
    }
}
