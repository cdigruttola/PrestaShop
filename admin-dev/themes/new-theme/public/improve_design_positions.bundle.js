/******/!function(e){// webpackBootstrap
/******/
function n(e){/******/
delete installedChunks[e]}function o(e){var n=document.getElementsByTagName("head")[0],o=document.createElement("script");o.type="text/javascript",o.charset="utf-8",o.src=h.p+""+e+"."+w+".hot-update.js",n.appendChild(o)}function t(){return new Promise(function(e,n){if("undefined"==typeof XMLHttpRequest)return n(new Error("No browser support"));try{var o=new XMLHttpRequest,t=h.p+""+w+".hot-update.json";o.open("GET",t,!0),o.timeout=1e4,o.send(null)}catch(e){return n(e)}o.onreadystatechange=function(){if(4===o.readyState)if(0===o.status)n(new Error("Manifest request to "+t+" timed out."));else if(404===o.status)e();else if(200!==o.status&&304!==o.status)n(new Error("Manifest request to "+t+" failed."));else{try{var i=JSON.parse(o.responseText)}catch(e){return void n(e)}e(i)}}})}function i(e){var n=E[e];if(!n)return h;var o=function(o){return n.hot.active?(E[o]?E[o].parents.indexOf(e)<0&&E[o].parents.push(e):(b=[e],v=o),n.children.indexOf(o)<0&&n.children.push(o)):b=[],h(o)};for(var t in h)Object.prototype.hasOwnProperty.call(h,t)&&"e"!==t&&Object.defineProperty(o,t,function(e){return{configurable:!0,enumerable:!0,get:function(){return h[e]},set:function(n){h[e]=n}}}(t));return o.e=function(e){function n(){x--,"prepare"===_&&(j[e]||d(e),0===x&&0===P&&p())}return"ready"===_&&c("prepare"),x++,h.e(e).then(n,function(e){throw n(),e})},o}function r(e){var n={_acceptedDependencies:{},_declinedDependencies:{},_selfAccepted:!1,_selfDeclined:!1,_disposeHandlers:[],_main:v!==e,active:!0,accept:function(e,o){if(void 0===e)n._selfAccepted=!0;else if("function"==typeof e)n._selfAccepted=e;else if("object"==typeof e)for(var t=0;t<e.length;t++)n._acceptedDependencies[e[t]]=o||function(){};else n._acceptedDependencies[e]=o||function(){}},decline:function(e){if(void 0===e)n._selfDeclined=!0;else if("object"==typeof e)for(var o=0;o<e.length;o++)n._declinedDependencies[e[o]]=!0;else n._declinedDependencies[e]=!0},dispose:function(e){n._disposeHandlers.push(e)},addDisposeHandler:function(e){n._disposeHandlers.push(e)},removeDisposeHandler:function(e){var o=n._disposeHandlers.indexOf(e);o>=0&&n._disposeHandlers.splice(o,1)},check:l,apply:u,status:function(e){if(!e)return _;O.push(e)},addStatusHandler:function(e){O.push(e)},removeStatusHandler:function(e){var n=O.indexOf(e);n>=0&&O.splice(n,1)},data:$[e]};return v=void 0,n}function c(e){_=e;for(var n=0;n<O.length;n++)O[n].call(null,e)}function a(e){return+e+""===e?+e:e}function l(e){if("idle"!==_)throw new Error("check() is only allowed in idle status");return k=e,c("check"),t().then(function(e){if(!e)return c("idle"),null;L={},j={},D=e.c,y=e.h,c("prepare");var n=new Promise(function(e,n){m={resolve:e,reject:n}});g={};return d(8),"prepare"===_&&0===x&&0===P&&p(),n})}function s(e,n){if(D[e]&&L[e]){L[e]=!1;for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(g[o]=n[o]);0==--P&&0===x&&p()}}function d(e){D[e]?(L[e]=!0,P++,o(e)):j[e]=!0}function p(){c("ready");var e=m;if(m=null,e)if(k)u(k).then(function(n){e.resolve(n)},function(n){e.reject(n)});else{var n=[];for(var o in g)Object.prototype.hasOwnProperty.call(g,o)&&n.push(a(o));e.resolve(n)}}function u(o){function t(e,n){for(var o=0;o<n.length;o++){var t=n[o];e.indexOf(t)<0&&e.push(t)}}if("ready"!==_)throw new Error("apply() is only allowed in ready status");o=o||{};var i,r,l,s,d,p={},u=[],f={},v=function(){};for(var m in g)if(Object.prototype.hasOwnProperty.call(g,m)){d=a(m);var k;k=g[m]?function(e){for(var n=[e],o={},i=n.slice().map(function(e){return{chain:[e],id:e}});i.length>0;){var r=i.pop(),c=r.id,a=r.chain;if((s=E[c])&&!s.hot._selfAccepted){if(s.hot._selfDeclined)return{type:"self-declined",chain:a,moduleId:c};if(s.hot._main)return{type:"unaccepted",chain:a,moduleId:c};for(var l=0;l<s.parents.length;l++){var d=s.parents[l],p=E[d];if(p){if(p.hot._declinedDependencies[c])return{type:"declined",chain:a.concat([d]),moduleId:c,parentId:d};n.indexOf(d)>=0||(p.hot._acceptedDependencies[c]?(o[d]||(o[d]=[]),t(o[d],[c])):(delete o[d],n.push(d),i.push({chain:a.concat([d]),id:d})))}}}}return{type:"accepted",moduleId:e,outdatedModules:n,outdatedDependencies:o}}(d):{type:"disposed",moduleId:m};var S=!1,O=!1,P=!1,x="";switch(k.chain&&(x="\nUpdate propagation: "+k.chain.join(" -> ")),k.type){case"self-declined":o.onDeclined&&o.onDeclined(k),o.ignoreDeclined||(S=new Error("Aborted because of self decline: "+k.moduleId+x));break;case"declined":o.onDeclined&&o.onDeclined(k),o.ignoreDeclined||(S=new Error("Aborted because of declined dependency: "+k.moduleId+" in "+k.parentId+x));break;case"unaccepted":o.onUnaccepted&&o.onUnaccepted(k),o.ignoreUnaccepted||(S=new Error("Aborted because "+d+" is not accepted"+x));break;case"accepted":o.onAccepted&&o.onAccepted(k),O=!0;break;case"disposed":o.onDisposed&&o.onDisposed(k),P=!0;break;default:throw new Error("Unexception type "+k.type)}if(S)return c("abort"),Promise.reject(S);if(O){f[d]=g[d],t(u,k.outdatedModules);for(d in k.outdatedDependencies)Object.prototype.hasOwnProperty.call(k.outdatedDependencies,d)&&(p[d]||(p[d]=[]),t(p[d],k.outdatedDependencies[d]))}P&&(t(u,[k.moduleId]),f[d]=v)}var j=[];for(r=0;r<u.length;r++)d=u[r],E[d]&&E[d].hot._selfAccepted&&j.push({module:d,errorHandler:E[d].hot._selfAccepted});c("dispose"),Object.keys(D).forEach(function(e){!1===D[e]&&n(e)});for(var L,H=u.slice();H.length>0;)if(d=H.pop(),s=E[d]){var M={},I=s.hot._disposeHandlers;for(l=0;l<I.length;l++)(i=I[l])(M);for($[d]=M,s.hot.active=!1,delete E[d],l=0;l<s.children.length;l++){var A=E[s.children[l]];A&&((L=A.parents.indexOf(d))>=0&&A.parents.splice(L,1))}}var T,C;for(d in p)if(Object.prototype.hasOwnProperty.call(p,d)&&(s=E[d]))for(C=p[d],l=0;l<C.length;l++)T=C[l],(L=s.children.indexOf(T))>=0&&s.children.splice(L,1);c("apply"),w=y;for(d in f)Object.prototype.hasOwnProperty.call(f,d)&&(e[d]=f[d]);var F=null;for(d in p)if(Object.prototype.hasOwnProperty.call(p,d)){s=E[d],C=p[d];var U=[];for(r=0;r<C.length;r++)T=C[r],i=s.hot._acceptedDependencies[T],U.indexOf(i)>=0||U.push(i);for(r=0;r<U.length;r++){i=U[r];try{i(C)}catch(e){o.onErrored&&o.onErrored({type:"accept-errored",moduleId:d,dependencyId:C[r],error:e}),o.ignoreErrored||F||(F=e)}}}for(r=0;r<j.length;r++){var q=j[r];d=q.module,b=[d];try{h(d)}catch(e){if("function"==typeof q.errorHandler)try{q.errorHandler(e)}catch(n){o.onErrored&&o.onErrored({type:"self-accept-error-handler-errored",moduleId:d,error:n,orginalError:e}),o.ignoreErrored||F||(F=n),F||(F=e)}else o.onErrored&&o.onErrored({type:"self-accept-errored",moduleId:d,error:e}),o.ignoreErrored||F||(F=e)}}return F?(c("fail"),Promise.reject(F)):(c("idle"),new Promise(function(e){e(u)}))}function h(n){if(E[n])return E[n].exports;var o=E[n]={i:n,l:!1,exports:{},hot:r(n),parents:(S=b,b=[],S),children:[]};return e[n].call(o.exports,o,o.exports,i(n)),o.l=!0,o.exports}var f=this.webpackHotUpdate;this.webpackHotUpdate=function(e,n){s(e,n),f&&f(e,n)};var v,m,g,y,k=!0,w="c6250a557b645acc135f",$={},b=[],S=[],O=[],_="idle",P=0,x=0,j={},L={},D={},E={};h.m=e,h.c=E,h.i=function(e){return e},h.d=function(e,n,o){h.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:o})},h.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return h.d(n,"a",n),n},h.o=function(e,n){return Object.prototype.hasOwnProperty.call(e,n)},h.p="",h.h=function(){return w},i(369)(h.s=369)}({179:function(e,n,o){"use strict";Object.defineProperty(n,"__esModule",{value:!0});var t=o(219),i=window.$;i(function(){new t.a,i("#hook-module-form").find('select[name="id_module"]').change(function(){var e=i(this),n=i("select[name='id_hook']");0!==e.val()&&(n.find("option").remove(),i.ajax({type:"POST",url:"index.php",async:!0,dataType:"json",data:{action:"getPossibleHookingListForModule",tab:"AdminModulesPositions",ajax:1,module_id:e.val(),token:token},success:function(e){if(e.hasError){var o="";for(var t in e.errors)"indexOf"!=t&&(o+=i("<div />").html(e.errors[t]).text()+"\n")}else{for(var r=0;r<e.length;r++){var c="";""!=e[r].description&&(c=" ("+e[r].description+")"),n.append('<option value="'+e[r].id_hook+'">'+e[r].name+c+"</option>")}n.prop("disabled",!1)}}}))})})},219:function(e,n,o){"use strict";function t(e,n){if(!(e instanceof n))throw new TypeError("Cannot call a class as a function")}var i=function(){function e(e,n){for(var o=0;o<n.length;o++){var t=n[o];t.enumerable=t.enumerable||!1,t.configurable=!0,"value"in t&&(t.writable=!0),Object.defineProperty(e,t.key,t)}}return function(n,o,t){return o&&e(n.prototype,o),t&&e(n,t),n}}(),r=window.$,c=function(){function e(){if(t(this,e),0!==r("#position-filters").length){var n=this;n.$panelSelection=r("#modules-position-selection-panel"),n.$panelSelectionSingleSelection=r("#modules-position-single-selection"),n.$panelSelectionMultipleSelection=r("#modules-position-multiple-selection"),n.$panelSelectionOriginalY=n.$panelSelection.offset().top,n.$panelSelectionOriginalYTopMargin=140,n.$showModules=r("#show-modules"),n.$modulesList=r(".modules-position-checkbox"),n.$hookPosition=r("#hook-position"),n.$hookSearch=r("#hook-search"),n.$modulePositionsForm=r("#module-positions-form"),n.handleList(),n.handleSortable(),r('input[name="form[general][enable_tos]"]').on("change",function(){return n.handle()})}}return i(e,[{key:"handleList",value:function(){var e=this,n=this;r(window).on("scroll",function(){var e=r(window).scrollTop();n.$panelSelection.css("top",e<20?0:e-n.$panelSelectionOriginalY+n.$panelSelectionOriginalYTopMargin)}),n.$modulesList.on("change",function(){var e=n.$modulesList.filter(":checked").length;0===e?(n.$panelSelection.hide(),n.$panelSelectionSingleSelection.hide(),n.$panelSelectionMultipleSelection.hide()):1===e?(n.$panelSelection.show(),n.$panelSelectionSingleSelection.show(),n.$panelSelectionMultipleSelection.hide()):(n.$panelSelection.show(),n.$panelSelectionSingleSelection.hide(),n.$panelSelectionMultipleSelection.show(),r("#modules-position-selection-count").html(e))}),n.$panelSelection.find("button").click(function(){r('button[name="unhookform"]').trigger("click")}),n.$hooksList=[],r("section.hook-panel .hook-name").each(function(){var e=r(this);n.$hooksList.push({title:e.html(),element:e,container:e.parents(".hook-panel")})}),n.$showModules.select2(),n.$showModules.on("change",function(){e.modulesPositionFilterHooks()}),n.$hookPosition.on("change",function(){e.modulesPositionFilterHooks()}),n.$hookSearch.on("input",function(){e.modulesPositionFilterHooks()}),r(".hook-checker").on("click",function(){r(".hook"+r(this).data("hook-id")).prop("checked",r(this).prop("checked"))})}},{key:"handleSortable",value:function(){var e=this;r(".sortable").sortable({forcePlaceholderSize:!0,start:function(e,n){r(this).data("previous-index",n.item.index())},update:function(n,o){var t=o.item.attr("id").split("_"),i={hookId:t[0],moduleId:t[1],way:r(this).data("previous-index")<o.item.index()?1:0,positions:[]};r.each(n.target.children,function(e,n){i.positions.push(r(n).attr("id"))}),r.ajax({type:"POST",headers:{"cache-control":"no-cache"},url:e.$modulePositionsForm.data("update-url"),data:i,success:function(){var e=0;r.each(n.target.children,function(n,o){r(o).find(".index-position").html(++e)}),window.showSuccessMessage(window.update_success_msg)}})}})}},{key:"modulesPositionFilterHooks",value:function(){for(var e=this,n=e.$hookSearch.val(),o=e.$showModules.val(),t=e.$hookPosition.prop("checked"),i=new RegExp("("+n+")","gi"),c=0;c<e.$hooksList.length;c++)e.$hooksList[c].container.toggle(""===n&&"all"===o),e.$hooksList[c].element.html(e.$hooksList[c].title),e.$hooksList[c].container.find(".module-item").removeClass("highlight");if(""!==n||"all"!==o){for(var a=r(),l=r(),s=void 0,d=0;d<e.$hooksList.length;d++)"all"!==o&&(s=e.$hooksList[d].container.find(".module-position-"+o),s.length>0&&(a=a.add(e.$hooksList[d].container),s.addClass("highlight"))),""!==n&&-1!==e.$hooksList[d].title.toLowerCase().search(n.toLowerCase())&&(l=l.add(e.$hooksList[d].container),e.$hooksList[d].element.html(e.$hooksList[d].title.replace(i,'<span class="highlight">$1</span>')));"all"===o&&""!==n?l.show():""===n&&"all"!==o?a.show():l.filter(a).show()}if(!t)for(var p=0;p<e.$hooksList.length;p++)e.$hooksList[p].container.is(".hook-position")&&e.$hooksList[p].container.hide()}}]),e}();n.a=c},369:function(e,n,o){e.exports=o(179)}});