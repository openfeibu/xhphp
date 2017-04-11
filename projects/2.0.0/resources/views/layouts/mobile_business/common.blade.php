<!DOCTYPE html>
<html lang="en" >
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no,minimal-ui">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="default" />
	<meta content="telephone=no" name="format-detection" />
  <!-- uc强制竖屏 -->
  <meta name="screen-orientation" content="portrait">
  <!-- UC应用模式 -->
  <meta name="browsermode" content="application">
  <!-- QQ强制竖屏 -->
  <meta name="x5-orientation" content="portrait">
  <!-- QQ应用模式 -->
  <meta name="x5-page-mode" content="app">
  <!-- UC禁止放大字体 -->
  <meta name="wap-font-scale" content="no">
	<title>校汇Plus | 校汇</title>
  <meta name="Keywords" content="校汇,广东农工商职业技术学院,AIB,农工商,能赚钱,大学生,大学生创业,大学生校园,大学生校园社团,大学生校园快递,大学生周边生活" />
  <meta name="Description" content="校汇是一个以校园任务为核心的移动互联网综合服务平台，立足于校园，致力打造完整的校园生态辐射圈。校汇一直专注于大学生活、社团文化、校园资讯、学生互动" />
  <link rel="apple-touch-icon-precomposed" href="./icon.png" />
  <link rel="shortcut icon" href="./icon.png" type="image/x-icon">
  <link rel="stylesheet" type="text/css" href="{{ asset('/mobile/css/reset.css') }}">
  <!-- 引入样式 -->
  <link rel="stylesheet" href="{{ asset('/mobile/css/style.css') }}">
  <!-- 引入组件库 -->
  <script src="{{ asset('/mobile/js/vue.js') }}"></script>
  <script src="{{ asset('/mobile/js/index.js') }}"></script>
  <script src="{{ asset('/mobile/js/jquery2.1.1.min.js') }}"></script>
  <script src="{{ asset('/mobile/js/main.js') }}"></script>
  <script src="{{ asset('/mobile/js/vue-router.js') }}"></script>
  <script src="{{ asset('/mobile/js/vue-template.js') }}"></script>
  <script src="{{ asset('/mobile/js/routes.js') }}"></script>
  <script src="{{ asset('https://cdn.bootcss.com/babel-polyfill/6.23.0/polyfill.min.js') }}"></script>

</head>
    @yield('content')
</html>
