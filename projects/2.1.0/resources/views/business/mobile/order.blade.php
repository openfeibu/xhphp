@extends('layouts.mobile_business.common')

@section('content')
<body >
	 <div id="app" v-cloak>
    <transition name="fade">
      <router-view></router-view>
    </transition>
  </div>


  <!-- 未发货 -->
  <template id="wfh">
    <div style="min-height:100%;" class="bgf5">
    <section class="main pt boxSizing" style="height:100%;" >
      <mt-header title="待发货" :fixed="true">
        <mt-button  onclick="winClose()" icon="back" slot="left">
        </mt-button>
      </mt-header>
      <!-- 没有订单 -->
      <div class="noOrder" v-if="wlist.length == 0">
        <span>没有该类型的订单</span>
      </div>
      <!-- 没有订单 -->
      <mt-loadmore :top-method="wloadTop" @top-status-change="whandleTopChange" ref="loadmore">
                  <ul
                  v-infinite-scroll="wloadMore"
                  infinite-scroll-disabled="wloading"
                  infinite-scroll-distance="10">
                  <li  class="shopOrder-item" v-for="(item,index) in wlist">
                    <div class="order-item-header">
                      <div class="shopNum">订单号:@{{item.order_sn}}</div>
                      <div class="order_status">@{{item.status_desc}}</div>
                    </div>
                    <a :href="'#/worderDe/'+index" v-if="item.order_goodses.length == 1">
                      <div class="img"><img :src="item.order_goodses[0]['goods_thumb']" alt=""></div>
                      <div class="test clearfix">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>
                    <a :href="'#/worderDe/'+index" v-else>
                      <div class="imgsBox clearfix">
                         <div class="img" v-for="goodsImg in item.order_goodses.slice(0, 3)"><img :src="goodsImg['goods_thumb']" alt=""></div>
                      </div>

                      <div class="test2 ">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>

                    <div class="order-item-bottom">
                      <div class="order_count">合计@{{item.total_fee}}元</div>
                    </div>
                  </li>
                </ul>
              <div slot="top" class="mint-loadmore-top">
                <span v-show="wtopStatus !== 'loading'" :class="{ 'rotate': wtopStatus === 'drop' }">↓</span>
                <span v-show="wtopStatus === 'loading'">正在加载中...</span>
              </div>
      </mt-loadmore>
    </section>
    </div>
  </template>
  <!-- 未发货 -->
  <!-- w订单详情 -->
  <template id="worderDe">
    <div>
      <section id="worder_de" class="pd">
          <mt-header title="订单详情" :fixed="true">
            <mt-button onclick="winClose()" icon="back" slot="left">
            </mt-button>
          </mt-header>
          <div class="checkMap" >
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">收货人：@{{nowwlist.consignee}}</span>
                      <span class="fr"><a v-on:click="call(nowwlist.mobile)" >@{{nowwlist.mobile}}</a></span>
                    </div>
                    <div class="li_bottom">
                      @{{nowwlist.address}}
                    </div>
                  </div>
          </div>
          <div class="shopOrder_info">
              <div class="shopName">商品列表</div>
              <ul>
                <li v-for="item in nowwlist.order_goodses">
                  <div class="img">
                    <img :src="item.goods_thumb" alt="">
                  </div>
                  <div class="test">
                    <div class="proName">@{{item.goods_name}}</div>
                    <div class="goods_desc">@{{item.goods_desc}}</div>
                    <div class="moneyNum">
                      <span>￥@{{item.goods_price}}</span>
                      <span>×@{{item.goods_number}}</span>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="order_smark">买家备注：@{{nowwlist.postscript}}</div>
          </div>
          <div class="order_money clearfix">
                <p><label for="" class="fl">商品总金额(元)：</label><span  class="fr">￥@{{nowwlist.goods_amount}}</span></p>
                <p><label for="" class="fl">运费(元)：</label><span  class="fr">+￥@{{nowwlist.shipping_fee}}</span></p>
                <p><label for="" class="fl">发票(元)：</label><span  class="fr">+￥0.00</span></p>
                <p><label for="" class="fl">优惠(元)：</label><span  class="fr">-￥0.00</span></p>
                <p><label for="" class="fl">红包(元)：</label><span  class="fr">-￥@{{nowwlist.coupon_price}}</span></p>
          </div>
          <div class="money_count">
            需付款：<span>￥@{{nowwlist.total_fee}}</span>
          </div>
          <div class="order_info">
            <p>
              <label for="">支付方式：@{{nowwlist.pay_name}}</label>
            </p>
            <p> <label for="">发票：无需发票</label>
            </p>
          </div>
          <div class="order_info">
            <p>
              <label for="">订单号：@{{nowwlist.order_sn}}</label>
            </p>
            <p> <label for="">下单时间：@{{nowwlist.created_at}}</label>
            </p>
            <p> <label for="">付款时间：@{{nowwlist.pay_time}}</label></p>
          </div>
          <div class="submitButton"><div class="delivery opa_active" style="width: 100%;" @click="delivery(nowwlist.order_id,nowwlist.seller_shipping_fee)">我要发货</div></div>
              <mt-popup
          v-model="popupVisible"
          position="top"
          :modal=false
         >发货成功
        </mt-popup>
      </section>
    </div>
  </template>
  <!-- w订单详情 -->
  <!-- 已发货 -->
  <template id="yfh">
    <div style="min-height:100%;" class="bgf5">
    <section class="main pt boxSizing " style="height:100%;">
      <mt-header title="已发货" :fixed="true">
        <mt-button onclick="winClose()" icon="back" slot="left">
        </mt-button>
      </mt-header>
      <!-- 没有订单 -->
      <div class="noOrder" v-if="ylist.length == 0">
        <span>没有该类型的订单</span>
      </div>
      <!-- 没有订单 -->
      <mt-loadmore :top-method="yloadTop" @top-status-change="yhandleTopChange"  ref="loadmore">
                  <ul
                  v-infinite-scroll="yloadMore"
                  infinite-scroll-disabled="yloading"
                  infinite-scroll-distance="10">
                  <li  class="shopOrder-item" v-for="(item,index) in ylist ">
                    <div class="order-item-header">
                      <div class="shopNum">订单号:@{{item.order_sn}}</div>
                      <div class="order_status">@{{item.status_desc}}<span v-if="item.task">(送货中)</span><span v-else>(等待接单)</span></div>
                    </div>
                    <a :href="'#/yorderDe/'+index" v-if="item.order_goodses.length == 1">
                      <div class="img"><img :src="item.order_goodses[0]['goods_thumb']" alt=""></div>
                      <div class="test clearfix">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>
                    <a :href="'#/yorderDe/'+index" v-else>
                      <div class="imgsBox clearfix">
                         <div class="img" v-for="goodsImg in item.order_goodses.slice(0, 3)"><img :src="goodsImg['goods_thumb']" alt=""></div>
                      </div>

                      <div class="test2 ">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>

                    <div class="order-item-bottom">
                      <div class="order_count">合计@{{item.total_fee}}元</div>
                    </div>
                  </li>
                </ul>
              <div slot="top" class="mint-loadmore-top">
                <span v-show="ytopStatus !== 'loading'" :class="{ 'rotate': ytopStatus === 'drop' }">↓</span>
                <span v-show="ytopStatus === 'loading'">正在加载中...</span>
              </div>
      </mt-loadmore>
    </section>
    </div>
  </template>
  <!-- 已发货 -->
    <!-- y订单详情 -->
  <template id="yorderDe">
    <div>
      <section id="worder_de" class="pd">
          <mt-header title="订单详情" :fixed="true">
            <mt-button onclick="winClose()" icon="back" slot="left">
            </mt-button>
          </mt-header>
          <div class="checkMap" >
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">收货人：@{{nowylist.consignee}}</span>
                      <span class="fr"><a v-on:click="call(nowylist.mobile)">@{{nowylist.mobile}}</a></span>
                    </div>
                    <div class="li_bottom">
                      @{{nowylist.address}}
                    </div>
                  </div>
          </div>
          <div class="checkMap" v-if="nowylist.task">
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">接单人：@{{nowylist.task.courier_nickname}}</span>
                      <span class="fr"><a v-on:click="call(nowylist.task.courier_mobile_no)">@{{nowylist.task.courier_mobile_no}}</a></span>
                    </div>
                  </div>
          </div>
          <div class="checkMap" v-else>
                    <div class="waitwork">等待接单</div>
          </div>
          <div class="shopOrder_info">
              <div class="shopName">商品列表</div>
              <ul>
                <li v-for="item in nowylist.order_goodses">
                  <div class="img">
                    <img :src="item.goods_thumb" alt="">
                  </div>
                  <div class="test">
                    <div class="proName">@{{item.goods_name}}</div>
                    <div class="goods_desc">@{{item.goods_desc}}</div>
                    <div class="moneyNum">
                      <span>￥@{{item.goods_price}}</span>
                      <span>×@{{item.goods_number}}</span>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="order_smark">买家备注：@{{nowylist.postscript}}</div>
          </div>
          <div class="order_money clearfix">
                <p><label for="" class="fl">商品总金额(元)：</label><span  class="fr">￥@{{nowylist.goods_amount}}</span></p>
                <p><label for="" class="fl">运费(元)：</label><span  class="fr">+￥@{{nowylist.shipping_fee}}</span></p>
                <p><label for="" class="fl">发票(元)：</label><span  class="fr">+￥0.00</span></p>
                <p><label for="" class="fl">优惠(元)：</label><span  class="fr">-￥0.00</span></p>
                <p><label for="" class="fl">红包(元)：</label><span  class="fr">-￥@{{nowylist.coupon_price}}</span></p>
          </div>
          <div class="money_count">
            实付款：<span>￥@{{nowylist.total_fee}}</span>
          </div>
          <div class="order_info">
            <p>
              <label for="">支付方式：@{{nowylist.pay_name}}</label>
            </p>
            <p> <label for="">发票：无需发票</label>
            </p>
          </div>
          <div class="order_info">
            <p>
              <label for="">订单号：@{{nowylist.order_sn}}</label>
            </p>
            <p> <label for="">下单时间：@{{nowylist.created_at}}</label>
            </p>
            <p> <label for="">付款时间：@{{nowylist.pay_time}}</label></p>
          </div>
          <!-- <div class="submitButton"><div class="delivery opa_active" style="width: 100%;" @click="delivery">我要发货</div></div> -->

      </section>
    </div>
  </template>
  <!-- y订单详情 -->
    <!-- y订单详情 -->
  <template id="yorderDeId">
    <div>
      <section id="worder_de" class="pd">
          <mt-header title="订单详情" :fixed="true">
            <mt-button onclick="winClose()" icon="back" slot="left">
            </mt-button>
          </mt-header>
          <div class="checkMap" >
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">收货人：@{{nowylist.consignee}}</span>
                      <span class="fr"><a v-on:click="call(nowylist.mobile)">@{{nowylist.mobile}}</a></span>
                    </div>
                    <div class="li_bottom">
                      @{{nowylist.address}}
                    </div>
                  </div>
          </div>
          <div class="checkMap" v-if="nowylist.task">
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">接单人：@{{nowylist.task.courier_nickname}}</span>
                      <span class="fr"><a v-on:click="call(nowylist.task.courier_mobile_no)">@{{nowylist.task.courier_mobile_no}}</a></span>
                    </div>
                  </div>
          </div>
          <div class="checkMap" v-else>
                    <div class="waitwork">等待接单</div>
          </div>
          <div class="shopOrder_info">
              <div class="shopName">商品列表</div>
              <ul>
                <li v-for="item in nowylist.order_goodses">
                  <div class="img">
                    <img :src="item.goods_thumb" alt="">
                  </div>
                  <div class="test">
                    <div class="proName">@{{item.goods_name}}</div>
                    <div class="goods_desc">@{{item.goods_desc}}</div>
                    <div class="moneyNum">
                      <span>￥@{{item.goods_price}}</span>
                      <span>×@{{item.goods_number}}</span>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="order_smark">买家备注：@{{nowylist.postscript}}</div>
          </div>
          <div class="order_money clearfix">
                <p><label for="" class="fl">商品总金额(元)：</label><span  class="fr">￥@{{nowylist.goods_amount}}</span></p>
                <p><label for="" class="fl">运费(元)：</label><span  class="fr">+￥@{{nowylist.shipping_fee}}</span></p>
                <p><label for="" class="fl">发票(元)：</label><span  class="fr">+￥0.00</span></p>
                <p><label for="" class="fl">优惠(元)：</label><span  class="fr">-￥0.00</span></p>
                <p><label for="" class="fl">红包(元)：</label><span  class="fr">-￥@{{nowylist.coupon_price}}</span></p>
          </div>
          <div class="money_count">
            实付款：<span>￥@{{nowylist.total_fee}}</span>
          </div>
          <div class="order_info">
            <p>
              <label for="">支付方式：@{{nowylist.pay_name}}</label>
            </p>
            <p> <label for="">发票：无需发票</label>
            </p>
          </div>
          <div class="order_info">
            <p>
              <label for="">订单号：@{{nowylist.order_sn}}</label>
            </p>
            <p> <label for="">下单时间：@{{nowylist.created_at}}</label>
            </p>
            <p> <label for="">付款时间：@{{nowylist.pay_time}}</label></p>
          </div>
          <!-- <div class="submitButton"><div class="delivery opa_active" style="width: 100%;" @click="delivery">我要发货</div></div> -->

      </section>
    </div>
  </template>
  <!-- y订单详情 -->
   <!-- 已完成 -->
  <template id="ywc">
    <div style="min-height:100%;" class="bgf5">
    <section class="main pt boxSizing " style="height:100%;">
      <mt-header title="已完成" :fixed="true">
        <mt-button onclick="winClose()" icon="back" slot="left">
        </mt-button>
      </mt-header>
      <!-- 没有订单 -->
      <div class="noOrder" v-if="clist.length == 0">
        <span>没有该类型的订单</span>
      </div>
      <!-- 没有订单 -->
      <mt-loadmore :top-method="cloadTop" @top-status-change="chandleTopChange"  ref="loadmore">
                  <ul
                  v-infinite-scroll="cloadMore"
                  infinite-scroll-disabled="cloading"
                  infinite-scroll-distance="10">
                  <li  class="shopOrder-item" v-for="(item,index) in clist ">
                    <div class="order-item-header">
                      <div class="shopNum">订单号:@{{item.order_sn}}</div>
                      <div class="order_status">@{{item.status_desc}}</div>
                    </div>
                    <a :href="'#/corderDe/'+index" v-if="item.order_goodses.length == 1">
                      <div class="img"><img :src="item.order_goodses[0]['goods_thumb']" alt=""></div>
                      <div class="test clearfix">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>
                    <a :href="'#/corderDe/'+index" v-else>
                      <div class="imgsBox clearfix">
                         <div class="img" v-for="goodsImg in item.order_goodses.slice(0, 3)"><img :src="goodsImg['goods_thumb']" alt=""></div>
                      </div>

                      <div class="test2 ">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>

                    <div class="order-item-bottom">
                      <div class="order_count">合计@{{item.total_fee}}元</div>
                    </div>
                  </li>
                </ul>
              <div slot="top" class="mint-loadmore-top">
                <span v-show="ctopStatus !== 'loading'" :class="{ 'rotate': ctopStatus === 'drop' }">↓</span>
                <span v-show="ctopStatus === 'loading'">正在加载中...</span>
              </div>
      </mt-loadmore>
    </section>
    </div>
  </template>
  <!-- 已完成 -->
  <!-- c订单详情 -->
  <template id="corderDe">
    <div>
      <section id="worder_de" class="pd">
          <mt-header title="订单详情" :fixed="true">
            <mt-button onclick="winClose()" icon="back" slot="left">
            </mt-button>
          </mt-header>
          <div class="checkMap" >
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">收货人：@{{nowclist.consignee}}</span>
                      <span class="fr"><a v-on:click="call(nowclist.mobile)">@{{nowclist.mobile}}</a></span>
                    </div>
                    <div class="li_bottom">
                      @{{nowclist.address}}
                    </div>
                  </div>
          </div>
          <div class="shopOrder_info">
              <div class="shopName">商品列表</div>
              <ul>
                <li v-for="item in nowclist.order_goodses">
                  <div class="img">
                    <img :src="item.goods_thumb" alt="">
                  </div>
                  <div class="test">
                    <div class="proName">@{{item.goods_name}}</div>
                    <div class="goods_desc">@{{item.goods_desc}}</div>
                    <div class="moneyNum">
                      <span>￥@{{item.goods_price}}</span>
                      <span>×@{{item.goods_number}}</span>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="order_smark">买家备注：@{{nowclist.postscript}}</div>
          </div>
          <div class="order_money clearfix">
                <p><label for="" class="fl">商品总金额(元)：</label><span  class="fr">￥@{{nowclist.goods_amount}}</span></p>
                <p><label for="" class="fl">运费(元)：</label><span  class="fr">+￥@{{nowclist.shipping_fee}}</span></p>
                <p><label for="" class="fl">发票(元)：</label><span  class="fr">+￥0.00</span></p>
                <p><label for="" class="fl">优惠(元)：</label><span  class="fr">-￥0.00</span></p>
                <p><label for="" class="fl">红包(元)：</label><span  class="fr">-￥@{{nowclist.coupon_price}}</span></p>
          </div>
          <div class="money_count">
            实付款：<span>￥@{{nowclist.total_fee}}</span>
          </div>
          <div class="order_info">
            <p>
              <label for="">支付方式：@{{nowclist.pay_name}}</label>
            </p>
            <p> <label for="">发票：无需发票</label>
            </p>
          </div>
          <div class="order_info">
            <p>
              <label for="">订单号：@{{nowclist.order_sn}}</label>
            </p>
            <p> <label for="">下单时间：@{{nowclist.created_at}}</label>
            </p>
            <p> <label for="">付款时间：@{{nowclist.pay_time}}</label></p>
          </div>
          <!-- <div class="submitButton"><div class="delivery opa_active" style="width: 100%;" @click="delivery">我要发货</div></div> -->

      </section>
    </div>
  </template>
  <!-- c订单详情 -->
  <!-- 退货与售后 -->
  <template id="thsh">
    <div style="min-height:100%;" class="bgf5">
    <section class="main pt boxSizing " style="height:100%;">
      <mt-header title="退货与售后" :fixed="true">
        <mt-button onclick="winClose()" icon="back" slot="left">
        </mt-button>
      </mt-header>
      <!-- 没有订单 -->
      <div class="noOrder" v-if="tlist.length == 0">
        <span>没有该类型的订单</span>
      </div>
      <!-- 没有订单 -->
      <mt-loadmore :top-method="tloadTop" @top-status-change="thandleTopChange"  ref="loadmore">
                  <ul
                  v-infinite-scroll="tloadMore"
                  infinite-scroll-disabled="tloading"
                  infinite-scroll-distance="10">
                  <li  class="shopOrder-item" v-for="(item,index) in tlist ">
                    <div class="order-item-header">
                      <div class="shopNum">订单号:@{{item.order_sn}}</div>
                      <div class="order_status">@{{item.status_desc}}</div>
                    </div>
                    <a :href="'#/torderDe/'+index" v-if="item.order_goodses.length == 1">
                      <div class="img"><img :src="item.order_goodses[0]['goods_thumb']" alt=""></div>
                      <div class="test clearfix">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>
                    <a :href="'#/torderDe/'+index" v-else>
                      <div class="imgsBox clearfix">
                         <div class="img" v-for="goodsImg in item.order_goodses.slice(0, 3)"><img :src="goodsImg['goods_thumb']" alt=""></div>
                      </div>

                      <div class="test2 ">
                        <div class="shopName">收货人：@{{item.consignee}}</div>
                        <div class="status_desc">手机号：@{{item.mobile}}</div>
                        <div class="status_desc">收货地址：@{{item.address}}</div>
                        <div class="remarks">备注：@{{item.postscript}}</div>
                      </div>
                    </a>

                    <div class="order-item-bottom">
                      <div class="order_count">合计@{{item.total_fee}}元</div>
                    </div>
                  </li>
                </ul>
              <div slot="top" class="mint-loadmore-top">
                <span v-show="ttopStatus !== 'loading'" :class="{ 'rotate': ttopStatus === 'drop' }">↓</span>
                <span v-show="ttopStatus === 'loading'">正在加载中...</span>
              </div>
      </mt-loadmore>
    </section>
    </div>
  </template>
  <!-- 退货与售后 -->
    <!-- t订单详情 -->
  <template id="torderDe">
    <div>
      <section id="worder_de" class="pd">
          <mt-header title="订单详情" :fixed="true">
            <mt-button onclick="winClose()" icon="back" slot="left">
            </mt-button>
          </mt-header>
          <div class="checkMap" >
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">收货人：@{{nowtlist.consignee}}</span>
                      <span class="fr"><a v-on:click="call(nowtlist.mobile)">@{{nowtlist.mobile}}</a></span>
                    </div>
                    <div class="li_bottom">
                      @{{nowtlist.address}}
                    </div>
                  </div>
          </div>
          <div class="checkMap" v-if="nowtlist.task">
                  <div class="usermap">
                    <div class="li_header">
                      <span class="fl">接单人：@{{nowtlist.task.courier_nickname}}</span>
                      <span class="fr"><a v-on:click="call(nowtlist.task.courier_mobile_no)">@{{nowtlist.task.courier_mobile_no}}</a></span>
                    </div>
                  </div>
          </div>
          <div class="checkMap" v-else>
                    <div class="waitwork">等待接单</div>
          </div>
          <div class="shopOrder_info">
              <div class="shopName">商品列表</div>
              <ul>
                <li v-for="item in nowtlist.order_goodses">
                  <div class="img">
                    <img :src="item.goods_thumb" alt="">
                  </div>
                  <div class="test">
                    <div class="proName">@{{item.goods_name}}</div>
                    <div class="goods_desc">@{{item.goods_desc}}</div>
                    <div class="moneyNum">
                      <span>￥@{{item.goods_price}}</span>
                      <span>×@{{item.goods_number}}</span>
                    </div>
                  </div>
                </li>
              </ul>
              <div class="order_smark">买家备注：@{{nowtlist.postscript}}</div>
          </div>
          <div class="order_money clearfix">
                <p><label for="" class="fl">商品总金额(元)：</label><span  class="fr">￥@{{nowtlist.goods_amount}}</span></p>
                <p><label for="" class="fl">运费(元)：</label><span  class="fr">+￥@{{nowtlist.shipping_fee}}</span></p>
                <p><label for="" class="fl">发票(元)：</label><span  class="fr">+￥0.00</span></p>
                <p><label for="" class="fl">优惠(元)：</label><span  class="fr">-￥0.00</span></p>
                <p><label for="" class="fl">红包(元)：</label><span  class="fr">-￥@{{nowtlist.coupon_price}}</span></p>
          </div>
          <div class="money_count">
            实付款：<span>￥@{{nowtlist.total_fee}}</span>
          </div>
          <div class="order_info">
            <p>
              <label for="">支付方式：@{{nowtlist.pay_name}}</label>
            </p>
            <p> <label for="">发票：无需发票</label>
            </p>
          </div>
          <div class="order_info">
            <p>
              <label for="">订单号：@{{nowtlist.order_sn}}</label>
            </p>
            <p> <label for="">下单时间：@{{nowtlist.created_at}}</label>
            </p>
            <p> <label for="">付款时间：@{{nowtlist.pay_time}}</label></p>
          </div>
          <div class="submitButton" v-if="nowtlist.status_desc=='退款中'"><div class="returnMoney opa_active" style="width: 100%;" @click="returnMoney(nowtlist.order_id)">同意退款</div></div>
          <mt-popup
          v-model="popupVisible"
          position="top"
          :modal=false
         >退款成功
        </mt-popup>
      </section>
    </div>
  </template>
  <!-- t订单详情 -->
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
