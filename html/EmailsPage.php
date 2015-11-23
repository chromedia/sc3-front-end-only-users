<?php 
	$Admin_Email = get_option("EWD_FEUP_Admin_Email");
	$Email_Subject = get_option("EWD_FEUP_Email_Subject");
	$Encrypted_Admin_Password = get_option("EWD_FEUP_Admin_Password");
	$Port = get_option("EWD_FEUP_Port");
	$Use_SMTP = get_option("EWD_FEUP_Use_SMTP");
	$SMTP_Mail_Server = get_option("EWD_FEUP_SMTP_Mail_Server");
	$SMTP_Username = get_option("EWD_FEUP_SMTP_Username");
	$Message_Body = get_option("EWD_FEUP_Message_Body");
	$Admin_Approval_Message_Body = get_option("EWD_FEUP_Admin_Approval_Message_Body");
	$Email_Field = get_option("EWD_FEUP_Email_Field");
	
	$key = 'EWD_FEUP';
	if (function_exists('mcrypt_decrypt')) {$Admin_Password = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($Encrypted_Admin_Password), MCRYPT_MODE_CBC, md5(md5($key))), "\0");}
	else {$Admin_Password = $Encrypted_Admin_Password;}
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div><h2>Email Settings</h2>

<p>We've switched to using the default WordPress SMTP mail function. To send SMTP email, use a plugin such as <a href='https://wordpress.org/plugins/wp-mail-smtp/'>WP Mail SMTP</a> to input your settings</p>


<form method="post" action="admin.php?page=EWD-FEUP-options&DisplayPage=Emails&Action=EWD_FEUP_UpdateEmailSettings">
<table class="form-table">
<th scope="row">Email Field Name</th>
<td>
	<fieldset><legend class="screen-reader-text"><span>Email Field Name</span></legend>
	<label title='Email Field Name'><input type='text' name='email_field' value='<?php echo $Email_Field; ?>' /> </label><br />
	<p>The name of the field that should be used to send the e-mail to for your registration form, if "Username is Email" on the "Options" tab isn't set to "Yes".</p>
	</fieldset>
</td>
</tr>
<tr>
<tr>
<th scope="row">Registration Message Body</th>
<td>
	<fieldset><legend class="screen-reader-text"><span>Message Body</span></legend>
	<label title='Message Body'></label><textarea class='ewd-feup-textarea' name='message_body'> <?php echo $Message_Body; ?></textarea><br />
	<p>What should be in the message sent to users upon registration? You can put [username], [password], or [join-date] to include the Username, Password or sign-up datetime for the user.</p>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row">Admin Approval Message Body</th>
<td>
	<fieldset><legend class="screen-reader-text"><span>Admin Approval Message Body</span></legend>
	<label title='Admin Approval Message Body'></label><textarea class='ewd-feup-textarea' name='admin_approval_message_body'> <?php echo $Admin_Approval_Message_Body; ?></textarea><br />
	<p>What should be in the message sent to users when they are approved, assuming that the option has been selected? You can put [username] or [join-date] to include the Username or sign-up datetime for the user.</p>
	</fieldset>
</td>
</tr>
<tr>
<th scope="row">Email Subject</th>
<td>
	<fieldset><legend class="screen-reader-text"><span>Email Subject</span></legend>
	<label title='Email Subject'><input type='text' name='email_subject' value='<?php echo $Email_Subject; ?>' /> </label><br />
	<p>The subject of the sign-up e-mail message.</p>
	</fieldset>
</td>
</tr>
</table>
<p class="submit"><input type="submit" name="Options_Submit" id="submit" class="button button-primary" value="Save Changes"  /></p></form>

</div>