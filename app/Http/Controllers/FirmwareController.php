<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Category;
use App\Models\Release;
use App\Models\Files;
use File;
use URL;
use Image;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Models\Documents;
use DB;
use App\Events\LatestFiles;

class FirmwareController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = DB::table('files')
            ->where('type', 1)
            ->get()
            ->sortBy('DESC');

        $categories = DB::table('categories')
            ->where(function ($query) {
                $query->where('file_type', 1);
            })
            ->get();

        $releases = DB::table('file_releases')
            ->where(function ($query) {
                $query->where('file_type', 1);
            })
            ->get();

        $firmware_files = [];
        $temp_file = [];
        $count = 0;

        foreach ($categories as $category) {

            $category_id = $category->id;
            $category_name = $category->name;

            foreach ($releases as $release) {

                $release_id = $release->id;
                $db_files = DB::table('files')
                    ->where('category', '=', $category_id)
                    ->where(function ($query) use ($category_id, $release_id) {
                        $query
                            ->where('release_id', '=', $release_id);
                    })
                    ->orderBy('created_at', 'DESC')
                    ->get();

                foreach ($db_files as $file) {
                    $count++;
                    $array_temp = [];

                    $array_temp['category']   =  $category->name;
                    $array_temp['release']    =  $release->name;
                    $array_temp['release_id'] =  $release->id;
                    $array_temp['id']         =  $file->id;
                    $array_temp['version']    =  $file->version;
                    $array_temp['latest']     =  $file->latest;
                    $array_temp['filename']   =  $file->filename;
                    $array_temp['created_at'] =  $file->created_at;
                    $array_temp['name']       =  $file->name;
                    $array_temp['filelink']    =  $file->filelink;

                    $temp_file[$category_id][$count] = $array_temp;
                }
            }
        }


        $files_collection = json_encode($temp_file);

        return view('firmwares/index')->with(['files' => $files, 'categories' => $categories, 'releases' => $releases, 'files_collection' => $files_collection]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = DB::table('categories')
            ->where(function ($query) {
                $query->where('file_type', 1);
            })
            ->get();

        $releases = DB::table('file_releases')
            ->where(function ($query) {
                $query->where('file_type', 1);
            })
            ->get();

        if (Auth::user()->isAdmin()) {

            return view('firmwares/create')->with(['categories' => $categories, 'releases' => $releases]);
        } else {
            return response()->view('errors.403');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (! $request->ajax()) {
            return "Request for ajax only";
        }

        $data = json_decode($request->input('files'));


        if (Auth::user()->isAdmin()) {

            $new_category =  $request->input('new_category');
            $category     =  $request->input('category');
            $is_latest    =  $request->input('is_latest');
            $file_name    =  $request->input('filename');


            if ($request->hasFile('file')) {

                // Upload and Save File
                $file_upload = $request->file('file');
                $filename = $file_upload->getClientOriginalName();

                $filepath = $request->file('file')->storeAs('files', $filename);
                //$filepath   = 'firmwares';
                $filefolder = 'files';
                $file_size  = File::size($file_upload);

                // request()->file('file')->store(
                //   'firmwares',
                //   's3'
                // );

                // Upload and Save Document
                $document_upload = $request->file('document');
                $documentname = $document_upload->getClientOriginalName();
                $docpath = $request->file('document')->storeAs('documents', $documentname);
                $docfolder = 'documents';
                $document_size = File::size($document_upload);

                if (!empty($new_category)) {
                    $category = new Category;
                    $category->name = $new_category;
                    $category->file_type = 1;
                    $category->total = 1;
                    $category->save();
                    $cat_id = $category->id;
                } else {
                    $cat_id = $category;
                }


                if ($is_latest == 1) {
                    DB::table('files')
                        ->where('category', $cat_id)
                        ->where('latest', 1)
                        ->update(['latest' => 0]);
                }

                if ($request->input('new_release')) {

                    $file_release = new Release;
                    $file_release->name = $request->input('new_release');
                    $file_release->file_type = 1;
                    $file_release->total = 1;
                    $file_release->save();
                }


                $file               = new Files;
                $file->filepath     = $filepath;
                $file->name         = $file_name;
                $file->filename     = $filename;
                $file->filesize     = $file_size;
                $file->filefolder   = $filefolder;
                $file->latest       = $is_latest;
                $file->type         = 1;
                $file->category     = $cat_id;

                if ($request->input('new_release')) {
                    $file->release_id   = $file_release->id;
                } elseif ($request->input('release_select')) {
                    $file->release_id   = $request->input('release_select');
                }
                $file->version      = $request->input('version');
                $file->save();

                $doc = new Documents;
                $doc->name = $documentname;
                $doc->folder =  $docfolder;
                $doc->file_id = $file->id;
                $doc->save();

                if ($request->input('notif_subscriber') == 1) {
                    // Send Notifications for the latest update
                    event(new LatestFiles($file, 'software'));
                }
            } else {

                // Upload and Save File
                $filepath = $request->input('firmware_link');


                // Upload and Save Document
                $document_upload = $request->file('document');
                $documentname = $document_upload->getClientOriginalName();
                $docpath = $request->file('document')->storeAs('documents', $documentname);
                $docfolder = 'documents';
                $document_size = File::size($document_upload);

                if (!empty($new_category)) {
                    $category = new Category;
                    $category->name = $new_category;
                    $category->file_type = 1;
                    $category->total = 1;
                    $category->save();
                    $cat_id = $category->id;
                } else {
                    $cat_id = $category;
                }

                if ($is_latest == 1) {
                    DB::table('files')
                        ->where('category', $cat_id)
                        ->where('latest', 1)
                        ->update(['latest' => 0]);
                }

                if ($request->input('new_release')) {

                    $file_release = new Release;
                    $file_release->name = $request->input('new_release');
                    $file_release->file_type = 1;
                    $file_release->total = 1;
                    $file_release->save();
                }


                $file               = new Files;
                $file->filepath     = $filepath;
                $file->name         = $file_name;
                $file->filename     = $filepath;
                $file->filelink     = true;
                $file->filesize     = 0;
                $file->filefolder   = 'files';
                $file->latest       = $is_latest;
                $file->type         = 1;
                $file->category     = $cat_id;

                if ($request->input('new_release')) {
                    $file->release_id   = $file_release->id;
                } elseif ($request->input('release_select')) {
                    $file->release_id   = $request->input('release_select');
                }
                $file->version      = $request->input('version');
                $file->save();

                $doc = new Documents;
                $doc->name = $documentname;
                $doc->folder =  $docfolder;
                $doc->file_id = $file->id;
                $doc->save();

                // Send Notifications for the latest update
                event(new LatestFiles($file));
            } 

            $request->session()->flash('alert-success', 'New software/firmware has been successfully added!');

            $upload_success = "yes";


            return Response($upload_success);
        } else {
            return response()->view('errors.403');
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

        if (!$file) {

            return response()->view('errors.404');
        }

        $filename = $file->filename;

        $total = DB::table('user_logs')
            ->where('type', 'LIKE', '% download%')
            ->where(function ($query) use ($filename) {
                $query->where('description', 'LIKE', '%' . $filename . '%');
            })
            ->count();

        return view('firmwares/show')->with(['file' => $file, 'total' => $total]);
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

        return view('firmwares/edit')->with(['file' => $file]);
    }


    public function editCategoryName($id)
    {

        $category = Category::findorfail($id);


        return view('firmwares/category/edit')->with(['category' => $category]);
    }

    public function editRelease($id)
    {

        $release = Release::findorfail($id);


        return view('firmwares/release/edit')->with(['release' => $release]);
    }


    public function updateRelease(Request $request, $id)
    {

        $release = DB::table('file_releases')->where('id', $id)->update(['name' => strip_tags($request->name)]);

        $request->session()->flash('alert-success', 'Release  has been successfully updated!');
        return redirect()->route('firmwares.index');
    }

    public function updateFirmwareName(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $category = Category::where('id', $id)->update(['name' => strip_tags($request->name)]);

        $request->session()->flash('alert-success', 'Software/Firmware  has been successfully updated!');

        return redirect()->route('firmwares.index');
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
        if ($request->latest) {
            $is_latest = 1;

            $cat_id = DB::table('files')->where('id', $id)->value('category');

            DB::table('files')->where('category', $cat_id)
                ->where('latest', 1)
                ->update(['latest' => 0]);
        } else {
            $is_latest = 0;
        }
        $updated = Files::where('id', $id)->update(['name' => $request->name, 'version' => $request->version, 'latest' =>  $is_latest]);

        if ($updated) {
            $request->session()->flash('alert-success', 'Software/Firmware has been successfully updated!');
            return redirect()->route('firmwares.index');
        } else {
            $request->session()->flash('alert-danger', 'Software/Firmware was not successfully updated!');
            return redirect()->route('firmwares.index');
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
}
