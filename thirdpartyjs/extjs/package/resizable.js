/*
 * Ext JS Library 1.1 Beta 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.Resizable=function(el,_2){this.el=Ext.get(el);if(_2&&_2.wrap){_2.resizeChild=this.el;this.el=this.el.wrap(typeof _2.wrap=="object"?_2.wrap:{cls:"xresizable-wrap"});this.el.id=this.el.dom.id=_2.resizeChild.id+"-rzwrap";this.el.setStyle("overflow","hidden");this.el.setPositioning(_2.resizeChild.getPositioning());_2.resizeChild.clearPositioning();if(!_2.width||!_2.height){var _3=_2.resizeChild.getSize();this.el.setSize(_3.width,_3.height);}if(_2.pinned&&!_2.adjustments){_2.adjustments="auto";}}this.proxy=this.el.createProxy({tag:"div",cls:"x-resizable-proxy",id:this.el.id+"-rzproxy"});this.proxy.unselectable();this.proxy.enableDisplayMode("block");Ext.apply(this,_2);if(this.pinned){this.disableTrackOver=true;this.el.addClass("x-resizable-pinned");}var _4=this.el.getStyle("position");if(_4!="absolute"&&_4!="fixed"){this.el.setStyle("position","relative");}if(!this.handles){this.handles="s,e,se";if(this.multiDirectional){this.handles+=",n,w";}}if(this.handles=="all"){this.handles="n s e w ne nw se sw";}var hs=this.handles.split(/\s*?[,;]\s*?| /);var ps=Ext.Resizable.positions;for(var i=0,_8=hs.length;i<_8;i++){if(hs[i]&&ps[hs[i]]){var _9=ps[hs[i]];this[_9]=new Ext.Resizable.Handle(this,_9,this.disableTrackOver,this.transparent);}}this.corner=this.southeast;if(this.handles.indexOf("n")!=-1||this.handles.indexOf("w")!=-1){this.updateBox=true;}this.activeHandle=null;if(this.resizeChild){if(typeof this.resizeChild=="boolean"){this.resizeChild=Ext.get(this.el.dom.firstChild,true);}else{this.resizeChild=Ext.get(this.resizeChild,true);}}if(this.adjustments=="auto"){var rc=this.resizeChild;var hw=this.west,he=this.east,hn=this.north,hs=this.south;if(rc&&(hw||hn)){rc.position("relative");rc.setLeft(hw?hw.el.getWidth():0);rc.setTop(hn?hn.el.getHeight():0);}this.adjustments=[(he?-he.el.getWidth():0)+(hw?-hw.el.getWidth():0),(hn?-hn.el.getHeight():0)+(hs?-hs.el.getHeight():0)-1];}if(this.draggable){this.dd=this.dynamic?this.el.initDD(null):this.el.initDDProxy(null,{dragElId:this.proxy.id});this.dd.setHandleElId(this.resizeChild?this.resizeChild.id:this.el.id);}this.addEvents({"beforeresize":true,"resize":true});if(this.width!==null&&this.height!==null){this.resizeTo(this.width,this.height);}else{this.updateChildSize();}if(Ext.isIE){this.el.dom.style.zoom=1;}Ext.Resizable.superclass.constructor.call(this);};Ext.extend(Ext.Resizable,Ext.util.Observable,{resizeChild:false,adjustments:[0,0],minWidth:5,minHeight:5,maxWidth:10000,maxHeight:10000,enabled:true,animate:false,duration:0.35,dynamic:false,handles:false,multiDirectional:false,disableTrackOver:false,easing:"easeOutStrong",widthIncrement:0,heightIncrement:0,pinned:false,width:null,height:null,preserveRatio:false,transparent:false,minX:0,minY:0,draggable:false,constrainTo:undefined,resizeRegion:undefined,resizeTo:function(_e,_f){this.el.setSize(_e,_f);this.updateChildSize();this.fireEvent("resize",this,_e,_f,null);},startSizing:function(e,_11){this.fireEvent("beforeresize",this,e);if(this.enabled){if(!this.overlay){this.overlay=this.el.createProxy({tag:"div",cls:"x-resizable-overlay",html:"&#160;"});this.overlay.unselectable();this.overlay.enableDisplayMode("block");this.overlay.on("mousemove",this.onMouseMove,this);this.overlay.on("mouseup",this.onMouseUp,this);}this.overlay.setStyle("cursor",_11.el.getStyle("cursor"));this.resizing=true;this.startBox=this.el.getBox();this.startPoint=e.getXY();this.offsets=[(this.startBox.x+this.startBox.width)-this.startPoint[0],(this.startBox.y+this.startBox.height)-this.startPoint[1]];this.overlay.setSize(Ext.lib.Dom.getViewWidth(true),Ext.lib.Dom.getViewHeight(true));this.overlay.show();if(this.constrainTo){var ct=Ext.get(this.constrainTo);this.resizeRegion=ct.getRegion().adjust(ct.getFrameWidth("t"),ct.getFrameWidth("l"),-ct.getFrameWidth("b"),-ct.getFrameWidth("r"));}this.proxy.setStyle("visibility","hidden");this.proxy.show();this.proxy.setBox(this.startBox);if(!this.dynamic){this.proxy.setStyle("visibility","visible");}}},onMouseDown:function(_13,e){if(this.enabled){e.stopEvent();this.activeHandle=_13;this.startSizing(e,_13);}},onMouseUp:function(e){var _16=this.resizeElement();this.resizing=false;this.handleOut();this.overlay.hide();this.proxy.hide();this.fireEvent("resize",this,_16.width,_16.height,e);},updateChildSize:function(){if(this.resizeChild){var el=this.el;var _18=this.resizeChild;var adj=this.adjustments;if(el.dom.offsetWidth){var b=el.getSize(true);_18.setSize(b.width+adj[0],b.height+adj[1]);}if(Ext.isIE){setTimeout(function(){if(el.dom.offsetWidth){var b=el.getSize(true);_18.setSize(b.width+adj[0],b.height+adj[1]);}},10);}}},snap:function(_1c,inc,min){if(!inc||!_1c){return _1c;}var _1f=_1c;var m=_1c%inc;if(m>0){if(m>(inc/2)){_1f=_1c+(inc-m);}else{_1f=_1c-m;}}return Math.max(min,_1f);},resizeElement:function(){var box=this.proxy.getBox();if(this.updateBox){this.el.setBox(box,false,this.animate,this.duration,null,this.easing);}else{this.el.setSize(box.width,box.height,this.animate,this.duration,null,this.easing);}this.updateChildSize();if(!this.dynamic){this.proxy.hide();}return box;},constrain:function(v,_23,m,mx){if(v-_23<m){_23=v-m;}else{if(v-_23>mx){_23=mx-v;}}return _23;},onMouseMove:function(e){if(this.enabled){try{if(this.resizeRegion&&!this.resizeRegion.contains(e.getPoint())){return;}var _27=this.curSize||this.startBox;var x=this.startBox.x,y=this.startBox.y;var ox=x,oy=y;var w=_27.width,h=_27.height;var ow=w,oh=h;var mw=this.minWidth,mh=this.minHeight;var mxw=this.maxWidth,mxh=this.maxHeight;var wi=this.widthIncrement;var hi=this.heightIncrement;var _36=e.getXY();var _37=-(this.startPoint[0]-Math.max(this.minX,_36[0]));var _38=-(this.startPoint[1]-Math.max(this.minY,_36[1]));var pos=this.activeHandle.position;switch(pos){case"east":w+=_37;w=Math.min(Math.max(mw,w),mxw);break;case"south":h+=_38;h=Math.min(Math.max(mh,h),mxh);break;case"southeast":w+=_37;h+=_38;w=Math.min(Math.max(mw,w),mxw);h=Math.min(Math.max(mh,h),mxh);break;case"north":_38=this.constrain(h,_38,mh,mxh);y+=_38;h-=_38;break;case"west":_37=this.constrain(w,_37,mw,mxw);x+=_37;w-=_37;break;case"northeast":w+=_37;w=Math.min(Math.max(mw,w),mxw);_38=this.constrain(h,_38,mh,mxh);y+=_38;h-=_38;break;case"northwest":_37=this.constrain(w,_37,mw,mxw);_38=this.constrain(h,_38,mh,mxh);y+=_38;h-=_38;x+=_37;w-=_37;break;case"southwest":_37=this.constrain(w,_37,mw,mxw);h+=_38;h=Math.min(Math.max(mh,h),mxh);x+=_37;w-=_37;break;}var sw=this.snap(w,wi,mw);var sh=this.snap(h,hi,mh);if(sw!=w||sh!=h){switch(pos){case"northeast":y-=sh-h;break;case"north":y-=sh-h;break;case"southwest":x-=sw-w;break;case"west":x-=sw-w;break;case"northwest":x-=sw-w;y-=sh-h;break;}w=sw;h=sh;}if(this.preserveRatio){switch(pos){case"southeast":case"east":h=oh*(w/ow);h=Math.min(Math.max(mh,h),mxh);w=ow*(h/oh);break;case"south":w=ow*(h/oh);w=Math.min(Math.max(mw,w),mxw);h=oh*(w/ow);break;case"northeast":w=ow*(h/oh);w=Math.min(Math.max(mw,w),mxw);h=oh*(w/ow);break;case"north":var tw=w;w=ow*(h/oh);w=Math.min(Math.max(mw,w),mxw);h=oh*(w/ow);x+=(tw-w)/2;break;case"southwest":h=oh*(w/ow);h=Math.min(Math.max(mh,h),mxh);var tw=w;w=ow*(h/oh);x+=tw-w;break;case"west":var th=h;h=oh*(w/ow);h=Math.min(Math.max(mh,h),mxh);y+=(th-h)/2;var tw=w;w=ow*(h/oh);x+=tw-w;break;case"northwest":var tw=w;var th=h;h=oh*(w/ow);h=Math.min(Math.max(mh,h),mxh);w=ow*(h/oh);y+=th-h;x+=tw-w;break;}}this.proxy.setBounds(x,y,w,h);if(this.dynamic){this.resizeElement();}}catch(e){}}},handleOver:function(){if(this.enabled){this.el.addClass("x-resizable-over");}},handleOut:function(){if(!this.resizing){this.el.removeClass("x-resizable-over");}},getEl:function(){return this.el;},getResizeChild:function(){return this.resizeChild;},destroy:function(_3e){this.proxy.remove();if(this.overlay){this.overlay.removeAllListeners();this.overlay.remove();}var ps=Ext.Resizable.positions;for(var k in ps){if(typeof ps[k]!="function"&&this[ps[k]]){var h=this[ps[k]];h.el.removeAllListeners();h.el.remove();}}if(_3e){this.el.update("");this.el.remove();}}});Ext.Resizable.positions={n:"north",s:"south",e:"east",w:"west",se:"southeast",sw:"southwest",nw:"northwest",ne:"northeast"};Ext.Resizable.Handle=function(rz,pos,_44,_45){if(!this.tpl){var tpl=Ext.DomHelper.createTemplate({tag:"div",cls:"x-resizable-handle x-resizable-handle-{0}"});tpl.compile();Ext.Resizable.Handle.prototype.tpl=tpl;}this.position=pos;this.rz=rz;this.el=this.tpl.append(rz.el.dom,[this.position],true);this.el.unselectable();if(_45){this.el.setOpacity(0);}this.el.on("mousedown",this.onMouseDown,this);if(!_44){this.el.on("mouseover",this.onMouseOver,this);this.el.on("mouseout",this.onMouseOut,this);}};Ext.Resizable.Handle.prototype={afterResize:function(rz){},onMouseDown:function(e){this.rz.onMouseDown(this,e);},onMouseOver:function(e){this.rz.handleOver(this,e);},onMouseOut:function(e){this.rz.handleOut(this,e);}};
