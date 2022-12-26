<?php

namespace App\Http\Controllers;

use App\Models\Image;
use Illuminate\Http\Request;
use App\Http\Requests\UploadRequest;
use App\Http\Requests\StatusUpdateRequest;
use App\Models\User;
use App\Models\Images;
use App\Models\VerifyTokens;
use Illuminate\Support\Facades\File;
class ImageController extends Controller
{
    //upload image
    public function uploadImage(UploadRequest $request)
    {
        $user = UserVerify::where('token')->first()->user;
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $path = $request->image->store('Images');
        if ($request->has('status') == '') {
            $status = 'hidden';
        } else {
            $status = $request->status;
        }
        if ($file) {
            $image = Image::create([
                'tile' => $request->input('title'),
                'path' => $path,
                'status' => $request->input('status'),
                'extension' => $extension,
            ]);
            $user->image()->attach($image->id);
            return response()->json([
                'status' => 'true',
                'message' => 'Image Uploaded Successful',
            ]);
        } else {
            return response()->json([
                'status' => 'false',
                'message' => 'Image Not Uploaded!!',
            ]);
        }
    }

    //View/List image
    public function viewImage()
    {
        $images = Image::where('status', 'public')->get();
        if ($iamge) {
            $images = Image::orderBy('id', 'DESC')->get();
            return response()->json(['message' => 'Images Found', $images]);
        } else {
            return response()->json([
                'message' => 'Image not found',
            ]);
        }
    }

    // Delete Image
    public function deleteImage($id)
    {
        $user = UserVerify::where('token')->first()->user;
        $image = Image::find($request->$id);
        if ($image) {
            $image->delete();
            return response()->json([
                'message' => 'Image deleted successfully',
            ]);
        } else {
            return response()->json([
                'message' => 'Image not found',
            ]);
        }
    }
    //search image
    public function searchImage(Request $request)
    {
        $user = UserVerify::where('token')->first()->user;
        $image = $user->image();
        $images = $this->searchQuery($request, $image);
        if ($images) {
            return response()->json([
                'status' => 'true',
                'message' => 'Image Found',
                'data' => $images,
            ]);
        }
        return response()->json('Image  does not exist', false);
    }

    public function searchQuery(Request $request, $image)
    {
        if ($request->has('title')) {
            $image->where('title', $request->get('title'));
        }
        if ($request->has('extension')) {
            $image->where('extension', $request->get('extension'));
        }
        if ($request->has('status')) {
            $image->where('status', $request->get('status'));
        }
        return $image->orderBy('id')->get();
    }
    // Set staus for picture
    public function statusUpdate(StatusUpdateRequest $request)
    {
        $image = image::where('id', $request->image_id)->first();
        if (hash_equals($request->status, 'private')) {
            if ($request->has('email')) {
                $user = User::where('email', $request->email)->first();
                $image->users()->attach($user);
            } else {
                return response()->json('Email for Private pictures ', false);
            }
        }
        $image->status = $request->status;
        $image->save();
        return response()->json($image, true);
    }
    // generates the shareable link
    public function getShareableLink(Request $request)
    {
        $image = image::where('id', $request->image_id)->first();
        if (!hash_equals($image->status, 'public')) {
            $user = VerifyToken::where('token', $request->token)->first()->user;
            if (!$image->users->contains($user)) {
                return response()->json('not accessible for you', false);
            }
        }
        $imageLink = route('imageview.image', [$image->id]);
        return response()->json($imageLink, true);
    }

    public function imageview(Request $request, $id)
    {
        $image = image::where('id', $id)->first();
        if (!hash_equals($image->status, 'public')) {
            $user = VerifyToken::where('token', $request->token)->first()->user;
            if (!$image->users->contains($user)) {
                return response()->json('not accessible for you', false);
            }
        }
        return response()->json($image->makeHidden('users'), true);
    }
}
