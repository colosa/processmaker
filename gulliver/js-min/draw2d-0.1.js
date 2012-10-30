/* This notice must be untouched at all times.

wz_jsgraphics.js    v. 3.05
The latest version is available at
http://www.walterzorn.com
or http://www.devira.com
or http://www.walterzorn.de

Copyright (c) 2002-2009 Walter Zorn. All rights reserved.
Created 3. 11. 2002 by Walter Zorn (Web: http://www.walterzorn.com )
Last modified: 2. 2. 2009

Performance optimizations for Internet Explorer
by Thomas Frank and John Holdsworth.
fillPolygon method implemented by Matthieu Haller.

High Performance JavaScript Graphics Library.
Provides methods
- to draw lines, rectangles, ellipses, polygons
	with specifiable line thickness,
- to fill rectangles, polygons, ellipses and arcs
- to draw text.
NOTE: Operations, functions and branching have rather been optimized
to efficiency and speed than to shortness of source code.

LICENSE: LGPL

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License (LGPL) as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA,
or see http://www.gnu.org/copyleft/lesser.html
*/


var jg_ok, jg_ie, jg_fast, jg_dom, jg_moz;


function _chkDHTM(wnd, x, i)
// Under XUL, owner of 'document' must be specified explicitly
{
	x = wnd.document.body || null;
	jg_ie = x && typeof x.insertAdjacentHTML != "undefined" && wnd.document.createElement;
	jg_dom = (x && !jg_ie &&
		typeof x.appendChild != "undefined" &&
		typeof wnd.document.createRange != "undefined" &&
		typeof (i = wnd.document.createRange()).setStartBefore != "undefined" &&
		typeof i.createContextualFragment != "undefined");
	jg_fast = jg_ie && wnd.document.all && !wnd.opera;
	jg_moz = jg_dom && typeof x.style.MozOpacity != "undefined";
	jg_ok = !!(jg_ie || jg_dom);
}

function _pntCnvDom()
{
	var x = this.wnd.document.createRange();
	x.setStartBefore(this.cnv);
	x = x.createContextualFragment(jg_fast? this._htmRpc() : this.htm);
	if(this.cnv) this.cnv.appendChild(x);
	this.htm = "";
}

function _pntCnvIe()
{
	if(this.cnv) this.cnv.insertAdjacentHTML("BeforeEnd", jg_fast? this._htmRpc() : this.htm);
	this.htm = "";
}

function _pntDoc()
{
	this.wnd.document.write(jg_fast? this._htmRpc() : this.htm);
	this.htm = '';
}

function _pntN()
{
	;
}

function _mkDiv(x, y, w, h)
{
	this.htm += '<div style="position:absolute;'+
		'left:' + x + 'px;'+
		'top:' + y + 'px;'+
		'width:' + w + 'px;'+
		'height:' + h + 'px;'+
		'clip:rect(0,'+w+'px,'+h+'px,0);'+
		'background-color:' + this.color +
		(!jg_moz? ';overflow:hidden' : '')+
		';"><\/div>';
}

function _mkDivIe(x, y, w, h)
{
	this.htm += '%%'+this.color+';'+x+';'+y+';'+w+';'+h+';';
}

function _mkDivPrt(x, y, w, h)
{
	this.htm += '<div style="position:absolute;'+
		'border-left:' + w + 'px solid ' + this.color + ';'+
		'left:' + x + 'px;'+
		'top:' + y + 'px;'+
		'width:0px;'+
		'height:' + h + 'px;'+
		'clip:rect(0,'+w+'px,'+h+'px,0);'+
		'background-color:' + this.color +
		(!jg_moz? ';overflow:hidden' : '')+
		';"><\/div>';
}

var _regex =  /%%([^;]+);([^;]+);([^;]+);([^;]+);([^;]+);/g;
function _htmRpc()
{
	return this.htm.replace(
		_regex,
		'<div style="overflow:hidden;position:absolute;background-color:'+
		'$1;left:$2px;top:$3px;width:$4px;height:$5px"></div>\n');
}

function _htmPrtRpc()
{
	return this.htm.replace(
		_regex,
		'<div style="overflow:hidden;position:absolute;background-color:'+
		'$1;left:$2px;top:$3px;width:$4px;height:$5px;border-left:$4px solid $1"></div>\n');
}

function _mkLin(x1, y1, x2, y2)
{
	if(x1 > x2)
	{
		var _x2 = x2;
		var _y2 = y2;
		x2 = x1;
		y2 = y1;
		x1 = _x2;
		y1 = _y2;
	}
	var dx = x2-x1, dy = Math.abs(y2-y1),
	x = x1, y = y1,
	yIncr = (y1 > y2)? -1 : 1;

	if(dx >= dy)
	{
		var pr = dy<<1,
		pru = pr - (dx<<1),
		p = pr-dx,
		ox = x;
		while(dx > 0)
		{--dx;
			++x;
			if(p > 0)
			{
				this._mkDiv(ox, y, x-ox, 1);
				y += yIncr;
				p += pru;
				ox = x;
			}
			else p += pr;
		}
		this._mkDiv(ox, y, x2-ox+1, 1);
	}

	else
	{
		var pr = dx<<1,
		pru = pr - (dy<<1),
		p = pr-dy,
		oy = y;
		if(y2 <= y1)
		{
			while(dy > 0)
			{--dy;
				if(p > 0)
				{
					this._mkDiv(x++, y, 1, oy-y+1);
					y += yIncr;
					p += pru;
					oy = y;
				}
				else
				{
					y += yIncr;
					p += pr;
				}
			}
			this._mkDiv(x2, y2, 1, oy-y2+1);
		}
		else
		{
			while(dy > 0)
			{--dy;
				y += yIncr;
				if(p > 0)
				{
					this._mkDiv(x++, oy, 1, y-oy);
					p += pru;
					oy = y;
				}
				else p += pr;
			}
			this._mkDiv(x2, oy, 1, y2-oy+1);
		}
	}
}

function _mkLin2D(x1, y1, x2, y2)
{
	if(x1 > x2)
	{
		var _x2 = x2;
		var _y2 = y2;
		x2 = x1;
		y2 = y1;
		x1 = _x2;
		y1 = _y2;
	}
	var dx = x2-x1, dy = Math.abs(y2-y1),
	x = x1, y = y1,
	yIncr = (y1 > y2)? -1 : 1;

	var s = this.stroke;
	if(dx >= dy)
	{
		if(dx > 0 && s-3 > 0)
		{
			var _s = (s*dx*Math.sqrt(1+dy*dy/(dx*dx))-dx-(s>>1)*dy) / dx;
			_s = (!(s-4)? Math.ceil(_s) : Math.round(_s)) + 1;
		}
		else var _s = s;
		var ad = Math.ceil(s/2);

		var pr = dy<<1,
		pru = pr - (dx<<1),
		p = pr-dx,
		ox = x;
		while(dx > 0)
		{--dx;
			++x;
			if(p > 0)
			{
				this._mkDiv(ox, y, x-ox+ad, _s);
				y += yIncr;
				p += pru;
				ox = x;
			}
			else p += pr;
		}
		this._mkDiv(ox, y, x2-ox+ad+1, _s);
	}

	else
	{
		if(s-3 > 0)
		{
			var _s = (s*dy*Math.sqrt(1+dx*dx/(dy*dy))-(s>>1)*dx-dy) / dy;
			_s = (!(s-4)? Math.ceil(_s) : Math.round(_s)) + 1;
		}
		else var _s = s;
		var ad = Math.round(s/2);

		var pr = dx<<1,
		pru = pr - (dy<<1),
		p = pr-dy,
		oy = y;
		if(y2 <= y1)
		{
			++ad;
			while(dy > 0)
			{--dy;
				if(p > 0)
				{
					this._mkDiv(x++, y, _s, oy-y+ad);
					y += yIncr;
					p += pru;
					oy = y;
				}
				else
				{
					y += yIncr;
					p += pr;
				}
			}
			this._mkDiv(x2, y2, _s, oy-y2+ad);
		}
		else
		{
			while(dy > 0)
			{--dy;
				y += yIncr;
				if(p > 0)
				{
					this._mkDiv(x++, oy, _s, y-oy+ad);
					p += pru;
					oy = y;
				}
				else p += pr;
			}
			this._mkDiv(x2, oy, _s, y2-oy+ad+1);
		}
	}
}

function _mkLinDott(x1, y1, x2, y2)
{
	if(x1 > x2)
	{
		var _x2 = x2;
		var _y2 = y2;
		x2 = x1;
		y2 = y1;
		x1 = _x2;
		y1 = _y2;
	}
	var dx = x2-x1, dy = Math.abs(y2-y1),
	x = x1, y = y1,
	yIncr = (y1 > y2)? -1 : 1,
	drw = true;
	if(dx >= dy)
	{
		var pr = dy<<1,
		pru = pr - (dx<<1),
		p = pr-dx;
		while(dx > 0)
		{--dx;
			if(drw) this._mkDiv(x, y, 1, 1);
			drw = !drw;
			if(p > 0)
			{
				y += yIncr;
				p += pru;
			}
			else p += pr;
			++x;
		}
	}
	else
	{
		var pr = dx<<1,
		pru = pr - (dy<<1),
		p = pr-dy;
		while(dy > 0)
		{--dy;
			if(drw) this._mkDiv(x, y, 1, 1);
			drw = !drw;
			y += yIncr;
			if(p > 0)
			{
				++x;
				p += pru;
			}
			else p += pr;
		}
	}
	if(drw) this._mkDiv(x, y, 1, 1);
}

function _mkOv(left, top, width, height)
{
	var a = (++width)>>1, b = (++height)>>1,
	wod = width&1, hod = height&1,
	cx = left+a, cy = top+b,
	x = 0, y = b,
	ox = 0, oy = b,
	aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
	st = (aa2>>1)*(1-(b<<1)) + bb2,
	tt = (bb2>>1) - aa2*((b<<1)-1),
	w, h;
	while(y > 0)
	{
		if(st < 0)
		{
			st += bb2*((x<<1)+3);
			tt += bb4*(++x);
		}
		else if(tt < 0)
		{
			st += bb2*((x<<1)+3) - aa4*(y-1);
			tt += bb4*(++x) - aa2*(((y--)<<1)-3);
			w = x-ox;
			h = oy-y;
			if((w&2) && (h&2))
			{
				this._mkOvQds(cx, cy, x-2, y+2, 1, 1, wod, hod);
				this._mkOvQds(cx, cy, x-1, y+1, 1, 1, wod, hod);
			}
			else this._mkOvQds(cx, cy, x-1, oy, w, h, wod, hod);
			ox = x;
			oy = y;
		}
		else
		{
			tt -= aa2*((y<<1)-3);
			st -= aa4*(--y);
		}
	}
	w = a-ox+1;
	h = (oy<<1)+hod;
	y = cy-oy;
	this._mkDiv(cx-a, y, w, h);
	this._mkDiv(cx+ox+wod-1, y, w, h);
}

function _mkOv2D(left, top, width, height)
{
	var s = this.stroke;
	width += s+1;
	height += s+1;
	var a = width>>1, b = height>>1,
	wod = width&1, hod = height&1,
	cx = left+a, cy = top+b,
	x = 0, y = b,
	aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
	st = (aa2>>1)*(1-(b<<1)) + bb2,
	tt = (bb2>>1) - aa2*((b<<1)-1);

	if(s-4 < 0 && (!(s-2) || width-51 > 0 && height-51 > 0))
	{
		var ox = 0, oy = b,
		w, h,
		pxw;
		while(y > 0)
		{
			if(st < 0)
			{
				st += bb2*((x<<1)+3);
				tt += bb4*(++x);
			}
			else if(tt < 0)
			{
				st += bb2*((x<<1)+3) - aa4*(y-1);
				tt += bb4*(++x) - aa2*(((y--)<<1)-3);
				w = x-ox;
				h = oy-y;

				if(w-1)
				{
					pxw = w+1+(s&1);
					h = s;
				}
				else if(h-1)
				{
					pxw = s;
					h += 1+(s&1);
				}
				else pxw = h = s;
				this._mkOvQds(cx, cy, x-1, oy, pxw, h, wod, hod);
				ox = x;
				oy = y;
			}
			else
			{
				tt -= aa2*((y<<1)-3);
				st -= aa4*(--y);
			}
		}
		this._mkDiv(cx-a, cy-oy, s, (oy<<1)+hod);
		this._mkDiv(cx+a+wod-s, cy-oy, s, (oy<<1)+hod);
	}

	else
	{
		var _a = (width-(s<<1))>>1,
		_b = (height-(s<<1))>>1,
		_x = 0, _y = _b,
		_aa2 = (_a*_a)<<1, _aa4 = _aa2<<1, _bb2 = (_b*_b)<<1, _bb4 = _bb2<<1,
		_st = (_aa2>>1)*(1-(_b<<1)) + _bb2,
		_tt = (_bb2>>1) - _aa2*((_b<<1)-1),

		pxl = new Array(),
		pxt = new Array(),
		_pxb = new Array();
		pxl[0] = 0;
		pxt[0] = b;
		_pxb[0] = _b-1;
		while(y > 0)
		{
			if(st < 0)
			{
				pxl[pxl.length] = x;
				pxt[pxt.length] = y;
				st += bb2*((x<<1)+3);
				tt += bb4*(++x);
			}
			else if(tt < 0)
			{
				pxl[pxl.length] = x;
				st += bb2*((x<<1)+3) - aa4*(y-1);
				tt += bb4*(++x) - aa2*(((y--)<<1)-3);
				pxt[pxt.length] = y;
			}
			else
			{
				tt -= aa2*((y<<1)-3);
				st -= aa4*(--y);
			}

			if(_y > 0)
			{
				if(_st < 0)
				{
					_st += _bb2*((_x<<1)+3);
					_tt += _bb4*(++_x);
					_pxb[_pxb.length] = _y-1;
				}
				else if(_tt < 0)
				{
					_st += _bb2*((_x<<1)+3) - _aa4*(_y-1);
					_tt += _bb4*(++_x) - _aa2*(((_y--)<<1)-3);
					_pxb[_pxb.length] = _y-1;
				}
				else
				{
					_tt -= _aa2*((_y<<1)-3);
					_st -= _aa4*(--_y);
					_pxb[_pxb.length-1]--;
				}
			}
		}

		var ox = -wod, oy = b,
		_oy = _pxb[0],
		l = pxl.length,
		w, h;
		for(var i = 0; i < l; i++)
		{
			if(typeof _pxb[i] != "undefined")
			{
				if(_pxb[i] < _oy || pxt[i] < oy)
				{
					x = pxl[i];
					this._mkOvQds(cx, cy, x, oy, x-ox, oy-_oy, wod, hod);
					ox = x;
					oy = pxt[i];
					_oy = _pxb[i];
				}
			}
			else
			{
				x = pxl[i];
				this._mkDiv(cx-x, cy-oy, 1, (oy<<1)+hod);
				this._mkDiv(cx+ox+wod, cy-oy, 1, (oy<<1)+hod);
				ox = x;
				oy = pxt[i];
			}
		}
		this._mkDiv(cx-a, cy-oy, 1, (oy<<1)+hod);
		this._mkDiv(cx+ox+wod, cy-oy, 1, (oy<<1)+hod);
	}
}

function _mkOvDott(left, top, width, height)
{
	var a = (++width)>>1, b = (++height)>>1,
	wod = width&1, hod = height&1, hodu = hod^1,
	cx = left+a, cy = top+b,
	x = 0, y = b,
	aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
	st = (aa2>>1)*(1-(b<<1)) + bb2,
	tt = (bb2>>1) - aa2*((b<<1)-1),
	drw = true;
	while(y > 0)
	{
		if(st < 0)
		{
			st += bb2*((x<<1)+3);
			tt += bb4*(++x);
		}
		else if(tt < 0)
		{
			st += bb2*((x<<1)+3) - aa4*(y-1);
			tt += bb4*(++x) - aa2*(((y--)<<1)-3);
		}
		else
		{
			tt -= aa2*((y<<1)-3);
			st -= aa4*(--y);
		}
		if(drw && y >= hodu) this._mkOvQds(cx, cy, x, y, 1, 1, wod, hod);
		drw = !drw;
	}
}

function _mkRect(x, y, w, h)
{
	var s = this.stroke;
	this._mkDiv(x, y, w, s);
	this._mkDiv(x+w, y, s, h);
	this._mkDiv(x, y+h, w+s, s);
	this._mkDiv(x, y+s, s, h-s);
}

function _mkRectDott(x, y, w, h)
{
	this.drawLine(x, y, x+w, y);
	this.drawLine(x+w, y, x+w, y+h);
	this.drawLine(x, y+h, x+w, y+h);
	this.drawLine(x, y, x, y+h);
}

function jsgFont()
{
	this.PLAIN = 'font-weight:normal;';
	this.BOLD = 'font-weight:bold;';
	this.ITALIC = 'font-style:italic;';
	this.ITALIC_BOLD = this.ITALIC + this.BOLD;
	this.BOLD_ITALIC = this.ITALIC_BOLD;
}
var Font = new jsgFont();

function jsgStroke()
{
	this.DOTTED = -1;
}
var Stroke = new jsgStroke();

function jsGraphics(cnv, wnd)
{
	this.setColor = function(x)
	{
		this.color = x.toLowerCase();
	};

	this.setStroke = function(x)
	{
		this.stroke = x;
		if(!(x+1))
		{
			this.drawLine = _mkLinDott;
			this._mkOv = _mkOvDott;
			this.drawRect = _mkRectDott;
		}
		else if(x-1 > 0)
		{
			this.drawLine = _mkLin2D;
			this._mkOv = _mkOv2D;
			this.drawRect = _mkRect;
		}
		else
		{
			this.drawLine = _mkLin;
			this._mkOv = _mkOv;
			this.drawRect = _mkRect;
		}
	};

	this.setPrintable = function(arg)
	{
		this.printable = arg;
		if(jg_fast)
		{
			this._mkDiv = _mkDivIe;
			this._htmRpc = arg? _htmPrtRpc : _htmRpc;
		}
		else this._mkDiv = arg? _mkDivPrt : _mkDiv;
	};

	this.setFont = function(fam, sz, sty)
	{
		this.ftFam = fam;
		this.ftSz = sz;
		this.ftSty = sty || Font.PLAIN;
	};

	this.drawPolyline = this.drawPolyLine = function(x, y)
	{
		for (var i=x.length - 1; i;)
		{--i;
			this.drawLine(x[i], y[i], x[i+1], y[i+1]);
		}
	};

	this.fillRect = function(x, y, w, h)
	{
		this._mkDiv(x, y, w, h);
	};

	this.drawPolygon = function(x, y)
	{
		this.drawPolyline(x, y);
		this.drawLine(x[x.length-1], y[x.length-1], x[0], y[0]);
	};

	this.drawEllipse = this.drawOval = function(x, y, w, h)
	{
		this._mkOv(x, y, w, h);
	};

	this.fillEllipse = this.fillOval = function(left, top, w, h)
	{
		var a = w>>1, b = h>>1,
		wod = w&1, hod = h&1,
		cx = left+a, cy = top+b,
		x = 0, y = b, oy = b,
		aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
		st = (aa2>>1)*(1-(b<<1)) + bb2,
		tt = (bb2>>1) - aa2*((b<<1)-1),
		xl, dw, dh;
		if(w) while(y > 0)
		{
			if(st < 0)
			{
				st += bb2*((x<<1)+3);
				tt += bb4*(++x);
			}
			else if(tt < 0)
			{
				st += bb2*((x<<1)+3) - aa4*(y-1);
				xl = cx-x;
				dw = (x<<1)+wod;
				tt += bb4*(++x) - aa2*(((y--)<<1)-3);
				dh = oy-y;
				this._mkDiv(xl, cy-oy, dw, dh);
				this._mkDiv(xl, cy+y+hod, dw, dh);
				oy = y;
			}
			else
			{
				tt -= aa2*((y<<1)-3);
				st -= aa4*(--y);
			}
		}
		this._mkDiv(cx-a, cy-oy, w, (oy<<1)+hod);
	};

	this.fillArc = function(iL, iT, iW, iH, fAngA, fAngZ)
	{
		var a = iW>>1, b = iH>>1,
		iOdds = (iW&1) | ((iH&1) << 16),
		cx = iL+a, cy = iT+b,
		x = 0, y = b, ox = x, oy = y,
		aa2 = (a*a)<<1, aa4 = aa2<<1, bb2 = (b*b)<<1, bb4 = bb2<<1,
		st = (aa2>>1)*(1-(b<<1)) + bb2,
		tt = (bb2>>1) - aa2*((b<<1)-1),
		// Vars for radial boundary lines
		xEndA, yEndA, xEndZ, yEndZ,
		iSects = (1 << (Math.floor((fAngA %= 360.0)/180.0) << 3))
				| (2 << (Math.floor((fAngZ %= 360.0)/180.0) << 3))
				| ((fAngA >= fAngZ) << 16),
		aBndA = new Array(b+1), aBndZ = new Array(b+1);
		
		// Set up radial boundary lines
		fAngA *= Math.PI/180.0;
		fAngZ *= Math.PI/180.0;
		xEndA = cx+Math.round(a*Math.cos(fAngA));
		yEndA = cy+Math.round(-b*Math.sin(fAngA));
		_mkLinVirt(aBndA, cx, cy, xEndA, yEndA);
		xEndZ = cx+Math.round(a*Math.cos(fAngZ));
		yEndZ = cy+Math.round(-b*Math.sin(fAngZ));
		_mkLinVirt(aBndZ, cx, cy, xEndZ, yEndZ);

		while(y > 0)
		{
			if(st < 0) // Advance x
			{
				st += bb2*((x<<1)+3);
				tt += bb4*(++x);
			}
			else if(tt < 0) // Advance x and y
			{
				st += bb2*((x<<1)+3) - aa4*(y-1);
				ox = x;
				tt += bb4*(++x) - aa2*(((y--)<<1)-3);
				this._mkArcDiv(ox, y, oy, cx, cy, iOdds, aBndA, aBndZ, iSects);
				oy = y;
			}
			else // Advance y
			{
				tt -= aa2*((y<<1)-3);
				st -= aa4*(--y);
				if(y && (aBndA[y] != aBndA[y-1] || aBndZ[y] != aBndZ[y-1]))
				{
					this._mkArcDiv(x, y, oy, cx, cy, iOdds, aBndA, aBndZ, iSects);
					ox = x;
					oy = y;
				}
			}
		}
		this._mkArcDiv(x, 0, oy, cx, cy, iOdds, aBndA, aBndZ, iSects);
		if(iOdds >> 16) // Odd height
		{
			if(iSects >> 16) // Start-angle > end-angle
			{
				var xl = (yEndA <= cy || yEndZ > cy)? (cx - x) : cx;
				this._mkDiv(xl, cy, x + cx - xl + (iOdds & 0xffff), 1);
			}
			else if((iSects & 0x01) && yEndZ > cy)
				this._mkDiv(cx - x, cy, x, 1);
		}
	};

/* fillPolygon method, implemented by Matthieu Haller.
This javascript function is an adaptation of the gdImageFilledPolygon for Walter Zorn lib.
C source of GD 1.8.4 found at http://www.boutell.com/gd/

THANKS to Kirsten Schulz for the polygon fixes!

The intersection finding technique of this code could be improved
by remembering the previous intertersection, and by using the slope.
That could help to adjust intersections to produce a nice
interior_extrema. */
	this.fillPolygon = function(array_x, array_y)
	{
		var i;
		var y;
		var miny, maxy;
		var x1, y1;
		var x2, y2;
		var ind1, ind2;
		var ints;

		var n = array_x.length;
		if(!n) return;

		miny = array_y[0];
		maxy = array_y[0];
		for(i = 1; i < n; i++)
		{
			if(array_y[i] < miny)
				miny = array_y[i];

			if(array_y[i] > maxy)
				maxy = array_y[i];
		}
		for(y = miny; y <= maxy; y++)
		{
			var polyInts = new Array();
			ints = 0;
			for(i = 0; i < n; i++)
			{
				if(!i)
				{
					ind1 = n-1;
					ind2 = 0;
				}
				else
				{
					ind1 = i-1;
					ind2 = i;
				}
				y1 = array_y[ind1];
				y2 = array_y[ind2];
				if(y1 < y2)
				{
					x1 = array_x[ind1];
					x2 = array_x[ind2];
				}
				else if(y1 > y2)
				{
					y2 = array_y[ind1];
					y1 = array_y[ind2];
					x2 = array_x[ind1];
					x1 = array_x[ind2];
				}
				else continue;

				 //  Modified 11. 2. 2004 Walter Zorn
				if((y >= y1) && (y < y2))
					polyInts[ints++] = Math.round((y-y1) * (x2-x1) / (y2-y1) + x1);

				else if((y == maxy) && (y > y1) && (y <= y2))
					polyInts[ints++] = Math.round((y-y1) * (x2-x1) / (y2-y1) + x1);
			}
			polyInts.sort(_CompInt);
			for(i = 0; i < ints; i+=2)
				this._mkDiv(polyInts[i], y, polyInts[i+1]-polyInts[i]+1, 1);
		}
	};

	this.drawString = function(txt, x, y)
	{
		this.htm += '<div style="position:absolute;white-space:nowrap;'+
			'left:' + x + 'px;'+
			'top:' + y + 'px;'+
			'font-family:' +  this.ftFam + ';'+
			'font-size:' + this.ftSz + ';'+
			'color:' + this.color + ';' + this.ftSty + '">'+
			txt +
			'<\/div>';
	};

/* drawStringRect() added by Rick Blommers.
Allows to specify the size of the text rectangle and to align the
text both horizontally (e.g. right) and vertically within that rectangle */
	this.drawStringRect = function(txt, x, y, width,height, halign, cls)
	{
                var classBk = typeof(cls) != 'undefined' ? 'class="'+cls+'" ' : '';
		this.htm += '<div '+classBk+' style="position:absolute;overflow:hidden;'+
			'left:' + x + 'px;'+
			'top:' + y + 'px;'+
			'width:'+width +'px;'+
                        'height:'+height +'px;'+
			'text-align:'+halign+';'+
			'font-family:' +  this.ftFam + ';'+
			'font-size:' + this.ftSz + ';'+
			'line-height: 100%;'+
                         'color:#000000;' + this.ftSty + '">'+
                         '<span style="display:inline-block; vertical-align:middle">' +txt + '<\/span>'+
			'<\/div>';
	};

	this.drawImage = function(imgSrc, x, y, w, h, a)
	{
		this.htm += '<div style="position:absolute;'+
			'left:' + x + 'px;'+
			'top:' + y + 'px;'+
			// w (width) and h (height) arguments are now optional.
			// Added by Mahmut Keygubatli, 14.1.2008
			(w? ('width:' +  w + 'px;') : '') +
			(h? ('height:' + h + 'px;'):'')+'">'+
			'<img src="' + imgSrc +'"'+ (w ? (' width="' + w + '"'):'')+ (h ? (' height="' + h + '"'):'') + (a? (' '+a) : '') + '>'+
			'<\/div>';
	};

	this.clear = function()
	{
		this.htm = "";
		if(this.cnv) this.cnv.innerHTML = "";
	};

	this._mkOvQds = function(cx, cy, x, y, w, h, wod, hod)
	{
		var xl = cx - x, xr = cx + x + wod - w, yt = cy - y, yb = cy + y + hod - h;
		if(xr > xl+w)
		{
			this._mkDiv(xr, yt, w, h);
			this._mkDiv(xr, yb, w, h);
		}
		else
			w = xr - xl + w;
		this._mkDiv(xl, yt, w, h);
		this._mkDiv(xl, yb, w, h);
	};
	
	this._mkArcDiv = function(x, y, oy, cx, cy, iOdds, aBndA, aBndZ, iSects)
	{
		var xrDef = cx + x + (iOdds & 0xffff), y2, h = oy - y, xl, xr, w;

		if(!h) h = 1;
		x = cx - x;

		if(iSects & 0xff0000) // Start-angle > end-angle
		{
			y2 = cy - y - h;
			if(iSects & 0x00ff)
			{
				if(iSects & 0x02)
				{
					xl = Math.max(x, aBndZ[y]);
					w = xrDef - xl;
					if(w > 0) this._mkDiv(xl, y2, w, h);
				}
				if(iSects & 0x01)
				{
					xr = Math.min(xrDef, aBndA[y]);
					w = xr - x;
					if(w > 0) this._mkDiv(x, y2, w, h);
				}
			}
			else
				this._mkDiv(x, y2, xrDef - x, h);
			y2 = cy + y + (iOdds >> 16);
			if(iSects & 0xff00)
			{
				if(iSects & 0x0100)
				{
					xl = Math.max(x, aBndA[y]);
					w = xrDef - xl;
					if(w > 0) this._mkDiv(xl, y2, w, h);
				}
				if(iSects & 0x0200)
				{
					xr = Math.min(xrDef, aBndZ[y]);
					w = xr - x;
					if(w > 0) this._mkDiv(x, y2, w, h);
				}
			}
			else
				this._mkDiv(x, y2, xrDef - x, h);
		}
		else
		{
			if(iSects & 0x00ff)
			{
				if(iSects & 0x02)
					xl = Math.max(x, aBndZ[y]);
				else
					xl = x;
				if(iSects & 0x01)
					xr = Math.min(xrDef, aBndA[y]);
				else
					xr = xrDef;
				y2 = cy - y - h;
				w = xr - xl;
				if(w > 0) this._mkDiv(xl, y2, w, h);
			}
			if(iSects & 0xff00)
			{
				if(iSects & 0x0100)
					xl = Math.max(x, aBndA[y]);
				else
					xl = x;
				if(iSects & 0x0200)
					xr = Math.min(xrDef, aBndZ[y]);
				else
					xr = xrDef;
				y2 = cy + y + (iOdds >> 16);
				w = xr - xl;
				if(w > 0) this._mkDiv(xl, y2, w, h);
			}
		}
	};

	this.setStroke(1);
	this.setFont("verdana,geneva,helvetica,sans-serif", "12px", Font.PLAIN);
	this.color = "#000000";
	this.htm = "";
	this.wnd = wnd || window;

	if(!jg_ok) _chkDHTM(this.wnd);
	if(jg_ok)
	{
		if(cnv)
		{
			if(typeof(cnv) == "string")
				this.cont = document.all? (this.wnd.document.all[cnv] || null)
					: document.getElementById? (this.wnd.document.getElementById(cnv) || null)
					: null;
			else if(cnv == window.document)
				this.cont = document.getElementsByTagName("body")[0];
			// If cnv is a direct reference to a canvas DOM node
			// (option suggested by Andreas Luleich)
			else this.cont = cnv;
			// Create new canvas inside container DIV. Thus the drawing and clearing
			// methods won't interfere with the container's inner html.
			// Solution suggested by Vladimir.
			this.cnv = this.wnd.document.createElement("div");
			this.cnv.style.fontSize=0;
			this.cont.appendChild(this.cnv);
			this.paint = jg_dom? _pntCnvDom : _pntCnvIe;
		}
		else
			this.paint = _pntDoc;
	}
	else
		this.paint = _pntN;

	this.setPrintable(false);
}

function _mkLinVirt(aLin, x1, y1, x2, y2)
{
	var dx = Math.abs(x2-x1), dy = Math.abs(y2-y1),
	x = x1, y = y1,
	xIncr = (x1 > x2)? -1 : 1,
	yIncr = (y1 > y2)? -1 : 1,
	p,
	i = 0;
	if(dx >= dy)
	{
		var pr = dy<<1,
		pru = pr - (dx<<1);
		p = pr-dx;
		while(dx > 0)
		{--dx;
			if(p > 0)    //  Increment y
			{
				aLin[i++] = x;
				y += yIncr;
				p += pru;
			}
			else p += pr;
			x += xIncr;
		}
	}
	else
	{
		var pr = dx<<1,
		pru = pr - (dy<<1);
		p = pr-dy;
		while(dy > 0)
		{--dy;
			y += yIncr;
			aLin[i++] = x;
			if(p > 0)    //  Increment x
			{
				x += xIncr;
				p += pru;
			}
			else p += pr;
		}
	}
	for(var len = aLin.length, i = len-i; i;)
		aLin[len-(i--)] = x;
};

function _CompInt(x, y)
{
	return(x - y);
}

/*
Script: Core.js
	Mootools - My Object Oriented javascript.

License:
	MIT-style license.

MooTools Copyright:
	copyright (c) 2007 Valerio Proietti, <http://mad4milk.net>

MooTools Credits:
	- Class is slightly based on Base.js <http://dean.edwards.name/weblog/2006/03/base/> (c) 2006 Dean Edwards, License <http://creativecommons.org/licenses/LGPL/2.1/>
	- Some functions are inspired by those found in prototype.js <http://prototype.conio.net/> (c) 2005 Sam Stephenson sam [at] conio [dot] net, MIT-style license
	- Documentation by Aaron Newton (aaron.newton [at] cnet [dot] com) and Valerio Proietti.
*/

var MooTools = {
	version: '1.11'
};

/* Section: Core Functions */

/*
Function: $defined
	Returns true if the passed in value/object is defined, that means is not null or undefined.

Arguments:
	obj - object to inspect
*/

function $defined(obj){
	return (obj != undefined);
};

/*
Function: $type
	Returns the type of object that matches the element passed in.

Arguments:
	obj - the object to inspect.

Example:
	>var myString = 'hello';
	>$type(myString); //returns "string"

Returns:
	'element' - if obj is a DOM element node
	'textnode' - if obj is a DOM text node
	'whitespace' - if obj is a DOM whitespace node
	'arguments' - if obj is an arguments object
	'object' - if obj is an object
	'string' - if obj is a string
	'number' - if obj is a number
	'boolean' - if obj is a boolean
	'function' - if obj is a function
	'regexp' - if obj is a regular expression
	'class' - if obj is a Class. (created with new Class, or the extend of another class).
	'collection' - if obj is a native htmlelements collection, such as childNodes, getElementsByTagName .. etc.
	false - (boolean) if the object is not defined or none of the above.
*/

function $type(obj){
	if (!$defined(obj)) return false;
	if (obj.htmlElement) return 'element';
	var type = typeof obj;
	if (type == 'object' && obj.nodeName){
		switch(obj.nodeType){
			case 1: return 'element';
			case 3: return (/\S/).test(obj.nodeValue) ? 'textnode' : 'whitespace';
		}
	}
	if (type == 'object' || type == 'function'){
		switch(obj.constructor){
			case Array: return 'array';
			case RegExp: return 'regexp';
			case Class: return 'class';
		}
		if (typeof obj.length == 'number'){
			if (obj.item) return 'collection';
			if (obj.callee) return 'arguments';
		}
	}
	return type;
};

/*
Function: $merge
	merges a number of objects recursively without referencing them or their sub-objects.

Arguments:
	any number of objects.

Example:
	>var mergedObj = $merge(obj1, obj2, obj3);
	>//obj1, obj2, and obj3 are unaltered
*/

function $merge(){
	var mix = {};
	for (var i = 0; i < arguments.length; i++){
		for (var property in arguments[i]){
			var ap = arguments[i][property];
			var mp = mix[property];
			if (mp && $type(ap) == 'object' && $type(mp) == 'object') mix[property] = $merge(mp, ap);
			else mix[property] = ap;
		}
	}
	return mix;
};

/*
Function: $extend
	Copies all the properties from the second passed object to the first passed Object.
	If you do myWhatever.extend = $extend the first parameter will become myWhatever, and your extend function will only need one parameter.

Example:
	(start code)
	var firstOb = {
		'name': 'John',
		'lastName': 'Doe'
	};
	var secondOb = {
		'age': '20',
		'sex': 'male',
		'lastName': 'Dorian'
	};
	$extend(firstOb, secondOb);
	//firstOb will become:
	{
		'name': 'John',
		'lastName': 'Dorian',
		'age': '20',
		'sex': 'male'
	};
	(end)

Returns:
	The first object, extended.
*/

var $extend = function(){
	var args = arguments;
	if (!args[1]) args = [this, args[0]];
	for (var property in args[1]) args[0][property] = args[1][property];
	return args[0];
};

/*
Function: $native
	Will add a .extend method to the objects passed as a parameter, but the property passed in will be copied to the object's prototype only if non previously existent.
	Its handy if you dont want the .extend method of an object to overwrite existing methods.
	Used automatically in MooTools to implement Array/String/Function/Number methods to browser that dont support them whitout manual checking.

Arguments:
	a number of classes/native javascript objects

*/

var $native = function(){
	for (var i = 0, l = arguments.length; i < l; i++){
		arguments[i].extend = function(props){
			for (var prop in props){
				if (!this.prototype[prop]) this.prototype[prop] = props[prop];
				if (!this[prop]) this[prop] = $native.generic(prop);
			}
		};
	}
};

$native.generic = function(prop){
	return function(bind){
		return this.prototype[prop].apply(bind, Array.prototype.slice.call(arguments, 1));
	};
};

$native(Function, Array, String, Number);

/*
Function: $chk
	Returns true if the passed in value/object exists or is 0, otherwise returns false.
	Useful to accept zeroes.

Arguments:
	obj - object to inspect
*/

function $chk(obj){
	return !!(obj || obj === 0);
};

/*
Function: $pick
	Returns the first object if defined, otherwise returns the second.

Arguments:
	obj - object to test
	picked - the default to return

Example:
	(start code)
		function say(msg){
			alert($pick(msg, 'no meessage supplied'));
		}
	(end)
*/

function $pick(obj, picked){
	return $defined(obj) ? obj : picked;
};

/*
Function: $random
	Returns a random integer number between the two passed in values.

Arguments:
	min - integer, the minimum value (inclusive).
	max - integer, the maximum value (inclusive).

Returns:
	a random integer between min and max.
*/

function $random(min, max){
	return Math.floor(Math.random() * (max - min + 1) + min);
};

/*
Function: $time
	Returns the current timestamp

Returns:
	a timestamp integer.
*/

function $time(){
	return new Date().getTime();
};

/*
Function: $clear
	clears a timeout or an Interval.

Returns:
	null

Arguments:
	timer - the setInterval or setTimeout to clear.

Example:
	>var myTimer = myFunction.delay(5000); //wait 5 seconds and execute my function.
	>myTimer = $clear(myTimer); //nevermind

See also:
	<Function.delay>, <Function.periodical>
*/

function $clear(timer){
	clearTimeout(timer);
	clearInterval(timer);
	return null;
};

/*
Class: Abstract
	Abstract class, to be used as singleton. Will add .extend to any object

Arguments:
	an object

Returns:
	the object with an .extend property, equivalent to <$extend>.
*/

var Abstract = function(obj){
	obj = obj || {};
	obj.extend = $extend;
	return obj;
};

//window, document

var Window = new Abstract(window);
var Document = new Abstract(document);
document.head = document.getElementsByTagName('head')[0];

/*
Class: window
	Some properties are attached to the window object by the browser detection.
	
Note:
	browser detection is entirely object-based. We dont sniff.

Properties:
	window.ie - will be set to true if the current browser is internet explorer (any).
	window.ie6 - will be set to true if the current browser is internet explorer 6.
	window.ie7 - will be set to true if the current browser is internet explorer 7.
	window.gecko - will be set to true if the current browser is Mozilla/Gecko.
	window.webkit - will be set to true if the current browser is Safari/Konqueror.
	window.webkit419 - will be set to true if the current browser is Safari2 / webkit till version 419.
	window.webkit420 - will be set to true if the current browser is Safari3 (Webkit SVN Build) / webkit over version 419.
	window.opera - is set to true by opera itself.
*/

window.xpath = !!(document.evaluate);
if (window.ActiveXObject) window.ie = window[window.XMLHttpRequest ? 'ie7' : 'ie6'] = true;
else if (document.childNodes && !document.all && !navigator.taintEnabled) window.webkit = window[window.xpath ? 'webkit420' : 'webkit419'] = true;
else if (document.getBoxObjectFor != null) window.gecko = true;

/*compatibility*/

window.khtml = window.webkit;

Object.extend = $extend;

/*end compatibility*/

//htmlelement

if (typeof HTMLElement == 'undefined'){
	var HTMLElement = function(){};
	if (window.webkit) document.createElement("iframe"); //fixes safari
	HTMLElement.prototype = (window.webkit) ? window["[[DOMElement.prototype]]"] : {};
}
HTMLElement.prototype.htmlElement = function(){};

//enables background image cache for internet explorer 6

if (window.ie6) try {document.execCommand("BackgroundImageCache", false, true);} catch(e){};

/*
Script: Class.js
	Contains the Class Function, aims to ease the creation of reusable Classes.

License:
	MIT-style license.
*/

/*
Class: Class
	The base class object of the <http://mootools.net> framework.
	Creates a new class, its initialize method will fire upon class instantiation.
	Initialize wont fire on instantiation when you pass *null*.

Arguments:
	properties - the collection of properties that apply to the class.

Example:
	(start code)
	var Cat = new Class({
		initialize: function(name){
			this.name = name;
		}
	});
	var myCat = new Cat('Micia');
	alert(myCat.name); //alerts 'Micia'
	(end)
*/

var Class = function(properties){
	var klass = function(){
		return (arguments[0] !== null && this.initialize && $type(this.initialize) == 'function') ? this.initialize.apply(this, arguments) : this;
	};
	$extend(klass, this);
	klass.prototype = properties;
	klass.constructor = Class;
	return klass;
};

/*
Property: empty
	Returns an empty function
*/

Class.empty = function(){};

Class.prototype = {

	/*
	Property: extend
		Returns the copy of the Class extended with the passed in properties.

	Arguments:
		properties - the properties to add to the base class in this new Class.

	Example:
		(start code)
		var Animal = new Class({
			initialize: function(age){
				this.age = age;
			}
		});
		var Cat = Animal.extend({
			initialize: function(name, age){
				this.parent(age); //will call the previous initialize;
				this.name = name;
			}
		});
		var myCat = new Cat('Micia', 20);
		alert(myCat.name); //alerts 'Micia'
		alert(myCat.age); //alerts 20
		(end)
	*/

	extend: function(properties){
		var proto = new this(null);
		for (var property in properties){
			var pp = proto[property];
			proto[property] = Class.Merge(pp, properties[property]);
		}
		return new Class(proto);
	},

	/*
	Property: implement
		Implements the passed in properties to the base Class prototypes, altering the base class, unlike <Class.extend>.

	Arguments:
		properties - the properties to add to the base class.

	Example:
		(start code)
		var Animal = new Class({
			initialize: function(age){
				this.age = age;
			}
		});
		Animal.implement({
			setName: function(name){
				this.name = name
			}
		});
		var myAnimal = new Animal(20);
		myAnimal.setName('Micia');
		alert(myAnimal.name); //alerts 'Micia'
		(end)
	*/

	implement: function(){
		for (var i = 0, l = arguments.length; i < l; i++) $extend(this.prototype, arguments[i]);
	}

};

//internal

Class.Merge = function(previous, current){
	if (previous && previous != current){
		var type = $type(current);
		if (type != $type(previous)) return current;
		switch(type){
			case 'function':
				var merged = function(){
					this.parent = arguments.callee.parent;
					return current.apply(this, arguments);
				};
				merged.parent = previous;
				return merged;
			case 'object': return $merge(previous, current);
		}
	}
	return current;
};

/*
Script: Array.js
	Contains Array prototypes, <$A>, <$each>

License:
	MIT-style license.
*/

/*
Class: Array
	A collection of The Array Object prototype methods.
*/

//custom methods

Array.extend({

	/*
	Property: forEach
		Iterates through an array; This method is only available for browsers without native *forEach* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:forEach>

		*forEach* executes the provided function (callback) once for each element present in the array. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>['apple','banana','lemon'].each(function(item, index){
		>	alert(index + " = " + item); //alerts "0 = apple" etc.
		>}, bindObj); //optional second arg for binding, not used here
	*/

	forEach: function(fn, bind){
		for (var i = 0, j = this.length; i < j; i++) fn.call(bind, this[i], i, this);
	},

	/*
	Property: filter
		This method is provided only for browsers without native *filter* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Objects:Array:filter>

		*filter* calls a provided callback function once for each element in an array, and constructs a new array of all the values for which callback returns a true value. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values. Array elements which do not pass the callback test are simply skipped, and are not included in the new array.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var biggerThanTwenty = [10,3,25,100].filter(function(item, index){
		> return item > 20;
		>});
		>//biggerThanTwenty = [25,100]
	*/

	filter: function(fn, bind){
		var results = [];
		for (var i = 0, j = this.length; i < j; i++){
			if (fn.call(bind, this[i], i, this)) results.push(this[i]);
		}
		return results;
	},

	/*
	Property: map
		This method is provided only for browsers without native *map* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:map>

		*map* calls a provided callback function once for each element in an array, in order, and constructs a new array from the results. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var timesTwo = [1,2,3].map(function(item, index){
		> return item*2;
		>});
		>//timesTwo = [2,4,6];
	*/

	map: function(fn, bind){
		var results = [];
		for (var i = 0, j = this.length; i < j; i++) results[i] = fn.call(bind, this[i], i, this);
		return results;
	},

	/*
	Property: every
		This method is provided only for browsers without native *every* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:every>

		*every* executes the provided callback function once for each element present in the array until it finds one where callback returns a false value. If such an element is found, the every method immediately returns false. Otherwise, if callback returned a true value for all elements, every will return true. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var areAllBigEnough = [10,4,25,100].every(function(item, index){
		> return item > 20;
		>});
		>//areAllBigEnough = false
	*/

	every: function(fn, bind){
		for (var i = 0, j = this.length; i < j; i++){
			if (!fn.call(bind, this[i], i, this)) return false;
		}
		return true;
	},

	/*
	Property: some
		This method is provided only for browsers without native *some* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:some>

		*some* executes the callback function once for each element present in the array until it finds one where callback returns a true value. If such an element is found, some immediately returns true. Otherwise, some returns false. callback is invoked only for indexes of the array which have assigned values; it is not invoked for indexes which have been deleted or which have never been assigned values.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - the object to bind "this" to (see <Function.bind>)

	Example:
		>var isAnyBigEnough = [10,4,25,100].some(function(item, index){
		> return item > 20;
		>});
		>//isAnyBigEnough = true
	*/

	some: function(fn, bind){
		for (var i = 0, j = this.length; i < j; i++){
			if (fn.call(bind, this[i], i, this)) return true;
		}
		return false;
	},

	/*
	Property: indexOf
		This method is provided only for browsers without native *indexOf* support.
		For more info see <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Global_Objects:Array:indexOf>

		*indexOf* compares a search element to elements of the Array using strict equality (the same method used by the ===, or triple-equals, operator).

	Arguments:
		item - any type of object; element to locate in the array
		from - integer; optional; the index of the array at which to begin the search (defaults to 0)

	Example:
		>['apple','lemon','banana'].indexOf('lemon'); //returns 1
		>['apple','lemon'].indexOf('banana'); //returns -1
	*/

	indexOf: function(item, from){
		var len = this.length;
		for (var i = (from < 0) ? Math.max(0, len + from) : from || 0; i < len; i++){
			if (this[i] === item) return i;
		}
		return -1;
	},

	/*
	Property: each
		Same as <Array.forEach>.

	Arguments:
		fn - function to execute with each item in the array; passed the item and the index of that item in the array
		bind - optional, the object that the "this" of the function will refer to.

	Example:
		>var Animals = ['Cat', 'Dog', 'Coala'];
		>Animals.each(function(animal){
		>	document.write(animal)
		>});
	*/

	/*
	Property: copy
		returns a copy of the array.

	Returns:
		a new array which is a copy of the current one.

	Arguments:
		start - integer; optional; the index where to start the copy, default is 0. If negative, it is taken as the offset from the end of the array.
		length - integer; optional; the number of elements to copy. By default, copies all elements from start to the end of the array.

	Example:
		>var letters = ["a","b","c"];
		>var copy = letters.copy();		// ["a","b","c"] (new instance)
	*/

	copy: function(start, length){
		start = start || 0;
		if (start < 0) start = this.length + start;
		length = length || (this.length - start);
		var newArray = [];
		for (var i = 0; i < length; i++) newArray[i] = this[start++];
		return newArray;
	},

	/*
	Property: remove
		Removes all occurrences of an item from the array.

	Arguments:
		item - the item to remove

	Returns:
		the Array with all occurrences of the item removed.

	Example:
		>["1","2","3","2"].remove("2") // ["1","3"];
	*/

	remove: function(item){
		var i = 0;
		var len = this.length;
		while (i < len){
			if (this[i] === item){
				this.splice(i, 1);
				len--;
			} else {
				i++;
			}
		}
		return this;
	},

	/*
	Property: contains
		Tests an array for the presence of an item.

	Arguments:
		item - the item to search for in the array.
		from - integer; optional; the index at which to begin the search, default is 0. If negative, it is taken as the offset from the end of the array.

	Returns:
		true - the item was found
		false - it wasn't

	Example:
		>["a","b","c"].contains("a"); // true
		>["a","b","c"].contains("d"); // false
	*/

	contains: function(item, from){
		return this.indexOf(item, from) != -1;
	},

	/*
	Property: associate
		Creates an object with key-value pairs based on the array of keywords passed in
		and the current content of the array.

	Arguments:
		keys - the array of keywords.

	Example:
		(start code)
		var Animals = ['Cat', 'Dog', 'Coala', 'Lizard'];
		var Speech = ['Miao', 'Bau', 'Fruuu', 'Mute'];
		var Speeches = Animals.associate(Speech);
		//Speeches['Miao'] is now Cat.
		//Speeches['Bau'] is now Dog.
		//...
		(end)
	*/

	associate: function(keys){
		var obj = {}, length = Math.min(this.length, keys.length);
		for (var i = 0; i < length; i++) obj[keys[i]] = this[i];
		return obj;
	},

	/*
	Property: extend
		Extends an array with another one.

	Arguments:
		array - the array to extend ours with

	Example:
		>var Animals = ['Cat', 'Dog', 'Coala'];
		>Animals.extend(['Lizard']);
		>//Animals is now: ['Cat', 'Dog', 'Coala', 'Lizard'];
	*/

	extend: function(array){
		for (var i = 0, j = array.length; i < j; i++) this.push(array[i]);
		return this;
	},

	/*
	Property: merge
		merges an array in another array, without duplicates. (case- and type-sensitive)

	Arguments:
		array - the array to merge from.

	Example:
		>['Cat','Dog'].merge(['Dog','Coala']); //returns ['Cat','Dog','Coala']
	*/

	merge: function(array){
		for (var i = 0, l = array.length; i < l; i++) this.include(array[i]);
		return this;
	},

	/*
	Property: include
		includes the passed in element in the array, only if its not already present. (case- and type-sensitive)

	Arguments:
		item - item to add to the array (if not present)

	Example:
		>['Cat','Dog'].include('Dog'); //returns ['Cat','Dog']
		>['Cat','Dog'].include('Coala'); //returns ['Cat','Dog','Coala']
	*/

	include: function(item){
		if (!this.contains(item)) this.push(item);
		return this;
	},

	/*
	Property: getRandom
		returns a random item in the Array
	*/

	getRandom: function(){
		return this[$random(0, this.length - 1)] || null;
	},

	/*
	Property: getLast
		returns the last item in the Array
	*/

	getLast: function(){
		return this[this.length - 1] || null;
	}

});

//copies

Array.prototype.each = Array.prototype.forEach;
Array.each = Array.forEach;

/* Section: Utility Functions */

/*
Function: $A()
	Same as <Array.copy>, but as function.
	Useful to apply Array prototypes to iterable objects, as a collection of DOM elements or the arguments object.

Example:
	(start code)
	function myFunction(){
		$A(arguments).each(argument, function(){
			alert(argument);
		});
	};
	//the above will alert all the arguments passed to the function myFunction.
	(end)
*/

function $A(array){
	return Array.copy(array);
};

/*
Function: $each
	Use to iterate through iterables that are not regular arrays, such as builtin getElementsByTagName calls, arguments of a function, or an object.

Arguments:
	iterable - an iterable element or an objct.
	function - function to apply to the iterable.
	bind - optional, the 'this' of the function will refer to this object.

Function argument:
	The function argument will be passed the following arguments.

	item - the current item in the iterator being procesed
	index - integer; the index of the item, or key in case of an object.

Examples:
	(start code)
	$each(['Sun','Mon','Tue'], function(day, index){
		alert('name:' + day + ', index: ' + index);
	});
	//alerts "name: Sun, index: 0", "name: Mon, index: 1", etc.
	//over an object
	$each({first: "Sunday", second: "Monday", third: "Tuesday"}, function(value, key){
		alert("the " + key + " day of the week is " + value);
	});
	//alerts "the first day of the week is Sunday",
	//"the second day of the week is Monday", etc.
	(end)
*/

function $each(iterable, fn, bind){
	if (iterable && typeof iterable.length == 'number' && $type(iterable) != 'object'){
		Array.forEach(iterable, fn, bind);
	} else {
		 for (var name in iterable) fn.call(bind || iterable, iterable[name], name);
	}
};

/*compatibility*/

Array.prototype.test = Array.prototype.contains;

/*end compatibility*/

/*
Script: String.js
	Contains String prototypes.

License:
	MIT-style license.
*/

/*
Class: String
	A collection of The String Object prototype methods.
*/

String.extend({

	/*
	Property: test
		Tests a string with a regular expression.

	Arguments:
		regex - a string or regular expression object, the regular expression you want to match the string with
		params - optional, if first parameter is a string, any parameters you want to pass to the regex ('g' has no effect)

	Returns:
		true if a match for the regular expression is found in the string, false if not.
		See <http://developer.mozilla.org/en/docs/Core_JavaScript_1.5_Reference:Objects:RegExp:test>

	Example:
		>"I like cookies".test("cookie"); // returns true
		>"I like cookies".test("COOKIE", "i") // ignore case, returns true
		>"I like cookies".test("cake"); // returns false
	*/

	test: function(regex, params){
		return (($type(regex) == 'string') ? new RegExp(regex, params) : regex).test(this);
	},

	/*
	Property: toInt
		parses a string to an integer.

	Returns:
		either an int or "NaN" if the string is not a number.

	Example:
		>var value = "10px".toInt(); // value is 10
	*/

	toInt: function(){
		return parseInt(this, 10);
	},

	/*
	Property: toFloat
		parses a string to an float.

	Returns:
		either a float or "NaN" if the string is not a number.

	Example:
		>var value = "10.848".toFloat(); // value is 10.848
	*/

	toFloat: function(){
		return parseFloat(this);
	},

	/*
	Property: camelCase
		Converts a hiphenated string to a camelcase string.

	Example:
		>"I-like-cookies".camelCase(); //"ILikeCookies"

	Returns:
		the camel cased string
	*/

	camelCase: function(){
		return this.replace(/-\D/g, function(match){
			return match.charAt(1).toUpperCase();
		});
	},

	/*
	Property: hyphenate
		Converts a camelCased string to a hyphen-ated string.

	Example:
		>"ILikeCookies".hyphenate(); //"I-like-cookies"
	*/

	hyphenate: function(){
		return this.replace(/\w[A-Z]/g, function(match){
			return (match.charAt(0) + '-' + match.charAt(1).toLowerCase());
		});
	},

	/*
	Property: capitalize
		Converts the first letter in each word of a string to Uppercase.

	Example:
		>"i like cookies".capitalize(); //"I Like Cookies"

	Returns:
		the capitalized string
	*/

	capitalize: function(){
		return this.replace(/\b[a-z]/g, function(match){
			return match.toUpperCase();
		});
	},

	/*
	Property: trim
		Trims the leading and trailing spaces off a string.

	Example:
		>"    i like cookies     ".trim() //"i like cookies"

	Returns:
		the trimmed string
	*/

	trim: function(){
		return this.replace(/^\s+|\s+$/g, '');
	},

	/*
	Property: clean
		trims (<String.trim>) a string AND removes all the double spaces in a string.

	Returns:
		the cleaned string

	Example:
		>" i      like     cookies      \n\n".clean() //"i like cookies"
	*/

	clean: function(){
		return this.replace(/\s{2,}/g, ' ').trim();
	},

	/*
	Property: rgbToHex
		Converts an RGB value to hexidecimal. The string must be in the format of "rgb(255,255,255)" or "rgba(255,255,255,1)";

	Arguments:
		array - boolean value, defaults to false. Use true if you want the array ['FF','33','00'] as output instead of "#FF3300"

	Returns:
		hex string or array. returns "transparent" if the output is set as string and the fourth value of rgba in input string is 0.

	Example:
		>"rgb(17,34,51)".rgbToHex(); //"#112233"
		>"rgba(17,34,51,0)".rgbToHex(); //"transparent"
		>"rgb(17,34,51)".rgbToHex(true); //['11','22','33']
	*/

	rgbToHex: function(array){
		var rgb = this.match(/\d{1,3}/g);
		return (rgb) ? rgb.rgbToHex(array) : false;
	},

	/*
	Property: hexToRgb
		Converts a hexidecimal color value to RGB. Input string must be the hex color value (with or without the hash). Also accepts triplets ('333');

	Arguments:
		array - boolean value, defaults to false. Use true if you want the array [255,255,255] as output instead of "rgb(255,255,255)";

	Returns:
		rgb string or array.

	Example:
		>"#112233".hexToRgb(); //"rgb(17,34,51)"
		>"#112233".hexToRgb(true); //[17,34,51]
	*/

	hexToRgb: function(array){
		var hex = this.match(/^#?(\w{1,2})(\w{1,2})(\w{1,2})$/);
		return (hex) ? hex.slice(1).hexToRgb(array) : false;
	},

	/*
	Property: contains
		checks if the passed in string is contained in the String. also accepts an optional second parameter, to check if the string is contained in a list of separated values.

	Example:
		>'a b c'.contains('c', ' '); //true
		>'a bc'.contains('bc'); //true
		>'a bc'.contains('b', ' '); //false
	*/

	contains: function(string, s){
		return (s) ? (s + this + s).indexOf(s + string + s) > -1 : this.indexOf(string) > -1;
	},

	/*
	Property: escapeRegExp
		Returns string with escaped regular expression characters

	Example:
		>var search = 'animals.sheeps[1]'.escapeRegExp(); // search is now 'animals\.sheeps\[1\]'

	Returns:
		Escaped string
	*/

	escapeRegExp: function(){
		return this.replace(/([.*+?^${}()|[\]\/\\])/g, '\\$1');
	}

});

Array.extend({

	/*
	Property: rgbToHex
		see <String.rgbToHex>, but as an array method.
	*/

	rgbToHex: function(array){
		if (this.length < 3) return false;
		if (this.length == 4 && this[3] == 0 && !array) return 'transparent';
		var hex = [];
		for (var i = 0; i < 3; i++){
			var bit = (this[i] - 0).toString(16);
			hex.push((bit.length == 1) ? '0' + bit : bit);
		}
		return array ? hex : '#' + hex.join('');
	},

	/*
	Property: hexToRgb
		same as <String.hexToRgb>, but as an array method.
	*/

	hexToRgb: function(array){
		if (this.length != 3) return false;
		var rgb = [];
		for (var i = 0; i < 3; i++){
			rgb.push(parseInt((this[i].length == 1) ? this[i] + this[i] : this[i], 16));
		}
		return array ? rgb : 'rgb(' + rgb.join(',') + ')';
	}

});

/* 
Script: Function.js
	Contains Function prototypes and utility functions .

License:
	MIT-style license.

Credits:
	- Some functions are inspired by those found in prototype.js <http://prototype.conio.net/> (c) 2005 Sam Stephenson sam [at] conio [dot] net, MIT-style license
*/

/*
Class: Function
	A collection of The Function Object prototype methods.
*/

Function.extend({

	/*
	Property: create
		Main function to create closures.

	Returns:
		a function.

	Arguments:
		options - An Options object.

	Options:
		bind - The object that the "this" of the function will refer to. Default is the current function.
		event - If set to true, the function will act as an event listener and receive an event as first argument.
				If set to a class name, the function will receive a new instance of this class (with the event passed as argument's constructor) as first argument.
				Default is false.
		arguments - A single argument or array of arguments that will be passed to the function when called.
		
					If both the event and arguments options are set, the event is passed as first argument and the arguments array will follow.
					
					Default is no custom arguments, the function will receive the standard arguments when called.
					
		delay - Numeric value: if set, the returned function will delay the actual execution by this amount of milliseconds and return a timer handle when called.
				Default is no delay.
		periodical - Numeric value: if set, the returned function will periodically perform the actual execution with this specified interval and return a timer handle when called.
				Default is no periodical execution.
		attempt - If set to true, the returned function will try to execute and return either the results or false on error. Default is false.
	*/

	create: function(options){
		var fn = this;
		options = $merge({
			'bind': fn,
			'event': false,
			'arguments': null,
			'delay': false,
			'periodical': false,
			'attempt': false
		}, options);
		if ($chk(options.arguments) && $type(options.arguments) != 'array') options.arguments = [options.arguments];
		return function(event){
			var args;
			if (options.event){
				event = event || window.event;
				args = [(options.event === true) ? event : new options.event(event)];
				if (options.arguments) args.extend(options.arguments);
			}
			else args = options.arguments || arguments;
			var returns = function(){
				return fn.apply($pick(options.bind, fn), args);
			};
			if (options.delay) return setTimeout(returns, options.delay);
			if (options.periodical) return setInterval(returns, options.periodical);
			if (options.attempt) try {return returns();} catch(err){return false;};
			return returns();
		};
	},

	/*
	Property: pass
		Shortcut to create closures with arguments and bind.

	Returns:
		a function.

	Arguments:
		args - the arguments passed. must be an array if arguments > 1
		bind - optional, the object that the "this" of the function will refer to.

	Example:
		>myFunction.pass([arg1, arg2], myElement);
	*/

	pass: function(args, bind){
		return this.create({'arguments': args, 'bind': bind});
	},

	/*
	Property: attempt
		Tries to execute the function, returns either the result of the function or false on error.

	Arguments:
		args - the arguments passed. must be an array if arguments > 1
		bind - optional, the object that the "this" of the function will refer to.

	Example:
		>myFunction.attempt([arg1, arg2], myElement);
	*/

	attempt: function(args, bind){
		return this.create({'arguments': args, 'bind': bind, 'attempt': true})();
	},

	/*
	Property: bind
		method to easily create closures with "this" altered.

	Arguments:
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1

	Returns:
		a function.

	Example:
		>function myFunction(){
		>	this.setStyle('color', 'red');
		>	// note that 'this' here refers to myFunction, not an element
		>	// we'll need to bind this function to the element we want to alter
		>};
		>var myBoundFunction = myFunction.bind(myElement);
		>myBoundFunction(); // this will make the element myElement red.
	*/

	bind: function(bind, args){
		return this.create({'bind': bind, 'arguments': args});
	},

	/*
	Property: bindAsEventListener
		cross browser method to pass event firer

	Arguments:
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1

	Returns:
		a function with the parameter bind as its "this" and as a pre-passed argument event or window.event, depending on the browser.

	Example:
		>function myFunction(event){
		>	alert(event.clientx) //returns the coordinates of the mouse..
		>};
		>myElement.onclick = myFunction.bindAsEventListener(myElement);
	*/

	bindAsEventListener: function(bind, args){
		return this.create({'bind': bind, 'event': true, 'arguments': args});
	},

	/*
	Property: delay
		Delays the execution of a function by a specified duration.

	Arguments:
		delay - the duration to wait in milliseconds.
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1

	Example:
		>myFunction.delay(50, myElement) //wait 50 milliseconds, then call myFunction and bind myElement to it
		>(function(){alert('one second later...')}).delay(1000); //wait a second and alert
	*/

	delay: function(delay, bind, args){
		return this.create({'delay': delay, 'bind': bind, 'arguments': args})();
	},

	/*
	Property: periodical
		Executes a function in the specified intervals of time

	Arguments:
		interval - the duration of the intervals between executions.
		bind - optional, the object that the "this" of the function will refer to.
		args - optional, the arguments passed. must be an array if arguments > 1
	*/

	periodical: function(interval, bind, args){
		return this.create({'periodical': interval, 'bind': bind, 'arguments': args})();
	}

});

/*
Script: Number.js
	Contains the Number prototypes.

License:
	MIT-style license.
*/

/*
Class: Number
	A collection of The Number Object prototype methods.
*/

Number.extend({

	/*
	Property: toInt
		Returns this number; useful because toInt must work on both Strings and Numbers.
	*/

	toInt: function(){
		return parseInt(this);
	},

	/*
	Property: toFloat
		Returns this number as a float; useful because toFloat must work on both Strings and Numbers.
	*/

	toFloat: function(){
		return parseFloat(this);
	},

	/*
	Property: limit
		Limits the number.

	Arguments:
		min - number, minimum value
		max - number, maximum value

	Returns:
		the number in the given limits.

	Example:
		>(12).limit(2, 6.5)  // returns 6.5
		>(-4).limit(2, 6.5)  // returns 2
		>(4.3).limit(2, 6.5) // returns 4.3
	*/

	limit: function(min, max){
		return Math.min(max, Math.max(min, this));
	},

	/*
	Property: round
		Returns the number rounded to specified precision.

	Arguments:
		precision - integer, number of digits after the decimal point. Can also be negative or zero (default).

	Example:
		>12.45.round() // returns 12
		>12.45.round(1) // returns 12.5
		>12.45.round(-1) // returns 10

	Returns:
		The rounded number.
	*/

	round: function(precision){
		precision = Math.pow(10, precision || 0);
		return Math.round(this * precision) / precision;
	},

	/*
	Property: times
		Executes a passed in function the specified number of times

	Arguments:
		function - the function to be executed on each iteration of the loop

	Example:
		>(4).times(alert);
	*/

	times: function(fn){
		for (var i = 0; i < this; i++) fn(i);
	}

});

/*
Script: Element.js
	Contains useful Element prototypes, to be used with the dollar function <$>.

License:
	MIT-style license.

Credits:
	- Some functions are inspired by those found in prototype.js <http://prototype.conio.net/> (c) 2005 Sam Stephenson sam [at] conio [dot] net, MIT-style license
*/

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/

var Element = new Class({

	/*
	Property: initialize
		Creates a new element of the type passed in.

	Arguments:
		el - string; the tag name for the element you wish to create. you can also pass in an element reference, in which case it will be extended.
		props - object; the properties you want to add to your element.
		Accepts the same keys as <Element.setProperties>, but also allows events and styles

	Props:
		the key styles will be used as setStyles, the key events will be used as addEvents. any other key is used as setProperty.

	Example:
		(start code)
		new Element('a', {
			'styles': {
				'display': 'block',
				'border': '1px solid black'
			},
			'events': {
				'click': function(){
					//aaa
				},
				'mousedown': function(){
					//aaa
				}
			},
			'class': 'myClassSuperClass',
			'href': 'http://mad4milk.net'
		});

		(end)
	*/

	initialize: function(el, props){
		if ($type(el) == 'string'){
			if (window.ie && props && (props.name || props.type)){
				var name = (props.name) ? ' name="' + props.name + '"' : '';
				var type = (props.type) ? ' type="' + props.type + '"' : '';
				delete props.name;
				delete props.type;
				el = '<' + el + name + type + '>';
			}
			el = document.createElement(el);
		}
		el = $(el);
		return (!props || !el) ? el : el.set(props);
	}

});

/*
Class: Elements
	- Every dom function such as <$$>, or in general every function that returns a collection of nodes in mootools, returns them as an Elements class.
	- The purpose of the Elements class is to allow <Element> methods to work also on <Elements> array.
	- Elements is also an Array, so it accepts all the <Array> methods.
	- Every node of the Elements instance is already extended with <$>.

Example:
	>$$('myselector').each(function(el){
	> //...
	>});

	some iterations here, $$('myselector') is also an array.

	>$$('myselector').setStyle('color', 'red');
	every element returned by $$('myselector') also accepts <Element> methods, in this example every element will be made red.
*/

var Elements = new Class({

	initialize: function(elements){
		return (elements) ? $extend(elements, this) : this;
	}

});

Elements.extend = function(props){
	for (var prop in props){
		this.prototype[prop] = props[prop];
		this[prop] = $native.generic(prop);
	}
};

/*
Section: Utility Functions

Function: $
	returns the element passed in with all the Element prototypes applied.

Arguments:
	el - a reference to an actual element or a string representing the id of an element

Example:
	>$('myElement') // gets a DOM element by id with all the Element prototypes applied.
	>var div = document.getElementById('myElement');
	>$(div) //returns an Element also with all the mootools extentions applied.

	You'll use this when you aren't sure if a variable is an actual element or an id, as
	well as just shorthand for document.getElementById().

Returns:
	a DOM element or false (if no id was found).

Note:
	you need to call $ on an element only once to get all the prototypes.
	But its no harm to call it multiple times, as it will detect if it has been already extended.
*/

function $(el){
	if (!el) return null;
	if (el.htmlElement) return Garbage.collect(el);
	if ([window, document].contains(el)) return el;
	var type = $type(el);
	if (type == 'string'){
		el = document.getElementById(el);
		type = (el) ? 'element' : false;
	}
	if (type != 'element') return null;
	if (el.htmlElement) return Garbage.collect(el);
	if (['object', 'embed'].contains(el.tagName.toLowerCase())) return el;
	$extend(el, Element.prototype);
	el.htmlElement = function(){};
	return Garbage.collect(el);
};

/*
Function: $$
	Selects, and extends DOM elements. Elements arrays returned with $$ will also accept all the <Element> methods.
	The return type of element methods run through $$ is always an array. If the return array is only made by elements,
	$$ will be applied automatically.

Arguments:
	HTML Collections, arrays of elements, arrays of strings as element ids, elements, strings as selectors.
	Any number of the above as arguments are accepted.

Note:
	if you load <Element.Selectors.js>, $$ will also accept CSS Selectors, otherwise the only selectors supported are tag names.

Example:
	>$$('a') //an array of all anchor tags on the page
	>$$('a', 'b') //an array of all anchor and bold tags on the page
	>$$('#myElement') //array containing only the element with id = myElement. (only with <Element.Selectors.js>)
	>$$('#myElement a.myClass') //an array of all anchor tags with the class "myClass"
	>//within the DOM element with id "myElement" (only with <Element.Selectors.js>)
	>$$(myelement, myelement2, 'a', ['myid', myid2, 'myid3'], document.getElementsByTagName('div')) //an array containing:
	>// the element referenced as myelement if existing,
	>// the element referenced as myelement2 if existing,
	>// all the elements with a as tag in the page,
	>// the element with id = myid if existing
	>// the element with id = myid2 if existing
	>// the element with id = myid3 if existing
	>// all the elements with div as tag in the page

Returns:
	array - array of all the dom elements matched, extended with <$>.  Returns as <Elements>.
*/

document.getElementsBySelector = document.getElementsByTagName;

function $$(){
	var elements = [];
	for (var i = 0, j = arguments.length; i < j; i++){
		var selector = arguments[i];
		switch($type(selector)){
			case 'element': elements.push(selector);
			case 'boolean': break;
			case false: break;
			case 'string': selector = document.getElementsBySelector(selector, true);
			default: elements.extend(selector);
		}
	}
	return $$.unique(elements);
};

$$.unique = function(array){
	var elements = [];
	for (var i = 0, l = array.length; i < l; i++){
		if (array[i].$included) continue;
		var element = $(array[i]);
		if (element && !element.$included){
			element.$included = true;
			elements.push(element);
		}
	}
	for (var n = 0, d = elements.length; n < d; n++) elements[n].$included = null;
	return new Elements(elements);
};

Elements.Multi = function(property){
	return function(){
		var args = arguments;
		var items = [];
		var elements = true;
		for (var i = 0, j = this.length, returns; i < j; i++){
			returns = this[i][property].apply(this[i], args);
			if ($type(returns) != 'element') elements = false;
			items.push(returns);
		};
		return (elements) ? $$.unique(items) : items;
	};
};

Element.extend = function(properties){
	for (var property in properties){
		HTMLElement.prototype[property] = properties[property];
		Element.prototype[property] = properties[property];
		Element[property] = $native.generic(property);
		var elementsProperty = (Array.prototype[property]) ? property + 'Elements' : property;
		Elements.prototype[elementsProperty] = Elements.Multi(property);
	}
};

/*
Class: Element
	Custom class to allow all of its methods to be used with any DOM element via the dollar function <$>.
*/

Element.extend({

	/*
	Property: set
		you can set events, styles and properties with this shortcut. same as calling new Element.
	*/

	set: function(props){
		for (var prop in props){
			var val = props[prop];
			switch(prop){
				case 'styles': this.setStyles(val); break;
				case 'events': if (this.addEvents) this.addEvents(val); break;
				case 'properties': this.setProperties(val); break;
				default: this.setProperty(prop, val);
			}
		}
		return this;
	},

	inject: function(el, where){
		el = $(el);
		switch(where){
			case 'before': el.parentNode.insertBefore(this, el); break;
			case 'after':
				var next = el.getNext();
				if (!next) el.parentNode.appendChild(this);
				else el.parentNode.insertBefore(this, next);
				break;
			case 'top':
				var first = el.firstChild;
				if (first){
					el.insertBefore(this, first);
					break;
				}
			default: el.appendChild(this);
		}
		return this;
	},

	/*
	Property: injectBefore
		Inserts the Element before the passed element.

	Arguments:
		el - an element reference or the id of the element to be injected in.

	Example:
		>html:
		><div id="myElement"></div>
		><div id="mySecondElement"></div>
		>js:
		>$('mySecondElement').injectBefore('myElement');
		>resulting html:
		><div id="mySecondElement"></div>
		><div id="myElement"></div>
	*/

	injectBefore: function(el){
		return this.inject(el, 'before');
	},

	/*
	Property: injectAfter
		Same as <Element.injectBefore>, but inserts the element after.
	*/

	injectAfter: function(el){
		return this.inject(el, 'after');
	},

	/*
	Property: injectInside
		Same as <Element.injectBefore>, but inserts the element inside.
	*/

	injectInside: function(el){
		return this.inject(el, 'bottom');
	},

	/*
	Property: injectTop
		Same as <Element.injectInside>, but inserts the element inside, at the top.
	*/

	injectTop: function(el){
		return this.inject(el, 'top');
	},

	/*
	Property: adopt
		Inserts the passed elements inside the Element.

	Arguments:
		accepts elements references, element ids as string, selectors ($$('stuff')) / array of elements, array of ids as strings and collections.
	*/

	adopt: function(){
		var elements = [];
		$each(arguments, function(argument){
			elements = elements.concat(argument);
		});
		$$(elements).inject(this);
		return this;
	},

	/*
	Property: remove
		Removes the Element from the DOM.

	Example:
		>$('myElement').remove() //bye bye
	*/

	remove: function(){
		return this.parentNode.removeChild(this);
	},

	/*
	Property: clone
		Clones the Element and returns the cloned one.

	Arguments:
		contents - boolean, when true the Element is cloned with childNodes, default true

	Returns:
		the cloned element

	Example:
		>var clone = $('myElement').clone().injectAfter('myElement');
		>//clones the Element and append the clone after the Element.
	*/

	clone: function(contents){
		var el = $(this.cloneNode(contents !== false));
		if (!el.$events) return el;
		el.$events = {};
		for (var type in this.$events) el.$events[type] = {
			'keys': $A(this.$events[type].keys),
			'values': $A(this.$events[type].values)
		};
		return el.removeEvents();
	},

	/*
	Property: replaceWith
		Replaces the Element with an element passed.

	Arguments:
		el - a string representing the element to be injected in (myElementId, or div), or an element reference.
		If you pass div or another tag, the element will be created.

	Returns:
		the passed in element

	Example:
		>$('myOldElement').replaceWith($('myNewElement')); //$('myOldElement') is gone, and $('myNewElement') is in its place.
	*/

	replaceWith: function(el){
		el = $(el);
		this.parentNode.replaceChild(el, this);
		return el;
	},

	/*
	Property: appendText
		Appends text node to a DOM element.

	Arguments:
		text - the text to append.

	Example:
		><div id="myElement">hey</div>
		>$('myElement').appendText(' howdy'); //myElement innerHTML is now "hey howdy"
	*/

	appendText: function(text){
		this.appendChild(document.createTextNode(text));
		return this;
	},

	/*
	Property: hasClass
		Tests the Element to see if it has the passed in className.

	Returns:
		true - the Element has the class
		false - it doesn't

	Arguments:
		className - string; the class name to test.

	Example:
		><div id="myElement" class="testClass"></div>
		>$('myElement').hasClass('testClass'); //returns true
	*/

	hasClass: function(className){
		return this.className.contains(className, ' ');
	},

	/*
	Property: addClass
		Adds the passed in class to the Element, if the element doesnt already have it.

	Arguments:
		className - string; the class name to add

	Example:
		><div id="myElement" class="testClass"></div>
		>$('myElement').addClass('newClass'); //<div id="myElement" class="testClass newClass"></div>
	*/

	addClass: function(className){
		if (!this.hasClass(className)) this.className = (this.className + ' ' + className).clean();
		return this;
	},

	/*
	Property: removeClass
		Works like <Element.addClass>, but removes the class from the element.
	*/

	removeClass: function(className){
		this.className = this.className.replace(new RegExp('(^|\\s)' + className + '(?:\\s|$)'), '$1').clean();
		return this;
	},

	/*
	Property: toggleClass
		Adds or removes the passed in class name to the element, depending on if it's present or not.

	Arguments:
		className - the class to add or remove

	Example:
		><div id="myElement" class="myClass"></div>
		>$('myElement').toggleClass('myClass');
		><div id="myElement" class=""></div>
		>$('myElement').toggleClass('myClass');
		><div id="myElement" class="myClass"></div>
	*/

	toggleClass: function(className){
		return this.hasClass(className) ? this.removeClass(className) : this.addClass(className);
	},

	/*
	Property: setStyle
		Sets a css property to the Element.

		Arguments:
			property - the property to set
			value - the value to which to set it; for numeric values that require "px" you can pass an integer

		Example:
			>$('myElement').setStyle('width', '300px'); //the width is now 300px
			>$('myElement').setStyle('width', 300); //the width is now 300px
	*/

	setStyle: function(property, value){
		switch(property){
			case 'opacity': return this.setOpacity(parseFloat(value));
			case 'float': property = (window.ie) ? 'styleFloat' : 'cssFloat';
		}
		property = property.camelCase();
		switch($type(value)){
			case 'number': if (!['zIndex', 'zoom'].contains(property)) value += 'px'; break;
			case 'array': value = 'rgb(' + value.join(',') + ')';
		}
		this.style[property] = value;
		return this;
	},

	/*
	Property: setStyles
		Applies a collection of styles to the Element.

	Arguments:
		source - an object or string containing all the styles to apply. When its a string it overrides old style.

	Examples:
		>$('myElement').setStyles({
		>	border: '1px solid #000',
		>	width: 300,
		>	height: 400
		>});

		OR

		>$('myElement').setStyles('border: 1px solid #000; width: 300px; height: 400px;');
	*/

	setStyles: function(source){
		switch($type(source)){
			case 'object': Element.setMany(this, 'setStyle', source); break;
			case 'string': this.style.cssText = source;
		}
		return this;
	},

	/*
	Property: setOpacity
		Sets the opacity of the Element, and sets also visibility == "hidden" if opacity == 0, and visibility = "visible" if opacity > 0.

	Arguments:
		opacity - float; Accepts values from 0 to 1.

	Example:
		>$('myElement').setOpacity(0.5) //make it 50% transparent
	*/

	setOpacity: function(opacity){
		if (opacity == 0){
			if (this.style.visibility != "hidden") this.style.visibility = "hidden";
		} else {
			if (this.style.visibility != "visible") this.style.visibility = "visible";
		}
		if (!this.currentStyle || !this.currentStyle.hasLayout) this.style.zoom = 1;
		if (window.ie) this.style.filter = (opacity == 1) ? '' : "alpha(opacity=" + opacity * 100 + ")";
		this.style.opacity = this.$tmp.opacity = opacity;
		return this;
	},

	/*
	Property: getStyle
		Returns the style of the Element given the property passed in.

	Arguments:
		property - the css style property you want to retrieve

	Example:
		>$('myElement').getStyle('width'); //returns "400px"
		>//but you can also use
		>$('myElement').getStyle('width').toInt(); //returns 400

	Returns:
		the style as a string
	*/

	getStyle: function(property){
		property = property.camelCase();
		var result = this.style[property];
		if (!$chk(result)){
			if (property == 'opacity') return this.$tmp.opacity;
			result = [];
			for (var style in Element.Styles){
				if (property == style){
					Element.Styles[style].each(function(s){
						var style = this.getStyle(s);
						result.push(parseInt(style) ? style : '0px');
					}, this);
					if (property == 'border'){
						var every = result.every(function(bit){
							return (bit == result[0]);
						});
						return (every) ? result[0] : false;
					}
					return result.join(' ');
				}
			}
			if (property.contains('border')){
				if (Element.Styles.border.contains(property)){
					return ['Width', 'Style', 'Color'].map(function(p){
						return this.getStyle(property + p);
					}, this).join(' ');
				} else if (Element.borderShort.contains(property)){
					return ['Top', 'Right', 'Bottom', 'Left'].map(function(p){
						return this.getStyle('border' + p + property.replace('border', ''));
					}, this).join(' ');
				}
			}
			if (document.defaultView) result = document.defaultView.getComputedStyle(this, null).getPropertyValue(property.hyphenate());
			else if (this.currentStyle) result = this.currentStyle[property];
		}
		if (window.ie) result = Element.fixStyle(property, result, this);
		if (result && property.test(/color/i) && result.contains('rgb')){
			return result.split('rgb').splice(1,4).map(function(color){
				return color.rgbToHex();
			}).join(' ');
		}
		return result;
	},

	/*
	Property: getStyles
		Returns an object of styles of the Element for each argument passed in.
		Arguments:
		properties - strings; any number of style properties
	Example:
		>$('myElement').getStyles('width','height','padding');
		>//returns an object like:
		>{width: "10px", height: "10px", padding: "10px 0px 10px 0px"}
	*/

	getStyles: function(){
		return Element.getMany(this, 'getStyle', arguments);
	},

	walk: function(brother, start){
		brother += 'Sibling';
		var el = (start) ? this[start] : this[brother];
		while (el && $type(el) != 'element') el = el[brother];
		return $(el);
	},

	/*
	Property: getPrevious
		Returns the previousSibling of the Element, excluding text nodes.

	Example:
		>$('myElement').getPrevious(); //get the previous DOM element from myElement

	Returns:
		the sibling element or undefined if none found.
	*/

	getPrevious: function(){
		return this.walk('previous');
	},

	/*
	Property: getNext
		Works as Element.getPrevious, but tries to find the nextSibling.
	*/

	getNext: function(){
		return this.walk('next');
	},

	/*
	Property: getFirst
		Works as <Element.getPrevious>, but tries to find the firstChild.
	*/

	getFirst: function(){
		return this.walk('next', 'firstChild');
	},

	/*
	Property: getLast
		Works as <Element.getPrevious>, but tries to find the lastChild.
	*/

	getLast: function(){
		return this.walk('previous', 'lastChild');
	},

	/*
	Property: getParent
		returns the $(element.parentNode)
	*/

	getParent: function(){
		return $(this.parentNode);
	},

	/*
	Property: getChildren
		returns all the $(element.childNodes), excluding text nodes. Returns as <Elements>.
	*/

	getChildren: function(){
		return $$(this.childNodes);
	},

	/*
	Property: hasChild
		returns true if the passed in element is a child of the $(element).
	*/

	hasChild: function(el){
		return !!$A(this.getElementsByTagName('*')).contains(el);
	},

	/*
	Property: getProperty
		Gets the an attribute of the Element.

	Arguments:
		property - string; the attribute to retrieve

	Example:
		>$('myImage').getProperty('src') // returns whatever.gif

	Returns:
		the value, or an empty string
	*/

	getProperty: function(property){
		var index = Element.Properties[property];
		if (index) return this[index];
		var flag = Element.PropertiesIFlag[property] || 0;
		if (!window.ie || flag) return this.getAttribute(property, flag);
		var node = this.attributes[property];
		return (node) ? node.nodeValue : null;
	},

	/*
	Property: removeProperty
		Removes an attribute from the Element

	Arguments:
		property - string; the attribute to remove
	*/

	removeProperty: function(property){
		var index = Element.Properties[property];
		if (index) this[index] = '';
		else this.removeAttribute(property);
		return this;
	},

	/*
	Property: getProperties
		same as <Element.getStyles>, but for properties
	*/

	getProperties: function(){
		return Element.getMany(this, 'getProperty', arguments);
	},

	/*
	Property: setProperty
		Sets an attribute for the Element.

	Arguments:
		property - string; the property to assign the value passed in
		value - the value to assign to the property passed in

	Example:
		>$('myImage').setProperty('src', 'whatever.gif'); //myImage now points to whatever.gif for its source
	*/

	setProperty: function(property, value){
		var index = Element.Properties[property];
		if (index) this[index] = value;
		else this.setAttribute(property, value);
		return this;
	},

	/*
	Property: setProperties
		Sets numerous attributes for the Element.

	Arguments:
		source - an object with key/value pairs.

	Example:
		(start code)
		$('myElement').setProperties({
			src: 'whatever.gif',
			alt: 'whatever dude'
		});
		<img src="whatever.gif" alt="whatever dude">
		(end)
	*/

	setProperties: function(source){
		return Element.setMany(this, 'setProperty', source);
	},

	/*
	Property: setHTML
		Sets the innerHTML of the Element.

	Arguments:
		html - string; the new innerHTML for the element.

	Example:
		>$('myElement').setHTML(newHTML) //the innerHTML of myElement is now = newHTML
	*/

	setHTML: function(){
		this.innerHTML = $A(arguments).join('');
		return this;
	},

	/*
	Property: setText
		Sets the inner text of the Element.

	Arguments:
		text - string; the new text content for the element.

	Example:
		>$('myElement').setText('some text') //the text of myElement is now = 'some text'
	*/

	setText: function(text){
		var tag = this.getTag();
		if (['style', 'script'].contains(tag)){
			if (window.ie){
				if (tag == 'style') this.styleSheet.cssText = text;
				else if (tag ==  'script') this.setProperty('text', text);
				return this;
			} else {
				this.removeChild(this.firstChild);
				return this.appendText(text);
			}
		}
		this[$defined(this.innerText) ? 'innerText' : 'textContent'] = text;
		return this;
	},

	/*
	Property: getText
		Gets the inner text of the Element.
	*/

	getText: function(){
		var tag = this.getTag();
		if (['style', 'script'].contains(tag)){
			if (window.ie){
				if (tag == 'style') return this.styleSheet.cssText;
				else if (tag ==  'script') return this.getProperty('text');
			} else {
				return this.innerHTML;
			}
		}
		return ($pick(this.innerText, this.textContent));
	},

	/*
	Property: getTag
		Returns the tagName of the element in lower case.

	Example:
		>$('myImage').getTag() // returns 'img'

	Returns:
		The tag name in lower case
	*/

	getTag: function(){
		return this.tagName.toLowerCase();
	},

	/*
	Property: empty
		Empties an element of all its children.

	Example:
		>$('myDiv').empty() // empties the Div and returns it

	Returns:
		The element.
	*/

	empty: function(){
		Garbage.trash(this.getElementsByTagName('*'));
		return this.setHTML('');
	}

});

Element.fixStyle = function(property, result, element){
	if ($chk(parseInt(result))) return result;
	if (['height', 'width'].contains(property)){
		var values = (property == 'width') ? ['left', 'right'] : ['top', 'bottom'];
		var size = 0;
		values.each(function(value){
			size += element.getStyle('border-' + value + '-width').toInt() + element.getStyle('padding-' + value).toInt();
		});
		return element['offset' + property.capitalize()] - size + 'px';
	} else if (property.test(/border(.+)Width|margin|padding/)){
		return '0px';
	}
	return result;
};

Element.Styles = {'border': [], 'padding': [], 'margin': []};
['Top', 'Right', 'Bottom', 'Left'].each(function(direction){
	for (var style in Element.Styles) Element.Styles[style].push(style + direction);
});

Element.borderShort = ['borderWidth', 'borderStyle', 'borderColor'];

Element.getMany = function(el, method, keys){
	var result = {};
	$each(keys, function(key){
		result[key] = el[method](key);
	});
	return result;
};

Element.setMany = function(el, method, pairs){
	for (var key in pairs) el[method](key, pairs[key]);
	return el;
};

Element.Properties = new Abstract({
	'class': 'className', 'for': 'htmlFor', 'colspan': 'colSpan', 'rowspan': 'rowSpan',
	'accesskey': 'accessKey', 'tabindex': 'tabIndex', 'maxlength': 'maxLength',
	'readonly': 'readOnly', 'frameborder': 'frameBorder', 'value': 'value',
	'disabled': 'disabled', 'checked': 'checked', 'multiple': 'multiple', 'selected': 'selected'
});
Element.PropertiesIFlag = {
	'href': 2, 'src': 2
};

Element.Methods = {
	Listeners: {
		addListener: function(type, fn){
			if (this.addEventListener) this.addEventListener(type, fn, false);
			else this.attachEvent('on' + type, fn);
			return this;
		},

		removeListener: function(type, fn){
			if (this.removeEventListener) this.removeEventListener(type, fn, false);
			else this.detachEvent('on' + type, fn);
			return this;
		}
	}
};

window.extend(Element.Methods.Listeners);
document.extend(Element.Methods.Listeners);
Element.extend(Element.Methods.Listeners);

var Garbage = {

	elements: [],

	collect: function(el){
		if (!el.$tmp){
			Garbage.elements.push(el);
			el.$tmp = {'opacity': 1};
		}
		return el;
	},

	trash: function(elements){
		for (var i = 0, j = elements.length, el; i < j; i++){
			if (!(el = elements[i]) || !el.$tmp) continue;
			if (el.$events) el.fireEvent('trash').removeEvents();
			for (var p in el.$tmp) el.$tmp[p] = null;
			for (var d in Element.prototype) el[d] = null;
			Garbage.elements[Garbage.elements.indexOf(el)] = null;
			el.htmlElement = el.$tmp = el = null;
		}
		Garbage.elements.remove(null);
	},

	empty: function(){
		Garbage.collect(window);
		Garbage.collect(document);
		Garbage.trash(Garbage.elements);
	}

};

window.addListener('beforeunload', function(){
	window.addListener('unload', Garbage.empty);
	if (window.ie) window.addListener('unload', CollectGarbage);
});//MooCanvas, My Object Oriented Canvas Element. Copyright (c) 2007 Olmo Maldonado, <http://ibolmo.no-ip.info/sandbox/moocanvas/>, MIT Style License.
/*
Script: Canvas.js
	Contains the <Canvas> class.

Dependencies:
	MooTools, <http://mootools.net/>
		Element, and its dependencies

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

/*
Class: Canvas
	Creates the element <canvas> and extends the element with getContext if not defined.

Arguments:
	id - The ID of the canvas element
	props - Optional properties for the canvas element, which also gets passed to the new Element
	
Example:
	> var cv = new Canvas('cv');
	> var ctx = cv.getContext('2d');
	> 
	> $(document.body).adopt(cv);
*/
var MooCanvas = new Class({
	
	initialize: function(id, props) {
		var el;
		if($type(id) == 'string') {
			props = $merge({width: 300, height: 150}, props, {'id': id});
			el = new Element('canvas', props);
			if(!el.getContext) {
				if(!CanvasRenderingContext2D.cssFixed) {
					document.createStyleSheet().cssText = 
						'canvas{display:inline-block;overflow:hidden;text-align:left;cursor:default;}' +
						'v\\:*{behavior:url(#default#VML)}' + 
						'o\\:*{behavior:url(#default#VML)}';
					CanvasRenderingContext2D.cssFixed = true;
				}
		
				el.set({
					styles: { 
						width: props.width,
						height: props.height,
						display: 'inline-block',
						overflow: 'hidden'
					},
					
					getContext: function() {
						this.context = this.context || new CanvasRenderingContext2D(el);
						return this.context;
					}
				});
				
			}
		}
		
		return el;
	}
	
});

/*
Class: CanvasRenderingContext2D
	Context2D class with all the Context methods specified by the WHATWG, <http://www.whatwg.org/specs/web-apps/current-work/#the-canvas>
	
Arguments:
	el - Element requesting the context2D
*/
var CanvasRenderingContext2D = new Class({

	initialize: function(el) {
		this.parent = el;
		this.fragment = document.createDocumentFragment();
		this.element = new Element('div', {
			styles: {
				width: el.clientWidth || el.width,
				height: el.clientHeight || el.height,
				overflow: 'hidden',
				position: 'absolute'	
			}
		});
		this.fragment.appendChild(this.element);

		this.m = [
			[1, 0, 0],
			[0, 1, 0],
			[0, 0, 1]
		];
		this.rot = 0;
		this.state = [];
		this.path = [];
		this.delay = 30;
		this.max = 10;
		this.i = 0;
		
		// from excanvas, subpixel rendering.
		this.Z = 10;
		this.Z2 = this.Z / 2;
		this.arcScaleX = 1;
		this.arcScaleY = 1;
		this.currentX = 0;
		this.currentY = 0;
		
		
		this.miterLimit = this.Z * 1;
	},
	
	lineWidth: 1,
	strokeStyle: '#000',
	fillStyle: '#fff',
	globalAlpha: 1,
	globalCompositeOperation: 'source-over',
	lineCap: 'butt',
	lineJoin: 'miter',
	shadowBlur: 0,
	shadowColor: '#000',
	shadowOffsetX: 0,
	shadowOffsetY: 0
	
});

/*
Script: Path.js

Dependencies:
	Canvas.js
	
Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	
	/*
		A path has a list of zero or more subpaths. 
		Each subpath consists of a list of one or more points, 
		connected by straight or curved lines, and a flag indicating whether
		the subpath is closed or not. A closed subpath is one where the
		last point of the subpath is connected to the first point of
		the subpath by a straight line. Subpaths with fewer than two
		points are ignored when painting the path.
	*/

	/*
	Property:
		Empties the list of subpaths so that the context once again has zero
		subpaths.
	*/
	beginPath: function() {
		this.path = [];
		this.moved = false;
	},
	
	/*
	Property:
		Creates a new subpath with the specified point as its first
		(and only) point.
	*/
	moveTo: function(x, y) {
		this.path.push('m', this.coord(x, y));
		this.currentX = x;
		this.currentY = y;
		this.moved = true;
	},
	
	/*
	Property:
		Does nothing if the context has no subpaths.
		Otherwise, marks the last subpath as closed, create a new
		subpath whose first point is the same as the previous 
		subpath's first point, and finally add this new subpath to the
		path.
	*/
	closePath: function() {
		this.path.push('x');
	},
	
	/*
	Property:
		Method must do nothing if the context has no subpaths. Otherwise, 
		it must connect the last point in the subpath to the given point 
		(x, y) using a straight line, and must then add the given point 
		(x, y) to the subpath.
	*/
	lineTo: function(x, y) {
		this.path.push((this.moved ? 'l' : ','), this.coord(x, y));
		this.currentX = x;
		this.currentY = y;
		this.moved = false;
	},

	/*
	Property:
		Method must do nothing if the context has no subpaths. Otherwise, 
		it must connect the last point in the subpath to the given point 
		(x, y) using a straight line, and must then add the given point 
		(x, y) to the subpath.
	*/
	quadraticCurveTo: function(cpx, cpy, x, y) {
		var cx = 2 * cpx,
			cy = 2 * cpy;
			
		this.bezierCurveTo(
			(cx + this.currentX) / 3, 
			(cy + this.currentY) / 3, 
			(cx + x) / 3, 
			(cy + y) / 3, 
			x, 
			y
		);
	},
	
	/*
	Property:
		Method must do nothing if the context has no subpaths. Otherwise, 
		it must connect the last point in the subpath to the given point 
		(x, y) using a bezier curve with control points (cp1x, cp1y) and 
		(cp2x, cp2y). Then, it must add the point (x, y) to the subpath.
	*/
	bezierCurveTo: function(cp0x, cp0y, cp1x, cp1y, x, y) {
		this.path.push(' c ',
			this.coord(cp0x, cp0y), ",",
			this.coord(cp1x, cp1y), ",",
			this.coord(x, y)
		);
		
		this.currentX = x;
		this.currentY = y;
	},
	
	/*
	Property:
		Method must do nothing if the context has no subpaths. If the context
		does have a subpath, then the behaviour depends on the arguments and 
		the last point in the subpath.
		
		Let the point (x0, y0) be the last point in the subpath. Let The Arc 
		be the shortest arc given by circumference of the circle that has one 
		point tangent to the line defined by the points (x0, y0) and (x1, y1), 
		another point tangent to the line defined by the points (x1, y1) and 
		(x2, y2), and that has radius radius. The points at which this circle 
		touches these two lines are called the start and end tangent points 
		respectively.
		
		If the point (x2, y2) is on the line defined by the points (x0, y0) 
		and (x1, y1) then the method must do nothing, as no arc would satisfy 
		the above constraints.
		
		Otherwise, the method must connect the point (x0, y0) to the start 
		tangent point by a straight line, then connect the start tangent point 
		to the end tangent point by The Arc, and finally add the start and end 
		tangent points to the subpath.
		
		Negative or zero values for radius must cause the implementation to 
		raise an INDEX_SIZE_ERR exception.
	*/
	arcTo: function(x, y, w, h) {

	},
	
	/*
	Property:
		Method draws an arc. If the context has any subpaths, then the method 
		must add a straight line from the last point in the subpath to the 
		start point of the arc. In any case, it must draw the arc between the 
		start point of the arc and the end point of the arc, and add the start 
		and end points of the arc to the subpath. The arc and its start and 
		end points are defined as follows:
		
		Consider a circle that has its origin at (x, y) and that has radius 
		radius. The points at startAngle and endAngle along the circle's 
		circumference, measured in radians clockwise from the positive x-axis, 
		are the start and end points respectively. The arc is the path along 
		the circumference of this circle from the start point to the end point, 
		going anti-clockwise if the anticlockwise argument is true, and 
		clockwise otherwise.
		
		Negative or zero values for radius must cause the implementation to 
		raise an INDEX_SIZE_ERR exception.
	*/
	arc: function(x, y, rad, a0, a1, cw) {
		if(this.rot === 0) rad *= this.Z;
		
		var x0 = Math.cos(a0) * rad,
			y0 = Math.sin(a0) * rad,
			x1 = Math.cos(a1) * rad,
			y1 = Math.sin(a1) * rad;
			
		if (this.rot !== 0) {
			var da = Math.PI / 24;
			this.lineTo(x0 + x, y0 + y);
			if(cw) {
				if (a0 < a1) a0 += 2 * Math.PI;
				while(a0 - da > a1) this.lineTo(x + Math.cos(a0 -= da) * rad, y + Math.sin(a0) * rad);
			} else {
				if (a1 < a0) a1 += 2 * Math.PI;
				while(a0 + da < a1) this.lineTo(x + Math.cos(a0 += da) * rad, y + Math.sin(a0) * rad);
			}
			this.lineTo(x1 + x, y1 + y);
			return;
		}
		
		if (x0 == x1 && !cw) x0 += 0.125;
		
		var c = this.getCoords(x, y);
		this.path.push(cw ? 'at ' : 'wa ',
			Math.round(c.x - this.arcScaleX * rad) + ',' + Math.round(c.y - this.arcScaleY * rad),  ' ',
			Math.round(c.x + this.arcScaleX * rad) + ',' + Math.round(c.y + this.arcScaleY * rad),  ' ',
			this.coord(x0 + x - this.Z2, y0 + y - this.Z2), ' ',
			this.coord(x1 + x - this.Z2, y1 + y - this.Z2)
		);
	},

	/*
	Property:
		method must create a new subpath containing just the four points 
		(x, y), (x+w, y), (x+w, y+h), (x, y+h), with those four points 
		connected by straight lines, and must then mark the subpath as 
		closed. It must then create a new subpath with the point (x, y) 
		as the only point in the subpath.
		
		Negative values for w and h must cause the implementation to raise 
		an INDEX_SIZE_ERR exception.
	*/
	rect: function(x, y, w, h) {
		this.moveTo(x, y);
		this.lineTo(x + w, y);
		this.lineTo(x + w, y + h);
		this.lineTo(x, y + h);
		this.closePath();
	},
	
	/*
	Property:
		Method must fill each subpath of the current path in turn, using 
		fillStyle, and using the non-zero winding number rule. Open subpaths 
		must be implicitly closed when being filled (without affecting the 
		actual subpaths).
	*/
	fill: function() {
		this.stroke(true);
	},


	/*
	Property:
		Method must stroke each subpath of the current path in turn, using 
		the strokeStyle, lineWidth, lineJoin, and (if appropriate) miterLimit 
		attributes.
		
		Paths, when filled or stroked, must be painted without affecting the 
		current path, and must be subject to transformations, shadow effects, 
		global alpha, clipping paths, and global composition operators.
		
		The transformation is applied to the path when it is drawn, not when 
		the path is constructed. Thus, a single path can be constructed and 
		then drawn according to different transformations without recreating 
		the path. 
	*/
	
	stroke: function(fill) {
		if(!this.path.length) return;
		var a, color;
		if (fill) {
			a = [1000, '<v:fill ' + this.processColorObject(this.fillStyle) + '></v:fill>'];
		} else {
			color = this.processColor(this.strokeStyle);
			a = [10, 
			'<v:stroke ' +
				'endcap="' + ((this.lineCap == 'butt') ? 'flat' : this.lineCap) + '" ' +
				'joinstyle="' + this.lineJoin + '" ' +
				'color="' + color.color + '" ' +
				'opacity="' + color.opacity + '"' +
			'/>'];
		}
		this.element.insertAdjacentHTML('beforeEnd', 
			'<v:shape ' +
				'path="' + this.path.join('') + 'e" ' +
				'stroked="' + !fill + '" ' +
				(!fill ? ('strokeweight="' + 0.8 * this.lineWidth * this.m[0][0] + '" ') : '') +
				'filled="' + !!fill + '" ' +
				'coordsize="' + this.Z * a[0] + ',' +  this.Z * a[0] + '" ' +
				'style="width:' + a[0] + 'px; height:' + a[0] + 'px; position: absolute;">' +
				a[1] +
			'</v:shape>'
		);
		
		this.parent.appendChild(this.fragment);
		
		if(fill && this.fillStyle.img) this.element.getLast().fill.alignshape = false; // not sure why this has to be called explicitly
											 
		this.path = [];
	},

	/*
	Property:
		Method must create a new clipping path by calculating the intersection 
		of the current clipping path and the area described by the current path 
		(after applying the current transformation), using the non-zero winding 
		number rule. Open subpaths must be implicitly closed when computing the 
		clipping path, without affecting the actual subpaths.
		
		When the context is created, the initial clipping path is the rectangle 
		with the top left corner at (0,0) and the width and height of the 
		coordinate space. 
	*/
	clip: function() {

	},
	
	/*
	Property:
		Method must return true if the point given by the x and y coordinates 
		passed to the method, when treated as coordinates in the canvas' 
		coordinate space unaffected by the current transformation, is within 
		the area of the canvas that is inside the current path; and must 
		return false otherwise.
	*/
	isPointInPath: function(x, y) {
	
	},
	
	processColor: function(col) { //path
		var a = this.globalAlpha;	
		if (col.substr(0, 3) == 'rgb') {
			if (col.charAt(3) == "a") {
				a*= col.match(/([\d.]*)\)$/)[1];
			}
			
			col = col.rgbToHex();
		}
		return {
			color: col,
			opacity: a
		};
	},
	
	/* 
		If a gradient has no stops defined, then the gradient must be treated as a 
		solid transparent black. Gradients are, naturally, only painted where the 
		stroking or filling effect requires that they be drawn.
	*/
	processColorObject: function(obj) {
		var ret = '', col;
		if(obj.addColorStop) {
			ret += ((obj.r0) ? (
				'type="gradientradial" ' +
				'focusposition="0.2, 0.2" ' +
				'focussize="0.2, 0.2" '
			) : (
				'type="gradient" ' +
				'focus="0" ' +
				'angle="' + (180 + (180 * obj.angle(obj.x0, obj.y0, obj.x1, obj.y1) / Math.PI)) + '" '
			)) +
				'color="' + obj.col0.color + '" ' +
				'opacity="' + obj.col0.opacity * 100 + '%" ' +
				'color2="' + obj.col1.color + '" ' +
				'o:opacity2="' + obj.col1.opacity * 100 + '%" ' +
				'colors="';
			if(obj.stops) {
				for (var i = 0, l = obj.stops.length; i < l; i++) {
					ret += Math.round(100 * obj.stops[i][0]) + '% ' + obj.stops[i][1];
				}
			}
			ret +=
				'" ';
		} else if(obj.img) { //pattern
			ret +=
				'type="tile" ' +
				'src="' + obj.img.src + '" ';
		} else {
			col = this.processColor(obj);
			ret +=
				'color="' + col.color + '" ' +
				'opacity="' + col.opacity + '" ';
		}
		
		return ret;
	},
	
	getCoords: function(x, y) {
		var m = this.m;
		return {
			x: this.Z * (x * m[0][0] + y * m[1][0] + m[2][0]) - this.Z2, 
			y: this.Z * (x * m[0][1] + y * m[1][1] + m[2][1]) - this.Z2
		};
	},

	coord: function(x, y) {
		var m = this.m;
		return  [
			 Math.round(this.Z * (x * m[0][0] + y * m[1][0] + m[2][0]) - this.Z2), ',',
			 Math.round(this.Z * (x * m[0][1] + y * m[1][1] + m[2][1]) - this.Z2)
		].join('');
	}
});
/*
Script: Rects.js

Dependencies: 
	Canvas.js, Path.js

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	
	/*
	Property: clearRect
		Clears the pixels in the specified rectangle.
		If height or width are zero has no effect.
		
		If no arguments, clears all of the canvas
		
		Currently, clearRect clears all of the canvas.
	 */
	clearRect: function(x, y, w, h) {
		//if((x <= 0) && (y <= 0) && ( x + w >= this.element.width) && (y + h >= this.element.height)){
			this.element.innerHTML = '';
		//} else {
		//	var f0 = this.fillStyle;
		//	this.fillStyle = '#fff';
		//	this.fillRect(x, y, w, h);
		//	this.fillStyle = f0;
		//}
	},
	
	/*
	Property: fillRect
		Paints the specified rectangle using fillStyle.
		If height or width are zero, this method has no effect.
	 */
	fillRect: function(x, y, w, h) {
		this.rect(x, y, w, h);
		this.fill();
	},
	
	/*
		Draws a rectangular outline of the specified size.
		If width or height are zero: ??
	 */
	strokeRect: function(x, y, w, h) {
		this.rect(x, y, w, h);
		this.stroke();
	}
	
});
/*
Script: Transform.js

Dependencies:
	Canvas.js

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	/*
		The transformation matrix is applied to all drawing operations prior 
		to their being rendered. It is also applied when creating the clip region.
		*  The transformations must be performed in reverse order. For instance, 
		if a scale transformation that doubles the width is applied, followed 
		by a rotation transformation that rotates drawing operations by a 
		quarter turn, and a rectangle twice as wide as it is tall is then 
		drawn on the canvas, the actual result will be a square.
	*/

  	/*
  	Property: scale
		Method must add the scaling transformation described by the arguments 
		to the transformation matrix. The x argument represents the scale factor 
		in the horizontal direction and the y argument represents the scale 
		factor in the vertical direction. The factors are multiples.
	*/
	scale: function(x, y) {
		this.arcScaleX *= x;
		this.arcScaleY *= y;
		
		this.matMult([
			[x, 0, 0],
			[0, y, 0],
			[0, 0, 1]
		]);
	},
	
  	/*
  	Property: rotate
		Method must add the rotation transformation described by the argument 
		to the transformation matrix. The angle argument represents a clockwise 
		rotation angle expressed in radians.
	*/
	rotate: function(ang) {
		this.rot += ang;
		var c = Math.cos(ang),
			s = Math.sin(ang);
		
		this.matMult([
			[ c, s, 0],
			[-s, c, 0],
			[ 0, 0, 1]
		]);
	},
	
  	/*
  	Property: translate
		Method must add the translation transformation described by the arguments 
		to the transformation matrix. The x argument represents the translation 
		distance in the horizontal direction and the y argument represents the 
		translation distance in the vertical direction. The arguments are in 
		coordinate space units.
	*/
	translate: function(x, y) {
		this.matMult([
			[1, 0, 0],
			[0, 1, 0],
			[x, y, 1]
		]);
	},
	
  	/*
  	Property: transform
		Method must multiply the current transformation matrix with the matrix described
		by the inputs.
	*/
 	transform: function(m11, m12, m21, m22, dx, dy) {
		this.matMult([
			[m11, m21, dx],
			[m12, m22, dy],
			[  0,   0,  1]
		]);		
	},
  
  	/*
  	Property: setTransform
  		Method must reset the current transform to the identity matrix, and then invoke 
  		the transform method with the same arguments.
  	*/
	setTransform: function(m11, m12, m21, m22, dx, dy) {
		this.m = [
			[1, 0, 0],
			[0, 1, 0],
			[0, 0, 1]
		];
		
		this.transform(m11, m12, m21, m22, dx, dy);
	},
	
	/*
		Property: matMult
			Method to multiply 3x3 matrice. Currently takes input and multiplies against
			the transform matrix and saves the result to the transform matrix.
			
			This is an optimized multiplication method. Will only multiply if the input
			value is not zero. Thus, minimizing multiplications and additions.
	*/
	matMult: function(b) {
		var m = this.m,
			o = [
				[0, 0, 0], 
				[0, 0, 0], 
				[0, 0, 0]
			];
		
		for(var i = 0; i < 3; i++) {
			if(b[0][i] !== 0) this.sum(o[0], this.mult(b[0][i], m[i]));
			if(b[1][i] !== 0) this.sum(o[1], this.mult(b[1][i], m[i]));
			if(b[2][i] !== 0) this.sum(o[2], this.mult(b[2][i], m[i]));
		}
		
		this.m = [o[0], o[1], o[2]];
	},

	mult: function(x, y) {
		return [x * y[0], x * y[1], x * y[2]];	
	},
	
	sum: function(o, v) {
		o[0] += v[0];
		o[1] += v[1];
		o[2] += v[2];
	}
});
/*
Script: Image.js

Dependencies:
	Canvas.js

Author:
	Olmo Maldonado, <http://olmo-maldonado.com/>
	
Credits:
	Lightly based from Ralph Sommerer's work: <http://blogs.msdn.com/sompost/archive/2006/02/22/536967.aspx>
	Moderately based from excanvas: <http://excanvas.sourceforge.net/>
	Great thanks to Inviz, <http://inviz.ru/>, for his optimizing help.
	
License:
	MIT License, <http://en.wikipedia.org/wiki/MIT_License>
*/

CanvasRenderingContext2D.implement({
	/*
	Property: drawImage
		This method is overloaded with three variants: drawImage(image, dx, dy),
		drawImage(image, dx, dy, dw, dh), and drawImage(image, sx, sy, sw, sh, 
		dx, dy, dw, dh). (Actually it is overloaded with six; each of those three 
		can take either an HTMLImageElement or an HTMLCanvasElement for the image 
		argument.) If not specified, the dw and dh arguments default to the values 
		of sw and sh, interpreted such that one CSS pixel in the image is treated 
		as one unit in the canvas coordinate space. If the sx, sy, sw, and sh 
		arguments are omitted, they default to 0, 0, the image's intrinsic width 
		in image pixels, and the image's intrinsic height in image pixels, 
		respectively.
		
		If the image is of the wrong type, the implementation must raise a 
		TYPE_MISMATCH_ERR exception. If one of the sy, sw, sw, and sh arguments 
		is outside the size of the image, or if one of the dw and dh arguments 
		is negative, the implementation must raise an INDEX_SIZE_ERR  exception.
		
		The specified region of the image specified by the source rectangle 
		(sx, sy, sw, sh) must be painted on the region of the canvas specified 
		by the destination rectangle (dx, dy, dw, dh).
		
		Images are painted without affecting the current path, and are subject to
		transformations, shadow effects, global alpha, clipping paths, and global 
		composition operators.
	*/
	drawImage: function (image, var_args) {
		var args = arguments, 
			length = args.length, 
			off = (length == 9) ? 4 : 0;
			
		if(!((length + '').test(/3|5|9/))) throw 'Wrong number of arguments';
		
		var w0 = image.runtimeStyle.width, 
			h0 = image.runtimeStyle.height;
		image.runtimeStyle.width = 'auto';
		image.runtimeStyle.height = 'auto';
	
		var w = image.width, 
			h = image.height;
		image.runtimeStyle.width = w0;
		image.runtimeStyle.height = h0;
		
		var sx = 0, 
			sy = 0, 
			sw = w, 
			sh = h, 
			dx = args[1 + off], 
			dy = args[2 + off], 
			dw = args[3 + off] || w, 
			dh = args[4 + off] || h;
		
		if (length == 9) {
			sx = args[1];
			sy = args[2];
			sw = args[3];
			sh = args[4];
		}

		var d = this.getCoords(dx, dy),
			vmlStr = 
			'<v:group coordsize="' + this.Z * 10 + ',' + this.Z * 10 + '" ' + 
				'coordorigin="0,0" ' +
				'style="width:10;height:10;position:absolute;';
		
		if (this.m[0][0] != 1 || this.m[0][1]) {
			var max = Math.max(
				this.getCoords(dx + dw, dy), 
				this.getCoords(dx, dy + dh), 
				this.getCoords(dx + dw, dy + dh)
			);
			
			vmlStr += 
				'padding:0;' +
				'padding-right:' + Math.round(Math.max(d.x, max) / this.Z) + 'px;' +
				'padding-bottom:' + Math.round(Math.max(d.y, max) / this.Z) + 'px;' +
				'filter:progid:DXImageTransform.Microsoft.Matrix(' +
					"M11='" + this.m[0][0] + "', M12='" + this.m[1][0] + "', " +
					"M21='" + this.m[0][1] + "', M22='" + this.m[1][1] + "', " +
					"Dx='" + Math.round(d.x / this.Z) + "', Dy='" + Math.round(d.y / this.Z) + "', " +
					"sizingmethod='clip'" +
				');';
		} else {
			vmlStr += 
				'top:' + Math.round(d.y / this.Z) + 'px;' +
				'left:' + Math.round(d.x / this.Z) + 'px;';
		}
		
		this.element.insertAdjacentHTML('BeforeEnd', vmlStr + 
			'"><v:image src="' + image.src + '" ' +
				'style="width:' + this.Z * dw + ';height:' + this.Z * dh + ';" ' +
				'cropleft="' + sx / w + '" ' +
				'croptop="' + sy / h + '" ' +
				'cropright="' + (w - sx - sw) / w + '" ' +
				'cropbottom="' + (h - sy - sh) / h + '" ' +
			'/></v:group>'
		);
		
		this.parent.appendChild(this.fragment);
	},

	drawImageFromRect: Function.empty,
	
	/*
	Property: getImageData
		Method must return an ImageData object representing the underlying 
		pixel data for the area of the canvas denoted by the rectangle which 
		has one corner at the (sx, sy) coordinate, and that has width sw and 
		height sh. Pixels outside the canvas must be returned as transparent 
		black.
	*/
	getImageData: function(sx, sy, sw, sh) {

	},
	
	/*
	Property: putImageData
		Method must take the given ImageData structure, and draw it at the 
		specified location dx,dy in the canvas coordinate space, mapping each 
		pixel represented by the ImageData structure into one device pixel.
	*/
	putImageData: function(image, dx, dy) {
	
	},

	getCoords: function(x, y) {
		var m = this.m;
		return {
			x: this.Z * (x * m[0][0] + y * m[1][0] + m[2][0]) - this.Z2, 
			y: this.Z * (x * m[0][1] + y * m[1][1] + m[2][1]) - this.Z2
		};
	}
	
});

/**This notice must be untouched at all times.
This is the COMPRESSED version of the Draw2D Library
WebSite: http://www.draw2d.org
Copyright: 2006 Andreas Herz. All rights reserved.
Created: 5.11.2006 by Andreas Herz (Web: http://www.freegroup.de )
LICENSE: LGPL
**/
Event=function(){
this.type=null;
this.target=null;
this.relatedTarget=null;
this.cancelable=false;
this.timeStamp=null;
this.returnValue=true;
};
Event.prototype.initEvent=function(sType,_3a0c){
this.type=sType;
this.cancelable=_3a0c;
this.timeStamp=(new Date()).getTime();
};
Event.prototype.preventDefault=function(){
if(this.cancelable){
this.returnValue=false;
}
};
Event.fireDOMEvent=function(_3a0d,_3a0e){
if(document.createEvent){
var evt=document.createEvent("Events");
evt.initEvent(_3a0d,true,true);
_3a0e.dispatchEvent(evt);
}else{
if(document.createEventObject){
var evt=document.createEventObject();
_3a0e.fireEvent("on"+_3a0d,evt);
}
}
};
EventTarget=function(){
this.eventhandlers=new Object();
};
EventTarget.prototype.addEventListener=function(sType,_3a11){
if(typeof this.eventhandlers[sType]=="undefined"){
this.eventhandlers[sType]=new Array;
}
this.eventhandlers[sType][this.eventhandlers[sType].length]=_3a11;
};
EventTarget.prototype.dispatchEvent=function(_3a12){
_3a12.target=this;
if(typeof this.eventhandlers[_3a12.type]!="undefined"){
for(var i=0;i<this.eventhandlers[_3a12.type].length;i++){
this.eventhandlers[_3a12.type][i](_3a12);
}
}
return _3a12.returnValue;
};
EventTarget.prototype.removeEventListener=function(sType,_3a15){
if(typeof this.eventhandlers[sType]!="undefined"){
var _3a16=new Array;
for(var i=0;i<this.eventhandlers[sType].length;i++){
if(this.eventhandlers[sType][i]!=_3a15){
_3a16[_3a16.length]=this.eventhandlers[sType][i];
}
}
this.eventhandlers[sType]=_3a16;
}
};
ArrayList=function(){
this.increment=10;
this.size=0;
this.data=new Array(this.increment);
};
ArrayList.EMPTY_LIST=new ArrayList();
ArrayList.prototype.reverse=function(){
var _3dc7=new Array(this.size);
for(var i=0;i<this.size;i++){
_3dc7[i]=this.data[this.size-i-1];
}
this.data=_3dc7;
};
ArrayList.prototype.getCapacity=function(){
return this.data.length;
};
ArrayList.prototype.getSize=function(){
return this.size;
};
ArrayList.prototype.isEmpty=function(){
return this.getSize()==0;
};
ArrayList.prototype.getLastElement=function(){
if(this.data[this.getSize()-1]!=null){
return this.data[this.getSize()-1];
}
};
ArrayList.prototype.getFirstElement=function(){
if(this.data[0]!=null){
return this.data[0];
}
};
ArrayList.prototype.get=function(i){
return this.data[i];
};
ArrayList.prototype.add=function(obj){
if(this.getSize()==this.data.length){
this.resize();
}
this.data[this.size++]=obj;
};
ArrayList.prototype.addAll=function(obj){
for(var i=0;i<obj.getSize();i++){
this.add(obj.get(i));
}
};
ArrayList.prototype.remove=function(obj){
var index=this.indexOf(obj);
if(index>=0){
return this.removeElementAt(index);
}
return null;
};
ArrayList.prototype.insertElementAt=function(obj,index){
if(this.size==this.capacity){
this.resize();
}
for(var i=this.getSize();i>index;i--){
this.data[i]=this.data[i-1];
}
this.data[index]=obj;
this.size++;
};
ArrayList.prototype.removeElementAt=function(index){
var _3dd3=this.data[index];
for(var i=index;i<(this.getSize()-1);i++){
this.data[i]=this.data[i+1];
}
this.data[this.getSize()-1]=null;
this.size--;
return _3dd3;
};
ArrayList.prototype.removeAllElements=function(){
this.size=0;
for(var i=0;i<this.data.length;i++){
this.data[i]=null;
}
};
ArrayList.prototype.indexOf=function(obj){
for(var i=0;i<this.getSize();i++){
if(this.data[i]==obj){
return i;
}
}
return -1;
};
ArrayList.prototype.contains=function(obj){
for(var i=0;i<this.getSize();i++){
if(this.data[i]==obj){
return true;
}
}
return false;
};
ArrayList.prototype.resize=function(){
newData=new Array(this.data.length+this.increment);
for(var i=0;i<this.data.length;i++){
newData[i]=this.data[i];
}
this.data=newData;
};
ArrayList.prototype.trimToSize=function(){
var temp=new Array(this.getSize());
for(var i=0;i<this.getSize();i++){
temp[i]=this.data[i];
}
this.size=temp.length-1;
this.data=temp;
};
ArrayList.prototype.sort=function(f){
var i,j;
var _3ddf;
var _3de0;
var _3de1;
var _3de2;
for(i=1;i<this.getSize();i++){
_3de0=this.data[i];
_3ddf=_3de0[f];
j=i-1;
_3de1=this.data[j];
_3de2=_3de1[f];
while(j>=0&&_3de2>_3ddf){
this.data[j+1]=this.data[j];
j--;
if(j>=0){
_3de1=this.data[j];
_3de2=_3de1[f];
}
}
this.data[j+1]=_3de0;
}
};
ArrayList.prototype.clone=function(){
var _3de3=new ArrayList(this.size);
for(var i=0;i<this.size;i++){
_3de3.add(this.data[i]);
}
return _3de3;
};
ArrayList.prototype.overwriteElementAt=function(obj,index){
this.data[index]=obj;
};
function trace(_3dbe){
var _3dbf=openwindow("about:blank",700,400);
_3dbf.document.writeln("<pre>"+_3dbe+"</pre>");
}
function openwindow(url,width,_3dc2){
var left=(screen.width-width)/2;
var top=(screen.height-_3dc2)/2;
property="left="+left+", top="+top+", toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=1,alwaysRaised,width="+width+",height="+_3dc2;
return window.open(url,"_blank",property);
}
function dumpObject(obj){
trace("----------------------------------------------------------------------------");
trace("- Object dump");
trace("----------------------------------------------------------------------------");
for(var i in obj){
try{
if(typeof obj[i]!="function"){
trace(i+" --&gt; "+obj[i]);
}
}
catch(e){
}
}
for(var i in obj){
try{
if(typeof obj[i]=="function"){
trace(i+" --&gt; "+obj[i]);
}
}
catch(e){
}
}
trace("----------------------------------------------------------------------------");
}
Drag=function(){
};
Drag.current=null;
Drag.currentTarget=null;
Drag.dragging=false;
Drag.isDragging=function(){
return this.dragging;
};
Drag.setCurrent=function(_326a){
this.current=_326a;
this.dragging=true;
};
Drag.getCurrent=function(){
return this.current;
};
Drag.clearCurrent=function(){
this.current=null;
this.dragging=false;
};
Draggable=function(_326b,_326c){
EventTarget.call(this);
this.construct(_326b,_326c);
this.diffX=0;
this.diffY=0;
this.targets=new ArrayList();
};
Draggable.prototype=new EventTarget;
Draggable.prototype.construct=function(_326d,_326e){
this.element=_326d;
this.constraints=_326e;
var oThis=this;
var _3270=function(){
var _3271=new DragDropEvent();
_3271.initDragDropEvent("dblclick",true);
oThis.dispatchEvent(_3271);
var _3272=arguments[0]||window.event;
_3272.cancelBubble=true;
_3272.returnValue=false;
};
var _3273=function(){
var _3274=arguments[0]||window.event;
var _3275=new DragDropEvent();
var _3276=oThis.node.workflow.getAbsoluteX();
var _3277=oThis.node.workflow.getAbsoluteY();
var _3278=oThis.node.workflow.getScrollLeft();
var _3279=oThis.node.workflow.getScrollTop();
_3275.x=_3274.clientX-oThis.element.offsetLeft+_3278-_3276;
_3275.y=_3274.clientY-oThis.element.offsetTop+_3279-_3277;
if(_3274.button==2){
_3275.initDragDropEvent("contextmenu",true);
oThis.dispatchEvent(_3275);
}else{
_3275.initDragDropEvent("dragstart",true);
if(oThis.dispatchEvent(_3275)){
oThis.diffX=_3274.clientX-oThis.element.offsetLeft;
oThis.diffY=_3274.clientY-oThis.element.offsetTop;
Drag.setCurrent(oThis);
if(oThis.isAttached==true){
oThis.detachEventHandlers();
}
oThis.attachEventHandlers();
}
}
_3274.cancelBubble=true;
_3274.returnValue=false;
};
var _327a=function(){
if(Drag.getCurrent()==null){
var _327b=arguments[0]||window.event;
if(Drag.currentHover!=null&&oThis!=Drag.currentHover){
var _327c=new DragDropEvent();
_327c.initDragDropEvent("mouseleave",false,oThis);
Drag.currentHover.dispatchEvent(_327c);
}
if(oThis!=null&&oThis!=Drag.currentHover){
var _327c=new DragDropEvent();
_327c.initDragDropEvent("mouseenter",false,oThis);
oThis.dispatchEvent(_327c);
}
Drag.currentHover=oThis;
}else{
}
};
if(this.element.addEventListener){
this.element.addEventListener("mousemove",_327a,false);
this.element.addEventListener("mousedown",_3273,false);
this.element.addEventListener("dblclick",_3270,false);
}else{
if(this.element.attachEvent){
this.element.attachEvent("onmousemove",_327a);
this.element.attachEvent("onmousedown",_3273);
this.element.attachEvent("ondblclick",_3270);
}else{
throw new Error("Drag not supported in this browser.");
}
}
};
Draggable.prototype.attachEventHandlers=function(){
var oThis=this;
oThis.isAttached=true;
this.tempMouseMove=function(){
var _327e=arguments[0]||window.event;
var _327f=new Point(_327e.clientX-oThis.diffX,_327e.clientY-oThis.diffY);
if(oThis.node.getCanSnapToHelper()){
_327f=oThis.node.getWorkflow().snapToHelper(oThis.node,_327f);
}
oThis.element.style.left=_327f.x+"px";
oThis.element.style.top=_327f.y+"px";
var _3280=oThis.node.workflow.getScrollLeft();
var _3281=oThis.node.workflow.getScrollTop();
var _3282=oThis.node.workflow.getAbsoluteX();
var _3283=oThis.node.workflow.getAbsoluteY();
var _3284=oThis.getDropTarget(_327e.clientX+_3280-_3282,_327e.clientY+_3281-_3283);
var _3285=oThis.getCompartment(_327e.clientX+_3280-_3282,_327e.clientY+_3281-_3283);
if(Drag.currentTarget!=null&&_3284!=Drag.currentTarget){
var _3286=new DragDropEvent();
_3286.initDragDropEvent("dragleave",false,oThis);
Drag.currentTarget.dispatchEvent(_3286);
}
if(_3284!=null&&_3284!=Drag.currentTarget){
var _3286=new DragDropEvent();
_3286.initDragDropEvent("dragenter",false,oThis);
_3284.dispatchEvent(_3286);
}
Drag.currentTarget=_3284;
if(Drag.currentCompartment!=null&&_3285!=Drag.currentCompartment){
var _3286=new DragDropEvent();
_3286.initDragDropEvent("figureleave",false,oThis);
Drag.currentCompartment.dispatchEvent(_3286);
}
if(_3285!=null&&_3285.node!=oThis.node&&_3285!=Drag.currentCompartment){
var _3286=new DragDropEvent();
_3286.initDragDropEvent("figureenter",false,oThis);
_3285.dispatchEvent(_3286);
}
Drag.currentCompartment=_3285;
var _3287=new DragDropEvent();
_3287.initDragDropEvent("drag",false);
oThis.dispatchEvent(_3287);
};
oThis.tempMouseUp=function(){
oThis.detachEventHandlers();
var _3288=arguments[0]||window.event;
var _3289=new DragDropEvent();
_3289.initDragDropEvent("dragend",false);
oThis.dispatchEvent(_3289);
var _328a=oThis.node.workflow.getScrollLeft();
var _328b=oThis.node.workflow.getScrollTop();
var _328c=oThis.node.workflow.getAbsoluteX();
var _328d=oThis.node.workflow.getAbsoluteY();
var _328e=oThis.getDropTarget(_3288.clientX+_328a-_328c,_3288.clientY+_328b-_328d);
var _328f=oThis.getCompartment(_3288.clientX+_328a-_328c,_3288.clientY+_328b-_328d);
if(_328e!=null){
var _3290=new DragDropEvent();
_3290.initDragDropEvent("drop",false,oThis);
_328e.dispatchEvent(_3290);
}
if(_328f!=null&&_328f.node!=oThis.node){
var _3290=new DragDropEvent();
_3290.initDragDropEvent("figuredrop",false,oThis);
_328f.dispatchEvent(_3290);
}
if(Drag.currentTarget!=null){
var _3290=new DragDropEvent();
_3290.initDragDropEvent("dragleave",false,oThis);
Drag.currentTarget.dispatchEvent(_3290);
Drag.currentTarget=null;
}
Drag.currentCompartment=null;
Drag.clearCurrent();
};
if(document.body.addEventListener){
document.body.addEventListener("mousemove",this.tempMouseMove,false);
document.body.addEventListener("mouseup",this.tempMouseUp,false);
}else{
if(document.body.attachEvent){
document.body.attachEvent("onmousemove",this.tempMouseMove);
document.body.attachEvent("onmouseup",this.tempMouseUp);
}else{
throw new Error("Drag doesn't support this browser.");
}
}
};
Draggable.prototype.detachEventHandlers=function(){
this.isAttached=false;
if(document.body.removeEventListener){
document.body.removeEventListener("mousemove",this.tempMouseMove,false);
document.body.removeEventListener("mouseup",this.tempMouseUp,false);
}else{
if(document.body.detachEvent){
document.body.detachEvent("onmousemove",this.tempMouseMove);
document.body.detachEvent("onmouseup",this.tempMouseUp);
}else{
throw new Error("Drag doesn't support this browser.");
}
}
};
Draggable.prototype.getDropTarget=function(x,y){
for(var i=0;i<this.targets.getSize();i++){
var _3294=this.targets.get(i);
if(_3294.node.isOver(x,y)&&_3294.node!=this.node){
return _3294;
}
}
return null;
};
Draggable.prototype.getCompartment=function(x,y){
var _3297=null;
for(var i=0;i<this.node.workflow.compartments.getSize();i++){
var _3299=this.node.workflow.compartments.get(i);
if(_3299.isOver(x,y)&&_3299!=this.node){
if(_3297==null){
_3297=_3299;
}else{
if(_3297.getZOrder()<_3299.getZOrder()){
_3297=_3299;
}
}
}
}
return _3297==null?null:_3297.dropable;
};
Draggable.prototype.getLeft=function(){
return this.element.offsetLeft;
};
Draggable.prototype.getTop=function(){
return this.element.offsetTop;
};
DragDropEvent=function(){
Event.call(this);
};
DragDropEvent.prototype=new Event();
DragDropEvent.prototype.initDragDropEvent=function(sType,_329b,_329c){
this.initEvent(sType,_329b);
this.relatedTarget=_329c;
};
DropTarget=function(_329d){
EventTarget.call(this);
this.construct(_329d);
};
DropTarget.prototype=new EventTarget;
DropTarget.prototype.construct=function(_329e){
this.element=_329e;
};
DropTarget.prototype.getLeft=function(){
var el=this.element;
var ol=el.offsetLeft;
while((el=el.offsetParent)!=null){
ol+=el.offsetLeft;
}
return ol;
};
DropTarget.prototype.getTop=function(){
var el=this.element;
var ot=el.offsetTop;
while((el=el.offsetParent)!=null){
ot+=el.offsetTop;
}
return ot;
};
DropTarget.prototype.getHeight=function(){
return this.element.offsetHeight;
};
DropTarget.prototype.getWidth=function(){
return this.element.offsetWidth;
};
PositionConstants=function(){
};
PositionConstants.NORTH=1;
PositionConstants.SOUTH=4;
PositionConstants.WEST=8;
PositionConstants.EAST=16;
Color=function(red,green,blue){
if(typeof green=="undefined"){
var rgb=this.hex2rgb(red);
this.red=rgb[0];
this.green=rgb[1];
this.blue=rgb[2];
}else{
this.red=red;
this.green=green;
this.blue=blue;
}
};
Color.prototype.type="Color";
Color.prototype.getHTMLStyle=function(){
return "rgb("+this.red+","+this.green+","+this.blue+")";
};
Color.prototype.getRed=function(){
return this.red;
};
Color.prototype.getGreen=function(){
return this.green;
};
Color.prototype.getBlue=function(){
return this.blue;
};
Color.prototype.getIdealTextColor=function(){
var _3f07=105;
var _3f08=(this.red*0.299)+(this.green*0.587)+(this.blue*0.114);
return (255-_3f08<_3f07)?new Color(0,0,0):new Color(255,255,255);
};
Color.prototype.hex2rgb=function(_3f09){
_3f09=_3f09.replace("#","");
return ({0:parseInt(_3f09.substr(0,2),16),1:parseInt(_3f09.substr(2,2),16),2:parseInt(_3f09.substr(4,2),16)});
};
Color.prototype.hex=function(){
return (this.int2hex(this.red)+this.int2hex(this.green)+this.int2hex(this.blue));
};
Color.prototype.int2hex=function(v){
v=Math.round(Math.min(Math.max(0,v),255));
return ("0123456789ABCDEF".charAt((v-v%16)/16)+"0123456789ABCDEF".charAt(v%16));
};
Color.prototype.darker=function(_3f0b){
var red=parseInt(Math.round(this.getRed()*(1-_3f0b)));
var green=parseInt(Math.round(this.getGreen()*(1-_3f0b)));
var blue=parseInt(Math.round(this.getBlue()*(1-_3f0b)));
if(red<0){
red=0;
}else{
if(red>255){
red=255;
}
}
if(green<0){
green=0;
}else{
if(green>255){
green=255;
}
}
if(blue<0){
blue=0;
}else{
if(blue>255){
blue=255;
}
}
return new Color(red,green,blue);
};
Color.prototype.lighter=function(_3f0f){
var red=parseInt(Math.round(this.getRed()*(1+_3f0f)));
var green=parseInt(Math.round(this.getGreen()*(1+_3f0f)));
var blue=parseInt(Math.round(this.getBlue()*(1+_3f0f)));
if(red<0){
red=0;
}else{
if(red>255){
red=255;
}
}
if(green<0){
green=0;
}else{
if(green>255){
green=255;
}
}
if(blue<0){
blue=0;
}else{
if(blue>255){
blue=255;
}
}
return new Color(red,green,blue);
};
Point=function(x,y){
this.x=x;
this.y=y;
};
Point.prototype.type="Point";
Point.prototype.getX=function(){
return this.x;
};
Point.prototype.getY=function(){
return this.y;
};
Point.prototype.getPosition=function(p){
var dx=p.x-this.x;
var dy=p.y-this.y;
if(Math.abs(dx)>Math.abs(dy)){
if(dx<0){
return PositionConstants.WEST;
}
return PositionConstants.EAST;
}
if(dy<0){
return PositionConstants.NORTH;
}
return PositionConstants.SOUTH;
};
Point.prototype.equals=function(o){
return this.x==o.x&&this.y==o.y;
};
Point.prototype.getDistance=function(other){
return Math.sqrt((this.x-other.x)*(this.x-other.x)+(this.y-other.y)*(this.y-other.y));
};
Point.prototype.getTranslated=function(other){
return new Point(this.x+other.x,this.y+other.y);
};
Dimension=function(x,y,w,h){
Point.call(this,x,y);
this.w=w;
this.h=h;
};
Dimension.prototype=new Point;
Dimension.prototype.type="Dimension";
Dimension.prototype.translate=function(dx,dy){
this.x+=dx;
this.y+=dy;
return this;
};
Dimension.prototype.resize=function(dw,dh){
this.w+=dw;
this.h+=dh;
return this;
};
Dimension.prototype.setBounds=function(rect){
this.x=rect.x;
this.y=rect.y;
this.w=rect.w;
this.h=rect.h;
return this;
};
Dimension.prototype.isEmpty=function(){
return this.w<=0||this.h<=0;
};
Dimension.prototype.getWidth=function(){
return this.w;
};
Dimension.prototype.getHeight=function(){
return this.h;
};
Dimension.prototype.getRight=function(){
return this.x+this.w;
};
Dimension.prototype.getBottom=function(){
return this.y+this.h;
};
Dimension.prototype.getTopLeft=function(){
return new Point(this.x,this.y);
};
Dimension.prototype.getCenter=function(){
return new Point(this.x+this.w/2,this.y+this.h/2);
};
Dimension.prototype.getBottomRight=function(){
return new Point(this.x+this.w,this.y+this.h);
};
Dimension.prototype.equals=function(o){
return this.x==o.x&&this.y==o.y&&this.w==o.w&&this.h==o.h;
};
SnapToHelper=function(_3f1b){
this.workflow=_3f1b;
};
SnapToHelper.NORTH=1;
SnapToHelper.SOUTH=4;
SnapToHelper.WEST=8;
SnapToHelper.EAST=16;
SnapToHelper.NORTH_EAST=SnapToHelper.NORTH|SnapToHelper.EAST;
SnapToHelper.NORTH_WEST=SnapToHelper.NORTH|SnapToHelper.WEST;
SnapToHelper.SOUTH_EAST=SnapToHelper.SOUTH|SnapToHelper.EAST;
SnapToHelper.SOUTH_WEST=SnapToHelper.SOUTH|SnapToHelper.WEST;
SnapToHelper.NORTH_SOUTH=SnapToHelper.NORTH|SnapToHelper.SOUTH;
SnapToHelper.EAST_WEST=SnapToHelper.EAST|SnapToHelper.WEST;
SnapToHelper.NSEW=SnapToHelper.NORTH_SOUTH|SnapToHelper.EAST_WEST;
SnapToHelper.prototype.snapPoint=function(_3f1c,_3f1d,_3f1e){
return _3f1d;
};
SnapToHelper.prototype.snapRectangle=function(_3f1f,_3f20){
return _3f1f;
};
SnapToHelper.prototype.onSetDocumentDirty=function(){
};
SnapToGrid=function(_39d9){
SnapToHelper.call(this,_39d9);
};
SnapToGrid.prototype=new SnapToHelper;
SnapToGrid.prototype.snapPoint=function(_39da,_39db,_39dc){
_39dc.x=this.workflow.gridWidthX*Math.floor(((_39db.x+this.workflow.gridWidthX/2)/this.workflow.gridWidthX));
_39dc.y=this.workflow.gridWidthY*Math.floor(((_39db.y+this.workflow.gridWidthY/2)/this.workflow.gridWidthY));
return 0;
};
SnapToGrid.prototype.snapRectangle=function(_39dd,_39de){
_39de.x=_39dd.x;
_39de.y=_39dd.y;
_39de.w=_39dd.w;
_39de.h=_39dd.h;
return 0;
};
SnapToGeometryEntry=function(type,_39cd){
this.type=type;
this.location=_39cd;
};
SnapToGeometryEntry.prototype.getLocation=function(){
return this.location;
};
SnapToGeometryEntry.prototype.getType=function(){
return this.type;
};
SnapToGeometry=function(_40db){
SnapToHelper.call(this,_40db);
};
SnapToGeometry.prototype=new SnapToHelper;
SnapToGeometry.THRESHOLD=5;
SnapToGeometry.prototype.snapPoint=function(_40dc,_40dd,_40de){
if(this.rows==null||this.cols==null){
this.populateRowsAndCols();
}
if((_40dc&SnapToHelper.EAST)!=0){
var _40df=this.getCorrectionFor(this.cols,_40dd.getX()-1,1);
if(_40df!=SnapToGeometry.THRESHOLD){
_40dc&=~SnapToHelper.EAST;
_40de.x+=_40df;
}
}
if((_40dc&SnapToHelper.WEST)!=0){
var _40e0=this.getCorrectionFor(this.cols,_40dd.getX(),-1);
if(_40e0!=SnapToGeometry.THRESHOLD){
_40dc&=~SnapToHelper.WEST;
_40de.x+=_40e0;
}
}
if((_40dc&SnapToHelper.SOUTH)!=0){
var _40e1=this.getCorrectionFor(this.rows,_40dd.getY()-1,1);
if(_40e1!=SnapToGeometry.THRESHOLD){
_40dc&=~SnapToHelper.SOUTH;
_40de.y+=_40e1;
}
}
if((_40dc&SnapToHelper.NORTH)!=0){
var _40e2=this.getCorrectionFor(this.rows,_40dd.getY(),-1);
if(_40e2!=SnapToGeometry.THRESHOLD){
_40dc&=~SnapToHelper.NORTH;
_40de.y+=_40e2;
}
}
return _40dc;
};
SnapToGeometry.prototype.snapRectangle=function(_40e3,_40e4){
var _40e5=_40e3.getTopLeft();
var _40e6=_40e3.getBottomRight();
var _40e7=this.snapPoint(SnapToHelper.NORTH_WEST,_40e3.getTopLeft(),_40e5);
_40e4.x=_40e5.x;
_40e4.y=_40e5.y;
var _40e8=this.snapPoint(SnapToHelper.SOUTH_EAST,_40e3.getBottomRight(),_40e6);
if(_40e7&SnapToHelper.WEST){
_40e4.x=_40e6.x-_40e3.getWidth();
}
if(_40e7&SnapToHelper.NORTH){
_40e4.y=_40e6.y-_40e3.getHeight();
}
return _40e7|_40e8;
};
SnapToGeometry.prototype.populateRowsAndCols=function(){
this.rows=new Array();
this.cols=new Array();
var _40e9=this.workflow.getDocument().getFigures();
var index=0;
for(var i=0;i<_40e9.getSize();i++){
var _40ec=_40e9.get(i);
if(_40ec!=this.workflow.getCurrentSelection()){
var _40ed=_40ec.getBounds();
this.cols[index*3]=new SnapToGeometryEntry(-1,_40ed.getX());
this.rows[index*3]=new SnapToGeometryEntry(-1,_40ed.getY());
this.cols[index*3+1]=new SnapToGeometryEntry(0,_40ed.x+(_40ed.getWidth()-1)/2);
this.rows[index*3+1]=new SnapToGeometryEntry(0,_40ed.y+(_40ed.getHeight()-1)/2);
this.cols[index*3+2]=new SnapToGeometryEntry(1,_40ed.getRight()-1);
this.rows[index*3+2]=new SnapToGeometryEntry(1,_40ed.getBottom()-1);
index++;
}
}
};
SnapToGeometry.prototype.getCorrectionFor=function(_40ee,value,side){
var _40f1=SnapToGeometry.THRESHOLD;
var _40f2=SnapToGeometry.THRESHOLD;
for(var i=0;i<_40ee.length;i++){
var entry=_40ee[i];
var _40f5;
if(entry.type==-1&&side!=0){
_40f5=Math.abs(value-entry.location);
if(_40f5<_40f1){
_40f1=_40f5;
_40f2=entry.location-value;
}
}else{
if(entry.type==0&&side==0){
_40f5=Math.abs(value-entry.location);
if(_40f5<_40f1){
_40f1=_40f5;
_40f2=entry.location-value;
}
}else{
if(entry.type==1&&side!=0){
_40f5=Math.abs(value-entry.location);
if(_40f5<_40f1){
_40f1=_40f5;
_40f2=entry.location-value;
}
}
}
}
}
return _40f2;
};
SnapToGeometry.prototype.onSetDocumentDirty=function(){
this.rows=null;
this.cols=null;
};
Border=function(){
this.color=null;
};
Border.prototype.type="Border";
Border.prototype.dispose=function(){
this.color=null;
};
Border.prototype.getHTMLStyle=function(){
return "";
};
Border.prototype.setColor=function(c){
this.color=c;
};
Border.prototype.getColor=function(){
return this.color;
};
Border.prototype.refresh=function(){
};
LineBorder=function(width){
Border.call(this);
this.width=1;
if(width){
this.width=width;
}
this.figure=null;
};
LineBorder.prototype=new Border;
LineBorder.prototype.type="LineBorder";
LineBorder.prototype.dispose=function(){
Border.prototype.dispose.call(this);
this.figure=null;
};
LineBorder.prototype.setLineWidth=function(w){
this.width=w;
if(this.figure!=null){
this.figure.html.style.border=this.getHTMLStyle();
}
};
LineBorder.prototype.getHTMLStyle=function(){
if(this.getColor()!=null){
return this.width+"px solid "+this.getColor().getHTMLStyle();
}
return this.width+"px solid black";
};
LineBorder.prototype.refresh=function(){
this.setLineWidth(this.width);
};
Figure=function(){
this.construct();
};
Figure.prototype.type="Figure";
Figure.ZOrderBaseIndex=100;
Figure.setZOrderBaseIndex=function(index){
Figure.ZOrderBaseIndex=index;
};
Figure.prototype.construct=function(){
this.lastDragStartTime=0;
this.x=0;
this.y=0;
this.border=null;
this.setDimension(10,10);
this.id=this.generateUId();
this.html=this.createHTMLElement();
this.canvas=null;
this.workflow=null;
this.draggable=null;
this.parent=null;
this.isMoving=false;
this.canSnapToHelper=true;
this.snapToGridAnchor=new Point(0,0);
this.timer=-1;
this.setDeleteable(true);
this.setCanDrag(true);
this.setResizeable(true);
this.setSelectable(true);
this.properties=new Object();
this.moveListener=new ArrayList();
};
Figure.prototype.dispose=function(){
this.canvas=null;
this.workflow=null;
this.moveListener=null;
if(this.draggable!=null){
this.draggable.removeEventListener("mouseenter",this.tmpMouseEnter);
this.draggable.removeEventListener("mouseleave",this.tmpMouseLeave);
this.draggable.removeEventListener("dragend",this.tmpDragend);
this.draggable.removeEventListener("dragstart",this.tmpDragstart);
this.draggable.removeEventListener("drag",this.tmpDrag);
this.draggable.removeEventListener("dblclick",this.tmpDoubleClick);
this.draggable.node=null;
}
this.draggable=null;
if(this.border!=null){
this.border.dispose();
}
this.border=null;
if(this.parent!=null){
this.parent.removeChild(this);
}
};
Figure.prototype.getProperties=function(){
return this.properties;
};
Figure.prototype.getProperty=function(key){
return this.properties[key];
};
Figure.prototype.setProperty=function(key,value){
this.properties[key]=value;
this.setDocumentDirty();
};
Figure.prototype.getId=function(){
return this.id;
};
Figure.prototype.setCanvas=function(_3deb){
this.canvas=_3deb;
};
Figure.prototype.getWorkflow=function(){
return this.workflow;
};
Figure.prototype.setWorkflow=function(_3dec){
if(this.draggable==null){
this.html.tabIndex="0";
var oThis=this;
this.keyDown=function(event){
event.cancelBubble=true;
event.returnValue=true;
oThis.onKeyDown(event.keyCode,event.ctrlKey);
};
if(this.html.addEventListener){
this.html.addEventListener("keydown",this.keyDown,false);
}else{
if(this.html.attachEvent){
this.html.attachEvent("onkeydown",this.keyDown);
}
}
this.draggable=new Draggable(this.html,Draggable.DRAG_X|Draggable.DRAG_Y);
this.draggable.node=this;
this.tmpContextMenu=function(_3def){
oThis.onContextMenu(oThis.x+_3def.x,_3def.y+oThis.y);
};
this.tmpMouseEnter=function(_3df0){
oThis.onMouseEnter();
};
this.tmpMouseLeave=function(_3df1){
oThis.onMouseLeave();
};
this.tmpDragend=function(_3df2){
oThis.onDragend();
};
this.tmpDragstart=function(_3df3){
var w=oThis.workflow;
w.showMenu(null);
if(oThis.workflow.toolPalette&&oThis.workflow.toolPalette.activeTool){
_3df3.returnValue=false;
oThis.workflow.onMouseDown(oThis.x+_3df3.x,_3df3.y+oThis.y);
oThis.workflow.onMouseUp(oThis.x+_3df3.x,_3df3.y+oThis.y);
return;
}
_3df3.returnValue=oThis.onDragstart(_3df3.x,_3df3.y);
};
this.tmpDrag=function(_3df5){
oThis.onDrag();
};
this.tmpDoubleClick=function(_3df6){
oThis.onDoubleClick();
};
this.draggable.addEventListener("contextmenu",this.tmpContextMenu);
this.draggable.addEventListener("mouseenter",this.tmpMouseEnter);
this.draggable.addEventListener("mouseleave",this.tmpMouseLeave);
this.draggable.addEventListener("dragend",this.tmpDragend);
this.draggable.addEventListener("dragstart",this.tmpDragstart);
this.draggable.addEventListener("drag",this.tmpDrag);
this.draggable.addEventListener("dblclick",this.tmpDoubleClick);
}
this.workflow=_3dec;
};
Figure.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.height=this.width+"px";
item.style.width=this.height+"px";
item.style.margin="0px";
item.style.padding="0px";
item.style.outline="none";
item.style.zIndex=""+Figure.ZOrderBaseIndex;
return item;
};
Figure.prototype.setParent=function(_3df8){
this.parent=_3df8;
};
Figure.prototype.getParent=function(){
return this.parent;
};
Figure.prototype.getZOrder=function(){
return this.html.style.zIndex;
};
Figure.prototype.setZOrder=function(index){
this.html.style.zIndex=index;
};
Figure.prototype.hasFixedPosition=function(){
return false;
};
Figure.prototype.getMinWidth=function(){
return 5;
};
Figure.prototype.getMinHeight=function(){
return 5;
};
Figure.prototype.getHTMLElement=function(){
if(this.html==null){
this.html=this.createHTMLElement();
}
return this.html;
};
Figure.prototype.paint=function(){
};
Figure.prototype.setBorder=function(_3dfa){
if(this.border!=null){
this.border.figure=null;
}
this.border=_3dfa;
this.border.figure=this;
this.border.refresh();
this.setDocumentDirty();
};
Figure.prototype.onContextMenu=function(x,y){
var menu=this.getContextMenu();
if(menu!=null){
this.workflow.showMenu(menu,x,y);
}
};
Figure.prototype.getContextMenu=function(){
return null;
};
Figure.prototype.onDoubleClick=function(){
};
Figure.prototype.onMouseEnter=function(){
};
Figure.prototype.onMouseLeave=function(){
};
Figure.prototype.onDrag=function(){
this.x=this.draggable.getLeft();
this.y=this.draggable.getTop();
if(this.isMoving==false){
this.isMoving=true;
this.setAlpha(0.5);
}
this.fireMoveEvent();
};
Figure.prototype.onDragend=function(){
if(this.getWorkflow().getEnableSmoothFigureHandling()==true){
var _3dfe=this;
var _3dff=function(){
if(_3dfe.alpha<1){
_3dfe.setAlpha(Math.min(1,_3dfe.alpha+0.05));
}else{
window.clearInterval(_3dfe.timer);
_3dfe.timer=-1;
}
};
if(_3dfe.timer>0){
window.clearInterval(_3dfe.timer);
}
_3dfe.timer=window.setInterval(_3dff,20);
}else{
this.setAlpha(1);
}
this.command.setPosition(this.x,this.y);
this.workflow.commandStack.execute(this.command);
this.command=null;
this.isMoving=false;
this.workflow.hideSnapToHelperLines();
this.fireMoveEvent();
};
Figure.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
this.command=new CommandMove(this,this.x,this.y);
return true;
};
Figure.prototype.setCanDrag=function(flag){
this.canDrag=flag;
if(flag){
this.html.style.cursor="move";
}else{
this.html.style.cursor=null;
}
};
Figure.prototype.setAlpha=function(_3e03){
if(this.alpha==_3e03){
return;
}
try{
this.html.style.MozOpacity=_3e03;
}
catch(exc){
}
try{
this.html.style.opacity=_3e03;
}
catch(exc){
}
try{
var _3e04=Math.round(_3e03*100);
if(_3e04>=99){
this.html.style.filter="";
}else{
this.html.style.filter="alpha(opacity="+_3e04+")";
}
}
catch(exc){
}
this.alpha=_3e03;
};
Figure.prototype.setDimension=function(w,h){
this.width=Math.max(this.getMinWidth(),w);
this.height=Math.max(this.getMinHeight(),h);
if(this.html==null){
return;
}
this.html.style.width=this.width+"px";
this.html.style.height=this.height+"px";
this.fireMoveEvent();
if(this.workflow!=null&&this.workflow.getCurrentSelection()==this){
this.workflow.showResizeHandles(this);
}
};
Figure.prototype.setPosition=function(xPos,yPos){
this.x=xPos;
this.y=yPos;
if(this.html==null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
this.fireMoveEvent();
if(this.workflow!=null&&this.workflow.getCurrentSelection()==this){
this.workflow.showResizeHandles(this);
}
};
Figure.prototype.isResizeable=function(){
return this.resizeable;
};
Figure.prototype.setResizeable=function(flag){
this.resizeable=flag;
};
Figure.prototype.isSelectable=function(){
return this.selectable;
};
Figure.prototype.setSelectable=function(flag){
this.selectable=flag;
};
Figure.prototype.isStrechable=function(){
return true;
};
Figure.prototype.isDeleteable=function(){
return this.deleteable;
};
Figure.prototype.setDeleteable=function(flag){
this.deleteable=flag;
};
Figure.prototype.setCanSnapToHelper=function(flag){
this.canSnapToHelper=flag;
};
Figure.prototype.getCanSnapToHelper=function(){
return this.canSnapToHelper;
};
Figure.prototype.getSnapToGridAnchor=function(){
return this.snapToGridAnchor;
};
Figure.prototype.setSnapToGridAnchor=function(point){
this.snapToGridAnchor=point;
};
Figure.prototype.getBounds=function(){
return new Dimension(this.getX(),this.getY(),this.getWidth(),this.getHeight());
};
Figure.prototype.getWidth=function(){
return this.width;
};
Figure.prototype.getHeight=function(){
return this.height;
};
Figure.prototype.getY=function(){
return this.y;
};
Figure.prototype.getX=function(){
return this.x;
};
Figure.prototype.getAbsoluteY=function(){
return this.y;
};
Figure.prototype.getAbsoluteX=function(){
return this.x;
};
Figure.prototype.onKeyDown=function(_3e0e,ctrl){
if(_3e0e==46&&this.isDeleteable()==true){
this.workflow.commandStack.execute(new CommandDelete(this));
}
if(ctrl){
this.workflow.onKeyDown(_3e0e,ctrl);
}
};
Figure.prototype.getPosition=function(){
return new Point(this.x,this.y);
};
Figure.prototype.isOver=function(iX,iY){
var x=this.getAbsoluteX();
var y=this.getAbsoluteY();
var iX2=x+this.width;
var iY2=y+this.height;
return (iX>=x&&iX<=iX2&&iY>=y&&iY<=iY2);
};
Figure.prototype.attachMoveListener=function(_3e16){
if(_3e16==null||this.moveListener==null){
return;
}
this.moveListener.add(_3e16);
};
Figure.prototype.detachMoveListener=function(_3e17){
if(_3e17==null||this.moveListener==null){
return;
}
this.moveListener.remove(_3e17);
};
Figure.prototype.fireMoveEvent=function(){
this.setDocumentDirty();
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
this.moveListener.get(i).onOtherFigureMoved(this);
}
};
Figure.prototype.onOtherFigureMoved=function(_3e1a){
};
Figure.prototype.setDocumentDirty=function(){
if(this.workflow!=null){
this.workflow.setDocumentDirty();
}
};
Figure.prototype.generateUId=function(){
var chars="0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
var _3e1c=10;
var _3e1d=10;
nbTry=0;
while(nbTry<1000){
var id="";
for(var i=0;i<_3e1c;i++){
var rnum=Math.floor(Math.random()*chars.length);
id+=chars.substring(rnum,rnum+1);
}
elem=document.getElementById(id);
if(!elem){
return id;
}
nbTry+=1;
}
return null;
};
Figure.prototype.disableTextSelection=function(e){
if(typeof e.onselectstart!="undefined"){
e.onselectstart=function(){
return false;
};
}else{
if(typeof e.style.MozUserSelect!="undefined"){
e.style.MozUserSelect="none";
}
}
};
Node=function(){
this.bgColor=null;
this.lineColor=new Color(128,128,255);
this.lineStroke=1;
this.ports=new ArrayList();
Figure.call(this);
};
Node.prototype=new Figure;
Node.prototype.type="Node";
Node.prototype.dispose=function(){
for(var i=0;i<this.ports.getSize();i++){
this.ports.get(i).dispose();
}
this.ports=null;
Figure.prototype.dispose.call(this);
};
Node.prototype.createHTMLElement=function(){
var item=Figure.prototype.createHTMLElement.call(this);
item.style.width="auto";
item.style.height="auto";
item.style.margin="0px";
item.style.padding="0px";
if(this.lineColor!=null){
item.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}
item.style.fontSize="1px";
if(this.bgColor!=null){
item.style.backgroundColor=this.bgColor.getHTMLStyle();
}
return item;
};
Node.prototype.paint=function(){
Figure.prototype.paint.call(this);
for(var i=0;i<this.ports.getSize();i++){
this.ports.get(i).paint();
}
};
Node.prototype.getPorts=function(){
return this.ports;
};
Node.prototype.getPort=function(_32a7){
if(this.ports==null){
return null;
}
for(var i=0;i<this.ports.getSize();i++){
var port=this.ports.get(i);
if(port.getName()==_32a7){
return port;
}
}
};
Node.prototype.addPort=function(port,x,y){
this.ports.add(port);
port.setOrigin(x,y);
port.setPosition(x,y);
port.setParent(this);
port.setDeleteable(false);
this.html.appendChild(port.getHTMLElement());
if(this.workflow!=null){
this.workflow.registerPort(port);
}
};
Node.prototype.removePort=function(port){
if(this.ports!=null){
this.ports.removeElementAt(this.ports.indexOf(port));
}
try{
this.html.removeChild(port.getHTMLElement());
}
catch(exc){
}
if(this.workflow!=null){
this.workflow.unregisterPort(port);
}
};
Node.prototype.setWorkflow=function(_32ae){
var _32af=this.workflow;
Figure.prototype.setWorkflow.call(this,_32ae);
if(_32af!=null){
for(var i=0;i<this.ports.getSize();i++){
_32af.unregisterPort(this.ports.get(i));
}
}
if(this.workflow!=null){
for(var i=0;i<this.ports.getSize();i++){
this.workflow.registerPort(this.ports.get(i));
}
}
};
Node.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!=null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
Node.prototype.getBackgroundColor=function(){
return this.bgColor;
};
Node.prototype.setColor=function(color){
this.lineColor=color;
if(this.lineColor!=null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border="0px";
}
};
Node.prototype.setLineWidth=function(w){
this.lineStroke=w;
if(this.lineColor!=null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border="0px";
}
};
VectorFigure=function(){
this.bgColor=null;
this.lineColor=new Color(0,0,0);
this.stroke=1;
this.graphics=null;
Node.call(this);
};
VectorFigure.prototype=new Node;
VectorFigure.prototype.type="VectorFigure";
VectorFigure.prototype.dispose=function(){
Node.prototype.dispose.call(this);
this.bgColor=null;
this.lineColor=null;
if(this.graphics!=null){
this.graphics.clear();
}
this.graphics=null;
};
VectorFigure.prototype.createHTMLElement=function(){
var item=Node.prototype.createHTMLElement.call(this);
item.style.border="0px";
item.style.backgroundColor="transparent";
return item;
};
VectorFigure.prototype.setWorkflow=function(_3e71){
Node.prototype.setWorkflow.call(this,_3e71);
if(this.workflow==null){
this.graphics.clear();
this.graphics=null;
}
};
VectorFigure.prototype.paint=function(){
if(this.graphics==null){
this.graphics=new jsGraphics(this.id);
}else{
this.graphics.clear();
}
Node.prototype.paint.call(this);
for(var i=0;i<this.ports.getSize();i++){
this.getHTMLElement().appendChild(this.ports.get(i).getHTMLElement());
}
};
VectorFigure.prototype.setDimension=function(w,h){
Node.prototype.setDimension.call(this,w,h);
if(this.graphics!=null){
this.paint();
}
};
VectorFigure.prototype.setTaskCount=function(_40c0){

}
VectorFigure.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.graphics!=null){
this.paint();
}
};
VectorFigure.prototype.getBackgroundColor=function(){
return this.bgColor;
};
VectorFigure.prototype.setLineWidth=function(w){
this.stroke=w;
if(this.graphics!=null){
this.paint();
}
};
VectorFigure.prototype.setColor=function(color){
this.lineColor=color;
if(this.graphics!=null){
this.paint();
}
};
VectorFigure.prototype.getColor=function(){
return this.lineColor;
};
SVGFigure=function(width,_30b2){
this.bgColor=null;
this.lineColor=new Color(0,0,0);
this.stroke=1;
this.context=null;
Node.call(this);
if(width&&_30b2){
this.setDimension(width,_30b2);
}
};
SVGFigure.prototype=new Node;
SVGFigure.prototype.type="SVGFigure";
SVGFigure.prototype.createHTMLElement=function(){
var item=new MooCanvas(this.id,{width:this.getWidth(),height:this.getHeight()});
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.zIndex=""+Figure.ZOrderBaseIndex;
this.context=item.getContext("2d");
return item;
};
SVGFigure.prototype.paint=function(){
this.context.clearRect(0,0,this.getWidth(),this.getHeight());
this.context.fillStyle="rgba(200,0,0,0.3)";
this.context.fillRect(0,0,this.getWidth(),this.getHeight());
};
SVGFigure.prototype.setDimension=function(w,h){
Node.prototype.setDimension.call(this,w,h);
this.html.width=w;
this.html.height=h;
if(this.context!=null){
this.paint();
}
};
SVGFigure.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.graphics!=null){
this.paint();
}
};
SVGFigure.prototype.getBackgroundColor=function(){
return this.bgColor;
};
SVGFigure.prototype.setLineWidth=function(w){
this.stroke=w;
if(this.context!=null){
this.paint();
}
};
SVGFigure.prototype.setColor=function(color){
this.lineColor=color;
if(this.context!=null){
this.paint();
}
};
SVGFigure.prototype.getColor=function(){
return this.lineColor;
};
Label=function(msg){
this.msg=msg;
this.bgColor=null;
this.color=new Color(0,0,0);
this.fontSize=10;
this.textNode=null;
this.align="center";
Figure.call(this);
};
Label.prototype=new Figure;
Label.prototype.type="Label";
Label.prototype.createHTMLElement=function(){
var item=Figure.prototype.createHTMLElement.call(this);
this.textNode=document.createTextNode(this.msg);
item.appendChild(this.textNode);
item.style.color=this.color.getHTMLStyle();
item.style.fontSize=this.fontSize+"pt";
item.style.width="auto";
item.style.height="auto";
item.style.paddingLeft="3px";
item.style.paddingRight="3px";
item.style.textAlign=this.align;
if(this.bgColor!=null){
item.style.backgroundColor=this.bgColor.getHTMLStyle();
}
return item;
};
Label.prototype.isResizeable=function(){
return false;
};
Label.prototype.setWordwrap=function(flag){
this.html.style.whiteSpace=flag?"wrap":"nowrap";
};
Label.prototype.setAlign=function(align){
this.align=align;
this.html.style.textAlign=align;
};
Label.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!=null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
Label.prototype.setColor=function(color){
this.color=color;
this.html.style.color=this.color.getHTMLStyle();
};
Label.prototype.setFontSize=function(size){
this.fontSize=size;
this.html.style.fontSize=this.fontSize+"pt";
};
Label.prototype.getWidth=function(){
if(window.getComputedStyle){
return parseInt(getComputedStyle(this.html,"").getPropertyValue("width"));
}
return parseInt(this.html.clientWidth);
};
Label.prototype.getHeight=function(){
if(window.getComputedStyle){
return parseInt(getComputedStyle(this.html,"").getPropertyValue("height"));
}
return parseInt(this.html.clientHeight);
};
Label.prototype.getText=function(){
this.msg=text;
};
Label.prototype.setText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createTextNode(this.msg);
this.html.appendChild(this.textNode);
};
Label.prototype.setStyledText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createElement("div");
this.textNode.style.whiteSpace="nowrap";
this.textNode.innerHTML=text;
this.html.appendChild(this.textNode);
};
Oval=function(){
VectorFigure.call(this);
};
Oval.prototype=new VectorFigure;
Oval.prototype.type="Oval";
Oval.prototype.paint=function(){
VectorFigure.prototype.paint.call(this);
this.graphics.setStroke(this.stroke);
var zoomfactor=1;
if(typeof workflow.zoomfactor != 'undefined' && workflow.zoomfactor != '')
zoomfactor = workflow.zoomfactor;
if(this.bgColor!=null){
this.graphics.setColor(this.bgColor.getHTMLStyle());
this.graphics.fillOval(0,0,(this.getWidth()+2)*zoomfactor,(this.getHeight()+2)*zoomfactor);
}
if(this.lineColor!=null){
this.graphics.setColor(this.lineColor.getHTMLStyle());
this.graphics.drawOval(0,0,(this.getWidth()+2)*zoomfactor,(this.getHeight()+2)*zoomfactor);
}
this.graphics.paint();
};
Circle=function(_3f5f){
Oval.call(this);
if(_3f5f){
this.setDimension(_3f5f,_3f5f);
}
};
Circle.prototype=new Oval;
Circle.prototype.type="Circle";
Circle.prototype.setDimension=function(w,h){
if(w>h){
Oval.prototype.setDimension.call(this,w,w);
}else{
Oval.prototype.setDimension.call(this,h,h);
}
};
Circle.prototype.isStrechable=function(){
return false;
};
Rectangle=function(width,_3e35){
this.bgColor=null;
this.lineColor=new Color(0,0,0);
this.lineStroke=1;
Figure.call(this);
if(width&&_3e35){
this.setDimension(width,_3e35);
}
};
Rectangle.prototype=new Figure;
Rectangle.prototype.type="Rectangle";
Rectangle.prototype.dispose=function(){
Figure.prototype.dispose.call(this);
this.bgColor=null;
this.lineColor=null;
};
Rectangle.prototype.createHTMLElement=function(){
var item=Figure.prototype.createHTMLElement.call(this);
item.style.width="auto";
item.style.height="auto";
item.style.margin="0px";
item.style.padding="0px";
item.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
item.style.fontSize="1px";
item.style.lineHeight="1px";
item.innerHTML="&nbsp";
if(this.bgColor!=null){
item.style.backgroundColor=this.bgColor.getHTMLStyle();
}
return item;
};
Rectangle.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!=null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
Rectangle.prototype.getBackgroundColor=function(){
return this.bgColor;
};
Rectangle.prototype.setColor=function(color){
this.lineColor=color;
if(this.lineColor!=null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border=this.lineStroke+"0px";
}
};
Rectangle.prototype.getColor=function(){
return this.lineColor;
};
Rectangle.prototype.getWidth=function(){
return Figure.prototype.getWidth.call(this)+2*this.lineStroke;
};
Rectangle.prototype.getHeight=function(){
return Figure.prototype.getHeight.call(this)+2*this.lineStroke;
};
Rectangle.prototype.setDimension=function(w,h){
return Figure.prototype.setDimension.call(this,w-2*this.lineStroke,h-2*this.lineStroke);
};
Rectangle.prototype.setLineWidth=function(w){
var diff=w-this.lineStroke;
this.setDimension(this.getWidth()-2*diff,this.getHeight()-2*diff);
this.lineStroke=w;
var c="transparent";
if(this.lineColor!=null){
c=this.lineColor.getHTMLStyle();
}
this.html.style.border=this.lineStroke+"px solid "+c;
};
Rectangle.prototype.getLineWidth=function(){
return this.lineStroke;
};
ImageFigure=function(url){
this.url=url;
Node.call(this);
this.setDimension(40,40);
};
ImageFigure.prototype=new Node;
ImageFigure.prototype.type="Image";
ImageFigure.prototype.createHTMLElement=function(){
var item=Node.prototype.createHTMLElement.call(this);
item.style.width=this.width+"px";
item.style.height=this.height+"px";
item.style.margin="0px";
item.style.padding="0px";
item.style.border="0px";
if(this.url!=null){
item.style.backgroundImage="url("+this.url+")";
}else{
item.style.backgroundImage="";
}
return item;
};
ImageFigure.prototype.setColor=function(color){
};
ImageFigure.prototype.isResizeable=function(){
return false;
};
ImageFigure.prototype.setImage=function(url){
this.url=url;
if(this.url!=null){
this.html.style.backgroundImage="url("+this.url+")";
}else{
this.html.style.backgroundImage="";
}
};
Port=function(_41c2,_41c3){
Corona=function(){
};
Corona.prototype=new Circle;
Corona.prototype.setAlpha=function(_41c4){
Circle.prototype.setAlpha.call(this,Math.min(0.3,_41c4));
};
if(_41c2==null){
this.currentUIRepresentation=new Circle();
}else{
this.currentUIRepresentation=_41c2;
}
if(_41c3==null){
this.connectedUIRepresentation=new Circle();
this.connectedUIRepresentation.setColor(null);
}else{
this.connectedUIRepresentation=_41c3;
}
this.disconnectedUIRepresentation=this.currentUIRepresentation;
this.hideIfConnected=false;
this.uiRepresentationAdded=true;
this.parentNode=null;
this.originX=0;
this.originY=0;
this.coronaWidth=10;
this.corona=null;
Rectangle.call(this);
this.setDimension(8,8);
this.setBackgroundColor(new Color(100,180,100));
this.setColor(new Color(90,150,90));
Rectangle.prototype.setColor.call(this,null);
this.dropable=new DropTarget(this.html);
this.dropable.node=this;
this.dropable.addEventListener("dragenter",function(_41c5){
_41c5.target.node.onDragEnter(_41c5.relatedTarget.node);
});
this.dropable.addEventListener("dragleave",function(_41c6){
_41c6.target.node.onDragLeave(_41c6.relatedTarget.node);
});
this.dropable.addEventListener("drop",function(_41c7){
_41c7.relatedTarget.node.onDrop(_41c7.target.node);
});
};
Port.prototype=new Rectangle;
Port.prototype.type="Port";
Port.ZOrderBaseIndex=5000;
Port.setZOrderBaseIndex=function(index){
Port.ZOrderBaseIndex=index;
};
Port.prototype.setHideIfConnected=function(flag){
this.hideIfConnected=flag;
};
Port.prototype.dispose=function(){
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
var _41cc=this.moveListener.get(i);
this.parentNode.workflow.removeFigure(_41cc);
_41cc.dispose();
}
Rectangle.prototype.dispose.call(this);
this.parentNode=null;
this.dropable.node=null;
this.dropable=null;
this.disconnectedUIRepresentation.dispose();
this.connectedUIRepresentation.dispose();
};
Port.prototype.createHTMLElement=function(){
var item=Rectangle.prototype.createHTMLElement.call(this);
item.style.zIndex=Port.ZOrderBaseIndex;
this.currentUIRepresentation.html.zIndex=Port.ZOrderBaseIndex;
item.appendChild(this.currentUIRepresentation.html);
this.uiRepresentationAdded=true;
return item;
};
Port.prototype.setUiRepresentation=function(_41ce){
if(_41ce==null){
_41ce=new Figure();
}
if(this.uiRepresentationAdded){
    //Commented for IE* errors while changing the shape from context menu
 //this.html.removeChild(this.currentUIRepresentation.getHTMLElement());
}
this.html.appendChild(_41ce.getHTMLElement());
_41ce.paint();
this.currentUIRepresentation=_41ce;
};
Port.prototype.onMouseEnter=function(){
this.setLineWidth(2);
};
Port.prototype.onMouseLeave=function(){
this.setLineWidth(0);
};
Port.prototype.setDimension=function(width,_41d0){
Rectangle.prototype.setDimension.call(this,width,_41d0);
this.connectedUIRepresentation.setDimension(width,_41d0);
this.disconnectedUIRepresentation.setDimension(width,_41d0);
this.setPosition(this.x,this.y);
};
Port.prototype.setBackgroundColor=function(color){
this.currentUIRepresentation.setBackgroundColor(color);
};
Port.prototype.getBackgroundColor=function(){
return this.currentUIRepresentation.getBackgroundColor();
};
Port.prototype.getConnections=function(){
var _41d2=new ArrayList();
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
var _41d5=this.moveListener.get(i);
if(_41d5 instanceof Connection){
_41d2.add(_41d5);
}
}
return _41d2;
};
Port.prototype.setColor=function(color){
this.currentUIRepresentation.setColor(color);
};
Port.prototype.getColor=function(){
return this.currentUIRepresentation.getColor();
};
Port.prototype.setLineWidth=function(width){
this.currentUIRepresentation.setLineWidth(width);
};
Port.prototype.getLineWidth=function(){
return this.currentUIRepresentation.getLineWidth();
};
Port.prototype.paint=function(){
this.currentUIRepresentation.paint();
};
Port.prototype.setPosition=function(xPos,yPos){
this.originX=xPos;
this.originY=yPos;
Rectangle.prototype.setPosition.call(this,xPos,yPos);
if(this.html==null){
return;
}
this.html.style.left=(this.x-this.getWidth()/2)+"px";
this.html.style.top=(this.y-this.getHeight()/2)+"px";
};
Port.prototype.setParent=function(_41da){
if(this.parentNode!=null){
this.parentNode.detachMoveListener(this);
}
this.parentNode=_41da;
if(this.parentNode!=null){
this.parentNode.attachMoveListener(this);
}
};
Port.prototype.attachMoveListener=function(_41db){
Rectangle.prototype.attachMoveListener.call(this,_41db);
if(this.hideIfConnected==true){
this.setUiRepresentation(this.connectedUIRepresentation);
}
};
Port.prototype.detachMoveListener=function(_41dc){
Rectangle.prototype.detachMoveListener.call(this,_41dc);
if(this.getConnections().getSize()==0){
this.setUiRepresentation(this.disconnectedUIRepresentation);
}
};
Port.prototype.getParent=function(){
return this.parentNode;
};
Port.prototype.onDrag=function(){
Rectangle.prototype.onDrag.call(this);
this.parentNode.workflow.showConnectionLine(this.parentNode.x+this.x,this.parentNode.y+this.y,this.parentNode.x+this.originX,this.parentNode.y+this.originY);
};
Port.prototype.getCoronaWidth=function(){
return this.coronaWidth;
};
Port.prototype.setCoronaWidth=function(width){
this.coronaWidth=width;
};
Port.prototype.onDragend=function(){
this.setAlpha(1);
this.setPosition(this.originX,this.originY);
this.parentNode.workflow.hideConnectionLine();
};
Port.prototype.setOrigin=function(x,y){
this.originX=x;
this.originY=y;
};
Port.prototype.onDragEnter=function(port){
this.parentNode.workflow.connectionLine.setColor(new Color(0,150,0));
this.parentNode.workflow.connectionLine.setLineWidth(3);
this.showCorona(true);
};
Port.prototype.onDragLeave=function(port){
this.parentNode.workflow.connectionLine.setColor(new Color(0,0,0));
this.parentNode.workflow.connectionLine.setLineWidth(1);
this.showCorona(false);
};
Port.prototype.onDrop=function(port){
if(this.parentNode.id==port.parentNode.id){
}else{
var _41e3=new CommandConnect(this.parentNode.workflow,port,this);
this.parentNode.workflow.getCommandStack().execute(_41e3);
}
};
Port.prototype.getAbsolutePosition=function(){
return new Point(this.getAbsoluteX(),this.getAbsoluteY());
};
Port.prototype.getAbsoluteBounds=function(){
return new Dimension(this.getAbsoluteX(),this.getAbsoluteY(),this.getWidth(),this.getHeight());
};
Port.prototype.getAbsoluteY=function(){
return this.originY+this.parentNode.getY();
};
Port.prototype.getAbsoluteX=function(){
return this.originX+this.parentNode.getX();
};
Port.prototype.onOtherFigureMoved=function(_41e4){
this.fireMoveEvent();
};
Port.prototype.getName=function(){
return this.getProperty("name");
};
Port.prototype.setName=function(name){
this.setProperty("name",name);
};
Port.prototype.isOver=function(iX,iY){
var x=this.getAbsoluteX()-this.coronaWidth-this.getWidth()/2;
var y=this.getAbsoluteY()-this.coronaWidth-this.getHeight()/2;
var iX2=x+this.width+(this.coronaWidth*2)+this.getWidth()/2;
var iY2=y+this.height+(this.coronaWidth*2)+this.getHeight()/2;
return (iX>=x&&iX<=iX2&&iY>=y&&iY<=iY2);
};
Port.prototype.showCorona=function(flag,_41ed){
if(flag==true){
this.corona=new Corona();
this.corona.setAlpha(0.3);
this.corona.setBackgroundColor(new Color(0,125,125));
this.corona.setColor(null);
this.corona.setDimension(this.getWidth()+(this.getCoronaWidth()*2),this.getWidth()+(this.getCoronaWidth()*2));
this.parentNode.getWorkflow().addFigure(this.corona,this.getAbsoluteX()-this.getCoronaWidth()-this.getWidth()/2,this.getAbsoluteY()-this.getCoronaWidth()-this.getHeight()/2);
}else{
if(flag==false&&this.corona!=null){
this.parentNode.getWorkflow().removeFigure(this.corona);
this.corona=null;
}
}
};
InputPort=function(_4067){
Port.call(this,_4067);
};
InputPort.prototype=new Port;
InputPort.prototype.type="InputPort";
InputPort.prototype.onDrop=function(port){
if(port.getMaxFanOut&&port.getMaxFanOut()<=port.getFanOut()){
return;
}
if(this.parentNode.id==port.parentNode.id){
}else{
if(port instanceof OutputPort){
var _4069=new CommandConnect(this.parentNode.workflow,port,this);
this.parentNode.workflow.getCommandStack().execute(_4069);
}
}
};
InputPort.prototype.onDragEnter=function(port){
if(port instanceof OutputPort){
Port.prototype.onDragEnter.call(this,port);
}else{
if(port instanceof LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getSource() instanceof InputPort){
Port.prototype.onDragEnter.call(this,line.getSource());
}
}else{
if(port instanceof LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getTarget() instanceof InputPort){
Port.prototype.onDragEnter.call(this,line.getTarget());
}
}
}
}
};
InputPort.prototype.onDragLeave=function(port){
if(port instanceof OutputPort){
Port.prototype.onDragLeave.call(this,port);
}else{
if(port instanceof LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getSource() instanceof InputPort){
Port.prototype.onDragLeave.call(this,line.getSource());
}
}else{
if(port instanceof LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getTarget() instanceof InputPort){
Port.prototype.onDragLeave.call(this,line.getTarget());
}
}
}
}
};
OutputPort=function(_357f){
Port.call(this,_357f);
this.maxFanOut=100;
};
OutputPort.prototype=new Port;
OutputPort.prototype.type="OutputPort";
OutputPort.prototype.onDrop=function(port){
if(this.getMaxFanOut()<=this.getFanOut()){
return;
}
if(this.parentNode.id==port.parentNode.id){
}else{
if(port instanceof InputPort){
var _3581=new CommandConnect(this.parentNode.workflow,this,port);
this.parentNode.workflow.getCommandStack().execute(_3581);
}
}
};
OutputPort.prototype.onDragEnter=function(port){
if(this.getMaxFanOut()<=this.getFanOut()){
return;
}
if(port instanceof InputPort){
Port.prototype.onDragEnter.call(this,port);
}else{
if(port instanceof LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getSource() instanceof OutputPort){
Port.prototype.onDragEnter.call(this,line.getSource());
}
}else{
if(port instanceof LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getTarget() instanceof OutputPort){
Port.prototype.onDragEnter.call(this,line.getTarget());
}
}
}
}
};
OutputPort.prototype.onDragLeave=function(port){
if(port instanceof InputPort){
Port.prototype.onDragLeave.call(this,port);
}else{
if(port instanceof LineStartResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getSource() instanceof OutputPort){
Port.prototype.onDragLeave.call(this,line.getSource());
}
}else{
if(port instanceof LineEndResizeHandle){
var line=this.workflow.currentSelection;
if(line instanceof Connection&&line.getTarget() instanceof OutputPort){
Port.prototype.onDragLeave.call(this,line.getTarget());
}
}
}
}
};
OutputPort.prototype.onDragstart=function(x,y){
if(this.maxFanOut==-1){
return true;
}
if(this.getMaxFanOut()<=this.getFanOut()){
return false;
}
return true;
};
OutputPort.prototype.setMaxFanOut=function(count){
this.maxFanOut=count;
};
OutputPort.prototype.getMaxFanOut=function(){
return this.maxFanOut;
};
OutputPort.prototype.getFanOut=function(){
if(this.getParent().workflow==null){
return 0;
}
var count=0;
var lines=this.getParent().workflow.getLines();
var size=lines.getSize();
for(var i=0;i<size;i++){
var line=lines.get(i);
if(line instanceof Connection){
if(line.getSource()==this){
count++;
}else{
if(line.getTarget()==this){
count++;
}
}
}
}
return count;
};
Line=function(){
this.lineColor=new Color(0,0,0);
this.stroke=1;
this.canvas=null;
this.workflow=null;
this.html=null;
this.graphics=null;
this.id=this.generateUId();
this.startX=30;
this.startY=30;
this.endX=100;
this.endY=100;
this.alpha=1;
this.isMoving=false;
this.zOrder=Line.ZOrderBaseIndex;
this.moveListener=new ArrayList();
this.setSelectable(true);
this.setDeleteable(true);
};
Line.ZOrderBaseIndex=200;
Line.setZOrderBaseIndex=function(index){
Line.ZOrderBaseIndex=index;
};
Line.prototype.dispose=function(){
this.canvas=null;
this.workflow=null;
if(this.graphics!=null){
this.graphics.clear();
}
this.graphics=null;
};
Line.prototype.getZOrder=function(){
return this.zOrder;
};
Line.prototype.setZOrder=function(index){
if(this.html!=null){
this.html.style.zIndex=index;
}
this.zOrder=index;
};
Line.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left="0px";
item.style.top="0px";
item.style.height="0px";
item.style.width="0px";
item.style.zIndex=this.zOrder;
return item;
};
Line.prototype.getHTMLElement=function(){
if(this.html==null){
this.html=this.createHTMLElement();
}
return this.html;
};
Line.prototype.getWorkflow=function(){
return this.workflow;
};
Line.prototype.isResizeable=function(){
return true;
};
Line.prototype.setCanvas=function(_3ecb){
this.canvas=_3ecb;
if(this.graphics!=null){
this.graphics.clear();
}
this.graphics=null;
};
Line.prototype.setWorkflow=function(_3ecc){
this.workflow=_3ecc;
if(this.graphics!=null){
this.graphics.clear();
}
this.graphics=null;
};
Line.prototype.paint=function(){
if(this.graphics==null){
this.graphics=new jsGraphics(this.id);
}else{
this.graphics.clear();
}
this.graphics.setStroke(this.stroke);
this.graphics.setColor(this.lineColor.getHTMLStyle());
this.graphics.drawLine(this.startX,this.startY,this.endX,this.endY);
this.graphics.paint();
};
Line.prototype.attachMoveListener=function(_3ecd){
this.moveListener.add(_3ecd);
};
Line.prototype.detachMoveListener=function(_3ece){
this.moveListener.remove(_3ece);
};
Line.prototype.fireMoveEvent=function(){
var size=this.moveListener.getSize();
for(var i=0;i<size;i++){
this.moveListener.get(i).onOtherFigureMoved(this);
}
};
Line.prototype.onOtherFigureMoved=function(_3ed1){
};
Line.prototype.setLineWidth=function(w){
this.stroke=w;
if(this.graphics!=null){
this.paint();
}
this.setDocumentDirty();
};
Line.prototype.setColor=function(color){
this.lineColor=color;
if(this.graphics!=null){
this.paint();
}
this.setDocumentDirty();
};
Line.prototype.getColor=function(){
return this.lineColor;
};
Line.prototype.setAlpha=function(_3ed4){
if(_3ed4==this.alpha){
return;
}
try{
this.html.style.MozOpacity=_3ed4;
}
catch(exc){
}
try{
this.html.style.opacity=_3ed4;
}
catch(exc){
}
try{
var _3ed5=Math.round(_3ed4*100);
if(_3ed5>=99){
this.html.style.filter="";
}else{
this.html.style.filter="alpha(opacity="+_3ed5+")";
}
}
catch(exc){
}
this.alpha=_3ed4;
};
Line.prototype.setStartPoint=function(x,y){
this.startX=x;
this.startY=y;
if(this.graphics!=null){
this.paint();
}
this.setDocumentDirty();
};
Line.prototype.setEndPoint=function(x,y){
this.endX=x;
this.endY=y;
if(this.graphics!=null){
this.paint();
}
this.setDocumentDirty();
};
Line.prototype.getStartX=function(){
return this.startX;
};
Line.prototype.getStartY=function(){
return this.startY;
};
Line.prototype.getStartPoint=function(){
return new Point(this.startX,this.startY);
};
Line.prototype.getEndX=function(){
return this.endX;
};
Line.prototype.getEndY=function(){
return this.endY;
};
Line.prototype.getEndPoint=function(){
return new Point(this.endX,this.endY);
};
Line.prototype.isSelectable=function(){
return this.selectable;
};
Line.prototype.setSelectable=function(flag){
this.selectable=flag;
};
Line.prototype.isDeleteable=function(){
return this.deleteable;
};
Line.prototype.setDeleteable=function(flag){
this.deleteable=flag;
};
Line.prototype.getLength=function(){
return Math.sqrt((this.startX-this.endX)*(this.startX-this.endX)+(this.startY-this.endY)*(this.startY-this.endY));
};
Line.prototype.getAngle=function(){
var _3edc=this.getLength();
var angle=-(180/Math.PI)*Math.asin((this.startY-this.endY)/_3edc);
if(angle<0){
if(this.endX<this.startX){
angle=Math.abs(angle)+180;
}else{
angle=360-Math.abs(angle);
}
}else{
if(this.endX<this.startX){
angle=180-angle;
}
}
return angle;
};
Line.prototype.onContextMenu=function(x,y){
var menu=this.getContextMenu();
if(menu!=null){
this.workflow.showMenu(menu,x,y);
}
};
Line.prototype.getContextMenu=function(){
return null;
};
Line.prototype.onDoubleClick=function(){
};
Line.prototype.setDocumentDirty=function(){
if(this.workflow!=null){
this.workflow.setDocumentDirty();
}
};
Line.prototype.generateUId=function(){
var chars="0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
var _3ee2=10;
var _3ee3=10;
nbTry=0;
while(nbTry<1000){
var id="";
for(var i=0;i<_3ee2;i++){
var rnum=Math.floor(Math.random()*chars.length);
id+=chars.substring(rnum,rnum+1);
}
elem=document.getElementById(id);
if(!elem){
return id;
}
nbTry+=1;
}
return null;
};
Line.prototype.containsPoint=function(px,py){
return Line.hit(this.startX,this.startY,this.endX,this.endY,px,py);
};
Line.hit=function(X1,Y1,X2,Y2,px,py){
var _3eef=5;
X2-=X1;
Y2-=Y1;
px-=X1;
py-=Y1;
var _3ef0=px*X2+py*Y2;
var _3ef1;
if(_3ef0<=0){
_3ef1=0;
}else{
px=X2-px;
py=Y2-py;
_3ef0=px*X2+py*Y2;
if(_3ef0<=0){
_3ef1=0;
}else{
_3ef1=_3ef0*_3ef0/(X2*X2+Y2*Y2);
}
}
var lenSq=px*px+py*py-_3ef1;
if(lenSq<0){
lenSq=0;
}
return Math.sqrt(lenSq)<_3eef;
};
ConnectionRouter=function(){
};
ConnectionRouter.prototype.type="ConnectionRouter";
ConnectionRouter.prototype.getDirection=function(r,p){
var _3f88=Math.abs(r.x-p.x);
var _3f89=3;
var i=Math.abs(r.y-p.y);
if(i<=_3f88){
_3f88=i;
_3f89=0;
}
i=Math.abs(r.getBottom()-p.y);
if(i<=_3f88){
_3f88=i;
_3f89=2;
}
i=Math.abs(r.getRight()-p.x);
if(i<_3f88){
_3f88=i;
_3f89=1;
}
return _3f89;
};
ConnectionRouter.prototype.getEndDirection=function(conn){
var p=conn.getEndPoint();
var rect=conn.getTarget().getParent().getBounds();
return this.getDirection(rect,p);
};
ConnectionRouter.prototype.getStartDirection=function(conn){
var p=conn.getStartPoint();
var rect=conn.getSource().getParent().getBounds();
return this.getDirection(rect,p);
};
ConnectionRouter.prototype.route=function(_3f91){
};
NullConnectionRouter=function(){
};
NullConnectionRouter.prototype=new ConnectionRouter;
NullConnectionRouter.prototype.type="NullConnectionRouter";
NullConnectionRouter.prototype.invalidate=function(){
};
NullConnectionRouter.prototype.route=function(_3f6a){
_3f6a.addPoint(_3f6a.getStartPoint());
_3f6a.addPoint(_3f6a.getEndPoint());
};
ManhattanConnectionRouter=function(){
this.MINDIST=20;
};
ManhattanConnectionRouter.prototype=new ConnectionRouter;
ManhattanConnectionRouter.prototype.type="ManhattanConnectionRouter";
ManhattanConnectionRouter.prototype.route=function(conn){
var _3ba8=conn.getStartPoint();
var _3ba9=this.getStartDirection(conn);
var toPt=conn.getEndPoint();
var toDir=this.getEndDirection(conn);
this._route(conn,toPt,toDir,_3ba8,_3ba9);
};
ManhattanConnectionRouter.prototype._route=function(conn,_3bad,_3bae,toPt,toDir){
var TOL=0.1;
var _3bb2=0.01;
var UP=0;
var RIGHT=1;
var DOWN=2;
var LEFT=3;
var xDiff=_3bad.x-toPt.x;
var yDiff=_3bad.y-toPt.y;
var point;
var dir;
if(((xDiff*xDiff)<(_3bb2))&&((yDiff*yDiff)<(_3bb2))){
conn.addPoint(new Point(toPt.x,toPt.y));
return;
}
if(_3bae==LEFT){
if((xDiff>0)&&((yDiff*yDiff)<TOL)&&(toDir==RIGHT)){
point=toPt;
dir=toDir;
}else{
if(xDiff<0){
point=new Point(_3bad.x-this.MINDIST,_3bad.y);
}else{
if(((yDiff>0)&&(toDir==DOWN))||((yDiff<0)&&(toDir==UP))){
point=new Point(toPt.x,_3bad.y);
}else{
if(_3bae==toDir){
var pos=Math.min(_3bad.x,toPt.x)-this.MINDIST;
point=new Point(pos,_3bad.y);
}else{
point=new Point(_3bad.x-(xDiff/2),_3bad.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_3bae==RIGHT){
if((xDiff<0)&&((yDiff*yDiff)<TOL)&&(toDir==LEFT)){
point=toPt;
dir=toDir;
}else{
if(xDiff>0){
point=new Point(_3bad.x+this.MINDIST,_3bad.y);
}else{
if(((yDiff>0)&&(toDir==DOWN))||((yDiff<0)&&(toDir==UP))){
point=new Point(toPt.x,_3bad.y);
}else{
if(_3bae==toDir){
var pos=Math.max(_3bad.x,toPt.x)+this.MINDIST;
point=new Point(pos,_3bad.y);
}else{
point=new Point(_3bad.x-(xDiff/2),_3bad.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_3bae==DOWN){
if(((xDiff*xDiff)<TOL)&&(yDiff<0)&&(toDir==UP)){
point=toPt;
dir=toDir;
}else{
if(yDiff>0){
point=new Point(_3bad.x,_3bad.y+this.MINDIST);
}else{
if(((xDiff>0)&&(toDir==RIGHT))||((xDiff<0)&&(toDir==LEFT))){
point=new Point(_3bad.x,toPt.y);
}else{
if(_3bae==toDir){
var pos=Math.max(_3bad.y,toPt.y)+this.MINDIST;
point=new Point(_3bad.x,pos);
}else{
point=new Point(_3bad.x,_3bad.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}else{
if(_3bae==UP){
if(((xDiff*xDiff)<TOL)&&(yDiff>0)&&(toDir==DOWN)){
point=toPt;
dir=toDir;
}else{
if(yDiff<0){
point=new Point(_3bad.x,_3bad.y-this.MINDIST);
}else{
if(((xDiff>0)&&(toDir==RIGHT))||((xDiff<0)&&(toDir==LEFT))){
point=new Point(_3bad.x,toPt.y);
}else{
if(_3bae==toDir){
var pos=Math.min(_3bad.y,toPt.y)-this.MINDIST;
point=new Point(_3bad.x,pos);
}else{
point=new Point(_3bad.x,_3bad.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}
}
}
}
this._route(conn,point,dir,toPt,toDir);
conn.addPoint(_3bad);
};
BezierConnectionRouter=function(_354a){
if(!_354a){
this.cheapRouter=new ManhattanConnectionRouter();
}else{
this.cheapRouter=null;
}
this.iteration=5;
};
BezierConnectionRouter.prototype=new ConnectionRouter;
BezierConnectionRouter.prototype.type="BezierConnectionRouter";
BezierConnectionRouter.prototype.drawBezier=function(_354b,_354c,t,iter){
var n=_354b.length-1;
var q=new Array();
var _3551=n+1;
for(var i=0;i<_3551;i++){
q[i]=new Array();
q[i][0]=_354b[i];
}
for(var j=1;j<=n;j++){
for(var i=0;i<=(n-j);i++){
q[i][j]=new Point((1-t)*q[i][j-1].x+t*q[i+1][j-1].x,(1-t)*q[i][j-1].y+t*q[i+1][j-1].y);
}
}
var c1=new Array();
var c2=new Array();
for(var i=0;i<n+1;i++){
c1[i]=q[0][i];
c2[i]=q[i][n-i];
}
if(iter>=0){
this.drawBezier(c1,_354c,t,--iter);
this.drawBezier(c2,_354c,t,--iter);
}else{
for(var i=0;i<n;i++){
_354c.push(q[i][n-i]);
}
}
};
BezierConnectionRouter.prototype.route=function(conn){
if(this.cheapRouter!=null&&(conn.getSource().getParent().isMoving==true||conn.getTarget().getParent().isMoving==true)){
this.cheapRouter.route(conn);
return;
}
var _3557=new Array();
var _3558=conn.getStartPoint();
var toPt=conn.getEndPoint();
this._route(_3557,conn,toPt,this.getEndDirection(conn),_3558,this.getStartDirection(conn));
var _355a=new Array();
this.drawBezier(_3557,_355a,0.5,this.iteration);
for(var i=0;i<_355a.length;i++){
conn.addPoint(_355a[i]);
}
conn.addPoint(toPt);
};
BezierConnectionRouter.prototype._route=function(_355c,conn,_355e,_355f,toPt,toDir){
var TOL=0.1;
var _3563=0.01;
var _3564=90;
var UP=0;
var RIGHT=1;
var DOWN=2;
var LEFT=3;
var xDiff=_355e.x-toPt.x;
var yDiff=_355e.y-toPt.y;
var point;
var dir;
if(((xDiff*xDiff)<(_3563))&&((yDiff*yDiff)<(_3563))){
_355c.push(new Point(toPt.x,toPt.y));
return;
}
if(_355f==LEFT){
if((xDiff>0)&&((yDiff*yDiff)<TOL)&&(toDir==RIGHT)){
point=toPt;
dir=toDir;
}else{
if(xDiff<0){
point=new Point(_355e.x-_3564,_355e.y);
}else{
if(((yDiff>0)&&(toDir==DOWN))||((yDiff<0)&&(toDir==UP))){
point=new Point(toPt.x,_355e.y);
}else{
if(_355f==toDir){
var pos=Math.min(_355e.x,toPt.x)-_3564;
point=new Point(pos,_355e.y);
}else{
point=new Point(_355e.x-(xDiff/2),_355e.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_355f==RIGHT){
if((xDiff<0)&&((yDiff*yDiff)<TOL)&&(toDir==LEFT)){
point=toPt;
dir=toDir;
}else{
if(xDiff>0){
point=new Point(_355e.x+_3564,_355e.y);
}else{
if(((yDiff>0)&&(toDir==DOWN))||((yDiff<0)&&(toDir==UP))){
point=new Point(toPt.x,_355e.y);
}else{
if(_355f==toDir){
var pos=Math.max(_355e.x,toPt.x)+_3564;
point=new Point(pos,_355e.y);
}else{
point=new Point(_355e.x-(xDiff/2),_355e.y);
}
}
}
if(yDiff>0){
dir=UP;
}else{
dir=DOWN;
}
}
}else{
if(_355f==DOWN){
if(((xDiff*xDiff)<TOL)&&(yDiff<0)&&(toDir==UP)){
point=toPt;
dir=toDir;
}else{
if(yDiff>0){
point=new Point(_355e.x,_355e.y+_3564);
}else{
if(((xDiff>0)&&(toDir==RIGHT))||((xDiff<0)&&(toDir==LEFT))){
point=new Point(_355e.x,toPt.y);
}else{
if(_355f==toDir){
var pos=Math.max(_355e.y,toPt.y)+_3564;
point=new Point(_355e.x,pos);
}else{
point=new Point(_355e.x,_355e.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}else{
if(_355f==UP){
if(((xDiff*xDiff)<TOL)&&(yDiff>0)&&(toDir==DOWN)){
point=toPt;
dir=toDir;
}else{
if(yDiff<0){
point=new Point(_355e.x,_355e.y-_3564);
}else{
if(((xDiff>0)&&(toDir==RIGHT))||((xDiff<0)&&(toDir==LEFT))){
point=new Point(_355e.x,toPt.y);
}else{
if(_355f==toDir){
var pos=Math.min(_355e.y,toPt.y)-_3564;
point=new Point(_355e.x,pos);
}else{
point=new Point(_355e.x,_355e.y-(yDiff/2));
}
}
}
if(xDiff>0){
dir=LEFT;
}else{
dir=RIGHT;
}
}
}
}
}
}
this._route(_355c,conn,point,dir,toPt,toDir);
_355c.push(_355e);
};
FanConnectionRouter=function(){
};
FanConnectionRouter.prototype=new NullConnectionRouter;
FanConnectionRouter.prototype.type="FanConnectionRouter";
FanConnectionRouter.prototype.route=function(conn){
var _40c7=conn.getStartPoint();
var toPt=conn.getEndPoint();
var lines=conn.getSource().getConnections();
var _40ca=new ArrayList();
var index=0;
for(var i=0;i<lines.getSize();i++){
var _40cd=lines.get(i);
if(_40cd.getTarget()==conn.getTarget()||_40cd.getSource()==conn.getTarget()){
_40ca.add(_40cd);
if(conn==_40cd){
index=_40ca.getSize();
}
}
}
if(_40ca.getSize()>1){
this.routeCollision(conn,index);
}else{
NullConnectionRouter.prototype.route.call(this,conn);
}
};
FanConnectionRouter.prototype.routeNormal=function(conn){
conn.addPoint(conn.getStartPoint());
conn.addPoint(conn.getEndPoint());
};
FanConnectionRouter.prototype.routeCollision=function(conn,index){
var start=conn.getStartPoint();
var end=conn.getEndPoint();
conn.addPoint(start);
var _40d3=10;
var _40d4=new Point((end.x+start.x)/2,(end.y+start.y)/2);
var _40d5=end.getPosition(start);
var ray;
if(_40d5==PositionConstants.SOUTH||_40d5==PositionConstants.EAST){
ray=new Point(end.x-start.x,end.y-start.y);
}else{
ray=new Point(start.x-end.x,start.y-end.y);
}
var _40d7=Math.sqrt(ray.x*ray.x+ray.y*ray.y);
var _40d8=_40d3*ray.x/_40d7;
var _40d9=_40d3*ray.y/_40d7;
var _40da;
if(index%2==0){
_40da=new Point(_40d4.x+(index/2)*(-1*_40d9),_40d4.y+(index/2)*_40d8);
}else{
_40da=new Point(_40d4.x+(index/2)*_40d9,_40d4.y+(index/2)*(-1*_40d8));
}
conn.addPoint(_40da);
conn.addPoint(end);
};
Graphics=function(_3a61,_3a62,_3a63){
this.jsGraphics=_3a61;
this.xt=_3a63.x;
this.yt=_3a63.y;
this.radian=_3a62*Math.PI/180;
this.sinRadian=Math.sin(this.radian);
this.cosRadian=Math.cos(this.radian);
};
Graphics.prototype.setStroke=function(x){
this.jsGraphics.setStroke(x);
};
Graphics.prototype.drawLine=function(x1,y1,x2,y2){
var _x1=this.xt+x1*this.cosRadian-y1*this.sinRadian;
var _y1=this.yt+x1*this.sinRadian+y1*this.cosRadian;
var _x2=this.xt+x2*this.cosRadian-y2*this.sinRadian;
var _y2=this.yt+x2*this.sinRadian+y2*this.cosRadian;
this.jsGraphics.drawLine(_x1,_y1,_x2,_y2);
};
Graphics.prototype.fillRect=function(x,y,w,h){
var x1=this.xt+x*this.cosRadian-y*this.sinRadian;
var y1=this.yt+x*this.sinRadian+y*this.cosRadian;
var x2=this.xt+(x+w)*this.cosRadian-y*this.sinRadian;
var y2=this.yt+(x+w)*this.sinRadian+y*this.cosRadian;
var x3=this.xt+(x+w)*this.cosRadian-(y+h)*this.sinRadian;
var y3=this.yt+(x+w)*this.sinRadian+(y+h)*this.cosRadian;
var x4=this.xt+x*this.cosRadian-(y+h)*this.sinRadian;
var y4=this.yt+x*this.sinRadian+(y+h)*this.cosRadian;
this.jsGraphics.fillPolygon([x1,x2,x3,x4],[y1,y2,y3,y4]);
};
Graphics.prototype.fillPolygon=function(_3a79,_3a7a){
var rotX=new Array();
var rotY=new Array();
for(var i=0;i<_3a79.length;i++){
rotX[i]=this.xt+_3a79[i]*this.cosRadian-_3a7a[i]*this.sinRadian;
rotY[i]=this.yt+_3a79[i]*this.sinRadian+_3a7a[i]*this.cosRadian;
}
this.jsGraphics.fillPolygon(rotX,rotY);
};
Graphics.prototype.setColor=function(color){
this.jsGraphics.setColor(color.getHTMLStyle());
};
Graphics.prototype.drawPolygon=function(_3a7f,_3a80){
var rotX=new Array();
var rotY=new Array();
for(var i=0;i<_3a7f.length;i++){
rotX[i]=this.xt+_3a7f[i]*this.cosRadian-_3a80[i]*this.sinRadian;
rotY[i]=this.yt+_3a7f[i]*this.sinRadian+_3a80[i]*this.cosRadian;
}
this.jsGraphics.drawPolygon(rotX,rotY);
};
Connection=function(){
Line.call(this);
this.sourcePort=null;
this.targetPort=null;
this.sourceDecorator=null;
this.targetDecorator=null;
this.sourceAnchor=new ConnectionAnchor();
this.targetAnchor=new ConnectionAnchor();
this.router=Connection.defaultRouter;
this.lineSegments=new ArrayList();
this.children=new ArrayList();
this.setColor(new Color(0,0,115));
this.setLineWidth(1);
};
Connection.prototype=new Line;
Connection.defaultRouter=new ManhattanConnectionRouter();
Connection.setDefaultRouter=function(_2e24){
Connection.defaultRouter=_2e24;
};
Connection.prototype.disconnect=function(){
if(this.sourcePort!=null){
this.sourcePort.detachMoveListener(this);
this.fireSourcePortRouteEvent();
}
if(this.targetPort!=null){
this.targetPort.detachMoveListener(this);
this.fireTargetPortRouteEvent();
}
};
Connection.prototype.reconnect=function(){
if(this.sourcePort!=null){
this.sourcePort.attachMoveListener(this);
this.fireSourcePortRouteEvent();
}
if(this.targetPort!=null){
this.targetPort.attachMoveListener(this);
this.fireTargetPortRouteEvent();
}
};
Connection.prototype.isResizeable=function(){
return true;
};
Connection.prototype.addFigure=function(_2e25,_2e26){
var entry=new Object();
entry.figure=_2e25;
entry.locator=_2e26;
this.children.add(entry);
if(this.graphics!=null){
this.paint();
}
};
Connection.prototype.setSourceDecorator=function(_2e28){
this.sourceDecorator=_2e28;
if(this.graphics!=null){
this.paint();
}
};
Connection.prototype.setTargetDecorator=function(_2e29){
this.targetDecorator=_2e29;
if(this.graphics!=null){
this.paint();
}
};
Connection.prototype.setSourceAnchor=function(_2e2a){
this.sourceAnchor=_2e2a;
this.sourceAnchor.setOwner(this.sourcePort);
if(this.graphics!=null){
this.paint();
}
};
Connection.prototype.setTargetAnchor=function(_2e2b){
this.targetAnchor=_2e2b;
this.targetAnchor.setOwner(this.targetPort);
if(this.graphics!=null){
this.paint();
}
};
Connection.prototype.setRouter=function(_2e2c){
if(_2e2c!=null){
this.router=_2e2c;
}else{
this.router=new NullConnectionRouter();
}
if(this.graphics!=null){
this.paint();
}
};
Connection.prototype.getRouter=function(){
return this.router;
};
Connection.prototype.paint=function(){
for(var i=0;i<this.children.getSize();i++){
var entry=this.children.get(i);
if(entry.isAppended==true){
this.html.removeChild(entry.figure.getHTMLElement());
}
entry.isAppended=false;
}
if(this.graphics==null){
this.graphics=new jsGraphics(this.id);
}else{
this.graphics.clear();
}
this.graphics.setStroke(this.stroke);
this.graphics.setColor(this.lineColor.getHTMLStyle());
this.startStroke();
this.router.route(this);
if(this.getSource().getParent().isMoving==false&&this.getTarget().getParent().isMoving==false){
if(this.targetDecorator!=null){
this.targetDecorator.paint(new Graphics(this.graphics,this.getEndAngle(),this.getEndPoint()));
}
if(this.sourceDecorator!=null){
this.sourceDecorator.paint(new Graphics(this.graphics,this.getStartAngle(),this.getStartPoint()));
}
}
this.finishStroke();
for(var i=0;i<this.children.getSize();i++){
var entry=this.children.get(i);
this.html.appendChild(entry.figure.getHTMLElement());
entry.isAppended=true;
entry.locator.relocate(entry.figure);
}
};
Connection.prototype.getStartPoint=function(){
if(this.isMoving==false){
return this.sourceAnchor.getLocation(this.targetAnchor.getReferencePoint());
}else{
return Line.prototype.getStartPoint.call(this);
}
};
Connection.prototype.getEndPoint=function(){
if(this.isMoving==false){
return this.targetAnchor.getLocation(this.sourceAnchor.getReferencePoint());
}else{
return Line.prototype.getEndPoint.call(this);
}
};
Connection.prototype.startStroke=function(){
this.oldPoint=null;
this.lineSegments=new ArrayList();
};
Connection.prototype.finishStroke=function(){
this.graphics.paint();
this.oldPoint=null;
};
Connection.prototype.getPoints=function(){
var _2e2f=new ArrayList();
var line;
for(var i=0;i<this.lineSegments.getSize();i++){
line=this.lineSegments.get(i);
_2e2f.add(line.start);
}
_2e2f.add(line.end);
return _2e2f;
};
Connection.prototype.addPoint=function(p){
p=new Point(parseInt(p.x),parseInt(p.y));
if(this.oldPoint!=null){
this.graphics.drawLine(this.oldPoint.x,this.oldPoint.y,p.x,p.y);
var line=new Object();
line.start=this.oldPoint;
line.end=p;
this.lineSegments.add(line);
}
this.oldPoint=new Object();
this.oldPoint.x=p.x;
this.oldPoint.y=p.y;
};
Connection.prototype.setSource=function(port){
if(this.sourcePort!=null){
this.sourcePort.detachMoveListener(this);
}
this.sourcePort=port;
if(this.sourcePort==null){
return;
}
this.sourceAnchor.setOwner(this.sourcePort);
this.fireSourcePortRouteEvent();
this.sourcePort.attachMoveListener(this);
this.setStartPoint(port.getAbsoluteX(),port.getAbsoluteY());
};
Connection.prototype.getSource=function(){
return this.sourcePort;
};
Connection.prototype.setTarget=function(port){
if(this.targetPort!=null){
this.targetPort.detachMoveListener(this);
}
this.targetPort=port;
if(this.targetPort==null){
return;
}
this.targetAnchor.setOwner(this.targetPort);
this.fireTargetPortRouteEvent();
this.targetPort.attachMoveListener(this);
this.setEndPoint(port.getAbsoluteX(),port.getAbsoluteY());
};
Connection.prototype.getTarget=function(){
return this.targetPort;
};
Connection.prototype.onOtherFigureMoved=function(_2e36){
if(_2e36==this.sourcePort){
this.setStartPoint(this.sourcePort.getAbsoluteX(),this.sourcePort.getAbsoluteY());
}else{
this.setEndPoint(this.targetPort.getAbsoluteX(),this.targetPort.getAbsoluteY());
}
};
Connection.prototype.containsPoint=function(px,py){
for(var i=0;i<this.lineSegments.getSize();i++){
var line=this.lineSegments.get(i);
if(Line.hit(line.start.x,line.start.y,line.end.x,line.end.y,px,py)){
return true;
}
}
return false;
};
Connection.prototype.getStartAngle=function(){
var p1=this.lineSegments.get(0).start;
var p2=this.lineSegments.get(0).end;
if(this.router instanceof BezierConnectionRouter){
p2=this.lineSegments.get(5).end;
}
var _2e3d=Math.sqrt((p1.x-p2.x)*(p1.x-p2.x)+(p1.y-p2.y)*(p1.y-p2.y));
var angle=-(180/Math.PI)*Math.asin((p1.y-p2.y)/_2e3d);
if(angle<0){
if(p2.x<p1.x){
angle=Math.abs(angle)+180;
}else{
angle=360-Math.abs(angle);
}
}else{
if(p2.x<p1.x){
angle=180-angle;
}
}
return angle;
};
Connection.prototype.getEndAngle=function(){
var p1=this.lineSegments.get(this.lineSegments.getSize()-1).end;
var p2=this.lineSegments.get(this.lineSegments.getSize()-1).start;
if(this.router instanceof BezierConnectionRouter){
p2=this.lineSegments.get(this.lineSegments.getSize()-5).end;
}
var _2e41=Math.sqrt((p1.x-p2.x)*(p1.x-p2.x)+(p1.y-p2.y)*(p1.y-p2.y));
var angle=-(180/Math.PI)*Math.asin((p1.y-p2.y)/_2e41);
if(angle<0){
if(p2.x<p1.x){
angle=Math.abs(angle)+180;
}else{
angle=360-Math.abs(angle);
}
}else{
if(p2.x<p1.x){
angle=180-angle;
}
}
return angle;
};
Connection.prototype.fireSourcePortRouteEvent=function(){
var _2e43=this.sourcePort.getConnections();
for(var i=0;i<_2e43.getSize();i++){
_2e43.get(i).paint();
}
};
Connection.prototype.fireTargetPortRouteEvent=function(){
var _2e45=this.targetPort.getConnections();
for(var i=0;i<_2e45.getSize();i++){
_2e45.get(i).paint();
}
};
ConnectionAnchor=function(owner){
this.owner=owner;
};
ConnectionAnchor.prototype.type="ConnectionAnchor";
ConnectionAnchor.prototype.getLocation=function(_40c2){
return this.getReferencePoint();
};
ConnectionAnchor.prototype.getOwner=function(){
return this.owner;
};
ConnectionAnchor.prototype.setOwner=function(owner){
this.owner=owner;
};
ConnectionAnchor.prototype.getBox=function(){
return this.getOwner().getAbsoluteBounds();
};
ConnectionAnchor.prototype.getReferencePoint=function(){
if(this.getOwner()==null){
return null;
}else{
return this.getOwner().getAbsolutePosition();
}
};
ChopboxConnectionAnchor=function(owner){
ConnectionAnchor.call(this,owner);
};
ChopboxConnectionAnchor.prototype=new ConnectionAnchor;
ChopboxConnectionAnchor.prototype.type="ChopboxConnectionAnchor";
ChopboxConnectionAnchor.prototype.getLocation=function(_3854){
var r=new Dimension();
r.setBounds(this.getBox());
r.translate(-1,-1);
r.resize(1,1);
var _3856=r.x+r.w/2;
var _3857=r.y+r.h/2;
if(r.isEmpty()||(_3854.x==_3856&&_3854.y==_3857)){
return new Point(_3856,_3857);
}
var dx=_3854.x-_3856;
var dy=_3854.y-_3857;
var scale=0.5/Math.max(Math.abs(dx)/r.w,Math.abs(dy)/r.h);
dx*=scale;
dy*=scale;
_3856+=dx;
_3857+=dy;
return new Point(Math.round(_3856),Math.round(_3857));
};
ChopboxConnectionAnchor.prototype.getBox=function(){
return this.getOwner().getParent().getBounds();
};
ChopboxConnectionAnchor.prototype.getReferencePoint=function(){
return this.getBox().getCenter();
};
ConnectionDecorator=function(){
this.color=new Color(0,0,0);
this.backgroundColor=new Color(250,250,250);
};
ConnectionDecorator.prototype.type="ConnectionDecorator";
ConnectionDecorator.prototype.paint=function(g){
};
ConnectionDecorator.prototype.setColor=function(c){
this.color=c;
};
ConnectionDecorator.prototype.setBackgroundColor=function(c){
this.backgroundColor=c;
};
ArrowConnectionDecorator=function(){
};
ArrowConnectionDecorator.prototype=new ConnectionDecorator;
ArrowConnectionDecorator.prototype.type="ArrowConnectionDecorator";
ArrowConnectionDecorator.prototype.paint=function(g){
if(this.backgroundColor!=null){
var zoomfactor = 1;
if(typeof workflow.zoomfactor != undefined && workflow.zoomfactor != '')
zoomfactor = workflow.zoomfactor;
g.setColor(this.backgroundColor);
g.fillPolygon([1*zoomfactor,10*zoomfactor,10*zoomfactor,1*zoomfactor],[0,5*zoomfactor,-5*zoomfactor,0]);
}
g.setColor(this.color);
g.setStroke(1);
g.drawPolygon([1*zoomfactor,10*zoomfactor,10*zoomfactor,1*zoomfactor],[0,5*zoomfactor,-5*zoomfactor,0]);
g.fillPolygon([1*zoomfactor,10*zoomfactor,10*zoomfactor,1*zoomfactor],[0,5*zoomfactor,-5*zoomfactor,0]);
};
CompartmentFigure=function(){
Node.call(this);
this.children=new ArrayList();
this.setBorder(new LineBorder(1));
this.dropable=new DropTarget(this.html);
this.dropable.node=this;
this.dropable.addEventListener("figureenter",function(_4072){
_4072.target.node.onFigureEnter(_4072.relatedTarget.node);
});
this.dropable.addEventListener("figureleave",function(_4073){
_4073.target.node.onFigureLeave(_4073.relatedTarget.node);
});
this.dropable.addEventListener("figuredrop",function(_4074){
_4074.target.node.onFigureDrop(_4074.relatedTarget.node);
});
};
CompartmentFigure.prototype=new Node;
CompartmentFigure.prototype.type="CompartmentFigure";
CompartmentFigure.prototype.onFigureEnter=function(_4075){
};
CompartmentFigure.prototype.onFigureLeave=function(_4076){
};
CompartmentFigure.prototype.onFigureDrop=function(_4077){
};
CompartmentFigure.prototype.getChildren=function(){
return this.children;
};
CompartmentFigure.prototype.addChild=function(_4078){
_4078.setZOrder(this.getZOrder()+1);
_4078.setParent(this);
this.children.add(_4078);
};
CompartmentFigure.prototype.removeChild=function(_4079){
_4079.setParent(null);
this.children.remove(_4079);
};
CompartmentFigure.prototype.setZOrder=function(index){
Node.prototype.setZOrder.call(this,index);
for(var i=0;i<this.children.getSize();i++){
this.children.get(i).setZOrder(index+1);
}
};
CompartmentFigure.prototype.setPosition=function(xPos,yPos){
var oldX=this.getX();
var oldY=this.getY();
Node.prototype.setPosition.call(this,xPos,yPos);
for(var i=0;i<this.children.getSize();i++){
var child=this.children.get(i);
child.setPosition(child.getX()+this.getX()-oldX,child.getY()+this.getY()-oldY);
}
};
CompartmentFigure.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
Node.prototype.onDrag.call(this);
for(var i=0;i<this.children.getSize();i++){
var child=this.children.get(i);
child.setPosition(child.getX()+this.getX()-oldX,child.getY()+this.getY()-oldY);
}
};
Document=function(_3a3b){
this.canvas=_3a3b;
};
Document.prototype.getFigures=function(){
var _3a3c=new ArrayList();
var _3a3d=this.canvas.figures;
var _3a3e=this.canvas.dialogs;
for(var i=0;i<_3a3d.getSize();i++){
var _3a40=_3a3d.get(i);
if(_3a3e.indexOf(_3a40)==-1&&_3a40.getParent()==null&&!(_3a40 instanceof Window)){
_3a3c.add(_3a40);
}
}
return _3a3c;
};
Document.prototype.getFigure=function(id){
return this.canvas.getFigure(id);
};
Document.prototype.getLines=function(){
return this.canvas.getLines();
};
Annotation=function(msg){
this.msg=msg;
this.color=new Color(0,0,0);
this.bgColor=new Color(241,241,121);
this.fontSize=10;
this.textNode=null;
Figure.call(this);
};
Annotation.prototype=new Figure;
Annotation.prototype.type="Annotation";
Annotation.prototype.createHTMLElement=function(){
var item=Figure.prototype.createHTMLElement.call(this);
item.style.color=this.color.getHTMLStyle();
item.style.backgroundColor=this.bgColor.getHTMLStyle();
item.style.fontSize=this.fontSize+"pt";
item.style.width="auto";
item.style.height="auto";
item.style.margin="0px";
item.style.padding="0px";
item.onselectstart=function(){
return false;
};
item.unselectable="on";
item.style.MozUserSelect="none";
item.style.cursor="default";
this.textNode=document.createTextNode(this.msg);
item.appendChild(this.textNode);
this.disableTextSelection(item);
return item;
};
Annotation.prototype.onDoubleClick=function(){
var _409d=new AnnotationDialog(this);
this.workflow.showDialog(_409d);
};
Annotation.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!=null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
}
};
Annotation.prototype.getBackgroundColor=function(){
return this.bgColor;
};
Annotation.prototype.setFontSize=function(size){
this.fontSize=size;
this.html.style.fontSize=this.fontSize+"pt";
};
Annotation.prototype.getText=function(){
return this.msg;
};
Annotation.prototype.setText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createTextNode(this.msg);
this.html.appendChild(this.textNode);
};
Annotation.prototype.setStyledText=function(text){
this.msg=text;
this.html.removeChild(this.textNode);
this.textNode=document.createElement("div");
this.textNode.innerHTML=text;
this.html.appendChild(this.textNode);
};
ResizeHandle=function(_2d2f,type){
Rectangle.call(this,5,5);
this.type=type;
var _2d31=this.getWidth();
var _2d32=_2d31/2;
switch(this.type){
case 1:
this.setSnapToGridAnchor(new Point(_2d31,_2d31));
break;
case 2:
this.setSnapToGridAnchor(new Point(_2d32,_2d31));
break;
case 3:
this.setSnapToGridAnchor(new Point(0,_2d31));
break;
case 4:
this.setSnapToGridAnchor(new Point(0,_2d32));
break;
case 5:
this.setSnapToGridAnchor(new Point(0,0));
break;
case 6:
this.setSnapToGridAnchor(new Point(_2d32,0));
break;
case 7:
this.setSnapToGridAnchor(new Point(_2d31,0));
break;
case 8:
this.setSnapToGridAnchor(new Point(_2d31,_2d32));
break;
}
this.setBackgroundColor(new Color(0,255,0));
this.setWorkflow(_2d2f);
this.setZOrder(10000);
};
ResizeHandle.prototype=new Rectangle;
ResizeHandle.prototype.type="ResizeHandle";
ResizeHandle.prototype.getSnapToDirection=function(){
switch(this.type){
case 1:
return SnapToHelper.NORTH_WEST;
case 2:
return SnapToHelper.NORTH;
case 3:
return SnapToHelper.NORTH_EAST;
case 4:
return SnapToHelper.EAST;
case 5:
return SnapToHelper.SOUTH_EAST;
case 6:
return SnapToHelper.SOUTH;
case 7:
return SnapToHelper.SOUTH_WEST;
case 8:
return SnapToHelper.WEST;
}
};
ResizeHandle.prototype.onDragend=function(){
if(this.commandMove==null){
return;
}
var _2d33=this.workflow.currentSelection;
this.commandMove.setPosition(_2d33.getX(),_2d33.getY());
this.commandResize.setDimension(_2d33.getWidth(),_2d33.getHeight());
this.workflow.getCommandStack().execute(this.commandResize);
this.workflow.getCommandStack().execute(this.commandMove);
this.commandMove=null;
this.commandResize=null;
this.workflow.hideSnapToHelperLines();
};
ResizeHandle.prototype.setPosition=function(xPos,yPos){
this.x=xPos;
this.y=yPos;
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
};
ResizeHandle.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
var _2d38=this.workflow.currentSelection;
this.commandMove=new CommandMove(_2d38,_2d38.getX(),_2d38.getY());
this.commandResize=new CommandResize(_2d38,_2d38.getWidth(),_2d38.getHeight());
return true;
};
ResizeHandle.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
Rectangle.prototype.onDrag.call(this);
var diffX=oldX-this.getX();
var diffY=oldY-this.getY();
var _2d3d=this.workflow.currentSelection.getX();
var _2d3e=this.workflow.currentSelection.getY();
var _2d3f=this.workflow.currentSelection.getWidth();
var _2d40=this.workflow.currentSelection.getHeight();
switch(this.type){
case 1:
this.workflow.currentSelection.setPosition(_2d3d-diffX,_2d3e-diffY);
this.workflow.currentSelection.setDimension(_2d3f+diffX,_2d40+diffY);
break;
case 2:
this.workflow.currentSelection.setPosition(_2d3d,_2d3e-diffY);
this.workflow.currentSelection.setDimension(_2d3f,_2d40+diffY);
break;
case 3:
this.workflow.currentSelection.setPosition(_2d3d,_2d3e-diffY);
this.workflow.currentSelection.setDimension(_2d3f-diffX,_2d40+diffY);
break;
case 4:
this.workflow.currentSelection.setPosition(_2d3d,_2d3e);
this.workflow.currentSelection.setDimension(_2d3f-diffX,_2d40);
break;
case 5:
this.workflow.currentSelection.setPosition(_2d3d,_2d3e);
this.workflow.currentSelection.setDimension(_2d3f-diffX,_2d40-diffY);
break;
case 6:
this.workflow.currentSelection.setPosition(_2d3d,_2d3e);
this.workflow.currentSelection.setDimension(_2d3f,_2d40-diffY);
break;
case 7:
this.workflow.currentSelection.setPosition(_2d3d-diffX,_2d3e);
this.workflow.currentSelection.setDimension(_2d3f+diffX,_2d40-diffY);
break;
case 8:
this.workflow.currentSelection.setPosition(_2d3d-diffX,_2d3e);
this.workflow.currentSelection.setDimension(_2d3f+diffX,_2d40);
break;
}
this.workflow.moveResizeHandles(this.workflow.getCurrentSelection());
};
ResizeHandle.prototype.setCanDrag=function(flag){
Rectangle.prototype.setCanDrag.call(this,flag);
if(!flag){
this.html.style.cursor="";
return;
}
switch(this.type){
case 1:
this.html.style.cursor="nw-resize";
break;
case 2:
this.html.style.cursor="s-resize";
break;
case 3:
this.html.style.cursor="ne-resize";
break;
case 4:
this.html.style.cursor="w-resize";
break;
case 5:
this.html.style.cursor="se-resize";
break;
case 6:
this.html.style.cursor="n-resize";
break;
case 7:
this.html.style.cursor="sw-resize";
break;
case 8:
this.html.style.cursor="e-resize";
break;
}
};
ResizeHandle.prototype.onKeyDown=function(_2d42,ctrl){
this.workflow.onKeyDown(_2d42,ctrl);
};
ResizeHandle.prototype.fireMoveEvent=function(){
};
LineStartResizeHandle=function(_40f9){
Rectangle.call(this);
this.setDimension(10,10);
this.setBackgroundColor(new Color(0,255,0));
this.setWorkflow(_40f9);
this.setZOrder(10000);
};
LineStartResizeHandle.prototype=new Rectangle;
LineStartResizeHandle.prototype.type="LineStartResizeHandle";
LineStartResizeHandle.prototype.onDragend=function(){
var line=this.workflow.currentSelection;
if(line instanceof Connection){
var start=line.sourceAnchor.getLocation(line.targetAnchor.getReferencePoint());
line.setStartPoint(start.x,start.y);
this.getWorkflow().showLineResizeHandles(line);
line.setRouter(line.oldRouter);
}else{
if(this.command==null){
return;
}
var x1=line.getStartX();
var y1=line.getStartY();
var x2=line.getEndX();
var y2=line.getEndY();
this.command.setEndPoints(x1,y1,x2,y2);
this.getWorkflow().getCommandStack().execute(this.command);
this.command=null;
}
};
LineStartResizeHandle.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
var line=this.workflow.currentSelection;
if(line instanceof Connection){
this.command=new CommandReconnect(line);
line.oldRouter=line.getRouter();
line.setRouter(new NullConnectionRouter());
}else{
var x1=line.getStartX();
var y1=line.getStartY();
var x2=line.getEndX();
var y2=line.getEndY();
this.command=new CommandMoveLine(line,x1,y1,x2,y2);
}
return true;
};
LineStartResizeHandle.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
Rectangle.prototype.onDrag.call(this);
var diffX=oldX-this.getX();
var diffY=oldY-this.getY();
var _410b=this.workflow.currentSelection.getStartPoint();
var line=this.workflow.currentSelection;
line.setStartPoint(_410b.x-diffX,_410b.y-diffY);
line.isMoving=true;
};
LineStartResizeHandle.prototype.onDrop=function(_410d){
var line=this.workflow.currentSelection;
line.isMoving=false;
if(line instanceof Connection){
this.command.setNewPorts(_410d,line.getTarget());
this.getWorkflow().getCommandStack().execute(this.command);
}
this.command=null;
};
LineStartResizeHandle.prototype.onKeyDown=function(_410f,ctrl){
if(this.workflow!=null){
this.workflow.onKeyDown(_410f,ctrl);
}
};
LineStartResizeHandle.prototype.fireMoveEvent=function(){
};
LineEndResizeHandle=function(_3f2a){
Rectangle.call(this);
this.setDimension(10,10);
this.setBackgroundColor(new Color(0,255,0));
this.setWorkflow(_3f2a);
this.setZOrder(10000);
};
LineEndResizeHandle.prototype=new Rectangle;
LineEndResizeHandle.prototype.type="LineEndResizeHandle";
LineEndResizeHandle.prototype.onDragend=function(){
var line=this.workflow.currentSelection;
if(line instanceof Connection){
var end=line.targetAnchor.getLocation(line.sourceAnchor.getReferencePoint());
line.setEndPoint(end.x,end.y);
this.getWorkflow().showLineResizeHandles(line);
line.setRouter(line.oldRouter);
}else{
if(this.command==null){
return;
}
var x1=line.getStartX();
var y1=line.getStartY();
var x2=line.getEndX();
var y2=line.getEndY();
this.command.setEndPoints(x1,y1,x2,y2);
this.workflow.getCommandStack().execute(this.command);
this.command=null;
}
};
LineEndResizeHandle.prototype.onDragstart=function(x,y){
if(!this.canDrag){
return false;
}
var line=this.workflow.currentSelection;
if(line instanceof Connection){
this.command=new CommandReconnect(line);
line.oldRouter=line.getRouter();
line.setRouter(new NullConnectionRouter());
}else{
var x1=line.getStartX();
var y1=line.getStartY();
var x2=line.getEndX();
var y2=line.getEndY();
this.command=new CommandMoveLine(line,x1,y1,x2,y2);
}
return true;
};
LineEndResizeHandle.prototype.onDrag=function(){
var oldX=this.getX();
var oldY=this.getY();
Rectangle.prototype.onDrag.call(this);
var diffX=oldX-this.getX();
var diffY=oldY-this.getY();
var _3f3c=this.workflow.currentSelection.getEndPoint();
var line=this.workflow.currentSelection;
line.setEndPoint(_3f3c.x-diffX,_3f3c.y-diffY);
line.isMoving=true;
};
LineEndResizeHandle.prototype.onDrop=function(_3f3e){
var line=this.workflow.currentSelection;
line.isMoving=false;
if(line instanceof Connection){
this.command.setNewPorts(line.getSource(),_3f3e);
this.getWorkflow().getCommandStack().execute(this.command);
}
this.command=null;
};
LineEndResizeHandle.prototype.onKeyDown=function(_3f40){
if(this.workflow!=null){
this.workflow.onKeyDown(_3f40);
}
};
LineEndResizeHandle.prototype.fireMoveEvent=function(){
};
Canvas=function(_3e59){
if(_3e59){
this.construct(_3e59);
}
this.enableSmoothFigureHandling=false;
this.canvasLines=new ArrayList();
};
Canvas.prototype.type="Canvas";
Canvas.prototype.construct=function(_3e5a){
this.canvasId=_3e5a;
this.html=document.getElementById(this.canvasId);
this.scrollArea=document.body.parentNode;
};
Canvas.prototype.setViewPort=function(divId){
this.scrollArea=document.getElementById(divId);
};
Canvas.prototype.addFigure=function(_3e5c,xPos,yPos,_3e5f){
if(this.enableSmoothFigureHandling==true){
if(_3e5c.timer<=0){
_3e5c.setAlpha(0.001);
}
var _3e60=_3e5c;
var _3e61=function(){
if(_3e60.alpha<1){
_3e60.setAlpha(Math.min(1,_3e60.alpha+0.05));
}else{
window.clearInterval(_3e60.timer);
_3e60.timer=-1;
}
};
if(_3e60.timer>0){
window.clearInterval(_3e60.timer);
}
_3e60.timer=window.setInterval(_3e61,30);
}
_3e5c.setCanvas(this);
if(xPos&&yPos){
_3e5c.setPosition(xPos,yPos);
}
if(_3e5c instanceof Line){
this.canvasLines.add(_3e5c);
this.html.appendChild(_3e5c.getHTMLElement());
}else{
var obj=this.canvasLines.getFirstElement();
if(obj==null){
this.html.appendChild(_3e5c.getHTMLElement());
}else{
this.html.insertBefore(_3e5c.getHTMLElement(),obj.getHTMLElement());
}
}
if(!_3e5f){
_3e5c.paint();
}
};
Canvas.prototype.removeFigure=function(_3e63){
if(this.enableSmoothFigureHandling==true){
var oThis=this;
var _3e65=_3e63;
var _3e66=function(){
if(_3e65.alpha>0){
_3e65.setAlpha(Math.max(0,_3e65.alpha-0.05));
}else{
window.clearInterval(_3e65.timer);
_3e65.timer=-1;
oThis.html.removeChild(_3e65.html);
_3e65.setCanvas(null);
}
};
if(_3e65.timer>0){
window.clearInterval(_3e65.timer);
}
_3e65.timer=window.setInterval(_3e66,20);
}else{
this.html.removeChild(_3e63.html);
_3e63.setCanvas(null);
}
if(_3e63 instanceof Line){
this.canvasLines.remove(_3e63);
}
};
Canvas.prototype.getEnableSmoothFigureHandling=function(){
return this.enableSmoothFigureHandling;
};
Canvas.prototype.setEnableSmoothFigureHandling=function(flag){
this.enableSmoothFigureHandling=flag;
};
Canvas.prototype.getWidth=function(){
return parseInt(this.html.style.width);
};
Canvas.prototype.getHeight=function(){
return parseInt(this.html.style.height);
};
Canvas.prototype.setBackgroundImage=function(_3e68,_3e69){
if(_3e68!=null){
if(_3e69){
this.html.style.background="transparent url("+_3e68+") ";
}else{
this.html.style.background="transparent url("+_3e68+") no-repeat";
}
}else{
this.html.style.background="transparent";
}
};
Canvas.prototype.getY=function(){
return this.y;
};
Canvas.prototype.getX=function(){
return this.x;
};
Canvas.prototype.getAbsoluteY=function(){
var el=this.html;
var ot=el.offsetTop;
while((el=el.offsetParent)!=null){
ot+=el.offsetTop;
}
return ot;
};
Canvas.prototype.getAbsoluteX=function(){
var el=this.html;
var ol=el.offsetLeft;
while((el=el.offsetParent)!=null){
ol+=el.offsetLeft;
}
return ol;
};
Canvas.prototype.getScrollLeft=function(){
return this.scrollArea.scrollLeft;
};
Canvas.prototype.getScrollTop=function(){
return this.scrollArea.scrollTop;
};
Workflow=function(id){
if(!id){
return;
}
this.gridWidthX=10;
this.gridWidthY=10;
this.snapToGridHelper=null;
this.verticalSnapToHelperLine=null;
this.horizontalSnapToHelperLine=null;
this.figures=new ArrayList();
this.lines=new ArrayList();
this.commonPorts=new ArrayList();
this.dropTargets=new ArrayList();
this.compartments=new ArrayList();
this.selectionListeners=new ArrayList();
this.dialogs=new ArrayList();
this.toolPalette=null;
this.dragging=false;
this.tooltip=null;
this.draggingLine=null;
this.commandStack=new CommandStack();
this.oldScrollPosLeft=0;
this.oldScrollPosTop=0;
this.currentSelection=null;
this.currentMenu=null;
this.connectionLine=new Line();
this.resizeHandleStart=new LineStartResizeHandle(this);
this.resizeHandleEnd=new LineEndResizeHandle(this);
this.resizeHandle1=new ResizeHandle(this,1);
this.resizeHandle2=new ResizeHandle(this,2);
this.resizeHandle3=new ResizeHandle(this,3);
this.resizeHandle4=new ResizeHandle(this,4);
this.resizeHandle5=new ResizeHandle(this,5);
this.resizeHandle6=new ResizeHandle(this,6);
this.resizeHandle7=new ResizeHandle(this,7);
this.resizeHandle8=new ResizeHandle(this,8);
this.resizeHandleHalfWidth=parseInt(this.resizeHandle2.getWidth()/2);
Canvas.call(this,id);
this.setPanning(false);
if(this.html!=null){
this.html.style.backgroundImage="url(grid_10.png)";
oThis=this;
this.html.tabIndex="0";
var _4116=function(){
var _4117=arguments[0]||window.event;
var diffX=_4117.clientX;
var diffY=_4117.clientY;
var _411a=oThis.getScrollLeft();
var _411b=oThis.getScrollTop();
var _411c=oThis.getAbsoluteX();
var _411d=oThis.getAbsoluteY();
if(oThis.getBestFigure(diffX+_411a-_411c,diffY+_411b-_411d)!=null){
return;
}
var line=oThis.getBestLine(diffX+_411a-_411c,diffY+_411b-_411d,null);
if(line!=null){
line.onContextMenu(diffX+_411a-_411c,diffY+_411b-_411d);
}else{
oThis.onContextMenu(diffX+_411a-_411c,diffY+_411b-_411d);
}
};
this.html.oncontextmenu=function(){
return false;
};
var oThis=this;
var _4120=function(event){
var ctrl=event.ctrlKey;
oThis.onKeyDown(event.keyCode,ctrl);
};
var _4123=function(){
var _4124=arguments[0]||window.event;
var diffX=_4124.clientX;
var diffY=_4124.clientY;
var _4127=oThis.getScrollLeft();
var _4128=oThis.getScrollTop();
var _4129=oThis.getAbsoluteX();
var _412a=oThis.getAbsoluteY();
oThis.onMouseDown(diffX+_4127-_4129,diffY+_4128-_412a);
};
var _412b=function(){
var _412c=arguments[0]||window.event;
if(oThis.currentMenu!=null){
oThis.removeFigure(oThis.currentMenu);
oThis.currentMenu=null;
}
if(_412c.button==2){
return;
}
var diffX=_412c.clientX;
var diffY=_412c.clientY;
var _412f=oThis.getScrollLeft();
var _4130=oThis.getScrollTop();
var _4131=oThis.getAbsoluteX();
var _4132=oThis.getAbsoluteY();
oThis.onMouseUp(diffX+_412f-_4131,diffY+_4130-_4132);
};
var _4133=function(){
var _4134=arguments[0]||window.event;
var diffX=_4134.clientX;
var diffY=_4134.clientY;
var _4137=oThis.getScrollLeft();
var _4138=oThis.getScrollTop();
var _4139=oThis.getAbsoluteX();
var _413a=oThis.getAbsoluteY();
oThis.currentMouseX=diffX+_4137-_4139;
oThis.currentMouseY=diffY+_4138-_413a;
var obj=oThis.getBestFigure(oThis.currentMouseX,oThis.currentMouseY);
if(Drag.currentHover!=null&&obj==null){
var _413c=new DragDropEvent();
_413c.initDragDropEvent("mouseleave",false,oThis);
Drag.currentHover.dispatchEvent(_413c);
}else{
var diffX=_4134.clientX;
var diffY=_4134.clientY;
var _4137=oThis.getScrollLeft();
var _4138=oThis.getScrollTop();
var _4139=oThis.getAbsoluteX();
var _413a=oThis.getAbsoluteY();
oThis.onMouseMove(diffX+_4137-_4139,diffY+_4138-_413a);
}
if(obj==null){
Drag.currentHover=null;
}
if(oThis.tooltip!=null){
if(Math.abs(oThis.currentTooltipX-oThis.currentMouseX)>10||Math.abs(oThis.currentTooltipY-oThis.currentMouseY)>10){
oThis.showTooltip(null);
}
}
};
var _413d=function(_413e){
var _413e=arguments[0]||window.event;
var diffX=_413e.clientX;
var diffY=_413e.clientY;
var _4141=oThis.getScrollLeft();
var _4142=oThis.getScrollTop();
var _4143=oThis.getAbsoluteX();
var _4144=oThis.getAbsoluteY();
var line=oThis.getBestLine(diffX+_4141-_4143,diffY+_4142-_4144,null);
if(line!=null){
line.onDoubleClick();
}
};
if(this.html.addEventListener){
this.html.addEventListener("contextmenu",_4116,false);
this.html.addEventListener("mousemove",_4133,false);
this.html.addEventListener("mouseup",_412b,false);
this.html.addEventListener("mousedown",_4123,false);
this.html.addEventListener("keydown",_4120,false);
this.html.addEventListener("dblclick",_413d,false);
}else{
if(this.html.attachEvent){
this.html.attachEvent("oncontextmenu",_4116);
this.html.attachEvent("onmousemove",_4133);
this.html.attachEvent("onmousedown",_4123);
this.html.attachEvent("onmouseup",_412b);
this.html.attachEvent("onkeydown",_4120);
this.html.attachEvent("ondblclick",_413d);
}else{
throw new Error("Open-jACOB Draw2D not supported in this browser.");
}
}
}
};
Workflow.prototype=new Canvas;
Workflow.prototype.type="Workflow";
Workflow.COLOR_GREEN=new Color(0,255,0);
Workflow.prototype.onScroll=function(){
var _4146=this.getScrollLeft();
var _4147=this.getScrollTop();
var _4148=_4146-this.oldScrollPosLeft;
var _4149=_4147-this.oldScrollPosTop;
for(var i=0;i<this.figures.getSize();i++){
var _414b=this.figures.get(i);
if(_414b.hasFixedPosition&&_414b.hasFixedPosition()==true){
_414b.setPosition(_414b.getX()+_4148,_414b.getY()+_4149);
}
}
this.oldScrollPosLeft=_4146;
this.oldScrollPosTop=_4147;
};
Workflow.prototype.setPanning=function(flag){
this.panning=flag;
if(flag){
this.html.style.cursor="move";
}else{
this.html.style.cursor="default";
}
};
Workflow.prototype.scrollTo=function(x,y,fast){
if(fast){
this.scrollArea.scrollLeft=x;
this.scrollArea.scrollTop=y;
}else{
var steps=40;
var xStep=(x-this.getScrollLeft())/steps;
var yStep=(y-this.getScrollTop())/steps;
var oldX=this.getScrollLeft();
var oldY=this.getScrollTop();
for(var i=0;i<steps;i++){
this.scrollArea.scrollLeft=oldX+(xStep*i);
this.scrollArea.scrollTop=oldY+(yStep*i);
}
}
};
Workflow.prototype.showTooltip=function(_4156,_4157){
if(this.tooltip!=null){
this.removeFigure(this.tooltip);
this.tooltip=null;
if(this.tooltipTimer>=0){
window.clearTimeout(this.tooltipTimer);
this.tooltipTimer=-1;
}
}
this.tooltip=_4156;
if(this.tooltip!=null){
this.currentTooltipX=this.currentMouseX;
this.currentTooltipY=this.currentMouseY;
this.addFigure(this.tooltip,this.currentTooltipX+10,this.currentTooltipY+10);
var oThis=this;
var _4159=function(){
oThis.tooltipTimer=-1;
oThis.showTooltip(null);
};
if(_4157==true){
this.tooltipTimer=window.setTimeout(_4159,5000);
}
}
};
Workflow.prototype.showDialog=function(_415a,xPos,yPos){
if(xPos){
this.addFigure(_415a,xPos,yPos);
}else{
this.addFigure(_415a,200,100);
}
this.dialogs.add(_415a);
};
Workflow.prototype.showMenu=function(menu,xPos,yPos){
if(this.menu!=null){
this.html.removeChild(this.menu.getHTMLElement());
this.menu.setWorkflow();
}
this.menu=menu;
if(this.menu!=null){
this.menu.setWorkflow(this);
this.menu.setPosition(xPos,yPos);
this.html.appendChild(this.menu.getHTMLElement());
this.menu.paint();
}
};
Workflow.prototype.onContextMenu=function(x,y){
var menu=this.getContextMenu();
if(menu!=null){
this.showMenu(menu,x,y);
}
};
Workflow.prototype.getContextMenu=function(){
return null;
};
Workflow.prototype.setToolWindow=function(_4163,x,y){
this.toolPalette=_4163;
if(y){
this.addFigure(_4163,x,y);
}else{
this.addFigure(_4163,20,20);
}
this.dialogs.add(_4163);
};
Workflow.prototype.setSnapToGrid=function(flag){
if(flag){
this.snapToGridHelper=new SnapToGrid(this);
}else{
this.snapToGridHelper=null;
}
};
Workflow.prototype.setSnapToGeometry=function(flag){
if(flag){
this.snapToGeometryHelper=new SnapToGeometry(this);
}else{
this.snapToGeometryHelper=null;
}
};
Workflow.prototype.setGridWidth=function(dx,dy){
this.gridWidthX=dx;
this.gridWidthY=dy;
};
Workflow.prototype.addFigure=function(_416a,xPos,yPos){
Canvas.prototype.addFigure.call(this,_416a,xPos,yPos,true);
_416a.setWorkflow(this);
var _416d=this;
if(_416a instanceof CompartmentFigure){
this.compartments.add(_416a);
}
if(_416a instanceof Line){
this.lines.add(_416a);
}else{
this.figures.add(_416a);
_416a.draggable.addEventListener("dragend",function(_416e){
});
_416a.draggable.addEventListener("dragstart",function(_416f){
var _4170=_416d.getFigure(_416f.target.element.id);
if(_4170==null){
return;
}
if(_4170.isSelectable()==false){
return;
}
_416d.showResizeHandles(_4170);
_416d.setCurrentSelection(_4170);
});
_416a.draggable.addEventListener("drag",function(_4171){
var _4172=_416d.getFigure(_4171.target.element.id);
if(_4172==null){
return;
}
if(_4172.isSelectable()==false){
return;
}
_416d.moveResizeHandles(_4172);
});
}
_416a.paint();
this.setDocumentDirty();
};
Workflow.prototype.removeFigure=function(_4173){
Canvas.prototype.removeFigure.call(this,_4173);
this.figures.remove(_4173);
this.lines.remove(_4173);
this.dialogs.remove(_4173);
_4173.setWorkflow(null);
if(_4173 instanceof CompartmentFigure){
this.compartments.remove(_4173);
}
if(_4173 instanceof Connection){
_4173.disconnect();
}
if(this.currentSelection==_4173){
this.setCurrentSelection(null);
}
this.setDocumentDirty();
};
Workflow.prototype.moveFront=function(_4174){
this.html.removeChild(_4174.getHTMLElement());
this.html.appendChild(_4174.getHTMLElement());
};
Workflow.prototype.moveBack=function(_4175){
this.html.removeChild(_4175.getHTMLElement());
this.html.insertBefore(_4175.getHTMLElement(),this.html.firstChild);
};
Workflow.prototype.getBestCompartmentFigure=function(x,y,_4178){
var _4179=null;
for(var i=0;i<this.figures.getSize();i++){
var _417b=this.figures.get(i);
if((_417b instanceof CompartmentFigure)&&_417b.isOver(x,y)==true&&_417b!=_4178){
if(_4179==null){
_4179=_417b;
}else{
if(_4179.getZOrder()<_417b.getZOrder()){
_4179=_417b;
}
}
}
}
return _4179;
};
Workflow.prototype.getBestFigure=function(x,y,_417e){
var _417f=null;
for(var i=0;i<this.figures.getSize();i++){
var _4181=this.figures.get(i);
if(_4181.isOver(x,y)==true&&_4181!=_417e){
if(_417f==null){
_417f=_4181;
}else{
if(_417f.getZOrder()<_4181.getZOrder()){
_417f=_4181;
}
}
}
}
return _417f;
};
Workflow.prototype.getBestLine=function(x,y,_4184){
var _4185=null;
for(var i=0;i<this.lines.getSize();i++){
var line=this.lines.get(i);
if(line.containsPoint(x,y)==true&&line!=_4184){
if(_4185==null){
_4185=line;
}else{
if(_4185.getZOrder()<line.getZOrder()){
_4185=line;
}
}
}
}
return _4185;
};
Workflow.prototype.getFigure=function(id){
for(var i=0;i<this.figures.getSize();i++){
var _418a=this.figures.get(i);
if(_418a.id==id){
return _418a;
}
}
return null;
};
Workflow.prototype.getFigures=function(){
return this.figures;
};
Workflow.prototype.getDocument=function(){
return new Document(this);
};
Workflow.prototype.addSelectionListener=function(w){
this.selectionListeners.add(w);
};
Workflow.prototype.removeSelectionListener=function(w){
this.selectionListeners.remove(w);
};
Workflow.prototype.setCurrentSelection=function(_418d){
if(_418d==null){
this.hideResizeHandles();
this.hideLineResizeHandles();
}
this.currentSelection=_418d;
for(var i=0;i<this.selectionListeners.getSize();i++){
var w=this.selectionListeners.get(i);
if(w!=null&&w.onSelectionChanged){
w.onSelectionChanged(this.currentSelection);
}
}
};
Workflow.prototype.getCurrentSelection=function(){
return this.currentSelection;
};
Workflow.prototype.getLines=function(){
return this.lines;
};
Workflow.prototype.registerPort=function(port){
port.draggable.targets=this.dropTargets;
this.commonPorts.add(port);
this.dropTargets.add(port.dropable);
};
Workflow.prototype.unregisterPort=function(port){
port.draggable.targets=null;
this.commonPorts.remove(port);
this.dropTargets.remove(port.dropable);
};
Workflow.prototype.getCommandStack=function(){
return this.commandStack;
};
Workflow.prototype.showConnectionLine=function(x1,y1,x2,y2){
this.connectionLine.setStartPoint(x1,y1);
this.connectionLine.setEndPoint(x2,y2);
if(this.connectionLine.canvas==null){
Canvas.prototype.addFigure.call(this,this.connectionLine);
}
};
Workflow.prototype.hideConnectionLine=function(){
if(this.connectionLine.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.connectionLine);
}
};
Workflow.prototype.showLineResizeHandles=function(_4196){
var _4197=this.resizeHandleStart.getWidth()/2;
var _4198=this.resizeHandleStart.getHeight()/2;
var _4199=_4196.getStartPoint();
var _419a=_4196.getEndPoint();
Canvas.prototype.addFigure.call(this,this.resizeHandleStart,_4199.x-_4197,_4199.y-_4197);
Canvas.prototype.addFigure.call(this,this.resizeHandleEnd,_419a.x-_4197,_419a.y-_4197);
this.resizeHandleStart.setCanDrag(_4196.isResizeable());
this.resizeHandleEnd.setCanDrag(_4196.isResizeable());
if(_4196.isResizeable()){
this.resizeHandleStart.setBackgroundColor(Workflow.COLOR_GREEN);
this.resizeHandleEnd.setBackgroundColor(Workflow.COLOR_GREEN);
this.resizeHandleStart.draggable.targets=this.dropTargets;
this.resizeHandleEnd.draggable.targets=this.dropTargets;
}else{
this.resizeHandleStart.setBackgroundColor(null);
this.resizeHandleEnd.setBackgroundColor(null);
}
};
Workflow.prototype.hideLineResizeHandles=function(){
if(this.resizeHandleStart.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandleStart);
}
if(this.resizeHandleEnd.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandleEnd);
}
};
Workflow.prototype.showResizeHandles=function(_419b){
this.hideLineResizeHandles();
this.hideResizeHandles();
if(this.getEnableSmoothFigureHandling()==true&&this.getCurrentSelection()!=_419b){
this.resizeHandle1.setAlpha(0.01);
this.resizeHandle2.setAlpha(0.01);
this.resizeHandle3.setAlpha(0.01);
this.resizeHandle4.setAlpha(0.01);
this.resizeHandle5.setAlpha(0.01);
this.resizeHandle6.setAlpha(0.01);
this.resizeHandle7.setAlpha(0.01);
this.resizeHandle8.setAlpha(0.01);
}
var _419c=this.resizeHandle1.getWidth();
var _419d=this.resizeHandle1.getHeight();
var _419e=_419b.getHeight();
var _419f=_419b.getWidth();
var xPos=_419b.getX();
var yPos=_419b.getY();
Canvas.prototype.addFigure.call(this,this.resizeHandle1,xPos-_419c,yPos-_419d);
Canvas.prototype.addFigure.call(this,this.resizeHandle3,xPos+_419f,yPos-_419d);
Canvas.prototype.addFigure.call(this,this.resizeHandle5,xPos+_419f,yPos+_419e);
Canvas.prototype.addFigure.call(this,this.resizeHandle7,xPos-_419c,yPos+_419e);
this.moveFront(this.resizeHandle1);
this.moveFront(this.resizeHandle3);
this.moveFront(this.resizeHandle5);
this.moveFront(this.resizeHandle7);
this.resizeHandle1.setCanDrag(_419b.isResizeable());
this.resizeHandle3.setCanDrag(_419b.isResizeable());
this.resizeHandle5.setCanDrag(_419b.isResizeable());
this.resizeHandle7.setCanDrag(_419b.isResizeable());
if(_419b.isResizeable()){
var green=new Color(0,255,0);
this.resizeHandle1.setBackgroundColor(green);
this.resizeHandle3.setBackgroundColor(green);
this.resizeHandle5.setBackgroundColor(green);
this.resizeHandle7.setBackgroundColor(green);
}else{
this.resizeHandle1.setBackgroundColor(null);
this.resizeHandle3.setBackgroundColor(null);
this.resizeHandle5.setBackgroundColor(null);
this.resizeHandle7.setBackgroundColor(null);
}
if(_419b.isStrechable()&&_419b.isResizeable()){
this.resizeHandle2.setCanDrag(_419b.isResizeable());
this.resizeHandle4.setCanDrag(_419b.isResizeable());
this.resizeHandle6.setCanDrag(_419b.isResizeable());
this.resizeHandle8.setCanDrag(_419b.isResizeable());
Canvas.prototype.addFigure.call(this,this.resizeHandle2,xPos+(_419f/2)-this.resizeHandleHalfWidth+1,yPos-_419d-6);
Canvas.prototype.addFigure.call(this,this.resizeHandle4,xPos+_419f+6,yPos+(_419e/2)-(_419d/2) );
Canvas.prototype.addFigure.call(this,this.resizeHandle6,xPos+(_419f/2)-this.resizeHandleHalfWidth+1,yPos+_419e+6);
Canvas.prototype.addFigure.call(this,this.resizeHandle8,xPos-_419c-5,yPos+(_419e/2)-(_419d/2) );
this.moveFront(this.resizeHandle2);
this.moveFront(this.resizeHandle4);
this.moveFront(this.resizeHandle6);
this.moveFront(this.resizeHandle8);
}
};
Workflow.prototype.hideResizeHandles=function(){
if(this.resizeHandle1.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle1);
}
if(this.resizeHandle2.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle2);
}
if(this.resizeHandle3.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle3);
}
if(this.resizeHandle4.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle4);
}
if(this.resizeHandle5.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle5);
}
if(this.resizeHandle6.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle6);
}
if(this.resizeHandle7.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle7);
}
if(this.resizeHandle8.canvas!=null){
Canvas.prototype.removeFigure.call(this,this.resizeHandle8);
}
};
Workflow.prototype.moveResizeHandles=function(_41a3){
var _41a4=this.resizeHandle1.getWidth();
var _41a5=this.resizeHandle1.getHeight();
var _41a6=_41a3.getHeight();
var _41a7=_41a3.getWidth();
var xPos=_41a3.getX();
var yPos=_41a3.getY();
this.resizeHandle1.setPosition(xPos-_41a4,yPos-_41a5);
this.resizeHandle3.setPosition(xPos+_41a7,yPos-_41a5);
this.resizeHandle5.setPosition(xPos+_41a7,yPos+_41a6);
this.resizeHandle7.setPosition(xPos-_41a4,yPos+_41a6);
if(_41a3.isStrechable()){
this.resizeHandle2.setPosition(xPos+(_41a7/2)-this.resizeHandleHalfWidth,yPos-_41a5);
this.resizeHandle4.setPosition(xPos+_41a7,yPos+(_41a6/2)-(_41a5/2));
this.resizeHandle6.setPosition(xPos+(_41a7/2)-this.resizeHandleHalfWidth,yPos+_41a6);
this.resizeHandle8.setPosition(xPos-_41a4,yPos+(_41a6/2)-(_41a5/2));
}
};
Workflow.prototype.onMouseDown=function(x,y){
this.dragging=true;
this.mouseDownPosX=x;
this.mouseDownPosY=y;
if(this.toolPalette!=null&&this.toolPalette.getActiveTool()!=null){
this.toolPalette.getActiveTool().execute(x,y);
}
this.setCurrentSelection(null);
this.showMenu(null);
var size=this.getLines().getSize();
for(var i=0;i<size;i++){
var line=this.lines.get(i);
if(line.containsPoint(x,y)&&line.isSelectable()){
this.hideResizeHandles();
this.setCurrentSelection(line);
this.showLineResizeHandles(this.currentSelection);
if(line instanceof Line&&!(line instanceof Connection)){
this.draggingLine=line;
}
break;
}
}
};
Workflow.prototype.onMouseUp=function(x,y){
this.dragging=false;
this.draggingLine=null;
};
Workflow.prototype.onMouseMove=function(x,y){
if(this.dragging==true&&this.draggingLine!=null){
var diffX=x-this.mouseDownPosX;
var diffY=y-this.mouseDownPosY;
this.draggingLine.startX=this.draggingLine.getStartX()+diffX;
this.draggingLine.startY=this.draggingLine.getStartY()+diffY;
this.draggingLine.setEndPoint(this.draggingLine.getEndX()+diffX,this.draggingLine.getEndY()+diffY);
this.mouseDownPosX=x;
this.mouseDownPosY=y;
this.showLineResizeHandles(this.currentSelection);
}else{
if(this.dragging==true&&this.panning==true){
var diffX=x-this.mouseDownPosX;
var diffY=y-this.mouseDownPosY;
this.scrollTo(this.getScrollLeft()-diffX,this.getScrollTop()-diffY,true);
this.onScroll();
}
}
};
Workflow.prototype.onKeyDown=function(_41b5,ctrl){
if(_41b5==46&&this.currentSelection!=null&&this.currentSelection.isDeleteable()){
this.commandStack.execute(new CommandDelete(this.currentSelection));
}else{
if(_41b5==90&&ctrl){
this.commandStack.undo();
}else{
if(_41b5==89&&ctrl){
this.commandStack.redo();
}
}
}
};
Workflow.prototype.setDocumentDirty=function(){
for(var i=0;i<this.dialogs.getSize();i++){
var d=this.dialogs.get(i);
if(d!=null&&d.onSetDocumentDirty){
d.onSetDocumentDirty();
}
}
if(this.snapToGeometryHelper!=null){
this.snapToGeometryHelper.onSetDocumentDirty();
}
if(this.snapToGridHelper!=null){
this.snapToGridHelper.onSetDocumentDirty();
}
};
Workflow.prototype.snapToHelper=function(_41b9,pos){
if(this.snapToGeometryHelper!=null){
if(_41b9 instanceof ResizeHandle){
var _41bb=_41b9.getSnapToGridAnchor();
pos.x+=_41bb.x;
pos.y+=_41bb.y;
var _41bc=new Point(pos.x,pos.y);
var _41bd=_41b9.getSnapToDirection();
var _41be=this.snapToGeometryHelper.snapPoint(_41bd,pos,_41bc);
if((_41bd&SnapToHelper.EAST_WEST)&&!(_41be&SnapToHelper.EAST_WEST)){
this.showSnapToHelperLineVertical(_41bc.x);
}else{
this.hideSnapToHelperLineVertical();
}
if((_41bd&SnapToHelper.NORTH_SOUTH)&&!(_41be&SnapToHelper.NORTH_SOUTH)){
this.showSnapToHelperLineHorizontal(_41bc.y);
}else{
this.hideSnapToHelperLineHorizontal();
}
_41bc.x-=_41bb.x;
_41bc.y-=_41bb.y;
return _41bc;
}else{
var _41bf=new Dimension(pos.x,pos.y,_41b9.getWidth(),_41b9.getHeight());
var _41bc=new Dimension(pos.x,pos.y,_41b9.getWidth(),_41b9.getHeight());
var _41bd=SnapToHelper.NSEW;
var _41be=this.snapToGeometryHelper.snapRectangle(_41bf,_41bc);
if((_41bd&SnapToHelper.WEST)&&!(_41be&SnapToHelper.WEST)){
this.showSnapToHelperLineVertical(_41bc.x);
}else{
if((_41bd&SnapToHelper.EAST)&&!(_41be&SnapToHelper.EAST)){
this.showSnapToHelperLineVertical(_41bc.getX()+_41bc.getWidth());
}else{
this.hideSnapToHelperLineVertical();
}
}
if((_41bd&SnapToHelper.NORTH)&&!(_41be&SnapToHelper.NORTH)){
this.showSnapToHelperLineHorizontal(_41bc.y);
}else{
if((_41bd&SnapToHelper.SOUTH)&&!(_41be&SnapToHelper.SOUTH)){
this.showSnapToHelperLineHorizontal(_41bc.getY()+_41bc.getHeight());
}else{
this.hideSnapToHelperLineHorizontal();
}
}
return _41bc.getTopLeft();
}
}else{
if(this.snapToGridHelper!=null){
var _41bb=_41b9.getSnapToGridAnchor();
pos.x=pos.x+_41bb.x;
pos.y=pos.y+_41bb.y;
var _41bc=new Point(pos.x,pos.y);
this.snapToGridHelper.snapPoint(0,pos,_41bc);
_41bc.x=_41bc.x-_41bb.x;
_41bc.y=_41bc.y-_41bb.y;
return _41bc;
}
}
return pos;
};
Workflow.prototype.showSnapToHelperLineHorizontal=function(_41c0){
if(this.horizontalSnapToHelperLine==null){
this.horizontalSnapToHelperLine=new Line();
this.horizontalSnapToHelperLine.setColor(new Color(175,175,255));
this.addFigure(this.horizontalSnapToHelperLine);
}
this.horizontalSnapToHelperLine.setStartPoint(0,_41c0);
this.horizontalSnapToHelperLine.setEndPoint(this.getWidth(),_41c0);
};
Workflow.prototype.showSnapToHelperLineVertical=function(_41c1){
if(this.verticalSnapToHelperLine==null){
this.verticalSnapToHelperLine=new Line();
this.verticalSnapToHelperLine.setColor(new Color(175,175,255));
this.addFigure(this.verticalSnapToHelperLine);
}
this.verticalSnapToHelperLine.setStartPoint(_41c1,0);
this.verticalSnapToHelperLine.setEndPoint(_41c1,this.getHeight());
};
Workflow.prototype.hideSnapToHelperLines=function(){
this.hideSnapToHelperLineHorizontal();
this.hideSnapToHelperLineVertical();
};
Workflow.prototype.hideSnapToHelperLineHorizontal=function(){
if(this.horizontalSnapToHelperLine!=null){
this.removeFigure(this.horizontalSnapToHelperLine);
this.horizontalSnapToHelperLine=null;
}
};
Workflow.prototype.hideSnapToHelperLineVertical=function(){
if(this.verticalSnapToHelperLine!=null){
this.removeFigure(this.verticalSnapToHelperLine);
this.verticalSnapToHelperLine=null;
}
};
Window=function(title){
this.title=title;
this.titlebar=null;
Figure.call(this);
this.setDeleteable(false);
this.setCanSnapToHelper(false);
this.setZOrder(Window.ZOrderIndex);
};
Window.prototype=new Figure;
Window.prototype.type="Window";
Window.ZOrderIndex=50000;
Window.setZOrderBaseIndex=function(index){
Window.ZOrderBaseIndex=index;
};
Window.prototype.hasFixedPosition=function(){
return true;
};
Window.prototype.hasTitleBar=function(){
return true;
};
Window.prototype.createHTMLElement=function(){
var item=Figure.prototype.createHTMLElement.call(this);
item.style.margin="0px";
item.style.padding="0px";
item.style.border="1px solid black";
item.style.backgroundImage="url(/skins/ext/images/gray/shapes/window_bg.png)";
//item.style.zIndex=Window.ZOrderBaseIndex;
item.style.cursor=null;
if(this.hasTitleBar()){
this.titlebar=document.createElement("div");
this.titlebar.style.position="absolute";
this.titlebar.style.left="0px";
this.titlebar.style.top="0px";
this.titlebar.style.width=this.getWidth()+"px";
this.titlebar.style.height="15px";
this.titlebar.style.margin="0px";
this.titlebar.style.padding="0px";
this.titlebar.style.font="normal 10px verdana";
this.titlebar.style.backgroundColor="blue";
this.titlebar.style.borderBottom="2px solid gray";
this.titlebar.style.whiteSpace="nowrap";
this.titlebar.style.textAlign="center";
this.titlebar.style.backgroundImage="url(/skins/ext/images/gray/shapes/window_toolbar.png)";
this.textNode=document.createTextNode(this.title);
this.titlebar.appendChild(this.textNode);
item.appendChild(this.titlebar);
}
return item;
};
Window.prototype.setDocumentDirty=function(_3b99){
};
Window.prototype.onDragend=function(){
};
Window.prototype.onDragstart=function(x,y){
if(this.titlebar==null){
return false;
}
if(this.canDrag==true&&x<parseInt(this.titlebar.style.width)&&y<parseInt(this.titlebar.style.height)){
return true;
}
return false;
};
Window.prototype.isSelectable=function(){
return false;
};
Window.prototype.setCanDrag=function(flag){
Figure.prototype.setCanDrag.call(this,flag);
this.html.style.cursor="";
if(this.titlebar==null){
return;
}
if(flag){
this.titlebar.style.cursor="move";
}else{
this.titlebar.style.cursor="";
}
};
Window.prototype.setWorkflow=function(_3b9d){
var _3b9e=this.workflow;
Figure.prototype.setWorkflow.call(this,_3b9d);
if(_3b9e!=null){
_3b9e.removeSelectionListener(this);
}
if(this.workflow!=null){
this.workflow.addSelectionListener(this);
}
};
Window.prototype.setDimension=function(w,h){
Figure.prototype.setDimension.call(this,w,h);
if(this.titlebar!=null){
this.titlebar.style.width=this.getWidth()+"px";
}
};
Window.prototype.setTitle=function(title){
this.title=title;
};
Window.prototype.getMinWidth=function(){
return 50;
};
Window.prototype.getMinHeight=function(){
return 50;
};
Window.prototype.isResizeable=function(){
return false;
};
Window.prototype.setAlpha=function(_3ba2){
};
Window.prototype.setBackgroundColor=function(color){
this.bgColor=color;
if(this.bgColor!=null){
this.html.style.backgroundColor=this.bgColor.getHTMLStyle();
}else{
this.html.style.backgroundColor="transparent";
this.html.style.backgroundImage="";
}
};
Window.prototype.setColor=function(color){
this.lineColor=color;
if(this.lineColor!=null){
this.html.style.border=this.lineStroke+"px solid "+this.lineColor.getHTMLStyle();
}else{
this.html.style.border="0px";
}
};
Window.prototype.setLineWidth=function(w){
this.lineStroke=w;
this.html.style.border=this.lineStroke+"px solid black";
};
Window.prototype.onSelectionChanged=function(_3ba6){
};
Button=function(_40ad,width,_40af){
this.x=0;
this.y=0;
this.id=this.generateUId();
this.enabled=true;
this.active=false;
this.palette=_40ad;
if(width&&_40af){
this.setDimension(width,_40af);
}else{
this.setDimension(24,24);
}
this.html=this.createHTMLElement();
};
Button.prototype.type="Button";
Button.prototype.dispose=function(){
};
Button.prototype.getImageUrl=function(){
if(this.enabled){
return this.type+".png";
}else{
return this.type+"_disabled.png";
}
};
Button.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.height=this.width+"px";
item.style.width=this.height+"px";
item.style.margin="0px";
item.style.padding="0px";
item.style.outline="none";
if(this.getImageUrl()!=null){
item.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
item.style.backgroundImage="";
}
var oThis=this;
this.omousedown=function(event){
if(oThis.enabled){
oThis.setActive(true);
}
event.cancelBubble=true;
event.returnValue=false;
};
this.omouseup=function(event){
if(oThis.enabled){
oThis.setActive(false);
oThis.execute();
}
event.cancelBubble=true;
event.returnValue=false;
};
if(item.addEventListener){
item.addEventListener("mousedown",this.omousedown,false);
item.addEventListener("mouseup",this.omouseup,false);
}else{
if(item.attachEvent){
item.attachEvent("onmousedown",this.omousedown);
item.attachEvent("onmouseup",this.omouseup);
}
}
return item;
};
Button.prototype.getHTMLElement=function(){
if(this.html==null){
this.html=this.createHTMLElement();
}
return this.html;
};
Button.prototype.execute=function(){
};
Button.prototype.setTooltip=function(_40b4){
this.tooltip=_40b4;
if(this.tooltip!=null){
this.html.title=this.tooltip;
}else{
this.html.title="";
}
};
Button.prototype.setActive=function(flag){
if(!this.enabled){
return;
}
this.active=flag;
if(flag==true){
this.html.style.border="2px inset";
}else{
this.html.style.border="0px";
}
};
Button.prototype.isActive=function(){
return this.active;
};
Button.prototype.setEnabled=function(flag){
this.enabled=flag;
if(this.getImageUrl()!=null){
this.html.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
this.html.style.backgroundImage="";
}
};
Button.prototype.setDimension=function(w,h){
this.width=w;
this.height=h;
if(this.html==null){
return;
}
this.html.style.width=this.width+"px";
this.html.style.height=this.height+"px";
};
Button.prototype.setPosition=function(xPos,yPos){
this.x=Math.max(0,xPos);
this.y=Math.max(0,yPos);
if(this.html==null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
};
Button.prototype.getWidth=function(){
return this.width;
};
Button.prototype.getHeight=function(){
return this.height;
};
Button.prototype.getY=function(){
return this.y;
};
Button.prototype.getX=function(){
return this.x;
};
Button.prototype.getPosition=function(){
return new Point(this.x,this.y);
};
Button.prototype.getToolPalette=function(){
return this.palette;
};
Button.prototype.generateUId=function(){
var chars="0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
var _40bc=10;
var _40bd=10;
nbTry=0;
while(nbTry<1000){
var id="";
for(var i=0;i<_40bc;i++){
var rnum=Math.floor(Math.random()*chars.length);
id+=chars.substring(rnum,rnum+1);
}
elem=document.getElementById(id);
if(!elem){
return id;
}
nbTry+=1;
}
return null;
};
ToggleButton=function(_3e78){
Button.call(this,_3e78);
this.isDownFlag=false;
};
ToggleButton.prototype=new Button;
ToggleButton.prototype.type="ToggleButton";
ToggleButton.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.height="24px";
item.style.width="24px";
item.style.margin="0px";
item.style.padding="0px";
if(this.getImageUrl()!=null){
item.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
item.style.backgroundImage="";
}
var oThis=this;
this.omousedown=function(event){
if(oThis.enabled){
if(!oThis.isDown()){
Button.prototype.setActive.call(oThis,true);
}
}
event.cancelBubble=true;
event.returnValue=false;
};
this.omouseup=function(event){
if(oThis.enabled){
if(oThis.isDown()){
Button.prototype.setActive.call(oThis,false);
}
oThis.isDownFlag=!oThis.isDownFlag;
oThis.execute();
}
event.cancelBubble=true;
event.returnValue=false;
};
if(item.addEventListener){
item.addEventListener("mousedown",this.omousedown,false);
item.addEventListener("mouseup",this.omouseup,false);
}else{
if(item.attachEvent){
item.attachEvent("onmousedown",this.omousedown);
item.attachEvent("onmouseup",this.omouseup);
}
}
return item;
};
ToggleButton.prototype.isDown=function(){
return this.isDownFlag;
};
ToggleButton.prototype.setActive=function(flag){
Button.prototype.setActive.call(this,flag);
this.isDownFlag=flag;
};
ToggleButton.prototype.execute=function(){
};
ToolGeneric=function(_39ec){
this.x=0;
this.y=0;
this.enabled=true;
this.tooltip=null;
this.palette=_39ec;
this.setDimension(10,10);
this.html=this.createHTMLElement();
};
ToolGeneric.prototype.type="ToolGeneric";
ToolGeneric.prototype.dispose=function(){
};
ToolGeneric.prototype.getImageUrl=function(){
if(this.enabled){
return this.type+".png";
}else{
return this.type+"_disabled.png";
}
};
ToolGeneric.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.id=this.id;
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.height="24px";
item.style.width="24px";
item.style.margin="0px";
item.style.padding="0px";
if(this.getImageUrl()!=null){
item.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
item.style.backgroundImage="";
}
var oThis=this;
this.click=function(event){
if(oThis.enabled){
oThis.palette.setActiveTool(oThis);
}
event.cancelBubble=true;
event.returnValue=false;
};
if(item.addEventListener){
item.addEventListener("click",this.click,false);
}else{
if(item.attachEvent){
item.attachEvent("onclick",this.click);
}
}
return item;
};
ToolGeneric.prototype.getHTMLElement=function(){
if(this.html==null){
this.html=this.createHTMLElement();
}
return this.html;
};
ToolGeneric.prototype.execute=function(x,y){
if(this.enabled){
this.palette.setActiveTool(null);
}
};
ToolGeneric.prototype.setTooltip=function(_39f2){
this.tooltip=_39f2;
if(this.tooltip!=null){
this.html.title=this.tooltip;
}else{
this.html.title="";
}
};
ToolGeneric.prototype.setActive=function(flag){
if(!this.enabled){
return;
}
if(flag==true){
this.html.style.border="2px inset";
}else{
this.html.style.border="0px";
}
};
ToolGeneric.prototype.setEnabled=function(flag){
this.enabled=flag;
if(this.getImageUrl()!=null){
this.html.style.backgroundImage="url("+this.getImageUrl()+")";
}else{
this.html.style.backgroundImage="";
}
};
ToolGeneric.prototype.setDimension=function(w,h){
this.width=w;
this.height=h;
if(this.html==null){
return;
}
this.html.style.width=this.width+"px";
this.html.style.height=this.height+"px";
};
ToolGeneric.prototype.setPosition=function(xPos,yPos){
this.x=Math.max(0,xPos);
this.y=Math.max(0,yPos);
if(this.html==null){
return;
}
this.html.style.left=this.x+"px";
this.html.style.top=this.y+"px";
};
ToolGeneric.prototype.getWidth=function(){
return this.width;
};
ToolGeneric.prototype.getHeight=function(){
return this.height;
};
ToolGeneric.prototype.getY=function(){
return this.y;
};
ToolGeneric.prototype.getX=function(){
return this.x;
};
ToolGeneric.prototype.getPosition=function(){
return new Point(this.x,this.y);
};
ToolPalette=function(title){
Window.call(this,title);
this.setDimension(75,400);
this.activeTool=null;
this.children=new Object();
};
ToolPalette.prototype=new Window;
ToolPalette.prototype.type="ToolPalette";
ToolPalette.prototype.dispose=function(){
Window.prototype.dispose.call(this);
};
ToolPalette.prototype.createHTMLElement=function(){
var item=Window.prototype.createHTMLElement.call(this);
this.scrollarea=document.createElement("div");
this.scrollarea.style.position="absolute";
this.scrollarea.style.left="0px";
if(this.hasTitleBar()){
this.scrollarea.style.top="15px";
}else{
this.scrollarea.style.top="0px";
}
this.scrollarea.style.width=this.getWidth()+"px";
this.scrollarea.style.height="15px";
this.scrollarea.style.margin="0px";
this.scrollarea.style.padding="0px";
this.scrollarea.style.font="normal 10px verdana";
this.scrollarea.style.borderBottom="2px solid gray";
this.scrollarea.style.whiteSpace="nowrap";
this.scrollarea.style.textAlign="center";
this.scrollarea.style.overflowX="auto";
this.scrollarea.style.overflowY="auto";
this.scrollarea.style.overflow="auto";
item.appendChild(this.scrollarea);
return item;
};
ToolPalette.prototype.setDimension=function(w,h){
Window.prototype.setDimension.call(this,w,h);
if(this.scrollarea!=null){
this.scrollarea.style.width=this.getWidth()+"px";
if(this.hasTitleBar()){
this.scrollarea.style.height=(this.getHeight()-15)+"px";
}else{
this.scrollarea.style.height=this.getHeight()+"px";
}
}
};
ToolPalette.prototype.addChild=function(item){
this.children[item.id]=item;
this.scrollarea.appendChild(item.getHTMLElement());
};
ToolPalette.prototype.getChild=function(id){
return this.children[id];
};
ToolPalette.prototype.getActiveTool=function(){
return this.activeTool;
};
ToolPalette.prototype.setActiveTool=function(tool){
if(this.activeTool!=tool&&this.activeTool!=null){
this.activeTool.setActive(false);
}
if(tool!=null){
tool.setActive(true);
}
this.activeTool=tool;
};
Dialog=function(title){
this.buttonbar=null;
if(title){
Window.call(this,title);
}else{
Window.call(this,"Dialog");
}
this.setDimension(400,300);
};
Dialog.prototype=new Window;
Dialog.prototype.type="Dialog";
Dialog.prototype.createHTMLElement=function(){
var item=Window.prototype.createHTMLElement.call(this);
var oThis=this;
this.buttonbar=document.createElement("div");
this.buttonbar.style.position="absolute";
this.buttonbar.style.left="0px";
this.buttonbar.style.bottom="0px";
this.buttonbar.style.width=this.getWidth()+"px";
this.buttonbar.style.height="30px";
this.buttonbar.style.margin="0px";
this.buttonbar.style.padding="0px";
this.buttonbar.style.font="normal 10px verdana";
this.buttonbar.style.backgroundColor="#C0C0C0";
this.buttonbar.style.borderBottom="2px solid gray";
this.buttonbar.style.whiteSpace="nowrap";
this.buttonbar.style.textAlign="center";
this.okbutton=document.createElement("button");
this.okbutton.style.border="1px solid gray";
this.okbutton.style.font="normal 10px verdana";
this.okbutton.style.width="80px";
this.okbutton.style.margin="5px";
this.okbutton.innerHTML="Ok";
this.okbutton.onclick=function(){
oThis.onOk();
};
this.buttonbar.appendChild(this.okbutton);
this.cancelbutton=document.createElement("button");
this.cancelbutton.innerHTML="Cancel";
this.cancelbutton.style.font="normal 10px verdana";
this.cancelbutton.style.border="1px solid gray";
this.cancelbutton.style.width="80px";
this.cancelbutton.style.margin="5px";
this.cancelbutton.onclick=function(){
oThis.onCancel();
};
this.buttonbar.appendChild(this.cancelbutton);
item.appendChild(this.buttonbar);
return item;
};
Dialog.prototype.onOk=function(){
this.workflow.removeFigure(this);
};
Dialog.prototype.onCancel=function(){
this.workflow.removeFigure(this);
};
Dialog.prototype.setDimension=function(w,h){
Window.prototype.setDimension.call(this,w,h);
if(this.buttonbar!=null){
this.buttonbar.style.width=this.getWidth()+"px";
}
};
Dialog.prototype.setWorkflow=function(_31c1){
Window.prototype.setWorkflow.call(this,_31c1);
this.setFocus();
};
Dialog.prototype.setFocus=function(){
};
Dialog.prototype.onSetDocumentDirty=function(){
};
InputDialog=function(){
Dialog.call(this);
this.setDimension(400,100);
};
InputDialog.prototype=new Dialog;
InputDialog.prototype.type="InputDialog";
InputDialog.prototype.createHTMLElement=function(){
var item=Dialog.prototype.createHTMLElement.call(this);
return item;
};
InputDialog.prototype.onOk=function(){
this.workflow.removeFigure(this);
};
InputDialog.prototype.onCancel=function(){
this.workflow.removeFigure(this);
};
PropertyDialog=function(_3f50,_3f51,label){
this.figure=_3f50;
this.propertyName=_3f51;
this.label=label;
Dialog.call(this);
this.setDimension(400,120);
};
PropertyDialog.prototype=new Dialog;
PropertyDialog.prototype.type="PropertyDialog";
PropertyDialog.prototype.createHTMLElement=function(){
var item=Dialog.prototype.createHTMLElement.call(this);
var _3f54=document.createElement("form");
_3f54.style.position="absolute";
_3f54.style.left="10px";
_3f54.style.top="30px";
_3f54.style.width="375px";
_3f54.style.font="normal 10px verdana";
item.appendChild(_3f54);
this.labelDiv=document.createElement("div");
this.labelDiv.innerHTML=this.label;
this.disableTextSelection(this.labelDiv);
_3f54.appendChild(this.labelDiv);
this.input=document.createElement("input");
this.input.style.border="1px solid gray";
this.input.style.font="normal 10px verdana";
this.input.type="text";
var value=this.figure.getProperty(this.propertyName);
if(value){
this.input.value=value;
}else{
this.input.value="";
}
this.input.style.width="100%";
_3f54.appendChild(this.input);
this.input.focus();
return item;
};
PropertyDialog.prototype.onOk=function(){
Dialog.prototype.onOk.call(this);
this.figure.setProperty(this.propertyName,this.input.value);
};
AnnotationDialog=function(_2e5e){
this.figure=_2e5e;
Dialog.call(this);
this.setDimension(400,100);
};
AnnotationDialog.prototype=new Dialog;
AnnotationDialog.prototype.type="AnnotationDialog";
AnnotationDialog.prototype.createHTMLElement=function(){
var item=Dialog.prototype.createHTMLElement.call(this);
var _2e60=document.createElement("form");
_2e60.style.position="absolute";
_2e60.style.left="10px";
_2e60.style.top="30px";
_2e60.style.width="375px";
_2e60.style.font="normal 10px verdana";
item.appendChild(_2e60);
this.label=document.createTextNode("Text");
_2e60.appendChild(this.label);
this.input=document.createElement("input");
this.input.style.border="1px solid gray";
this.input.style.font="normal 10px verdana";
this.input.type="text";
var value=this.figure.getText();
if(value){
this.input.value=value;
}else{
this.input.value="";
}
this.input.style.width="100%";
_2e60.appendChild(this.input);
this.input.focus();
return item;
};
AnnotationDialog.prototype.onOk=function(){
this.workflow.getCommandStack().execute(new CommandSetText(this.figure,this.input.value));
this.workflow.removeFigure(this);
};
PropertyWindow=function(){
this.currentSelection=null;
Window.call(this,"Property Window");
this.setDimension(200,100);
};
PropertyWindow.prototype=new Window;
PropertyWindow.prototype.type="PropertyWindow";
PropertyWindow.prototype.dispose=function(){
Window.prototype.dispose.call(this);
};
PropertyWindow.prototype.createHTMLElement=function(){
var item=Window.prototype.createHTMLElement.call(this);
item.appendChild(this.createLabel("Type:",15,25));
item.appendChild(this.createLabel("X :",15,50));
item.appendChild(this.createLabel("Y :",15,70));
item.appendChild(this.createLabel("Width :",85,50));
item.appendChild(this.createLabel("Height :",85,70));
this.labelType=this.createLabel("",50,25);
this.labelX=this.createLabel("",40,50);
this.labelY=this.createLabel("",40,70);
this.labelWidth=this.createLabel("",135,50);
this.labelHeight=this.createLabel("",135,70);
this.labelType.style.fontWeight="normal";
this.labelX.style.fontWeight="normal";
this.labelY.style.fontWeight="normal";
this.labelWidth.style.fontWeight="normal";
this.labelHeight.style.fontWeight="normal";
item.appendChild(this.labelType);
item.appendChild(this.labelX);
item.appendChild(this.labelY);
item.appendChild(this.labelWidth);
item.appendChild(this.labelHeight);
return item;
};
PropertyWindow.prototype.onSelectionChanged=function(_31cf){
Window.prototype.onSelectionChanged.call(this,_31cf);
if(this.currentSelection!=null){
this.currentSelection.detachMoveListener(this);
}
this.currentSelection=_31cf;
if(_31cf!=null&&_31cf!=this){
this.labelType.innerHTML=_31cf.type;
if(_31cf.getX){
this.labelX.innerHTML=_31cf.getX();
this.labelY.innerHTML=_31cf.getY();
this.labelWidth.innerHTML=_31cf.getWidth();
this.labelHeight.innerHTML=_31cf.getHeight();
this.currentSelection=_31cf;
this.currentSelection.attachMoveListener(this);
}else{
this.labelX.innerHTML="";
this.labelY.innerHTML="";
this.labelWidth.innerHTML="";
this.labelHeight.innerHTML="";
}
}else{
this.labelType.innerHTML="&lt;none&gt;";
this.labelX.innerHTML="";
this.labelY.innerHTML="";
this.labelWidth.innerHTML="";
this.labelHeight.innerHTML="";
}
};
PropertyWindow.prototype.getCurrentSelection=function(){
return this.currentSelection;
};
PropertyWindow.prototype.onOtherFigureMoved=function(_31d0){
if(_31d0==this.currentSelection){
this.onSelectionChanged(_31d0);
}
};
PropertyWindow.prototype.createLabel=function(text,x,y){
var l=document.createElement("div");
l.style.position="absolute";
l.style.left=x+"px";
l.style.top=y+"px";
l.style.font="normal 10px verdana";
l.style.whiteSpace="nowrap";
l.style.fontWeight="bold";
l.innerHTML=text;
return l;
};
ColorDialog=function(){
this.maxValue={"h":"359","s":"100","v":"100"};
this.HSV={0:359,1:100,2:100};
this.slideHSV={0:359,1:100,2:100};
this.SVHeight=165;
this.wSV=162;
this.wH=162;
Dialog.call(this,"Color Chooser");
this.loadSV();
this.setColor(new Color(255,0,0));
this.setDimension(219,244);
};
ColorDialog.prototype=new Dialog;
ColorDialog.prototype.type="ColorDialog";
ColorDialog.prototype.createHTMLElement=function(){
var oThis=this;
var item=Dialog.prototype.createHTMLElement.call(this);
this.outerDiv=document.createElement("div");
this.outerDiv.id="plugin";
this.outerDiv.style.top="15px";
this.outerDiv.style.left="0px";
this.outerDiv.style.width="201px";
this.outerDiv.style.position="absolute";
this.outerDiv.style.padding="9px";
this.outerDiv.display="block";
this.outerDiv.style.background="#0d0d0d";
this.plugHEX=document.createElement("div");
this.plugHEX.id="plugHEX";
this.plugHEX.innerHTML="F1FFCC";
this.plugHEX.style.color="white";
this.plugHEX.style.font="normal 10px verdana";
this.outerDiv.appendChild(this.plugHEX);
this.SV=document.createElement("div");
this.SV.onmousedown=function(event){
oThis.mouseDownSV(oThis.SVslide,event);
};
this.SV.id="SV";
this.SV.style.cursor="crosshair";
this.SV.style.background="#FF0000 url(SatVal.png)";
this.SV.style.position="absolute";
this.SV.style.height="166px";
this.SV.style.width="167px";
this.SV.style.marginRight="10px";
this.SV.style.filter="progid:DXImageTransform.Microsoft.AlphaImageLoader(src='SatVal.png', sizingMethod='scale')";
this.SV.style["float"]="left";
this.outerDiv.appendChild(this.SV);
this.SVslide=document.createElement("div");
this.SVslide.onmousedown=function(event){
oThis.mouseDownSV(event);
};
this.SVslide.style.top="40px";
this.SVslide.style.left="40px";
this.SVslide.style.position="absolute";
this.SVslide.style.cursor="crosshair";
this.SVslide.style.background="url(slide.gif)";
this.SVslide.style.height="9px";
this.SVslide.style.width="9px";
this.SVslide.style.lineHeight="1px";
this.outerDiv.appendChild(this.SVslide);
this.H=document.createElement("form");
this.H.id="H";
this.H.onmousedown=function(event){
oThis.mouseDownH(event);
};
this.H.style.border="1px solid #000000";
this.H.style.cursor="crosshair";
this.H.style.position="absolute";
this.H.style.width="19px";
this.H.style.top="28px";
this.H.style.left="191px";
this.outerDiv.appendChild(this.H);
this.Hslide=document.createElement("div");
this.Hslide.style.top="-7px";
this.Hslide.style.left="-8px";
this.Hslide.style.background="url(slideHue.gif)";
this.Hslide.style.height="5px";
this.Hslide.style.width="33px";
this.Hslide.style.position="absolute";
this.Hslide.style.lineHeight="1px";
this.H.appendChild(this.Hslide);
this.Hmodel=document.createElement("div");
this.Hmodel.style.height="1px";
this.Hmodel.style.width="19px";
this.Hmodel.style.lineHeight="1px";
this.Hmodel.style.margin="0px";
this.Hmodel.style.padding="0px";
this.Hmodel.style.fontSize="1px";
this.H.appendChild(this.Hmodel);
item.appendChild(this.outerDiv);
return item;
};
ColorDialog.prototype.onOk=function(){
Dialog.prototype.onOk.call(this);
};
browser=function(v){
return (Math.max(navigator.userAgent.toLowerCase().indexOf(v),0));
};
ColorDialog.prototype.showColor=function(c){
this.plugHEX.style.background="#"+c;
this.plugHEX.innerHTML=c;
};
ColorDialog.prototype.getSelectedColor=function(){
var rgb=this.hex2rgb(this.plugHEX.innerHTML);
return new Color(rgb[0],rgb[1],rgb[2]);
};
ColorDialog.prototype.setColor=function(color){
if(color==null){
color=new Color(100,100,100);
}
var hex=this.rgb2hex(Array(color.getRed(),color.getGreen(),color.getBlue()));
this.updateH(hex);
};
ColorDialog.prototype.XY=function(e,v){
var z=browser("msie")?Array(event.clientX+document.body.scrollLeft,event.clientY+document.body.scrollTop):Array(e.pageX,e.pageY);
return z[v];
};
ColorDialog.prototype.mkHSV=function(a,b,c){
return (Math.min(a,Math.max(0,Math.ceil((parseInt(c)/b)*a))));
};
ColorDialog.prototype.ckHSV=function(a,b){
if(a>=0&&a<=b){
return (a);
}else{
if(a>b){
return (b);
}else{
if(a<0){
return ("-"+oo);
}
}
}
};
ColorDialog.prototype.mouseDownH=function(e){
this.slideHSV[0]=this.HSV[0];
var oThis=this;
this.H.onmousemove=function(e){
oThis.dragH(e);
};
this.H.onmouseup=function(e){
oThis.H.onmousemove="";
oThis.H.onmouseup="";
};
this.dragH(e);
};
ColorDialog.prototype.dragH=function(e){
var y=this.XY(e,1)-this.getY()-40;
this.Hslide.style.top=(this.ckHSV(y,this.wH)-5)+"px";
this.slideHSV[0]=this.mkHSV(359,this.wH,this.Hslide.style.top);
this.updateSV();
this.showColor(this.commit());
this.SV.style.backgroundColor="#"+this.hsv2hex(Array(this.HSV[0],100,100));
};
ColorDialog.prototype.mouseDownSV=function(o,e){
this.slideHSV[0]=this.HSV[0];
var oThis=this;
function reset(){
oThis.SV.onmousemove="";
oThis.SV.onmouseup="";
oThis.SVslide.onmousemove="";
oThis.SVslide.onmouseup="";
}
this.SV.onmousemove=function(e){
oThis.dragSV(e);
};
this.SV.onmouseup=reset;
this.SVslide.onmousemove=function(e){
oThis.dragSV(e);
};
this.SVslide.onmouseup=reset;
this.dragSV(e);
};
ColorDialog.prototype.dragSV=function(e){
var x=this.XY(e,0)-this.getX()-1;
var y=this.XY(e,1)-this.getY()-20;
this.SVslide.style.left=this.ckHSV(x,this.wSV)+"px";
this.SVslide.style.top=this.ckHSV(y,this.wSV)+"px";
this.slideHSV[1]=this.mkHSV(100,this.wSV,this.SVslide.style.left);
this.slideHSV[2]=100-this.mkHSV(100,this.wSV,this.SVslide.style.top);
this.updateSV();
};
ColorDialog.prototype.commit=function(){
var r="hsv";
var z={};
var j="";
for(var i=0;i<=r.length-1;i++){
j=r.substr(i,1);
z[i]=(j=="h")?this.maxValue[j]-this.mkHSV(this.maxValue[j],this.wH,this.Hslide.style.top):this.HSV[i];
}
return (this.updateSV(this.hsv2hex(z)));
};
ColorDialog.prototype.updateSV=function(v){
this.HSV=v?this.hex2hsv(v):Array(this.slideHSV[0],this.slideHSV[1],this.slideHSV[2]);
if(!v){
v=this.hsv2hex(Array(this.slideHSV[0],this.slideHSV[1],this.slideHSV[2]));
}
this.showColor(v);
return v;
};
ColorDialog.prototype.loadSV=function(){
var z="";
for(var i=this.SVHeight;i>=0;i--){
z+="<div style=\"background:#"+this.hsv2hex(Array(Math.round((359/this.SVHeight)*i),100,100))+";\"><br/></div>";
}
this.Hmodel.innerHTML=z;
};
ColorDialog.prototype.updateH=function(v){
this.plugHEX.innerHTML=v;
this.HSV=this.hex2hsv(v);
this.SV.style.backgroundColor="#"+this.hsv2hex(Array(this.HSV[0],100,100));
this.SVslide.style.top=(parseInt(this.wSV-this.wSV*(this.HSV[1]/100))+20)+"px";
this.SVslide.style.left=(parseInt(this.wSV*(this.HSV[1]/100))+5)+"px";
this.Hslide.style.top=(parseInt(this.wH*((this.maxValue["h"]-this.HSV[0])/this.maxValue["h"]))-7)+"px";
};
ColorDialog.prototype.toHex=function(v){
v=Math.round(Math.min(Math.max(0,v),255));
return ("0123456789ABCDEF".charAt((v-v%16)/16)+"0123456789ABCDEF".charAt(v%16));
};
ColorDialog.prototype.hex2rgb=function(r){
return ({0:parseInt(r.substr(0,2),16),1:parseInt(r.substr(2,2),16),2:parseInt(r.substr(4,2),16)});
};
ColorDialog.prototype.rgb2hex=function(r){
return (this.toHex(r[0])+this.toHex(r[1])+this.toHex(r[2]));
};
ColorDialog.prototype.hsv2hex=function(h){
return (this.rgb2hex(this.hsv2rgb(h)));
};
ColorDialog.prototype.hex2hsv=function(v){
return (this.rgb2hsv(this.hex2rgb(v)));
};
ColorDialog.prototype.rgb2hsv=function(r){
var max=Math.max(r[0],r[1],r[2]);
var delta=max-Math.min(r[0],r[1],r[2]);
var H;
var S;
var V;
if(max!=0){
S=Math.round(delta/max*100);
if(r[0]==max){
H=(r[1]-r[2])/delta;
}else{
if(r[1]==max){
H=2+(r[2]-r[0])/delta;
}else{
if(r[2]==max){
H=4+(r[0]-r[1])/delta;
}
}
}
var H=Math.min(Math.round(H*60),360);
if(H<0){
H+=360;
}
}
return ({0:H?H:0,1:S?S:0,2:Math.round((max/255)*100)});
};
ColorDialog.prototype.hsv2rgb=function(r){
var R;
var B;
var G;
var S=r[1]/100;
var V=r[2]/100;
var H=r[0]/360;
if(S>0){
if(H>=1){
H=0;
}
H=6*H;
F=H-Math.floor(H);
A=Math.round(255*V*(1-S));
B=Math.round(255*V*(1-(S*F)));
C=Math.round(255*V*(1-(S*(1-F))));
V=Math.round(255*V);
switch(Math.floor(H)){
case 0:
R=V;
G=C;
B=A;
break;
case 1:
R=B;
G=V;
B=A;
break;
case 2:
R=A;
G=V;
B=C;
break;
case 3:
R=A;
G=B;
B=V;
break;
case 4:
R=C;
G=A;
B=V;
break;
case 5:
R=V;
G=A;
B=B;
break;
}
return ({0:R?R:0,1:G?G:0,2:B?B:0});
}else{
return ({0:(V=Math.round(V*255)),1:V,2:V});
}
};
LineColorDialog=function(_2e57){
ColorDialog.call(this);
this.figure=_2e57;
var color=_2e57.getColor();
this.updateH(this.rgb2hex(color.getRed(),color.getGreen(),color.getBlue()));
};
LineColorDialog.prototype=new ColorDialog;
LineColorDialog.prototype.type="LineColorDialog";
LineColorDialog.prototype.onOk=function(){
var _2e59=this.workflow;
ColorDialog.prototype.onOk.call(this);
if(typeof this.figure.setColor=="function"){
_2e59.getCommandStack().execute(new CommandSetColor(this.figure,this.getSelectedColor()));
if(_2e59.getCurrentSelection()==this.figure){
_2e59.setCurrentSelection(this.figure);
}
}
};
BackgroundColorDialog=function(_40a6){
ColorDialog.call(this);
this.figure=_40a6;
var color=_40a6.getBackgroundColor();
if(color!=null){
this.updateH(this.rgb2hex(color.getRed(),color.getGreen(),color.getBlue()));
}
};
BackgroundColorDialog.prototype=new ColorDialog;
BackgroundColorDialog.prototype.type="BackgroundColorDialog";
BackgroundColorDialog.prototype.onOk=function(){
var _40a8=this.workflow;
ColorDialog.prototype.onOk.call(this);
if(typeof this.figure.setBackgroundColor=="function"){
_40a8.getCommandStack().execute(new CommandSetBackgroundColor(this.figure,this.getSelectedColor()));
if(_40a8.getCurrentSelection()==this.figure){
_40a8.setCurrentSelection(this.figure);
}
}
};
AnnotationDialog=function(_2e5e){
this.figure=_2e5e;
Dialog.call(this);
this.setDimension(400,100);
};
AnnotationDialog.prototype=new Dialog;
AnnotationDialog.prototype.type="AnnotationDialog";
AnnotationDialog.prototype.createHTMLElement=function(){
var item=Dialog.prototype.createHTMLElement.call(this);
var _2e60=document.createElement("form");
_2e60.style.position="absolute";
_2e60.style.left="10px";
_2e60.style.top="30px";
_2e60.style.width="375px";
_2e60.style.font="normal 10px verdana";
item.appendChild(_2e60);
this.label=document.createTextNode("Text");
_2e60.appendChild(this.label);
this.input=document.createElement("input");
this.input.style.border="1px solid gray";
this.input.style.font="normal 10px verdana";
this.input.type="text";
var value=this.figure.getText();
if(value){
this.input.value=value;
}else{
this.input.value="";
}
this.input.style.width="100%";
_2e60.appendChild(this.input);
this.input.focus();
return item;
};
AnnotationDialog.prototype.onOk=function(){
this.workflow.getCommandStack().execute(new CommandSetText(this.figure,this.input.value));
this.workflow.removeFigure(this);
};
Command=function(label){
this.label=label;
};
Command.prototype.type="Command";
Command.prototype.getLabel=function(){
};
Command.prototype.canExecute=function(){
return true;
};
Command.prototype.execute=function(){
};
Command.prototype.undo=function(){
};
Command.prototype.redo=function(){
};
CommandStack=function(){
this.undostack=new Array();
this.redostack=new Array();
this.maxundo=50;
this.eventListeners=new ArrayList();
};
CommandStack.PRE_EXECUTE=1;
CommandStack.PRE_REDO=2;
CommandStack.PRE_UNDO=4;
CommandStack.POST_EXECUTE=8;
CommandStack.POST_REDO=16;
CommandStack.POST_UNDO=32;
CommandStack.POST_MASK=CommandStack.POST_EXECUTE|CommandStack.POST_UNDO|CommandStack.POST_REDO;
CommandStack.PRE_MASK=CommandStack.PRE_EXECUTE|CommandStack.PRE_UNDO|CommandStack.PRE_REDO;
CommandStack.prototype.type="CommandStack";
CommandStack.prototype.setUndoLimit=function(count){
this.maxundo=count;
};
CommandStack.prototype.markSaveLocation=function(){
this.undostack=new Array();
this.redostack=new Array();
};
CommandStack.prototype.execute=function(_3f6c){
if(_3f6c.canExecute()==false){
return;
}
this.notifyListeners(_3f6c,CommandStack.PRE_EXECUTE);
this.undostack.push(_3f6c);
_3f6c.execute();
this.redostack=new Array();
if(this.undostack.length>this.maxundo){
this.undostack=this.undostack.slice(this.undostack.length-this.maxundo);
}
this.notifyListeners(_3f6c,CommandStack.POST_EXECUTE);
};
CommandStack.prototype.undo=function(){
var _3f6d=this.undostack.pop();
if(_3f6d){
this.notifyListeners(_3f6d,CommandStack.PRE_UNDO);
this.redostack.push(_3f6d);
_3f6d.undo();
this.notifyListeners(_3f6d,CommandStack.POST_UNDO);
}
};
CommandStack.prototype.redo=function(){
var _3f6e=this.redostack.pop();
if(_3f6e){
this.notifyListeners(_3f6e,CommandStack.PRE_REDO);
this.undostack.push(_3f6e);
_3f6e.redo();
this.notifyListeners(_3f6e,CommandStack.POST_REDO);
}
};
CommandStack.prototype.canRedo=function(){
return this.redostack.length>0;
};
CommandStack.prototype.canUndo=function(){
return this.undostack.length>0;
};
CommandStack.prototype.addCommandStackEventListener=function(_3f6f){
this.eventListeners.add(_3f6f);
};
CommandStack.prototype.removeCommandStackEventListener=function(_3f70){
this.eventListeners.remove(_3f70);
};
CommandStack.prototype.notifyListeners=function(_3f71,state){
var event=new CommandStackEvent(_3f71,state);
var size=this.eventListeners.getSize();
for(var i=0;i<size;i++){
this.eventListeners.get(i).stackChanged(event);
}
};
CommandStackEvent=function(_3e48,_3e49){
this.command=_3e48;
this.details=_3e49;
};
CommandStackEvent.prototype.type="CommandStackEvent";
CommandStackEvent.prototype.getCommand=function(){
return this.command;
};
CommandStackEvent.prototype.getDetails=function(){
return this.details;
};
CommandStackEvent.prototype.isPostChangeEvent=function(){
return 0!=(this.getDetails()&CommandStack.POST_MASK);
};
CommandStackEvent.prototype.isPreChangeEvent=function(){
return 0!=(this.getDetails()&CommandStack.PRE_MASK);
};
CommandStackEventListener=function(){
};
CommandStackEventListener.prototype.type="CommandStackEventListener";
CommandStackEventListener.prototype.stackChanged=function(event){
};
CommandAdd=function(_4089,_408a,x,y,_408d){
Command.call(this,"add figure");
this.parent=_408d;
this.figure=_408a;
this.x=x;
this.y=y;
this.workflow=_4089;
};
CommandAdd.prototype=new Command;
CommandAdd.prototype.type="CommandAdd";
CommandAdd.prototype.execute=function(){
this.redo();
};
CommandAdd.prototype.redo=function(){
if(this.x&&this.y){
this.workflow.addFigure(this.figure,this.x,this.y);
}else{
this.workflow.addFigure(this.figure);
}
this.workflow.setCurrentSelection(this.figure);
if(this.parent!=null){
this.parent.addChild(this.figure);
}
};
CommandAdd.prototype.undo=function(){
this.workflow.removeFigure(this.figure);
this.workflow.setCurrentSelection(null);
if(this.parent!=null){
this.parent.removeChild(this.figure);
}
};
CommandDelete=function(_3f13){
Command.call(this,"delete figure");
this.parent=_3f13.parent;
this.figure=_3f13;
this.workflow=_3f13.workflow;
this.connections=null;
};
CommandDelete.prototype=new Command;
CommandDelete.prototype.type="CommandDelete";
CommandDelete.prototype.execute=function(){
this.redo();
};
CommandDelete.prototype.undo=function(){
this.workflow.addFigure(this.figure);
if(this.figure instanceof Connection){
this.figure.reconnect();
}
this.workflow.setCurrentSelection(this.figure);
if(this.parent!=null){
this.parent.addChild(this.figure);
}
for(var i=0;i<this.connections.getSize();++i){
this.workflow.addFigure(this.connections.get(i));
this.connections.get(i).reconnect();
}
};
CommandDelete.prototype.redo=function(){
this.workflow.removeFigure(this.figure);
this.workflow.setCurrentSelection(null);
if(this.figure.getPorts&&this.connections==null){
this.connections=new ArrayList();
var ports=this.figure.getPorts();
for(var i=0;i<ports.getSize();i++){
if(ports.get(i).getConnections){
this.connections.addAll(ports.get(i).getConnections());
}
}
}
if(this.connections==null){
this.connections=new ArrayList();
}
if(this.parent!=null){
this.parent.removeChild(this.figure);
}
for(var i=0;i<this.connections.getSize();++i){
this.workflow.removeFigure(this.connections.get(i));
}
};
CommandMove=function(_3f21,x,y){
Command.call(this,"move figure");
this.figure=_3f21;
this.oldX=x;
this.oldY=y;
this.oldCompartment=_3f21.getParent();
};
CommandMove.prototype=new Command;
CommandMove.prototype.type="CommandMove";
CommandMove.prototype.setPosition=function(x,y){
this.newX=x;
this.newY=y;
this.newCompartment=this.figure.workflow.getBestCompartmentFigure(x,y,this.figure);
};
CommandMove.prototype.canExecute=function(){
return this.newX!=this.oldX||this.newY!=this.oldY;
};
CommandMove.prototype.execute=function(){
this.redo();
};
CommandMove.prototype.undo=function(){
this.figure.setPosition(this.oldX,this.oldY);
if(this.newCompartment!=null){
this.newCompartment.removeChild(this.figure);
}
if(this.oldCompartment!=null){
this.oldCompartment.addChild(this.figure);
}
this.figure.workflow.moveResizeHandles(this.figure);
};
CommandMove.prototype.redo=function(){
this.figure.setPosition(this.newX,this.newY);
if(this.oldCompartment!=null){
this.oldCompartment.removeChild(this.figure);
}
if(this.newCompartment!=null){
this.newCompartment.addChild(this.figure);
}
this.figure.workflow.moveResizeHandles(this.figure);
};
CommandResize=function(_3e82,width,_3e84){
Command.call(this,"resize figure");
this.figure=_3e82;
this.oldWidth=width;
this.oldHeight=_3e84;
};
CommandResize.prototype=new Command;
CommandResize.prototype.type="CommandResize";
CommandResize.prototype.setDimension=function(width,_3e86){
this.newWidth=width;
this.newHeight=_3e86;
};
CommandResize.prototype.canExecute=function(){
return this.newWidth!=this.oldWidth||this.newHeight!=this.oldHeight;
};
CommandResize.prototype.execute=function(){
this.redo();
};
CommandResize.prototype.undo=function(){
this.figure.setDimension(this.oldWidth,this.oldHeight);
this.figure.workflow.moveResizeHandles(this.figure);
};
CommandResize.prototype.redo=function(){
this.figure.setDimension(this.newWidth,this.newHeight);
this.figure.workflow.moveResizeHandles(this.figure);
};
CommandSetText=function(_41ee,text){
Command.call(this,"set text");
this.figure=_41ee;
this.newText=text;
this.oldText=_41ee.getText();
};
CommandSetText.prototype=new Command;
CommandSetText.prototype.type="CommandSetText";
CommandSetText.prototype.execute=function(){
this.redo();
};
CommandSetText.prototype.redo=function(){
this.figure.setText(this.newText);
};
CommandSetText.prototype.undo=function(){
this.figure.setText(this.oldText);
};
CommandSetColor=function(_3a42,color){
Command.call(this,"set color");
this.figure=_3a42;
this.newColor=color;
this.oldColor=_3a42.getColor();
};
CommandSetColor.prototype=new Command;
CommandSetColor.prototype.type="CommandSetColor";
CommandSetColor.prototype.execute=function(){
this.redo();
};
CommandSetColor.prototype.undo=function(){
this.figure.setColor(this.oldColor);
};
CommandSetColor.prototype.redo=function(){
this.figure.setColor(this.newColor);
};
CommandSetBackgroundColor=function(_2d19,color){
Command.call(this,"set background color");
this.figure=_2d19;
this.newColor=color;
this.oldColor=_2d19.getBackgroundColor();
};
CommandSetBackgroundColor.prototype=new Command;
CommandSetBackgroundColor.prototype.type="CommandSetBackgroundColor";
CommandSetBackgroundColor.prototype.execute=function(){
this.redo();
};
CommandSetBackgroundColor.prototype.undo=function(){
this.figure.setBackgroundColor(this.oldColor);
};
CommandSetBackgroundColor.prototype.redo=function(){
this.figure.setBackgroundColor(this.newColor);
};
CommandConnect=function(_4063,_4064,_4065){
Command.call(this,"create connection");
this.workflow=_4063;
this.source=_4064;
this.target=_4065;
this.connection=null;
};
CommandConnect.prototype=new Command;
CommandConnect.prototype.type="CommandConnect";
CommandConnect.prototype.setConnection=function(_4066){
this.connection=_4066;
};
CommandConnect.prototype.execute=function(){
if(this.connection==null){
this.connection=new Connection();
}
this.connection.setSource(this.source);
this.connection.setTarget(this.target);
this.workflow.addFigure(this.connection);
};
CommandConnect.prototype.redo=function(){
this.workflow.addFigure(this.connection);
this.connection.reconnect();
};
CommandConnect.prototype.undo=function(){
this.workflow.removeFigure(this.connection);
};
CommandReconnect=function(con){
Command.call(this,"reconnect connection");
this.con=con;
this.oldSourcePort=con.getSource();
this.oldTargetPort=con.getTarget();
this.oldRouter=con.getRouter();
};
CommandReconnect.prototype=new Command;
CommandReconnect.prototype.type="CommandReconnect";
CommandReconnect.prototype.canExecute=function(){
return true;
};
CommandReconnect.prototype.setNewPorts=function(_3548,_3549){
this.newSourcePort=_3548;
this.newTargetPort=_3549;
};
CommandReconnect.prototype.execute=function(){
this.redo();
};
CommandReconnect.prototype.undo=function(){
this.con.setSource(this.oldSourcePort);
this.con.setTarget(this.oldTargetPort);
this.con.setRouter(this.oldRouter);
if(this.con.getWorkflow().getCurrentSelection()==this.con){
this.con.getWorkflow().showLineResizeHandles(this.con);
}
};
CommandReconnect.prototype.redo=function(){
this.con.setSource(this.newSourcePort);
this.con.setTarget(this.newTargetPort);
this.con.setRouter(this.oldRouter);
if(this.con.getWorkflow().getCurrentSelection()==this.con){
this.con.getWorkflow().showLineResizeHandles(this.con);
}
};
CommandMoveLine=function(line,_359c,_359d,endX,endY){
Command.call(this,"move line");
this.line=line;
this.startX1=_359c;
this.startY1=_359d;
this.endX1=endX;
this.endY1=endY;
};
CommandMoveLine.prototype=new Command;
CommandMoveLine.prototype.type="CommandMoveLine";
CommandMoveLine.prototype.canExecute=function(){
return this.startX1!=this.startX2||this.startY1!=this.startY2||this.endX1!=this.endX2||this.endY1!=this.endY2;
};
CommandMoveLine.prototype.setEndPoints=function(_35a0,_35a1,endX,endY){
this.startX2=_35a0;
this.startY2=_35a1;
this.endX2=endX;
this.endY2=endY;
};
CommandMoveLine.prototype.execute=function(){
this.redo();
};
CommandMoveLine.prototype.undo=function(){
this.line.setStartPoint(this.startX1,this.startY1);
this.line.setEndPoint(this.endX1,this.endY1);
if(this.line.workflow.getCurrentSelection()==this.line){
this.line.workflow.showLineResizeHandles(this.line);
}
};
CommandMoveLine.prototype.redo=function(){
this.line.setStartPoint(this.startX2,this.startY2);
this.line.setEndPoint(this.endX2,this.endY2);
if(this.line.workflow.getCurrentSelection()==this.line){
this.line.workflow.showLineResizeHandles(this.line);
}
};
Menu=function(){
this.menuItems=new ArrayList();
Figure.call(this);
this.setSelectable(false);
this.setDeleteable(false);
this.setCanDrag(false);
this.setResizeable(false);
this.setSelectable(false);
this.setZOrder(10000);
this.dirty=false;
};
Menu.prototype=new Figure;
Menu.prototype.type="Menu";
Menu.prototype.createHTMLElement=function(){
var item=document.createElement("div");
item.style.position="absolute";
item.style.left=this.x+"px";
item.style.top=this.y+"px";
item.style.margin="0px";
item.style.padding="0px";
item.style.zIndex=""+Figure.ZOrderBaseIndex;
item.style.border="1px solid gray";
item.style.background="lavender";
item.style.cursor="pointer";
return item;
};
Menu.prototype.setWorkflow=function(_41f4){
this.workflow=_41f4;
};
Menu.prototype.appendMenuItem=function(item){
this.menuItems.add(item);
item.parentMenu=this;
this.dirty=true;
};
Menu.prototype.getHTMLElement=function(){
var html=Figure.prototype.getHTMLElement.call(this);
if(this.dirty){
this.createList();
}
return html;
};
Menu.prototype.createList=function(){
this.dirty=false;
this.html.innerHTML="";
var oThis=this;
for(var i=0;i<this.menuItems.getSize();i++){
var item=this.menuItems.get(i);
var li=document.createElement("a");
li.innerHTML=item.getLabel();
li.style.display="block";
li.style.fontFamily="Verdana, Arial, Helvetica, sans-serif";
li.style.fontSize="9pt";
li.style.color="dimgray";
li.style.borderBottom="1px solid silver";
li.style.paddingLeft="5px";
li.style.paddingRight="5px";
li.style.cursor="pointer";
this.html.appendChild(li);
li.menuItem=item;
if(li.addEventListener){
li.addEventListener("click",function(event){
var _41fc=arguments[0]||window.event;
_41fc.cancelBubble=true;
_41fc.returnValue=false;
var diffX=_41fc.clientX;
var diffY=_41fc.clientY;
var _41ff=document.body.parentNode.scrollLeft;
var _4200=document.body.parentNode.scrollTop;
this.menuItem.execute(diffX+_41ff,diffY+_4200);
},false);
li.addEventListener("mouseup",function(event){
event.cancelBubble=true;
event.returnValue=false;
},false);
li.addEventListener("mousedown",function(event){
event.cancelBubble=true;
event.returnValue=false;
},false);
li.addEventListener("mouseover",function(event){
this.style.backgroundColor="silver";
},false);
li.addEventListener("mouseout",function(event){
this.style.backgroundColor="transparent";
},false);
}else{
if(li.attachEvent){
li.attachEvent("onclick",function(event){
var _4206=arguments[0]||window.event;
_4206.cancelBubble=true;
_4206.returnValue=false;
var diffX=_4206.clientX;
var diffY=_4206.clientY;
var _4209=document.body.parentNode.scrollLeft;
var _420a=document.body.parentNode.scrollTop;
event.srcElement.menuItem.execute(diffX+_4209,diffY+_420a);
});
li.attachEvent("onmousedown",function(event){
event.cancelBubble=true;
event.returnValue=false;
});
li.attachEvent("onmouseup",function(event){
event.cancelBubble=true;
event.returnValue=false;
});
li.attachEvent("onmouseover",function(event){
event.srcElement.style.backgroundColor="silver";
});
li.attachEvent("onmouseout",function(event){
event.srcElement.style.backgroundColor="transparent";
});
}
}
}
};
MenuItem=function(label,_3f5b,_3f5c){
this.label=label;
this.iconUrl=_3f5b;
this.parentMenu=null;
this.action=_3f5c;
};
MenuItem.prototype.type="MenuItem";
MenuItem.prototype.isEnabled=function(){
return true;
};
MenuItem.prototype.getLabel=function(){
return this.label;
};
MenuItem.prototype.execute=function(x,y){
this.parentMenu.workflow.showMenu(null);
this.action(x,y);
};
Locator=function(){
};
Locator.prototype.type="Locator";
Locator.prototype.relocate=function(_3a8d){
};
ConnectionLocator=function(_3f69){
Locator.call(this);
this.connection=_3f69;
};
ConnectionLocator.prototype=new Locator;
ConnectionLocator.prototype.type="ConnectionLocator";
ConnectionLocator.prototype.getConnection=function(){
return this.connection;
};
ManhattenMidpointLocator=function(_3b8e){
ConnectionLocator.call(this,_3b8e);
};
ManhattenMidpointLocator.prototype=new ConnectionLocator;
ManhattenMidpointLocator.prototype.type="ManhattenMidpointLocator";
ManhattenMidpointLocator.prototype.relocate=function(_3b8f){
var conn=this.getConnection();
var p=new Point();
var _3b92=conn.getPoints();
var index=Math.floor((_3b92.getSize()-2)/2);
var p1=_3b92.get(index);
var p2=_3b92.get(index+1);
p.x=(p2.x-p1.x)/2+p1.x+5;
p.y=(p2.y-p1.y)/2+p1.y+5;
_3b8f.setPosition(p.x,p.y);
};
