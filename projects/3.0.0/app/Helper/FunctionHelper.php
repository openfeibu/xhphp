<?php
use App\User;

if (!function_exists('escape_content')) {
	function escape_content($content)
	{
		if ($content == base64_encode(base64_decode($content))){
			if(preg_match("/^\d*$/",$content)){
				return $content;
			}elseif(preg_match("/^[a-z]+$/",$content)){
				return $content;
			}else{
				return base64_decode($content);
			}
		}
		return $content;
	}
}
if (!function_exists('round_route')) {
	function round_route()
	{
		return [
			'order_getOrderList',
			'topic_getTopicList',
			'topic_getTopics',
			'topic_getTopicCommentsList',
			'shop_getShopList',
			'shop_getShopGoodses',
			'order_getOrderDetail',
			'association_getAssociations',
			'drivingSchool_getDrivingSchool',
			'index',
		];
	}
}
if (!function_exists('dtime')) {
	function dtime()
	{
		return date('Y-m-d H:i:s');
	}
}
if (!function_exists('seller_check_refund_order_info')) {
	function seller_check_refund_order_info($pay_status,$shipping_status,$order_status)
	{
		if($pay_status !=1 || $shipping_status !=0 || $order_status !=3 ){
			return false;
		}
		return true;
	}
}
if (!function_exists('check_refund_order_info')) {
	function check_refund_order_info($pay_status,$shipping_status,$order_status)
	{
		if($pay_status !=1 || $shipping_status !=0 || $order_status >=2 ){
			return false;
		}
		return true;
	}
}
if (!function_exists('check_confirm_order_info')) {
	function check_confirm_order_info ($pay_status,$shipping_status,$order_status)
	{
		if($pay_status !=1 || $shipping_status !=1 || $order_status >=2 ){
			return false;
		}
		return true;
	}
}
if (!function_exists('seller_check_Shipping_order_info')) {
	function seller_check_Shipping_order_info ($pay_status,$shipping_status,$order_status)
	{
		if($pay_status !=1 || $shipping_status !=0 || $order_status >=2 ){
			return false;
		}
		return true;
	}
}

if (!function_exists('sellerHandle')) {
	function sellerHandle($shop){
		if(in_array($shop->shop_status,[4,0,2]))
		{
			throw new \App\Exceptions\Custom\OutputServerMessageException(trans('common.shop_status_validator.'.$shop->shop_status));
		}
	}
}
if (!function_exists('buyerHandle')) {
	function buyerHandle($shop){
		if($shop->shop_status != 1)
		{
			throw new \App\Exceptions\Custom\OutputServerMessageException(trans('common.shop_status_validator.'.$shop->shop_status));
		}
		$time = strtotime(date('H:i:s',time()));
		if($time < strtotime($shop->open_time) || $time > strtotime($shop->close_time)){
			throw new \App\Exceptions\Custom\OutputServerMessageException('店铺休息中');
		}
	}
}

//取货码
if(!function_exists('get_pick_code'))
{
	function get_pick_code()
	{
		return rand(100000,999999);
	}
}
/*
获取店铺应得价格
*/
if(!function_exists('get_receivable'))
{
	function get_receivable($type,$order_info)
	{
		if($type == 1)
		{
			//学生店铺
			$receivable = $order_info->total_fee;
		}
		if($type == 2)
		{
			//商家
			$receivable = $order_info->total_fee - $order_info->seller_shipping_fee - $order_info->shipping_fee;
		}
		return $receivable;
	}
}
if(!function_exists('get_order_receivable'))
{
	function get_order_receivable()
	{

	}
}
/*
抽奖概率
*/
if(!function_exists('get_rand'))
{
	function get_rand($proArr) {
	    $result = '';

	    //概率数组的总概率精度
	    $proSum = array_sum($proArr);

	    //概率数组循环
	    foreach ($proArr as $key => $proCur) {
	        $randNum = mt_rand(1, $proSum);
	        if ($randNum <= $proCur) {
	            $result = $key;
	            break;
	        } else {
	            $proSum -= $proCur;
	        }
	    }
	    unset ($proArr);

	    return $result;
	}
}

if(!function_exists('get_nickname'))
{
	function get_nickname()
	{
		$nicheng_tou=array('快乐的','冷静的','醉熏的','潇洒的','糊涂的','积极的','冷酷的','深情的','粗暴的','温柔的','可爱的','愉快的','义气的','认真的','威武的','帅气的','传统的','潇洒的','漂亮的','自然的','专一的','听话的','昏睡的','狂野的','等待的','搞怪的','幽默的','魁梧的','活泼的','开心的','高兴的','超帅的','留胡子的','坦率的','直率的','轻松的','痴情的','完美的','精明的','无聊的','有魅力的','丰富的','繁荣的','饱满的','炙热的','暴躁的','碧蓝的','俊逸的','英勇的','健忘的','故意的','无心的','土豪的','朴实的','兴奋的','幸福的','淡定的','不安的','阔达的','孤独的','独特的','疯狂的','时尚的','落后的','风趣的','忧伤的','大胆的','爱笑的','矮小的','健康的','合适的','玩命的','沉默的','斯文的','香蕉','苹果','鲤鱼','鳗鱼','任性的','细心的','粗心的','大意的','甜甜的','酷酷的','健壮的','英俊的','霸气的','阳光的','默默的','大力的','孝顺的','忧虑的','着急的','紧张的','善良的','凶狠的','害怕的','重要的','危机的','欢喜的','欣慰的','满意的','跳跃的','诚心的','称心的','如意的','怡然的','娇气的','无奈的','无语的','激动的','愤怒的','美好的','感动的','激情的','激昂的','震动的','虚拟的','超级的','寒冷的','精明的','明理的','犹豫的','忧郁的','寂寞的','奋斗的','勤奋的','现代的','过时的','稳重的','热情的','含蓄的','开放的','无辜的','多情的','纯真的','拉长的','热心的','从容的','体贴的','风中的','曾经的','追寻的','儒雅的','优雅的','开朗的','外向的','内向的','清爽的','文艺的','长情的','平常的','单身的','伶俐的','高大的','懦弱的','柔弱的','爱笑的','乐观的','耍酷的','酷炫的','神勇的','年轻的','唠叨的','瘦瘦的','无情的','包容的','顺心的','畅快的','舒适的','靓丽的','负责的','背后的','简单的','谦让的','彩色的','缥缈的','欢呼的','生动的','复杂的','慈祥的','仁爱的','魔幻的','虚幻的','淡然的','受伤的','雪白的','高高的','糟糕的','顺利的','闪闪的','羞涩的','缓慢的','迅速的','优秀的','聪明的','含糊的','俏皮的','淡淡的','坚强的','平淡的','欣喜的','能干的','灵巧的','友好的','机智的','机灵的','正直的','谨慎的','俭朴的','殷勤的','虚心的','辛勤的','自觉的','无私的','无限的','踏实的','老实的','现实的','可靠的','务实的','拼搏的','个性的','粗犷的','活力的','成就的','勤劳的','单纯的','落寞的','朴素的','悲凉的','忧心的','洁净的','清秀的','自由的','小巧的','单薄的','贪玩的','刻苦的','干净的','壮观的','和谐的','文静的','调皮的','害羞的','安详的','自信的','端庄的','坚定的','美满的','舒心的','温暖的','专注的','勤恳的','美丽的','腼腆的','优美的','甜美的','甜蜜的','整齐的','动人的','典雅的','尊敬的','舒服的','妩媚的','秀丽的','喜悦的','甜美的','彪壮的','强健的','大方的','俊秀的','聪慧的','迷人的','陶醉的','悦耳的','动听的','明亮的','结实的','魁梧的','标致的','清脆的','敏感的','光亮的','大气的','老迟到的','知性的','冷傲的','呆萌的','野性的','隐形的','笑点低的','微笑的','笨笨的','难过的','沉静的','火星上的','失眠的','安静的','纯情的','要减肥的','迷路的','烂漫的','哭泣的','贤惠的','苗条的','温婉的','发嗲的','会撒娇的','贪玩的','执着的','眯眯眼的','花痴的','想人陪的','眼睛大的','高贵的','傲娇的','心灵美的','爱撒娇的','细腻的','天真的','怕黑的','感性的','飘逸的','怕孤独的','忐忑的','高挑的','傻傻的','冷艳的','爱听歌的','还单身的','怕孤单的','懵懂的');

		$nicheng_wei=array('嚓茶','凉面','便当','毛豆','花生','可乐','灯泡','哈密瓜','野狼','背包','眼神','缘分','雪碧','人生','牛排','蚂蚁','飞鸟','灰狼','斑马','汉堡','悟空','巨人','绿茶','自行车','保温杯','大碗','墨镜','魔镜','煎饼','月饼','月亮','星星','芝麻','啤酒','玫瑰','大叔','小伙','哈密瓜，数据线','太阳','树叶','芹菜','黄蜂','蜜粉','蜜蜂','信封','西装','外套','裙子','大象','猫咪','母鸡','路灯','蓝天','白云','星月','彩虹','微笑','摩托','板栗','高山','大地','大树','电灯胆','砖头','楼房','水池','鸡翅','蜻蜓','红牛','咖啡','机器猫','枕头','大船','诺言','钢笔','刺猬','天空','飞机','大炮','冬天','洋葱','春天','夏天','秋天','冬日','航空','毛衣','豌豆','黑米','玉米','眼睛','老鼠','白羊','帅哥','美女','季节','鲜花','服饰','裙子','白开水','秀发','大山','火车','汽车','歌曲','舞蹈','老师','导师','方盒','大米','麦片','水杯','水壶','手套','鞋子','自行车','鼠标','手机','电脑','书本','奇迹','身影','香烟','夕阳','台灯','宝贝','未来','皮带','钥匙','心锁','故事','花瓣','滑板','画笔','画板','学姐','店员','电源','饼干','宝马','过客','大白','时光','石头','钻石','河马','犀牛','西牛','绿草','抽屉','柜子','往事','寒风','路人','橘子','耳机','鸵鸟','朋友','苗条','铅笔','钢笔','硬币','热狗','大侠','御姐','萝莉','毛巾','期待','盼望','白昼','黑夜','大门','黑裤','钢铁侠','哑铃','板凳','枫叶','荷花','乌龟','仙人掌','衬衫','大神','草丛','早晨','心情','茉莉','流沙','蜗牛','战斗机','冥王星','猎豹','棒球','篮球','乐曲','电话','网络','世界','中心','鱼','鸡','狗','老虎','鸭子','雨','羽毛','翅膀','外套','火','丝袜','书包','钢笔','冷风','八宝粥','烤鸡','大雁','音响','招牌','胡萝卜','冰棍','帽子','菠萝','蛋挞','香水','泥猴桃','吐司','溪流','黄豆','樱桃','小鸽子','小蝴蝶','爆米花','花卷','小鸭子','小海豚','日记本','小熊猫','小懒猪','小懒虫','荔枝','镜子','曲奇','金针菇','小松鼠','小虾米','酒窝','紫菜','金鱼','柚子','果汁','百褶裙','项链','帆布鞋','火龙果','奇异果','煎蛋','唇彩','小土豆','高跟鞋','戒指','雪糕','睫毛','铃铛','手链','香氛','红酒','月光','酸奶','银耳汤','咖啡豆','小蜜蜂','小蚂蚁','蜡烛','棉花糖','向日葵','水蜜桃','小蝴蝶','小刺猬','小丸子','指甲油','康乃馨','糖豆','薯片','口红','超短裙','乌冬面','冰淇淋','棒棒糖','长颈鹿','豆芽','发箍','发卡','发夹','发带','铃铛','小馒头','小笼包','小甜瓜','冬瓜','香菇','小兔子','含羞草','短靴','睫毛膏','小蘑菇','跳跳糖','小白菜','草莓','柠檬','月饼','百合','纸鹤','小天鹅','云朵','芒果','面包','海燕','小猫咪','龙猫','唇膏','鞋垫','羊','黑猫','白猫','万宝路','金毛','山水','音响');

		$tou_num=rand(0,331);

		$wei_num=rand(0,325);

		$nickname = $nicheng_tou[$tou_num].$nicheng_wei[$wei_num];

		$user = User::where(['nickname' => $nickname])->first(['uid']);

		if($user)
		{
			$user_last_uid = User::orderBy('uid','desc')->value('uid');
			$uid = $user_last_uid + 1;
			$nickname = $nickname.$uid;
		}
		return $nickname; //输出生成的昵称


	}
}
if(!function_exists('get_device_type'))
{
	function get_device_type()
	{
		//全部变成小写字母
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if(strpos($agent,'iphone') || strpos($agent,'ipad'))
		{
			$type ='ios';
		}
		else if(strpos($agent,'android'))
		{
			$type ='and';
		}
		else if (strpos($agent,'MicroMessenger')) {
			$type = 'wechat';
		}
		else{
			$type = 'web';
		}
		return type;
	}
}
if(!function_exists('handle_alipay_parameter'))
{
	function handle_alipay_parameter($parameter)
	{
		foreach($parameter as $key => $value)
		{
			$parameter[$key] = "\"".$value."\"";
		}
		return $parameter;
	}
}
/**
 * 友好的时间显示
 *
 * @param  int    $sTime 待显示的时间
 * @param  string $type  类型. normal | mohu | full | ymd | other
 * @param  string $alt   已失效
 * @return string
 */
if(!function_exists('friendlyDate')){
	function friendlyDate($sTime, $type = 'normal', $alt = 'false')
	{
	    if (!$sTime) {
	        return '';
	    }
	//	var_dump($sTime);exit;
	    $sTime = strtotime($sTime);
	    //sTime=源时间，cTime=当前时间，dTime=时间差
	    $cTime = time();
	    $dTime = $cTime - $sTime;
	    $dDay = intval(date('z', $cTime)) - intval(date('z', $sTime));
	    //$dDay     =   intval($dTime/3600/24);
	    $dYear = intval(date('Y', $cTime)) - intval(date('Y', $sTime));
	    //normal：n秒前，n分钟前，n小时前，日期
	    if ($type == 'normal') {
	        if ($dTime < 60) {
	            if ($dTime < 10) {
	                return '刚刚';    //by yangjs
	            } else {
	                return intval(floor($dTime / 10) * 10).'秒前';
	            }
	        } elseif ($dTime < 3600) {
	            return intval($dTime / 60).'分钟前';
	            //今天的数据.年份相同.日期相同.
	        } elseif ($dYear == 0 && $dDay == 0) {
	            //return intval($dTime/3600)."小时前";
	            return '今天'.date('H:i', $sTime);
	        } elseif ($dYear == 0) {
	            return date('m月d日 H:i', $sTime);
	        } else {
	            return date('Y-m-d H:i', $sTime);
	        }
	    } elseif ($type == 'mohu') {
	        if ($dTime < 60) {
	            return $dTime.'秒前';
	        } elseif ($dTime < 3600) {
	            return intval($dTime / 60).'分钟前';
	        } elseif ($dTime >= 3600 && $dDay == 0) {
	            return intval($dTime / 3600).'小时前';
	        } elseif ($dDay > 0 && $dDay <= 7) {
	            return intval($dDay).'天前';
	        } elseif ($dDay > 7 &&  $dDay <= 30) {
	            return intval($dDay / 7).'周前';
	        } elseif ($dDay > 30) {
	            return intval($dDay / 30).'个月前';
	        }
	        //full: Y-m-d , H:i:s
	    } elseif ($type == 'full') {
	        return date('Y-m-d , H:i:s', $sTime);
	    } elseif ($type == 'ymd') {
	        return date('Y-m-d', $sTime);
	    } else {
	        if ($dTime < 60) {
	            return $dTime.'秒前';
	        } elseif ($dTime < 3600) {
	            return intval($dTime / 60).'分钟前';
	        } elseif ($dTime >= 3600 && $dDay == 0) {
	            return intval($dTime / 3600).'小时前';
	        } elseif ($dYear == 0) {
	            return date('Y-m-d H:i:s', $sTime);
	        } else {
	            return date('Y-m-d H:i:s', $sTime);
	        }
	    }
	}
}
/**
 * 获取字符串的长度
 *
 * 计算时, 汉字或全角字符占1个长度, 英文字符占0.5个长度
 *
 * @param  string $str
 * @param  bool   $filter 是否过滤html标签
 * @return int    字符串的长度
 */
if (!function_exists('get_str_length')) {

	function get_str_length($str, $filter = false)
	{
	    if ($filter) {
	        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
	        $str = strip_tags($str);
	    }

	    return (strlen($str) + mb_strlen($str, 'UTF8')) / 4;
	}
}
/*图片处理*/
if (!function_exists('handle_img')) {
	function handle_img($img)
	{
		return array_filter(explode(',',$img));
	}

}
/* 用户信息处理 */
if (!function_exists('handle_user')) {
	function handle_user($user)
	{
		if(isset($user->realname) && !empty($user->realname))
		{
			$user->realname = handle_name($user->realname);
		}
		if(isset($user->id_number) && !empty($user->id_number))
		{
			$user->id_number = handle_idcard($user->id_number);
		}
		return $user;
	}

}
/*名字处理*/
if (!function_exists('handle_name')) {
	function handle_name($str)
	{
		//判断是否包含中文字符
		if(preg_match("/[\x{4e00}-\x{9fa5}]+/u", $str)) {
		    //按照中文字符计算长度
		    $len = mb_strlen($str, 'UTF-8');
		    //echo '中文';
		    if($len >= 3){
		        //三个字符或三个字符以上掐头取尾，中间用*代替
		        $str = mb_substr($str, 0, 1, 'UTF-8') . '*' . mb_substr($str, -1, 1, 'UTF-8');
		    } elseif($len == 2) {
		        //两个字符
		        $str = mb_substr($str, 0, 1, 'UTF-8') . '*';
		    }
		} else {
		    //按照英文字串计算长度
		    $len = strlen($str);
		    //echo 'English';
		    if($len >= 3) {
		        //三个字符或三个字符以上掐头取尾，中间用*代替
		        $str = substr($str, 0, 1) . '*' . substr($str, -1);
		    } elseif($len == 2) {
		        //两个字符
		        $str = substr($str2, 0, 1) . '*';
		    }
		}
		return $str;
	}
}
/*身份证处理*/
if (!function_exists('handle_idcard')) {
	function handle_idcard($id_number)
	{
		return strlen($id_number) == 15 ? substr_replace($id_number,"******",6,6) : (strlen($id_number)==18 ? substr_replace($id_number,"******",8,6) : '');
	}
}
