/*!
 * jQuery Tools v1.2.7 - The missing UI library for the Web
 * 
 * scrollable/scrollable.js
 * scrollable/scrollable.autoscroll.js
 * scrollable/scrollable.navigator.js
 * 
 * NO COPYRIGHTS OR LICENSES. DO WHAT YOU LIKE.
 * 
 * http://flowplayer.org/tools/
 * 
 */
(function (a) {
        a.tools = a.tools || {
            version: "v1.2.7"
        }, a.tools.scrollable = {
            conf: {
                activeClass: "active",
                circular: !1,
                clonedClass: "cloned",
                disabledClass: "disabled",
                easing: "swing",
                initialIndex: 0,
                item: "> *",
                items: ".items",
                keyboard: !0,
                mousewheel: !1,
                next: ".next",
                prev: ".prev",
                size: 1,
                speed: 400,
                vertical: !1,
                touch: !0,
                wheelSpeed: 0
            }
        };

        function b(a, b) {
            var c = parseInt(a.css(b), 10);
            if (c) return c;
            var d = a[0].currentStyle;
            return d && d.width && parseInt(d.width, 10)
        }

        function c(b, c) {
            var d = a(c);
            return d.length < 2 ? d : b.parent().find(c)
        }
        var d;

        function e(b, e) {
            var f = this,
                g = b.add(f),
                h = b.children(),
                i = 0,
                j = e.vertical;
            d || (d = f), h.length > 1 && (h = a(e.items, b)), e.size > 1 && (e.circular = !1), a.extend(f, {
                    getConf: function () {
                        return e
                    },
                    getIndex: function () {
                        return i
                    },
                    getSize: function () {
                        return f.getItems().size()
                    },
                    getNaviButtons: function () {
                        return n.add(o)
                    },
                    getRoot: function () {
                        return b
                    },
                    getItemWrap: function () {
                        return h
                    },
                    getItems: function () {
                        return h.find(e.item).not("." + e.clonedClass)
                    },
                    move: function (a, b) {
                        return f.seekTo(i + a, b)
                    },
                    next: function (a) {
                        return f.move(e.size, a)
                    },
                    prev: function (a) {
                        return f.move(-e.size, a)
                    },
                    begin: function (a) {
                        return f.seekTo(0, a)
                    },
                    end: function (a) {
                        return f.seekTo(f.getSize() - 1, a)
                    },
                    focus: function () {
                        d = f;
                        return f
                    },
                    addItem: function (b) {
                        b = a(b), e.circular ? (h.children().last().before(b), h.children().first().replaceWith(b.clone().addClass(e.clonedClass))) : (h.append(b), o.removeClass("disabled")), g.trigger("onAddItem", [b]);
                        return f
                    },
                    seekTo: function (b, c, k) {
                        b.jquery || (b *= 1);
                        if (e.circular && b === 0 && i == -1 && c !== 0) return f;
                        if (!e.circular && b < 0 || b > f.getSize() || b < -1) return f;
                        var l = b;
                        b.jquery ? b = f.getItems().index(b) : l = f.getItems().eq(b);
                        var m = a.Event("onBeforeSeek");
                        if (!k) {
                            g.trigger(m, [b, c]);
                            if (m.isDefaultPrevented() || !l.length) return f
                        }
                        var n = j ? {
                            top: -l.position().top
                        } : {
                            left: -l.position().left
                        };
                        i = b, d = f, c === undefined && (c = e.speed), h.animate(n, c, e.easing, k || function () {
                                g.trigger("onSeek", [b])
                            });
                        return f
                    }
                }), a.each(["onBeforeSeek", "onSeek", "onAddItem"], function (b, c) {
                    a.isFunction(e[c]) && a(f).on(c, e[c]), f[c] = function (b) {
                        b && a(f).on(c, b);
                        return f
                    }
                });
            if (e.circular) {
                var k = f.getItems().slice(-1).clone().prependTo(h),
                    l = f.getItems().eq(1).clone().appendTo(h);
                k.add(l).addClass(e.clonedClass), f.onBeforeSeek(function (a, b, c) {
                        if (!a.isDefaultPrevented()) {
                            if (b == -1) {
                                f.seekTo(k, c, function () {
                                        f.end(0)
                                    });
                                return a.preventDefault()
                            }
                            b == f.getSize() && f.seekTo(l, c, function () {
                                    f.begin(0)
                                })
                        }
                    });
                var m = b.parents().add(b).filter(function () {
                        if (a(this).css("display") === "none") return !0
                    });
                m.length ? (m.show(), f.seekTo(0, 0, function () {}), m.hide()) : f.seekTo(0, 0, function () {})
            }
            var n = c(b, e.prev).click(function (a) {
                    a.stopPropagation(), f.prev()
                }),
                o = c(b, e.next).click(function (a) {
                        a.stopPropagation(), f.next()
                    });
            e.circular || (f.onBeforeSeek(function (a, b) {
                        setTimeout(function () {
                                a.isDefaultPrevented() || (n.toggleClass(e.disabledClass, b <= 0), o.toggleClass(e.disabledClass, b >= f.getSize() - 1))
                            }, 1)
                    }), e.initialIndex || n.addClass(e.disabledClass)), f.getSize() < 2 && n.add(o).addClass(e.disabledClass), e.mousewheel && a.fn.mousewheel && b.mousewheel(function (a, b) {
                    if (e.mousewheel) {
                        f.move(b < 0 ? 1 : -1, e.wheelSpeed || 50);
                        return !1
                    }
                });
            if (e.touch) {
                var p = {};
                h[0].ontouchstart = function (a) {
                    var b = a.touches[0];
                    p.x = b.clientX, p.y = b.clientY
                }, h[0].ontouchmove = function (a) {
                    if (a.touches.length == 1 && !h.is(":animated")) {
                        var b = a.touches[0],
                            c = p.x - b.clientX,
                            d = p.y - b.clientY;
                        ////1 line below commented and 1 line added by SliderVilla
						//f[j && d > 0 || !j && c > 0 ? "next" : "prev"](), a.preventDefault()
						f[j && d > 0 || !j && c > 0 ? "next" : "prev"]()
                    }
                }
            }
            e.keyboard && a(document).on("keydown.scrollable", function (b) {
                    if (!(!e.keyboard || b.altKey || b.ctrlKey || b.metaKey || a(b.target).is(":input"))) {
                        if (e.keyboard != "static" && d != f) return;
                        var c = b.keyCode;
                        if (j && (c == 38 || c == 40)) {
                            f.move(c == 38 ? -1 : 1);
                            return b.preventDefault()
                        }
                        if (!j && (c == 37 || c == 39)) {
                            f.move(c == 37 ? -1 : 1);
                            return b.preventDefault()
                        }
                    }
                }), e.initialIndex && f.seekTo(e.initialIndex, 0, function () {})
        }
        a.fn.scrollable = function (b) {
            var c = this.data("scrollable");
            if (c) return c;
            b = a.extend({}, a.tools.scrollable.conf, b), this.each(function () {
                    c = new e(a(this), b), a(this).data("scrollable", c)
                });
            return b.api ? c : this
        }
    })(jQuery);
(function (a) {
        var b = a.tools.scrollable;
        b.autoscroll = {
            conf: {
                autoplay: !0,
                interval: 3e3,
                autopause: !0
            }
        }, a.fn.autoscroll = function (c) {
            typeof c == "number" && (c = {
                    interval: c
                });
            var d = a.extend({}, b.autoscroll.conf, c),
                e;
            this.each(function () {
                    var b = a(this).data("scrollable"),
                        c = b.getRoot(),
                        f, g = !1;

                    function h() {
                        f && clearTimeout(f), f = setTimeout(function () {
                                b.next()
                            }, d.interval)
                    }
                    b && (e = b), b.play = function () {
                        f || (g = !1, c.on("onSeek", h), h())
                    }, b.pause = function () {
                        f = clearTimeout(f), c.off("onSeek", h)
                    }, b.resume = function () {
                        g || b.play()
                    }, b.stop = function () {
                        g = !0, b.pause()
                    }, d.autopause && c.add(b.getNaviButtons()).hover(b.pause, b.resume), d.autoplay && b.play()
                });
            return d.api ? e : this
        }
    })(jQuery);
(function (a) {
        var b = a.tools.scrollable;
        b.navigator = {
            conf: {
                navi: ".navi",
                naviItem: null,
                activeClass: "active",
				//1 line below added by SliderVilla
				style: "",
                indexed: !1,
                idPrefix: null,
                history: !1
            }
        };

        function c(b, c) {
            var d = a(c);
            return d.length < 2 ? d : b.parent().find(c)
        }
        a.fn.navigator = function (d) {
            typeof d == "string" && (d = {
                    navi: d
                }), d = a.extend({}, b.navigator.conf, d);
            var e;
            this.each(function () {
                    var b = a(this).data("scrollable"),
                        f = d.navi.jquery ? d.navi : c(b.getRoot(), d.navi),
                        g = b.getNaviButtons(),
                        h = d.activeClass,
                        i = d.history && history.pushState,
                        j = b.getConf().size;
                    b && (e = b), b.getNaviButtons = function () {
                        return g.add(f)
                    }, i && (history.pushState({
                                i: 0
                            }, ""), a(window).on("popstate", function (a) {
                                var c = a.originalEvent.state;
                                c && b.seekTo(c.i)
                            }));

                    function k(a, c, d) {
                        b.seekTo(c), d.preventDefault(), i && history.pushState({
                                i: c
                            }, "")
                    }

                    function l() {
                        return f.find(d.naviItem || "> *")
                    }

                    function m(b) {
                        var c = a("<" + (d.naviItem || "a") + "/>").click(function (c) {
                                k(a(this), b, c)
                            });
                        //Commented 1 line and added 1 line below : SliderVilla
						//b === 0 && c.addClass(h),  d.indexed && c.text(b + 1), d.idPrefix && c.attr("id", d.idPrefix + b);
						c.attr("style",d.style), b === 0 && c.addClass(h),  d.indexed && c.text(b + 1), d.idPrefix && c.attr("id", d.idPrefix + b);
                        return c.appendTo(f)
                    }
                    l().length ? l().each(function (b) {
                            a(this).click(function (c) {
                                    k(a(this), b, c)
                                })
                        }) : a.each(b.getItems(), function (a) {
                            a % j == 0 && m(a)
                        }), b.onBeforeSeek(function (a, b) {
                            setTimeout(function () {
                                    if (!a.isDefaultPrevented()) {
                                        var c = b / j,
                                            d = l().eq(c);
                                        d.length && l().removeClass(h).eq(c).addClass(h)
                                    }
                                }, 1)
                        }), b.onAddItem(function (a, c) {
                            var d = b.getItems().index(c);
                            d % j == 0 && m(d)
                        })
                });
            return d.api ? e : this
        }
    })(jQuery);
/* SliderVilla : Functions for Glam Slider */
function svilla_t(T,F,G){
	F.find("img").animate({opacity:1},100);
	if(T=='1')	F.find(".glam_slideri").hover(function(){jQuery(this).find(".glam_slide_content").slideDown("fast");}, 
			 function(){jQuery(this).find(".glam_slide_content").stop(true,false).slideUp("fast");});
	else F.find(".glam_slide_content").slideDown("fast");
}
function svilla_u(F,I,G,H,O){
	F.find("img").animate({opacity:O},100).fadeIn();
	F.find(".glam_slide_content").stop(true,true);
	F.find(".glam_slide_content").hide();
}

function svilla_glam_onload(T,H){
	var w=jQuery(H+" .glam_slide:not(.glam_cloned)");
	var f=jQuery(H+" .glam_slide").width();
	jQuery(H).find(".glam_slide_content").hide();
	
	if(w.size()>1){
		w.slice(1,2).clone().addClass("glam_cloned").appendTo(H+" .glam_items");
		w.slice(w.size()-2,w.size()-1).clone().addClass("glam_cloned").prependTo(H+" .glam_items");
		jQuery(H+" .glam_items").css("left",(-jQuery(w[0]).position().left));
		svilla_t(T,jQuery(w[0]));
	}
}
function glam_responsiveScrollable(ri, rw, rh, riw, rsw, rp, rv){
	var siteWidth =  document.getElementById(ri).offsetWidth;
	//jQuery('#'+ri+' .glam_slide').css('width', (siteWidth* (riw/rw) ) );
	if(rw==0)rw=960;
	var bannerHeight = (siteWidth * rh ) / rw;	
	jQuery('#'+ri+' .glam_slider_instance,#'+ri+' .glam_slide,#'+ri+' .glam_slider_thumbnail').css('height', bannerHeight);
	
	jQuery('#'+ri+' .glam_items').css('margin-left', (siteWidth* (rsw/rw) ) );
	jQuery('#'+ri+' .glam_side_link').css('width', ( (siteWidth* (rsw/rw)) + rp ) );
	jQuery('#'+ri+' .glam_side_link').css('height', ( bannerHeight + 2*rp ) );
	
	var iwidth=(siteWidth*( (riw/rv) - ((rp/2) * (rv - 1)) ))/rw;
	jQuery('#'+ri+' .glam_slideri').css('width',iwidth);
	
	jQuery('#'+ri+' .glam_slider_instance').css('margin', (siteWidth* (rp/rw) ) );
	
	var m = siteWidth* (rp/(2*rw));
	jQuery('#'+ri+' .glam_slideri').css({ "margin-left": m+"px", "margin-right": m+"px" } );
	
	jQuery('#'+ri+' .glam_slide_content').css('height', 'auto' );
	
	var api = jQuery('#'+ri+' .glam_slider_instance').data('scrollable');
	var currentSlide = api.getIndex();
	api.seekTo(currentSlide, 50);
}
var glam_waitForFinalEvent = (function () {
  var glam_timers = {};
  return function (callback, ms, uniqueId, ri, rw, rh, riw, rsw, rp, rv) {
	if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (glam_timers[uniqueId]) {
      clearTimeout (glam_timers[uniqueId]);
    }
    glam_timers[uniqueId] = setTimeout(function(){ callback(ri, rw, rh, riw, rsw, rp, rv); }, ms);
  };
})();
