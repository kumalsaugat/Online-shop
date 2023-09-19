<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index(Request $request){
        $brands = Brand::latest('id');



        if (!empty($request->get('keyword'))){
            $brands = $brands->where('name','like','%'.$request->keyword.'%');
        }

        $brands = $brands->paginate(5);

        return view ('admin.brand.list',[
            'brands' => $brands,
        ]);
    }

    public function create(){


        return view('admin.brand.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands',
            'status' => 'required',
        ]);

        if(!empty($validator->passes())){
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success','Brand added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand created Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        return view('brand.index');
    }

    public function edit($id, Request $request){
        $brand = Brand::find($id);

        if(empty($brand)){
            $request->session()->flash('error', 'Not Found');
            return redirect()->route('brand.index');
        }

        return view ('admin.brand.edit',[
            'brand' => $brand ,
        ]);
    }

    public function update($id, Request $request){

        $brand = Brand::find($id);

        if(empty($brand)){
            $request->session()->flash('error', 'Records Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
            'status' => 'required',
        ]);

        if(!empty($validator->passes())){

            $brand->name = $request->name;
            $brand->slug = $request->slug;
            $brand->status = $request->status;
            $brand->save();

            $request->session()->flash('success','Brand Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Brand Updated Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        return view ('brand.index');
    }

    public function destroy($id, Request $request){
        $brand = Brand::find($id);

        if (empty($brand)){
            $request->session()->flash('error','Brand not found');
            return response()->json([
                'status' => false,
                'message' => 'Brand Not Found',
            ]);
        }

        $brand->delete();
        $request->session()->flash('success','Brand Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Brand Deleted Successfully',
        ]);
    }

}
