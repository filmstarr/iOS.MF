<?php
// Version: 2.0 RC4; Register

// Before showing users a registration form, show them the registration agreement.
function template_registration_agreement()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;
	$agreement=explode('<br /><br />',$context['agreement']);
	echo '
		<form action="', $scripturl, '?action=register" method="post" accept-charset="', $context['character_set'], '" id="registration">
			<h2>'.$txt['iAgreement'].'</h2>
			<ul class="posts">
	
				<li>
					<div class="last message" style="font-size:11px;">
					',$agreement[0],' <a href="#" onclick="this.parentNode.innerHTML=\'', addslashes($context['agreement']) ,'\'; return false;">[', $txt['iMore'] ,'...]</a>
					</div>
				</li>
			
			</ul>
			<div id="confirm_buttons" style="text-align: center;">';

	// Age restriction in effect?
	if ($context['show_coppa'])
		echo '
				<input id="regisbutt" type="submit" name="accept_agreement" value="', $context['coppa_agree_above'], '" /><br /><br />
				<input id="regisbutt" type="submit" name="accept_agreement_coppa" value="', $context['coppa_agree_below'], '" />';		
	else
		echo '
				<button style="width: 90%; height: 51px;" name="accept_agreement">'. $txt['agreement_agree'] .'</button>';
	echo '
			</div>
			<input type="hidden" name="step" value="1" />
		</form>';

}

// Before registering - get their information.
function template_registration_form()
{
	global $context, $settings, $options, $scripturl, $txt, $modSettings;

	// Make sure they've agreed to the terms and conditions.
	echo '
<script language="JavaScript" type="text/javascript" src="', $settings['default_theme_url'], '/scripts/register.js"></script>
<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[
	function verifyAgree()
	{
		if (currentAuthMethod == \'passwd\' && document.forms.creator.smf_autov_pwmain.value != document.forms.creator.smf_autov_pwverify.value)
		{
			alert("', $txt['register_passwords_differ_js'], '");
			return false;
		}';

	// If they haven't checked the "I agree" box, tell them and don't submit.
	if ($context['require_agreement'])
		echo '

		if (!document.forms.creator.regagree.checked)
		{
			alert("', $txt['register_agree'], '");
			return false;
		}';

	// Otherwise, let it through.
	echo '

		return true;
	}

	var currentAuthMethod = \'passwd\';
	function updateAuthMethod()
	{
		// What authentication method is being used?
		if (!document.getElementById(\'auth_openid\') || !document.getElementById(\'auth_openid\').checked)
			currentAuthMethod = \'passwd\';
		else
			currentAuthMethod = \'openid\';

		// No openID?
		if (!document.getElementById(\'auth_openid\'))
			return true;

		document.forms.creator.openid_url.disabled = currentAuthMethod == \'openid\' ? false : true;
		document.forms.creator.smf_autov_pwmain.disabled = currentAuthMethod == \'passwd\' ? false : true;
		document.forms.creator.smf_autov_pwverify.disabled = currentAuthMethod == \'passwd\' ? false : true;
		document.getElementById(\'smf_autov_pwmain_div\').style.display = currentAuthMethod == \'passwd\' ? \'\' : \'none\';
		document.getElementById(\'smf_autov_pwverify_div\').style.display = currentAuthMethod == \'passwd\' ? \'\' : \'none\';

		if (currentAuthMethod == \'passwd\')
		{
			verificationHandle.refreshMainPassword();
			verificationHandle.refreshVerifyPassword();
			document.forms.creator.openid_url.style.backgroundColor = \'\';
		}
		else
		{
			document.forms.creator.smf_autov_pwmain.style.backgroundColor = \'\';
			document.forms.creator.smf_autov_pwverify.style.backgroundColor = \'\';
			document.forms.creator.openid_url.style.backgroundColor = \'#FCE184\';
		}

		return true;
	}';

	if ($context['require_agreement'])
		echo '
	function checkAgree()
	{
		document.forms.creator.regSubmit.disabled =  (currentAuthMethod == "passwd" && (isEmptyText(document.forms.creator.smf_autov_pwmain) || isEmptyText(document.forms.creator.user) || isEmptyText(document.forms.creator.email))) || (currentAuthMethod == "openid" && isEmptyText(document.forms.creator.openid_url)) || !document.forms.creator.regagree.checked;
		setTimeout("checkAgree();", 1000);
	}
	setTimeout("checkAgree();", 1000);';

	echo '
// ]]></script>';

	// Any errors?
	if (!empty($context['registration_errors']))
	{
		foreach ($context['registration_errors'] as $error)
				echo '
			<h2>', $error, '</h2>';
			
	}

	echo '
<form action="', $scripturl, '?action=register2" method="post" accept-charset="', $context['character_set'], '" name="creator" id="creator" onsubmit="return verifyAgree();">';

echo'	
	
	<h2></h2>
	
	<ul class="login" style="clear:both;">
	
		
		<li>
			<div class="field">
				<div class="fieldname">'. $txt['username'] .'</div>
				<div class="fieldinfo"><input type="text" name="user" id="smf_autov_username" size="30" tabindex="', $context['tabindex']++, '" maxlength="25" value="', isset($context['username']) ? $context['username'] : '', '" /></div>
			</div>
		</li>
		
		<li>
			<div class="last field">
				<div class="fieldname">'. $txt['email'] .'</div>
				<div class="fieldinfo"><input type="text" name="email" id="smf_autov_reserve1" size="30" tabindex="', $context['tabindex']++, '" value="', isset($context['email']) ? $context['email'] : '', '" /></div>
			</div>
		</li>
		
	</ul>
	
	<h2>'. $txt['password'] .'</h2>
		
	<ul class="login">

		<li>
			<div class="field">
				<div class="fieldname">'. $txt['iChoose'] .'</div>
				<div class="fieldinfo"><input type="password" name="passwrd1" id="smf_autov_pwmain" size="30" tabindex="', $context['tabindex']++, '" /></div>
			</div>
		</li>
		
		<li>
			<div class="last field">
				<div class="fieldname">'. $txt['iVerify'] .'</div>
				<div class="fieldinfo"><input type="password" name="passwrd2" id="smf_autov_pwverify" size="30" tabindex="', $context['tabindex']++, '" /></div>
			</div>
		</li>
		
	</ul>';
	
if ($context['visual_verification']) {
	echo'
	<h2>'. $txt['iVerification'] .'</h2>
	<ul class="login">
	<li>';
		if(!empty($modSettings['recaptcha_enabled']) && ($modSettings['recaptcha_enabled'] == 1) && !empty($modSettings['recaptcha_public_key']) && !empty($modSettings['recaptcha_private_key']))
		{
			echo'
			<div id="verification_control">
			<div id="recaptcha_widget" style="display: none;">
				<script type="text/javascript">
					var RecaptchaOptions = {
						theme : \'custom\',
						custom_theme_widget: \'recaptcha_widget\'
					};
				</script>
				<div id="recaptcha_image"></div>
				<span class="recaptcha_only_if_image">'.$txt['ireCAPTCHA1'].'<br /><br /></span>
				<input id="recaptcha_response_field" name="recaptcha_response_field" type="text"><br /><br />
				<a class="ottieni" href="javascript:Recaptcha.reload();">'.$txt['ireCAPTCHA2'].'</a>	
				<script type="text/javascript" src="http://api.recaptcha.net/challenge?k='.$modSettings['recaptcha_public_key'].'"></script>
				<noscript>
				<iframe src="http://api.recaptcha.net/noscript?k='.$modSettings['recaptcha_public_key'].'" height="200" width="500" frameborder="0"></iframe><br />
				<textarea name="recaptcha_challenge_field" rows="3" cols="40">
				</textarea>
				<input type="hidden" name="recaptcha_response_field" value="manual_challenge" />
				</noscript>
			</div>
			</div>';
		}
		else {
			echo '
						<div class="verification field">
				<div class="fieldname">'. $txt['iCode'] .'</div>
				<div class="fieldinfo">', template_control_verification($context['visual_verification_id'], 'all'), '</div>
			</div>
		</li>	
			
		<li>
			<div class="last field">
				<div class="fieldname">'. $txt['iVerify'] .'</div>
				<div class="fieldinfo"><input type="text" name="register_vv[code]" value="" size="30" tabindex="', $context['tabindex']++, '" />
			</div>';
		}
	echo '	
	</li>
	</ul>';
}

	echo'<div class="child buttons">
	
	<button name="regSubmit">'. $txt['register'] .'</button>
	<input type="hidden" name="step" value="2" />
	</div></form>';



echo '

<script language="JavaScript" type="text/javascript"><!-- // --><![CDATA[';

	// Uncheck the agreement thing....
	

	// Clever registration stuff...
	echo '
	var regTextStrings = {
		"username_valid": "', $txt['registration_username_available'], '",
		"username_invalid": "', $txt['registration_username_unavailable'], '",
		"username_check": "', $txt['registration_username_check'], '",
		"password_short": "', $txt['registration_password_short'], '",
		"password_reserved": "', $txt['registration_password_reserved'], '",
		"password_numbercase": "', $txt['registration_password_numbercase'], '",
		"password_no_match": "', $txt['registration_password_no_match'], '",
		"password_valid": "', $txt['registration_password_valid'], '"
	};
	var verificationHandle = new smfRegister("creator", ', empty($modSettings['password_strength']) ? 0 : $modSettings['password_strength'], ', regTextStrings);
	// Update the authentication status.
	updateAuthMethod();';

echo '
// ]]></script>';
}

// After registration... all done ;).
function template_after()
{
	global $context, $settings, $options, $txt, $scripturl;

	// Not much to see here, just a quick... "you're now registered!" or what have you.
	echo '<h2>', $context['description'], '</h2>';
	echo '<br /><div align="center"><button onclick="go(\'home\');">', $txt['iDone'], '</button></div>';
}

?>