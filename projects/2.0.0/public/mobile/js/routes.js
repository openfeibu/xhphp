
const routes = [
  { path: '/home', component: home,meta: { }
},
  { path: '/center', component: center,meta: { }
},

 { path: '/wfh', component: wfh,meta: { }
},
 { path: '/worderDe/:id', component: worderDe,meta: { }
},
 { path: '/yorderDe/:id', component: yorderDe,meta: { }
},
 { path: '/corderDe/:id', component: corderDe,meta: { }
},
 { path: '/torderDe/:id', component: torderDe,meta: { }
},
 { path: '/yfh', component: yfh,meta: { }
},
 { path: '/ywc', component: ywc,meta: { }
},
 { path: '/thsh', component: thsh,meta: { }
},
 { path: '/product', component: product,meta: { }
},
 { path: '/productDe/:id', component: productDe,meta: { }
},
 { path: '/addProduct', component: addProduct,meta: { }
},
 { path: '/classify', component: classify,meta: { }
},
  { path: '/', component: home,meta: { }
},

//   { path: '/table', component: table ,meta: { msg: [{"value":"首页","path":"/"},{"value":"商品管理","path":""}],index:"2-1" }
// },
//   { path: '/notShipped', component: notShipped ,meta: { msg: [{"value":"首页","path":"/"},{"value":"未发货","path":""}],index:"1-1" }
// },  
//   { path: '/shipped', component: shipped ,meta: { msg: [{"value":"首页","path":"/"},{"value":"已发货","path":""}],index:"1-2" }
// },
//   { path: '/succ', component: succ ,meta: { msg: [{"value":"首页","path":"/"},{"value":"已完成","path":""}],index:"1-3" }
// },
//  { path: '/cancell', component: cancell ,meta: { msg: [{"value":"首页","path":"/"},{"value":"退款与售后","path":""}],index:"1-4" }
// },
//  { path: '/setting', component: setting ,meta: { msg: [{"value":"首页","path":"/"},{"value":"店铺设置","path":""}],index:"4" }
// },
]
const router = new VueRouter({
  routes // （缩写）相当于 routes: routes
})
