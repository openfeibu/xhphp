<?php

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

    'accepted'             => ':attribute只能是yes、on、1或true中的一种',
    'active_url'           => ':attribute不是有效的链接',
    'after'                => ':attribute必须是:date后的一个值',
    'alpha'                => ':attribute必须是字母',
    'alpha_dash'           => ':attribute只能包含字母和数字，以及破折号和下划线',
    'alpha_num'            => ':attribute只能包含字母和数字',
    'array'                => ':attribute只能是数组',
    'before'               => ':attribute必须是:date前的一个值',
    'between'              => [
        'numeric' => ':attribute必须在:min 到 :max之间',
        'file'    => ':attribute必须在:min 到 :max kilobytes之间',
        'string'  => ':attribute必须在:min 到 :max 个字符之间',
        'array'   => ':attribute中的键值对个数必须在:min 到 :max 之间',
    ],
    'boolean'              => ':attribute只能是true、false、1、0、"1"、和"0"中的一个',
    'confirmed'            => ':attribute缺少对应的:attribute _confirmation',
    'date'                 => ':attribute不是有效的日期',
    'date_format'          => ':attribute不是正确的日期格式:format.',
    'different'            => ':attribute必须与:other不同',
    'digits'               => ':attribute必须是数字且长度为:digits',
    'digits_between'       => ':attribute必须是数字且长度介于:min和:max之间',
    'email'                => ':attribute不是有效的邮箱地址',
    'exists'               => ':attribute不存在于数据库中',
    'filled'               => ':attribute field is required.',
    'image'                => ':attribute必须是图片',
    'in'                   => ':attribute不在规定的范围内',
    'integer'              => ':attribute的类型必须是int',
    'ip'                   => ':attribute必须是有效的IP地址',
    'json'                 => ':attribute的类型必须是JSON.',
    'max'                  => [
        'numeric' => ':attribute必须小于或等于:max.',
        'file'    => ':attribute必须小于或等于:max kilobytes',
        'string'  => ':attribute必须小于或等于:max个字符',
        'array'   => ':attribute中的键值对个数不能超过:max个',
    ],
    'mimes'                => ':attribute必须是以下的一种: :values',
    'min'                  => [
        'numeric' => ':attribute必须大于或等于 :min.',
        'file'    => ':attribute必须大于或等于 :min kilobytes.',
        'string'  => ':attribute必须大于或等于 :min 个字符',
        'array'   => ':attribute中的键值对个数必须大于或等于 :min 个',
    ],
    'not_in'               => ':attribute的值非法',
    'numeric'              => ':attribute必须是数值',
    'regex'                => ':attribute格式无效',
    'required'             => '缺少参数:attribute',
    'required_if'          => ':attribute field is required when :other is :value.',
    'required_unless'      => ':attribute field is required unless :other is in :values.',
    'required_with'        => ':attribute field is required when :values is present.',
    'required_with_all'    => ':attribute field is required when :values is present.',
    'required_without'     => ':attribute field is required when :values is not present.',
    'required_without_all' => ':attribute field is required when none of :values are present.',
    'same'                 => ':attribute and :other must match.',
    'size'                 => [
        'numeric' => ':attribute must be :size.',
        'file'    => ':attribute must be :size kilobytes.',
        'string'  => ':attribute must be :size characters.',
        'array'   => ':attribute must contain :size items.',
    ],
    'string'               => ':attribute必须是字符串',
    'timezone'             => ':attribute必须是有效的时区标识',
    'unique'               => ':attribute已存在',
    'url'                  => ':attribute必须是有效的URL',

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
            'exists' => '任务不存在',
        ],
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

    'attributes' => [],

];
