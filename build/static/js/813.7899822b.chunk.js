/*! For license information please see 813.7899822b.chunk.js.LICENSE.txt */
(self.webpackChunksommelier=self.webpackChunksommelier||[]).push([[813],{7813:(e,n,t)=>{"use strict";t.d(n,{A:()=>D});var o=t(9950),r=t(7119),i=t(8738),l=t.n(i);var a=!1;if("undefined"!==typeof window){var u={get passive(){a=!0}};window.addEventListener("testPassive",null,u),window.removeEventListener("testPassive",null,u)}var c="undefined"!==typeof window&&window.navigator&&window.navigator.platform&&(/iP(ad|hone|od)/.test(window.navigator.platform)||"MacIntel"===window.navigator.platform&&window.navigator.maxTouchPoints>1),d=[],s=!1,v=-1,f=void 0,m=void 0,y=function(e){return d.some((function(n){return!(!n.options.allowTouchMove||!n.options.allowTouchMove(e))}))},p=function(e){var n=e||window.event;return!!y(n.target)||(n.touches.length>1||(n.preventDefault&&n.preventDefault(),!1))},h=function(){void 0!==m&&(document.body.style.paddingRight=m,m=void 0),void 0!==f&&(document.body.style.overflow=f,f=void 0)},g=function(e,n){if(e){if(!d.some((function(n){return n.targetElement===e}))){var t={targetElement:e,options:n||{}};d=[].concat(function(e){if(Array.isArray(e)){for(var n=0,t=Array(e.length);n<e.length;n++)t[n]=e[n];return t}return Array.from(e)}(d),[t]),c?(e.ontouchstart=function(e){1===e.targetTouches.length&&(v=e.targetTouches[0].clientY)},e.ontouchmove=function(n){1===n.targetTouches.length&&function(e,n){var t=e.targetTouches[0].clientY-v;!y(e.target)&&(n&&0===n.scrollTop&&t>0||function(e){return!!e&&e.scrollHeight-e.scrollTop<=e.clientHeight}(n)&&t<0?p(e):e.stopPropagation())}(n,e)},s||(document.addEventListener("touchmove",p,a?{passive:!1}:void 0),s=!0)):function(e){if(void 0===m){var n=!!e&&!0===e.reserveScrollBarGap,t=window.innerWidth-document.documentElement.clientWidth;n&&t>0&&(m=document.body.style.paddingRight,document.body.style.paddingRight=t+"px")}void 0===f&&(f=document.body.style.overflow,document.body.style.overflow="hidden")}(n)}}else console.error("disableBodyScroll unsuccessful - targetElement must be provided when calling disableBodyScroll on IOS devices.")};function b(e){let n=arguments.length>1&&void 0!==arguments[1]?arguments[1]:{isStateful:!0};const t=function(){let e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,[n,t]=o.useState(e);const{current:r}=o.useRef({current:n});return Object.defineProperty(r,"current",{get:()=>n,set:e=>{Object.is(n,e)||(n=e,t(e))}}),r}(null),r=(0,o.useRef)(null),i=n.isStateful?t:r;return o.useEffect((()=>{!e||("function"==typeof e?e(i.current):e.current=i.current)})),i}function w(){return w=Object.assign||function(e){for(var n=1;n<arguments.length;n++){var t=arguments[n];for(var o in t)Object.prototype.hasOwnProperty.call(t,o)&&(e[o]=t[o])}return e},w.apply(this,arguments)}var E=function(e){var n=e.classes,t=e.classNames,r=e.styles,i=e.id,a=e.closeIcon,u=e.onClick;return o.createElement("button",{id:i,className:l()(n.closeButton,null==t?void 0:t.closeButton),style:null==r?void 0:r.closeButton,onClick:u,"data-testid":"close-button"},a||o.createElement("svg",{className:null==t?void 0:t.closeIcon,style:null==r?void 0:r.closeIcon,width:28,height:28,viewBox:"0 0 36 36","data-testid":"close-icon"},o.createElement("path",{d:"M28.5 9.62L26.38 7.5 18 15.88 9.62 7.5 7.5 9.62 15.88 18 7.5 26.38l2.12 2.12L18 20.12l8.38 8.38 2.12-2.12L20.12 18z"})))},A="undefined"!==typeof window,C=["input","select","textarea","a[href]","button","[tabindex]","audio[controls]","video[controls]",'[contenteditable]:not([contenteditable="false"])'];function I(e){return null===e.offsetParent||"hidden"===getComputedStyle(e).visibility}function S(e){if("INPUT"!==e.tagName||"radio"!==e.type||!e.name)return!0;var n=(e.form||e.ownerDocument).querySelectorAll('input[type="radio"][name="'+e.name+'"]'),t=function(e,n){for(var t=0;t<e.length;t++)if(e[t].checked&&e[t].form===n)return e[t]}(n,e.form);return t===e||void 0===t&&n[0]===e}function k(e){for(var n=document.activeElement,t=e.querySelectorAll(C.join(",")),o=[],r=0;r<t.length;r++){var i=t[r];(n===i||!i.disabled&&O(i)>-1&&!I(i)&&S(i))&&o.push(i)}return o}function O(e){var n=parseInt(e.getAttribute("tabindex"),10);return isNaN(n)?function(e){return e.getAttribute("contentEditable")}(e)?0:e.tabIndex:n}var R=function(e){var n=e.container,t=e.initialFocusRef,r=(0,o.useRef)();return(0,o.useEffect)((function(){var e=function(e){(null==n?void 0:n.current)&&function(e,n){if(e&&"Tab"===e.key){if(!n||!n.contains)return process,!1;if(!n.contains(e.target))return!1;var t=k(n),o=t[0],r=t[t.length-1];e.shiftKey&&e.target===o?(r.focus(),e.preventDefault()):!e.shiftKey&&e.target===r&&(o.focus(),e.preventDefault())}}(e,n.current)};if(A&&document.addEventListener("keydown",e),A&&(null==n?void 0:n.current)){var o=function(){-1!==C.findIndex((function(e){var n;return null==(n=document.activeElement)?void 0:n.matches(e)}))&&(r.current=document.activeElement)};if(t)o(),requestAnimationFrame((function(){var e;null==(e=t.current)||e.focus()}));else{var i=k(n.current);i[0]&&(o(),i[0].focus())}}return function(){var n;A&&(document.removeEventListener("keydown",e),null==(n=r.current)||n.focus())}}),[n,t]),null},B=[],N=function(e){B.push(e)},x=function(e){B=B.filter((function(n){return n!==e}))},L=function(e){return!!B.length&&B[B.length-1]===e};var T=function(e,n,t,r,i){var l=(0,o.useRef)(null);(0,o.useEffect)((function(){return n&&e.current&&r&&(l.current=e.current,g(e.current,{reserveScrollBarGap:i})),function(){var e;l.current&&((e=l.current)?(d=d.filter((function(n){return n.targetElement!==e})),c?(e.ontouchstart=null,e.ontouchmove=null,s&&0===d.length&&(document.removeEventListener("touchmove",p,a?{passive:!1}:void 0),s=!1)):d.length||h()):console.error("enableBodyScroll unsuccessful - targetElement must be provided when calling enableBodyScroll on IOS devices."),l.current=null)}}),[n,t,e,r,i])},P={root:"react-responsive-modal-root",overlay:"react-responsive-modal-overlay",overlayAnimationIn:"react-responsive-modal-overlay-in",overlayAnimationOut:"react-responsive-modal-overlay-out",modalContainer:"react-responsive-modal-container",modalContainerCenter:"react-responsive-modal-containerCenter",modal:"react-responsive-modal-modal",modalAnimationIn:"react-responsive-modal-modal-in",modalAnimationOut:"react-responsive-modal-modal-out",closeButton:"react-responsive-modal-closeButton"};const D=o.forwardRef((function(e,n){var t,i,a,u,c=e.open,d=e.center,s=e.blockScroll,v=void 0===s||s,f=e.closeOnEsc,m=void 0===f||f,y=e.closeOnOverlayClick,p=void 0===y||y,h=e.container,g=e.showCloseIcon,C=void 0===g||g,I=e.closeIconId,S=e.closeIcon,k=e.focusTrapped,O=void 0===k||k,B=e.initialFocusRef,D=void 0===B?void 0:B,j=e.animationDuration,M=void 0===j?300:j,F=e.classNames,q=e.styles,G=e.role,K=void 0===G?"dialog":G,H=e.ariaDescribedby,U=e.ariaLabelledby,W=e.containerId,Y=e.modalId,z=e.onClose,J=e.onEscKeyDown,Q=e.onOverlayClick,V=e.onAnimationEnd,X=e.children,Z=e.reserveScrollBarGap,$=b(n),_=(0,o.useRef)(null),ee=(0,o.useRef)(null),ne=(0,o.useRef)(null);null===ne.current&&A&&(ne.current=document.createElement("div"));var te=(0,o.useState)(!1),oe=te[0],re=te[1];!function(e,n){(0,o.useEffect)((function(){return n&&N(e),function(){x(e)}}),[n,e])}(_,c),T(_,c,oe,v,Z);var ie=function(e){27===e.keyCode&&L(_)&&(null==J||J(e),m&&z())};(0,o.useEffect)((function(){return function(){oe&&(ne.current&&!h&&document.body.contains(ne.current)&&document.body.removeChild(ne.current),document.removeEventListener("keydown",ie))}}),[oe]),(0,o.useEffect)((function(){c&&!oe&&(re(!0),!ne.current||h||document.body.contains(ne.current)||document.body.appendChild(ne.current),document.addEventListener("keydown",ie))}),[c]);var le=function(){ee.current=!1},ae=h||ne.current,ue=c?null!=(t=null==F?void 0:F.overlayAnimationIn)?t:P.overlayAnimationIn:null!=(i=null==F?void 0:F.overlayAnimationOut)?i:P.overlayAnimationOut,ce=c?null!=(a=null==F?void 0:F.modalAnimationIn)?a:P.modalAnimationIn:null!=(u=null==F?void 0:F.modalAnimationOut)?u:P.modalAnimationOut;return oe&&ae?r.createPortal(o.createElement("div",{className:l()(P.root,null==F?void 0:F.root),style:null==q?void 0:q.root,"data-testid":"root"},o.createElement("div",{className:l()(P.overlay,null==F?void 0:F.overlay),"data-testid":"overlay","aria-hidden":!0,style:w({animation:ue+" "+M+"ms"},null==q?void 0:q.overlay)}),o.createElement("div",{ref:_,id:W,className:l()(P.modalContainer,d&&P.modalContainerCenter,null==F?void 0:F.modalContainer),style:null==q?void 0:q.modalContainer,"data-testid":"modal-container",onClick:function(e){null===ee.current&&(ee.current=!0),ee.current?(null==Q||Q(e),p&&z(),ee.current=null):ee.current=null}},o.createElement("div",{ref:$,className:l()(P.modal,null==F?void 0:F.modal),style:w({animation:ce+" "+M+"ms"},null==q?void 0:q.modal),onMouseDown:le,onMouseUp:le,onClick:le,onAnimationEnd:function(){c||re(!1),null==V||V()},id:Y,role:K,"aria-modal":"true","aria-labelledby":U,"aria-describedby":H,"data-testid":"modal",tabIndex:-1},O&&o.createElement(R,{container:$,initialFocusRef:D}),X,C&&o.createElement(E,{classes:P,classNames:F,styles:q,closeIcon:S,onClick:z,id:I})))),ae):null}))},8738:(e,n)=>{var t;!function(){"use strict";var o={}.hasOwnProperty;function r(){for(var e="",n=0;n<arguments.length;n++){var t=arguments[n];t&&(e=l(e,i(t)))}return e}function i(e){if("string"===typeof e||"number"===typeof e)return e;if("object"!==typeof e)return"";if(Array.isArray(e))return r.apply(null,e);if(e.toString!==Object.prototype.toString&&!e.toString.toString().includes("[native code]"))return e.toString();var n="";for(var t in e)o.call(e,t)&&e[t]&&(n=l(n,t));return n}function l(e,n){return n?e?e+" "+n:e+n:e}e.exports?(r.default=r,e.exports=r):void 0===(t=function(){return r}.apply(n,[]))||(e.exports=t)}()}}]);