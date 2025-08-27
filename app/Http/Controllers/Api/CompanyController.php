<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            // 1. Create Company
            $company = Company::create([
                'legal_facility_name' => $request->data['legal_facility_name'],
                'phone' => $request->data['phone'],
                'email' => $request->data['email']
            ]);

            // 2. Create Company Details
            $company->details()->create([
                'bank_account_holder' => $request->data['bank_account_holder'],
                'bank_account_number' => $request->data['bank_account_number'],
                'bank_ifsc' => $request->data['bank_ifsc'],
                'company_pan' => $request->data['company_pan'],
                'cin' => $request->data['cin'],
                'cin_doc_id' => $request->data['cin_doc_id'],
                'company_pan_doc_id' => $request->data['company_pan_doc_id'],
                'tax_doc_id' => $request->data['tax_doc_id'],
                'tax_id' => $request->data['tax_id'],
                'license_number' => $request->data['license_number'],
                'license_number_doc_id' => $request->data['license_number_doc_id'],
                'license_issued_year' => $request->data['license_issued_year'],
            ]);

            // 3. Create Address
            $company->address()->create($request->address);

            // 4. Create KYC
            if ($request->verifications && count($request->verifications)) {
                $kyc = $company->kyc()->create([
                    'entity_type' => $request->entity_type,
                    'verification_status' => 'UNVERIFIED',
                    'comment' => ''
                ]);

                foreach ($request->verifications as $verification) {
                    $kyc->fieldVerifications()->create([
                        'field_data_id' => $verification['fieldDataId'],
                        'agent_id' => $verification['agentId'],
                        'status' => $verification['status'],
                        'comment' => $verification['comment'] ?? '',
                        'kyc_submit_version' => $verification['kycSubmitVersion']
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Company stored successfully', 'company' => $company->load('details', 'address', 'kyc.fieldVerifications')]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getInfo(Request $request)
    {

        if (isset($request['company_id'])) {
            $companyData = Company::with([
                'address',
                'details',
                'kyc.fieldVerifications'
            ])->find($request['company_id']);
        } else {
            $companyData = Company::with([
                'address',
                'details',
                'kyc.fieldVerifications'
            ])->first();
        }

        $companyData->setAttribute('beneficiary', $companyData->details);
        unset($companyData->details);

        return response()->json(
            $companyData
        );
    }

    public function getAll(Request $request)
    {

        $companyData = Company::with([
            'address',
            'details',
            'kyc.fieldVerifications'
        ])->get();

        $companyData->each(function ($company) {
            $company->setAttribute('beneficiary', $company->details);
            $company->setAttribute('name', $company->legal_facility_name);
            unset($company->details);
        });

        return response()->json([
            'total_count'     => $companyData->count(),
            'message' => 'Branch created successfully',
            'search_response' => $companyData,
        ]);
    }
}
