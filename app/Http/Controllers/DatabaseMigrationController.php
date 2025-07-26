<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DatabaseMigrationController extends Controller
{
    // TODO: begin migration tomorrow
    public function company(Request $request)
    {
        $pacom_companies = DB::table('pacom_company')->get();
        $company_table = DB::table('companies');

        $last = $company_table->orderBy('id', 'desc')->first();

        foreach ($pacom_companies as $key => $pacom_company) {
            $company_table->insertGetId([
                'id' => $last->id + ($key + 1),
                'email' => $pacom_company->companyEmail,
                'name' => $pacom_company->companyName,
                'country' => $pacom_company->companyCountry,
                'fax' => $pacom_company->companyFax,
                'telephone_no' => $pacom_company->companyPhone,
                'address' => $company->companyAddress1 ?? $company->companyAddress2 ?? '-',
                'created_at' => date('Y-m-d h:m:s'),
                'updated_at' => date('Y-m-d h:m:s'),
                'ref_id' => $pacom_company->companyID
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function user(Request $request)
    {
        $user_table = DB::table('users');

        $pacom_users = DB::table('pacom_user')
            ->leftJoin('companies', 'companies.ref_id', '=', 'pacom_user.companyID')
            ->select('pacom_user.*', 'companies.ref_id', 'companies.name as company_name')
            ->groupBy('pacom_user.userID')
            ->get();

        $user_last = $user_table->orderBy('id', 'desc')->first();

        foreach ($pacom_users as $key => $user) {
            $insert_data = [
                'id' => $user_last->id + ($key + 1),
                'firstname' => $user->userFirstName ?? '-',
                'lastname' => $user->userLastName ?? '-',
                'email' => $user->userEmail,
                'password' => $user->userPassword,
                'company_id' => $user->ref_id,
                'company' =>  $user->company_name,
                'phone' => $user->userPhone ?? $user->userMobile,
                'fax' => $user->userFax,
                'country' => '-',
            ];

            $user_table->insert($insert_data);
        }

        return response()->json(['success' => true]);
    }

    public function rma_ticket()
    {
        $pacom_rma_record = DB::table('pacom_rma_record');

        $pacom_rma_records = $pacom_rma_record
            ->leftJoin('pacom_user', "pacom_user.userUsername", "=", "pacom_rma_record.Req_Acc_Id")
            ->leftJoin('users', function ($join) {
                $join->on('users.firstname', '=', 'pacom_user.userFirstName')
                    ->on('users.lastname', '=', 'pacom_user.userLastName');
            })
            ->leftJoin("companies", "companies.id", '=', 'users.company_id')
            ->select(
                'pacom_rma_record.*', 
                'pacom_user.userFirstName', 
                'pacom_user.userLastName', 
                'users.id as user_id',
                'users.company as user_company',
                'users.country as user_country',
                'companies.id as company_id',
                'companies.name as company_name',
                'companies.telephone_no as company_phone',
                'companies.fax as company_fax',
                'companies.country as company_country',
                'companies.address as company_address',
            )
            ->groupBy('pacom_rma_record.RMA_No')
            ->get();

        $status = [
            'Completely Shipped' => 'Shipped',
            'Submitted' => 'Open',
            'Completed' => 'Confirmed',
            'Partially Shipped' => 'Shipped',
            'Received' => 'Received'
        ];

        $last_rma = DB::table('rma_tickets')->orderBy('id','desc')->first();

        foreach ($pacom_rma_records as $key => $rma_record) {
            $rma_tickets_insert_data = [
                'requested_date' => $rma_record->Req_Date ? date('Y-m-d', strtotime($rma_record->Req_Date)) : null, 
                'requester_name' => $rma_record->Req_Name,
                'requester_phone' => $rma_record->Req_Tel,
                'requester_company' => $rma_record->Company_Name,
                'requester_email' => $rma_record->Req_Email,
                'po_number' => $rma_record->PO,
                'requester_fax' => $rma_record->Req_Fax,
                'company_name' => $rma_record->company_name ?? $rma_record->user_company ?? $rma_record->Company_Name,
                'company_phone' => $rma_record->company_phone,
                'company_fax' => $rma_record->company_fax,
                'company_address' => $rma_record->company_address,
                'company_country' => $rma_record->company_country,
                'company_isvar' => 1,
                'currency' => $rma_record->Currency,
                'status' => isset($status[$rma_record->RMA_Status]) ? $status[$rma_record->RMA_Status] : 'Open',
                'has_quotation' => 0,
                'has_confirmed' => 0,
                'user_id' => $rma_record->user_id,
                'company_id' => $rma_record->company_id,
                'cust_can_edit' => 1,
                'notify' => 0,
                'country' => $rma_record->company_country ?? $rma_record->user_country,
                'extra_rma_records_detail' => json_encode([
                    'RMA_No' => $rma_record->RMA_No,
                    'Req_Acc_Id' => $rma_record->Req_Acc_Id,
                ])
            ];

            if (isset($last_rma->id)) {
                $rma_tickets_insert_data['id'] = $last_rma->id + ($key + 1);
            }

            DB::table('rma_tickets')->insert($rma_tickets_insert_data);
        }
        return response()->json([
            'success' => true
        ]);
    }

    public function rma_ticket_faulties()
    {
        $faultyItems = DB::table('pacom_rma_faultyitems')->get();

        $last_rma_item = DB::table('rma_items')->orderBy('id', 'desc')->first();

        foreach ($faultyItems as $key => $faulty) {
            $rmaTickets = DB::table('rma_tickets')
                                ->where('extra_rma_records_detail->RMA_No', $faulty->RMA_No)
                                ->first();

            $faulty_insert_data = [
                'model' => $faulty->Model,
                'under_warranty' => $faulty->Under_Warranty == 'Y' ? 1 : 0,
                'original_order_date' => $faulty->OriginalOrderDate,
                'fault_described_by_customer' => null,
                'serial_number' => $faulty->Serial_No,
                'root_cause_analysis' => $faulty->Fault_Found,
                'pacom_fault_description' => $faulty->Fault_Description,
                'pacom_comment' => null,
                'company_fax' => optional($rmaTickets)->company_fax,
                'received_date' => null,
                'repaired_date' => $faulty->Repaired_Date,
                'status' => $faulty->Status,
                'invalid_serial_number' => null,
                'repair_cost' => $faulty->RepairCost,
                'rma_id' => optional($rmaTickets)->id,
                'extra_rma_pacom_faultyitems_details' => json_encode([
                    'Repaired_By' => $faulty->Repaired_by,
                    'Date_Purchased' => $faulty->Date_Purchased,
                ]),
            ];

            if (isset($last_rma_item->id)) {
                $faulty_insert_data['id'] = $last_rma_item->id + ($key + 1);
            }

            DB::table('rma_items')->insert($faulty_insert_data);
        }

        return response()->json(['success' => true]);
    }

    public function user_companies() 
    {
        $companies = DB::table('companies');
        $users = DB::table('users');
        $user_c = DB::table('user_companies');

        $company_users = $users->leftJoin('companies', "companies.id", "=", "users.company_id")
            ->select("users.id as user_id", "companies.id as company_id")
            ->get();

        foreach ($company_users as $company_user) {
            $exists = $user_c->where([
                'user_id' => $company_user->user_id,
                'company_id' => $company_user->company_id,
            ])->first();

            if (!$exists) {
                $user_c->insert([
                    'user_id' => $company_user->user_id,
                    'company_id' => $company_user->company_id,
                    'primary' => 1,
                    'created_at' => date('Y-m-d h:m:s'),
                    'updated_at' => date('Y-m-d h:m:s'),
                ]);
            }
        }

        return response()->json(['success' => true]);
    }

    public function update_user_status()
    {
        $users = DB::table('users');

        $users->where('status', '=', null)->update(['status' => 1]);

        return response()->json(['success' => true]);
    }

    public function update_rma_tickets_date()
    {
        $rma_tickets = DB::table('rma_tickets');
        
        $t = $rma_tickets->where(DB::raw('LEFT(requested_date, 2)'), '=', '00')
        ->update([
            'requested_date' =>DB::raw('CONCAT("20", SUBSTRING(requested_date, 3))')
        ]);
    
        return response()->json(['success' => true]);
    }

    public function product_migration() 
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('description')->change();
        
            if (!Schema::hasColumn('products', 'pacom_product_id')) {
                $table->integer('pacom_product_id', false, true)->nullable();
            }
        }); 

        if (!Schema::hasTable('pacom_products')) {
            Schema::create('pacom_products', function (Blueprint $table) {
                $table->integer('productID', true);
                $table->integer('productLevel2ID', false, true)->nullable();
                $table->integer('productLevel3ID', false, true)->nullable();
                $table->integer('productLevel4ID', false, true)->nullable();
                $table->integer('productOrder', false, true)->nullable();
                $table->string('productTitle', 255)->nullable();
                $table->string('productTitleL1', 255)->nullable();
                $table->string('productTitleL2', 255)->nullable();
                $table->string('productTitleL3', 255)->nullable();
                $table->string('productTitleL4', 255)->nullable();
                $table->string('productTitleL5', 255)->nullable();
                $table->string('productFileName', 255)->nullable();
                $table->string('productCode', 255)->nullable();
                $table->text('productSummary')->nullable();
                $table->text('productSummaryL1')->nullable();
                $table->text('productSummaryL2')->nullable();
                $table->text('productSummaryL3')->nullable();
                $table->text('productSummaryL4')->nullable();
                $table->text('productSummaryL5')->nullable();
                $table->string('productSummaryLinkText', 255)->nullable();
                $table->text('productDesc')->nullable();
                $table->text('productDescL1')->nullable();
                $table->text('productDescL2')->nullable();
                $table->text('productDescL3')->nullable();
                $table->text('productDescL4')->nullable();
                $table->text('productDescL5')->nullable();
                $table->text('productKeyFeatures')->nullable();
                $table->text('productKeyFeaturesL1')->nullable();
                $table->text('productKeyFeaturesL2')->nullable();
                $table->text('productKeyFeaturesL3')->nullable();
                $table->text('productKeyFeaturesL4')->nullable();
                $table->text('productKeyFeaturesL5')->nullable();
                $table->text('productNotes')->nullable();
                $table->string('productLanguages', 255)->nullable();
                $table->string('productImage', 255)->nullable();
                $table->string('productImageAltText', 255)->nullable();
                $table->string('productImageHiRes', 255)->nullable();
                $table->string('productImageHiResAltText', 255)->nullable();
                $table->enum('productAccess', ['p', 'r', 'm', 'a'])->nullable();
                $table->enum('productIndex', ['y', 'n'])->nullable();
                $table->enum('productFeatured', ['y', 'n'])->nullable();
                $table->enum('productActive', ['y', 'n'])->nullable();
                $table->integer('downloadID1', false, true)->nullable();
                $table->integer('downloadID2', false, true)->nullable();
                $table->integer('downloadID3', false, true)->nullable();
                $table->integer('downloadID4', false, true)->nullable();
                $table->integer('downloadID5', false, true)->nullable();
                $table->string('pdfDocumentID', 500)->nullable();
                $table->string('pdfDocumentIDfr', 255)->nullable();
                $table->string('pdfDocumentIDes', 255)->nullable();
                $table->string('pdfDocumentIDse', 255)->nullable();
                $table->string('productLogo', 255)->nullable();
                $table->string('productLogoAltText', 255)->nullable();
                $table->string('productLogoHiRes', 255)->nullable();
                $table->string('productLogoHiResAltText', 255)->nullable();
                $table->string('toprightLogo', 255)->nullable();
                $table->string('toprightLogoAltText', 255)->nullable();
                $table->string('toprightLogoHiRes', 255)->nullable();
                $table->string('toprightLogoHiResAltText', 255)->nullable();
                $table->enum('productPrintable', ['y', 'n'])->nullable();
            });
        } 
        
        $content = file_get_contents(public_path('db/products.json'));

        $products = json_decode($content, true);

        foreach ($products as $product) {
            $exists = DB::table('pacom_products')->where('productID', $product['productID'])->exists();
            if (!$exists) {
                DB::table('pacom_products')->insert($product);
            }
        }

        foreach (DB::table('pacom_products')->get() as $key => $pacom_product) {
            $exists = DB::table('products')->where('name', $pacom_product->productTitle)->exists();
            if (!$exists) {
                DB::table('products')->insert([
                    'name' => $pacom_product->productTitle,
                    'description' => $pacom_product->productDesc,
                    'user_id' => null,
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'pacom_product_id' => $pacom_product->productID
                ]);
            }
        }
    }

    public function sync_user_role() 
    {
        $role_users = DB::table('role_user')->get();
        
        foreach ($role_users as $role_user) {
            $role = DB::table('roles')->where('id', $role_user->role_id)->first();
            $user = User::where('id', $role_user->user_id)->first();

            if ($user && $role) {
                if ($role_user->role_id == 1) {
                    $user->assignRole('admin');
                } else {
                    $user->assignRole($role->name);
                }
            }
        }

        $pacom_users = DB::table('pacom_user')->get();

        foreach ($pacom_users as $pacom_user) {
            $user = User::where('email', $pacom_user->userEmail)->first();

            if ($pacom_user->userIsStaff == 'y' && $user) {
                $user->assignRole('staff');
            }

            if ($pacom_user->userIsAdmin == 'y' && $user) {
                $user->assignRole('admin');
            }

            if ($pacom_user->userIsCustomer == 'y' && $user) {
                $user->assignRole('customer');
            }

            if ($pacom_user->userIsTrainer == 'y' && $user) {
                $user->assignRole('trainer');
            }
        }
    }
}
