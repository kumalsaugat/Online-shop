<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TempImage;

class TempImagesController extends Controller
{
    public function index() {
        return view ('');
    }

    public function create(Request $request) {
        $image = $request->image;

        if (!empty($image)){
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp',$newName);

            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'message' => 'image uploaded successfully'
            ]);
        }
    }

    public function store() {
        return view ('');
    }
}
