<!DOCTYPE html>
<html style="height: 100%;">
<head>
  <meta charset="UTF-8">
  <!-- 引入样式 -->
  <link rel="stylesheet" href="<?php echo e(asset('/css/index.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('/css/reset.css')); ?>">
  <link rel="stylesheet" href="<?php echo e(asset('/css/style.css')); ?>">
	  <!-- 先引入 Vue -->
  <script src="<?php echo e(asset('/js/jquery2.1.1.min.js')); ?>"></script>
  <script src="<?php echo e(asset('/js/vue.min.js')); ?>"></script>
  <script src="<?php echo e(asset('/js/vue-router.js')); ?>"></script>
  <!-- 引入组件库 -->
  <script src="<?php echo e(asset('/js/element.js')); ?>"></script>
  <script src="<?php echo e(asset('/js/vue-template.js')); ?>"></script>
  <script src="<?php echo e(asset('/js/routes.js')); ?>"></script>
</head>
	<?php echo $__env->yieldContent('content'); ?>
</html>