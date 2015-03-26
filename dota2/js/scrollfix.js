(function($) {
	//URI:http://caibaojian.com/scrollfix
	//author:caibaojian
	//website:http://caibaojian.com
	//descirption:scroll and fixed some div
	$.fn.scrollFix = function(options) {
		return this.each(function() {

			var opts = $.extend({}, $.fn.scrollFix.defaultOptions, options);
			var obj = $(this),
				base = this,
				selfTop = 0,
				selfLeft = 0,
				toTop = 0,
				parentOffsetLeft = 0,
				parentOffsetTop = 0,
				outerHeight,
				outerWidth,
				objWidth = 0,
				placeholder = jQuery('<div>'), //鍒涘缓涓€涓猨query瀵硅薄
				optsTop = opts.distanceTop, //瀹氫箟鍒伴《閮ㄧ殑楂樺害
				endfix = 0; //寮€濮嬪仠姝㈠浐瀹氱殑浣嶇疆

			var originalPosition;
			var originalOffsetTop;
			var originalZIndex;
			var lastOffsetLeft = -1;
			var isUnfixed = true;
			//濡傛灉娌℃湁鎵惧埌鑺傜偣锛屼笉杩涜澶勭悊
			if (obj.length <= 0) {
				return;
			}
			if (lastOffsetLeft == -1) {
				originalZIndex = obj.css('z-index');
				position = obj.css('position');
				originalPosition = obj.css('position');

				originalOffsetTop = obj.css('top');
			}

			var zIndex = obj.css('zIndex');
			if (opts.zIndex != 0) {
				zIndex = opts.zIndex;
			}
			//鑾峰彇鐩稿瀹氫綅鎴栬€呯粷瀵瑰畾浣嶇殑鐖剁被
			var parents = obj.parent();
			var Position = parents.css('position');
			while (!/^relative|absolute$/i.test(Position)) { //妫€娴嬫诞鍔ㄥ厓绱犵殑鐖剁被鍏冪礌瀹氫綅涓�'relative'鎴栬€�'absolute',鏄殑璇濋€€鍑猴紝鍚﹀垯鐨勮瘽锛屾墽琛屽惊鐜紝缁х画瀵绘壘瀹冪殑鐖剁被
				parents = parents.parent();
				Position = parents.css('position');
				if (/^body|html$/i.test(parents[0].tagName)) break; //鍋囧鐖剁被鍏冪礌鐨勬爣绛句负body鎴栬€匟TML锛岃鏄庢病鏈夋壘鍒扮埗绫讳负浠ヤ笂鐨勫畾浣嶏紝閫€鍑哄惊鐜�
			}

			var ie6 = !-[1, ] && !window.XMLHttpRequest; //鍏煎IE6
			var resizeWindow = false;

			function resetScroll() {
				setUnfixed();
				selfTop = obj.offset().top; //瀵硅薄璺濈椤堕儴楂樺害
				selfLeft = obj.offset().left; //瀵硅薄璺濈宸﹁竟瀹藉害
				outerHeight = obj.outerHeight(); //瀵硅薄楂樺害
				outerHeight = parseFloat(outerHeight) + parseFloat(obj.css('marginBottom').replace(/auto/, 0));
				outerWidth = obj.outerWidth(); //瀵硅薄澶栧搴�
				objWidth = obj.width();
				var documentHeight = $(document).height(); //鏂囨。楂樺害
				var startTop = $(opts.startTop), //寮€濮嬫诞鍔ㄥ浐瀹氬璞�
					startBottom = $(opts.startBottom),
					toBottom, //鍋滄婊氬姩浣嶇疆璺濈搴曢儴鐨勯珮搴�
					ScrollHeight; //瀵硅薄婊氬姩鐨勯珮搴�

				//璁＄畻鐖剁被鍋忕Щ鍊�
				if (/^body|html$/i.test(parents[0].tagName)) { //褰撶埗绫诲厓绱犻潪body鎴栬€匟TML鏃讹紝璇存槑鎵惧埌浜嗕竴涓埗绫讳负'relative'鎴栬€�'absolute'鐨勫厓绱狅紝寰楀嚭瀹冪殑鍋忕Щ楂樺害
					parentOffsetTop = 0, parentOffsetLeft = 0;
				} else {
					parentOffsetLeft = parents.offset().left, parentOffsetTop = parents.offset().top;
				}

				// 璁＄畻鐖惰妭鐐圭殑涓婅竟鍒伴《閮ㄨ窛绂�
				// 濡傛灉 body 鏈� top 灞炴€�, 娑堥櫎杩欎簺浣嶇Щ
				var bodyToTop = parseInt(jQuery('body').css('top'), 10);
				if (!isNaN(bodyToTop)) {
					optsTop += bodyToTop;
				}
				//璁＄畻鍋滃湪搴曢儴鐨勮窛绂�
				if (!isNaN(opts.endPos)) {
					toBottom = opts.endPos;
				} else {
					toBottom = parseFloat(documentHeight - $(opts.endPos).offset().top);
				}
				//璁＄畻闇€瑕佹粴鍔ㄧ殑楂樺害浠ュ強鍋滄婊氬姩鐨勯珮搴�
				ScrollHeight = parseFloat(documentHeight - toBottom - optsTop), endfix = parseFloat(ScrollHeight - outerHeight);
				//璁＄畻椤堕儴鐨勮窛绂诲€�
				if (startTop[0]) {
					var startTopOffset = startTop.offset(),
						startTopPos = startTopOffset.top;
					selfTop = startTopPos;
				}
				if (startBottom[0]) {
					var startBottomOffset = startBottom.offset(),
						startBottomPos = startBottomOffset.top,
						startBottomHeight = startBottom.outerHeight();
					selfTop = parseFloat(startBottomPos + startBottomHeight);
				}

				toTop = selfTop - optsTop;
				toTop = (toTop > 0) ? toTop : 0;

				var selfBottom = documentHeight - selfTop - outerHeight;
				//濡傛灉婊氬姩鍋滃湪搴曢儴鐨勫€间笉涓�0锛屽苟涓旇嚜韬埌搴曢儴鐨勯珮搴﹀皬浜庝笂闈㈣繖涓€硷紝涓嶆墽琛屾诞鍔ㄥ浐瀹�
				if ((toBottom != 0) && (selfBottom <= toBottom)) {
					return;
				}

			}
			function setUnfixed() {
				if (!isUnfixed) {
					lastOffsetLeft = -1;
					placeholder.css("display", "none");
					obj.css({
						'z-index': originalZIndex,
						'width': '',
						'position': originalPosition,
						'left': '',
						'top': originalOffsetTop,
						'margin-left': ''
					});
					obj.removeClass('scrollfixed');
					isUnfixed = true;
				}
			}

			function onScroll() {
				lastOffsetLeft = 1;
				var ScrollTop = $(window).scrollTop();
				if (opts.bottom != -1) {
					ScrollTop = ScrollTop + $(window).height() - outerHeight - opts.bottom;
				}
				if (ScrollTop > toTop && (ScrollTop < endfix)) {
					if (ie6) { //IE6鍒欎娇鐢ㄨ繖涓牱寮�
						obj.addClass(opts.baseClassName).css({
							"z-index": zIndex,
							"position": "absolute",
							"top": opts.bottom == -1 ? ScrollTop + optsTop - parentOffsetTop : ScrollTop - parentOffsetTop,
							"bottom": 'auto',
							"left": selfLeft - parentOffsetLeft,
							'width': objWidth
						})
					} else {
						obj.addClass(opts.baseClassName).css({
							"z-index": zIndex,
							"position": "fixed",
							"top": opts.bottom == -1 ? optsTop : '',
							"bottom": opts.bottom == -1 ? '' : opts.bottom,
							"left": selfLeft,
							"width": objWidth
						});
					}
					placeholder.css({
						'height': outerHeight,
						'width': outerWidth,
						'display': 'block'
					}).insertBefore(obj);
				} else if (ScrollTop >= endfix) {
					obj.addClass(opts.baseClassName).css({
						"z-index": zIndex,
						"position": "absolute",
						"top": endfix - parentOffsetTop + optsTop,
						'bottom': '',
						"left": selfLeft - parentOffsetLeft,
						"width": objWidth
					});
					placeholder.css({
						'height': outerHeight,
						'width': outerWidth,
						'display': 'block'
					}).insertBefore(obj)
				} else {
					obj.removeClass(opts.baseClassName).css({
						"z-index": originalZIndex,
						"position": "static",
						"top": "",
						"bottom": "",
						"left": ""
					});
					placeholder.remove()
				}
			}
			var Timer = 0;
			// if (isUnfixed) {
			resetScroll();
			// }
			$(window).on("scroll", function() {
				if (Timer) {
					clearTimeout(Timer);
				}
				Timer = setTimeout(onScroll, 0);
			});
			// 褰撳彂鐜拌皟鏁村睆骞曞ぇ灏忔椂锛岄噸鏂版墽琛屼唬鐮�
			$(window).on("resize", function() {
				if (Timer) {
					clearTimeout(Timer);
				}
				Timer = setTimeout(function() {
					isUnfixed = false;
					resetScroll();
					onScroll();
				}, 0);
			});
		})
	}
	$.fn.scrollFix.defaultOptions = {
		startTop: null, //婊戝埌杩欎釜浣嶇疆椤堕儴鏃跺紑濮嬫诞鍔紝榛樿涓虹┖
		startBottom: null, //婊戝埌杩欎釜浣嶇疆鏈寮€濮嬫诞鍔紝榛樿涓虹┖
		distanceTop: 0, //鍥哄畾鍦ㄩ《閮ㄧ殑楂樺害
		endPos: 0, //鍋滈潬鍦ㄥ簳閮ㄧ殑浣嶇疆锛屽彲浠ヤ负jquery瀵硅薄
		bottom: -1, //搴曢儴浣嶇疆
		zIndex: 0, //z-index鍊�
		baseClassName: 'scrollfixed' //寮€濮嬪浐瀹氭椂娣诲姞鐨勭被
	};
})(jQuery);