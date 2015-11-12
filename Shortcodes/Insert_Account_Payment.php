<?php

function EWD_FEUP_Account_Payment($atts) {
	global  $wpdb;
	global $ewd_feup_user_table_name;
	
	$Custom_CSS = get_option("EWD_FEUP_Custom_CSS");

	$Payment_Frequency = get_option("EWD_FEUP_Payment_Frequency");
	$Payment_Types = get_option("EWD_FEUP_Payment_Types");
	$Membership_Cost = get_option("EWD_FEUP_Membership_Cost");
	$PayPal_Email_Address = get_option("EWD_FEUP_PayPal_Email_Address");
	$Pricing_Currency_Code = get_option("EWD_FEUP_Pricing_Currency_Code");
	$Thank_You_URL = get_option("EWD_FEUP_Thank_You_URL");
	$Discount_Codes_Array = get_option("EWD_FEUP_Discount_Codes_Array");
	
	// Get the attributes passed by the shortcode, and store them in new variables for processing
	extract( shortcode_atts( array(
				'username' => '',
				'level' => '',
				'discount_code' => '',
				),
		$atts
		)
	);

	$ReturnString = "<style type='text/css'>";
	$ReturnString .= $Custom_CSS;
	$ReturnString .= EWD_FEUP_Add_Modified_Styles();
	$ReturnString .= "</style>";

	if ($username == "" and isset($_POST['Username'])) {
		$username = $_POST['Username'];
	}
	if ($level == "" and isset($_POST['level'])) {
		$level = $_POST['level'];
	}
	if ($discount_code == "" and isset($_POST['discount_code'])) {
		$discount_code = $_POST['discount_code'];
	}

	if ($username == "" or ($Payment_Types == "Levels" and $level == "")) {
		$ReturnString .= "<div class-'ewd-feup-paypal-username-form'>";
		$ReturnString .= "<form action='#' method='post'>";
		if ($username == "") {
			$ReturnString .= "<div class='feup-pure-control-group'>";
			$ReturnString .= "<label for='Username' id='ewd-feup-paypal-username-div' class='ewd-feup-field-label'>" . __('Username', 'EWD_FEUP') . ": </label>";
			$ReturnString .= "<input type='text' class='ewd-feup-text-input ewd-feup-paypal-username-input' name='Username'>";
			$ReturnString .= "</div>";
		}
		else {
			$ReturnString .= "<div class='feup-pure-control-group'>";
			$ReturnString .= "<label for='Username' id='ewd-feup-paypal-username-label' class='ewd-feup-field-label'>" . __('Username', 'EWD_FEUP') . ": </label>";
			$ReturnString .= "<input type='hidden' name='Username' value='" . $username . "' />";
			$ReturnString .= "<span class='ewd-feup-username'>" . $username . "</span>";
			$ReturnString .= "</div>";
		}
		if ($Payment_Types == "Levels") {
			if ($level == "") {
				$ReturnString .= "<div class='feup-pure-control-group'>";
				$ReturnString .= "<label for='Username' id='ewd-feup-paypal-username-div' class='ewd-feup-field-label'>" . __('Username', 'EWD_FEUP') . ": </label>";
				$ReturnString .= "<input type='text' class='ewd-feup-text-input ewd-feup-paypal-username-input' name='level'>";
				$ReturnString .= "</div>";
			}
			else {
				$ReturnString .= "<input type='hidden' name='level' value='" . $level . "' />";
			}

		}
		$ReturnString .= "<div class='feup-pure-control-group'><label for='submit'></label><input type='submit' class='ewd-feup-submit feup-pure-button feup-pure-button-primary' name='PayPal_Username_Submit' value='" . __('Username', 'EWD_FEUP') . "'></div>";
		$ReturnString .= "</form>";
		$ReturnString .= "</div>";
	}

	//Create form with link to pay for appointment
	else {
		$User = $wpdb->get_row($wpdb->prepare("SELECT * FROM $ewd_feup_user_table_name WHERE Username=%s", $username));

		if ($Payment_Frequency == "One_Time") {
			if ($Payment_Types == "Membership") {
				if ($discount_code != "") {$Discount = EWD_FEUP_Calculate_Discount("Membership", $discount_code, $Payment_Frequency);}
				else {$Discount['Amount'] = 0;}
				$Payment_Amount = $Membership_Cost - $Discount['Amount'];
	
				$ReturnString .= "<div class='ewd-feup-paypal-form'>";
				//$ReturnString .= "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
				$ReturnString .= "<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
	        	$ReturnString .= "<input type='hidden' name='item_name_1' value='" . substr(get_bloginfo('name'), 0, 100) . " Site Membership' />";
	        	$ReturnString .= "<input type='hidden' name='quantity_1' value='1' />";
	        	$ReturnString .= "<input type='hidden' name='amount_1' value='" . $Payment_Amount . "' />";
	 			$ReturnString .= "<input type='hidden' name='custom' value='User_ID=" . $User->User_ID . "&discount_code=" . $discount_code . "' />";
	
	    		$ReturnString .= "<input type='hidden' name='cmd' value='_cart' />";
	    		$ReturnString .= "<input type='hidden' name='upload' value='1' />";
	    		$ReturnString .= "<input type='hidden' name='business' value='" . $PayPal_Email_Address . "' />";
	 			
	    		$ReturnString .= "<input type='hidden' name='currency_code' value='" . $Pricing_Currency_Code . "' />";
	    		//$ReturnString .= "<input type='hidden' name='lc' value='CA' />"
	    		//$ReturnString .= "<input type='hidden' name='rm' value='2' />";
	    		$ReturnString .= "<input type='hidden' name='return' value='" . $Thank_You_URL . "' />";
	    		//$ReturnString .= "<input type='hidden' name='cancel_return' value='" . ' />
	    		$ReturnString .= "<input type='hidden' name='notify_url' value='" . get_site_url() . "' />";
	 			
	    		$ReturnString .= "<input type='submit' class='submit-button' value='Proceed to Payment' />";
				$ReturnString .= "</form>";
				$ReturnString .= "</div>";
			}
			else {
				if ($discount_code != "") {$Discount = EWD_FEUP_Calculate_Discount($level, $discount_code, $Payment_Frequency);}
				else {$Discount['Amount'] = 0;}
				$Level_Cost = EWD_FEUP_Calculate_Level_Payment($level);
				$Payment_Amount = $Level_Cost - $Discount['Amount'];
		
				$ReturnString .= "<div class='ewd-feup-paypal-form'>";
				//$ReturnString .= "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
				$ReturnString .= "<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
	        	$ReturnString .= "<input type='hidden' name='item_name_1' value='" . substr(get_bloginfo('name'), 0, 100) . " " . $level . " Fee' />";
	        	$ReturnString .= "<input type='hidden' name='quantity_1' value='1' />";
	        	$ReturnString .= "<input type='hidden' name='amount_1' value='" . $Payment_Amount . "' />";
	 			$ReturnString .= "<input type='hidden' name='custom' value='User_ID=" . $User->User_ID . "&discount_code=" . $discount_code . "' />";
	
	    		$ReturnString .= "<input type='hidden' name='cmd' value='_cart' />";
	    		$ReturnString .= "<input type='hidden' name='upload' value='1' />";
	    		$ReturnString .= "<input type='hidden' name='business' value='" . $PayPal_Email_Address . "' />";
	 			
	    		$ReturnString .= "<input type='hidden' name='currency_code' value='" . $Pricing_Currency_Code . "' />";
	    		//$ReturnString .= "<input type='hidden' name='lc' value='CA' />"
	    		//$ReturnString .= "<input type='hidden' name='rm' value='2' />";
	    		$ReturnString .= "<input type='hidden' name='return' value='" . $Thank_You_URL . "' />";
	    		//$ReturnString .= "<input type='hidden' name='cancel_return' value='" . ' />
	    		$ReturnString .= "<input type='hidden' name='notify_url' value='" . get_site_url() . "' />";
	 			
	    		$ReturnString .= "<input type='submit' class='submit-button' value='Proceed to Payment' />";
				$ReturnString .= "</form>";
				$ReturnString .= "</div>";
			}
		}
		else {
			if ($Payment_Types == "Membership") {
				if ($discount_code != "") {$Discount = EWD_FEUP_Calculate_Discount("Membership", $discount_code, $Payment_Frequency);}
				else {$Discount['Amount'] = 0;}
				
				if ($Discount['Amount'] == 0) {
					$Payment_Amount = $Membership_Cost;
				}
				elseif ($Discount['Amount'] != 0 and $Discount['Recurring'] != "Yes") {
					
					$Trial = true;
					$Payment_Amount_Trial = $Membership_Cost - $Discount['Amount'];
					$Payment_Amount = $Membership_Cost;
				}
				else {
					$Payment_Amount = $Membership_Cost - $Discount['Amount'];
				}

				if ($Payment_Frequency == "Yearly") {$PP_Frequnecy = "Y";}
				if ($Payment_Frequency == "Monthly") {$PP_Frequnecy = "M";}
	
				$ReturnString .= "<div class='ewd-feup-paypal-form'>";
				$ReturnString .= "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
				//$ReturnString .= "<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
	        	$ReturnString .= "<input type='hidden' name='item_name' value='" . substr(get_bloginfo('name'), 0, 100) . " " . $Payment_Frequency . " Membership' />";
	        	$ReturnString .= "<input type='hidden' name='a3' value='" . $Payment_Amount . "' />";
	        	$ReturnString .= "<input type='hidden' name='p3' value='1' />";
	        	$ReturnString .= "<input type='hidden' name='t3' value='" . $PP_Frequnecy . "' />";

	        	if ($Trial) {
	        		$ReturnString .= "<input type='hidden' name='a1' value='" . $Payment_Amount_Trial . "' />";
	        		$ReturnString .= "<input type='hidden' name='p1' value='1' />";
	        		$ReturnString .= "<input type='hidden' name='t1' value='" . $PP_Frequnecy . "' />";
	        	}

	 			$ReturnString .= "<input type='hidden' name='src' value='1' />";
	 			$ReturnString .= "<input type='hidden' name='custom' value='User_ID=" . $User->User_ID . "&discount_code=" . $discount_code . "' />";
	
	    		$ReturnString .= "<input type='hidden' name='cmd' value='_xclick-subscriptions' />";
	    		$ReturnString .= "<input type='hidden' name='business' value='" . $PayPal_Email_Address . "' />";
	 			
	    		$ReturnString .= "<input type='hidden' name='currency_code' value='" . $Pricing_Currency_Code . "' />";
	    		//$ReturnString .= "<input type='hidden' name='lc' value='CA' />"
	    		//$ReturnString .= "<input type='hidden' name='rm' value='2' />";
	    		$ReturnString .= "<input type='hidden' name='return' value='" . $Thank_You_URL . "' />";
	    		//$ReturnString .= "<input type='hidden' name='cancel_return' value='" . ' />
	    		$ReturnString .= "<input type='hidden' name='notify_url' value='" . get_site_url() . "' />";
	 			
	    		$ReturnString .= "<input type='submit' class='submit-button' value='Proceed to Payment' />";
				$ReturnString .= "</form>";
				$ReturnString .= "</div>";
			}
			else {
				if ($discount_code != "") {$Discount = EWD_FEUP_Calculate_Discount($level, $discount_code, $Payment_Frequency);}
				else {$Discount['Amount'] = 0;}

				$Level_Cost = EWD_FEUP_Calculate_Level_Payment($level);
				
				if ($Discount['Amount'] == 0) {
					$Payment_Amount = $Level_Cost ;
				}
				elseif ($Discount['Amonut'] != 0 and $Discount['Recurring'] != "Yes") {
					$Trial = true;
					$Payment_Amount_Trial = $Level_Cost - $Discount['Amount'];
					$Payment_Amount = $Level_Cost;
				}
				else {
					$Payment_Amount = $Level_Cost - $Discount['Amount'];
				}

				if ($Payment_Frequency == "Yearly") {$PP_Frequnecy = "Y";}
				if ($Payment_Frequency == "Monthly") {$PP_Frequnecy = "M";}
	
				$ReturnString .= "<div class='ewd-feup-paypal-form'>";
				//$ReturnString .= "<form action='https://www.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
				$ReturnString .= "<form action='https://www.sandbox.paypal.com/cgi-bin/webscr' method='post' class='standard-form'>";
	        	$ReturnString .= "<input type='hidden' name='item_name' value='" . substr(get_bloginfo('name'), 0, 100) . " " . $Payment_Frequency . " " . $level . "' />";
	        	$ReturnString .= "<input type='hidden' name='a3' value='" . $Payment_Amount . "' />";
	        	$ReturnString .= "<input type='hidden' name='p3' value='1' />";
	        	$ReturnString .= "<input type='hidden' name='t3' value='" . $PP_Frequnecy . "' />";

	        	if ($Trial) {
	        		$ReturnString .= "<input type='hidden' name='a1' value='" . $Payment_Amount_Trial . "' />";
	        		$ReturnString .= "<input type='hidden' name='p3' value='1' />";
	        		$ReturnString .= "<input type='hidden' name='t3' value='" . $PP_Frequnecy . "' />";
	        	}

	 			$ReturnString .= "<input type='hidden' name='src' value='1' />";
	 			$ReturnString .= "<input type='hidden' name='custom' value='User_ID=" . $User->User_ID . "&discount_code=" . $discount_code . "' />";
	
	    		$ReturnString .= "<input type='hidden' name='cmd' value='_xclick-subscriptions' />";
	    		$ReturnString .= "<input type='hidden' name='business' value='" . $PayPal_Email_Address . "' />";
	 			
	    		$ReturnString .= "<input type='hidden' name='currency_code' value='" . $Pricing_Currency_Code . "' />";
	    		//$ReturnString .= "<input type='hidden' name='lc' value='CA' />"
	    		//$ReturnString .= "<input type='hidden' name='rm' value='2' />";
	    		$ReturnString .= "<input type='hidden' name='return' value='" . $Thank_You_URL . "' />";
	    		//$ReturnString .= "<input type='hidden' name='cancel_return' value='" . ' />
	    		$ReturnString .= "<input type='hidden' name='notify_url' value='" . get_site_url() . "' />";
	 			
	    		$ReturnString .= "<input type='submit' class='submit-button' value='Proceed to Payment' />";
				$ReturnString .= "</form>";
				$ReturnString .= "</div>";
			}
		}

		if (sizeof($Discount_Codes_Array) > 0) {
			$ReturnString .= "<div class='ewd-feup-discount-div'>";
			$ReturnString .= __("Have a discount code? Enter it below.", 'EWD_FEUP');
			$ReturnString .= "<div class='ewd-feup-discount-form'>";
			$ReturnString .= "<form action='#' method='post'>";
			$ReturnString .= "<input type='hidden' name='Payment_Required' value='Yes' />";
			if ($username != "") {$ReturnString .= "<input type='hidden' name='Username' value='" . $username . "' />";}
			if ($level != "") {$ReturnString .= "<input type='hidden' name='Username' value='" . $level . "' />";}

			$ReturnString .= "<div class='feup-pure-control-group'>";
			$ReturnString .= "<label for='Username' id='ewd-feup-paypal-discount-code-div' class='ewd-feup-field-label'>" . __('Discount Code', 'EWD_FEUP') . ": </label>";
			$ReturnString .= "<input type='text' class='ewd-feup-text-input ewd-feup-paypal-discount-code-input' name='discount_code'>";
			$ReturnString .= "</div>";

			$ReturnString .= "<div class='feup-pure-control-group'><label for='submit'></label><input type='submit' class='ewd-feup-submit feup-pure-button feup-pure-button-primary' name='Discount_Submit' value='" . __("Use Discount Code", 'EWD_FEUP') . "'></div>";
			$ReturnString .= "</form>";
			$ReturnString .= "</div>";
		}
	}

	return $ReturnString;
}
add_shortcode('account-payment', "EWD_FEUP_Account_Payment");


function EWD_FEUP_Calculate_Discount($Applies_To, $discount_code, $Payment_Frequency) {
	$Discount_Codes_Array = get_option("EWD_FEUP_Discount_Codes_Array");

	if (!is_array($Discount_Codes_Array)) {
		$Discount['Amount'] = 0;
		$Discount['Recurring'] = 'No';

		return $Discount;
	}

	foreach ($Discount_Codes_Array as $Discount_Code_Item) {
		if ($Discount_Code_Item['Code'] == $discount_code) {
			if ($Discount_Code_Item['Applicable'] == $Applies_To) {
				$Expires = strtotime($Discount_Code_Item['Expiry']);
				$Currently = time();
				if ($Currently < $Expires) {
					$Discount['Amount'] = $Discount_Code_Item['Amount'];
					$Discount['Recurring'] = $Discount_Code_Item['Recurring'];
					
					return $Discount;
				}
			}
		}
	}

	if (!isset($Discount)) {
		$Discount['Amount'] = 0;
		$Discount['Recurring'] = 'No';

		return $Discount;
	}
}

function EWD_FEUP_Calculate_Level_Payment($level) {
	$Levels_Payment_Array = get_option("EWD_FEUP_Levels_Payment_Array");

	if (!is_array($Levels_Payment_Array)) {return 0;}

	foreach ($Levels_Payment_Array as $Level_Payment_Item) {
		if ($Level_Payment_Item['Level'] == $level) {
			return $Level_Payment_Item['Amount'];
		}
	}

	return 0;
}
?>