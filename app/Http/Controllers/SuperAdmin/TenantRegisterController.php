<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Stancl\Tenancy\Tenant;
use Stancl\Tenancy\Database\TenantDatabaseManager;

class TenantRegisterController extends Controller
{
    public function register(Request $request)
    {
        // Step 1: Validate input
        $validator = Validator::make($request->all(), [
            'tenant_id' => 'required|unique:tenants,id',
            'domain' => 'required|unique:domains,domain',
            'database' => 'required|string',
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Step 2: Create tenant
        $tenant = Tenant::create([
            'id' => $request->tenant_id,
            'tenancy_db_name' => $request->database,
            'data' => [
                'domain' => $request->domain,
            ],
        ]);

        $tenant->domains()->create([
            'domain' => $request->domain,
        ]);

        // Step 3: Run tenant migrations
        $tenant->run(function () use ($request) {
            \Artisan::call('tenants:migrate', ['--tenants' => [$request->tenant_id]]);

            // Step 4: Create default user inside tenant
            \App\Models\User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        });

        return response()->json([
            'message' => 'Tenant and user created successfully',
            'tenant_id' => $tenant->id,
            'domain' => $tenant->getAttribute('domain'),
        ], 201);
    }
}
