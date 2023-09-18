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

    public function edit($categoryId, Request $request) {
        $category = Category::find($categoryId);
        if(empty($category)){
            return redirect()->route('categories.index');
        }

        return view ('admin.category.edit',compact('category'));
    }

    public function update($categoryId, Request $request) {
        $category = Category::find($categoryId);

        if(empty($category)){
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category Not Found',
            ]);
        }

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

            $oldImage = $category->image;

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

                //Delete Old Image Here
                File::delete(public_path().'/uploads/category/thumb/'.$oldImage);
                File::delete(public_path().'/uploads/category/'.$oldImage);
            }

            $request->session()->flash('success','Category Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Category Updated Successfully',
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
    public function destroy($categoryId, Request $request) {
        $category = Category::find($categoryId);
        if (empty($category)){
            $request->session()-flash('error','Category not found');
            return response()->json([
                'status' => true,
                'message' => 'Category Not Found',
            ]);
        }

        File::delete(public_path().'/uploads/category/thumb/'.$category->image);
        File::delete(public_path().'/uploads/category/'.$category->image);

        $category->delete();

        $request->session()->flash('success','Category Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Category Deleted Successfully',
        ]);

    }
}
