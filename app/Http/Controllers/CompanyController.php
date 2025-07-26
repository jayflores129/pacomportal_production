<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use Redirect;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{


    public function index(Request $request)
    {
        $sortby = (request()->has('sortby')) ? strip_tags(request('sortby')) : '';
        $sort   = (request()->has('sort')) ? strip_tags(request('sort')) : '';

        $_company = Company::query();

        if ($sortby && $sort) {
            $_company->orderBy($sortby, $sort);
        } else {
            $_company->orderBy('created_at', 'desc');
        }

        if ($search = $request->search) {
            $_company->where(function ($query) use ($search) {
                $query->where('email', "like", "%$search%")
                    ->orWhere('name', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('country', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('fax', 'like', "%$search%")
                    ->orWhere('telephone_no', 'like', "%$search%")
                    ->orWhere('currency', 'like', "%$search%")
                    ->orWhere('contact_person', 'like', "%$search%");

                if ($date_create = date_create($search)) {
                    $date_formatted = date_format($date_create, 'Y-m-d');
                    $query->orWhereDate(DB::raw("STR_TO_DATE(created_at, '%Y-%m-%d')"), "$date_formatted");
                }
            });
        }

        $companies = $_company->paginate(10);

        $pagination = json_decode(
            json_encode($companies)
        )->links;

        if ($sortby && $sort) $companies->withPath('companies?sortby=' . $sortby . '&sort=' . $sort . '');

        return view('companies/index')->with(['companies' => $companies, 'pagination' => $pagination]);
    }

    public function create()
    {
        return view('companies/create');
    }

    public function edit($id)
    {
        $company = Company::findorfail($id);

        return view('companies/edit')->with(['company' => $company]);
    }

    public function show($id)
    {

        $company = Company::findorfail($id);
        $users   = User::where('company_id', $id)->get();

        return view('companies/show')->with(['company' => $company, 'users' => $users]);
    }


    public function searchUserCompany($id, Request $request)
    {

        if ($request->ajax()) {

            $company = Company::where('id', $id)->get();

            $response = $company;

            return Response($response);
        }
    }

    /**
     * Search for companies using ajax
     * @param  Request $request 
     * @return json           
     */
    public function search(Request $request)
    {

        if ($request->ajax() && $request->input('name')) {


            // $companies = Company::where( 'name', 'LIKE', '%' . $request->input('name') . '%' )->get();

            //$table = "companies";  

            //if($companies->count() < 1) {
            $users = User::where('company', 'LIKE', '%' . $request->input('name') . '%')
                ->orWhere('firstname', 'LIKE', '%' . $request->input('name') . '%')
                ->orWhere('lastname', 'LIKE', '%' . $request->input('name') . '%')
                ->orWhere('email', 'LIKE', '%' . $request->input('name') . '%')
                ->get();

            $table = "users";
            // }
            $response = ['companies' => $users, 'table' => $table];

            return Response($response);
        }
    }


    /**
     * Search for companies
     * @param  Request $request 
     * @return [type]          
     */
    public function searchCompanies(Request $request)
    {

        if ($request->ajax()) {

            $search    = strip_tags($request->input('search'));

            if ($request->input('search')) {

                // Search companies            
                $companies = DB::table('companies')
                    ->where('name', $search)
                    ->orWhere(function ($query) use ($search) {
                        $query->orWhere('description', $search)
                            ->orWhere('country', $search);
                    })
                    ->get();


                foreach ($companies as $item) {

                    $users  =  DB::table('users')
                        ->where('company_id', '=', $item->id)
                        ->count();

                    $item->users =  $users;
                }
            } else {


                // Search companies        
                $companies = Company::orderBy('created_at', 'desc')->paginate(10);


                foreach ($companies as $item) {

                    $users  =  DB::table('users')
                        ->where('company_id', '=', $item->id)
                        ->count();

                    $item->users =  $users;
                }
            }

            $response = [
                'contacts' => $companies,
                'search_for' => $search
            ];

            return $response;
        }
    }



    /**
     * Store new company
     * @param array $request
     */
    public function store(Request $request)
    {

        $v =  $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            'currency' => 'required'
        ]);


        if ($request->ajax()) {

            $company = new Company();
            $company->name = strip_tags($request->input('company_name'));
            $company->email = strip_tags($request->input('email'));
            $company->description = strip_tags($request->input('description'));
            $company->country = strip_tags($request->input('country'));
            $company->address = strip_tags($request->input('address'));
            $company->telephone_no = strip_tags($request->input('tel_no'));
            $company->fax = strip_tags($request->input('fax'));
            $company->currency = strip_tags($request->input('currency'));
            $company->contact_person = strip_tags($request->input('contact_person'));
            $company->save();

            $response = array(
                'status' => 'success',
                'msg' => $request->message,
                'id' => $company->id
            );
            $request->session()->flash('alert-success', 'New company has been added -' . $request->input('company_name') . '');

            return response()->json($response);
        } else {

            $this->validate($request, [
                'name' => 'required|unique:companies',
                'email' => 'required',
                'currency' => 'required'
            ]);


            $company = new Company();
            $company->name = strip_tags($request->input('name'));
            $company->email = strip_tags($request->input('email'));
            $company->description = strip_tags($request->input('description'));
            $company->country = strip_tags($request->input('country'));
            $company->address = strip_tags($request->input('address'));
            $company->telephone_no = strip_tags($request->input('tel_no'));
            $company->currency = strip_tags($request->input('currency'));
            $company->fax = strip_tags($request->input('fax'));
            $company->contact_person = strip_tags($request->input('contact_person'));
            $company->save();

            $request->session()->flash('alert-success', 'New company has been successfully added!');

            return redirect('admin/companies');
        }
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required',
            'country' => 'required',
            'currency' => 'required',
        ]);



        $company = Company::findorfail($id);

        $company->name = strip_tags($request->input('name'));
        $company->description = strip_tags($request->input('description'));
        $company->address = strip_tags($request->input('address'));
        $company->email = strip_tags($request->input('email'));
        $company->country = strip_tags($request->input('country'));
        $company->telephone_no = strip_tags($request->input('telephone_no'));
        $company->currency = strip_tags($request->input('currency'));
        $company->fax = strip_tags($request->input('fax'));
        $company->contact_person = strip_tags($request->input('contact_person'));

        $company->save();

        $request->session()->flash('alert-success', 'Company info has been successfully updated');

        return redirect()->route('companies.show', ['company' => $id]);
    }
}
