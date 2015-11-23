/* Used to show and hide the admin tabs for FEUP */
function ShowTab(TabName) {
		jQuery(".OptionTab").each(function() {
				jQuery(this).addClass("HiddenTab");
				jQuery(this).removeClass("ActiveTab");
		});
		jQuery("#"+TabName).removeClass("HiddenTab");
		jQuery("#"+TabName).addClass("ActiveTab");
		
		jQuery(".nav-tab").each(function() {
				jQuery(this).removeClass("nav-tab-active");
		});
		jQuery("#"+TabName+"_Menu").addClass("nav-tab-active");
}

function ShowMoreOptions() {
	jQuery(".feup-email-advanced-settings").toggle();
	jQuery(".feup-email-toggle-show").toggle();
	jQuery(".feup-email-toggle-hide").toggle();

	return false;
}

function ShowOptionTab(TabName) {
	jQuery(".feup-option-set").each(function() {
		jQuery(this).addClass("feup-hidden");
	});
	jQuery("#"+TabName).removeClass("feup-hidden");
	
	jQuery(".options-subnav-tab").each(function() {
		jQuery(this).removeClass("options-subnav-tab-active");
	});
	jQuery("#"+TabName+"_Menu").addClass("options-subnav-tab-active");
}

jQuery(document).ready(function() {	
	jQuery('.ewd-feup-one-click-install-div-load').on('click', function() {
		jQuery('#ewd-feup-one-click-install-div').removeClass('ewd-feup-oci-no-show');
		jQuery('#ewd-feup-one-click-install-div').addClass('ewd-feup-oci-main-event');
		jQuery('#ewd-feup-one-click-blur').addClass('ewd-feup-grey-out');
		jQuery('#ewd-feup-one-click-blur').width(jQuery('#ewd-feup-one-click-blur').width() + 180);
	});

	jQuery('#ewd-feup-one-click-blur').on('click', function() {
		jQuery('#ewd-feup-one-click-install-div').addClass('ewd-feup-oci-no-show');
		jQuery('#ewd-feup-one-click-install-div').removeClass('ewd-feup-oci-main-event');
		jQuery('#ewd-feup-one-click-blur').removeClass('ewd-feup-grey-out');
	});

});

/* This code is required to make changing the field order a drag-and-drop affair */
jQuery(document).ready(function() {	
	jQuery('.fields-list').sortable({
		items: '.list-item',
		opacity: 0.6,
		cursor: 'move',
		axis: 'y',
		update: function() {
			var order = jQuery(this).sortable('serialize') + '&action=ewd_feup_update_field_order';
			jQuery.post(ajaxurl, order, function(response) {});
		}
	});

	/*jQuery('.levels-list').sortable({
		items: '.list-item',
		opacity: 0.6,
		cursor: 'move',
		axis: 'y',
		update: function() {
			var order = jQuery(this).sortable('serialize') + '&action=ewd_feup_update_levels_order';
			alert(order);
			jQuery.post(ajaxurl, order, function(response) {alert(response);});
		}
	});*/
});

//This still needs adapting x2
jQuery(document).ready(function() {
	SetDiscountDeleteHandlers();

	jQuery('.ewd-feup-add-discount-code').on('click', function(event) {
		var ID = jQuery(this).data('nextid');

		var HTML = "<tr id='ewd-feup-discount-code-row-" + ID + "'>";
		HTML += "<td><a class='ewd-feup-delete-discount-code' data-reminderID='" + ID + "'>Delete</a></td>";
		HTML += "<td><input type='text' name='Discount_Code_" + ID + "_Code'></td>";
		HTML += "<td><input type='text' name='Discount_Code_" + ID + "_Amount'></td>";
		HTML += "<td><select name='Discount_Code_" + ID + "_Recurring'>";
		HTML += "<option value='No'>No</option>";
		HTML += "<option value='Yes'>Yes</option>";
		HTML += "</select></td>";
		HTML += "<td><select name='Discount_Code_" + ID + "_Applicable'>";
		HTML += "<option value='Membership'>Membership</option>";
		//HTML += "<option value='Hours'>Hour(s)</option>";
		//HTML += "<option value='Days'>Day(s)</option>";
		HTML += "</select></td>";
		HTML += "<td><input type='datetime-local' name='Discount_Code_" + ID + "_Expiry'></td>";
		HTML += "</tr>";

		//jQuery('table > tr#ewd-uasp-add-reminder').before(HTML);
		jQuery('#ewd-feup-discount-codes-table tr:last').before(HTML);

		ID++;
		jQuery(this).data('nextid', ID); //updates but doesn't show in DOM

		SetDiscountDeleteHandlers();

		event.preventDefault();
	});
});

function SetDiscountDeleteHandlers() {
	jQuery('.ewd-feup-delete-discount-code').on('click', function(event) {
		var ID = jQuery(this).data('reminderid');
		var tr = jQuery('#ewd-feup-discount-code-row-'+ID);

		tr.fadeOut(400, function(){
            tr.remove();
        });

		event.preventDefault();
	});
}
