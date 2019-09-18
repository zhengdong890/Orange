$(function(){
			
			var $subblock = $(".subpage"), $head=$subblock.find('h2'), $ul = $("#proinfo"), $lis = $ul.find("li"), inter=false,ulinter=false;
			
		$('#pros_shop_css').mouseover(function(){
			
			$ul.show();
		});
			
		$('#pros_shop_css').mouseout(function(){
			
			$ul.hide();
		});
		

	
	    $ul.click(function(event){
				event.stopPropagation();
			});
			
			$(document).click(function(){
				$ul.hide();
				inter=!inter;
			});

			$lis.hover(function(){
				if(!$(this).hasClass('nochild')){
					$(this).addClass("prosahover");
					$(this).find(".prosmore").removeClass('hide');
				}
			},function(){
				if(!$(this).hasClass('nochild')){
					if($(this).hasClass("prosahover")){
						$(this).removeClass("prosahover");
					}
					$(this).find(".prosmore").addClass('hide');
				}
			});
						
			
			var shop_css = "{$shop_css}";	
          		
			$('.navwrap').css('background','#'+shop_css);
			$('.pros').css('background','#'+shop_css);
				
		})();
		
		