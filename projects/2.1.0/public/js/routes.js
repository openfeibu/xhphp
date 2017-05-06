
var routes = [
  { path: '/classify', component: classify,meta: { msg: [{"value":"首页","path":"/"},{"value":"分类管理","path":""}],index:"2-2" }
},
  { path: '/table', component: table ,meta: { msg: [{"value":"首页","path":"/"},{"value":"商品管理","path":""}],index:"2-1" }
},
  { path: '/notShipped', component: notShipped ,meta: { msg: [{"value":"首页","path":"/"},{"value":"未发货","path":""}],index:"1-1" }
},  
  { path: '/shipped', component: shipped ,meta: { msg: [{"value":"首页","path":"/"},{"value":"已发货","path":""}],index:"1-2" }
},
  { path: '/succ', component: succ ,meta: { msg: [{"value":"首页","path":"/"},{"value":"已完成","path":""}],index:"1-3" }
},
 { path: '/cancell', component: cancell ,meta: { msg: [{"value":"首页","path":"/"},{"value":"退款与售后","path":""}],index:"1-4" }
},
 { path: '/setting', component: setting ,meta: { msg: [{"value":"首页","path":"/"},{"value":"店铺设置","path":""}],index:"4" }
},
]
var router = new VueRouter({
  routes :routes// （缩写）相当于 routes: routes
})
