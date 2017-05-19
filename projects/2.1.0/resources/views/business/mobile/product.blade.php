@extends('layouts.mobile_business.common')

@section('content')
<body >
	 <div id="app" v-cloak>
    <transition name="fade">
      <router-view></router-view>
    </transition>
  </div>
  <!-- 商品列表 -->
  <template id="product">
    <div style="min-height:100%;" class="bgf5">
    <div class="main pt boxSizing " style="height:100%;">
      <mt-header title="我的宝贝" :fixed="true">
        <mt-button  onclick="winClose()" icon="back" slot="left">
        </mt-button>
         <mt-button icon="add" slot="right" onclick="window.location.href='#/addProduct'"></mt-button>
      </mt-header>
      <div class="changeClass">
        <label for="">已选分类：</label>
        <select name="" id=""  @change="changeClass" v-model="catId">
              <option value="all" >全部分类</option>
              <option v-for="item in productClassAll" :value="item.cat_id" :selected="item.cat_id == catId">@{{item.cat_name}}</option>
        </select>
      </div>
      <!-- 没有订单 -->
      <div class="noOrder" v-if="list.length == 0">
        <span>还没有上传商品</span>
      </div>
      <!-- 没有订单 -->
      <mt-loadmore :top-method="tloadTop" @top-status-change="thandleTopChange" ref="loadmore">
                  <ul
                  v-infinite-scroll="tloadMore"
                  infinite-scroll-disabled="loading"
                  infinite-scroll-distance="10">
                  <li  class="product-item" v-for="(item,index) in list ">
                      <mt-cell-swipe v-if="item.is_on_sale == 0"
                        :right="[
                          {
                            content: '上架',
                            style: { background: '#ccc', color: '#fff', lineHeight:'82px' },
                            handler: function(){change_is_on_sale(item.goods_id,1,index)}
                          },{
                            content: '删除',
                            style: { background: '#FF4949', color: '#fff', lineHeight:'82px' },
                            handler:  function(){deletePro(item.goods_id,index)}
                          }
                        ]">
                        <a :href="'#/productDe/'+item.goods_id" >
                          <div  :class="item.goods_number == 0 ? 'noBg' : ''" class="proimg"><img :src="item.goods_thumb" alt=""></div>
                          <div class="proName">@{{item.goods_name}}（已下架）</div>
                          <div class="proDec">@{{item.goods_desc}}</div>
                          <div class="proDec">库存：@{{item.goods_number}}</div>
                          <p class="money" style="color:#FF4949">￥@{{item.goods_price}}</p>
                        </a>
                      </mt-cell-swipe>

                      <mt-cell-swipe  v-else-if="item.is_on_sale == 1"
                        :right="[
                          {
                            content: '下架',
                            style: { background: '#ccc', color: '#fff', lineHeight:'1.8rem' },
                            handler:  function(){change_is_on_sale(item.goods_id,0,index)}
                          },{
                            content: '删除',
                            style: { background: '#FF4949', color: '#fff', lineHeight:'1.8rem' },
                            handler: function(){deletePro(item.goods_id,index)}
                          }
                        ]">
                          <a :href="'#/productDe/'+item.goods_id" >
                            <div  :class="item.goods_number == 0 ? 'noBg' : ''" class="proimg"><img :src="item.goods_thumb" alt=""></div>
                            <div class="proName">@{{item.goods_name}}</div>
                            <div class="proDec">@{{item.goods_desc}}</div>
                            <div class="proDec">库存：@{{item.goods_number}}</div>
                            <p class="money" style="color:#FF4949">￥@{{item.goods_price}}</p>
                          </a>
                      </mt-cell-swipe>



                  </li>
                </ul>
              <div slot="top" class="mint-loadmore-top">
                <span v-show="ttopStatus !== 'loading'" :class="{ 'rotate': ttopStatus === 'drop' }">↓</span>
                <span v-show="ttopStatus === 'loading'">正在加载中...</span>
              </div>
      </mt-loadmore>
    </div>
    </div>
  </template>
  <!-- 商品列表 -->
  <!-- 商品详情 -->
  <template id="productDe">
    <div style="min-height:100%;" class="bgf">
    <section class="main pd boxSizing" style="height:100%">
      <mt-header title="宝贝详情" :fixed="true">
        <mt-button onclick="returnUp()" icon="back" slot="left">
        </mt-button>
      </mt-header>
      <div class="productImg">
        <img :src="productDeList.goods_thumb" alt="">
        <input name="bookpic" type="file" id="bookpic" class="upload" @change="change()">
      </div>
      <div class="proForm">
        <div class="productName input-item" >
          <label >商品名称:</label>
          <input type="text" v-model="productDeList.goods_name" />
        </div>
        <div class="productName input-item" >
          <label >本店售价:</label>
          <input type="number"  v-model="productDeList.goods_price" />
        </div>
        <div class="productName input-item" >
          <label >商品库存:</label>
          <input type="number"  v-model="productDeList.goods_number"/>
        </div>
        <div class="productName input-item" >
          <label style="font-size: 0.2rem">商品重量(kg):</label>
          <input type="number"  v-model="productDeList.weight" />
        </div>
        <div class="productName input-item" >
          <label >商品分类:</label>
          <select name="" id=""  v-model="productDeList.cat_id">
            <option v-for="item in productClassAll" :value="item.cat_id" :selected="productDeList.cat_id == item.cat_id">@{{item.cat_name}}</option>
          </select>
        </div>
        <div class="productName input-item" >
          <label >是否上架:</label>
          <mt-switch  v-model="isOnSale" style="margin-top:0.2rem"></mt-switch>
        </div>
        <div class="productName input-item" >
          <label >商品简介:</label>
          <textarea name="" id="" v-model="productDeList.goods_desc"></textarea>
        </div>
      </div>
      <div class="pro-updata-submit">
        <mt-button type="primary" size="large" @click="updataPro">保存</mt-button>
      </div>

    </section>
    </div>
  </template>
  <!-- 商品详情 -->
  <!-- add商品 -->
  <template id="addProduct">
    <div style="min-height:100%;" class="bgf">
    <section class="main pd boxSizing" style="height:100%;">
      <mt-header title="上传宝贝" :fixed="true">
        <mt-button onclick="returnUp()" icon="back" slot="left">
        </mt-button>
      </mt-header>
      <div class="productImg">
        <img :src="productDeList.goods_thumb" alt="">
        <input name="bookpic" type="file" id="bookpic" class="upload" @change="change()">
      </div>
      <div class="proForm">
        <div class="productName input-item" >
          <label >商品名称:</label>
          <input type="text" v-model="productDeList.goods_name" placeholder="点击输入商品名称"/>
        </div>
        <div class="productName input-item" >
          <label >本店售价:</label>
          <input type="number"  v-model="productDeList.goods_price" placeholder="点击输入商品售价"/>
        </div>
        <div class="productName input-item" >
          <label >商品库存:</label>
          <input type="number"  v-model="productDeList.goods_number"placeholder="点击输入商品库存"/>
        </div>
        <div class="productName input-item" >
          <label style="font-size: 0.2rem">商品重量(kg):</label>
          <input type="number"  v-model="productDeList.weight"placeholder="点击输入商品重量"/>
        </div>
        <div class="productName input-item" >
          <label >商品分类:</label>
          <select name="" id=""  v-model="productDeList.cat_id">
            <option v-for="item in productClassAll" :value="item.cat_id" >@{{item.cat_name}}</option>
          </select>
        </div>
        <div class="productName input-item" >
          <label >是否上架:</label>
          <mt-switch  v-model="isOnSale" style="margin-top:0.2rem"></mt-switch>
        </div>
        <div class="productName input-item" >
          <label >商品简介:</label>
          <textarea name="" id="" v-model="productDeList.goods_desc" placeholder="点击输入商品简介"></textarea>
        </div>
      </div>
      <div class="pro-updata-submit">
        <mt-button type="primary" size="large" @click="updataPro">上传</mt-button>
      </div>

    </section>
    </div>
  </template>
  <!-- add商品 -->
  <!-- 分类管理 -->
  <template id="classify">
    <div style="min-height:100%;" class="bgf">
    <section class="main pd boxSizing" style="height:100%;">
      <mt-header title="分类管理" :fixed="true">
        <mt-button onclick="winClose()" icon="back" slot="left">
        </mt-button>
         <mt-button icon="add" slot="right" @click="addClassify"></mt-button>
      </mt-header>
      <ul>
        <li v-for = "(item,index) in productClassAll">
           <mt-cell-swipe  :right="[
                          {
                            content: '修改',
                            style: { background: '#ccc', color: '#fff',},
                            handler:  function(){ updataClassify(item.cat_id,item.cat_name,index)}
                          },{
                            content: '删除',
                            style: { background: '#FF4949', color: '#fff' },
                            handler:  function(){deleteClassify(item.cat_id,item.cat_name,index)}
                          }
                        ]" :title="item.cat_name"></mt-cell-swipe>
        </li>
      </ul>

    </section>
    </div>
  </template>
  <!-- 分类管理 -->
</body>
 <script>
    var Main = {
      methods: {

      },
      created:function(){

      },

    }
  var Ctor = Vue.extend(Main);
  new Ctor({router:router}).$mount('#app');
  </script>
@stop
