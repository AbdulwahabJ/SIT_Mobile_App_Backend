<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Exception;
use App\Models\Moment;
use Illuminate\Support\Facades\Storage;



class MomentsController extends Controller
{


    public function uploadeMomentImages(Request $request)
    {

        $sectionName = $request->input('section_name');
        if ($sectionName == 'Holy Mosques') {
            $sectionName = 'holy_mosques';
        } else if ($sectionName == 'Religious Lectures') {
            $sectionName = 'religious_lectures';

        }
        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('moments_images/' . $sectionName, 'public');
                $imagePaths[] = $path;
            }
        }

        Moment::create([
            'image_paths' => json_encode($imagePaths),
        ]);

        return response()->json([
            'message' => 'Images uploaded successfully!',
            'section_name' => $request->input('section_name'),
            'images' => $imagePaths,
        ], 200);
    }


    public function getAllSectionImages()
    {
        $section1Path = 'moments_images/holy_mosques';
        $section2Path = 'moments_images/Mazarat';
        $section3Path = 'moments_images/religious_lectures';

        $section1Images = $this->getImagesFromDirectory($section1Path);
        $section2Images = $this->getImagesFromDirectory($section2Path);
        $section3Images = $this->getImagesFromDirectory($section3Path);

        return response()->json([
            'section1' => $section1Images,
            'section2' => $section2Images,
            'section3' => $section3Images,
        ], 200);
    }

    private function getImagesFromDirectory($directory)
    {
        $images = [];
        $files = Storage::disk('public')->files($directory);

        foreach ($files as $file) {
            $images[] = url('storage/' . $file);
        }

        return $images;
    }
}








// public function getAllMoments()
// {
//     $moments = Moment::all();

//     $section1 = [];
//     $section2 = [];
//     $section3 = [];

//     foreach ($moments as $moment) {
//         $moment->image_paths = json_decode($moment->image_paths);
//         dd(vars: $moment->image_paths);

//          // تحويل المسارات من JSON إلى مصفوفة
//         foreach ($moment->image_paths as &$path) {
//             // $path = Storage::url($path);
//             $path = asset('storage/moments_images' . $path);
//             // احصل على URL الصورة العامة
//         }
//     }

//     // return response()->json($moments);
// }
