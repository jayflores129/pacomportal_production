<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Categories;
use App\Models\Files;
use File;
use URL;
use Image;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Models\Documents;
use Illuminate\Support\Facades\Input;
use App\Models\Releases;
use App\Models\UserLogs;
use App\Events\RepairStatus;
use Response;
use DB;
use App\Models\Download;

class FilesController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
    * Display Upload Page
    * 
    * @param N/A
    */
    public function index() {

    }


    /**
    * Store Data from Upload File
    * 
    * @param $request
    */
    public function store(Request $request) {   

        if( $request->ajax() )
        {     
                

            $data = json_decode($request->input('files'));
            

            if(Auth::user()->isAdmin()) {

            	// Validating Input Fields
            	$this->validate($request, [
                    //'category' => 'required',s
                    //'files' => 'required',
                    //'document' => 'required'

                ]);

                //$category = $request->input('category');
                $category =  $request->input('category');

               if( $request->hasFile('file') ) {

               		// Upload and Save File
                    $file_upload = $request->file('file');
                    $filename = $file_upload->getClientOriginalName();
                    $filepath = $request->file('file')->storeAs('files', $filename);
                    $filefolder = 'files';
                    $file_size = File::size($file_upload);
                    
                    $file = new Files;
                    $file->filepath    = $filepath;
                    $file->filename    = $filename;
                    $file->name        = strip_tags( $request->filename );
                    $file->filesize    = $file_size;
                    $file->filefolder  = $filefolder;
                    $file->type        = 3;
                    $file->category    = null;
                    $file->release_id = null;
                    $file->save();

               }

        			$request->session()->flash('alert-success', 'File has been successfully saved!');

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
    * Store Firmware from Upload File
    * 
    * @param $request
    */
    public function store_firmware(Request $request) {   

        if( $request->ajax() )
        {     
                
            $data = json_decode($request->input('files'));
        

            if(Auth::user()->hasRole(['admin', 'super admin'])) {

                // Validating Input Fields
                $this->validate($request, [
                    //'category' => 'required',
                    //'files' => 'required',
                    //'document' => 'required'

                ]);


                $new_category =  $request->input('new_category');
                $category =  $request->input('category');


               if( $request->hasFile('file') ) {

                    // Upload and Save File
                    $file_upload = $request->file('file');
                    $filename = $file_upload->getClientOriginalName();
                    $filepath = $request->file('file')->storeAs('files', $filename);
                    $filefolder = 'files';
                    $file_size = File::size($file_upload);
                    

                    // Upload and Save Document
                    $document_upload = $request->file('document');
                    $documentname = $document_upload->getClientOriginalName();
                    $docpath = $request->file('document')->storeAs('documents', $documentname);
                    $docfolder = 'documents';
                    $document_size = File::size($document_upload);
           


                    if( !empty( $new_category ) ) {

                        $category = new Categories;
                        $category->name = $new_category;
                        $category->save();

                        $cat_id = $category->id;

                    } else {
                        
                        $cat_id = $category;
                    }

                    if( $request->input('new_release') ) {

                        $file_release = new Releases;
                        $file_release->name = $request->input('new_release');
                        $file_release->save();

                    }
                

                    $file               = new Files;
                    $file->filepath     = $filepath;
                    $file->filename     = $filename;
                    $file->filesize     = $file_size;
                    $file->filefolder   = $filefolder;
                    $file->type         = 1;
                    $file->category     = $cat_id;
                    if( $request->input('new_release') ) {
                        $file->release_id   = $file_release->id;
                    } elseif( $request->input('release_select') ) {
                        $file->release_id   = $request->input('release_select');
                    }
                    $file->version      = $request->input('version');
                    $file->save();

                    $doc = new Documents;
                    $doc->name = $documentname;
                    $doc->folder =  $docfolder;
                    $doc->file_id = $file->id;
                    $doc->save();
               }

                    $request->session()->flash('alert-success', 'File has been successfully saved!');

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
    * Store Document from Upload File
    * 
    * @param $request
    */
    public function store_document(Request $request) {   

        if( $request->ajax() )
        {     
                
            $data = json_decode($request->input('files'));
        

            if(Auth::user()->hasRole(['admin', 'super admin'])) {

                // Validating Input Fields
                $this->validate($request, [
                    //'category' => 'required',
                    //'files' => 'required',
                    //'document' => 'required'

                ]);


                $new_category =  $request->input('new_category');
                $category =  $request->input('category');


               if( $request->hasFile('file') ) {

                    // Upload and Save File
                    $file_upload = $request->file('file');
                    $filename = $file_upload->getClientOriginalName();
                    $filepath = $request->file('file')->storeAs('files', $filename);
                    $filefolder = 'files';
                    $file_size = File::size($file_upload);
                    

                    // Upload and Save Document
                    $document_upload = $request->file('document');
                    $documentname = $document_upload->getClientOriginalName();
                    $docpath = $request->file('document')->storeAs('documents', $documentname);
                    $docfolder = 'documents';
                    $document_size = File::size($document_upload);
           


                    if( !empty( $new_category ) ) {

                        $category = new Categories;
                        $category->name = $new_category;
                        $category->save();

                        $cat_id = $category->id;

                    } else {
                        
                        $cat_id = $category;
                    }

                    if( $request->input('new_release') ) {

                        $file_release = new Releases;
                        $file_release->name = $request->input('new_release');
                        $file_release->save();

                    }
                

                    $file               = new Files;
                    $file->filepath     = $filepath;
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

                    $doc = new Documents;
                    $doc->name = $documentname;
                    $doc->folder =  $docfolder;
                    $doc->file_id = $file->id;
                    $doc->save();
               }

                    $request->session()->flash('alert-success', 'File has been successfully saved!');

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

    public function confirm_delete($id = '') {

         if(Auth::user()->isAdmin()) {

            $file = Files::find($id);

            return Redirect::to('/files/delete')->with('file', $file );
        }
        else {
             return response()->view('errors.403');
        }
    }

    public function destroy(Request $request, $id) {
        $user = Auth::user();

        if(Auth::user()->isAdmin()) {

            $filename = Files::find($id)->value('filename');

            $filepath = './app/files/' . $filename;

             Storage::delete($filepath);

            $file = Files::find($id)->delete();
            


             $log = new UserLogs();
             $log->type = '<span class="bg-danger">File Deletion</span>'; 
             $log->description = $user->firstname . '' . $user->lastname . ' has deleted <strong>' . $filename . '</strong>';
             $log->old_value = '';
             $log->new_value = '';
             $log->action = 'delete';
             $log->user_id = Auth::user()->id; 
             $log->created_by = Auth::user()->id; 
             $log->save();

             $request->session()->flash('alert-success', 'File has been successfully deleted!');

            return Redirect()->back();
        }
        else {
             return response()->view('errors.403');
        }
    }

    public function show($id) {

        $file = Files::findorfail($id);
        $filename = $file->name;

        $total = DB::table('user_logs')
                    ->where('type','LIKE', '% download%')
                    ->where( function( $query ) use ( $filename ){
                        $query->where('description','LIKE', '%'. $filename .'%');
                    })
                    ->count();

        return view('files/show')->with(['file' => $file, 'total' => $total, 'filename' => $filename ]);

    }

    public function edit($id) {

         if(Auth::user()->isAdmin()) {

             $file = Files::findorfail($id);

             return view('files/delete')->with('file', $file );
        }
        else {
             return response()->view('errors.403');
        }

    }

    public function download( $id )
    {
        if (Auth::user())
        {
            $filename = Files::where('id', $id)->value('filename');

           $path = storage_path( 'app/files/' . $filename); 

           if (!file_exists($path)) {
            return '
                <script>
                    alert("File does not exist.");
                    window.history.back();
                </script>
            ';
           }

            $count    = Files::where( 'id', $id )->value('downloads');
            $count    = 1;

            if(Auth::user()->company != NULL) {
                $company = Auth::user()->company;
            } else {
                $company = '';
            }
             
            DB::table('files')->where( 'id', $id )
                       ->increment( 'downloads', 1 );   

                
            $download = new Download();
            $download->file_id = $id;
            $download->user_id = Auth::user()->id;
            $download->company_name = $company;
            $download->save(); 
           return response()->download($path);

        }
        else {
             return response()->view('errors.403');
        }

    }

    public function download_doc($doc)
    {
        if (Auth::user())
        {

           $path = storage_path( 'app/documents/' . $doc); 

           return response()->download($path);
        }
        else {
             return response()->view('errors.403');
        }

    }


    public function download_taskfile($doc)
    {
        if (Auth::user())
        {

           $path = storage_path( 'app/tickets/' . $doc); 

           return response()->download($path);
        }
        else {
             return response()->view('errors.403');
        }

    }

    public function download_comment_file($doc)
    {
        if (Auth::user())
        {

           $path = storage_path( 'app/comments/' . $doc); 

           return response()->download($path);
        }
        else {
             return response()->view('errors.403');
        }

    }

    public function download_software_file($doc)
    {
        if (Auth::user())
        {
            
           $path = storage_path( 'app/softwares/' . $doc); 

           return response()->download($path);
        }
        else {
             return response()->view('errors.403');
        }

    }

    public function view_file( $filename )
    {
      
        $path = public_path() . '/storage/profile/' . $filename;

         if( !File::exists($path )) {
            return 'error';
         };

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header('Content-Type', $type);

        return $response;
    
    }


}
