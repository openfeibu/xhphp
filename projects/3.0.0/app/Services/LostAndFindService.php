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

            'img'       => isset($this->request->img) ? $this->request->img : '',
            'thumb'     => isset($this->request->thumb) ? $this->request->thumb : ''
        ];
        return $this->lostAndFindRepository->create($data);
    }
    public function getList($where)
    {
        $losses = $this->lostAndFindRepository->getList($where);
        foreach ($losses as $key => $loss) {
            $loss->url = config('app.web_url').'/LostAndFound/Lf-detail.html?loss_id='.$loss->loss_id;
            $loss->type_desc = trans('common.loss_type.'.$loss->type);
        }
        return $losses;
    }
    public function getCats()
    {
        return $this->lostAndFindRepository->getCats();
    }
    public function getLoss($where)
    {
        return $this->lostAndFindRepository->getLoss($where);
    }
    public function delete($where)
    {
        return $this->lostAndFindRepository->delete($where);
    }
    public function getUsers($where)
    {
        return $this->lostAndFindRepository->getUsers($where);
    }
}
