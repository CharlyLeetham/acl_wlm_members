 jQuery('.cta-btns').on('click', function(e){
        e.preventDefault();
        var action = jQuery(this).attr("data-action");
		var userID = jQuery(this).attr("data-user-id");
		var levelID = jQuery(this).attr("data-level-id");
		console.log (levelID);
		console.log (userID);
		
        jQuery.ajax({
            type : "post",
            dataType : "html",
            url : aclLocalVars.ajax_url,
            data : {action: "acl_wlm_user_action", action: action},
			/* success: function(response) {
               
            },
            complete: function() {
               
            error: function () {
               
            }  */
        })
        // disable button

        // if reply success
        // add percentage * subtotal as saving
        // recalculate total price
        // reset paypal button status
        // reset visa

        // if fail
        // prompt promo code not found

        // reactivate button
    })