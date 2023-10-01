<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\TempImage;
use Image;




class ProductController extends Controller
{
    public function index(Request $request){
        $products = Product::latest('id');



        if (!empty($request->get('keyword'))){
            $products = $products->where('name','like','%'.$request->keyword.'%');
        }


        $products = $products->paginate(5);

        return view ('admin.product.list',[
            'products' => $products,
        ]);
    }

    public function create(){

        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();

        return view('admin.product.create',[
            'categories' => $categories ,
            'brands' => $brands,
        ]);
    }

    public function store(Request $request){

        $rules = [
            'title'=> 'required',
            'slug'=> 'required|unique:products',
            'price'=> 'required',
            'sku'=> 'required',
            'track_qty'=> 'required|in:Yes,NO',
            'category'=> 'required|numeric',
            'is_featured' => 'required|in:Yes,No',
        ];
        if(!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if(!empty($validator->passes())){
            $product = new Product();
            $product->title = $request->title;
            $product->slug = $request->slug;
            $product->description = $request->description;
            $product->price = $request->price;
            $product->compare_price = $request->compare_price;
            $product->sku = $request->sku;
            $product->barcode = $request->barcode;
            $product->track_qty = $request->track_qty;
            $product->qty = $request->qty;
            $product->status = $request->status;
            $product->category_id = $request->category;
            $product->sub_category_id = $request->sub_category;
            $product->brand_id = $request->brand;
            $product->is_featured = $request->is_featured;
            $product->save();

            //Gallery Save
            if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_image_id){

                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray); // like jpg,gif,png etc.

                    $productImage = new ProductImage();
                    $productImage->product_id = $product->id;
                    $productImage->image = 'NULL';
                    $productImage->save();

                    $imageName = $product->id.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //Generate Product Thumbnails

                    // Large Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destPath = public_path().'/uploads/product/large/'.$tempImageInfo->name;
                    $image = Image::make($sourcePath);
                    $image->resize(1400, null,function ($constraint){
                        $constraint->aspectRatio();
                    });
                    $image->save($destPath);

                    // small Image
                    $destPath = public_path().'/uploads/product/small/'.$tempImageInfo->name;
                    $image = Image::make($sourcePath);
                    $image->fit(300, 300);
                    $image->save($destPath);


                }
            }

            $request->session()->flash('success','Product Added Successfully');

            return response()->json([
                'status' => true,
                'message' =>'Product Added Successfully' ,
            ]);


        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    // public function edit($id, Request $request){
    //     $brand = Brand::find($id);

    //     if(empty($brand)){
    //         $request->session()->flash('error', 'Not Found');
    //         return redirect()->route('brand.index');
    //     }

    //     return view ('admin.brand.edit',[
    //         'brand' => $brand ,
    //     ]);
    // }

}
