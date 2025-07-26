<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\Files;
use App\Events\LatestFiles;
use File;
use Auth;

class CertificateController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = DB::table('files')
                        ->where('type', 3)
                        ->orderBy('created_at', 'DESC')
                        ->get();
            
        return view('certificates/index')->with('files', $files);  
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('certificates/create');
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


                    // Send Notifications for the latest update
                    $file->link = url('certificates');
                    $file->type = 'certificates';
                    event(new LatestFiles($file));

               }

                 $request->session()->flash('alert-success', 'File has been successfully added!');

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
     * Edit Certificate's name
     * @param  int $id 
     * @return  view
     */
    public function editName($id)
    {

        $file = Files::findorfail($id);
        return view('certificates/edit')->with(['file' => $file]);

    }


    /**
     * Process the changes to the certificate's name
     * @param  Request $request 
     * @param  Int     $id      
     * @return Route       
     */
    public function updateName(Request $request, $id)
    {
            $this->validate($request, [
                    'name' => 'required',
            ]);

          $category = Files::where('id', $id)->update(['name' => strip_tags($request->name) ]);

          $request->session()->flash('alert-success', 'Certificate\'s name has been successfully updated!');

          return redirect()->route('certificates.index');
        
    }

}
