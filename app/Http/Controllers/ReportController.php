<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class ReportController extends Controller
{



    public function index()
    {


        try{

            $reports=Report::latest()->paginate(10);
            return response()->json(
        [ 
            'success'=>true,
            'data'=>$reports->items(),
             'pagination' => [
                    'current_page' => $reports->currentPage(),
                    'last_page' => $reports->lastPage(),
                    'per_page' => $reports->perPage(),
                    'total' => $reports->total(),
                ]
        ] );


        }

        catch (\Exception $e)
        {
            return response()->json([

                'success'=>false,
                'message'=>'Failed to fetch file' . $e->getMessage()
            ],500 );



        }




    }

     public function show($id)
    {
 

        try{

            $report=Report::findOrFail($id);
            return response()->json(
        [ 
            'success'=>true,
            'data'=>$report,
             
        ] );


        }

        catch (\Exception $e)
        {
            return response()->json([

                'success'=>false,
                'message'=>'Not found'
            ],404 );



        }




    }


    
public function store(Request $request)
{

$validator= Validator::make($request->all(),[

'name'=>'required|string|max:255',
'description'=>'required|string',
'document' => 'nullable|file|mimes:pdf,doc,docx|max:7120',



]);

if($validator->fails()){

    return response()->json([
        'success'=>false,
        'message'=>'Validation failed',
        'errors'=>$validator->errors()

    ],422);


}


try{


    $report=new Report();
    $report->name=$request->name;
    $report->description=$request->description;
        $report->url = "";

    
    if ($request->hasFile('document')) {
        $document = $request->file('document');
        $filename = time() . '_' . $document->getClientOriginalName();
        $path = $document->storeAs('uploads', $filename, 'public');
        $report->url = $filename;
    }
    
    // $report->url=$request->url;
$report->save();

return response()->json([

'success'=>true,
'message'=>'Uploaded Succesfully',
'data'=>$report

],201);



}

catch(\Exception $e){


return response()->json([

    'success'=>false,
    'message'=>'Failed to upload Post' . $e->getMessage(),


],404);

}





}


public function update(Request $request,$id)
{

   $validator= Validator::make($request->all(),[

'name'=>'required|string|max:255',
'description'=>'required|string',
'document' => 'nullable|file|mimes:pdf,doc,docx|max:7120',



]);

    if($validator->fails())
    {

        return response()->json([
            'success'=>false,
            'message'=>'validation failed',
            'errors'=>$validator->errors(),



        ],422);



    }


    try{

        $report=Report::findOrFail($id);

$report->name=$request->name;
$report->description=$request->description;


if($request->hasFile('document'))
{

    if($report->url)
    {

        $this->deleteFileFromStorage($report->url);
    }

  $file = $request->file('document');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('uploads', $filename, 'public');
                $report->url = $filename;


}

$report->save();

return response()->json([
'success'=>true,
'message'=>'Report Updated Successfully',
'data'=>$report

]);


    }


    catch(\Exception $e)
    {
        return response() ->json([

             'success' => false,
                'message' => 'Failed to update blog post: ' . $e->getMessage()
        ],500);




    
    }






}


public function destroy($id)
{


    try{
    $report=Report::findOrFail($id);
    
    if($report->url)
    {


        $this->deleteFileFromStorage($report->url);


    }


    $report->delete();

    return response()->json(['success'=>true,'message'=>'Deleted succesfully']);

    }

    catch(\Exception $e){


        return response()->json(

            [
                'success'=>true,
                'message'=>'Deleted unsuccesfully',
                'error'=>$e.getMessage(),


            ],500
        );




    }







}







 private function deleteFileFromStorage($filename)
    {
        try {
            if (Storage::disk('public')->exists('uploads/' . $filename)) {
                Storage::disk('public')->delete('uploads/' . $filename);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }




    //
}
