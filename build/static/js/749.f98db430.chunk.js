"use strict";(self.webpackChunksommelier=self.webpackChunksommelier||[]).push([[749],{7749:(e,t,s)=>{s.r(t),s.d(t,{default:()=>q});var a=s(9950),r=s(3099),i=s(9666),n=s(6598),l=s(8566),o=s(6604),c=s.n(o),d=s(8858),m=s(7844),g=s(1580),h=s.n(g),u=s(2213),p=s(2074),x=s(480),j=s.n(x),y=s(3939),b=s(8341),v=s(4414);class S extends a.Component{constructor(){super(...arguments),this.forceStateUpdate=()=>{setTimeout((()=>{this.forceUpdate(),this.props.update()}),100)}}render(){const{addProduct:e,removeProduct:t,product:s,cartProducts:r,restaurant:i}=this.props;return s.quantity=1,(0,v.jsx)(a.Fragment,{children:"true"===localStorage.getItem("recommendedLayoutV2")?(0,v.jsx)("div",{className:"product-slider-item",children:(0,v.jsx)("div",{className:"block border-radius-275 recommended-item-shadow",children:(0,v.jsxs)("div",{className:"block-content recommended-item-content py-5 mb-5",style:{position:"relative",height:"17.5rem"},children:[(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)(p.N_,{to:i.slug+"/"+s.id,children:(0,v.jsx)(y.Ay,{children:(0,v.jsx)("img",{src:s.image,alt:s.name,className:"recommended-item-image"})})}),(0,v.jsx)(a.Fragment,{children:void 0!==r.find((e=>e.id===s.id))&&(0,v.jsx)(j(),{duration:150,children:(0,v.jsx)("div",{className:"quantity-badge-recommended",style:{backgroundColor:localStorage.getItem("storeColor")},children:(0,v.jsx)("span",{children:s.addon_categories&&s.addon_categories.length?(0,v.jsx)(a.Fragment,{children:(0,v.jsx)("i",{className:"si si-check",style:{lineHeight:"1.3rem"}})}):(0,v.jsx)(a.Fragment,{children:r.find((e=>e.id===s.id)).quantity})})})})})]}),(0,v.jsxs)("div",{className:"my-2 recommended-item-meta",children:[(0,v.jsx)("div",{className:"px-5 text-left recommended-v2-ellipsis-meta",children:"true"===localStorage.getItem("showVegNonVegBadge")&&null!==s.is_veg?(0,v.jsx)("div",{className:"d-flex justify-content-between align-items-center",children:s.is_veg?(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("img",{src:"/assets/img/various/veg-icon-bg.png",alt:"Veg",style:{width:"1rem",alignSelf:"center"},className:"mr-1 my-1"}),(0,v.jsx)("span",{className:"meta-name",children:s.name})]}):(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("img",{src:"/assets/img/various/non-veg-icon-bg.png",alt:"Non-Veg",style:{width:"1rem",alignSelf:"center"},className:"mr-1 my-1"}),(0,v.jsx)("span",{className:"meta-name",children:s.name})]})}):(0,v.jsx)("span",{className:"meta-name",children:s.name})}),(0,v.jsx)("div",{className:"ml-2",children:(0,v.jsx)("span",{className:"meta-price",children:"true"===localStorage.getItem("hidePriceWhenZero")&&"0.00"===s.price?(0,v.jsx)("span",{style:{height:"20px",display:"block"},children:" "}):(0,v.jsxs)(a.Fragment,{children:[s.old_price>0&&(0,v.jsxs)("span",{className:"strike-text mr-1",children:[" ","left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")," ",s.old_price,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]}),(0,v.jsxs)("span",{children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")," ",s.price,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]})]})})}),(0,v.jsx)("div",{className:"d-flex btn-group btn-group-sm my-5 btn-full justify-content-around",role:"group","aria-label":"btnGroupIcons1",style:{height:"40px"},children:s.is_active?(0,v.jsxs)(a.Fragment,{children:[s.addon_categories&&s.addon_categories.length?(0,v.jsxs)("button",{disabled:!0,type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},children:[(0,v.jsx)("span",{className:"btn-dec",children:"-"}),(0,v.jsx)(h(),{duration:"500"})]}):(0,v.jsxs)("button",{type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},onClick:()=>{t(s),this.forceStateUpdate()},children:[(0,v.jsx)("span",{className:"btn-dec",children:"-"}),(0,v.jsx)(h(),{duration:"500"})]}),s.addon_categories.length?(0,v.jsx)(m.A,{product:s,addProduct:e,update:this.props.forceStateUpdate,forceUpdate:this.forceStateUpdate}):(0,v.jsxs)("button",{type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},onClick:()=>{e(s),this.forceStateUpdate()},children:[(0,v.jsx)("span",{className:"btn-inc",children:"+"}),(0,v.jsx)(h(),{duration:"500"})]})]}):(0,v.jsx)("div",{className:"text-danger text-item-not-available d-flex align-items-center",children:localStorage.getItem("cartItemNotAvailable")})})]})]})})},s.id):(0,v.jsx)("div",{className:"col-6 p-0 d-flex justify-content-center px-5",children:(0,v.jsx)("div",{className:"block border-radius-275 recommended-item-shadow mb-3",children:(0,v.jsxs)("div",{className:"block-content recommended-item-content py-5 mb-5",style:{position:"relative",height:"17.5rem"},children:[(0,v.jsx)(p.N_,{to:i.slug+"/"+s.id,children:(0,v.jsx)("img",{src:s.image,alt:s.name,className:"recommended-item-image"})}),(0,v.jsx)(a.Fragment,{children:void 0!==r.find((e=>e.id===s.id))&&(0,v.jsx)(j(),{duration:150,children:(0,v.jsx)("div",{className:"quantity-badge-recommended",style:{backgroundColor:localStorage.getItem("storeColor")},children:(0,v.jsx)("span",{children:s.addon_categories&&s.addon_categories.length?(0,v.jsx)(a.Fragment,{children:(0,v.jsx)("i",{className:"si si-check",style:{lineHeight:"1.3rem"}})}):(0,v.jsx)(a.Fragment,{children:r.find((e=>e.id===s.id)).quantity})})})})}),(0,v.jsx)("div",{className:"my-2 recommended-item-meta",children:(0,v.jsxs)("div",{className:"px-5 text-left recommended-v2-ellipsis-meta",children:["true"===localStorage.getItem("showVegNonVegBadge")&&null!==s.is_veg?(0,v.jsx)("div",{className:"d-flex justify-content-left align-items-center",children:s.is_veg?(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("img",{src:"/assets/img/various/veg-icon-bg.png",alt:"Veg",style:{width:"1rem",alignSelf:"center"},className:"mr-1 my-1"}),(0,v.jsx)("span",{className:"meta-name",children:s.name})]}):(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("img",{src:"/assets/img/various/non-veg-icon-bg.png",alt:"Non-Veg",style:{width:"1rem",alignSelf:"center"},className:"mr-1 my-1"}),(0,v.jsx)("span",{className:"meta-name",children:s.name})]})}):(0,v.jsx)("span",{className:"meta-name",children:s.name}),(0,v.jsx)("div",{className:"ml-2",children:(0,v.jsx)("span",{className:"meta-price",children:"true"===localStorage.getItem("hidePriceWhenZero")&&"0.00"===s.price?null:(0,v.jsxs)(a.Fragment,{children:[s.old_price>0&&(0,v.jsxs)("span",{className:"strike-text mr-1",children:[" ","left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")," ",s.old_price,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]}),(0,v.jsxs)("span",{children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")," ",s.price,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]})]})})}),(0,v.jsx)("div",{className:"d-flex btn-group btn-group-sm my-5 btn-full justify-content-around",role:"group","aria-label":"btnGroupIcons1",style:{height:"40px"},children:s.is_active?(0,v.jsxs)(a.Fragment,{children:[s.addon_categories&&s.addon_categories.length?(0,v.jsxs)("button",{disabled:!0,type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},children:[(0,v.jsx)("span",{className:"btn-dec",children:"-"}),(0,v.jsx)(h(),{duration:"500"})]}):(0,v.jsxs)("button",{type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},onClick:()=>{t(s),this.forceStateUpdate()},children:[(0,v.jsx)("span",{className:"btn-dec",children:"-"}),(0,v.jsx)(h(),{duration:"500"})]}),s.addon_categories&&s.addon_categories.length?(0,v.jsx)(m.A,{product:s,addProduct:e,update:this.props.forceStateUpdate,forceUpdate:this.forceStateUpdate}):(0,v.jsxs)("button",{type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},onClick:()=>{e(s),this.forceStateUpdate()},children:[(0,v.jsx)("span",{className:"btn-inc",children:"+"}),(0,v.jsx)(h(),{duration:"500"})]})]}):(0,v.jsx)("div",{className:"text-danger text-item-not-available d-flex align-items-center",children:localStorage.getItem("cartItemNotAvailable")})})]})})]})})},s.id)})}}S.contextTypes={router:()=>null};const N=(0,b.Ng)((e=>({cartProducts:e.cart.products})),{addProduct:l.Bj,removeProduct:l.qY})(S);var f=s(8672),I=s.n(f),_=s(8730),k=s(65);class F extends a.Component{constructor(){super(...arguments),this.state={update:!0,items_backup:[],searching:!1,data:[],filterText:null,filter_items:[],items:[],queryLengthError:!1},this.forceStateUpdate=()=>{setTimeout((()=>{this.forceUpdate(),this.state.update?this.setState({update:!1}):this.setState({update:!0})}),100)},this.searchForItem=e=>{this.searchItem(e.target.value)},this.searchItem=(0,k.s)((e=>{e.length>=3?(this.setState({filterText:e}),this.props.searchItem(this.state.items,e,localStorage.getItem("itemSearchText"),localStorage.getItem("itemSearchNoResultText")),this.setState({searching:!0,queryLengthError:!1})):this.setState({queryLengthError:!0}),0===e.length&&(this.setState({filterText:null,queryLengthError:!1}),this.props.clearSearch(this.state.items_backup),this.setState({searching:!1}))}),500),this.inputFocus=()=>{this.refs.searchGroup.classList.add("search-shadow-light")},this.handleClickOutside=e=>{this.refs.searchGroup&&!this.refs.searchGroup.contains(e.target)&&this.refs.searchGroup.classList.remove("search-shadow-light")}}componentDidMount(){document.addEventListener("mousedown",this.handleClickOutside)}componentWillUnmount(){document.removeEventListener("mousedown",this.handleClickOutside)}static getDerivedStateFromProps(e,t){if(e.data!==t.data){if(null!==t.filterText)return{data:e.data};if(null===t.filterText)return{items_backup:e.data,data:e.data,filter_items:e.data.items}}if(e.restaurant_backup_items&&t.items>=0){let t=[];return e.restaurant_backup_items.hasOwnProperty("items")&&Object.keys(e.restaurant_backup_items.items).forEach((s=>{e.restaurant_backup_items.items[s].forEach((e=>{t.push(e)}))})),{items:t}}return null}shouldComponentUpdate(e,t){return t!==this.state.data}render(){const{addProduct:e,removeProduct:t,cartProducts:s,restaurant:r}=this.props,{data:i}=this.state;return(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("div",{className:"col-12 mt-10",children:(0,v.jsxs)("div",{className:"input-group",ref:"searchGroup",onClick:this.inputFocus,children:[(0,v.jsx)("input",{type:"text",className:"form-control items-search-box",placeholder:localStorage.getItem("itemSearchPlaceholder"),onChange:this.searchForItem}),(0,v.jsx)("div",{className:"input-group-append",children:(0,v.jsx)("span",{className:"input-group-text items-search-box-icon",children:(0,v.jsx)("i",{className:"si si-magnifier"})})})]})}),(0,v.jsx)("div",{children:this.state.queryLengthError&&(0,v.jsx)("div",{className:"auth-error",children:(0,v.jsx)("div",{className:"",children:localStorage.getItem("searchAtleastThreeCharsMsg")})})}),(0,v.jsxs)("div",{className:"bg-grey-light mt-20 ".concat(r&&!r.certificate?"mb-100":null),children:[!this.state.searching&&(0,v.jsxs)("div",{className:"px-5",children:[i.recommended?null:(0,v.jsxs)(d.Ay,{height:480,width:400,speed:1.2,primaryColor:"#f3f3f3",secondaryColor:"#ecebeb",children:[(0,v.jsx)("rect",{x:"10",y:"22",rx:"4",ry:"4",width:"185",height:"137"}),(0,v.jsx)("rect",{x:"10",y:"168",rx:"0",ry:"0",width:"119",height:"18"}),(0,v.jsx)("rect",{x:"10",y:"193",rx:"0",ry:"0",width:"79",height:"18"}),(0,v.jsx)("rect",{x:"212",y:"22",rx:"4",ry:"4",width:"185",height:"137"}),(0,v.jsx)("rect",{x:"212",y:"168",rx:"0",ry:"0",width:"119",height:"18"}),(0,v.jsx)("rect",{x:"212",y:"193",rx:"0",ry:"0",width:"79",height:"18"}),(0,v.jsx)("rect",{x:"10",y:"272",rx:"4",ry:"4",width:"185",height:"137"}),(0,v.jsx)("rect",{x:"10",y:"418",rx:"0",ry:"0",width:"119",height:"18"}),(0,v.jsx)("rect",{x:"10",y:"443",rx:"0",ry:"0",width:"79",height:"18"}),(0,v.jsx)("rect",{x:"212",y:"272",rx:"4",ry:"4",width:"185",height:"137"}),(0,v.jsx)("rect",{x:"212",y:"418",rx:"0",ry:"0",width:"119",height:"18"}),(0,v.jsx)("rect",{x:"212",y:"443",rx:"0",ry:"0",width:"79",height:"18"})]}),i.recommended&&i.recommended.length>0&&(0,v.jsx)("h3",{className:"px-10 py-10 recommended-text mb-0",children:localStorage.getItem("itemsPageRecommendedText")}),(0,v.jsx)("div",{className:"true"===localStorage.getItem("recommendedLayoutV2")?"product-slider":"row m-0",children:i.recommended?i.recommended.map((s=>(0,v.jsx)(N,{restaurant:r,shouldUpdate:this.state.update,update:this.forceStateUpdate,product:s,addProduct:e,removeProduct:t},s.id))):null})]}),i.items&&Object.keys(i.items).map(((n,l)=>(0,v.jsx)("div",{id:n+l,children:(0,v.jsx)(c(),{trigger:n,open:0===l||("true"===localStorage.getItem("expandAllItemMenu")||this.props.menuClicked),children:i.items[n].map((i=>(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("span",{className:"hidden",children:i.quantity=1}),(0,v.jsxs)("div",{className:"category-list-item",style:{display:"flex",justifyContent:"space-between"},children:[null!==i.image&&(0,v.jsx)(a.Fragment,{children:(0,v.jsxs)(p.N_,{to:r.slug+"/"+i.id,children:[(0,v.jsxs)(a.Fragment,{children:[this.state.searching?(0,v.jsx)("img",{src:i.image,alt:i.name,className:"flex-item-image"}):(0,v.jsx)(y.Ay,{children:(0,v.jsx)(_.A,{src:i.image,placeholder:"/assets/img/various/blank-white.jpg",children:(e,t)=>(0,v.jsx)("img",{style:{opacity:t?"0.5":"1"},src:e,alt:i.name,className:"flex-item-image"})})}),void 0!==s.find((e=>e.id===i.id))&&(0,v.jsx)(a.Fragment,{children:(0,v.jsx)("div",{style:{position:"absolute",top:"0"},children:(0,v.jsx)("div",{className:"quantity-badge-list",style:{backgroundColor:localStorage.getItem("storeColor")},children:(0,v.jsx)("span",{children:i.addon_categories.length?(0,v.jsx)(a.Fragment,{children:(0,v.jsx)("i",{className:"si si-check",style:{lineHeight:"1.3rem"}})}):(0,v.jsx)(a.Fragment,{children:s.find((e=>e.id===i.id)).quantity})})})})})]}),"true"===localStorage.getItem("showVegNonVegBadge")&&null!==i.is_veg&&(0,v.jsx)(a.Fragment,{children:i.is_veg?(0,v.jsx)("img",{src:"/assets/img/various/veg-icon-bg.png",alt:"Veg",className:"mr-1 veg-non-veg-badge"}):(0,v.jsx)("img",{src:"/assets/img/various/non-veg-icon-bg.png",alt:"Non-Veg",className:"mr-1 veg-non-veg-badge"})})]})}),(0,v.jsxs)("div",{className:null!==i.image?"flex-item-name ml-12":"flex-item-name",children:[null===i.image&&(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)(a.Fragment,{children:void 0!==s.find((e=>e.id===i.id))&&(0,v.jsx)(a.Fragment,{children:(0,v.jsx)("div",{children:(0,v.jsx)("div",{className:"quantity-badge-list--no-image",style:{backgroundColor:localStorage.getItem("storeColor")},children:(0,v.jsx)("span",{children:i.addon_categories.length?(0,v.jsx)(a.Fragment,{children:(0,v.jsx)("i",{className:"si si-check",style:{lineHeight:"1.3rem"}})}):(0,v.jsx)(a.Fragment,{children:s.find((e=>e.id===i.id)).quantity})})})})})}),(0,v.jsx)(a.Fragment,{children:"true"===localStorage.getItem("showVegNonVegBadge")&&null!==i.is_veg&&(0,v.jsx)(a.Fragment,{children:i.is_veg?(0,v.jsx)("img",{src:"/assets/img/various/veg-icon-bg.png",alt:"Veg",className:"mr-1 veg-non-veg-badge-noimage"}):(0,v.jsx)("img",{src:"/assets/img/various/non-veg-icon-bg.png",alt:"Non-Veg",className:"mr-1 veg-non-veg-badge-noimage"})})})]}),(0,v.jsx)("span",{className:"item-name",children:i.name})," ",(0,v.jsx)(u.A,{item:i}),(0,v.jsx)("span",{className:"item-price",children:"true"===localStorage.getItem("hidePriceWhenZero")&&"0.00"===i.price?null:(0,v.jsxs)(a.Fragment,{children:[i.old_price>0&&(0,v.jsxs)("span",{className:"strike-text mr-1",children:[" ","left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")," ",i.old_price,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]}),(0,v.jsxs)("span",{children:["left"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")," ",i.price,"right"===localStorage.getItem("currencySymbolAlign")&&localStorage.getItem("currencyFormat")]}),i.old_price>0&&"true"===localStorage.getItem("showPercentageDiscount")?(0,v.jsx)(a.Fragment,{children:(0,v.jsxs)("p",{className:"price-percentage-discount mb-0",style:{color:localStorage.getItem("cartColorBg")},children:[parseFloat((parseFloat(i.old_price)-parseFloat(i.price))/parseFloat(i.old_price)*100).toFixed(0),localStorage.getItem("itemPercentageDiscountText")]})}):(0,v.jsx)("br",{})]})}),null!==i.desc?(0,v.jsx)("div",{className:"item-desc-short",children:(0,v.jsx)(I(),{lines:1,more:localStorage.getItem("showMoreButtonText"),less:localStorage.getItem("showLessButtonText"),anchorclassName:"show-more ml-1",children:(0,v.jsx)("div",{dangerouslySetInnerHTML:{__html:i.desc}})})}):null]}),(0,v.jsxs)("div",{className:"item-actions pull-right pb-0",children:[(0,v.jsx)("div",{className:"btn-group btn-group-sm",role:"group","aria-label":"btnGroupIcons1",children:i.is_active?(0,v.jsxs)(a.Fragment,{children:[i.addon_categories&&i.addon_categories.length?(0,v.jsxs)("button",{disabled:!0,type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},children:[(0,v.jsx)("span",{className:"btn-dec",children:"-"}),(0,v.jsx)(h(),{duration:"500"})]}):(0,v.jsxs)("button",{type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},onClick:()=>{i.quantity=1,t(i),this.forceStateUpdate()},children:[(0,v.jsx)("span",{className:"btn-dec",children:"-"}),(0,v.jsx)(h(),{duration:"500"})]}),i.addon_categories&&i.addon_categories.length?(0,v.jsx)(m.A,{product:i,addProduct:e,forceUpdate:this.forceStateUpdate}):(0,v.jsxs)("button",{type:"button",className:"btn btn-add-remove",style:{color:localStorage.getItem("cartColor-bg")},onClick:()=>{e(i),this.forceStateUpdate()},children:[(0,v.jsx)("span",{className:"btn-inc",children:"+"}),(0,v.jsx)(h(),{duration:"500"})]})]}):(0,v.jsx)("div",{className:"text-danger text-item-not-available",children:localStorage.getItem("cartItemNotAvailable")})}),i.addon_categories&&i.addon_categories.length>0&&(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("span",{className:"customizable-item-text d-block text-center",style:{color:localStorage.getItem("storeColor")},children:localStorage.getItem("customizableItemText")}),(0,v.jsx)("br",{})]})]})]})]},i.id)))})},n))),(0,v.jsx)("div",{className:"mb-50"})]})]})}}const C=(0,b.Ng)((e=>({cartProducts:e.cart.products})),{addProduct:l.Bj,removeProduct:l.qY,searchItem:i.US,clearSearch:i.iP})(F);var w=s(2515),L=s(8429),T=s(1068),A=s(7889),P=s.n(A),U=s(513),E=s(9085);class B extends a.Component{constructor(){super(...arguments),this.state={is_active:1,loading:!0,menuListOpen:!1,menuClicked:!1},this.handleMenuOpen=()=>{this.setState({menuListOpen:!0}),document.getElementsByTagName("html")[0].classList.add("noscroll"),document.getElementsByTagName("body")[0].classList.add("noscroll")},this.handleClickOutside=e=>{this.refs.menuItemBlock&&!this.refs.menuItemBlock.contains(e.target)&&(document.getElementsByTagName("html")[0].classList.remove("noscroll"),document.getElementsByTagName("body")[0].classList.remove("noscroll"),this.setState({menuListOpen:!1}))},this.handleMenuItemClick=e=>{this.setState({menuClicked:!0});const t=document.getElementById(e.currentTarget.dataset.name);setTimeout((()=>{t.scrollIntoView(),window.scrollBy(0,-40),this.setState({menuListOpen:!1}),document.getElementsByTagName("html")[0].classList.remove("noscroll"),document.getElementsByTagName("body")[0].classList.remove("noscroll")}),this.state.menuClicked?0:500)}}componentDidMount(){this.props.getSettings(),this.props.getAllLanguages();const{user:e}=this.props;let t=e.success?this.props.getRestaurantInfoForLoggedInUser(this.props.restaurant):this.props.getRestaurantInfo(this.props.restaurant);t&&t.then((e=>{e&&(e.payload.id?this.props.getRestaurantItems(this.props.restaurant):this.context.router.history.push("/"),1===e.payload.delivery_type&&localStorage.setItem("userSelected","DELIVERY"),2===e.payload.delivery_type&&localStorage.setItem("userSelected","SELFPICKUP"),3===e.payload.delivery_type&&localStorage.getItem("userPreferredSelection"),3===e.payload.delivery_type&&localStorage.getItem("userPreferredSelection"),"undefined"===e.payload.is_active&&this.setState({loading:!0}),1!==e.payload.is_active&&0!==e.payload.is_active||(this.setState({loading:!1}),this.setState({is_active:e.payload.is_active})))})),null===localStorage.getItem("userSelected")&&localStorage.setItem("userSelected","DELIVERY"),document.addEventListener("mousedown",this.handleClickOutside)}componentWillReceiveProps(e){if(this.state.is_active||document.getElementsByTagName("html")[0].classList.add("page-inactive"),this.props.languages!==e.languages)if(localStorage.getItem("userPreferedLanguage"))this.props.getSingleLanguageData(localStorage.getItem("userPreferedLanguage"));else if(e.languages.length){const t=e.languages.filter((e=>1===e.is_default))[0].id;this.props.getSingleLanguageData(t)}}componentWillUnmount(){this.props.resetInfo(),document.removeEventListener("mousedown",this.handleClickOutside),document.getElementsByTagName("html")[0].classList.remove("page-inactive"),document.getElementsByTagName("html")[0].classList.remove("noscroll"),document.getElementsByTagName("body")[0].classList.remove("noscroll")}render(){return window.innerWidth>768?(0,v.jsx)(L.C5,{to:"/"}):(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)(w.A,{seotitle:"".concat(this.props.restaurant_info.name," | ").concat(localStorage.getItem("seoMetaTitle")),seodescription:localStorage.getItem("seoMetaDescription"),ogtype:"website",ogtitle:"".concat(this.props.restaurant_info.name," | ").concat(localStorage.getItem("seoOgTitle")),ogdescription:localStorage.getItem("seoOgDescription"),ogurl:window.location.href,twittertitle:"".concat(this.props.restaurant_info.name," | ").concat(localStorage.getItem("seoTwitterTitle")),twitterdescription:localStorage.getItem("seoTwitterDescription")}),(0,v.jsxs)("div",{children:[(0,v.jsx)(T.A,{history:this.props.history,restaurant:this.props.restaurant_info,withLinkToRestaurant:!1}),(0,v.jsx)(C,{data:this.props.restaurant_items,restaurant:this.props.restaurant_info,menuClicked:this.state.menuClicked,shouldItemsListUpdate:localStorage.getItem("cleared"),restaurant_backup_items:this.props.restaurant_backup_items})]},this.props.restaurant),this.props.restaurant_info.certificate&&(0,v.jsxs)("div",{className:"mb-100 text-center certificate-code",children:[localStorage.getItem("certificateCodeText")," ",this.props.restaurant_info.certificate]}),(0,v.jsx)("div",{children:!this.state.loading&&(0,v.jsx)(a.Fragment,{children:this.state.is_active?(0,v.jsx)(n.A,{}):(0,v.jsx)("div",{className:"auth-error no-click",children:(0,v.jsx)("div",{className:"error-shake",children:localStorage.getItem("notAcceptingOrdersMsg")})})})}),(0,v.jsx)("div",{className:"menu-list-container",children:this.state.menuListOpen?(0,v.jsxs)(a.Fragment,{children:[(0,v.jsx)("div",{className:"menu-open-backdrop"}),(0,v.jsx)("div",{className:"menu-items-block",ref:"menuItemBlock",children:(0,v.jsx)("div",{className:"menu-item-block-inner",children:this.props.restaurant_items.items&&(0,v.jsx)(a.Fragment,{children:Object.keys(this.props.restaurant_items.items).map(((e,t)=>(0,v.jsx)("div",{className:"menu-item-block-single",onClick:this.handleMenuItemClick,"data-name":e+t,children:(0,v.jsxs)(j(),{bottom:!0,duration:150*t,children:[(0,v.jsx)("div",{className:"menu-item-block-single-name",children:e}),(0,v.jsx)("div",{className:"menu-item-block-single-quantity",children:Object.keys(this.props.restaurant_items.items[e]).length})]})},e)))})})})]}):(0,v.jsx)("div",{className:"menu-button-block-main",onClick:this.handleMenuOpen,style:{bottom:this.props.cartTotal.productQuantity>0?"5rem":"2rem"},children:(0,v.jsx)(P(),{bottom:!0,children:(0,v.jsxs)("button",{className:"btn btn-menu-list",style:{backgroundColor:localStorage.getItem("storeColor")},children:[(0,v.jsx)("i",{className:"si si-list mr-1"})," ",localStorage.getItem("itemsMenuButtonText"),(0,v.jsx)(h(),{duration:"500",hasTouch:!1})]})})})})]})}}B.contextTypes={router:()=>null};const O=(0,b.Ng)((e=>({restaurant_info:e.items.restaurant_info,restaurant_items:e.items.restaurant_items,cartTotal:e.total.data,settings:e.settings.settings,languages:e.languages.languages,language:e.languages.language,user:e.user.user,restaurant_backup_items:e.items.restaurant_backup_items})),{getRestaurantInfo:i.gX,getRestaurantItems:i.K,getSettings:U.m,getAllLanguages:E.N,getSingleLanguageData:E.l,getRestaurantInfoForLoggedInUser:i.NA,resetInfo:i.AF})(B);class V extends a.Component{render(){return(0,v.jsx)(a.Fragment,{children:window.innerWidth>=768?(0,v.jsx)(r.A,{restaurant:this.props.match.params.restaurant}):(0,v.jsx)(O,{restaurant:this.props.match.params.restaurant,history:this.props.history})})}}const q=V},65:(e,t,s)=>{s.d(t,{s:()=>a});const a=(e,t)=>{let s;return function(){clearTimeout(s),s=setTimeout((()=>e.apply(this,arguments)),t)}}}}]);