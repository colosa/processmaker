/*
  * Ext.ux.menu.StoreMenu  Addon
  *
  * @author    Marco Wienkoop (wm003/lubber)
  * @copyright (c) 2009, Marco Wienkoop (marco.wienkoop@lubber.de) http://www.lubber.de
*/

Ext.namespace('Ext.ux.menu');Ext.ux.menu.StoreMenu=function(config){Ext.ux.menu.StoreMenu.superclass.constructor.call(this,config);if(!this.store){this.store=new Ext.data.SimpleStore({fields:['config'],url:this.url,baseParams:this.baseParams});}
this.on('show',this.onMenuLoad,this);this.store.on('beforeload',this.onBeforeLoad,this);this.store.on('load',this.onLoad,this);};Ext.extend(Ext.ux.menu.StoreMenu,Ext.menu.Menu,{loadingText:Ext.LoadMask.prototype.msg||'Loading...',loaded:false,onMenuLoad:function(){if(!this.loaded){this.store.load();}},updateMenuItems:function(loadedState,records){this.removeAll();this.el.sync();if(loadedState){for(var i=0,len=records.length;i<len;i++){if(records[i].json.handler){eval("records[i].json.handler = "+records[i].json.handler);}
if(records[i].json.menu){eval("records[i].json.menu = "+records[i].json.menu);}
this.add(records[i].json);}}
else{this.add('<span class="loading-indicator">'+this.loadingText+'</span>');}
this.loaded=loadedState;},onBeforeLoad:function(store){this.store.baseParams=this.baseParams;this.updateMenuItems(false);},onLoad:function(store,records){this.updateMenuItems(true,records);}});