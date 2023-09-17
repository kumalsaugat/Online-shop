<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Support\Facades\File;
use Image;

class CategoryController extends Controller
{
    public function index(Request $request) {
        $categories = Category::latest();
        if (!empty($request->get('keyword'))){
            $categories = $categories->where('name','like','%'.$request->get('keyword').'%');
        }

        $categories = $categories->paginate(5);

        return view ('admin.category.list',compact('categories'));
    }

    public function create() {
        return view ('admin.category.create');
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:categories',
        ]);

        if ($validator->passes()){

            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->save();

            //Save Image Here
            if (!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);

                $newImageName = $category->id.'.'.$ext;
                $sPath = public_path().'/temp/'.$tempImage->name;  //sourcePath
                $dPath = public_path().'/uploads/category/'.$newImageName;  //destinationPath

                File::copy($sPath,$dPath);

                //Generate image thumbnail using Imageintervention
                $dPath = public_path().'/uploads/category/thumb/'.$newImageName;
                $img = Image::make($sPath);
                $img->resize(450, 600);
                $img->save($dPath);


                $category->image = $newImageName ;
                $category->save();
            }

            $request->session()->flash('success','Category added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category Added Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        return view ('categories.index');
    }

    public function edit() {

    }

    public function update() {

    }
    public function destroy() {

    }
}
