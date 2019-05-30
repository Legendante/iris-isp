(function($){
$(document).ready(function(){
	
	if ( self != top ) {
		$.logooos_findNotReadyInserted();
	}
	else {
		var logooos_containers = $('.logooos_container');
	
		if (logooos_containers.length )
		{
			logooos_containers.each(function(){
				$(this).removeClass('logooos_notready');
				$.logooos_run($(this));
			});
		}
	}

});


$.logooos_findNotReadyInserted = function() {
	
	var logooos_containers = $('.logooos_container.logooos_notready');
	
	if (logooos_containers.length )
	{
		logooos_containers.each(function(){
			$(this).removeClass('logooos_notready');
			$.logooos_run($(this));
		});
	}

			
	setTimeout(function() {
		$.logooos_findNotReadyInserted();
	},1000);
}


$.logooos_run = function( logooos_container ) {

		/*======================== Logos ========================*/
		
		var logooos = logooos_container.children('.logooos');
		var logooos_items = logooos.children('.logooos_item');
		var logooos_links = logooos_items.children('a');
		var logooos_images = logooos_items.children('img');
		var logooos_item_height_percentage= 0.65;
        var logooos_lastwindowswidth = $(window).width();
    
		
		if(logooos.hasClass('logooos_list') && logooos.hasClass('logooos_showdetails')) {
			var logooos_morelink = logooos_container.find('.logooos_morelink');
		}
		
		if(logooos.hasClass('logooos_showdetails')) {
			var logooos_detailsarea_closeBtn = logooos_container.find('.logooos_detailsarea_closeBtn');
		}
		
		
		var logooos_detailsarea_page_url = '';
		
		if(logooos.hasClass('logooos_showdetails')) {
			logooos_detailsarea_page_url = logooos.data('detailspageurl');
			logooos.removeAttr('data-detailspageurl');
		}
		
		
		if (logooos.length )
		{

			logooos_calculateItemsWidthAndHight(logooos);
			
			if (logooos.hasClass('logooos_slider'))
			{
				logooos_runSlider(logooos);	
			}

			
			$(window).resize(function() {
                
                if(logooos_lastwindowswidth !== $(window).width()) {
                    
                    logooos_lastwindowswidth = $(window).width();
                    
                    logooos_calculateItemsWidthAndHight(logooos);

                    if (logooos.hasClass('logooos_slider'))
                    {
                        setTimeout(function(){
                            logooos_runSlider(logooos);	
                        },500);
                    }
                }
					
			});
			
			
			// Hover Effects
			
			logooos_items.mouseenter(function(){
				
				if($(this).parent().data('hovereffect')=='effect1') {
					
					$(this).css('box-shadow', '0px 0px 10px 2px '+$(this).parent().data('hovereffectcolor'));
					
				}
				else if($(this).parent().data('hovereffect')=='effect2') {
					
					$(this).children('a').children('.logooos_effectspan').css('box-shadow', 'inset 0px 0px '+$(this).width()/10+'px 3px '+$(this).parent().data('hovereffectcolor'));
					
				}
				else if($(this).parent().data('hovereffect')=='effect3') {
					$(this).css('border-color', $(this).parent().data('hovereffectcolor'));
				}
				else if($(this).parent().data('hovereffect')=='effect4') {
					
					$(this).parent().children('.logooos_item').stop().animate({opacity: 0.3},300);
					
					if($(this).parent().hasClass('logooos_list')) {
						$(this).parent().children('.logooos_textcontainer').stop().animate({opacity: 0.3},300);
						$(this).next().stop().animate({opacity: 1},300);
					}
					
					$(this).stop().animate({opacity: 1},300);
				}
				
			});
			
			logooos_items.mouseleave(function(){
				if($(this).parent().data('hovereffect')=='effect1') {
					$(this).css('box-shadow', '');
				}
				else if($(this).parent().data('hovereffect')=='effect2') {
					$(this).children('a').children('.logooos_effectspan').css('box-shadow', '');
				}
				else if($(this).parent().data('hovereffect')=='effect3') {
					$(this).css('border-color', $(this).parent().data('bordercolor'));
				}
				else if($(this).parent().data('hovereffect')=='effect4') {
					$(this).parent().children('.logooos_item').stop().animate({opacity: 1},300);
					
					if($(this).parent().hasClass('logooos_list')) {
						$(this).parent().children('.logooos_textcontainer').stop().animate({opacity: 1},300);
					}
				}

			});
			
			// Tooltip
			
			if(logooos.hasClass('logooos_withtooltip')) {
				
				logooos_items.mouseenter(function(){
					
					var tooltips=$('.logooos_tooltip');
					if(tooltips.length) {
						tooltips.remove();
					}
					
					if($(this).data('title')!='') 
					{
						var tooltip=$('<div class="logooos_tooltip"><span class="logooos_tooltipText">'+$(this).data('title')+'<span class="logooos_tooltipArrow"></span></span></div>');
						tooltip.appendTo('body');
							
						tooltip.css('opacity',0);
							
						var arrowBgPosition='';
							
						// Left
						if($(this).offset().left + $(this).width()/2 - tooltip.width()/2 < 0) {
							tooltip.css('left', 1 );
							arrowBgPosition = $(this).offset().left + $(this).width()/2 - 11 +'px';
						}
						else if($(this).offset().left + $(this).width()/2 - tooltip.width()/2 +tooltip.width() > $(window).width()) {
							tooltip.css('right', 1 );
							arrowBgPosition = $(this).offset().left - tooltip.offset().left + $(this).width()/2 - 11 +'px';
						}
						else {
							tooltip.css('left', $(this).offset().left + $(this).width()/2 - tooltip.width()/2 );
							arrowBgPosition='center';
						}
							
						// Top
						if($(window).scrollTop() > $(this).offset().top - tooltip.height()) {
							tooltip.css('top', $(this).offset().top + $(this).height()+13);
							arrowBgPosition+=' top';
							tooltip.find('.logooos_tooltipArrow').css({'background-position': arrowBgPosition, 'bottom': '100%'});
						}
						else {
							tooltip.css('top', $(this).offset().top - tooltip.height()+9);
							arrowBgPosition+=' bottom';
							tooltip.find('.logooos_tooltipArrow').css({'background-position': arrowBgPosition, 'top': '100%'});
						}
							
						// Show
						if( $(this).offset().left < $(this).parent().parent().offset().left + $(this).parent().parent().width()) {
							tooltip.animate({opacity:1,top:'-=10px'},'slow');
						}
					}
						
				});
					
				// Remove Tooltip
				logooos_items.mouseleave(function(){
					var tooltips=$('.logooos_tooltip');
					if(tooltips.length) {
						tooltips.remove();
					}
				});
			
			}
			
			
		}
		
		
		// Details Area
		
		if(logooos.hasClass('logooos_slider') && logooos.hasClass('logooos_showdetails')) {
		
			logooos_items.click(function() {
					
				var logooos_logoid = $(this).data('id');
				var logooos_selectedlogo = $(this);
				var logooos_detailsarea = $(this).parent().parent().parent().children('.logooos_detailsarea');
				var logooos_detailsarea_container = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container');
				var logooos_detailsarea_img = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_img');
				var logooos_detailsarea_title = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_title');
				var logooos_detailsarea_text = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_text');
				
				
				logooos_selectedlogo.parent().children('.logooos_item').removeClass('logooos_loading');
				logooos_selectedlogo.addClass('logooos_loading');
					
				logooos_detailsarea.slideUp('slow',function(){
					$.get(logooos_detailsarea_page_url, { logo_id: logooos_logoid } , function(data){

						logooos_detailsarea_img.css({'backgroundImage': 'url('+data.thumbnailsrc+')', 'backgroundSize': data.bgSize});
						logooos_detailsarea_title.text(data.title);
						logooos_detailsarea_text.html(data.text);
							
						logooos_detailsarea_title.css('paddingTop',0);
							
						logooos_detailsarea.slideDown('slow');
							
						if(logooos_selectedlogo.parent().data('itemsheightpercentage')!='') {
							logooos_item_height_percentage = logooos_selectedlogo.parent().data('itemsheightpercentage');
						}
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {
							logooos_detailsarea_img.height(logooos_detailsarea_img.width()*logooos_item_height_percentage);
						}
							
						logooos_selectedlogo.removeClass('logooos_loading');
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {
							if((logooos_detailsarea_text.height()+logooos_detailsarea_title.height()) < logooos_detailsarea_img.height()) {
								logooos_detailsarea_title.css('paddingTop', (logooos_detailsarea_img.height() - (logooos_detailsarea_text.height()+logooos_detailsarea_title.height()+15))/2)
							}
						}
							
					}, 'json');
				});
					
				return false;
					
			});
			
		}
			
		
		if(logooos.hasClass('logooos_grid') && logooos.hasClass('logooos_showdetails')) {
		
			logooos_items.click(function() {
					
				var logooos_logoid = $(this).data('id');
					
				var logooos_loopindex = 1;
				var logooos_selectedlogo = $(this);
				var logooos_nextlogo = $(this).next('div');
				var logooos_detailsarea = $(this).parent().children('.logooos_detailsarea');
				var logooos_detailsarea_container = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container');
				var logooos_detailsarea_img = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_img');
				var logooos_detailsarea_title = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_title');
				var logooos_detailsarea_text = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_text');
				
				
				logooos_selectedlogo.parent().children('.logooos_item').removeClass('logooos_loading');
				logooos_selectedlogo.addClass('logooos_loading');
					
				logooos_detailsarea.slideUp('slow',function(){
					
					while(logooos_loopindex == 1 ) {
							
						if(logooos_nextlogo.length == 1) {
								
							if(logooos_selectedlogo.offset().top != logooos_nextlogo.offset().top) {
									
								logooos_detailsarea.insertBefore( logooos_nextlogo );
								logooos_loopindex= 0;
									
							}
							else {
								logooos_nextlogo = logooos_nextlogo.next('div');
							}
								
						}
						else if(logooos_nextlogo.length == 0) {

							logooos_detailsarea.insertAfter( logooos_selectedlogo.parent().children('div:last-child') );
							logooos_loopindex= 0;

						}
							
					}
					
						
					$.get(logooos_detailsarea_page_url, { logo_id: logooos_logoid } , function(data){

						logooos_detailsarea_img.css({'backgroundImage': 'url('+data.thumbnailsrc+')', 'backgroundSize': data.bgSize});
						logooos_detailsarea_title.text(data.title);
						logooos_detailsarea_text.html(data.text);
							
						logooos_detailsarea_title.css('paddingTop',0);
							
						logooos_detailsarea.slideDown('slow');
							
						if(logooos_selectedlogo.parent().data('itemsheightpercentage')!='') {
							logooos_item_height_percentage = logooos_selectedlogo.parent().data('itemsheightpercentage');
						}
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {
							logooos_detailsarea_img.height(logooos_detailsarea_img.width()*logooos_item_height_percentage);
						}
						
						logooos_selectedlogo.removeClass('logooos_loading');
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {
							if((logooos_detailsarea_text.height()+logooos_detailsarea_title.height()) < logooos_detailsarea_img.height()) {
								logooos_detailsarea_title.css('paddingTop', (logooos_detailsarea_img.height() - (logooos_detailsarea_text.height()+logooos_detailsarea_title.height()+15))/2)
							}
						}
							
					}, 'json');
						
				});
					
					
				return false;
					
			});
		}
		
		
		if(logooos.hasClass('logooos_list') && logooos.hasClass('logooos_showdetails')) {		
			
			logooos_items.click(function() {
					
				var logooos_logoid = $(this).data('id');
				var logooos_selectedlogo = $(this);
				var logooos_detailsarea = $(this).parent().children('.logooos_detailsarea');
				var logooos_detailsarea_container = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container');
				var logooos_detailsarea_img = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_img');
				var logooos_detailsarea_title = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_title');
				var logooos_detailsarea_text = $(this).parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_text');
			
				
				logooos_selectedlogo.parent().children('.logooos_item').removeClass('logooos_loading');
				logooos_selectedlogo.addClass('logooos_loading');
					
				logooos_detailsarea.slideUp('slow',function(){
						
						
					logooos_selectedlogo.parent().children('div.logooos_item').fadeIn('slow');
					logooos_selectedlogo.parent().children('.logooos_textcontainer').children('.logooos_title, .logooos_text').slideDown('slow');
					logooos_selectedlogo.parent().children('.logooos_textcontainer').removeClass('logooos_withoutMinHeight');
							
					logooos_detailsarea.insertBefore( logooos_selectedlogo );
						
					$.get(logooos_detailsarea_page_url, { logo_id: logooos_logoid } , function(data){

						logooos_detailsarea_img.css({'backgroundImage': 'url('+data.thumbnailsrc+')', 'backgroundSize': data.bgSize});
						logooos_detailsarea_title.text(data.title);
						logooos_detailsarea_text.html(data.text);
							
						logooos_detailsarea_title.css('paddingTop',0);
							
							
						logooos_selectedlogo.css('display','none');
						logooos_selectedlogo.next('div').children('.logooos_title, .logooos_text').css('display','none');
						logooos_selectedlogo.next('div').addClass('logooos_withoutMinHeight');
							
							
						logooos_detailsarea.slideDown('slow');
							
						if(logooos_selectedlogo.parent().data('itemsheightpercentage')!='') {
							logooos_item_height_percentage = logooos_selectedlogo.parent().data('itemsheightpercentage');
						}
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {						
							logooos_detailsarea_img.height(logooos_detailsarea_img.width()*logooos_item_height_percentage);
						}
						
						logooos_selectedlogo.removeClass('logooos_loading');
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {
							if((logooos_detailsarea_text.height()+logooos_detailsarea_title.height()) < logooos_detailsarea_img.height()) {
								logooos_detailsarea_title.css('paddingTop', (logooos_detailsarea_img.height() - (logooos_detailsarea_text.height()+logooos_detailsarea_title.height()+15))/2)
							}
						}
							
					}, 'json');
				});
					
				return false;
			});
		
		}	
			
			
			
		if(logooos.hasClass('logooos_list') && logooos.hasClass('logooos_showdetails')) {	
			
			logooos_morelink.click(function() {
					
				var logooos_logoid = $(this).parent().parent().prev('.logooos_item').data('id');
				var logooos_selectedlogo = $(this).parent().parent().prev('.logooos_item');
				var logooos_detailsarea = $(this).parent().parent().parent().children('.logooos_detailsarea');
				var logooos_detailsarea_container = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container');
				var logooos_detailsarea_img = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_img');
				var logooos_detailsarea_title = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_title');
				var logooos_detailsarea_text = $(this).parent().parent().parent().children('.logooos_detailsarea').children('.logooos_detailsarea_container').children('.logooos_detailsarea_text');
				
				
				logooos_selectedlogo.parent().children('.logooos_item').removeClass('logooos_loading');
				logooos_selectedlogo.addClass('logooos_loading');
					
				logooos_detailsarea.slideUp('slow',function(){
						
						
					logooos_selectedlogo.parent().children('div.logooos_item').fadeIn('slow');
					logooos_selectedlogo.parent().children('.logooos_textcontainer').children('.logooos_title, .logooos_text').slideDown('slow');
					logooos_selectedlogo.parent().children('.logooos_textcontainer').removeClass('logooos_withoutMinHeight');
							
					logooos_detailsarea.insertBefore( logooos_selectedlogo );
						
					$.get(logooos_detailsarea_page_url, { logo_id: logooos_logoid } , function(data){

						logooos_detailsarea_img.css({'backgroundImage': 'url('+data.thumbnailsrc+')', 'backgroundSize': data.bgSize});
						logooos_detailsarea_title.text(data.title);
						logooos_detailsarea_text.html(data.text);
							
						logooos_detailsarea_title.css('paddingTop',0);
							
							
						logooos_selectedlogo.css('display','none');
						logooos_selectedlogo.next('div').children('.logooos_title, .logooos_text').css('display','none');
						logooos_selectedlogo.next('div').addClass('logooos_withoutMinHeight');
							
							
						logooos_detailsarea.slideDown('slow');
							
						if(logooos_selectedlogo.parent().data('itemsheightpercentage')!='') {
							logooos_item_height_percentage = logooos_selectedlogo.parent().data('itemsheightpercentage');
						}
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {
							logooos_detailsarea_img.height(logooos_detailsarea_img.width()*logooos_item_height_percentage);
						}
						
						logooos_selectedlogo.removeClass('logooos_loading');
						
						if(logooos_detailsarea.hasClass('logooos_withoutLogo')==false) {
							if((logooos_detailsarea_text.height()+logooos_detailsarea_title.height()) < logooos_detailsarea_img.height()) {
								logooos_detailsarea_title.css('paddingTop', (logooos_detailsarea_img.height() - (logooos_detailsarea_text.height()+logooos_detailsarea_title.height()+15))/2)
							}
						}
							
					}, 'json');
				});
					
				return false;
			});
		
		}		
			
			
			
			
			
		if(logooos.hasClass('logooos_showdetails')) {
		
			logooos_detailsarea_closeBtn.click(function() {
				$(this).parent().stop().slideUp('slow', function(){
						
					if($(this).parent().hasClass('logooos_list')) {
						$(this).parent().children('div.logooos_item, div.logooos_textcontainer .logooos_title, div.logooos_textcontainer .logooos_text').css('display','block');
							
						$(this).parent().children('div.logooos_item').fadeIn('slow');
						$(this).parent().children('.logooos_textcontainer').children('.logooos_title, .logooos_text').slideDown('slow');
							
						$(this).parent().children('.logooos_textcontainer').removeClass('logooos_withoutMinHeight');
					}
					
				});
					
				return false;
			});
			
		}
			
			
}




function logooos_calculateItemsWidthAndHight(list) {
	
	if(list.data('itemsheightpercentage')!='') {
		var logooos_item_height_percentage = list.data('itemsheightpercentage');
	}
	else {
		var logooos_item_height_percentage= 0.65;
	}
	
	var logooos_itemBorderLeftRight = parseInt(list.children('.logooos_item').css('borderLeftWidth').replace('px', ''))+parseInt(list.children('.logooos_item').css('borderRightWidth').replace('px', ''));
	
	
	if(list.hasClass('logooos_grid') || list.hasClass('logooos_slider')) {
		
		
		
		if(list.hasClass('logooos_grid')) {
			list.parent().width('auto');
			
			if(list.hasClass('logooos_showdetails')) {
				list.children('.logooos_detailsarea').css('display','none');
				list.children('.logooos_detailsarea').insertAfter(list.children('div:last-child'));
				
				if(list.parent().width() < 481) {
					list.children('.logooos_detailsarea').addClass('logooos_small_width');
				}
				else {
					list.children('.logooos_detailsarea').removeClass('logooos_small_width');
				}
			}
		}
		
		if(list.hasClass('logooos_slider')) {
			list.parents('.logooos_container').width('auto');
			
			if(list.hasClass('logooos_showdetails')) {
				list.parents('.logooos_container').children('.logooos_detailsarea').css('display','none');
				
				if(list.parents('.logooos_container').width() < 481) {
					list.parents('.logooos_container').children('.logooos_detailsarea').addClass('logooos_small_width');
				}
				else {
					list.parents('.logooos_container').children('.logooos_detailsarea').removeClass('logooos_small_width');
				}
			}
		}
		
		if(list.data('marginBetweenItems')!='') {
			list.children('.logooos_item').css('margin', Math.floor(parseFloat(list.data('marginbetweenitems'))/2));
		}
							
		var logooos_itemMarginLeftRight = parseFloat(list.children('.logooos_item').css('marginLeft').replace('px', ''))+parseFloat(list.children('.logooos_item').css('marginRight').replace('px', ''));		
				
		if( $(window).width() >= 1024 || !list.hasClass('logooos_responsive') ) {
			list.parent().width(Math.floor(list.width()/list.data('columnsnum'))*list.data('columnsnum'));
			list.children('.logooos_item').width(Math.floor(list.width()/list.data('columnsnum'))-(logooos_itemMarginLeftRight+logooos_itemBorderLeftRight) );
		}
		else if( $(window).width() < 1024 && $(window).width() >= 481) {
			var windowHeight = $(window).height();
			var windowWidth = $(window).width();
						
			if(windowHeight < windowWidth && list.data('columnsnum') > 4) {
				list.parent().width(Math.floor(list.width()/4)*4);
				list.children('.logooos_item').width(Math.floor(list.width()/4)-(logooos_itemMarginLeftRight+logooos_itemBorderLeftRight) );
			}
			else if(windowHeight > windowWidth && list.data('columnsnum') > 3) {
				list.parent().width(Math.floor(list.width()/3)*3);
				list.children('.logooos_item').width(Math.floor(list.width()/3)-(logooos_itemMarginLeftRight+logooos_itemBorderLeftRight) );
			}
			else {
				list.parent().width(Math.floor(list.width()/list.data('columnsnum'))*list.data('columnsnum'));
				list.children('.logooos_item').width(Math.floor(list.width()/list.data('columnsnum'))-(logooos_itemMarginLeftRight+logooos_itemBorderLeftRight) );
			}
		}
		else if( $(window).width() < 481 && list.data('columnsnum') > 2 ) {
			list.parent().width(Math.floor(list.width()/2)*2);
			list.children('.logooos_item').width(Math.floor(list.width()/2)-(logooos_itemMarginLeftRight+logooos_itemBorderLeftRight) );
		}
		else {
			list.parent().width(Math.floor(list.width()/list.data('columnsnum'))*list.data('columnsnum'));
			list.children('.logooos_item').width(Math.floor(list.width()/list.data('columnsnum'))-(logooos_itemMarginLeftRight+logooos_itemBorderLeftRight) );
		}
					
					
					
	}
	else if(list.hasClass('logooos_list')) {
		
		if(list.hasClass('logooos_showdetails')) {
			list.children('.logooos_detailsarea').css('display','none');
			list.find('.logooos_title, .logooos_text').css('display','block');
			list.find('.logooos_textcontainer.logooos_withoutMinHeight').removeClass('logooos_withoutMinHeight');
			
			if(list.parent().width() < 481) {
				list.children('.logooos_detailsarea').addClass('logooos_small_width');
			}
			else {
				list.children('.logooos_detailsarea').removeClass('logooos_small_width');
			}
		}	
		
		if( list.parent().width() < 481 ) {
			list.children('.logooos_item').width(Math.floor(list.width())-logooos_itemBorderLeftRight ).css({'marginBottom':30, 'float':'none'});
			list.children('.logooos_item').height(parseInt(list.children('.logooos_item').width()*logooos_item_height_percentage));
			list.children('.logooos_textcontainer').css('min-height',0);
			list.children('.logooos_textcontainer').children('.logooos_text, .logooos_title').css({'marginLeft':0});
		}
		else {
			list.children('.logooos_item').width(180).css({'marginBottom':0, 'float':'left'});
			list.children('.logooos_item').height(parseInt(list.children('.logooos_item').width()*logooos_item_height_percentage));
			list.children('.logooos_textcontainer').css('min-height',list.children('.logooos_item').height()+logooos_itemBorderLeftRight);
			list.children('.logooos_textcontainer').children('.logooos_text, .logooos_title').css({'marginLeft':210});
		}
		
			
	}
		
	list.children('.logooos_item').height(parseInt(list.children('.logooos_item').width()*logooos_item_height_percentage));	
	
	list.children('.logooos_item').css('display','inline-block');
	
}




function logooos_runSlider(slider) {
	
	
			var min=slider.data('columnsnum');
			var max=slider.data('columnsnum');
			var pauseOnHover = true;
			
			if(slider.data('itemsheightpercentage')!='') {
				var logooos_item_height_percentage = slider.data('itemsheightpercentage');
			}
			else {
				var logooos_item_height_percentage= 0.65;
			}
			
			if(slider.hasClass('logooos_responsive')) {
			
				if( $(window).width() <= 480 ) {
					min=1;
					max=1;
				}
				else if($(window).width() > 480 &&  $(window).width() < 600 && slider.data('columnsnum') > 3 ) {		
					min=3;
					max=3;
				}
				else if($(window).width() > 600 &&  $(window).width() < 1024 && slider.data('columnsnum') > 4 ) {
					min=4;
					max=4;
				}
				
			}
			
			
			if(slider.data('pauseduration')=='0') {
				pauseOnHover = 'immediate-resume';
			}
				
			
			slider.carouFredSel({
				responsive: true,
				width:'100%',
				circular:slider.data('circular'),
				infinite:true,
				prev: {
					button: function() {
						if(slider.data('pauseduration')=='0') {
							return null;
						}
						else {
							$(this).parent().append('<a class="logooos_prev '+$(this).data('buttonsarrowscolor')+'" style="background-color:'+$(this).data('buttonsbgcolor')+';border-color:'+$(this).data('buttonsbordercolor')+';" href="#"></a>');
							return $(this).parents().children(".logooos_prev");
						}
					}
				},
				next: {
					button: function() {
						if(slider.data('pauseduration')=='0') {
							return null;
						}
						else {
							$(this).parent().append('<a class="logooos_next '+$(this).data('buttonsarrowscolor')+'" style="background-color:'+$(this).data('buttonsbgcolor')+';border-color:'+$(this).data('buttonsbordercolor')+';" href="#"></a>');
							return $(this).parents().children(".logooos_next");
						}
					}
				},
				pagination: {
					container: function() {
						if(slider.data('pagination')=='enabled' && slider.data('pauseduration')!='0') {
							return $(this).parents().next(".logooos_slider_pagination");
						}
					},
					anchorBuilder: function() {
						return '<span style="background-color:'+slider.data('paginationcolor')+';border-color:'+slider.data('paginationcolor')+';"></span>';
					}
				},
				scroll: {
					items:function(num) {
						if(num==1) {
							return 1;
						}
						else if(num>=2 && num<=5) {
							return 2;
						}
						else if(num>=6 && num<=7) {
							return 3;
						}
						else if(num>=8 && num<=9) {
							return 4;
						}
						else if(num>=10) {
							return 5;
						}
					},
					easing:slider.data('easingfunction'),
					duration: slider.data('scrollduration'),
					fx: slider.data('transitioneffect')
				},
				items: {
					width: 200,
					visible: {
						min: min,
						max: max
					}
				},
				auto: {
					play: slider.data('autoplay'),
					timeoutDuration: slider.data('pauseduration'),
					pauseOnHover: pauseOnHover
				},
				swipe: {
					onMouse: false,
					onTouch: true
				}
			});
			
			if( $(window).width() > 1024 && slider.data('pauseduration')!='0') {
				slider.parents('.caroufredsel_wrapper').mouseenter(function(){
					$(this).children(".logooos_prev").fadeIn('slow');
					$(this).children(".logooos_next").fadeIn('slow');
				});
				
				slider.parents('.caroufredsel_wrapper').mouseleave(function(){
					$(this).children(".logooos_prev").fadeOut('slow');
					$(this).children(".logooos_next").fadeOut('slow');
				});
			}
			
			var logooos_itemMarginTopBottom = parseFloat(slider.children('.logooos_item').css('marginLeft').replace('px', ''))+parseFloat(slider.children('.logooos_item').css('marginRight').replace('px', ''));
			var logooos_itemBorderTopBottom = parseInt(slider.children('.logooos_item').css('borderLeftWidth').replace('px', ''))+parseInt(slider.children('.logooos_item').css('borderRightWidth').replace('px', ''));
			
			slider.children('.logooos_item').height(parseInt(slider.children('.logooos_item').width()*logooos_item_height_percentage));
			
			if(logooos_itemBorderTopBottom >= 1) {
				slider.parent().height(parseInt(slider.children('.logooos_item').width()*logooos_item_height_percentage + logooos_itemMarginTopBottom + logooos_itemBorderTopBottom +1));
			}
			else {
				slider.parent().height(parseInt(slider.children('.logooos_item').width()*logooos_item_height_percentage + logooos_itemMarginTopBottom + logooos_itemBorderTopBottom ));
			}
			
			slider.height(parseInt(slider.children('.logooos_item').height()+ logooos_itemMarginTopBottom + logooos_itemBorderTopBottom));
			
			if(logooos_itemBorderTopBottom >= 1) {
				slider.parent().height(parseInt(slider.children('.logooos_item').height()+ logooos_itemMarginTopBottom + logooos_itemBorderTopBottom +1));
				slider.parent().width(slider.parent().width()+1);
			}
			else {
				slider.parent().height(parseInt(slider.children('.logooos_item').height()+ logooos_itemMarginTopBottom + logooos_itemBorderTopBottom ));
				slider.parent().width(slider.parent().width());
			}
				
			if(slider.data('pauseduration')!='0') {
				logooos_prev=slider.parents().children(".logooos_prev");
				logooos_prev.css('top',slider.parents().height()/2 - logooos_prev.height()/2 );
				logooos_prev.css('display','none');
							
				logooos_next=slider.parents().children(".logooos_next");
				logooos_next.css('top',slider.parents().height()/2 - logooos_next.height()/2 );
				logooos_next.css('display','none');
			}
			
}

})(jQuery);