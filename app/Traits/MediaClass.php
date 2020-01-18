<?php

namespace App\Traits;

use App\Http\Resources\MediaResource;

use App\Media;
use App\MyConst;

trait MediaClass
{
    use ProcessUploadMedia;

    public function upload($type, $image_base64, $user)
    {
        // type: 0: avatar, 1:certificate
        $path = $user;
        switch ($type) {

            case MyConst::AVATAR:
                $type_action = 'avatars';
                break;
            case MyConst::CERTIFICATE:
                $type_action = 'certificates';
                break;
            case MyConst::BANNER:
                $type_action = 'banner';
                break;

        }
        @list(, $image_base64) = explode(',', $image_base64);
        $filename = str_random(3);
        //generating unique file name;
        $file_name = 'image_' . $filename . '.jpeg';
        $link = '';
        if ($image_base64 != "") { // storing image in storage/app/public Folder
            $data = new \stdClass();
            $data->action = $type_action;
            $data->path = $path . '/' . $file_name;
            $data->base64 = ($image_base64);
            $this->processMedia($data);
            $link = '/' . $type_action . '/' . $path . '/' . $file_name;
        }
        return $link;
    }

}
