"use strict";(self.webpackChunksommelier=self.webpackChunksommelier||[]).push([[383],{7383:(e,t,s)=>{s.r(t),s.d(t,{default:()=>B});var r=s(9950),i=s(8858),a=s(2515),l=s(8429),o=s(8341),n=s(518),d=s(1753),c=s(8469);var h=s(576),m=s(1580),g=s.n(m),x=s(7038),p=s.n(x),y=s(2074),u=s(7902),v=s(4414);class j extends r.Component{constructor(){super(...arguments),this.__refreshOrders=()=>{this.refs.btnSpinner&&this.refs.btnSpinner.classList.add("fa-spin"),setTimeout((()=>{this.refs.btnSpinner&&this.refs.btnSpinner.classList.remove("fa-spin")}),2e3),this.props.refreshOrders()},this.getDeliveryGuyTotalEarning=e=>{let t=0;return e.commission&&(t+=parseFloat(e.commission)),e.tip_amount&&(t+=parseFloat(e.tip_amount)),t}}componentDidMount(){document.getElementsByTagName("body")[0].classList.remove("bg-grey"),document.getElementsByTagName("body")[0].classList.add("delivery-dark-bg")}render(){const{new_orders:e,delivery_user:t}=this.props;return(0,v.jsx)(r.Fragment,{children:(0,v.jsxs)("div",{className:"mb-100",children:[(0,v.jsx)("div",{className:"d-flex justify-content-between nav-dark",children:t.data.status?(0,v.jsxs)(r.Fragment,{children:[(0,v.jsx)("div",{className:"delivery-tab-title px-20 py-15",children:localStorage.getItem("deliveryNewOrdersTitle")}),(0,v.jsx)("div",{className:"delivery-order-refresh",children:(0,v.jsxs)("button",{className:"btn btn-refreshOrders mr-15",onClick:this.__refreshOrders,style:{position:"relative"},children:[localStorage.getItem("deliveryOrdersRefreshBtn")," ",(0,v.jsx)("i",{ref:"btnSpinner",className:"fa fa-refresh"}),(0,v.jsx)(g(),{duration:1200})]})})]}):null}),t.data.status?(0,v.jsx)(r.Fragment,{children:0===e.length?(0,v.jsx)("p",{className:"text-center text-muted py-15 mb-10 bg-white",children:localStorage.getItem("deliveryNoNewOrders")}):(0,v.jsx)("div",{className:"p-15",children:(0,v.jsx)("div",{className:"delivery-list-wrapper pb-20",children:e.map((e=>(0,v.jsxs)(y.N_,{to:"/delivery/orders/".concat(e.unique_order_id),style:{position:"relative"},children:[(0,v.jsxs)("div",{className:"delivery-list-item px-15 pb-5 pt-15",children:[(0,v.jsxs)("div",{className:"d-flex justify-content-between",children:[(0,v.jsx)("div",{children:(0,v.jsx)("p",{className:"m-0",children:"true"===localStorage.getItem("showFromNowDate")?(0,v.jsx)(p(),{fromNow:!0,children:e.updated_at}):(0,v.jsx)(p(),{format:"DD/MM/YYYY hh:mma",children:e.updated_at})})}),(0,v.jsx)("div",{children:"true"===localStorage.getItem("enableDeliveryGuyEarning")&&(0,v.jsxs)("p",{className:"m-0 list-delivery-commission",children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)(u.A,{to:this.getDeliveryGuyTotalEarning(e),speed:1e3,digits:2}),"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]})})]}),(0,v.jsxs)("div",{className:"d-flex justify-content-between",children:[(0,v.jsx)("div",{className:"font-w700 list-delivery-store-name",children:e.restaurant.name}),(0,v.jsx)("div",{children:(0,v.jsxs)("p",{className:"m-0 font-w700",children:["#",e.unique_order_id.substr(e.unique_order_id.length-8)]})})]}),(0,v.jsx)("p",{children:"true"===localStorage.getItem("showDeliveryFullAddressOnList")?(0,v.jsx)("span",{children:e.address}):(0,v.jsxs)("span",{className:"d-flex align-items-center",children:[(0,v.jsx)("i",{className:"si si-pointer mr-2"}),(0,v.jsx)("span",{style:{maxWidth:"100%",display:"block"},className:"truncate-text",children:e.address})]})})]}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]},e.id)))})})}):(0,v.jsx)("div",{className:"d-flex justify-content-center pt-100",children:(0,v.jsx)("div",{className:"delivery-guy-status delivery-guy-offline",children:(0,v.jsx)("span",{children:localStorage.getItem("deliveryAppYouAreOfflineBtn")})})})]})})}}const f=j;class b extends r.Component{constructor(){super(...arguments),this.__refreshOrders=()=>{this.refs.btnSpinner&&this.refs.btnSpinner.classList.add("fa-spin"),setTimeout((()=>{this.refs.btnSpinner&&this.refs.btnSpinner.classList.remove("fa-spin")}),2e3),this.props.refreshOrders()},this.getDeliveryGuyTotalEarning=e=>{let t=0;return e.commission&&(t+=parseFloat(e.commission)),e.tip_amount&&(t+=parseFloat(e.tip_amount)),t}}componentDidMount(){document.getElementsByTagName("body")[0].classList.remove("bg-grey"),document.getElementsByTagName("body")[0].classList.add("delivery-dark-bg")}render(){const{accepted_orders:e}=this.props;return(0,v.jsx)(r.Fragment,{children:(0,v.jsxs)("div",{className:"mb-100",children:[(0,v.jsxs)("div",{className:"d-flex justify-content-between nav-dark",children:[(0,v.jsx)("div",{className:"delivery-tab-title px-20 py-15",children:localStorage.getItem("deliveryAcceptedOrdersTitle")}),(0,v.jsx)("div",{className:"delivery-order-refresh",children:(0,v.jsxs)("button",{className:"btn btn-refreshOrders mr-15",onClick:this.__refreshOrders,style:{position:"relative"},children:[localStorage.getItem("deliveryOrdersRefreshBtn")," ",(0,v.jsx)("i",{ref:"btnSpinner",className:"fa fa-refresh"}),(0,v.jsx)(g(),{duration:1200})]})})]}),0===e.length?(0,v.jsx)("p",{className:"text-center text-muted py-15 mb-10 bg-white",children:localStorage.getItem("deliveryNoOrdersAccepted")}):(0,v.jsx)("div",{className:"p-15",children:(0,v.jsx)("div",{className:"delivery-list-wrapper pb-20",children:e.map((e=>(0,v.jsxs)(y.N_,{to:"/delivery/orders/".concat(e.unique_order_id),style:{position:"relative"},children:[(0,v.jsxs)("div",{className:"delivery-list-item px-15 pb-5 pt-15",children:[(0,v.jsxs)("div",{className:"d-flex justify-content-between",children:[(0,v.jsx)("div",{children:(0,v.jsx)("p",{className:"m-0",children:"true"===localStorage.getItem("showFromNowDate")?(0,v.jsx)(p(),{fromNow:!0,children:e.updated_at}):(0,v.jsx)(p(),{format:"DD/MM/YYYY hh:mma",children:e.updated_at})})}),(0,v.jsx)("div",{children:"true"===localStorage.getItem("enableDeliveryGuyEarning")&&(0,v.jsxs)("p",{className:"m-0 list-delivery-commission",children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)(u.A,{to:this.getDeliveryGuyTotalEarning(e),speed:1e3,digits:2}),"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]})})]}),(0,v.jsxs)("div",{className:"d-flex justify-content-between",children:[(0,v.jsx)("div",{className:"font-w700 list-delivery-store-name",children:e.restaurant.name}),(0,v.jsx)("div",{children:(0,v.jsxs)("p",{className:"m-0 font-w700",children:["#",e.unique_order_id.substr(e.unique_order_id.length-8)]})})]}),(0,v.jsx)("p",{children:"true"===localStorage.getItem("showDeliveryFullAddressOnList")?(0,v.jsx)("span",{children:e.address}):(0,v.jsxs)("span",{className:"d-flex align-items-center",children:[(0,v.jsx)("i",{className:"si si-pointer mr-2"}),(0,v.jsx)("span",{style:{maxWidth:"100%",display:"block"},className:"truncate-text",children:e.address})]})})]}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]},e.id)))})})]})})}}const w=b;class _ extends r.Component{render(){const{order:e}=this.props;return(0,v.jsx)(r.Fragment,{children:(0,v.jsxs)("div",{className:"delivery-account-orders-block p-15 mb-20",children:[(0,v.jsxs)("div",{className:"d-flex justify-content-between mb-2",children:[(0,v.jsx)("div",{className:"font-w700",children:(0,v.jsxs)("h4",{className:"mb-0 ".concat("true"===localStorage.getItem("deliveryAppLightMode")?"text-dark":"text-white"),children:["#",e.order.unique_order_id.substr(e.order.unique_order_id.length-6)]})}),(0,v.jsx)("div",{children:"true"===localStorage.getItem("showFromNowDate")?(0,v.jsx)(p(),{fromNow:!0,children:e.updated_at}):(0,v.jsx)(p(),{format:"DD/MM/YYYY hh:mma",children:e.updated_at})})]}),(0,v.jsxs)("div",{className:"d-flex justify-content-between mb-2",children:[(0,v.jsx)("div",{className:"mr-4",children:e.is_complete?(0,v.jsx)("span",{className:"btn btn-sm btn-delivery-success min-width-125",children:localStorage.getItem("deliveryCompletedText")}):(0,v.jsx)("span",{className:"btn btn-sm btn-delivery-ongoing min-width-125",children:localStorage.getItem("deliveryOnGoingText")})}),(0,v.jsx)("div",{children:"COD"===e.order.payment_mode?(0,v.jsxs)("span",{className:"btn btn-sm btn-delivery-success min-width-175",children:[localStorage.getItem("deliveryCashOnDelivery"),":"," ","left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),e.order.payable,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]}):(0,v.jsxs)("span",{className:"btn btn-sm btn-delivery-success min-width-175",children:[(0,v.jsx)("i",{className:"si si-check mr-2"})," ",localStorage.getItem("deliveryOnlinePayment")]})})]}),(0,v.jsxs)("div",{children:[(0,v.jsx)("i",{className:"si si-pointer mr-1"})," ",e.order.address]})]})})}}const N=_;var S=s(9452);class I extends r.Component{constructor(){super(...arguments),this.state={series:[{name:"Earnings",data:[0,0,0,0,0,0,0]}],options:{chart:{height:350,type:"line",zoom:{enabled:!1},dropShadow:{enabled:!0,top:0,left:0,blur:3,opacity:.8},toolbar:{show:!1}},dataLabels:{enabled:!1},stroke:{curve:"smooth",colors:["#18c775"]},title:{text:localStorage.getItem("deliveryLastSevenDaysEarningTitle"),align:"left",style:{color:"#fafafa"}},grid:{row:{colors:["#222b45","transparent"],opacity:1}},xaxis:{labels:{style:{colors:["#fafafa","#fafafa","#fafafa","#fafafa","#fafafa","#fafafa","#fafafa"]}}},yaxis:{labels:{style:{colors:["#fafafa","#fafafa","#fafafa","#fafafa","#fafafa","#fafafa","#fafafa"]}}},test:{name:"saurabh",age:"27"}}}}componentWillReceiveProps(e){const t=[{name:"Earnings",data:e.data.chartData}];this.setState({series:t})}render(){return(0,v.jsx)(r.Fragment,{children:(0,v.jsx)(S.A,{options:this.state.options,series:this.state.series,type:"line",height:350})})}}const k=I;class O extends r.Component{render(){const{transaction:e}=this.props;return(0,v.jsx)(r.Fragment,{children:(0,v.jsxs)("div",{className:"delivery-account-orders-block p-15 mb-20",children:[(0,v.jsxs)("div",{className:"d-flex justify-content-between mb-2",children:[(0,v.jsx)("div",{children:(0,v.jsxs)("h4",{className:"mb-0 ".concat("true"===localStorage.getItem("deliveryAppLightMode")?"text-dark":"text-white"),children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),e.amount/100,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]})}),(0,v.jsx)("div",{children:"true"===localStorage.getItem("showFromNowDate")?(0,v.jsx)(p(),{fromNow:!0,children:e.created_at}):(0,v.jsx)(p(),{format:"DD/MM/YYYY hh:mma",children:e.created_at})})]}),(0,v.jsxs)("div",{className:"mb-2 float-right",children:["deposit"===e.type&&(0,v.jsx)("span",{className:"btn btn-sm delivery-wallet-deposit min-width-125",children:localStorage.getItem("walletDepositText")}),"withdraw"===e.type&&(0,v.jsx)("span",{className:"btn btn-sm delivery-wallet-withdraw min-width-125",children:localStorage.getItem("walletWithdrawText")})]}),(0,v.jsx)("div",{children:e.meta.description})]})})}}const A=O;class D extends r.Component{constructor(){super(...arguments),this.state={series:[{name:"Earnings",data:[0,0,0,0,0,0,0]}],options:{chart:{height:350,type:"line",zoom:{enabled:!1},dropShadow:{enabled:!0,top:0,left:0,blur:3,opacity:.8},toolbar:{show:!1}},dataLabels:{enabled:!1},stroke:{curve:"smooth",colors:["#18c775"]},title:{text:localStorage.getItem("deliveryLastSevenDaysEarningTitle"),align:"left",style:{color:"#222b45"}},grid:{row:{colors:["#eee","transparent"],opacity:1}},xaxis:{labels:{style:{colors:["#222b45","#222b45","#222b45","#222b45","#222b45","#222b45","#222b45"]}}},yaxis:{labels:{style:{colors:["#222b45","#222b45","#222b45","#222b45","#222b45","#222b45","#222b45"]}}},test:{name:"saurabh",age:"27"}}}}componentWillReceiveProps(e){const t=[{name:"Earnings",data:e.data.chartData}];this.setState({series:t})}render(){return(0,v.jsx)(r.Fragment,{children:(0,v.jsx)(S.A,{options:this.state.options,series:this.state.series,type:"line",height:350})})}}const T=D;class F extends r.Component{constructor(){super(...arguments),this.getRatingStars=e=>{var t="rating-green";return e<=3&&(t="rating-orange"),e<=2&&(t="rating-red"),(0,v.jsxs)("span",{className:"store-rating "+t,children:[e," ",(0,v.jsx)("i",{className:"fa fa-star text-white"})]})}}render(){return(0,v.jsx)(r.Fragment,{children:(0,v.jsxs)("div",{className:"delivery-account-orders-block p-15 mb-20",children:[(0,v.jsx)("p",{className:"mb-0",children:(0,v.jsx)("span",{className:"ml-1",children:this.getRatingStars(this.props.rating)})}),(0,v.jsx)("p",{className:"mb-2",children:this.props.review})]})})}}const C=F;var L=s(5136);class E extends r.Component{constructor(){super(...arguments),this.state={loading:!1,show_orderhistory:!0,show_earnings:!1,show_reviews:!1,show_completedOrders:!1,delivery_status:null,no_completedOrders:!1,completedOrders:[],next_page:d.zs,loading_more:!1},this.filterOnGoingOrders=()=>{this.props.updateDeliveryOrderHistory(this.props.delivery_user.data.orders.filter((e=>0===e.is_complete))),this.setState({show_orderhistory:!0,show_earnings:!1,show_completedOrders:!1}),this.removeScrollEvent(),this.setState({completedOrders:[],next_page:d.zs})},this.filterCompletedOrders=()=>{this.__getCompletedOrder(this.props.delivery_user.data.auth_token),this.setState({show_orderhistory:!1,show_earnings:!1,show_reviews:!1,show_completedOrders:!0})},this.__getCompletedOrder=e=>{this.state.loading||(this.setState({loading:!0}),this.registerScrollEvent(),c.A.post(this.state.next_page,{token:e}).then((e=>{const t=e.data,s=t.data;console.log("Next Page URL: "+t.next_page_url),s.length?this.setState({completedOrders:[...this.state.completedOrders,...s],next_page:t.next_page_url,loading:!1,loading_more:!1}):this.setState({completedOrders:[],loading:!1,loading_more:!1}),t.next_page_url||this.removeScrollEvent()})))},this.scrollFunc=()=>{if(document.documentElement.scrollTop+50+window.innerHeight>document.documentElement.offsetHeight||document.documentElement.scrollTop+50+window.innerHeight===document.documentElement.offsetHeight){const{delivery_user:e}=this.props;this.setState({loading_more:!0}),this.__getCompletedOrder(e.data.auth_token)}},this.showEarningsTable=()=>{this.setState({show_orderhistory:!1,show_earnings:!0,show_reviews:!1,show_completedOrders:!1}),this.removeScrollEvent(),this.setState({completedOrders:[],next_page:d.zs})},this.showReviews=()=>{this.setState({show_orderhistory:!1,show_earnings:!1,show_reviews:!0,show_completedOrders:!1}),this.removeScrollEvent(),this.setState({completedOrders:[],next_page:d.zs})},this.handleToggleLightDarkMode=()=>{if(null!==localStorage.getItem("deliveryAppLightMode")){new Promise((e=>{localStorage.removeItem("deliveryAppLightMode"),e("Removed Light State")})).then((()=>{window.location.reload()}))}else{new Promise((e=>{localStorage.setItem("deliveryAppLightMode","true"),e("Set Light State")})).then((()=>{window.location.reload()}))}},this.toggleDeliveryOnOffStatus=()=>{this.setState({loading:!0});const{delivery_user:e}=this.props;this.props.toggleDeliveryGuyStatus(e.data.auth_token).then((()=>{this.setState({loading:!1})}))},this.__changeStatusAndLogoutDelivery=()=>{const{delivery_user:e}=this.props;e.success&&"FoodomaaAndroidWebViewUA"===navigator.userAgent&&"undefined"!==window.Android&&window.Android.logoutDelivery(),this.props.toggleDeliveryGuyStatus(e.data.auth_token,!0).then((()=>{this.props.logoutDeliveryUser()}))}}componentDidMount(){const{delivery_user:e}=this.props;this.props.updateDeliveryUserInfo(e.data.id,e.data.auth_token),document.getElementsByTagName("body")[0].classList.remove("bg-grey")}registerScrollEvent(){window.addEventListener("scroll",this.scrollFunc)}removeScrollEvent(){window.removeEventListener("scroll",this.scrollFunc)}componentWillReceiveProps(e){this.props.delivery_user!==e.delivery_user&&this.setState({delivery_status:e.delivery_user.data.status})}componentWillUnmount(){this.removeScrollEvent()}render(){const{delivery_user:e,order_history:t}=this.props;return(0,v.jsxs)(r.Fragment,{children:[this.state.loading&&(0,v.jsx)(L.A,{}),(0,v.jsxs)("div",{className:"d-flex justify-content-between nav-dark",children:[(0,v.jsxs)("div",{className:"delivery-tab-title px-20 py-15",children:[localStorage.getItem("deliveryWelcomeMessage")," ",e.data.name]}),(0,v.jsx)("div",{className:"delivery-order-refresh",children:(0,v.jsxs)("button",{className:"btn btn-delivery-logout mr-15",onClick:()=>this.__changeStatusAndLogoutDelivery(),children:[localStorage.getItem("deliveryLogoutDelivery")," ",(0,v.jsx)("i",{className:"si si-logout"})]})})]}),(0,v.jsx)("div",{children:(0,v.jsx)("button",{onClick:this.handleToggleLightDarkMode,className:"btn btn-default btn-block btn-toggleLightDark",children:localStorage.getItem("deliveryToggleLightDarkMode")})}),(0,v.jsx)("div",{onClick:this.toggleDeliveryOnOffStatus,className:"d-flex justify-content-center my-2",children:null===this.state.delivery_status?(0,v.jsx)("div",{className:"delivery-guy-status delivery-guy-status-neutral",children:(0,v.jsx)("span",{children:(0,v.jsx)("div",{className:"spin-load"})})}):(0,v.jsx)(r.Fragment,{children:this.state.delivery_status?(0,v.jsx)("div",{className:"delivery-guy-status delivery-guy-online",children:(0,v.jsx)("span",{children:localStorage.getItem("deliveryAppYouAreOnlineBtn")})}):(0,v.jsx)("div",{className:"delivery-guy-status delivery-guy-offline",children:(0,v.jsx)("span",{children:localStorage.getItem("deliveryAppYouAreOfflineBtn")})})})}),(0,v.jsxs)("div",{className:"mb-100 pt-20",children:[(0,v.jsx)("div",{className:"pr-5",children:"true"===localStorage.getItem("deliveryAppLightMode")?(0,v.jsx)(T,{data:e.chart}):(0,v.jsx)(k,{data:e.chart})}),(0,v.jsxs)("div",{className:"row gutters-tiny px-15 mt-20",children:["true"===localStorage.getItem("enableDeliveryGuyEarning")&&(0,v.jsxs)(r.Fragment,{children:[(0,v.jsx)("div",{className:"col-6",onClick:()=>this.showEarningsTable(),children:(0,v.jsxs)("div",{className:"block shadow-light delivery-block-transparent",style:{position:"relative"},children:[(0,v.jsx)("div",{className:"block-content block-content-full clearfix text-white",children:(0,v.jsxs)("div",{className:"font-size-h3 font-w600",children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)(u.A,{to:e.data.wallet_balance,speed:1e3,className:"font-size-h3 font-w600",easing:e=>e<.5?16*e*e*e*e*e:1+16*--e*e*e*e*e,digits:2}),"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)("div",{className:"font-size-sm font-w600 text-uppercase",children:localStorage.getItem("deliveryEarningsText")})]})}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})}),(0,v.jsx)("div",{className:"col-6",onClick:()=>this.showEarningsTable(),children:(0,v.jsxs)("div",{className:"block shadow-light delivery-block-transparent",style:{position:"relative"},children:[(0,v.jsx)("div",{className:"block-content block-content-full clearfix text-white",children:(0,v.jsxs)("div",{className:"font-size-h3 font-w600",children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)(u.A,{to:e.data.totalEarnings,speed:1e3,className:"font-size-h3 font-w600",easing:e=>e<.5?16*e*e*e*e*e:1+16*--e*e*e*e*e,digits:2}),"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)("div",{className:"font-size-sm font-w600 text-uppercase",children:localStorage.getItem("deliveryTotalEarningsText")})]})}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})})]}),(0,v.jsx)("div",{className:"col-6 col-xl-3",onClick:()=>this.filterOnGoingOrders(),children:(0,v.jsxs)("div",{className:"block shadow-medium delivery-block-ongoing",style:{position:"relative"},children:[(0,v.jsxs)("div",{className:"block-content block-content-full clearfix text-white",children:[(0,v.jsx)("div",{className:"float-right mt-10",children:(0,v.jsx)("i",{className:"si si-control-forward fa-3x"})}),(0,v.jsx)(u.A,{to:e.data.onGoingCount,speed:1e3,className:"font-size-h3 font-w600",easing:e=>e<.5?16*e*e*e*e*e:1+16*--e*e*e*e*e}),(0,v.jsx)("div",{className:"font-size-sm font-w600 text-uppercase",children:localStorage.getItem("deliveryOnGoingText")})]}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})}),(0,v.jsx)("div",{className:"col-6 col-xl-3",onClick:()=>this.filterCompletedOrders(),children:(0,v.jsxs)("div",{className:"block shadow-medium delivery-block-completed",style:{position:"relative"},children:[(0,v.jsxs)("div",{className:"block-content block-content-full clearfix text-white",children:[(0,v.jsx)("div",{className:"float-right mt-10",children:(0,v.jsx)("i",{className:"si si-check fa-3x"})}),(0,v.jsx)(u.A,{to:e.data.completedCount,speed:1e3,className:"font-size-h3 font-w600",easing:e=>e<.5?16*e*e*e*e*e:1+16*--e*e*e*e*e}),(0,v.jsx)("div",{className:"font-size-sm font-w600 text-uppercase",children:localStorage.getItem("deliveryCompletedText")})]}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})}),"true"===localStorage.getItem("showDeliveryCollection")&&(0,v.jsx)("div",{className:"col",children:(0,v.jsxs)("div",{className:"block shadow-light delivery-block-transparent",style:{position:"relative"},children:[(0,v.jsx)("div",{className:"block-content block-content-full clearfix text-white",children:(0,v.jsxs)("div",{className:"font-size-h3 font-w600",children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)(u.A,{to:e.data.deliveryCollection,speed:1e3,className:"font-size-h3 font-w600",easing:e=>e<.5?16*e*e*e*e*e:1+16*--e*e*e*e*e,digits:2}),"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)("div",{className:"font-size-sm font-w600 text-uppercase",children:localStorage.getItem("deliveryCollectionText")})]})}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})}),(0,v.jsx)("div",{className:"col",onClick:()=>this.showReviews(),children:(0,v.jsxs)("div",{className:"block shadow-light delivery-block-transparent",style:{position:"relative"},children:[(0,v.jsx)("div",{className:"block-content block-content-full clearfix text-white",children:(0,v.jsxs)("div",{className:"font-size-h3 font-w600",children:[(0,v.jsx)("i",{className:"fa fa-star mr-1"}),e.data.averageRating,(0,v.jsx)("div",{className:"font-size-sm font-w600 text-uppercase",children:localStorage.getItem("reviewsPageTitle")})]})}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})})]}),this.state.show_completedOrders&&(0,v.jsx)("div",{className:"orders-history px-15 mt-20",children:this.state.completedOrders&&this.state.completedOrders.length>0?this.state.completedOrders.map((e=>(0,v.jsx)(N,{order:e},e.id))):null}),this.state.show_orderhistory&&(0,v.jsx)("div",{className:"orders-history px-15 mt-20",children:t&&t.length>0?t.map((e=>(0,v.jsx)(N,{order:e},e.id))):null}),this.state.show_earnings&&(0,v.jsx)("div",{className:"delivery-earnings px-15 mt-20",children:e.data.earnings&&e.data.earnings.map((e=>(0,v.jsx)(A,{transaction:e},e.id)))}),this.state.show_reviews&&(0,v.jsx)("div",{className:"delivery-reviews px-15 mt-20",children:e.data.ratings&&e.data.ratings.map((e=>(0,v.jsx)(C,{rating:e.rating_delivery,review:e.review_delivery},e.id)))})]})]})}}E.contextTypes={router:()=>null};const M=(0,o.Ng)((e=>({delivery_user:e.delivery_user.delivery_user,order_history:e.delivery_user.order_history})),{updateDeliveryUserInfo:h.nc,updateDeliveryOrderHistory:h.sB,toggleDeliveryGuyStatus:h.Ak})(E);var z=s(3295);class Y extends r.Component{constructor(){super(...arguments),this.__refreshOrders=()=>{this.refs.btnSpinner&&this.refs.btnSpinner.classList.add("fa-spin"),setTimeout((()=>{this.refs.btnSpinner&&this.refs.btnSpinner.classList.remove("fa-spin")}),2e3),this.props.refreshOrders()},this.getDeliveryGuyTotalEarning=e=>{let t=0;return e.commission&&(t+=parseFloat(e.commission)),e.tip_amount&&(t+=parseFloat(e.tip_amount)),t}}componentDidMount(){document.getElementsByTagName("body")[0].classList.remove("bg-grey"),document.getElementsByTagName("body")[0].classList.add("delivery-dark-bg")}render(){const{pickedup_orders:e}=this.props;return(0,v.jsx)(r.Fragment,{children:(0,v.jsxs)("div",{className:"mb-100",children:[(0,v.jsxs)("div",{className:"d-flex justify-content-between nav-dark",children:[(0,v.jsx)("div",{className:"delivery-tab-title px-20 py-15",children:localStorage.getItem("deliveryPickedupOrdersTitle")}),(0,v.jsx)("div",{className:"delivery-order-refresh",children:(0,v.jsxs)("button",{className:"btn btn-refreshOrders mr-15",onClick:this.__refreshOrders,style:{position:"relative"},children:[localStorage.getItem("deliveryOrdersRefreshBtn")," ",(0,v.jsx)("i",{ref:"btnSpinner",className:"fa fa-refresh"}),(0,v.jsx)(g(),{duration:1200})]})})]}),0===e.length?(0,v.jsx)("p",{className:"text-center text-muted py-15 mb-10 bg-white",children:localStorage.getItem("deliveryNoPickedupOrdersMsg")}):(0,v.jsx)("div",{className:"p-15",children:(0,v.jsx)("div",{className:"delivery-list-wrapper pb-20",children:e.map((e=>(0,v.jsxs)(y.N_,{to:"/delivery/orders/".concat(e.unique_order_id),style:{position:"relative"},children:[(0,v.jsxs)("div",{className:"delivery-list-item px-15 pb-5 pt-15",children:[(0,v.jsxs)("div",{className:"d-flex justify-content-between",children:[(0,v.jsx)("div",{children:(0,v.jsx)("p",{className:"m-0",children:"true"===localStorage.getItem("showFromNowDate")?(0,v.jsx)(p(),{fromNow:!0,children:e.updated_at}):(0,v.jsx)(p(),{format:"DD/MM/YYYY hh:mma",children:e.updated_at})})}),(0,v.jsx)("div",{children:"true"===localStorage.getItem("enableDeliveryGuyEarning")&&(0,v.jsxs)("p",{className:"m-0 list-delivery-commission",children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat"),(0,v.jsx)(u.A,{to:this.getDeliveryGuyTotalEarning(e),speed:1e3,digits:2}),"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]})})]}),(0,v.jsxs)("div",{className:"d-flex justify-content-between",children:[(0,v.jsx)("div",{className:"font-w700 list-delivery-store-name",children:e.restaurant.name}),(0,v.jsx)("div",{children:(0,v.jsxs)("p",{className:"m-0 font-w700",children:["#",e.unique_order_id.substr(e.unique_order_id.length-8)]})})]}),(0,v.jsx)("p",{children:"true"===localStorage.getItem("showDeliveryFullAddressOnList")?(0,v.jsx)("span",{children:e.address}):(0,v.jsxs)("span",{className:"d-flex align-items-center",children:[(0,v.jsx)("i",{className:"si si-pointer mr-2"}),(0,v.jsx)("span",{style:{maxWidth:"100%",display:"block"},className:"truncate-text",children:e.address})]})})]}),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]},e.id)))})})]})})}}const G=Y;class W extends r.Component{constructor(){super(...arguments),this.state={play:!1,tabIndex:0},this.audio=new Audio("/assets/audio/delivery-notification.mp3"),this.__refreshOrders=()=>{const{delivery_user:e}=this.props;e.success&&e.data.status&&(console.log("refresh orders called"),this.props.getDeliveryOrders(this.props.delivery_user.data.auth_token))},this.getLocationName=e=>{try{return console.log("Came to try"),(0,v.jsx)("span",{style:{maxWidth:"100px",display:"block"},className:"truncate-text",children:JSON.parse(e).address})}catch{return null}},this.onTabSelect=e=>{localStorage.setItem("deliveryTabIndex",e),this.setState({tabIndex:e})}}componentDidMount(){this.props.delivery_user.success&&this.props.getDeliveryOrders(this.props.delivery_user.data.auth_token),this.refreshSetInterval=setInterval((()=>{this.__refreshOrders()}),15e3)}componentWillReceiveProps(e){const{delivery_orders:t}=this.props;t.new_orders&&e.delivery_orders.new_orders.length>t.new_orders.length&&"FoodomaaAndroidWebViewUA"!==navigator.userAgent&&(this.audio.play(),"vibrate"in navigator&&navigator.vibrate(["100","150","100","100","150","100"]))}componentWillUnmount(){clearInterval(this.refreshSetInterval)}render(){if(window.innerWidth>768)return(0,v.jsx)(l.C5,{to:"/"});const{accepted_orders:e,new_orders:t,pickedup_orders:s}=this.props.delivery_orders;return(0,v.jsxs)(r.Fragment,{children:[(0,v.jsx)(a.A,{seotitle:"Delivery Orders",seodescription:localStorage.getItem("seoMetaDescription"),ogtype:"website",ogtitle:localStorage.getItem("seoOgTitle"),ogdescription:localStorage.getItem("seoOgDescription"),ogurl:window.location.href,twittertitle:localStorage.getItem("seoTwitterTitle"),twitterdescription:localStorage.getItem("seoTwitterDescription")}),(0,v.jsxs)(z.tU,{selectedIndex:null===localStorage.getItem("deliveryTabIndex")?this.state.tabIndex:parseInt(localStorage.getItem("deliveryTabIndex")),onSelect:e=>this.onTabSelect(e),children:[(0,v.jsx)("div",{className:"content font-size-xs clearfix footer-fixed",style:{display:"block",width:"100%",padding:"0",height:"4.6rem"},children:(0,v.jsxs)(z.wb,{children:[(0,v.jsx)(z.oz,{children:(0,v.jsxs)("div",{className:"text-center",children:[(0,v.jsx)("span",{className:"cart-quantity-badge",style:{backgroundColor:"#f44336",top:"2px",left:"45px"},children:t&&t.length}),(0,v.jsx)("i",{className:"si si-bell fa-2x"})," ",(0,v.jsx)("br",{}),localStorage.getItem("deliveryFooterNewTitle"),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})}),(0,v.jsx)(z.oz,{children:(0,v.jsxs)("div",{className:"text-center",children:[(0,v.jsx)("span",{className:"cart-quantity-badge",style:{backgroundColor:"#f44336",top:"2px",left:"45px"},children:e&&e.length}),(0,v.jsx)("i",{className:"si si-grid fa-2x"})," ",(0,v.jsx)("br",{}),localStorage.getItem("deliveryFooterAcceptedTitle"),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})}),(0,v.jsx)(z.oz,{children:(0,v.jsxs)("div",{className:"text-center",children:[(0,v.jsx)("span",{className:"cart-quantity-badge",style:{backgroundColor:"#f44336",top:"2px",left:"45px"},children:s&&s.length}),(0,v.jsx)("i",{className:"si si-bag fa-2x"})," ",(0,v.jsx)("br",{}),localStorage.getItem("deliveryFooterPickedup"),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})}),(0,v.jsx)(z.oz,{children:(0,v.jsxs)("div",{className:"text-center",children:[(0,v.jsx)("i",{className:"si si-user fa-2x"})," ",(0,v.jsx)("br",{})," ",localStorage.getItem("deliveryFooterAccount"),(0,v.jsx)(g(),{duration:"500",hasTouch:"true"})]})})]})}),(0,v.jsx)(z.Kp,{children:this.props.delivery_orders.new_orders?(0,v.jsx)(f,{refreshOrders:this.__refreshOrders,getLocationName:this.getLocationName,new_orders:this.props.delivery_orders.new_orders,delivery_user:this.props.delivery_user}):(0,v.jsx)("div",{className:"pt-50",children:(0,v.jsxs)(i.Ay,{height:window.innerHeight,width:window.innerWidth,speed:1.2,primaryColor:"true"===localStorage.getItem("deliveryAppLightMode")?"#E0E0E0":"#161b31",secondaryColor:"true"===localStorage.getItem("deliveryAppLightMode")?"#fefefe":"#222b45",children:[(0,v.jsx)("rect",{x:"15",y:"30",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"30",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"70",rx:"0",ry:"0",width:"250",height:"23"}),(0,v.jsx)("rect",{x:"15",y:"173",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"173",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"213",rx:"0",ry:"0",width:"250",height:"23"}),(0,v.jsx)("rect",{x:"15",y:"316",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"316",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"356",rx:"0",ry:"0",width:"250",height:"23"})]})})}),(0,v.jsx)(z.Kp,{children:this.props.delivery_orders.accepted_orders?(0,v.jsx)(w,{refreshOrders:this.__refreshOrders,getLocationName:this.getLocationName,accepted_orders:this.props.delivery_orders.accepted_orders}):(0,v.jsx)("div",{className:"pt-50",children:(0,v.jsxs)(i.Ay,{height:window.innerHeight,width:window.innerWidth,speed:1.2,primaryColor:"#161b31",secondaryColor:"#222b45",children:[(0,v.jsx)("rect",{x:"15",y:"30",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"30",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"70",rx:"0",ry:"0",width:"250",height:"23"}),(0,v.jsx)("rect",{x:"15",y:"173",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"173",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"213",rx:"0",ry:"0",width:"250",height:"23"}),(0,v.jsx)("rect",{x:"15",y:"316",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"316",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"356",rx:"0",ry:"0",width:"250",height:"23"})]})})}),(0,v.jsx)(z.Kp,{children:this.props.delivery_orders.pickedup_orders?(0,v.jsx)(G,{refreshOrders:this.__refreshOrders,getLocationName:this.getLocationName,pickedup_orders:this.props.delivery_orders.pickedup_orders}):(0,v.jsx)("div",{className:"pt-50",children:(0,v.jsxs)(i.Ay,{height:window.innerHeight,width:window.innerWidth,speed:1.2,primaryColor:"#161b31",secondaryColor:"#222b45",children:[(0,v.jsx)("rect",{x:"15",y:"30",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"30",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"70",rx:"0",ry:"0",width:"250",height:"23"}),(0,v.jsx)("rect",{x:"15",y:"173",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"173",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"213",rx:"0",ry:"0",width:"250",height:"23"}),(0,v.jsx)("rect",{x:"15",y:"316",rx:"0",ry:"0",width:"150",height:"30"}),(0,v.jsx)("rect",{x:"283",y:"316",rx:"0",ry:"0",width:"75",height:"30"}),(0,v.jsx)("rect",{x:"15",y:"356",rx:"0",ry:"0",width:"250",height:"23"})]})})}),(0,v.jsx)(z.Kp,{children:(0,v.jsx)(M,{delivery_user:this.props.delivery_user,logoutDeliveryUser:this.props.logoutDeliveryUser})})]})]})}}const B=(0,o.Ng)((e=>({delivery_user:e.delivery_user.delivery_user,delivery_orders:e.delivery_orders.delivery_orders})),{getDeliveryOrders:e=>t=>{c.A.post(d.gO,{token:e}).then((e=>{const s=e.data;return t({type:n._,payload:s})})).catch((function(e){console.log(e)}))},logoutDeliveryUser:h.Ud})(W)},576:(e,t,s)=>{s.d(t,{$t:()=>l,Ak:()=>c,Ud:()=>o,nc:()=>n,sB:()=>d});var r=s(3392),i=s(1753),a=s(8469);const l=(e,t)=>s=>{a.A.post(i.V4,{email:e,password:t}).then((e=>{const t=e.data;return s({type:r.Vl,payload:t})})).catch((function(e){console.log(e)}))},o=()=>e=>{e({type:r.oM,payload:[]})},n=(e,t)=>s=>{a.A.post(i.s$,{token:t,user_id:e}).then((e=>{const t={delivery_user:e.data,order_history:e.data.data.orders};return s({type:r.gw,payload:t})})).catch((function(e){console.log(e)}))},d=e=>t=>{t({type:r.v9,payload:e})},c=function(e){let t=arguments.length>1&&void 0!==arguments[1]&&arguments[1];return s=>a.A.post(i.H2,{token:e,toggle_status:!0,force_offline:t}).then((e=>{const t={delivery_user:e.data,order_history:e.data.data.orders};return s({type:r.gw,payload:t})})).catch((function(e){console.log(e)}))}}}]);