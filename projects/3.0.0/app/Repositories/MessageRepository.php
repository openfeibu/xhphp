<?php

namespace App\Repositories;

use DB;
use App\Message;
use Illuminate\Http\Request;

class MessageRepository
{

	protected $request;

	function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * 获取纸条列表
	 */
	public function getMessageList(array $param)
	{
		return Message::select(DB::raw("message.mid, message.content, message.created_at, message.type, message.name"))
                      ->whereIn('message.uid_receiver', [$param['uid'], '1'])
                      ->whereNull('message.deleted_at')
                      ->orderBy('message.created_at', 'desc')
                      ->skip(10 * $param['page'] - 10)
                      ->take($param['num'] ?: 10)
                      ->get();
	}


	/**
	 * 创建纸条信息
	 */
	public function message2AllMember(array $param)
	{
		config(['database.default' => 'write']);
        DB::table('message')->insert($param);
		config(['database.default' => 'read']);
	}

	/**
	 * 创建纸条
	 */
	public function createMessageSingleOne($uid_receiver, $aid_sender, $type, $name, $content)
	{
		$msg = new Message;
		$msg->setConnection('write');
		$msg->uid_receiver = $uid_receiver;
		$msg->aid_sender = $aid_sender;
		$msg->type = $type;
		$msg->name = $name;
		$msg->content = $content;
		$msg->save();
	}
}
