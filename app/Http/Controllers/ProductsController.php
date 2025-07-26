<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\UserLogs;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{
    //

     public function index()
    {

    	$products = Product::orderBy('created_at','DESC')->paginate(20);

        $pagination = json_decode(
            json_encode($products)
        )->links;

        return view('products/index')->with(['products' => $products, 'pagination' => $pagination]);
    }


    public function create()
    {
        return view('products/create');
    }


    public function show($id) 
    {
        $product = Product::findorfail($id);

        if( $product ) {
            return view('products/show')->with('product', $product );
        } else {
            return view('errors.404');
        }
        
    }

    public function edit($id) 
    {
        $product = Product::findorfail($id);

        if( $product ) {
            return view('products/edit')->with('product', $product );
        } else {
            return view('errors.404');
        }
        
    }

    public function store(Request $request) 
    {

        // Validating Input Fields
        $this->validate($request, [
            'name' => 'required|unique:products',

        ]);


            $created_by = Auth::user()->id;

            $product                    = new Product;
            $product->name              = strip_tags( $request->input('name') );
            $product->description       = strip_tags(  $request->input('description') );
            $product->user_id           = $created_by;
            $product->save();

            session()->flash('alert-success', 'Product has been successfully added!');

            return redirect('admin/products')->with('products', 'added');

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
                'name' => 'required'
        ]); 

        Product::where('id', $id)->update(['description' => strip_tags( $request->input('description') ) ]);


        session()->flash('alert-success', 'Product desription has been successfully updated!');

        return redirect('/admin/products');


    }

    public function destroy($id, Request $request) 
    {
        if(optional(Auth::user())->isAdmin()) {

             $product = Product::find($id)->value('name');
           
             Product::find($id)->delete();

             $logs = new UserLogs();   
             $logs->type = '<span class="bg-danger">Product Deleted</span>'; 
             $logs->description = 'Deleted <strong>' . $product . '</strong> from the products, ';
             $logs->old_value = $product;
             $logs->new_value = '';
             $logs->action = 'deleted';
             $logs->user_id = Auth::user()->id; 
             $logs->created_by = Auth::user()->id; 
             $logs->save();


            session()->flash('alert-danger', 'Product has been successfully deleted!');

            return redirect('admin/products');

        }
        else {

             return response()->view('errors.403');
        }


    }

}
