<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Files;
use DB;
use Response;
use App\Documents;

class ApiController extends Controller
{

	/**
	 * Retrieve Firmware and Softwares
	 * @return [type] [description]
	 */
    public function firmware()
    {
        $files     = DB::table('files')
                    ->where('type', 1)
                    ->get()
                    ->sortBy('DESC'); 

        $categories = DB::table('categories')
                    ->where( function($query) {
                            $query->where('file_type', 1);
                    })
                    ->get();

        $releases   = DB::table('file_releases')
                     ->where( function($query) {
                            $query->where('file_type', 1);
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
                            ->get();

                        foreach( $db_files as $file ) {
                            $count++;   

                            $document = DB::table('documents')->where('file_id', $file->id)->value('name'); 
                            $document_id = DB::table('documents')->where('file_id', $file->id)->value('id');


                            $temp_file['id']                =  $file->id;
                            $temp_file['category']          =  'firmware';
                            $temp_file['name']              =  $category->name;
                            $temp_file['release']           =  $release->name;
                            $temp_file['version']           =  $file->version;
                            $temp_file['filename']          =  $file->filename;
                            $temp_file['is_latest']         =  $file->latest;
                            $temp_file['referenced_file_id'] =  $document_id;
                            $temp_file['updated_at']        =  $file->updated_at;
                            $temp_file['created_at']        =  $file->created_at;

                            $firmware_files[] = $temp_file;
                        }
                    $count = 0;
            }    
        }               



        return $firmware_files;
    }

    /**
     * Retrive all Technical Documentation
     * @return array
     */
    public function tecnicalDocumentation()
    {
        $files      = DB::table('files')
                        ->where('type', 2)
                        ->get()
                        ->sortBy('DESC');

 
        $categories = DB::table('categories')
                        ->where( function($query) {
                                $query->where('file_type', 2);
                        })
                        ->get();

        $releases   = DB::table('file_releases')
                         ->where( function($query) {
                                $query->where('file_type', 2);

                        })
                        ->get();

         $technical_files = [];                   
         $temp_file      = [];
         $count          = 0;

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
                                $document = DB::table('documents')->where('file_id', $file->id)->value('name');
                                $document_id = DB::table('documents')->where('file_id', $file->id)->value('id');

                                $temp_file['id']                =  $file->id;
                                $temp_file['category']          =  'technical documentation';
                                $temp_file['name']              =  $category->name;
                                $temp_file['release']           =  $release->name;
                                $temp_file['version']           =  $file->version;
                                $temp_file['filename']          =  $file->filename;
                                $temp_file['updated_at']        =  $file->updated_at;
                                $temp_file['created_at']        =  $file->created_at;

                                $technical_files[] =  $temp_file;
                            }
                        $count = 0;
                }
                

         }               

        return $technical_files;
    }

    /**
     * Retreives all certificats
     * @return array
     */
    public function certificates()
    {
        $files = DB::table('files')
                ->where('type', 3)
                ->get()
                ->sortBy('DESC');

        $output   = [];        
        $response = [];
        $count    = 0;

        foreach( $files as $file) {
        	$count++;


        	$response['id'] = $file->id;
            $response['category'] = 'certificates';
        	$response['filename'] = $file->name;
        	$response['created_at'] = $file->created_at;

            $output[] = $response;

        }        

        return $output;      
            
    }

    /**
     * Download using the id of the file
     * @param  int  $id 
     * @return response file     
     */
    public function download( $id )
    {

       $filename = Files::where('id', $id)->value('filename');	


       if( empty($filename) ) {
       		return response()->json([
				    'data' => 'Resource not found',
				]);
       } else {

	       return response()->download( storage_path( 'app/files/' . $filename ) );

       }

    }

    /**
     * Download using the id of the document
     * @param  int  $id 
     * @return response file    
     */
    public function downloadDocument( $id )
    {

       $filename = Documents::where('id', $id)->value('name');	

  
       if( empty($filename) ) {
       		return response()->json([
				    'data' => 'Resource not found',
				]);
       } else {

	       return response()->download( storage_path( 'app/documents/' . $filename ) );

       }


    }


}
