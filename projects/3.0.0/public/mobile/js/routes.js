
var routes = [
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
 { path: '/yorderDeId/:id', component: yorderDeId,meta: { }
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
];
var router = new VueRouter({
  routes: routes // （缩写）相当于 routes: routes
})
