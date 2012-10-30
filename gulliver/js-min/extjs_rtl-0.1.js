// default menu aligns
Ext.override(Ext.Button, { menuAlign:'tr-br?', iconAlign: 'right', subMenuAlign:'tr-tl?' });
Ext.override(Ext.menu.Menu, { subMenuAlign:'tr-tl?' });
Ext.override(Ext.menu.DateMenu, { subMenuAlign:'tr-tl?' });
Ext.override(Ext.menu.ColorMenu, { subMenuAlign:'tr-tl?' });

// default align for tips
Ext.override(Ext.Tip, {defaultAlign:'tr-bl?'});

// isClickOnArrow for SplitButton should check for smaller than left
Ext.override(Ext.SplitButton, {
    isClickOnArrow : function(e){
        return this.arrowAlign != 'bottom' ?
               e.getPageX() < this.el.child(this.buttonSelector).getRegion().left :  // changed for RTL
               e.getPageY() > this.el.child(this.buttonSelector).getRegion().bottom;
    }
});

// Ext.layout.HBoxLayout needs total new onLayout
Ext.override(Ext.layout.HBoxLayout,{
    onLayout : function(ct, target){
        Ext.layout.HBoxLayout.superclass.onLayout.call(this, ct, target);

        var cs = ct.items.items, len = cs.length, c, i, last = len-1, cm;
        var size = this.getLayoutTargetSize(target);

        var w = size.width - target.getPadding('lr') - this.scrollOffset,
            h = size.height - target.getPadding('tb'),
            l = this.padding.left, t = this.padding.top;

        if ((Ext.isIE && !Ext.isStrict) && (w < 1 || h < 1)) {
            return;
        } else if (w < 1 && h < 1) {
            return;
        }

        var stretchHeight = h - (this.padding.top + this.padding.bottom);

        var totalFlex = 0;
        var totalWidth = 0;

        var maxHeight = 0;

        for(i = 0; i < len; i++){
            c = cs[i];
            cm = c.margins;
            totalFlex += c.flex || 0;
            totalWidth += c.getWidth() + cm.left + cm.right;
            maxHeight = Math.max(maxHeight, c.getHeight() + cm.top + cm.bottom);
        }

        var innerCtHeight = maxHeight + this.padding.top + this.padding.bottom;

        switch(this.align){
            case 'stretch':
                this.innerCt.setSize(w, h);
                break;
            case 'stretchmax':
            case 'top':
                this.innerCt.setSize(w, innerCtHeight);
                break;
            case 'middle':
                this.innerCt.setSize(w, h = Math.max(h, innerCtHeight));
                break;

        }

        var extraWidth = w - totalWidth - this.padding.left - this.padding.right;
        var allocated = 0;

        var cw, ch, ct, availableHeight = h - this.padding.top - this.padding.bottom;

        if(this.pack == 'center'){
            l += extraWidth ? extraWidth/2 : 0;
        }else if(this.pack == 'end'){
            l += extraWidth;
        }
        for(i = 0; i < len; i++){
            c = cs[i];
            cm = c.margins;
            cw = c.getWidth();
            ch = c.getHeight();

            l += cm.right;
            if(this.align != 'middle'){
                ct = t + cm.top;
            }else{
                var diff = availableHeight - (ch + cm.top + cm.bottom);
                if(diff == 0){
                    ct = t + cm.top;
                }else{
                    ct = t + cm.top + (diff/2);
                }
            }

            if(this.pack == 'start' && c.flex){
                var ratio = c.flex/totalFlex;
                var add = Math.floor(extraWidth*ratio);
                allocated += add;
                if(i == last){
                    add += (extraWidth-allocated);
                }
                cw += add;
                c.setWidth(cw);
            }
            c.setPosition(w - l - cw, ct);
            if(this.align == 'stretch'){
                c.setHeight((stretchHeight - (cm.top + cm.bottom)).constrain(c.minHeight || 0, c.maxHeight || 1000000));
            }else if(this.align == 'stretchmax'){
                c.setHeight((maxHeight - (cm.top + cm.bottom)).constrain(c.minHeight || 0, c.maxHeight || 1000000));
            }
            l += cw + cm.left;
        }
    }

});

Ext.override(Ext.layout.VBoxLayout, {
    onLayout : function(ct, target){
        Ext.layout.VBoxLayout.superclass.onLayout.call(this, ct, target);

        var cs = ct.items.items, len = cs.length, c, i, last = len-1, cm;
        var size = this.getLayoutTargetSize(target);

        var w = size.width - target.getPadding('lr') - this.scrollOffset,
            h = size.height - target.getPadding('tb'),
            l = this.padding.right, t = this.padding.top;

        if ((Ext.isIE && !Ext.isStrict) && (w < 1 || h < 1)) {
            return;
        } else if (w < 1 && h < 1) {
            return;
        }

        var stretchWidth = w - (this.padding.left + this.padding.right);

        var totalFlex = 0;
        var totalHeight = 0;

        var maxWidth = 0;

        for(i = 0; i < len; i++){
            c = cs[i];
            cm = c.margins;
            totalFlex += c.flex || 0;
            totalHeight += c.getHeight() + cm.top + cm.bottom;
            maxWidth = Math.max(maxWidth, c.getWidth() + cm.left + cm.right);
        }

        var innerCtWidth = maxWidth + this.padding.left + this.padding.right;

        switch(this.align){
            case 'stretch':
                this.innerCt.setSize(w, h);
                break;
            case 'stretchmax':
            case 'left':
            case 'center':
                this.innerCt.setSize(w = Math.max(w, innerCtWidth), h);
                break;

        }

        var extraHeight = h - totalHeight - this.padding.top - this.padding.bottom;
        var allocated = 0;

        var cw, ch, cl, availableWidth = w - this.padding.left - this.padding.right;

        if(this.pack == 'center'){
            t += extraHeight ? extraHeight/2 : 0;
        }else if(this.pack == 'end'){
            t += extraHeight;
        }
        for(i = 0; i < len; i++){
            c = cs[i];
            cm = c.margins;
            cw = c.getWidth();
            ch = c.getHeight();

            t += cm.top;
            if(this.align != 'center'){
                cl = l + cm.right;
            }else{
                var diff = availableWidth - (cw + cm.left + cm.right);
                if(diff == 0){
                    cl = l + cm.right;
                }else{
                    cl = l + cm.right + (diff/2);
                }
            }

            c.setPosition(w - cl - cw, t);
            if(this.pack == 'start' && c.flex){
                var ratio = c.flex/totalFlex;
                var add = Math.floor(extraHeight*ratio);
                allocated += add;
                if(i == last){
                    add += (extraHeight-allocated);
                }
                ch += add;
                c.setHeight(ch);
            }
            if(this.align == 'stretch'){
                c.setWidth((stretchWidth - (cm.left + cm.right)).constrain(c.minWidth || 0, c.maxWidth || 1000000));
            }else if(this.align == 'stretchmax'){
                c.setWidth((maxWidth - (cm.left + cm.right)).constrain(c.minWidth || 0, c.maxWidth || 1000000));
            }
            t += ch + cm.bottom;
        }
    }
});

// FormLayout
Ext.override(Ext.layout.FormLayout, {
	 setContainer : function(ct){
	    Ext.layout.FormLayout.superclass.setContainer.call(this, ct);
	    if(ct.labelAlign){
	        ct.addClass('x-form-label-'+ct.labelAlign);
	    }
	
	    if(ct.hideLabels){
	        Ext.apply(this, {
	            labelStyle: 'display:none',
	            elementStyle: 'padding-right:0;',
	            labelAdjust: 0
	        });
	    }else{
	        this.labelSeparator = ct.labelSeparator || this.labelSeparator;
	        ct.labelWidth = ct.labelWidth || 100;
	        if(Ext.isNumber(ct.labelWidth)){
	            var pad = Ext.isNumber(ct.labelPad) ? ct.labelPad : 5;
	            Ext.apply(this, {
	                labelAdjust: ct.labelWidth + pad,
	                labelStyle: 'width:' + ct.labelWidth + 'px;',
	                elementStyle: 'padding-right:' + (ct.labelWidth + pad) + 'px'
	            });
	        }
	        if(ct.labelAlign == 'top'){
	            Ext.apply(this, {
	                labelStyle: 'width:auto;',
	                labelAdjust: 0,
	                elementStyle: 'padding-right:0;'
	            });
	        }
	    }
	}
});

Ext.override(Ext.form.Field, {
	alignErrorIcon : function(){
		this.errorIcon.alignTo(this.el, 'tr-tl', [-2, 0]);
	}
});

Ext.override(Ext.form.TriggerField, {
    alignErrorIcon: function() {
        if(this.wrap){
            this.errorIcon.alignTo(this.wrap, 'tr-tl', [-2, 0]);
        }
    }
});

// CheckbxGroup
/*
Ext.override(Ext.form.CheckboxGroup ,{
    onRender : function(ct, position){
        if(!this.el){
            var panelCfg = {
                cls: this.groupCls,
                layout: 'column',
                border: false,
                renderTo: ct
            };
            var colCfg = {
                defaultType: this.defaultType,
                layout: 'form',
                hideLabels: true,   // added for correct display of rtl
                border: false,
                defaults: {
                    hideLabel: true,
                    anchor: '100%'
                }
            }
            if(this.items[0].items){

                // The container has standard ColumnLayout configs, so pass them in directly

                Ext.apply(panelCfg, {
                    layoutConfig: {columns: this.items.length},
                    defaults: this.defaults,
                    items: this.items
                })
                for(var i=0, len=this.items.length; i<len; i++){
                    Ext.applyIf(this.items[i], colCfg);
                };

            }else{

                // The container has field item configs, so we have to generate the column
                // panels first then move the items into the columns as needed.

                var numCols, cols = [];

                if(typeof this.columns == 'string'){ // 'auto' so create a col per item
                    this.columns = this.items.length;
                }
                if(!Ext.isArray(this.columns)){
                    var cs = [];
                    for(var i=0; i<this.columns; i++){
                        cs.push((100/this.columns)*.01); // distribute by even %
                    }
                    this.columns = cs;
                }

                numCols = this.columns.length;

                // Generate the column configs with the correct width setting
                for(var i=0; i<numCols; i++){
                    var cc = Ext.apply({items:[]}, colCfg);
                    cc[this.columns[i] <= 1 ? 'columnWidth' : 'width'] = this.columns[i];
                    if(this.defaults){
                        cc.defaults = Ext.apply(cc.defaults || {}, this.defaults)
                    }
                    cols.push(cc);
                };

                // Distribute the original items into the columns
                if(this.vertical){
                    var rows = Math.ceil(this.items.length / numCols), ri = 0;
                    for(var i=0, len=this.items.length; i<len; i++){
                        if(i>0 && i%rows==0){
                            ri++;
                        }
                        if(this.items[i].fieldLabel){
                            this.items[i].hideLabel = false;
                        }
                        cols[ri].items.push(this.items[i]);
                    };
                }else{
                    for(var i=0, len=this.items.length; i<len; i++){
                        var ci = i % numCols;
                        if(this.items[i].fieldLabel){
                            this.items[i].hideLabel = false;
                        }
                        cols[ci].items.push(this.items[i]);
                    };
                }

                Ext.apply(panelCfg, {
                    layoutConfig: {columns: numCols},
                    items: cols
                });
            }

            this.panel = new Ext.Panel(panelCfg);
            this.el = this.panel.getEl();

            if(this.forId && this.itemCls){
                var l = this.el.up(this.itemCls).child('label', true);
                if(l){
                    l.setAttribute('htmlFor', this.forId);
                }
            }

            var fields = this.panel.findBy(function(c){
                return c.isFormField;
            }, this);

            this.items = new Ext.util.MixedCollection();
            this.items.addAll(fields);
        }
        Ext.form.CheckboxGroup.superclass.onRender.call(this, ct, position);
    }
});
*/
// Toolbar
Ext.override(Ext.layout.ToolbarLayout ,{
    onLayout : function(ct, target){
		if(!this.leftTr){
	        var align = ct.buttonAlign == 'center' ? 'center' : 'right';
	        target.addClass('x-toolbar-layout-ct');
	        target.insertHtml('beforeEnd',
	             '<table cellspacing="0" class="x-toolbar-ct"><tbody><tr><td class="x-toolbar-right" align="' + align + '"><table cellspacing="0"><tbody><tr class="x-toolbar-right-row"></tr></tbody></table></td><td class="x-toolbar-left" align="left"><table cellspacing="0" class="x-toolbar-left-ct"><tbody><tr><td><table cellspacing="0"><tbody><tr class="x-toolbar-left-row"></tr></tbody></table></td><td><table cellspacing="0"><tbody><tr class="x-toolbar-extras-row"></tr></tbody></table></td></tr></tbody></table></td></tr></tbody></table>');
	        this.leftTr = target.child('tr.x-toolbar-right-row', true);
	        this.rightTr = target.child('tr.x-toolbar-left-row', true);
	        this.extrasTr = target.child('tr.x-toolbar-extras-row', true);
	    }
	    var side = ct.buttonAlign == 'right' ? this.rightTr : this.leftTr,
	        pos = 0,
	        items = ct.items.items;
	
	    for(var i = 0, len = items.length, c; i < len; i++, pos++) {
	        c = items[i];
	        if(c.isFill){
	            side = this.rightTr;
	            pos = -1;
	        }else if(!c.rendered){
	            c.render(this.insertCell(c, side, pos));
	        }else{
	            if(!c.xtbHidden && !this.isValidParent(c, side.childNodes[pos])){
	                var td = this.insertCell(c, side, pos);
	                td.appendChild(c.getPositionEl().dom);
	                c.container = Ext.get(td);
	            }
	        }
	    }
	  //strip extra empty cells
		this.cleanup(this.leftTr);
		this.cleanup(this.rightTr);
		this.cleanup(this.extrasTr);
		this.fitToSize(target);
	}

		
});

Ext.Container.LAYOUTS.toolbar = Ext.layout.ToolbarLayout;

// HtmlEditor
Ext.override(Ext.form.HtmlEditor, {
    getDocMarkup : function(){
        return '<html><head><style type="text/css">body{border:0;margin:0;padding:3px;height:98%;cursor:text;direction:rtl;}</style></head><body></body></html>';
    }
});

// DateField
Ext.override(Ext.form.DateField, {
    onTriggerClick : function(){
        if(this.disabled){
            return;
        }
        if(this.menu == null){
            this.menu = new Ext.menu.DateMenu({
                hideOnClick: false
            });
        }
        Ext.apply(this.menu.picker,  {
            minDate : this.minValue,
            maxDate : this.maxValue,
            disabledDatesRE : this.disabledDatesRE,
            disabledDatesText : this.disabledDatesText,
            disabledDays : this.disabledDays,
            disabledDaysText : this.disabledDaysText,
            format : this.format,
            showToday : this.showToday,
            minText : String.format(this.minText, this.formatDate(this.minValue)),
            maxText : String.format(this.maxText, this.formatDate(this.maxValue))
        });
        this.menu.picker.setValue(this.getValue() || new Date());
        this.menu.show(this.el, "tr-br?");
        this.menuEvents('on');
    }
});

// Tabs
Ext.override(Ext.TabPanel, {
    autoScrollTabs : function(){
        this.pos = this.tabPosition=='bottom' ? this.footer : this.header;
        var count = this.items.length,
	        ow = this.pos.dom.offsetWidth,
	        tw = this.pos.dom.clientWidth,
	        wrap = this.stripWrap,
	        wd = wrap.dom,
	        cw = wd.offsetWidth,
	        pos = this.getScrollPos(),
	        l = cw - (this.edge.getOffsetsTo(this.stripWrap)[0] + pos);
        if(!this.enableTabScroll || count < 1 || cw < 20){ // 20 to prevent display:none issues
            return;
        }
        if(l <= tw){
            wrap.setWidth(tw);
            if(this.scrolling){
                this.scrolling = false;
                this.pos.removeClass('x-tab-scrolling');
                this.scrollLeft.hide();
                this.scrollRight.hide();
                // See here: http://extjs.com/forum/showthread.php?t=49308&highlight=isSafari
                if(Ext.isAir || Ext.isWebKit){
                    wd.style.marginLeft = '';
                    wd.style.marginRight = '';
                }
            }
            wd.scrollLeft = 0;
        }else{
            if(!this.scrolling){
                this.pos.addClass('x-tab-scrolling');
                // See here: http://extjs.com/forum/showthread.php?t=49308&highlight=isSafari
                if(Ext.isAir || Ext.isWebKit){
                    wd.style.marginLeft = '18px';
                    wd.style.marginRight = '18px';
                }
            }
            tw -= wrap.getMargins('lr');
            wrap.setWidth(tw > 20 ? tw : 20);
            if(!this.scrolling){
                if(!this.scrollLeft){
                    this.createScrollers();
                }else{
                    this.scrollLeft.show();
                    this.scrollRight.show();
                }
            }
            this.scrolling = true;
            if(pos > (l-tw)){ // ensure it stays within bounds
                wd.scrollLeft = l-tw;
            }else{ // otherwise, make sure the active tab is still visible
                this.scrollToTab(this.activeTab, false);
            }
            this.updateScrollButtons();
        }
    },
    onScrollRight : function(){
        var pos = this.getScrollPos();
        var s = Math.max(this.getScrollWidth(), pos - this.getScrollIncrement());
        if(s != pos){
            this.scrollTo(s, this.animScroll);
        }
    },
    onScrollLeft : function(){
        var pos = this.getScrollPos();
        var s = Math.min(0, pos + this.getScrollIncrement());

        if(s != pos){
            this.scrollTo(s, this.animScroll);
        }
    },

    // private
    updateScrollButtons : function(){
        var pos = this.getScrollPos();
        this.scrollLeft[pos == 0 ? 'addClass' : 'removeClass']('x-tab-scroller-left-disabled');
        this.scrollRight[pos <= this.getScrollWidth() ? 'addClass' : 'removeClass']('x-tab-scroller-right-disabled');
    }
});

// Grids
Ext.override(Ext.grid.GridView.SplitDragZone,{
    handleMouseDown : function(e){
        var t = this.view.findHeaderCell(e.getTarget());
        if(t && this.allowHeaderDrag(e)){
            var xy = this.view.fly(t).getXY(), x = xy[0], y = xy[1];
            var exy = e.getXY(), ex = exy[0];
            var w = t.offsetWidth, adjust = false;
            if((ex - x) <= this.hw){
                adjust = 0;
            }else if((x+w) - ex <= this.hw){
                adjust = -1;
            }
            if(adjust !== false){
                this.cm = this.grid.colModel;
                var ci = this.view.getCellIndex(t);
                if(adjust == -1){
                  if (ci + adjust < 0) {
                    return;
                  }
                    while(this.cm.isHidden(ci+adjust)){
                        --adjust;
                        if(ci+adjust < 0){
                            return;
                        }
                    }
                }
                this.cellIndex = ci+adjust;
                this.split = t.dom;
                if(this.cm.isResizable(this.cellIndex) && !this.cm.isFixed(this.cellIndex)){
                    Ext.grid.GridView.SplitDragZone.superclass.handleMouseDown.apply(this, arguments);
                }
            }else if(this.view.columnDrag){
                this.view.columnDrag.callHandleMouseDown(e);
            }
        }
    },
    endDrag : function(e){
        this.marker.hide();
        var v = this.view;
        var endX = Math.max(this.minX, e.getPageX());
        var diff = this.startPos - endX;
        v.onColumnSplitterMoved(this.cellIndex, this.cm.getColumnWidth(this.cellIndex)+diff);
        setTimeout(function(){
            v.headersDisabled = false;
        }, 50);
    }
});

Ext.override(Ext.grid.GridView, {
    handleHdMove : function(e, t){
		var hd = this.findHeaderCell(this.activeHdRef);
		   if(hd && !this.headersDisabled){
			   var hw = this.splitHandleWidth || 5,
               r = this.activeHdRegion,
               x = e.getPageX(),
               ss = hd.style,
               cur = '';
            if(this.grid.enableColumnResize !== false){
                if(r.right - x <= hw && this.cm.isResizable(this.activeHdIndex-1)){
                    cur = Ext.isAir ? 'move' : Ext.isWebKit ? 'e-resize' : 'col-resize'; // col-resize not always supported
                }else if(x - r.left <= (!this.activeHdBtn ? hw : 2) && this.cm.isResizable(this.activeHdIndex)){
                    cur = Ext.isAir ? 'move' : Ext.isWebKit ? 'w-resize' : 'col-resize';
                }
            }
        }
    },
    syncFocusEl : function(row, col, hscroll){
        var xy = row;
        if(!Ext.isArray(xy)){
            row = Math.min(row, Math.max(0, this.getRows().length-1));
            xy = this.getResolvedXY(this.resolveCell(row, col, hscroll));
        }
        var sc_xy = this.scroller.getXY()
        if (!xy) {xy=sc_xy;}
        //this.focusEl.setXY(xy||this.scroller.getXY());
        this.focusEl.setTop(xy[1]-sc_xy[1]+this.scroller.getScroll().top);
        this.focusEl.setRight(xy[0]-sc_xy[0]);
    },
    handleHdDown : function(e, t){
        if(Ext.fly(t).hasClass('x-grid3-hd-btn')){
            e.stopEvent();
            var hd = this.findHeaderCell(t);
            Ext.fly(hd).addClass('x-grid3-hd-menu-open');
            var index = this.getCellIndex(hd);
            this.hdCtxIndex = index;
            var ms = this.hmenu.items, cm = this.cm;
            ms.get("asc").setDisabled(!cm.isSortable(index));
            ms.get("desc").setDisabled(!cm.isSortable(index));
            this.hmenu.on("hide", function(){
                Ext.fly(hd).removeClass('x-grid3-hd-menu-open');
            }, this, {single:true});
            this.hmenu.show(t, "tr-br?");
        }
    }

});

Ext.override(Ext.Layer, {
    hideAction : function(){
        this.visible = false;
        if(this.useDisplay === true){
            this.setDisplayed(false);
        }else{
            this.setLeftTop(0,-10000); // negative x in firefox shows scrollbar in RTL
        }
    }
});

Ext.override(Ext.form.ComboBox, { listAlign:'tr-br?' });