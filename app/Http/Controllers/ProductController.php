<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{


    public function index ()
    {


        try{


        $products=Product::latest()->paginate(10);


        return response()->json([
            'success'=>true,
            'message'=>'Succesful',
            'data'=>$products->items(),
            'pagination'=>
            [
                  'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),

            ]
        
        
        ]);


        }

        catch (\Exception $e)
        {


            return response()->json([
                'success'=>true,
                'message'=>'Not Found',
            'error'=>$e.getMessage()
        ],500);


        }











    }
    //

    public function store(Request $request)
    {



        $validator=Validator::make($request->all(),[
            'name'=>"string|required|max:255",
       
            'description'=>"string|required",
                       'price'=>"integer|required",
            'sale_price'=>"integer|required",
            'stock_quantity'=>"integer|required",
            'manage_stock'=>"integer|required",
            'weight'=>"integer|required",
            'featured_image'=>"image|mimes:jpg,png,jpeg,gif|max:5120"





        ]);



        if($validator->fails())
        {


return response()->json([

    'success'=>false,
    'message'=>'Validation failed',
    'error'=>$validator->errors()
],422);

        }


        try{

            $Check_product=Product::where("name","=",$request->name)
                            ->where("description","=",$request->description)
                            ->where("price","=",$request->price) ->get();


            if(count($Check_product)>0)
            {



                
  return response()->json([
'success'=>false,
'message'=>'Product Already Exists'

],403);

            }

            else{ 

            $product=new Product();



            $product->name=$request->name;
            $product->slug=$this->generateSlug($request->name);
            $product->description=$request->description;
            $product->price=$request->price;
            $product->sale_price=$request->sale_price;
            $product->stock_quantity=$request->stock_quantity;
            $product->manage_stock=$request->manage_stock;
            $product->weight=$request->weight;

  if ($request->hasFile('featured_image')) {
                $image = $request->file('featured_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('uploads', $filename, 'public');
                $product->featured_image = $filename;
            }


            

            $product->save();
            return response()->json([
'success'=>true,
'message'=>'Product Created',
'data'=>$product

],201);


}



        }

        catch(\Exception $e){


return response()->json([
'success'=>'failed',
'message'=>'Failed ' . $e->getMessage()


],500);

        }



    }





        public function show($id)
{


    try{

    $product=Product::findOrFail($id);
    
    return response()-> json([
        'success'=>true,
        'data'=>$product

    ]);



    }

    catch(\Exception $e)
    {


         return response()-> json([
        'success'=>false,
        'message'=>'Not found',
        


    ],404);
        



    }





    

    
}



public function update (Request $request,$id)
{


     $validator=Validator::make($request->all(),[
            'name'=>"string|required|max:255",
       
            'description'=>"string|required",
                       'price'=>"integer|required",
            'sale_price'=>"integer|required",
            'stock_quantity'=>"integer|required",
            'manage_stock'=>"integer|required",
            'weight'=>"integer|required",
            'featured_image'=>"image|mimes:jpg,png,jpeg,gif|max:5120"





        ]);



        if($validator->fails())
        {


return response()->json([

    'success'=>false,
    'message'=>'Validation failed',
    'error'=>$validator->errors()
],422);

        }






        try{

            $product=Product::findOrFail($id);
             $product->name=$request->name;
            $product->slug=$this->generateSlug($request->name);
            $product->description=$request->description;
            $product->price=$request->price;
            $product->sale_price=$request->sale_price;
            $product->stock_quantity=$request->stock_quantity;
            $product->manage_stock=$request->manage_stock;
            $product->weight=$request->weight;



            if($request->hasFile('featured_image'))
            {

              if ($product->featured_image) {
                    $this->deleteFileFromStorage($product->featured_image);
                }

                $image = $request->file('featured_image');
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('uploads', $filename, 'public');
                $product->featured_image = $filename;


            }
            $product->save();

            return response()->json([
                'success' => true,
                'message' => 'Product  updated successfully',
                'data' => $product
            ]);





        }

        catch(\Exception $e)
        {

return response()->json([
                'success' => false,
                'message' => 'Failed to update Product: ' . $e->getMessage()
            ], 500);



        }





}





public function destroy($id){


    try{

$product=Product::findOrFail($id);
$img=$product->featured_image;

if($img){

    
$this->deleteFileFromStorage($img);

}


$product->delete();


return response()->json([
'success'=>true,
'message'=>'Successfully Deleted'

]);


    }

    catch(\Exception $e)
    {


        return response()->json([
'success'=>false,
'message'=>'Failed'. $e->getMessage()
]);



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


    





     private function generateSlug($title)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        $originalSlug = $slug;
        $count = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

}
