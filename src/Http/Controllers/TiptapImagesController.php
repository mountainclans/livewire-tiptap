<?php

namespace MountainClans\LivewireTiptap\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\ImageManager;
use MountainClans\LivewireTiptap\Models\EditorMedia;

class TiptapImagesController
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => ['required', 'image', 'max:10240'], // до 10 МБ
        ]);

        $file = $request->file('image');
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file->getPathname());

        $image->scaleDown(width: 1290);

        $filename = Str::uuid() . '.webp';
        $relativePath = 'tiptap/' . $filename;

        Storage::disk('public')->makeDirectory('tiptap');
        $image->toWebp()->save(
            Storage::disk('public')->path($relativePath)
        );

        EditorMedia::create([
            'path' => $relativePath,
        ]);

        return response()->json([
            'url' => Storage::url($relativePath),
        ]);
    }
}
