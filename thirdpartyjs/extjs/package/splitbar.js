/*
 * Ext JS Library 1.1 Beta 1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.SplitBar=function(_1,_2,_3,_4,_5){this.el=Ext.get(_1,true);this.el.dom.unselectable="on";this.resizingEl=Ext.get(_2,true);this.orientation=_3||Ext.SplitBar.HORIZONTAL;this.minSize=0;this.maxSize=2000;this.animate=false;this.useShim=false;this.shim=null;if(!_5){this.proxy=Ext.SplitBar.createProxy(this.orientation);}else{this.proxy=Ext.get(_5).dom;}this.dd=new Ext.dd.DDProxy(this.el.dom.id,"XSplitBars",{dragElId:this.proxy.id});this.dd.b4StartDrag=this.onStartProxyDrag.createDelegate(this);this.dd.endDrag=this.onEndProxyDrag.createDelegate(this);this.dragSpecs={};this.adapter=new Ext.SplitBar.BasicLayoutAdapter();this.adapter.init(this);if(this.orientation==Ext.SplitBar.HORIZONTAL){this.placement=_4||(this.el.getX()>this.resizingEl.getX()?Ext.SplitBar.LEFT:Ext.SplitBar.RIGHT);this.el.addClass("x-splitbar-h");}else{this.placement=_4||(this.el.getY()>this.resizingEl.getY()?Ext.SplitBar.TOP:Ext.SplitBar.BOTTOM);this.el.addClass("x-splitbar-v");}this.addEvents({"resize":true,"moved":true,"beforeresize":true,"beforeapply":true});Ext.SplitBar.superclass.constructor.call(this);};Ext.extend(Ext.SplitBar,Ext.util.Observable,{onStartProxyDrag:function(x,y){this.fireEvent("beforeresize",this);if(!this.overlay){var o=Ext.DomHelper.insertFirst(document.body,{cls:"x-drag-overlay",html:"&#160;"},true);o.unselectable();o.enableDisplayMode("block");Ext.SplitBar.prototype.overlay=o;}this.overlay.setSize(Ext.lib.Dom.getViewWidth(true),Ext.lib.Dom.getViewHeight(true));this.overlay.show();Ext.get(this.proxy).setDisplayed("block");var _9=this.adapter.getElementSize(this);this.activeMinSize=this.getMinimumSize();this.activeMaxSize=this.getMaximumSize();var c1=_9-this.activeMinSize;var c2=Math.max(this.activeMaxSize-_9,0);if(this.orientation==Ext.SplitBar.HORIZONTAL){this.dd.resetConstraints();this.dd.setXConstraint(this.placement==Ext.SplitBar.LEFT?c1:c2,this.placement==Ext.SplitBar.LEFT?c2:c1);this.dd.setYConstraint(0,0);}else{this.dd.resetConstraints();this.dd.setXConstraint(0,0);this.dd.setYConstraint(this.placement==Ext.SplitBar.TOP?c1:c2,this.placement==Ext.SplitBar.TOP?c2:c1);}this.dragSpecs.startSize=_9;this.dragSpecs.startPoint=[x,y];Ext.dd.DDProxy.prototype.b4StartDrag.call(this.dd,x,y);},onEndProxyDrag:function(e){Ext.get(this.proxy).setDisplayed(false);var _d=Ext.lib.Event.getXY(e);if(this.overlay){this.overlay.hide();}var _e;if(this.orientation==Ext.SplitBar.HORIZONTAL){_e=this.dragSpecs.startSize+(this.placement==Ext.SplitBar.LEFT?_d[0]-this.dragSpecs.startPoint[0]:this.dragSpecs.startPoint[0]-_d[0]);}else{_e=this.dragSpecs.startSize+(this.placement==Ext.SplitBar.TOP?_d[1]-this.dragSpecs.startPoint[1]:this.dragSpecs.startPoint[1]-_d[1]);}_e=Math.min(Math.max(_e,this.activeMinSize),this.activeMaxSize);if(_e!=this.dragSpecs.startSize){if(this.fireEvent("beforeapply",this,_e)!==false){this.adapter.setElementSize(this,_e);this.fireEvent("moved",this,_e);this.fireEvent("resize",this,_e);}}},getAdapter:function(){return this.adapter;},setAdapter:function(_f){this.adapter=_f;this.adapter.init(this);},getMinimumSize:function(){return this.minSize;},setMinimumSize:function(_10){this.minSize=_10;},getMaximumSize:function(){return this.maxSize;},setMaximumSize:function(_11){this.maxSize=_11;},setCurrentSize:function(_12){var _13=this.animate;this.animate=false;this.adapter.setElementSize(this,_12);this.animate=_13;},destroy:function(_14){if(this.shim){this.shim.remove();}this.dd.unreg();this.proxy.parentNode.removeChild(this.proxy);if(_14){this.el.remove();}}});Ext.SplitBar.createProxy=function(dir){var _16=new Ext.Element(document.createElement("div"));_16.unselectable();var cls="x-splitbar-proxy";_16.addClass(cls+" "+(dir==Ext.SplitBar.HORIZONTAL?cls+"-h":cls+"-v"));document.body.appendChild(_16.dom);return _16.dom;};Ext.SplitBar.BasicLayoutAdapter=function(){};Ext.SplitBar.BasicLayoutAdapter.prototype={init:function(s){},getElementSize:function(s){if(s.orientation==Ext.SplitBar.HORIZONTAL){return s.resizingEl.getWidth();}else{return s.resizingEl.getHeight();}},setElementSize:function(s,_1b,_1c){if(s.orientation==Ext.SplitBar.HORIZONTAL){if(!s.animate){s.resizingEl.setWidth(_1b);if(_1c){_1c(s,_1b);}}else{s.resizingEl.setWidth(_1b,true,0.1,_1c,"easeOut");}}else{if(!s.animate){s.resizingEl.setHeight(_1b);if(_1c){_1c(s,_1b);}}else{s.resizingEl.setHeight(_1b,true,0.1,_1c,"easeOut");}}}};Ext.SplitBar.AbsoluteLayoutAdapter=function(_1d){this.basic=new Ext.SplitBar.BasicLayoutAdapter();this.container=Ext.get(_1d);};Ext.SplitBar.AbsoluteLayoutAdapter.prototype={init:function(s){this.basic.init(s);},getElementSize:function(s){return this.basic.getElementSize(s);},setElementSize:function(s,_21,_22){this.basic.setElementSize(s,_21,this.moveSplitter.createDelegate(this,[s]));},moveSplitter:function(s){var yes=Ext.SplitBar;switch(s.placement){case yes.LEFT:s.el.setX(s.resizingEl.getRight());break;case yes.RIGHT:s.el.setStyle("right",(this.container.getWidth()-s.resizingEl.getLeft())+"px");break;case yes.TOP:s.el.setY(s.resizingEl.getBottom());break;case yes.BOTTOM:s.el.setY(s.resizingEl.getTop()-s.el.getHeight());break;}}};Ext.SplitBar.VERTICAL=1;Ext.SplitBar.HORIZONTAL=2;Ext.SplitBar.LEFT=1;Ext.SplitBar.RIGHT=2;Ext.SplitBar.TOP=3;Ext.SplitBar.BOTTOM=4;
