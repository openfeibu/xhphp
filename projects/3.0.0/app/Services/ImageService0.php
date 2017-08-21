<?php

namespace App\Services;

use Log;
use File;
use Session;
use Storage;
use Illuminate\Http\Request;
use Gregwar\Captcha\CaptchaBuilder;
use App\Repositories\UserRepository;
use App\Repositories\ImageRepository;
use App\Services\HelpService;
use App\Services\QiniuService;

class ImageService0
{

	protected $request;

	protected $userRepository;

	protected $imageRepository;

	protected $helpService;

	function __construct(Request $request,
						 HelpService $helpService ,
						 UserRepository $userRepository,
						 ImageRepository $imageRepository,
						 QiniuService $qiniuService)
	{
		$this->request = $request;
		$this->userRepository = $userRepository;
		$this->imageRepository = $imageRepository;
		$this->helpService = $helpService;
		$this->qiniuService = $qiniuService;
	}

	/**
	 * 生成图片验证码，并返回链接
	 */
	public function generateCaptcha()
	{
		try {
			$builder = new CaptchaBuilder;
	        $builder->build($width = 100, $height = 40, $font = null);
	        $phrase = $builder->getPhrase();
	        $this->putCaptchaIntoSession($phrase);
	        $file = 'image\captcha\\' . md5(time().'captcha') . '.jpg';
	        $builder->save($file);
		} catch (Exception $e) {
			throw new \App\Exceptions\Custom\RequestFailedException('获取图片验证码失败');
		}

        return $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() . '\\' . $file;
	}

	/**
	 * 将图片验证码中的验证码保存到Session中
	 */
	public function putCaptchaIntoSession($captcha)
	{
		Session::flash('captcha', $captcha);
	}

	/**
	 * 检验用户提交的验证码是否正确
	 */
	public function checkCaptchaWithInput($input)
	{
		if (Session::get('captcha', 0) and $input !== Session::get('captcha')) {
			throw new \App\Exceptions\Custom\CaptchaImageIncorrectException();
		}
		Session::forget('captcha');
		Session::forget('login.failure');
	}

	/**
	 * 上传图片
	 * 注意：用户必须是已登录状态
	 *
	 * @param  file $files  要上传的图片文件
	 * @param  string $usage 图片的用途，并将上传的图片文件存放到public/uploads/$usage中
	 *
	 * @return array        图片链接
	 */
	public function uploadThumbImages($files, $usage)
	{
		//获取用户信息
		$user = $this->userRepository->getUserByToken($this->request->token);

		//如果文件夹不存在，则创建文件夹
		$directory = public_path('uploads') . DIRECTORY_SEPARATOR . $usage;
        $thumb_directory = $directory . DIRECTORY_SEPARATOR . 'thumb';

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
            File::makeDirectory($thumb_directory, 0755, true);
        }

		//保存图片文件到服务器
		$i = 0;
		foreach ($files['uploadfile'] as $file) {
		    $extension = $file->getClientOriginalExtension();
		    $imageName = md5($user->token.time().rand()) . '.' . $extension;
		    $img_url = '/uploads/'.$usage.'/'.$imageName;
			$thumb_url = public_path().'/uploads/'.$usage.'/thumb/'.$imageName;
		    #todo 图片压缩：分别上传图片缩略图及其原图
		    Storage::put($img_url, file_get_contents($file->getRealPath()));

		    $images_url[$i]['img_url'] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/'. $img_url;
		    $images_url[$i]['uid'] = $user->uid;
		    $images_url[$i]['usage'] = $usage;
		    $images_url[$i]['created_at'] = date("Y-m-d H:i:s");

		    $imgs_url[$i] = $images_url[$i]['img_url'];
		    $thumbs_url[$i] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/uploads/'.$usage.'/thumb/'.$imageName;
		    $this->helpService->image_png_size_add(public_path().$img_url,$thumb_url);
		    $i++;
		}
		if ($i === 0) {
			$extension = $files['uploadfile']->getClientOriginalExtension();
			$imageName = md5($user->token.time().rand()) . '.' . $extension;
			$img_url = '/uploads/'.$usage.'/'.$imageName;
			$thumb_url = public_path().'/uploads/'.$usage.'/thumb/'.$imageName;

		    Storage::put($img_url, file_get_contents($files['uploadfile']->getRealPath()));

		    $images_url['img_url'] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/'. $img_url;
		    $images_url['uid'] = $user->uid;
		    $images_url['usage'] = $usage;
		    $images_url['created_at'] = date("Y-m-d H:i:s");

		    $imgs_url[] = $images_url['img_url'];
		    $thumbs_url[] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/uploads/'.$usage.'/thumb/'.$imageName;
		    $this->helpService->image_png_size_add(public_path().$img_url,$thumb_url);
		}

		//保存图片信息到数据库
	    $this->imageRepository->saveImages($images_url);

	    //保存图片链接到Session
	    // $this->saveImagesUrl2Session($imgs_url);

	    //将数组转成以逗号隔开的字符串
	    $image_url = $thumb_img_url = '';
	    $count = count($imgs_url) - 1;
	    if ($count > 0) {
		    for ($i=0; $i < $count; $i++) {
		    	$image_url .= $imgs_url[$i] . ',';
		    	$thumb_img_url .= $thumbs_url[$i] . ',';
		    }
		    $image_url .= $imgs_url[$i];
		    $thumb_img_url .= $thumbs_url[$i];
	    } else {
	    	$image_url = $imgs_url[0];
	    	$thumb_img_url .= $thumbs_url[0];
	    }

	    return [
			'image_url' => $image_url,
			'thumb_img_url'=> $thumb_img_url,
	    ];
	}
	/**
	 * 上传图片
	 * 注意：用户必须是已登录状态
	 *
	 * @param  file $files  要上传的图片文件
	 * @param  string $usage 图片的用途，并将上传的图片文件存放到public/uploads/$usage中
	 *
	 * @return array        图片链接
	 */
	public function uploadImages($files, $usage)
	{
		return
		//获取用户信息
		$user = $this->userRepository->getUserByToken($this->request->token);

		//如果文件夹不存在，则创建文件夹
        $directory = public_path('uploads') . DIRECTORY_SEPARATOR . $usage;
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

		//保存图片文件到服务器
		$i = 0;
		foreach ($files['uploadfile'] as $file) {
		    $extension = $file->getClientOriginalExtension();
		    $imageName = isset($user->token) ? md5($user->token.time().rand()) . '.' . $extension : md5(time().rand()) . '.' . $extension ;
		    $img_url = '/uploads/'.$usage.'/'.$imageName;

		    #todo 图片压缩：分别上传图片缩略图及其原图
		    Storage::put($img_url, file_get_contents($file->getRealPath()));

		    $images_url[$i]['img_url'] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() . $img_url;
		    $images_url[$i]['uid'] = isset($user->uid) ? $user->uid : 0;
		    $images_url[$i]['usage'] = $usage;
		    $images_url[$i]['created_at'] = date("Y-m-d H:i:s");

		    $imgs_url[$i] = $images_url[$i]['img_url'];
		    $i++;
		}
		if ($i === 0) {
			$extension = $files['uploadfile']->getClientOriginalExtension();
			$imageName = isset($user->token) ? md5($user->token.time().rand()) . '.' . $extension : md5(time().rand()) . '.' . $extension ;
			$img_url = '/uploads/'.$usage.'/'.$imageName;

		    Storage::put($img_url, file_get_contents($files['uploadfile']->getRealPath()));

		    $images_url['img_url'] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() . $img_url;
		    $images_url['uid'] = isset($user->uid) ? $user->uid : 0 ;
		    $images_url['usage'] = $usage;
		    $images_url['created_at'] = date("Y-m-d H:i:s");

		    $imgs_url[] = $images_url['img_url'];
		}

		//保存图片信息到数据库
	    $this->imageRepository->saveImages($images_url);

	    //保存图片链接到Session
	    // $this->saveImagesUrl2Session($imgs_url);

	    //将数组转成以逗号隔开的字符串
	    $image_url = '';
	    $count = count($imgs_url) - 1;
	    if ($count > 0) {
		    for ($i=0; $i < $count; $i++) {
		    	$image_url .= $imgs_url[$i] . ',';
		    }
		    $image_url .= $imgs_url[$i];
	    } else {
	    	$image_url = $imgs_url[0];
	    }

	    return $image_url;
	}

	/*  后台上传图片 */
	public function uploadAdminImages ($files, $usage,$id = 0)
	{
		//如果文件夹不存在，则创建文件夹
        $directory = $id ? public_path('uploads') . DIRECTORY_SEPARATOR . $usage. DIRECTORY_SEPARATOR .$id : public_path('uploads') . DIRECTORY_SEPARATOR . $usage;
        $thumb_directory = $directory. DIRECTORY_SEPARATOR .'thumb';
        $url = $id ?  '/uploads/'.$usage.'/'.$id : '/uploads/'.$usage;
        $thumb_url = $url.'/thumb';
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
            File::makeDirectory($thumb_directory, 0755, true);
        }

		//保存图片文件到服务器
		$i = 0;
		foreach ($files['uploadfile'] as $file) {
		    $extension = $file->getClientOriginalExtension();
		    $imageName = md5(time().rand()) . '.' . $extension;
		    $img = $url.'/'.$imageName;
			$thumb = $thumb_url.'/'.$imageName;
		    #todo 图片压缩：分别上传图片缩略图及其原图
		    Storage::put($img, file_get_contents($file->getRealPath()));

		    $images_url[$i]['img_url'] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/'. $img;
		    $images_url[$i]['usage'] = $usage;
		    $images_url[$i]['created_at'] = date("Y-m-d H:i:s");

		    $imgs_url[$i] = $images_url[$i]['img_url'];
		    $thumbs_url[$i] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/'.$thumb;
		    $this->helpService->image_png_size_add(public_path().$img,public_path().$thumb);
		    $i++;
		}
		if ($i === 0) {
			$extension = $files['uploadfile']->getClientOriginalExtension();
			$imageName = md5(time().rand()) . '.' . $extension;
			$img = $url.'/'.$imageName;
			$thumb = $thumb_url.'/'.$imageName;

		    Storage::put($img, file_get_contents($files['uploadfile']->getRealPath()));

		    $images_url['img_url'] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/'. $img;
		    $images_url['usage'] = $usage;
		    $images_url['created_at'] = date("Y-m-d H:i:s");

		    $imgs_url[] = $images_url['img_url'];
		    $thumbs_url[] = $this->request->getSchemeAndHttpHost() . $this->request->getBasePath() .'/'.$thumb;
		    $this->helpService->image_png_size_add(public_path().$img,public_path().$thumb);
		}

		//保存图片信息到数据库
	    $this->imageRepository->saveImages($images_url);

	    //保存图片链接到Session
	    // $this->saveImagesUrl2Session($imgs_url);

	    //将数组转成以逗号隔开的字符串
	    $image_url = $thumb_img_url = '';
	    $count = count($imgs_url) - 1;
	    if ($count > 0) {
		    for ($i=0; $i < $count; $i++) {
		    	$image_url .= $imgs_url[$i] . ',';
		    	$thumb_img_url .= $thumbs_url[$i] . ',';
		    }
		    $image_url .= $imgs_url[$i];
		    $thumb_img_url .= $thumbs_url[$i];
	    } else {
	    	$image_url = $imgs_url[0];
	    	$thumb_img_url .= $thumbs_url[0];
	    }

	    return [
			'image_url' => $image_url,
			'thumb_img_url'=> $thumb_img_url,
	    ];
	}
	/**
	 * 保存图片链接到Session
	 */
	public function saveImagesUrl2Session($images_url)
	{
		if (!is_array($images_url)) {
			$images_url = [$images_url];
		}
		if (Session::has('uploadImgUrl')) {
			$images_url = array_unique(array_merge($images_url, Session::get('uploadImgUrl')));
		}
		Session::put('uploadImgUrl', $images_url);
	}

}
