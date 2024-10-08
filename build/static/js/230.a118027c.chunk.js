"use strict";(self.webpackChunksommelier=self.webpackChunksommelier||[]).push([[230],{5664:(e,t,s)=>{s.d(t,{A:()=>u});var r=s(9950),a=s(1580),o=s.n(a),i=s(413),n=s.n(i),l=s(4414);class c extends r.Component{constructor(){super(...arguments),this.state={shareButton:!1,androidShareButton:!1},this.shareLink=e=>{navigator.share&&navigator.share({url:e.link}).then((()=>console.log("Successful share"))).catch((e=>console.log("Error sharing",e)))},this.shareLinkViaAndroidApp=e=>{"FoodomaaAndroidWebViewUA"===navigator.userAgent&&"undefined"!==window.Android&&window.Android.shareDataThroughIntent(e.link)}}componentDidMount(){navigator.share&&this.setState({shareButton:!0}),"FoodomaaAndroidWebViewUA"===navigator.userAgent&&"undefined"!==window.Android&&this.setState({shareButton:!1,androidShareButton:!0})}render(){return(0,l.jsxs)(r.Fragment,{children:[this.state.shareButton&&(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns nav-share-btn",style:{position:"relative"},onClick:()=>this.shareLink(this.props),children:[(0,l.jsx)("i",{className:"si si-share"}),(0,l.jsx)(o(),{duration:"500"})]}),this.state.androidShareButton&&(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns nav-share-btn",style:{position:"relative"},onClick:()=>this.shareLinkViaAndroidApp(this.props),children:[(0,l.jsx)("i",{className:"si si-share"}),(0,l.jsx)(o(),{duration:"500"})]})]})}}const d=c;class h extends r.Component{render(){return(0,l.jsx)(r.Fragment,{children:(0,l.jsx)("div",{className:"col-12 p-0 fixed",style:{zIndex:"9"},children:(0,l.jsx)("div",{className:"block m-0",children:(0,l.jsx)("div",{className:"block-content p-0 ".concat(this.props.dark&&"nav-dark"),children:(0,l.jsxs)("div",{className:"input-group ".concat(this.props.boxshadow&&"search-box"),children:[!this.props.disable_back_button&&(0,l.jsxs)("div",{className:"input-group-prepend",children:[this.props.back_to_home&&(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns",style:{position:"relative"},onClick:()=>{setTimeout((()=>{this.context.router.history.push("/")}),200)},children:[(0,l.jsx)("i",{className:"si si-arrow-left"}),(0,l.jsx)(o(),{duration:"500"})]}),this.props.goto_orders_page&&(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns",style:{position:"relative"},onClick:()=>{setTimeout((()=>{this.context.router.history.push("/my-orders")}),200)},children:[(0,l.jsx)("i",{className:"si si-arrow-left"}),(0,l.jsx)(o(),{duration:"500"})]}),this.props.goto_accounts_page&&(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns",style:{position:"relative"},onClick:()=>{setTimeout((()=>{this.context.router.history.push("/my-account")}),200)},children:[(0,l.jsx)("i",{className:"si si-arrow-left"}),(0,l.jsx)(o(),{duration:"500"})]}),!this.props.back_to_home&&!this.props.goto_orders_page&&!this.props.goto_accounts_page&&(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns ".concat(this.props.dark&&"nav-dark"),style:{position:"relative"},onClick:()=>{setTimeout((()=>{this.context.router.history.goBack()}),200)},children:[(0,l.jsx)("i",{className:"si si-arrow-left"}),(0,l.jsx)(o(),{duration:"500"})]})]}),(0,l.jsxs)("p",{className:"form-control search-input d-flex align-items-center ".concat(this.props.dark&&"nav-dark"),children:[this.props.logo&&(0,l.jsx)("img",{src:"/assets/img/logos/logo.png",alt:localStorage.getItem("storeName"),width:"120"}),this.props.has_title?(0,l.jsx)(r.Fragment,{children:this.props.from_checkout?(0,l.jsxs)("span",{className:"nav-page-title",id:"checkoutNavPageTitle",children:[localStorage.getItem("cartToPayText")," ",(0,l.jsxs)("span",{style:{color:localStorage.getItem("storeColor")},children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),this.props.title,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]})]}):(0,l.jsx)("span",{className:"nav-page-title",children:this.props.title})}):null,this.props.has_delivery_icon&&(0,l.jsx)(n(),{left:!0,children:(0,l.jsx)("img",{src:"/assets/img/various/delivery-bike.png",alt:this.props.title,className:"nav-page-title"})})]}),this.props.has_restaurant_info?(0,l.jsxs)("div",{className:"fixed-restaurant-info hidden",ref:e=>{this.heading=e},children:[(0,l.jsx)("span",{className:"font-w700 fixedRestaurantName",children:this.props.restaurant.name}),(0,l.jsx)("br",{}),(0,l.jsxs)("span",{className:"font-w400 fixedRestaurantTime",children:[(0,l.jsx)("i",{className:"si si-clock"})," ",this.props.restaurant.delivery_time," ",localStorage.getItem("homePageMinsText")]})]}):null,(0,l.jsxs)("div",{className:"input-group-append",children:[!this.props.disable_search&&(0,l.jsxs)("button",{type:"submit",className:"btn search-navs-btns",style:{position:"relative"},children:[(0,l.jsx)("i",{className:"si si-magnifier"}),(0,l.jsx)(o(),{duration:"500"})]}),this.props.homeButton&&(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns nav-home-btn",style:{position:"relative"},onClick:()=>{setTimeout((()=>{this.context.router.history.push("/")}),200)},children:[(0,l.jsx)("i",{className:"si si-home"}),(0,l.jsx)(o(),{duration:"500"})]}),this.props.shareButton&&(0,l.jsx)(d,{link:window.location.href})]})]})})})})})}}h.contextTypes={router:()=>null};const u=h},5230:(e,t,s)=>{s.r(t),s.d(t,{default:()=>p});var r=s(9950),a=s(8858),o=s(3507),i=s(1580),n=s.n(i),l=s(3939),c=s(480),d=s.n(c),h=s(8341),u=s(9351),m=s(5664),g=s(4414);class x extends r.Component{constructor(){super(...arguments),this.state={total:null,restaurants:[],loading:!1,loading_more:!0,selfpickup:!1,userPreferredSelectionDelivery:!0,userPreferredSelectionSelfPickup:!1,no_restaurants:!1,data:[],review_data:[],isHomeDelivery:!0},this.getMyFavoriteRestaurants=()=>{if(localStorage.getItem("userSetAddress")){this.setState({loading:!0});const e=JSON.parse(localStorage.getItem("userSetAddress"));this.props.getFavoriteRestaurants(e.lat,e.lng).then((e=>{e&&e.payload.length?this.setState({total:e.payload.length,no_restaurants:!1,loading:!1,loading_more:!1}):this.setState({total:0,no_restaurants:!0,loading:!1,loading_more:!1})}))}}}componentDidMount(){this.getMyFavoriteRestaurants(),"DELIVERY"===localStorage.getItem("userPreferredSelection")&&this.setState({userPreferredSelectionDelivery:!0,isHomeDelivery:!0}),"SELFPICKUP"===localStorage.getItem("userPreferredSelection")&&"true"===localStorage.getItem("enSPU")?this.setState({userPreferredSelectionSelfPickup:!0,isHomeDelivery:!1}):(localStorage.setItem("userPreferredSelection","DELIVERY"),localStorage.setItem("userSelected","DELIVERY"),this.setState({userPreferredSelectionDelivery:!0,isHomeDelivery:!0}))}render(){return(0,g.jsxs)(r.Fragment,{children:[(0,g.jsx)(m.A,{boxshadow:!0,has_title:!0,title:localStorage.getItem("favouriteStoresPageTitle"),disable_search:!0,goto_accounts_page:!0,homeButton:!0}),(0,g.jsxs)("div",{className:"bg-white mb-100",children:[this.state.no_restaurants&&(0,g.jsx)("div",{className:"bg-light "+("true"===localStorage.getItem("enSPU")?"sticky-top":"pt-3"),children:(0,g.jsx)("div",{className:"px-15 py-3 d-flex justify-content-between align-items-center pt-100",children:(0,g.jsx)("h1",{className:"restaurant-count mb-0 mr-2",children:localStorage.getItem("noRestaurantMessage")})})}),this.state.loading?(0,g.jsxs)(a.Ay,{height:378,width:400,speed:1.2,primaryColor:"#f3f3f3",secondaryColor:"#ecebeb",children:[(0,g.jsx)("rect",{x:"20",y:"20",rx:"4",ry:"4",width:"80",height:"78"}),(0,g.jsx)("rect",{x:"144",y:"30",rx:"0",ry:"0",width:"115",height:"18"}),(0,g.jsx)("rect",{x:"144",y:"60",rx:"0",ry:"0",width:"165",height:"16"}),(0,g.jsx)("rect",{x:"20",y:"145",rx:"4",ry:"4",width:"80",height:"78"}),(0,g.jsx)("rect",{x:"144",y:"155",rx:"0",ry:"0",width:"115",height:"18"}),(0,g.jsx)("rect",{x:"144",y:"185",rx:"0",ry:"0",width:"165",height:"16"}),(0,g.jsx)("rect",{x:"20",y:"270",rx:"4",ry:"4",width:"80",height:"78"}),(0,g.jsx)("rect",{x:"144",y:"280",rx:"0",ry:"0",width:"115",height:"18"}),(0,g.jsx)("rect",{x:"144",y:"310",rx:"0",ry:"0",width:"165",height:"16"})]}):(0,g.jsx)(r.Fragment,{children:0===this.props.restaurants.length?(0,g.jsxs)(a.Ay,{height:378,width:400,speed:1.2,primaryColor:"#f3f3f3",secondaryColor:"#ecebeb",children:[(0,g.jsx)("rect",{x:"20",y:"20",rx:"4",ry:"4",width:"80",height:"78"}),(0,g.jsx)("rect",{x:"144",y:"30",rx:"0",ry:"0",width:"115",height:"18"}),(0,g.jsx)("rect",{x:"144",y:"60",rx:"0",ry:"0",width:"165",height:"16"}),(0,g.jsx)("rect",{x:"20",y:"145",rx:"4",ry:"4",width:"80",height:"78"}),(0,g.jsx)("rect",{x:"144",y:"155",rx:"0",ry:"0",width:"115",height:"18"}),(0,g.jsx)("rect",{x:"144",y:"185",rx:"0",ry:"0",width:"165",height:"16"}),(0,g.jsx)("rect",{x:"20",y:"270",rx:"4",ry:"4",width:"80",height:"78"}),(0,g.jsx)("rect",{x:"144",y:"280",rx:"0",ry:"0",width:"115",height:"18"}),(0,g.jsx)("rect",{x:"144",y:"310",rx:"0",ry:"0",width:"165",height:"16"})]}):(0,g.jsx)("div",{className:"pt-50",children:this.props.restaurants.map(((e,t)=>(0,g.jsx)(r.Fragment,{children:(0,g.jsx)(l.Ay,{children:(0,g.jsx)("div",{className:"col-xs-12 col-sm-12 restaurant-block",children:(0,g.jsxs)(o.A,{to:"../stores/"+e.slug,delay:200,className:"block text-center mb-3",clickAction:()=>{"DELIVERY"===localStorage.getItem("userPreferredSelection")&&1===e.delivery_type&&localStorage.setItem("userSelected","DELIVERY"),"SELFPICKUP"===localStorage.getItem("userPreferredSelection")&&2===e.delivery_type&&localStorage.setItem("userSelected","SELFPICKUP"),"DELIVERY"===localStorage.getItem("userPreferredSelection")&&3===e.delivery_type&&localStorage.setItem("userSelected","DELIVERY"),"SELFPICKUP"===localStorage.getItem("userPreferredSelection")&&3===e.delivery_type&&localStorage.setItem("userSelected","SELFPICKUP")},children:[(0,g.jsxs)("div",{className:"block-content block-content-full ".concat(e.is_featured&&e.is_active?"ribbon ribbon-bookmark ribbon-warning pt-2":"pt-2"," "),children:[e.is_featured?(0,g.jsx)(r.Fragment,{children:null==e.custom_featured_name?(0,g.jsx)("div",{className:"ribbon-box",children:localStorage.getItem("restaurantFeaturedText")}):(0,g.jsx)("div",{className:"ribbon-box",children:e.custom_featured_name})}):null,(0,g.jsx)(d(),{duration:500,children:(0,g.jsx)("img",{src:e.image,alt:e.name,className:"restaurant-image ".concat(!e.is_active&&"restaurant-not-active")})})]}),(0,g.jsxs)("div",{className:"block-content block-content-full restaurant-info",children:[(0,g.jsx)("div",{className:"font-w600 mb-5 text-dark",children:e.name}),(0,g.jsx)("div",{className:"font-size-sm text-muted truncate-text text-muted",children:e.description}),!e.is_active&&(0,g.jsx)("span",{className:"restaurant-not-active-msg",children:localStorage.getItem("restaurantNotActiveMsg")}),(0,g.jsx)("hr",{className:"my-10"}),(0,g.jsxs)("div",{className:"text-center restaurant-meta mt-5 d-flex align-items-center justify-content-between text-muted",children:[(0,g.jsxs)("div",{className:"col-2 p-0 text-left store-rating-block",children:[(0,g.jsx)("i",{className:"fa fa-star pr-1 ".concat(!e.is_active&&"restaurant-not-active"),style:{color:localStorage.getItem("storeColor")}})," ","0"===e.avgRating?e.rating:e.avgRating]}),(0,g.jsx)("div",{className:"col-4 p-0 text-center store-distance-block",children:this.state.selfpickup?(0,g.jsxs)("span",{children:[(0,g.jsx)("i",{className:"si si-pointer pr-1"}),e.distance&&e.distance.toFixed(1)," ","Km"]}):(0,g.jsxs)("span",{children:[(0,g.jsx)("i",{className:"si si-clock pr-1"})," ",e.delivery_time," ",localStorage.getItem("homePageMinsText")]})}),(0,g.jsxs)("div",{className:"col-6 p-0 text-center store-avgprice-block",children:[(0,g.jsx)("i",{className:"si si-wallet"})," ","left"===localStorage.getItem("currencySymbolAlign")&&(0,g.jsxs)(r.Fragment,{children:[localStorage.getItem("currencyFormat"),e.price_range," "]}),"right"===localStorage.getItem("currencySymbolAlign")&&(0,g.jsxs)(r.Fragment,{children:[e.price_range,localStorage.getItem("currencyFormat")," "]}),localStorage.getItem("homePageForTwoText")]})]})]}),(0,g.jsx)(n(),{duration:"500",hasTouch:!1})]})})})},e.id)))})}),this.state.loading_more?(0,g.jsx)("div",{className:"",children:(0,g.jsxs)(a.Ay,{height:120,width:400,speed:1.2,primaryColor:"#f3f3f3",secondaryColor:"#ecebeb",children:[(0,g.jsx)("rect",{x:"20",y:"20",rx:"4",ry:"4",width:"80",height:"78"}),(0,g.jsx)("rect",{x:"144",y:"35",rx:"0",ry:"0",width:"115",height:"18"}),(0,g.jsx)("rect",{x:"144",y:"65",rx:"0",ry:"0",width:"165",height:"16"})]})}):null]})]})}}const p=(0,h.Ng)((e=>({restaurants:e.restaurant.favoriteRestaurants})),{getDeliveryRestaurants:u.hk,getSelfpickupRestaurants:u.fY,getFavoriteRestaurants:u.hI}(x))},413:(e,t,s)=>{function r(e,t){var s=t.left,r=t.right,a=t.mirror,o=t.opposite,i=(s?1:0)|(r?2:0)|(a?16:0)|(o?32:0)|(e?64:0);if(h.hasOwnProperty(i))return h[i];if(!a!=!(e&&o)){var n=[r,s];s=n[0],r=n[1]}var l=s?"-100%":r?"100%":"0",d=e?"from {\n        opacity: 1;\n      }\n      to {\n        transform: translate3d("+l+", 0, 0) skewX(30deg);\n        opacity: 0;\n      }\n    ":"from {\n        transform: translate3d("+l+", 0, 0) skewX(-30deg);\n        opacity: 0;\n      }\n      60% {\n        transform: skewX(20deg);\n        opacity: 1;\n      }\n      80% {\n        transform: skewX(-5deg);\n        opacity: 1;\n      }\n      to {\n        transform: none;\n        opacity: 1;\n      }";return h[i]=(0,c.animation)(d),h[i]}function a(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:c.defaults,t=e.children,s=(e.out,e.forever),a=e.timeout,o=e.duration,i=void 0===o?c.defaults.duration:o,l=e.delay,d=void 0===l?c.defaults.delay:l,h=e.count,u=void 0===h?c.defaults.count:h,m=function(e,t){var s={};for(var r in e)t.indexOf(r)>=0||Object.prototype.hasOwnProperty.call(e,r)&&(s[r]=e[r]);return s}(e,["children","out","forever","timeout","duration","delay","count"]),g={make:r,duration:void 0===a?i:a,delay:d,forever:s,count:u,style:{animationFillMode:"both"}};return m.left,m.right,m.mirror,m.opposite,(0,n.default)(m,g,g,t)}Object.defineProperty(t,"__esModule",{value:!0});var o,i=s(7374),n=(o=i)&&o.__esModule?o:{default:o},l=s(1942),c=s(7244),d={out:l.bool,left:l.bool,right:l.bool,mirror:l.bool,opposite:l.bool,duration:l.number,timeout:l.number,delay:l.number,count:l.number,forever:l.bool},h={};a.propTypes=d,t.default=a,e.exports=t.default}}]);