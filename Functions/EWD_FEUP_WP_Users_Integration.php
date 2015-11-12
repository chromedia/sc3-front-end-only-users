<?php
$Include_WP_Users = get_option("EWD_FEUP_Include_WP_Users");

function EWD_FEUP_Import_WP_Users() {
	global $wpdb;
	global $ewd_feup_user_table_name;

	$Blog_ID = get_current_blog_id();
	$WP_Users = get_users( 'blog_id=' . $Blog_ID ); 
	foreach ($WP_Users as $WP_User) {
		$FEUP_User = $wpdb->get_results($wpdb->prepare("SELECT User_ID FROM $ewd_feup_user_table_name WHERE User_WP_ID=%d", $WP_User->ID));
		if ($wpdb->num_rows == 0) {
			EWD_FEUP_Create_WP_FEUP_User($WP_User);
		}
	} 
}

if ($Include_WP_Users == "Yes") {add_action( 'user_register', 'EWD_FEUP_Add_WP_User', 10, 1 );}
function EWD_FEUP_Add_WP_User($WP_User_ID) {
	$WP_User = get_user_by('id', $WP_User_ID);
	EWD_FEUP_Create_WP_FEUP_User($WP_User);
}

if ($Include_WP_Users == "Yes") {add_action('wp_login', 'EWD_FEUP_WP_User_Login', 10, 2);}
function EWD_FEUP_WP_User_Login($User_Login, $WP_User) {
	global $wpdb;
	global $ewd_feup_user_table_name;

	$FEUP_User = $wpdb->get_row($wpdb->prepare("SELECT Username FROM $ewd_feup_user_table_name WHERE User_WP_ID=%d", $WP_User->ID));
	$WP_Login = "Yes";
	
	Confirm_Login($FEUP_User->Username, $WP_Login);
}

function EWD_FEUP_Create_WP_FEUP_User($WP_User) {
	$Email_Confirmation = get_option("EWD_FEUP_Email_Confirmation");
	$Admin_Approval = get_option("EWD_FEUP_Admin_Approval");
	$Default_User_Level = get_option("EWD_Default_User_Level");
	$Salt = get_option("EWD_FEUP_Hash_Salt");
	$Use_Crypt = get_option("EWD_FEUP_Use_Crypt");
	$Username_Is_Email = get_option("EWD_FEUP_Username_Is_Email");

	if($Use_Crypt == "Yes") {
		$User_Fields['User_Password'] = Generate_Password(EWD_FEUP_RandomString());
	} else {
		$User_Fields['User_Password'] = sha1(md5(EWD_FEUP_RandomString().$Salt));
	}

	if ($Username_Is_Email == "Yes") {$User_Fields['Username'] = $WP_User->user_email;}
	else {$User_Fields['Username'] = $WP_User->user_login;}
	$User_Fields['User_Admin_Approved'] = "No";
	$User_Fields['User_Email_Confirmed'] = "No";
	$User_Fields['User_Date_Created'] = date("Y-m-d H:i:s");
	$User_Fields['Level_ID'] = $Default_User_Level;
	$User_Fields['User_WP_ID'] = $WP_User->ID;

	Add_EWD_FEUP_User($User_Fields);
}

?>