<?php

namespace App\Services;

use Log;
use File;
use Session;
use Storage;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\ImageRepository;
use App\Services\HelpService;

class QiniuService
{

	protected $request;

	protected $userRepository;

	protected $imageRepository;

	protected $helpService;

	function __construct(Request $request,
						 HelpService $helpService ,
						 UserRepository $userRepository,
						 ImageRepository $imageRepository)
	{
		$this->request = $request;
		$this->userRepository = $userRepository;
		$this->imageRepository = $imageRepository;
		$this->helpService = $helpService;
	}

    public function uploadImages($files,$usage,$id = 0,$thumb = 1,$thumbnail = '400x')
    {
        $disk = \Storage::disk('qiniu');
        $url = $id ?  $usage.'/'.$id : $usage;

		
        $i = 0;
        foreach($files as $key => $file)
        {
            $extension = $file->getClientOriginalExtension();
		    $imageName = time().rand(100000, 999999) . '.' . $extension;
            $img = $url.'/'.$imageName;
            $disk->put($img,file_get_contents($file->getRealPath()));
            $images_url['img_url'] =  config('app.img_url').'/'. $img;
		    $images_url['usage'] = $usage;
		    $images_url['created_at'] = date("Y-m-d H:i:s");

            $imgs_url[] =  $images_url['img_url'];
		    $thumbs_url[] = $images_url['img_url'].'?imageMogr2/thumbnail/'.$thumbnail;
        }
        //保存图片信息到数据库
        $this->imageRepository->saveImages($images_url);

		$image_url = implode(',',$imgs_url);
		$thumb_img_url = implode(',',$thumbs_url);

        if($thumb)
        {
            return [
                'image_url' => $image_url,
                'thumb_img_url'=> $thumb_img_url,
            ];
        }
        return $image_url;
    }

}
