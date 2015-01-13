jQuery(function () {
  jQuery('.moreInfo').each(function () {
    // options
    var distance = 10;
    var time = 250;
    var hideDelay = 200;

    var hideDelayTimer = null;

    // tracker
    var beingShown = false;
    var shown = false;
    
    var trigger = jQuery('.trigger', this);
    var tooltip = jQuery('.tooltip', this).css('opacity', 0);
	
    // set the mouseover and mouseout on both element
    jQuery([trigger.get(0), tooltip.get(0)]).mouseover(function () {
      // stops the hide event if we move from the trigger to the tooltip element
      if (hideDelayTimer) clearTimeout(hideDelayTimer);

      // don't trigger the animation again if we're being shown, or already visible
      if (beingShown || shown) {
        return;
      } else {
        beingShown = true;

        // reset position of tooltip box
        tooltip.css({
          display: 'block' // brings the tooltip back in to view
        })

        // (we're using chaining on the tooltip) now animate it's opacity and position
        .animate({
          /*top: '-=' + distance + 'px',*/
          opacity: 1
        }, time, 'swing', function() {
          // once the animation is complete, set the tracker variables
          beingShown = false;
          shown = true;
        });
      }
    }).mouseout(function () {
      // reset the timer if we get fired again - avoids double animations
      if (hideDelayTimer) clearTimeout(hideDelayTimer);
      
      // store the timer so that it can be cleared in the mouseover if required
      hideDelayTimer = setTimeout(function () {
        hideDelayTimer = null;
        tooltip.animate({
          /*top: '-=' + distance + 'px',*/
          opacity: 0
        }, time, 'swing', function () {
          // once the animate is complete, set the tracker variables
          shown = false;
          // hide the tooltip entirely after the effect (opacity alone doesn't do the job)
          tooltip.css('display', 'none');
        });
      }, hideDelay);
    });
  });
/* Added for validations - Start */	
	jQuery('#glam_slider_form').submit(function(event) { 
			var speed=jQuery("#glam_slider_speed").val();
			if(speed=='' || speed <= 0 || isNaN(speed)) {
					alert("Speed of Transition should be a number greater than 0!"); 
					jQuery("#glam_slider_speed").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_speed').offset().top-50}, 600);
					return false;
				}	
			var time=jQuery("#glam_slider_time").val();
			if(time=='' || time <= 0 || isNaN(time)) {
					alert("Time between Transitions should be a number greater than 0!"); 
					jQuery("#glam_slider_time").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_time').offset().top-50}, 600);
					return false;
				}
			var posts=jQuery("#glam_slider_no_posts").val();
			if(posts=='' || posts <= 0 || isNaN(posts)) {
					alert("Number of Posts in the Glam Slider should be a number greater than 0!"); 
					jQuery("#glam_slider_no_posts").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_no_posts').offset().top-50}, 600);
					return false;
				}
			var visible=jQuery("#glam_slider_visible").val();
			if(visible=='' || visible <= 0 || isNaN(visible)) {
					alert("Number of Items Visible should be a number greater than 0!"); 
					jQuery("#glam_slider_visible").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_visible').offset().top-50}, 600);
					return false;
			}
			var width=jQuery("#glam_slider_width").val();
			if(width=='' || width < 0 || isNaN(width)) {
					alert("Maximum Slider Width should be a number greater than or equal to 0!"); 
					jQuery("#glam_slider_width").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_width').offset().top-50}, 600);
					return false;
				}
			var height=jQuery("#glam_slider_height").val();
			if(height=='' || height <= 0 || isNaN(height)) {
					alert("Maximum Slider Height should be a number greater than 0!"); 
					jQuery("#glam_slider_height").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_height').offset().top-50}, 600);
					return false;
				}
			var iwidth=jQuery("#glam_slider_iwidth").val();
			if(iwidth=='' || iwidth <=0 || isNaN(iwidth)) {
					alert("Center Slide Width should be a number greater 0!"); 
					jQuery("#glam_slider_iwidth").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_iwidth').offset().top-50}, 600);
					return false;
				}
			var swidth=jQuery("#glam_slider_swidth").val();
			if(swidth=='' || swidth <=0 || isNaN(swidth)) {
					alert("Side Slides Width should be a number greater 0!"); 
					jQuery("#glam_slider_swidth").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_swidth').offset().top-50}, 600);
					return false;
				}
			//
			var numpost=jQuery("#glam_slider_no_posts").val();
			var visiblepost=jQuery("#glam_slider_visible").val();
			if(parseInt(numpost) < parseInt(visiblepost) ) { 
					alert("Number of posts should be greater than or equal to Visible posts"); 
					jQuery("#glam_slider_no_posts").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_no_posts').offset().top-50}, 600);
					return false;
				}

			//for Quick embed shortcode popup
			var slider_id = jQuery("#glam_slider_id").val(),	
			    hiddensliderid=jQuery("#hidden_sliderid").val(),		
			    slider_catslug=jQuery("#glam_slider_catslug").val(),
			    hiddencatslug=jQuery("#hidden_category").val(),
			    prev=jQuery("#glam_slider_preview").val(),
			    hiddenpreview=jQuery("#hidden_preview").val(),
			    new_save=jQuery("#oldnew").val();
			if(prev=='1' && slider_catslug=='') {
				alert("Select the category whose posts you want to show!"); 
				jQuery("#glam_slider_catslug").addClass('error');
				jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_catslug').offset().top-50}, 600);
				return false;
			}
			if(prev=='0') {
				if(slider_id=='' || isNaN(slider_id) || slider_id<=0){
					alert("Select the slider name!"); 
					jQuery("#glam_slider_id").addClass('error');
					jQuery("html,body").animate({scrollTop:jQuery('#glam_slider_id').offset().top-50}, 600);
					return false;
				}
			}
			if(hiddenpreview != prev || new_save=='1' || slider_id != hiddensliderid || slider_catslug != hiddencatslug ) jQuery('#glampopup').val("1");					
			else jQuery('#glampopup').val("0");	
		});
		/* Added for validations - end */
		/* Added for preview - start */
		var selpreview=jQuery("#glam_slider_preview").val();
		if(selpreview=='2')
			jQuery("#glam_slider_form .form-table tr.glam_slider_params").css("display","none");
		else if(selpreview=='1'){
			jQuery("#glam_slider_form .glam_sid").css("display","none");
			jQuery("#glam_slider_form .form-table tr.glam_slider_params").css("display","table-row");
			jQuery("#glam_slider_form .glam_catslug").css("display","block");
		}
		else if(selpreview=='0'){
			jQuery("#glam_slider_form .glam_catslug").css("display","none");
			jQuery("#glam_slider_form .form-table tr.glam_slider_params").css("display","table-row");
			jQuery("#glam_slider_form .glam_sid").css("display","block");
		}
		/* Added for preview - end */
		/* Show Or Hide Navigation Arrow */
		jQuery(".switchShow").on("click", function(){
			jQuery(this).addClass("showSelected");
			var switchhide=jQuery(this).siblings(".switchHide");
			switchhide.removeClass("hideSelected");
			jQuery(this).siblings(".showHideSwitch-checkbox").val(1);
			  
		});
		jQuery(".switchHide").on("click", function(){
			jQuery(this).addClass("hideSelected");
			var switchshow=jQuery(this).siblings(".switchShow");
			jQuery(switchshow).removeClass("showSelected");
			jQuery(this).siblings(".showHideSwitch-checkbox").val(0);
		});
});

/* Added for preview */
function checkpreview(curr_preview){
	if(curr_preview=='2')
		jQuery("#glam_slider_form .form-table tr.glam_slider_params").css("display","none");
	else if(curr_preview=='1'){
		jQuery("#glam_slider_form .glam_sid").css("display","none");
		jQuery("#glam_slider_form .form-table tr.glam_slider_params").css("display","table-row");
		jQuery("#glam_slider_form .glam_catslug").css("display","block");
	}
	else if(curr_preview=='0'){
		jQuery("#glam_slider_form .glam_catslug").css("display","none");
		jQuery("#glam_slider_form .form-table tr.glam_slider_params").css("display","table-row");
		jQuery("#glam_slider_form .glam_sid").css("display","block");
	}
}
/* End Of preview */

