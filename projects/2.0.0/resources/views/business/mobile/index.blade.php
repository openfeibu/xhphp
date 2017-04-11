@extends('layouts.mobile_business.common')

@section('content')
<body >
	 <div id="app">
    <transition name="fade">
      <router-view></router-view>
    </transition>
      <mt-tabbar v-model="selected" :fixed="true">
        <mt-tab-item id="1" href="#/home" >
          <img slot="icon" :src="selected == 1 ? 'http://web.feibu.info/images/footer03h.png' : 'http://web.feibu.info/images/footer03.png' ">
            工作台
        </mt-tab-item>
        <mt-tab-item id="2" href="#/center">
          <img slot="icon" :src="selected == 2 ? 'http://web.feibu.info/images/footer04h.png' : 'http://web.feibu.info/images/footer04.png' ">
          店铺
        </mt-tab-item>
      </mt-tabbar>
  </div>
  <!-- 首页 -->
  <template id="home" >
    <div class="main pb01">
      <div class="fb-home-header">
          <div class="fb-home-img"><img :src="userInfo.avatar_url" alt=""></div>
          <div class="fb-home-name">@{{userInfo.nickname}}</div>
          <ul>
            <li>
              <p>￥@{{userInfo.todayIncome}}</p>
              <span>今日收益</span>
            </li>
            <li>
              <p>@{{userInfo.sale_count}}</p>
              <span>总订单</span>
            </li>
            <li>
              <p>￥@{{userInfo.income}}</p>
              <span>总收益</span>
            </li>
          </ul>
      </div>
      <div class="fb-home-power">
        <div class="home-power-title">功能管理</div>

        <div class="power-title">订单管理</div>
        <ul>
          <li class="power03">
           <a href="{{ url('/mbusiness/order') }}#/wfh">未发货</a>
          </li>
          <li class="power04">
            <a href="{{ url('/mbusiness/order') }}#/yfh">已发货</a>
          </li>
          <li class="power05">
            <a href="{{ url('/mbusiness/order') }}#/ywc">已完成</a>
          </li>
          <li class="power06">
            <a href="{{ url('/mbusiness/order') }}#/thsh">退货与售后</a>
          </li>
        </ul>
         <div class="power-title">商品管理</div>
        <ul>
          <li class="power01">
            <a href="{{ url('/mbusiness/product') }}#/product">我的宝贝</a>
          </li>
          <li class="power01">
            <a href="{{ url('/mbusiness/product') }}#/classify">分类管理</a>
          </li>
         <!--  <li class="power02">
            <a href="">数据统计</a>
          </li> -->
        </ul>
      </div>
    </div>
  </template>
  <!-- 首页 -->
  <template id="center">
    <div class="main pd bgf5 boxSizing" style=" min-height:100%;">
        <mt-header :title="shopInfo.shop_name" :fixed="true">
        </mt-header>
        <mt-cell  title="店铺LOGO" class="mt01">
          <div class="img"><img :src="shopInfo.shop_img" alt=""></div>
        </mt-cell>

        <mt-cell  title="店铺电话"  class="mt01">
          @{{shopInfo.mobile_no}}
        </mt-cell>
        <mt-cell  title="店铺地址"  >
          @{{shopInfo.address}}
        </mt-cell>
        <div @click="changeDes">
        <mt-cell  title="店铺简介" :label="shopInfo.description" is-link >

        </mt-cell>
        </div>
        <div @click="openPicker">
        <mt-cell  title="开店时间"  is-link  class="mt01" >
            @{{shopInfo.open_time}} - @{{shopInfo.close_time}}
        </mt-cell>
        </div>
        <mt-cell  title="是否开店"  >
            <mt-switch :value.sync="shopStatus" v-model="shopStatus" @change="changeStatus"></mt-switch>
        </mt-cell>
        <mt-cell  title="联系客服" to="tel:020-32168995" is-link class="mt01">
         020-32168995
        </mt-cell>
        <mt-button size="large" onclick="window.location.href={{ url('mbusiness/logout') }}">注销</mt-button>
        <!-- 日期修改 -->
          <mt-datetime-picker
              ref="open"
              type="time"
              v-model="open_time"
              :value="shopInfo.open_time"
              @confirm="handleopenValue">
          </mt-datetime-picker>
          <mt-datetime-picker
              ref="close"
              type="time"
              :value="shopInfo.close_time"
              v-model="close_time"
              @confirm="handlecloseValue">
          </mt-datetime-picker>
        <!-- 日期修改 -->

    </div>

  </template>

</body>
 <script>

    var Main = {
      methods: {
        //路由变化

      },
      created:function(){
        var that = this;
        if(window.location.hash.indexOf("home") != -1){
          that.selected = 1
        }else if(window.location.hash.indexOf("center") != -1){
          that.selected = 2

        }
      },
      watch: {
            // '$route': 'fetchData'
        },
     data() {
          return {
            selected:"1"

          }
        }
    }
  var Ctor = Vue.extend(Main);
  new Ctor({router}).$mount('#app');
  </script>
@stop
