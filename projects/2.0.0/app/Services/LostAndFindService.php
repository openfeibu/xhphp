<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\LostAndFindRepository;
use App\Repositories\UserRepository;

class LostAndFindService{

    public function __construct(Request $request,
                                LostAndFindRepository $lostAndFindRepository,
                                UserRepository $userRepository)
    {
        $this->request = $request;
        $this->lostAndFindRepository = $lostAndFindRepository;
        $this->userRepository = $userRepository;
    }
    public function create()
    {
        $user = $this->userRepository->getUser();
        $data = [
            'uid'       => $user->uid,
            'mobile'    => $this->request->mobile,
            'college_id'=> $user->college_id,
            'content'   => $this->request->content,
            'type'      => $this->request->type,
            'cat_id'    => $this->request->cat_id,
            'img'       => $this->request->img,
            'thumb'     => $this->request->thumb
        ];
        return $this->lostAndFindRepository->create($data);
    }
    public function getList($where)
    {
        return $this->lostAndFindRepository->getList($where);
    }
    public function getCats()
    {
        return $this->lostAndFindRepository->getCats();
    }
    public function delete($where)
    {
        return $this->lostAndFindRepository->delete($where);
    }

}
