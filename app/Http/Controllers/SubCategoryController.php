<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\SubCategory;


class SubCategoryController extends Controller
{

    public function index(Request $request){
        $subCategories = SubCategory::select('sub_categories.*','categories.name as categoryName')
                        ->latest('sub_categories.id')
                        ->leftJoin('categories','categories.id','sub_categories.category_id');

        if (!empty($request->get('keyword'))){
            $subCategories = $subCategories->where('sub_categories.name','like','%'.$request->get('keyword').'%');
            $subCategories = $subCategories->orWhere('sub_categories.name','like','%'.$request->get('keyword').'%');
        }

        $subCategories = $subCategories->paginate(5);

        return view ('admin.sub-category.list',compact('subCategories'));
    }


    public function create(){
        $categories = Category::orderBy('name','ASC')->get();


        return view('admin.sub-category.create', compact('categories'));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories',
            'category' => 'required',
            'status' => 'required',
        ]);

        if(!empty($validator->passes())){
            $subCategory = new SubCategory();
            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success','Sub-Category added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub-Category created Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        return view ('sub-categories.index');
    }

    public function edit($id, Request $request){
        $subCategory = SubCategory::find($id);

        $categories = Category::orderBy('name','ASC')->get();

        if(empty($subCategory)){
            $request->session()->flash('error', 'Not Found');
            return redirect()->route('sub-categories.index');
        }

        return view ('admin.sub-category.edit',compact('subCategory','categories'));
    }

    public function update($id, Request $request){

        $subCategory = SubCategory::find($id);

        if(empty($subCategory)){
            $request->session()->flash('error', 'Records Not Found');
            return response()->json([
                'status' => false,
                'notFound' => true,
            ]);
        }
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subCategory->id.',id',
            'category' => 'required',
            'status' => 'required',
        ]);

        if(!empty($validator->passes())){

            $subCategory->name = $request->name;
            $subCategory->slug = $request->slug;
            $subCategory->status = $request->status;
            $subCategory->category_id = $request->category;
            $subCategory->save();

            $request->session()->flash('success','Sub-Category Updated Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Sub-Category Updated Successfully',
            ]);
        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
        return view ('sub-categories.index');
    }

    public function destroy($id, Request $request){
        $subCategory = SubCategory::find($id);

        if (empty($subCategory)){
            $request->session()->flash('error','Sub Category not found');
            return response()->json([
                'status' => false,
                'message' => 'Sub Category Not Found',
            ]);
        }

        $subCategory->delete();
        $request->session()->flash('success','Sub Category Deleted Successfully');

        return response()->json([
            'status' => true,
            'message' => 'Sub Category Deleted Successfully',
        ]);
    }


}
