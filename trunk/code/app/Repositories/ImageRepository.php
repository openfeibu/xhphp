<?php

namespace App\Repositories;

use App\Image;
use Illuminate\Http\Request;

class ImageRepository
{

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 将单个图片的相关信息保存到数据库
	 */
	public function saveImage(array $param)
	{
		$img = new Image;
		$img->setConnection('write');
		$img->uid = $param['user_id'];
		$img->usage = $param['usage'];
		$img->img_url = $param['url'];
		$img->save();
	}

	/**
	 * 将多个图片的相关信息保存到数据库
	 */
	public function saveImages(array $imageArr)
	{
		Image::unguard();
        Image::insert($imageArr);
        Image::reguard();
	}


}