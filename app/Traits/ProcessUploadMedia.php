<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 3/25/2019
 * Time: 16:40
 */

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait ProcessUploadMedia
{

    public function processMedia($data)
    {

        Storage::disk( $data->action)->put($data->path, base64_decode($data->base64));
//        if ($data->type == 1) {
//            $video = public_path($data->action . '/' . ($data->path));
//            $thumbnail = public_path('thumbnail/'. $data->thumbnail) ;
//            $ffmpeg = env('FFMPEG');
//            if (!is_dir(public_path('thumbnail'))) {
//                mkdir(public_path('thumbnail'));
//            }
//            (exec("$ffmpeg -i $video -deinterlace -an -ss 1 -t 00:00:01 -r 1 -y -vcodec mjpeg -f mjpeg $thumbnail 2>&1"));
//
//        }

    }

}