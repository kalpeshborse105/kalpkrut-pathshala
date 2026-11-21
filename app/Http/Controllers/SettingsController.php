<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Branch, User, Role, SystemSetting, Company, Institute};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $company = Company::with('institutes.branches')->first();
        $institute = Institute::first();
        $branches = Branch::all();
        $users = User::with(['role', 'branch'])->paginate(15);
        $roles = Role::all();
        $settings = SystemSetting::all()->pluck('value', 'key');

        return view('settings.index', compact('company', 'institute', 'branches', 'users', 'roles', 'settings'));
    }

    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'institute_id' => 'required|exists:institutes,id',
            'name' => 'required|string|max:191',
            'code' => 'required|string|max:50|unique:branches,code',
            'email' => 'nullable|email|max:191',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
        ]);

        $validated['status'] = 'active';
        Branch::create($validated);

        return back()->with('success', 'New campus branch added successfully.');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
            'designation' => 'nullable|string|max:100',
            'password' => 'required|min:6',
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $branch = Branch::findOrFail($validated['branch_id']);

        User::create([
            'company_id' => $branch->institute->company_id ?? 1,
            'branch_id' => $branch->id,
            'role_id' => $role->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'user_type' => $role->slug,
            'designation' => $validated['designation'],
            'status' => 'active',
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'User account created successfully.');
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Update Key-Value Settings in SystemSetting table
        foreach ($data as $key => $val) {
            if ($val !== null) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => (string)$val, 'group' => 'general']
                );
            }
        }

        // Update Institute record
        $institute = Institute::first();
        if ($institute) {
            $instituteUpdates = [];
            if ($request->filled('institute_name')) {
                $instituteUpdates['name'] = $request->input('institute_name');
            }
            if ($request->filled('institute_tagline')) {
                $instituteUpdates['tagline'] = $request->input('institute_tagline');
            }
            if ($request->filled('institute_code')) {
                $instituteUpdates['code'] = $request->input('institute_code');
            }
            if ($request->filled('institute_email')) {
                $instituteUpdates['email'] = $request->input('institute_email');
            }
            if ($request->filled('institute_phone')) {
                $instituteUpdates['phone'] = $request->input('institute_phone');
            }

            if (!empty($instituteUpdates)) {
                $institute->update($instituteUpdates);
            }
        }

        // Update Company record
        $company = Company::first();
        if ($company) {
            $companyUpdates = [];
            if ($request->filled('institute_name')) {
                $companyUpdates['name'] = $request->input('institute_name');
            }
            if ($request->filled('institute_email')) {
                $companyUpdates['email'] = $request->input('institute_email');
            }
            if ($request->filled('institute_phone')) {
                $companyUpdates['phone'] = $request->input('institute_phone');
            }
            if ($request->filled('institute_address')) {
                $companyUpdates['address'] = $request->input('institute_address');
            }
            if ($request->filled('institute_city')) {
                $companyUpdates['city'] = $request->input('institute_city');
            }
            if ($request->filled('institute_state')) {
                $companyUpdates['state'] = $request->input('institute_state');
            }
            if ($request->filled('institute_pincode')) {
                $companyUpdates['pincode'] = $request->input('institute_pincode');
            }
            if ($request->filled('registration_no')) {
                $companyUpdates['registration_no'] = $request->input('registration_no');
            }
            if ($request->filled('gstin')) {
                $companyUpdates['gstin'] = $request->input('gstin');
            }

            if (!empty($companyUpdates)) {
                $company->update($companyUpdates);
            }
        }

        // Invalidate view caches so updates reflect instantly across the entire application
        Cache::forget('system_settings_dict');
        Cache::forget('default_institute');
        Cache::forget('default_company');

        return back()->with('success', 'Institute profile and branding settings updated successfully!');
    }
}
