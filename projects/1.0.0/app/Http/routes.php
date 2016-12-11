<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/','HomeController@index');

Route::post('user/register', 'UserController@register')->name('user_register');
Route::post('user/isMobileExist', 'UserController@isMobileExist')->name('user_isMobileExist');
Route::post('user/login', 'UserController@login')->name('user_login');
Route::post('user/logout', 'UserController@logout')->name('user_logout');
Route::post('user/resetPassword', 'UserController@resetPassword')->name('user_resetPassword');
Route::post('user/changePassword', 'UserController@changePassword')->name('user_changePassword');
Route::post('user/changeUserInfo', 'UserController@changeUserInfo')->name('user_changeUserInfo');
Route::post('user/realNameAuth', 'UserController@realNameAuth')->name('user_realNameAuth');
Route::get('user/getMyInfo', 'UserController@getMyInfo')->name('user_getMyInfo');
Route::get('user/getOthersInfo', 'UserController@getOthersInfo')->name('user_getOthersInfo');
Route::post('user/uploadAvatarFile', 'UserController@uploadAvatarFile')->name('user_uploadAvatarFile');
Route::post('user/sendRegisterSMS', 'UserController@sendRegisterSMS')->name('user_sendRegisterSMS');
Route::post('user/sendResetPasswordSMS', 'UserController@sendResetPasswordSMS')->name('user_sendResetPasswordSMS');
Route::get('user/getWallet','UserController@getWallet')->name('user_getWallet');
Route::get('user/getAlipayInfo','UserController@getAlipayInfo')->name('user_getAlipayInfo');
Route::get('user/walletAccount','UserController@walletAccount')->name('user_walletAccount');
Route::post('user/bindAlipay','UserController@bindAlipay')->name('user_bindAlipay');
Route::post('user/changeAlipay','UserController@changeAlipay')->name('user_changeAlipay');
Route::post('user/setPayPassword','UserController@setPayPassword')->name('user_setPayPassword');
Route::post('user/changePayPassword','UserController@changePayPassword')->name('user_changePayPassword');
Route::get('user/sendChangeAliSMS','UserController@sendChangeAliSMS')->name('user_sendChangeAliSMS');
Route::post('user/resetPayPassword','UserController@resetPayPassword')->name('user_resetPayPassword');
Route::get('user/sendResetPayPasswordSMS','UserController@sendResetPayPasswordSMS')->name('user_sendResetPayPasswordSMS');
Route::post('user/withdrawalsApply','UserController@withdrawalsApply')->name('user_withdrawalsApply');
Route::get('user/getVerifyImageURL','UserController@getVerifyImageURL')->name('user_getVerifyImageURL');
Route::post('user/realNameAuthUploadImg','UserController@realNameAuthUploadImg');
Route::post('user/h5RealNameAuth','UserController@h5RealNameAuth');
Route::get('user/getMobileBytoken','UserController@getMobileBytoken');
Route::post('user/uploadImage','UserController@uploadImage');

Route::get('order/getOrderList', 'OrderController@getOrderList')->name('order_getOrderList');
Route::get('order/getOrder', 'OrderController@getOrder')->name('order_getOrder');
Route::get('order/getOrderByToken', 'OrderController@getOrderByToken');
Route::post('order/createOrder', 'OrderController@createOrder')->name('order_createOrder');
Route::post('order/claimOrder', 'OrderController@claimOrder')->name('order_claimOrder');
Route::get('order/getMyOrder', 'OrderController@getMyOrder')->name('order_getMyOrder');
Route::get('order/getMyWork', 'OrderController@getMyWork')->name('order_getMyWork');
Route::post('order/askCancel', 'OrderController@askCancel')->name('order_askCancel');
Route::post('order/agreeCancel', 'OrderController@agreeCancel')->name('order_agreeCancel');
Route::get('order/getCourierList', 'OrderController@getCourierList')->name('order_getCourierList');
Route::post('order/chooseCourier', 'OrderController@chooseCourier')->name('order_chooseCourier');
Route::post('order/finishWork', 'OrderController@finishWork')->name('order_finishWork');
Route::post('order/confirmFinishWork', 'OrderController@confirmFinishWork')->name('order_confirmFinishWork');
Route::get('order/orderAgreement', 'OrderController@orderAgreement')->name('order_orderAgreement');
Route::get('order/getRecommendOrders', 'OrderController@getRecommendOrders');
Route::get('order/autoFinishWork', 'ScheduleController@autoFinishWork');
Route::get('order/remindOrder', 'OrderController@remindOrder');

Route::post('alipay/alipayAppNotify','AlipayController@alipayAppNotify');
Route::post('alipay/alipayAppReturn','AlipayController@alipayAppReturn');
Route::post('alipay/alipayWapNotify','AlipayController@alipayWapNotify');
Route::post('alipay/alipayTelecomWapNotify','AlipayController@alipayTelecomWapNotify');

Route::post('association/join', 'AssociationController@join')->name('association_join');
Route::post('association/uploadJoinFiles', 'AssociationController@uploadJoinFiles')->name('association_uploadJoinFiles');
Route::get('association/getActivityList', 'AssociationController@getActivityList')->name('association_getActivityList');
Route::get('association/getActivity', 'AssociationController@getActivity')->name('association_getActivity');
Route::get('association/getAssociationActivity', 'AssociationController@getAssociationActivity')->name('association_getAssociationActivity');
Route::get('association/getInformationList', 'AssociationController@getInformationList')->name('association_getInformationList');
Route::get('association/getInfomation', 'AssociationController@getInfomation')->name('association_getInfomation');
Route::post('association/createMessage', 'AssociationController@createMessage')->name('association_createMessage');
Route::post('association/createInformation', 'AssociationController@createInformation')->name('association_createInformation');
Route::post('association/uploadInformationImageFiles', 'AssociationController@uploadInformationImageFiles')->name('association_uploadInformationImageFiles');
Route::post('association/createActivity', 'AssociationController@createActivity')->name('association_createActivity');
Route::post('association/uploadActivityImageFiles', 'AssociationController@uploadActivityImageFiles')->name('association_uploadActivityImageFiles');
Route::post('association/setProfile', 'AssociationController@setProfile')->name('association_setProfile');
Route::post('association/manageMember', 'AssociationController@manageMember')->name('association_manageMember');
Route::get('association/getHotActivities', 'AssociationController@getHotActivities')->name('association_getHotActivities');
Route::get('association/getHotInformation', 'AssociationController@getHotInformation')->name('association_getHotInformation');
Route::get('association/getAssociations', 'AssociationController@getAssociations')->name('association_getAssociations');
Route::get('association/getMyAssociations', 'AssociationController@getMyAssociations')->name('association_getMyAssociations');
Route::get('association/getAssociationsDetails', 'AssociationController@getAssociationsDetails')->name('association_getAssociationsDetails');
Route::get('association/getAssociationNotice', 'AssociationController@getAssociationNotice')->name('association_getAssociationNotice');
Route::get('association/getAssociationMember', 'AssociationController@getAssociationMember')->name('association_getAssociationMember');
Route::post('association/joinAssociationMember', 'AssociationController@joinAssociationMember')->name('association_joinAssociationMember');
Route::get('association/updateMemberLevel', 'AssociationController@updateMemberLevel')->name('association_updateMemberLevel');
Route::get('association/deleteMember', 'AssociationController@deleteMember')->name('association_deleteMember');
Route::get('association/releaseNotice', 'AssociationController@releaseNotice')->name('association_releaseNotice');
Route::get('association/quitAssociation', 'AssociationController@quitAssociation')->name('association_quitAssociation');
Route::get('association/checkMember', 'AssociationController@checkMember')->name('association_checkMember');
Route::get('association/checkMemberList', 'AssociationController@checkMemberList')->name('association_checkMemberList');
Route::get('association/checkNewNotice', 'AssociationController@checkNewNotice')->name('association_checkNewNotice');
Route::get('association/deleteActivity', 'AssociationController@deleteActivity');

Route::get('message/getMessageList', 'MessageController@getMessageList')->name('other_getMessageList');

Route::post('verify', 'VerifyController@verify');

Route::get('home/getADList', 'HomeController@getADList');
Route::get('home/getExtra', 'HomeController@getExtra');


Route::get('topic/getTopic', 'TopicController@getTopic')->name('topic_getTopic');
Route::get('topic/getMyTopic', 'TopicController@getMyTopic')->name('topic_getMyTopic');
Route::get('topic/getMyComment', 'TopicController@getMyComment')->name('topic_getMyComment');
Route::get('topic/getTopicList', 'TopicController@getTopicList')->name('topic_getTopicList');
Route::post('topic/createTopic', 'TopicController@createTopic')->name('topic_createTopic');
Route::post('topic/deleteTopic', 'TopicController@deleteTopic')->name('topic_deleteTopic');
Route::get('topic/getTopicCommentsList', 'TopicController@getTopicCommentsList')->name('topic_getTopicCommentsList');
Route::post('topic/comment', 'TopicController@comment')->name('topic_comment');
Route::post('topic/deleteComment', 'TopicController@deleteComment')->name('topic_deleteComment');
Route::post('topic/undoDeleteTopic', 'TopicController@undoDeleteTopic')->name('topic_undoDeleteTopic');
Route::post('topic/thumbUp', 'TopicController@thumbUp')->name('topic_thumbUp');
Route::post('topic/uploadImage', 'TopicController@uploadImage')->name('topic_uploadImage');
Route::post('topic/search', 'TopicController@search')->name('topic_search');


//Route::get('shop/getShopList', 'ShopController@getShopList')->name('login');
//Route::get('shop/getShopGoodList', 'ShopController@getShopGoodList')->name('login');
//Route::post('shop/good/putOn', 'ShopController@putOn')->name('login');
//Route::post('shop/good/pullOff', 'ShopController@pullOff')->name('login');
//Route::post('shop/order', 'ShopController@order')->name('login');
//Route::post('shop/favourite', 'ShopController@favourite')->name('login');


Route::post('shop/store', 'ShopController@store')->name('shop_storeShop');
Route::get('shop/shops','ShopController@getShopList')->name('shop_getShopList');

Route::post('goods/store','GoodsController@store')->name('shop_storeGoods');
Route::get('goods/shopGoodses','GoodsController@getShopGoodses')->name('shop_getShopGoodses');
Route::get('goods/goodses','GoodsController@getGoodses')->name('shop_getGoodses');

Route::post('cart/store','CartController@store')->name('shop_storeCart');
Route::get('cart/carts','CartController@getCarts')->name('shop_getCarts');
Route::get('cart/shopCarts','CartController@getShopCarts')->name('shop_getShopCarts');
Route::post('cart/updateCartGoodsNumber','CartController@updateCartGoodsNumber')->name('shop_updateCartGoodsNumber');
Route::post('cart/destroy','CartController@destroy');
Route::post('cart/destroyAll','CartController@destroyAll');
Route::get('cart/getTotal','CartController@getTotal')->name('shop_getTotalCart');

Route::get('orderInfo/index','OrderInfoController@index')->name('shop_indexOrderInfoController');
Route::get('orderInfo/create','OrderInfoController@create');
Route::get('orderInfo/show','OrderInfoController@show');
Route::post('orderInfo/store', 'OrderInfoController@store')->name('shop_storeOrderInfo');
Route::get('orderInfo/orders','OrderInfoController@orders')->name('shop_OrderInfoa');

Route::get('userAddress/index','UserAddressController@index');
Route::get('userAddress/show','UserAddressController@show');
Route::get('userAddress/getDefault','UserAddressController@getDefault');
Route::post('userAddress/store','UserAddressController@store');
Route::post('userAddress/update','UserAddressController@update');
Route::post('userAddress/destroy','UserAddressController@destroy');
//Route::get('/alipay', 'AlipayController@index')->name('login');
//Route::post('/alipay/mobilePay', 'AlipayController@mobilePay')->name('login');
//Route::post('/alipay/alipayNotify', 'AlipayController@alipayNotify')->name('login');


Route::post('/integral/integral_list', 'IntegralController@integral_list')->name('other_integralList');
Route::post('/integral/integral_level', 'IntegralController@integral_level')->name('other_integralLevel');
Route::post('/integral/integral_explain', 'IntegralController@integral_explain')->name('other_integralExplain');
Route::post('/integral/integral_share', 'IntegralController@integral_share')->name('other_integralShare');
Route::post('/accusation', 'ReportController@report')->name('other_accusation');
Route::post('/reportCrash', 'ReportController@reportCrash')->name('other_reportCrash');

Route::post('/feedback', 'FeedbackController@feedback')->name('other_feedback');

Route::post('telecom/queryRealName','TelecomController@queryRealName')->name('telecom_queryRealName');
Route::get('telecom/telecomPackage','TelecomController@telecomPackage')->name('telecom_telecomPackage');
Route::post('telecom/telecomPackageStore','TelecomController@telecomPackageStore')->name('telecom_telecomPackageStore');
Route::get('telecom/getTelecomOrders','TelecomController@getTelecomOrders')->name('telecom_getTelecomOrders');
Route::get('telecom/hasTelecomOrder','TelecomController@hasTelecomOrder')->name('telecom_hasTelecomOrder');
Route::get('telecom/getTransactorTelecomOrders','TelecomController@getTransactorTelecomOrders')->name('telecom_getTransactorTelecomOrders');
Route::get('telecom/getTelecomOrdersCount','TelecomController@getTelecomOrdersCount')->name('telecom_getTelecomOrdersCount');
Route::get('telecom/autoCheckRealName', 'ScheduleController@autoCheckRealName');

Route::get('version','VersionController@index')->name('other_version');
Route::get('api','ApiController@getApi');
Route::get('getCommonData','ApiController@getCommonData');

Route::get('paper','PaperController@index');

Route::get('getWebUrl','GetWebUrlController@index');

Route::post('network/checkPassword', 'ReportNetworkFailureController@checkPassword');
Route::get('network/checkByToken', 'ReportNetworkFailureController@checkByToken');
Route::post('network/reportByToken', 'ReportNetworkFailureController@reportByToken');
Route::get('network/reportList', 'ReportNetworkFailureController@reportList');

Route::get('getNewTopicNotifications','NotificationController@getNewTopicNotifications');
Route::get('hasNewTopicNotification','NotificationController@hasNewTopicNotification');

Route::get('chickenSoupList', 'ChickenSoupController@chickenSoupList');
Route::get('chickenSoupDetail', 'ChickenSoupController@chickenSoupDetail');
