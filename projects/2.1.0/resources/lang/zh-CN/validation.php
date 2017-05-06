<?php

/*
 * This file is part of Hifone.
 *
 * (c) Hifone.com <hifone@hifone.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'   => ':attribute 只能是yes、on、1或true中的一种。',
    'active_url' => ':attribute 不是一个有效的URL网址。',
    'after'      => ':attribute 必须在 :date 之后。',
    'alpha'      => ':attribute 只能包含字母。',
    'alpha_dash' => ':attribute 只能包含字母，数字和破折号。',
    'alpha_num'  => ':attribute 只允许包含字母和数字。',
    'array'      => ':attribute 必须是个数组。',
    'before'     => ':attribute 必须在 :date 之前。',
    'between'    => [
        'numeric' => ':attribute 必须在 :min 到 :max 之间。',
        'file'    => ':attribute 必须在 :min 到 :max KB 之间。',
        'string'  => ':attribute 必须在 :min 到 :max 字符之间。',
        'array'   => ':attribute 必须在 :min 到 :max 个数目之间。',
    ],
    'boolean'        => ':attribute 必须为 true（正确） 或者 false（错误）',
    'confirmed'      => ':attribute 与确认项目不匹配',
    'date'           => ':attribute 不是个有效日期',
    'date_format'    => ':attribute 不符合 :format 的格式',
    'different'      => ':attribute 和 :other 不能相同。',
    'digits'         => ':attribute 必须是  :digits  位数。',
    'digits_between' => ':attribute 必须在 :min 和 :max 位之间。',
    'email'          => ':attribute 必须是个有效的邮件地址。',
    'exists'         => '选择的 :attribute 无效。',
    'filled'         => ':attribute 字段必填。',
    'image'          => ':attribute 必须是图片。',
    'in'             => '选择的 :attribute 无效。',
    'integer'        => ':attribute 必须是整数。',
    'ip'             => ':attribute 必须是一个有效的 IP 地址。',
    'json'           => ':attribute 必须是规范的 JSON 字串。',
    'max'            => [
        'numeric' => ':attribute 不能大于 :max。',
        'file'    => ':attribute 不能大于 :max KB。',
        'string'  => ':attribute 不能大于 :max 个字符。',
        'array'   => ':attribute 不能超过 :max 个。',
    ],
    'mimes' => ':attribute 文件类型必须是 :values。',
    'min'   => [
        'numeric' => ':attribute 最少是  :min。',
        'file'    => ':attribute 至少需要 :min KB。',
        'string'  => ':attribute 最少需要 :min个字符。',
        'array'   => ':attribute 最少需要 :min 个。',
    ],
    'not_in'               => '选择的 :attribute 无效。',
    'numeric'              => ':attribute 必须是数字。',
    'regex'                => ':attribute 格式无效。',
    'required'             => ':attribute 字段必填。',
    'required_if'          => ':attribute 项在 :other 是 :value 时是必须填写的。',
    'required_unless'      => ':attribute 是必须的除非 :other 在 :values 中。',
    'required_with'        => '当含有 :values 时， :attribute 是必需的。',
    'required_with_all'    => '当含有 :values 时， :attribute 是必需的。',
    'required_without'     => '当 :values 不存在时， :attribute 是必需的。',
    'required_without_all' => '一项:values 也没有时 :attribute 区域是必填的。',
    'same'                 => ':attribute 和 :other  必需匹配。',
    'size'                 => [
        'numeric' => ':attribute 必须是  :size',
        'file'    => ':attribute 必须是 :size KB大小',
        'string'  => ':attribute 必须是 :size 个字符',
        'array'   => ':attribute 必须包含 :size 个',
    ],
    'string'   => ':attribute必须是一个字符串。',
    'timezone' => ':attribute 必须是个有效的区域。',
    'unique'   => ':attribute 已经被占用',
    'url'      => ':attribute 的格式无效',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
   //     'shop_name'	=> [
   //     	'unique'	=> '该店名已存在',
			//'required' 	=> '店名在4-30字之间',
			//'between'	=> '店名在4-30字之间',
   //     ],
   //     'shop_img' => [
   //     	'required'	=> '商店图片不能为空',
   //     ],
   //     'description' => [
   //     	'required'	=> '商店描述不能为空',
   //     ],

        //hzw
        'mobile_no' => [
            'integer' => '请检查手机号码',
            'unique' => '该手机号码已注册！',
            'exists' => '该手机号码未注册！',
        ],
        'password' => [
            'required' => '密码不能为空',
            'alpha_dash' => '密码只能包含字母和数字，以及破折号和下划线',
        ],
        'new_password' => [
            'required' => '新密码不能为空',
        ],
        'nickname' => [
            'unique' => '该昵称已被使用！',
            'alpha_dash' => '昵称只能包含字母和数字，以及破折号和下划线',
        ],
        'avatar_url' => [
            'url' => '头像无效',
            'active_url' => '头像无效',
        ],
        'gender' => [
            'in' => '性别只能是（男、女、保密）中的一种!',
        ],
        'order_id' => [
            'exists' => '任务或订单不存在',
        ],
        'end_time' => [
            'after' => '结束时间必须在开始时间和当前时间之后，请核对结束时间',
        ],
        'place' => [
            'required' => '请输入地点',
        ],
        'introduction' => [
            'required' => '请填写简介',
        ],
        'topic_type' => [
            'required' => '请填写类型',
            'exists' => '类型选择错误',
        ],
        'topic_content' => [
            'required' => '内容不能为空',
            'between' => '内容长度需小于:max个字',
        ],
        'topic_id' => [
            'required' => '请选择话题',
            'exists' => '该话题不存在',
        ],
        'comment_id' => [
            'exists' => '该评论不存在',
        ],
        'topic_comment' => [
            'required' => '评论不能为空',
            'between' => '评论长度不得超过:max个字',
        ],
        //hzw
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

     'attributes' => [
     	'avatar_url'	=> '头像',
     	'certificate'	=> '证书',
		'shop_img' 		=> '商店图片',
		'shop_name' 	=> '商店名称',
		'description'	=> '详情',
		'shop_type'		=> '商店类型',
		'shop_status'	=> '商店状态',
		'page'			=> '页数',
		'goods_id'		=> '商品',
		'goods_name'	=> '商品名称',
		'goods_img'    	=> '商品图片',
        'goods_price'	=> '商品价格',
        'goods_desc' 	=> '商品描述',
        'goods_number' 	=> '商品库存',
        'cat_id'        => '商品分类',
        'alipay_name'	=> '支付宝名称',
        'alipay'		=> '支付宝账号',
        'sms_code'		=> '短信验证码',
        'pay_password'  => '支付密码',
        'new_paypassword' => '新支付密码',
        'old_paypassword' => '旧支付密码',
        'money' 		=> '金额',
        'fee'			=> '金额',
        'service_fee' 	=> '手续费',
        'goods_fee' 	=> '物费',
        'phone'			=> '手机号码',
        'mobile'		=> '手机号码',
        'iccid'			=> 'iccid',
        'outOrderNumber'=> '常用号码',
        'telecom_phone' => '电信手机号码',
		'telecom_iccid' => 'iccid',
		'telecom_outOrderNumber' => '常用号码',
		'package_id' => '套餐',  		
		'idcard' => '身份证号码', 
		'name' => '姓名',  		  
		'major' => '专业',
		'dormitory_no' => '宿舍号',
		'student_id' => '学号',	
		'transactor' => '办理人',
		'consignee' => "收货人姓名",
        'address' => "收货地址",
    ],

];
