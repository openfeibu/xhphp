<!DOCTYPE html>
<html style="height: 100%;">
<head>
  <meta charset="UTF-8">
  <!-- 引入样式 -->
  <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-default/index.css">
  <link rel="stylesheet" href="{{ asset('/css/reset.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
	  <!-- 先引入 Vue -->
  <script src="{{ asset('/js/jquery2.1.1.min.js') }}"></script>
  <script src="{{ asset('/js/vue.min.js') }}"></script>
  <script src="{{ asset('/js/vue-router.js') }}"></script>
  <!-- 引入组件库 -->
  <script src="{{ asset('/js/element.js') }}"></script>
  <script src="{{ asset('/js/vue-template.js') }}"></script>
  <script src="{{ asset('/js/routes.js') }}"></script>
</head>
	@yield('content')
</html>