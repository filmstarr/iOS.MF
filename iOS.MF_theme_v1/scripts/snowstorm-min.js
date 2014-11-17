/* Source: https://github.com/scottschiller/snowstorm/ */
/** @license

 DHTML Snowstorm! JavaScript-based snow for web pages
 Making it snow on the internets since 2003. You're welcome.
 -----------------------------------------------------------
 Version 1.44.20131215 (Previous rev: 1.44.20131208)
 Copyright (c) 2007, Scott Schiller. All rights reserved.
 Code provided under the BSD License
 http://schillmania.com/projects/snowstorm/license.txt
*/
var snowStorm=function(e,t){function i(e,t){return isNaN(t)&&(t=0),Math.random()*e+t}function s(e){return 1===parseInt(i(2),10)?-1*e:e}function n(){e.setTimeout(function(){a.start(!0)},20),a.events.remove(r?t:e,"mousemove",n)}function l(){a.excludeMobile&&f||n(),a.events.remove(e,"load",l)}this.autoStart=!0,this.excludeMobile=!0,this.flakesMax=128,this.flakesMaxActive=64,this.animationInterval=33,this.useGPU=!0,this.className=null,this.excludeMobile=!0,this.flakeBottom=null,this.followMouse=!0,this.snowColor="#fff",this.snowCharacter="&bull;",this.snowStick=!0,this.targetElement=null,this.useMeltEffect=!0,this.useTwinkleEffect=!1,this.usePositionFixed=!1,this.usePixelPosition=!1,this.freezeOnBlur=!0,this.flakeLeftOffset=0,this.flakeRightOffset=0,this.flakeWidth=8,this.flakeHeight=8,this.vMaxX=5,this.vMaxY=4,this.zIndex=0;var o,a=this,r=navigator.userAgent.match(/msie/i),m=navigator.userAgent.match(/msie 6/i),f=navigator.userAgent.match(/mobile|opera m(ob|in)/i),h=r&&"BackCompat"===t.compatMode,u=h||m,c=null,d=null,v=null,p=null,y=null,k=null,g=null,x=1,w=2,F=6,b=!1,z=!1,E=function(){try{t.createElement("div").style.opacity="0.5"}catch(e){return!1}return!0}(),H=!1,M=t.createDocumentFragment();return o=function(){function i(t){e.setTimeout(t,1e3/(a.animationInterval||20))}function s(e){var t=o.style[e];return void 0!==t?e:null}var n,l=e.requestAnimationFrame||e.webkitRequestAnimationFrame||e.mozRequestAnimationFrame||e.oRequestAnimationFrame||e.msRequestAnimationFrame||i;n=l?function(){return l.apply(e,arguments)}:null;var o;o=t.createElement("div");var r={transform:{ie:s("-ms-transform"),moz:s("MozTransform"),opera:s("OTransform"),webkit:s("webkitTransform"),w3:s("transform"),prop:null},getAnimationFrame:n};return r.transform.prop=r.transform.w3||r.transform.moz||r.transform.webkit||r.transform.ie||r.transform.opera,o=null,r}(),this.timer=null,this.flakes=[],this.disabled=!1,this.active=!1,this.meltFrameCount=20,this.meltFrames=[],this.setXY=function(e,t,i){return e?void(a.usePixelPosition||z?(e.style.left=t-a.flakeWidth+"px",e.style.top=i-a.flakeHeight+"px"):u?(e.style.right=100-t/c*100+"%",e.style.top=Math.min(i,y-a.flakeHeight)+"px"):a.flakeBottom?(e.style.right=100-t/c*100+"%",e.style.top=Math.min(i,y-a.flakeHeight)+"px"):(e.style.right=100-t/c*100+"%",e.style.bottom=100-i/v*100+"%")):!1},this.events=function(){function t(e){var t=o.call(e),i=t.length;return l?(t[1]="on"+t[1],i>3&&t.pop()):3===i&&t.push(!1),t}function i(e,t){var i=e.shift(),s=[a[t]];l?i[s](e[0],e[1]):i[s].apply(i,e)}function s(){i(t(arguments),"add")}function n(){i(t(arguments),"remove")}var l=!e.addEventListener&&e.attachEvent,o=Array.prototype.slice,a={add:l?"attachEvent":"addEventListener",remove:l?"detachEvent":"removeEventListener"};return{add:s,remove:n}}(),this.randomizeWind=function(){var e;if(k=s(i(a.vMaxX,.2)),g=i(a.vMaxY,.2),this.flakes)for(e=0;e<this.flakes.length;e++)this.flakes[e].active&&this.flakes[e].setVelocities()},this.scrollHandler=function(){var i;if(p=a.flakeBottom?0:parseInt(e.scrollY||t.documentElement.scrollTop||(u?t.body.scrollTop:0),10),isNaN(p)&&(p=0),!b&&!a.flakeBottom&&a.flakes)for(i=0;i<a.flakes.length;i++)0===a.flakes[i].active&&a.flakes[i].stick()},this.resizeHandler=function(){e.innerWidth||e.innerHeight?(c=e.innerWidth-16-a.flakeRightOffset,v=a.flakeBottom||e.innerHeight):(c=(t.documentElement.clientWidth||t.body.clientWidth||t.body.scrollWidth)-(r?0:8)-a.flakeRightOffset,v=a.flakeBottom||t.documentElement.clientHeight||t.body.clientHeight||t.body.scrollHeight),y=t.body.offsetHeight,d=parseInt(c/2,10)},this.resizeHandlerAlt=function(){c=a.targetElement.offsetWidth-a.flakeRightOffset,v=a.flakeBottom||a.targetElement.offsetHeight,d=parseInt(c/2,10),y=t.body.offsetHeight},this.freeze=function(){return a.disabled?!1:(a.disabled=1,void(a.timer=null))},this.resume=function(){return a.disabled?(a.disabled=0,void a.timerInit()):!1},this.toggleSnow=function(){a.flakes.length?(a.active=!a.active,a.active?(a.show(),a.resume()):(a.stop(),a.freeze())):a.start()},this.stop=function(){var i;for(this.freeze(),i=0;i<this.flakes.length;i++)this.flakes[i].o.style.display="none";a.events.remove(e,"scroll",a.scrollHandler),a.events.remove(e,"resize",a.resizeHandler),a.freezeOnBlur&&(r?(a.events.remove(t,"focusout",a.freeze),a.events.remove(t,"focusin",a.resume)):(a.events.remove(e,"blur",a.freeze),a.events.remove(e,"focus",a.resume)))},this.show=function(){var i;for(i=0;i<this.flakes.length;i++)this.flakes[i].o.style.display="block";a.events.add(e,"resize",a.resizeHandler),a.events.add(e,"scroll",a.scrollHandler),a.freezeOnBlur&&(r?(a.events.add(t,"focusout",a.freeze),a.events.add(t,"focusin",a.resume)):(a.events.add(e,"blur",a.freeze),a.events.add(e,"focus",a.resume)))},this.SnowFlake=function(e,s,n){var l=this;this.type=e,this.x=s||parseInt(i(c-20),10),this.y=isNaN(n)?-i(v)-12:n,this.vX=null,this.vY=null,this.vAmpTypes=[1,1.2,1.4,1.6,1.8],this.vAmp=this.vAmpTypes[this.type]||1,this.melting=!1,this.meltFrameCount=a.meltFrameCount,this.meltFrames=a.meltFrames,this.meltFrame=0,this.twinkleFrame=0,this.active=1,this.fontSize=10+this.type/5*10,this.o=t.createElement("div"),this.o.innerHTML=a.snowCharacter,a.className&&this.o.setAttribute("class",a.className),this.o.style.color=a.snowColor,this.o.style.position=b?"fixed":"absolute",a.useGPU&&o.transform.prop&&(this.o.style[o.transform.prop]="translate3d(0px, 0px, 0px)"),this.o.style.width=a.flakeWidth+"px",this.o.style.height=a.flakeHeight+"px",this.o.style.fontFamily="arial,verdana",this.o.style.cursor="default",this.o.style.overflow="hidden",this.o.style.fontWeight="normal",this.o.style.zIndex=a.zIndex,M.appendChild(this.o),this.refresh=function(){return isNaN(l.x)||isNaN(l.y)?!1:void a.setXY(l.o,l.x,l.y)},this.stick=function(){u||a.targetElement!==t.documentElement&&a.targetElement!==t.body?l.o.style.top=v+p-a.flakeHeight+"px":a.flakeBottom?l.o.style.top=a.flakeBottom+"px":(l.o.style.display="none",l.o.style.top="auto",l.o.style.bottom="0%",l.o.style.position="fixed",l.o.style.display="block")},this.vCheck=function(){l.vX>=0&&l.vX<.2?l.vX=.2:l.vX<0&&l.vX>-.2&&(l.vX=-.2),l.vY>=0&&l.vY<.2&&(l.vY=.2)},this.move=function(){var e,t=l.vX*x;l.x+=t,l.y+=l.vY*l.vAmp,l.x>=c||c-l.x<a.flakeWidth?l.x=0:0>t&&l.x-a.flakeLeftOffset<-a.flakeWidth&&(l.x=c-a.flakeWidth-1),l.refresh(),e=v+p-l.y+a.flakeHeight,e<a.flakeHeight?(l.active=0,a.snowStick?l.stick():l.recycle()):(a.useMeltEffect&&l.active&&l.type<3&&!l.melting&&Math.random()>.998&&(l.melting=!0,l.melt()),a.useTwinkleEffect&&(l.twinkleFrame<0?Math.random()>.97&&(l.twinkleFrame=parseInt(8*Math.random(),10)):(l.twinkleFrame--,E?l.o.style.opacity=l.twinkleFrame&&l.twinkleFrame%2===0?0:1:l.o.style.visibility=l.twinkleFrame&&l.twinkleFrame%2===0?"hidden":"visible")))},this.animate=function(){l.move()},this.setVelocities=function(){l.vX=k+i(.12*a.vMaxX,.1),l.vY=g+i(.12*a.vMaxY,.1)},this.setOpacity=function(e,t){return E?void(e.style.opacity=t):!1},this.melt=function(){a.useMeltEffect&&l.melting&&l.meltFrame<l.meltFrameCount?(l.setOpacity(l.o,l.meltFrames[l.meltFrame]),l.o.style.fontSize=l.fontSize-l.fontSize*(l.meltFrame/l.meltFrameCount)+"px",l.o.style.lineHeight=a.flakeHeight+2+.75*a.flakeHeight*(l.meltFrame/l.meltFrameCount)+"px",l.meltFrame++):l.recycle()},this.recycle=function(){l.o.style.display="none",l.o.style.position=b?"fixed":"absolute",l.o.style.bottom="auto",l.setVelocities(),l.vCheck(),l.meltFrame=0,l.melting=!1,l.setOpacity(l.o,1),l.o.style.padding="0px",l.o.style.margin="0px",l.o.style.fontSize=l.fontSize+"px",l.o.style.lineHeight=a.flakeHeight+2+"px",l.o.style.textAlign="center",l.o.style.verticalAlign="baseline",l.x=parseInt(i(c-a.flakeWidth-20),10),l.y=parseInt(-1*i(v),10)-a.flakeHeight,l.refresh(),l.o.style.display="block",l.active=1},this.recycle(),this.refresh()},this.snow=function(){var e,t,s=0,n=null;for(e=0,t=a.flakes.length;t>e;e++)1===a.flakes[e].active&&(a.flakes[e].move(),s++),a.flakes[e].melting&&a.flakes[e].melt();s<a.flakesMaxActive&&(n=a.flakes[parseInt(i(a.flakes.length),10)],0===n.active&&(n.melting=!0)),a.timer&&o.getAnimationFrame(a.snow)},this.mouseMove=function(e){if(!a.followMouse)return!0;var t=parseInt(e.clientX,10);d>t?x=-w+t/d*w:(t-=d,x=t/d*w)},this.createSnow=function(e,t){var s;for(s=0;e>s;s++)a.flakes[a.flakes.length]=new a.SnowFlake(parseInt(i(F),10)),(t||s>a.flakesMaxActive)&&(a.flakes[a.flakes.length-1].active=-1);a.targetElement.appendChild(M)},this.timerInit=function(){a.timer=!0,a.snow()},this.init=function(){var i;for(i=0;i<a.meltFrameCount;i++)a.meltFrames.push(1-i/a.meltFrameCount);a.randomizeWind(),a.createSnow(a.flakesMax),a.events.add(e,"resize",a.resizeHandler),a.events.add(e,"scroll",a.scrollHandler),a.freezeOnBlur&&(r?(a.events.add(t,"focusout",a.freeze),a.events.add(t,"focusin",a.resume)):(a.events.add(e,"blur",a.freeze),a.events.add(e,"focus",a.resume))),a.resizeHandler(),a.scrollHandler(),a.followMouse&&a.events.add(r?t:e,"mousemove",a.mouseMove),a.animationInterval=Math.max(20,a.animationInterval),a.timerInit()},this.start=function(i){if(H){if(i)return!0}else H=!0;if("string"==typeof a.targetElement){var s=a.targetElement;if(a.targetElement=t.getElementById(s),!a.targetElement)throw new Error('Snowstorm: Unable to get targetElement "'+s+'"')}if(a.targetElement||(a.targetElement=t.body||t.documentElement),a.targetElement!==t.documentElement&&a.targetElement!==t.body&&(a.resizeHandler=a.resizeHandlerAlt,a.usePixelPosition=!0),a.resizeHandler(),a.usePositionFixed=a.usePositionFixed&&!u&&!a.flakeBottom,e.getComputedStyle)try{z="relative"===e.getComputedStyle(a.targetElement,null).getPropertyValue("position")}catch(n){z=!1}b=a.usePositionFixed,c&&v&&!a.disabled&&(a.init(),a.active=!0)},a.autoStart&&a.events.add(e,"load",l,!1),this}(window,document);