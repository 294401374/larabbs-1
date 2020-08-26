<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Image;
use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Http\Requests\Api\ImageRequest;

class ImagesController extends Controller
{
    public function store(ImageRequest $request, Image $image, ImageUploadHandler $uploader)
    {
        $user = $request->user();
        $size = $request->type == 'avatar' ? 416 : 1024;
        $folder = Str::plural($request->type);
        $file = $request->image;
        $result = $uploader->save($file, $folder, $user->id, $size );
        $image->path = $result['path'];
        $image->user_id = $user->id;
        $image->type = $request->type;
        $image->save();
        
        return new ImageResource($image);
    }
}
