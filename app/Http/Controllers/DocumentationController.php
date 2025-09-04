<?php

namespace App\Http\Controllers;

use App\Events\LatestFiles;
use App\Mail\LatestFilesMail;
use App\Mail\UserTechnicalDocumentationUpdate;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Files;
use App\Models\Category;
use App\Models\Documents;
use App\Models\User;
use App\Models\Role;
use App\Models\Repairs;
use App\Models\Login;
use App\Models\Release;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class DocumentationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = DB::table('files')
                        ->where('type', 2)
                        ->get()
                        ->sortBy('DESC');

        $categories = DB::table('categories')
                        ->where( function($query) {
                                $query->where('file_type', 2);
                        })
                        ->get();

        $releases = DB::table('file_releases')
                         ->where( function($query) {
                                $query->where('file_type', 2);
                        })
                        ->get();

         $firmware_files = [];                   
         $temp_file = [];
         $count = 0;

         foreach( $categories as $category ) {

                $category_id = $category->id;
                $category_name = $category->name;

                foreach( $releases as $release ) {

                    $release_id = $release->id;
                    $db_files = DB::table('files')
                                ->where('category', '=', $category_id)
                                ->where( function($query) use ($category_id, $release_id) {
                                        $query
                                            ->where('release_id', '=', $release_id );
                                })
                                ->orderBy('release_id', 'DESC')
                                //->orderBy('created_at', 'DESC')
                                ->get();

                             foreach( $db_files as $file ) {
                                $count++;   
                                $temp_file[$category_id][$count]['category']   =  $category->name;
                                $temp_file[$category_id][$count]['release']    =  $release->name;
                                $temp_file[$category_id][$count]['release_id'] =  $release->id;
                                $temp_file[$category_id][$count]['id']         =  $file->id;
                                $temp_file[$category_id][$count]['version']    =  $file->version;
                                $temp_file[$category_id][$count]['filename']   =  $file->filename;
                                $temp_file[$category_id][$count]['name']       =  $file->name;
                                $temp_file[$category_id][$count]['created_at'] =  $file->created_at;

                            }
                }
                
         }               


        $files_collection = json_encode($temp_file);

        return view('documentation/index')->with([
            'files' => $files, 
            'categories' => $categories, 
            'releases' => $releases, 
            'files_collection' => $files_collection
        ]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = DB::table('categories')
                        ->where( function($query) {
                                $query->where('file_type', 2);
                        })
                        ->get();

        $releases = DB::table('file_releases')
                         ->where( function($query) {
                                $query->where('file_type', 2);
                        })
                        ->get();
        
        return view('documentation/create')->with([ 'categories' => $categories, 'releases' => $releases ]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if( $request->ajax() )
        {     
                
            $data = json_decode($request->input('files'));
        

            if(Auth::user()->isAdmin()) {


                $new_category =  $request->input('new_category');
                $category =  $request->input('category');
                $file_name =  $request->input('filename');


               if( $request->hasFile('file') ) {

                    // Upload and Save File
                    $file_upload = $request->file('file');
                    $filename = $file_upload->getClientOriginalName();
                    $filepath = $request->file('file')->storeAs('files', $filename);
                    $filefolder = 'files';
                    $file_size = File::size($file_upload);
                    


                    if( !empty( $new_category ) ) {

                        $category = new Category;
                        $category->name = $new_category;
                        $category->file_type = 2;
                        $category->total = 1;
                        $category->save();

                        $cat_id = $category->id;

                    } else {
                        
                        $cat_id = $category;
                    }

                    if( $request->input('new_release') ) {

                        $file_release = new Release;
                        $file_release->name = $request->input('new_release');
                        $file_release->file_type = 2;
                        $file_release->total = 1;
                        $file_release->save();

                    }
                

                    $file               = new Files;
                    $file->filepath     = $filepath;
                    $file->name         = $file_name;
                    $file->filename     = $filename;
                    $file->filesize     = $file_size;
                    $file->filefolder   = $filefolder;
                    $file->type         = 2;
                    $file->category     = $cat_id;
                    if( $request->input('new_release') ) {
                        $file->release_id   = $file_release->id;
                    } elseif( $request->input('release_select') ) {
                        $file->release_id   = $request->input('release_select');
                    }
                    $file->version      = $request->input('version');
                    $file->save();


                    // Send Notifications for the latest update
                    $file->link = url('technical-documentation');

                    if ($request->input('notif_subscriber') == 1) {
                        event(new LatestFiles($file, 'technical'));
                    }
               }

                $request->session()->flash('alert-success', 'Technical Document has been successfully added!');

                $upload_success = "yes";
                    
                return Response($upload_success);
            }
            else {
                return response()->view('errors.403');
            }

        }
        else 
        {
            return "ajax";
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
      
        $file = Files::findorfail($id);
        $filename = $file->filename;

        $total = DB::table('user_logs')
                    ->where('type','LIKE', '% download%')
                    ->where( function( $query ) use ( $filename ){
                        $query->where('description','LIKE', '%'. $filename .'%');
                    })
                    ->count();

             return view('documentation/show')->with(['file' => $file, 'total' => $total ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $file = Files::findorfail($id);

        return view('documentation/edit')->with(['file' => $file]);    
    }


    public function editCategoryName($id)
    {

        $category = Category::findorfail($id);
        return view('documentation/category/edit')->with(['category' => $category]);

    }

    public function updateTechDocsName(Request $request, $id)
    {
            $this->validate($request, [
                    'name' => 'required',
            ]);

          $category = Category::where('id', $id)->update(['name' => strip_tags($request->name) ]);

          $request->session()->flash('alert-success', 'Technical Documentation has been successfully updated!');

          return redirect()->route('technical-documentation.index');
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $updated = Files::where('id', $id)->update(['name' => $request->name,'version' => $request->version ]);
        
        if( $updated ) {
            $request->session()->flash('alert-success', 'Technical Documentation has been successfully updated!');
            return redirect()->route('technical-documentation.index');
        } else {
            $request->session()->flash('alert-danger', 'Technical Documentation was not successfully updated!');
            return redirect()->route('technical-documentation.index');
        }
  
    }

    public function view_upload($id)
    {
        $file = Files::where('id', $id)->first();

        return view('documentation/upload', [
            'file' => $file
        ]);
    }

    public function upload(Request $request, $id)
    {
        if( $request->hasFile('file') ) {
            $file = Files::find($id);

            // Upload and Save File
            $file_upload = $request->file('file');
            $filename = $file_upload->getClientOriginalName();

            $filefolder = '';

            if ($file->type == 1) {
                $filefolder = 'files';
            }

            if ($file->type == 2) {
                $filefolder = 'documents';
            }

            if ($file->type == 3) {
                $filefolder = 'files';
            }

            $filepath = $request->file('file')->storeAs($filefolder, $filename);
            $file_size = File::size($file_upload);
            
            
            $file->filepath    = $filepath;
            $file->filename    = $filename;
            $file->name        = strip_tags( $request->filename );
            $file->filesize    = $file_size;
            $file->filefolder  = $filefolder;
            $file->save();

            session()->flash('alert-success', 'A new file uploaded successfully!');

            if ($file->type == 1) {
                return redirect()->route('firmwares.index');
            }

            if ($file->type == 2) {
                return redirect()->route('technical-documentation.index');
            }

            if ($file->type == 3) {
                return redirect()->route('certificates.index');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function editRelease($id)
    {
        
        $release = Release::findorfail($id);


        return view('documentation/release/edit')->with(['release' => $release]);

    }


    public function updateRelease(Request $request, $id)
    {
        
        $release = DB::table('file_releases')->where('id', $id)->update(['name' => strip_tags($request->name) ]);

        $request->session()->flash('alert-success', 'Release  has been successfully updated!');
        return redirect()->route('technical-documentation.index');

    }

}
