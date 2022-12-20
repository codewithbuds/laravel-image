<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
class ImageController extends Controller
{
    //upload image
    public function create(Request $request)
    {
        $images = new Image();
        $request->validate([
            'title' => 'required',
            'image' => 'required|max:1024',
        ]);

        $filename = '';
        if ($request->hasFile('image')) {
            $filename = $request->file('image')->store('posts', 'public');
        } else {
            $filename = null;
        }

        $images->title = $request->title;
        $images->image = $filename;
        $result = $images->save();
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
    //View image
    public function get()
    {
        $images = Image::orderBy('id', 'DESC')->get();
        return response()->json($images);
    }

    // Remove Image
    public function delete($id)
    {
        $images = Image::findOrFail($id);
        $destination = public_path('storage\\' . $images->image);
        if (File::exists($destination)) {
            File::delete($destination);
        }
        $result = $images->delete();
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function private($image)
    {
        $path = "private/images/{$image}";
        if (Storage::exists ($path)) {
             return Storage::download ($path);
        }     
        abort (404);
    }

}
