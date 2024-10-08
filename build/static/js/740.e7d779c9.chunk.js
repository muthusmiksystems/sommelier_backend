"use strict";(self.webpackChunksommelier=self.webpackChunksommelier||[]).push([[740],{9740:(e,t,s)=>{s.r(t),s.d(t,{default:()=>x});var a=s(9950),o=s(2920),i=s(9752),l=s(8858),n=s(8429),r=s(2074),d=s(72),c=s.n(d),h=s(3393),g=s(8341),m=s(5136),p=s(4414);class u extends a.Component{constructor(){super(),this.state={loading:!1,name:"",email:"",onlyPhone:"",phone:"",password:"",otp:"",accessToken:"",provider:"",error:!1,email_phone_already_used:!1,invalid_otp:!1,showResendOtp:!1,countdownStart:!1,countDownSeconds:30,enSOV:"",errorMessage:"",countryCodeSelect:"",isFoodomaaAndroidWebView:!1},this.handleInputChange=e=>{"phone"===e.target.name?(this.setState({phone:this.state.countryCodeSelect+e.target.value.replace(/^0+/,"")}),this.setState({onlyPhone:e.target.value.replace(/^0+/,"")})):this.setState({[e.target.name]:e.target.value.trim()})},this.handleCountryCodeChange=e=>{const{target:t}=e;this.setState({countryCodeSelect:t.value},(()=>{this.setState({phone:t.value+this.state.onlyPhone})}))},this.handleRegister=e=>{e.preventDefault(),this.validator.fieldValid("name")&&this.validator.fieldValid("email")&&this.validator.fieldValid("phone")&&this.validator.fieldValid("password")?(this.setState({loading:!0}),"true"===this.state.enSOV?this.props.sendOtp(this.state.email,this.state.phone,null).then((e=>{e.payload.otp||this.setState({error:!0,errorMessage:e.payload.message})})):this.props.registerUser(this.state.name,this.state.email,this.state.phone,this.state.password,this.getLocationFromLocalStorage(),null)):(console.log("Validation Failed"),this.validator.showMessages())},this.handleRegisterAfterSocialLogin=e=>{e.preventDefault(),this.setState({loading:!0}),this.validator.fieldValid("phone")?"true"===this.state.enSOV?this.props.sendOtp(this.state.email,this.state.phone,null).then((e=>{e.payload.otp||this.setState({error:!0})})):this.props.loginUser(this.state.name,this.state.email,null,this.state.accessToken,this.state.phone,this.state.provider,this.getLocationFromLocalStorage(),this.state.otp):(this.setState({loading:!1}),console.log("Validation Failed"),this.validator.showMessages())},this.resendOtp=()=>{this.validator.fieldValid("phone")&&(this.setState({countDownSeconds:15,showResendOtp:!1}),this.props.sendOtp(this.state.email,this.state.phone,null).then((e=>{e.payload.otp||this.setState({error:!0})})))},this.handleVerifyOtp=e=>{e.preventDefault(),console.log("verify otp clicked"),this.validator.fieldValid("otp")&&(this.setState({loading:!0}),this.props.verifyOtp(this.state.phone,this.state.otp))},this.handleSocialLogin=e=>{"true"===this.state.enSOV?(this.setState({name:e._profile.name,email:e._profile.email,accessToken:e._token.accessToken,provider:e._provider,social_login:!0}),this.props.sendOtp(e._profile.email,null,e._token.accessToken,e._provider).then((e=>{e.payload.otp||this.setState({error:!0})}))):(this.setState({name:e._profile.name,email:e._profile.email,accessToken:e._token.accessToken,provider:e._provider,social_login:!0}),this.props.loginUser(e._profile.name,e._profile.email,null,e._token.accessToken,null,e._provider,this.getLocationFromLocalStorage(),this.state.otp))},this.handleSocialLoginFailure=e=>{this.setState({error:!0})},this.handleCountDown=()=>{setTimeout((()=>{this.setState({showResendOtp:!0}),clearInterval(this.intervalID)}),31e3),this.intervalID=setInterval((()=>{console.log("interval going on"),this.setState({countDownSeconds:this.state.countDownSeconds-1})}),1e3)},this.processDefaultCountryCode=()=>{const e=localStorage.getItem("phoneCountryCode").split(",");return 0===e.length?(0,p.jsx)("span",{className:"country-code"}):1===e.length?(0,p.jsx)("span",{className:"country-code",children:e[0].replace(/\s/g,"")}):e.length>1?(0,p.jsx)("select",{name:"countryCodeSelect",onChange:this.handleCountryCodeChange,className:"country-code--dropdown",children:e.map((e=>(0,p.jsx)("option",{value:e.replace(/\s/g,""),children:e.replace(/\s/g,"")},e)))}):void 0},this.getLocationFromLocalStorage=()=>{const e=JSON.parse(localStorage.getItem("userSetAddress"));return null===e||e.hasOwnProperty("businessLocation")?null:e},this.validator=new(c())({autoForceUpdate:this,messages:{required:localStorage.getItem("fieldValidationMsg"),string:localStorage.getItem("nameValidationMsg"),email:localStorage.getItem("emailValidationMsg"),regex:localStorage.getItem("phoneValidationMsg"),min:localStorage.getItem("minimumLengthValidationMessage")}})}componentDidMount(){const e=localStorage.getItem("phoneCountryCode").split(",");this.setState({countryCodeSelect:e[0].replace(/\s/g,"")});const t=this.props.settings&&this.props.settings.find((e=>"enSOV"===e.key));this.setState({enSOV:t.value}),"false"===localStorage.getItem("enableFacebookLogin")&&"false"===localStorage.getItem("enableGoogleLogin")&&document.getElementById("socialLoginDiv")&&document.getElementById("socialLoginDiv").classList.add("hidden"),"true"!==localStorage.getItem("enableFacebookLogin")&&"true"!==localStorage.getItem("enableGoogleLogin")||setTimeout((()=>{this.refs.socialLogin&&this.refs.socialLogin.classList.remove("hidden"),this.refs.socialLoginLoader&&this.refs.socialLoginLoader.classList.add("hidden")}),500),"FoodomaaAndroidWebViewUA"===navigator.userAgent&&"undefined"!==window.Android&&this.setState({isFoodomaaAndroidWebView:!0})}componentWillReceiveProps(e){const{user:t}=this.props;if(t!==e.user&&this.setState({loading:!1}),e.user.success){if(null!==e.user.data.default_address){const t={lat:e.user.data.default_address.latitude,lng:e.user.data.default_address.longitude,address:e.user.data.default_address.address,house:e.user.data.default_address.house,tag:e.user.data.default_address.tag};localStorage.setItem("userSetAddress",JSON.stringify(t))}"FoodomaaAndroidWebViewUA"===navigator.userAgent&&"undefined"!==window.Android&&window.Android.registerFcm(e.user.data.auth_token)}e.user.email_phone_already_used&&this.setState({email_phone_already_used:!0}),e.user.otp&&(this.setState({email_phone_already_used:!1,error:!1}),document.getElementById("registerForm").classList.add("hidden"),document.getElementById("socialLoginDiv").classList.add("hidden"),document.getElementById("phoneFormAfterSocialLogin").classList.add("hidden"),document.getElementById("otpForm").classList.remove("hidden"),this.setState({countdownStart:!0}),this.handleCountDown(),this.validator.hideMessages()),e.user.valid_otp&&(this.setState({invalid_otp:!1,error:!1,loading:!0}),this.state.social_login?this.props.loginUser(this.state.name,this.state.email,null,this.state.accessToken,this.state.phone,this.state.provider,this.getLocationFromLocalStorage(),this.state.otp):this.props.registerUser(this.state.name,this.state.email,this.state.phone,this.state.password,this.getLocationFromLocalStorage(),this.state.otp),console.log("VALID OTP, REG USER NOW")),!1===e.user.valid_otp&&(console.log("Invalid OTP"),this.setState({invalid_otp:!0})),e.user||this.setState({error:!0}),e.user.proceed_login&&(console.log("From Social : user already exists"),this.props.loginUser(this.state.name,this.state.email,null,this.state.accessToken,null,this.state.provider,this.getLocationFromLocalStorage(),this.state.otp)),e.user.enter_phone_after_social_login&&(console.log("After Send OTP Enter Number"),this.validator.hideMessages(),document.getElementById("registerForm").classList.add("hidden"),document.getElementById("socialLoginDiv").classList.add("hidden"),document.getElementById("phoneFormAfterSocialLogin").classList.remove("hidden"),console.log("ask to fill the phone number and send otp process..."))}componentWillUnmount(){console.log("Countdown cleared"),clearInterval(this.intervalID)}render(){if("true"===localStorage.getItem("enOLnR"))return(0,p.jsx)(n.C5,{to:"/login"});if(window.innerWidth>768)return(0,p.jsx)(n.C5,{to:"/"});if(null===localStorage.getItem("storeColor"))return(0,p.jsx)(n.C5,{to:"/"});const{user:e}=this.props;return e.success?"1"===localStorage.getItem("fromCartToLogin")?(localStorage.removeItem("fromCartToLogin"),(0,p.jsx)(n.C5,{to:"/cart"})):(0,p.jsx)(n.C5,{to:"/my-account"}):(0,p.jsxs)(a.Fragment,{children:[this.state.error&&(0,p.jsx)("div",{className:"auth-error",children:(0,p.jsx)("div",{className:"error-shake",children:""!==this.state.errorMessage?this.state.errorMessage:localStorage.getItem("loginErrorMessage")})}),this.state.email_phone_already_used&&(0,p.jsx)("div",{className:"auth-error",children:(0,p.jsx)("div",{className:"error-shake",children:localStorage.getItem("emailPhoneAlreadyRegistered")})}),this.state.invalid_otp&&(0,p.jsx)("div",{className:"auth-error",children:(0,p.jsx)("div",{className:"error-shake",children:localStorage.getItem("invalidOtpMsg")})}),this.state.loading&&(0,p.jsx)(m.A,{}),(0,p.jsxs)("div",{className:"cust-auth-header",children:[(0,p.jsx)("div",{className:"input-group",children:(0,p.jsx)("div",{className:"input-group-prepend",children:(0,p.jsx)(i.A,{history:this.props.history})})}),(0,p.jsx)("img",{src:"/assets/img/various/login-illustration.png",className:"login-image pull-right",alt:"login-header"}),(0,p.jsxs)("div",{className:"login-texts px-15 mt-30 pb-20",children:[(0,p.jsx)("span",{className:"login-title",children:localStorage.getItem("registerRegisterTitle")}),(0,p.jsx)("br",{}),(0,p.jsx)("span",{className:"login-subtitle",children:localStorage.getItem("registerRegisterSubTitle")})]})]}),(0,p.jsxs)("div",{className:"bg-white",children:[(0,p.jsxs)("form",{onSubmit:this.handleRegister,id:"registerForm",children:[(0,p.jsxs)("div",{className:"form-group px-15 pt-30",children:[(0,p.jsxs)("div",{className:"col-md-9 pb-5",children:[(0,p.jsx)("input",{type:"text",name:"name",onChange:this.handleInputChange,className:"form-control auth-input",placeholder:localStorage.getItem("loginLoginNameLabel")}),this.validator.message("name",this.state.name,"required|string")]}),(0,p.jsxs)("div",{className:"col-md-9 pb-5",children:[(0,p.jsx)("input",{type:"text",name:"email",onChange:this.handleInputChange,className:"form-control auth-input",placeholder:localStorage.getItem("loginLoginEmailLabel")}),this.validator.message("email",this.state.email,"required|email")]}),(0,p.jsx)("div",{className:"col-md-9 pb-5",children:(0,p.jsxs)("div",{children:[this.processDefaultCountryCode(),(0,p.jsxs)("span",{children:[(0,p.jsx)("input",{name:"phone",type:"tel",onChange:this.handleInputChange,className:"form-control phone-number-country-code auth-input",placeholder:localStorage.getItem("loginLoginPhoneLabel")}),this.validator.message("phone",this.state.phone,["required",{regex:["^\\+[1-9]\\d{1,14}$"]},{min:["8"]}])]})]})}),(0,p.jsxs)("div",{className:"col-md-9",children:[(0,p.jsx)("input",{type:"password",name:"password",onChange:this.handleInputChange,className:"form-control auth-input",placeholder:localStorage.getItem("loginLoginPasswordLabel")}),this.validator.message("password",this.state.password,"required|min:8")]})]}),(0,p.jsx)("div",{className:"mt-20 mx-15 d-flex justify-content-center",children:(0,p.jsx)("button",{type:"submit",className:"btn btn-main",style:{backgroundColor:localStorage.getItem("storeColor"),width:"90%",borderRadius:"4px"},children:localStorage.getItem("firstScreenRegisterBtn")})})]}),(0,p.jsx)("form",{onSubmit:this.handleVerifyOtp,id:"otpForm",className:"hidden",children:(0,p.jsxs)("div",{className:"form-group px-15 pt-30",children:[(0,p.jsxs)("label",{className:"col-12 auth-input-label",children:[localStorage.getItem("otpSentMsg")," ",this.state.phone,this.validator.message("otp",this.state.otp,"required|numeric|min:4|max:6")]}),(0,p.jsx)("div",{className:"col-md-9",children:(0,p.jsx)("input",{name:"otp",type:"tel",onChange:this.handleInputChange,className:"form-control auth-input",required:!0})}),(0,p.jsx)("button",{type:"submit",className:"btn btn-main",style:{backgroundColor:localStorage.getItem("storeColor")},children:localStorage.getItem("verifyOtpBtnText")}),(0,p.jsxs)("div",{className:"mt-30 mb-10",children:[this.state.showResendOtp&&(0,p.jsxs)("div",{className:"resend-otp",onClick:this.resendOtp,children:[localStorage.getItem("resendOtpMsg")," ",this.state.phone]}),this.state.countDownSeconds>0&&(0,p.jsxs)("div",{className:"resend-otp countdown",children:[localStorage.getItem("resendOtpCountdownMsg")," ",this.state.countDownSeconds]})]})]})}),(0,p.jsx)("form",{onSubmit:this.handleRegisterAfterSocialLogin,id:"phoneFormAfterSocialLogin",className:"hidden",children:(0,p.jsxs)("div",{className:"form-group px-15 pt-30",children:[(0,p.jsxs)("label",{className:"col-12 auth-input-label",children:[localStorage.getItem("socialWelcomeText")," ",this.state.name,","]}),(0,p.jsxs)("label",{className:"col-12 auth-input-label",children:[localStorage.getItem("enterPhoneToVerify")," "]}),(0,p.jsx)("div",{className:"col-md-9 pb-5",children:(0,p.jsxs)("div",{children:[this.processDefaultCountryCode(),(0,p.jsxs)("span",{children:[(0,p.jsx)("input",{name:"phone",type:"tel",onChange:this.handleInputChange,className:"form-control phone-number-country-code auth-input"}),this.validator.message("phone",this.state.phone,["required",{regex:["^\\+[1-9]\\d{1,14}$"]},{min:["8"]}])]})]})}),(0,p.jsx)("button",{type:"submit",className:"btn btn-main",style:{backgroundColor:localStorage.getItem("storeColor")},children:localStorage.getItem("registerRegisterTitle")})]})}),!this.state.isFoodomaaAndroidWebView&&(0,p.jsxs)("div",{className:"text-center mt-3 mb-20",id:"socialLoginDiv",children:[(0,p.jsx)("p",{className:"login-or mt-2",children:"OR"}),(0,p.jsx)("div",{ref:"socialLoginLoader",children:(0,p.jsxs)(l.Ay,{height:60,width:400,speed:1.2,primaryColor:"#f3f3f3",secondaryColor:"#ecebeb",children:[(0,p.jsx)("rect",{x:"28",y:"0",rx:"0",ry:"0",width:"165",height:"45"}),(0,p.jsx)("rect",{x:"210",y:"0",rx:"0",ry:"0",width:"165",height:"45"})]})}),(0,p.jsxs)("div",{ref:"socialLogin",className:"hidden",children:["true"===localStorage.getItem("enableFacebookLogin")&&(0,p.jsx)(h.A,{provider:"facebook",appId:localStorage.getItem("facebookAppId"),onLoginSuccess:this.handleSocialLogin,onLoginFailure:()=>console.log("Failed didn't get time to init or login failed"),className:"facebook-login-button mr-2",children:(0,p.jsxs)("div",{className:"d-flex justify-content-between align-items-center",children:[(0,p.jsx)("div",{children:(0,p.jsx)("img",{src:"/assets/img/various/facebook.png",alt:"Facebook Login",className:"img-fluid",style:{width:"18px",marginRight:"10px"}})}),(0,p.jsx)("div",{style:{fontSize:"14px"},children:localStorage.getItem("facebookLoginButtonText")})]})}),"true"===localStorage.getItem("enableGoogleLogin")&&(0,p.jsx)(h.A,{provider:"google",appId:localStorage.getItem("googleAppId"),onLoginSuccess:this.handleSocialLogin,onLoginFailure:()=>console.log("Failed didn't get time to init or login failed"),className:"google-login-button",children:(0,p.jsxs)("div",{className:"d-flex justify-content-between align-items-center",children:[(0,p.jsx)("div",{children:(0,p.jsx)("img",{src:"/assets/img/various/google.png",alt:"Google",className:"img-fluid",style:{width:"18px",marginRight:"10px"}})}),(0,p.jsx)("div",{children:localStorage.getItem("googleLoginButtonText")})]})})]})]}),(0,p.jsx)("div",{children:(0,p.jsx)("div",{className:"wave-container login-bottom-wave",children:(0,p.jsxs)("svg",{viewBox:"0 0 120 28",className:"wave-svg",children:[(0,p.jsxs)("defs",{children:[(0,p.jsxs)("filter",{id:"goo",children:[(0,p.jsx)("feGaussianBlur",{in:"SourceGraphic",stdDeviation:"1",result:"blur"}),(0,p.jsx)("feColorMatrix",{in:"blur",mode:"matrix",values:"1 0 0 0 0 0 1 0 0 0 0 0 1 0 0 0 0 0 13 -9",result:"goo"}),(0,p.jsx)("xfeBlend",{in:"SourceGraphic",in2:"goo"})]}),(0,p.jsx)("path",{id:"wave",d:"M 0,10 C 30,10 30,15 60,15 90,15 90,10 120,10 150,10 150,15 180,15 210,15 210,10 240,10 v 28 h -240 z"})]}),(0,p.jsx)("use",{id:"wave3",className:"wave",xlinkHref:"#wave",x:"0",y:"-2"}),(0,p.jsx)("use",{id:"wave2",className:"wave",xlinkHref:"#wave",x:"0",y:"0"})]})})}),(0,p.jsxs)("div",{className:"text-center mt-50 mb-100 auth-login-text-block",children:[localStorage.getItem("regsiterAlreadyHaveAccount")," ",(0,p.jsx)(r.k2,{to:"/login",style:{color:localStorage.getItem("storeColor")},className:"auth-login-link",children:localStorage.getItem("firstScreenLoginBtn")})]}),"null"!==localStorage.getItem("registrationPolicyMessage")&&(0,p.jsx)("div",{className:"mt-20 mb-20 d-flex align-items-center justify-content-center",dangerouslySetInnerHTML:{__html:localStorage.getItem("registrationPolicyMessage")}})]})]})}}u.contextTypes={router:()=>null};const x=(0,g.Ng)((e=>({user:e.user.user,settings:e.settings.settings})),{registerUser:o.DY,loginUser:o.Lx,sendOtp:o.Ix,verifyOtp:o.RY})(u)},3393:(e,t,s)=>{s.d(t,{A:()=>l});s(9950);var a=s(7764),o=s.n(a),i=s(4414);const l=o()((e=>{let{children:t,triggerLogin:s,...a}=e;return(0,i.jsx)("button",{onClick:s,...a,className:a.className,children:t})}))},9752:(e,t,s)=>{s.d(t,{A:()=>r});var a=s(9950),o=s(1580),i=s.n(o),l=s(4414);class n extends a.Component{render(){return(0,l.jsx)(a.Fragment,{children:(0,l.jsxs)("button",{type:"button",className:"btn search-navs-btns back-button",style:{position:"relative"},onClick:this.context.router.history.goBack,children:[(0,l.jsx)("i",{className:"si si-arrow-left"}),(0,l.jsx)(i(),{duration:"500"})]})})}}n.contextTypes={router:()=>null};const r=n}}]);