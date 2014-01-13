(function($){
/*----------------------------------------------------------------------------------
Class: FloatObject
http://archive.plugins.jquery.com/project/floatobject
-------------------------------------------------------------------------------------*/
	function FloatObject(jqObj, params){
		this.jqObj = jqObj;
		
		switch(params.speed){
			case 'fast': this.steps = 5; break;
			case 'normal': this.steps = 10; break;
			case 'slow': this.steps = 20; break;
			default: this.steps = 10;
		};
		
		var offset = this.jqObj.offset();
		
		this.currentX = offset.left;
		this.currentY = offset.top;
		
		this.origX = typeof(params.x) == "string" ?  this.currentX : params.x;
		this.origY = typeof(params.y) == "string" ?  this.currentY : params.y;
		//if( params.y) this.origY = params.y;
				
		//now we make sure the object is in absolute positions.
		this.jqObj.css({'position':'absolute' , 'top':this.currentY ,'left':this.currentX});
	}
	
	FloatObject.prototype.updateLocation = function(){
		this.updatedX = $(window).scrollLeft() + this.origX;
		this.updatedY = $(window).scrollTop()+ this.origY;
		
		this.dx = Math.abs(this.updatedX - this.currentX );
		this.dy = Math.abs(this.updatedY - this.currentY );
		
		return this.dx || this.dy;
	}
	
	FloatObject.prototype.move = function(){
		if( this.jqObj.css("position") != "absolute" ) return;
		var cx = 0;
		var cy = 0;
		
		if( this.dx > 0 ){			
			if( this.dx < this.steps / 2 )
				cx = (this.dx >= 1) ? 1 : 0;
			else
				cx = Math.round(this.dx/this.steps);
			
			if( this.currentX < this.updatedX )
				this.currentX += cx;
			else
				this.currentX -= cx;
		}
		
		if( this.dy > 0 ){
			if( this.dy < this.steps / 2 )
				cy = (this.dy >= 1) ? 1 : 0;
			else
				cy = Math.round(this.dy/this.steps);
			
			if( this.currentY < this.updatedY )
				this.currentY += cy;
			else
				this.currentY -= cy;
		}
		
		this.jqObj.css({'left':this.currentX, 'top': this.currentY });
	}
/*----------------------------------------------------------------------------------
Object: floatMgr
http://archive.plugins.jquery.com/project/floatobject
-------------------------------------------------------------------------------------*/		
	$.floatMgr = {		
		FOArray: new Array() ,		
		timer: null ,
		
		initializeFO: function(jqObj,params){
			var settings =  $.extend({
				x: 0 ,
				y: 0 ,
				speed: 'normal'	},params||{});
			var newFO = new FloatObject(jqObj,settings);
			
			$.floatMgr.FOArray.push(newFO);
			
			if( !$.floatMgr.timer ) $.floatMgr.adjustFO();
			
			//now making sure we are registered to all required window events
			if( !$.floatMgr.registeredEvents ){
					$(window).bind("resize", $.floatMgr.onChange);
					$(window).bind("scroll", $.floatMgr.onChange);
					$.floatMgr.registeredEvents = true;
			}		
		} , 
		
		adjustFO: function(){
			$.floatMgr.timer = null;
			
			var moveFO = false;
			
			for( var i = 0 ; i < $.floatMgr.FOArray.length ; i++ ){
				 FO = $.floatMgr.FOArray[i];
				 if( FO.updateLocation() )  moveFO = true;
			}
			
			if( moveFO ){
				for( var i = 0 ; i < $.floatMgr.FOArray.length ; i++ ){
					FO = $.floatMgr.FOArray[i];
					FO.move();
				}
				
				if( !$.floatMgr.timer ) $.floatMgr.timer = setTimeout($.floatMgr.adjustFO,50);
			}
		},
		
		onChange: function(){
			if( !$.floatMgr.timer ) $.floatMgr.adjustFO();
		}
	};
	
/*----------------------------------------------------------------------------------
Function: floatbutton
http://archive.plugins.jquery.com/project/floatobject
-------------------------------------------------------------------------------------*/		
	$.fn.floatbutton = function(userOptions) {
		// Default options
		var options = {
			x: "current",
			y: "current",
			on_click: function(){}
		}
		$.extend(options, userOptions);
	
		var container = $(this); //we only operate on the first selected object;
		container.click(function(){
			if (options.on_click != null){
				options.on_click();
			}
		});
		
		$.floatMgr.initializeFO(container, options); 
		if( $.floatMgr.timer == null ) $.floatMgr.adjustFO();
		
		return container;
	};

/*----------------------------------------------------------------------------------
Function: countdown
-------------------------------------------------------------------------------------*/	
	$.fn.countdown = function(userOptions){
		// Default options
		var options = {
			color: 'red',
			until: {}, // countdown target
			displayFormat: "%%D%%日%%H%%時%%M%%分%%S%%秒",
			finishMessage: "<span style=\"color:red;\"><b>00</b></span>日<span style=\"color:red;\"><b>00</b></span>時<span style=\"color:red;\"><b>00</b></span>分<span style=\"color:red;\"><b>00</b></span>秒",
			errorMessage: "ターゲット日時を設定ください。",
			on_timeup: function(){}
		}
		$.extend(options, userOptions);
        return $(this).each(function(){
            var container = $(this);
            var until = options.until;
            var until_date = new Date();

            if (container.text() != ''){
                if ( !(/[^\d,]+/g).test(container.text())){
                    until = "'" + container.text() + "'";
                } else {
                    var tmp = Date.parse("'" + container.text() + "'");
                    if (!isNaN(tmp)) {
                        until = "'" + container.text() + "'";
                    }
                }
            }
            
            var is_valid = function(){
                if (until == null || until == ''){
                    return false;
                }
                
                try {
                    if (!(/[^\d,]+/g).test(until)){
                        until_date = parseInt(until);
                    } else {
                        until_date = Date.parse(until);
                        if (isNaN(until_date)){
                            var tmp = until.replace(/-/g, "/");
                            tmp = tmp.replace(/\./g, "/");
                            until_date = Date.parse(tmp);
                        }
                    }
                    
                    if (isNaN(until_date)){
                        return false;
                    }
                } catch(e){
                    return false;
                }
                
                return true;
            }
            
            var calcage = function(secs, num1, num2) {
                s = (parseInt((secs/num1)%num2)).toString();
                if (s == null){
                    //console.log(s);
                }
                if (s.length < 2){
                    s = "0" + s;
                }
                return "<span style=\"color:" + options.color + "\"><b>" + s + "</b></span>";
            }
            
            
            if (!is_valid()){
                container.html(options.errorMessage);
                return;
            }
            
            var countit = function(){
                var now = new Date();
                var ddiff = until_date - now.getTime();
                secs = parseInt(ddiff/1000);
                if (secs<0){
                    container.html(options.finishMessage);
                    clearInterval(interval);
                    if (options.on_timeup != null){
                        options.on_timeup();
                    }
                    return;
                }
                var display_string = options.displayFormat.replace(/%%D%%/g, calcage(secs, 86400, 100000));
                display_string = display_string.replace(/%%H%%/g, calcage(secs, 3600, 24));
                display_string = display_string.replace(/%%M%%/g, calcage(secs, 60, 60));
                display_string = display_string.replace(/%%S%%/g, calcage(secs, 1, 60));
                container.html(display_string);
            }
            
            countit();
            var interval = setInterval(function(){
                countit();
            }, 1000);
        });
	};	
    
/*----------------------------------------------------------------------------------
Function: hotspot
-------------------------------------------------------------------------------------*/		
	$.fn.hotspot = function(userOptions){
		// Default options
		var options = {
			interval: 1200,
			spot: "<li>",
			color: 'red'
		}
		$.extend(options, userOptions);
		return $(this).each(function(){
            var container = $(this);
            var fadeIn = true;
            var fade_str = '<span class="hotspot_to_fade">' + options.spot + '</span>';
            var html = container.html();
            container.html(fade_str + html);
            
            $('.hotspot_to_fade').css("color", options.color);
            
            var timerId = setInterval(function(){
                if (fadeIn){
                    $('.hotspot_to_fade').fadeTo("slow", 0.1);
                    fadeIn = false;
                } else {
                    $('.hotspot_to_fade').fadeTo("slow", 1.0);				
                    fadeIn = true;
                }
            }, options.interval);
        });
	};
    
/*----------------------------------------------------------------------------------
Function: webticker, liScroll
-------------------------------------------------------------------------------------*/		
	$.fn.liScroll = function(settings) {
		settings = $.extend({
		travelocity: 0.07
		}, settings);		
		return this.each(function(){
			var $strip = $(this);
			$strip.addClass("newsticker")
			var stripWidth = 1;
			$strip.find("li").each(function(i){
				stripWidth += $(this, i).outerWidth(true);
			});
			var $mask = $strip.wrap("<div class='mask'></div>");
			var $tickercontainer = $strip.parent().wrap("<div class='tickercontainer'></div>");								
			var containerWidth = $strip.parent().parent().width();
			$strip.width(stripWidth);			
			var totalTravel = stripWidth+containerWidth;
			var defTiming = totalTravel/settings.travelocity;		
			function scrollnews(spazio, tempo){
				$strip.animate({left: '-='+ spazio}, tempo, "linear", function(){$strip.css("left", containerWidth); scrollnews(totalTravel, defTiming);});
			}
			scrollnews(totalTravel, defTiming);				
			$strip.hover(function(){
				$(this).stop();
			},
			function(){
				var offset = $(this).offset();
				var residualSpace = offset.left + stripWidth;
				var residualTime = residualSpace/settings.travelocity;
				scrollnews(residualSpace, residualTime);
			});	
		});	
	};
})(jQuery);