<?php

/*
* Templates for new user registration
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


//Before showing users a registration form, show them the registration agreement.
function template_registration_agreement() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;

  $agreement = explode('<br /><br />', $context['agreement']);
  echo '
		<form data-ajax="false" action="', $scripturl, '?action=register" method="post" accept-charset="', $context['character_set'], '" id="registration">
			<h2>' . $txt['iAgreement'] . '</h2>
			<div class="agreement">
			', $agreement[0], ' <a style="color: #007AFF;" href="#" onclick="this.parentNode.innerHTML=\'', addslashes($context['agreement']), '\'; return false;">[', $txt['iMore'], '...]</a>
			</div>';
  
  echo '
      <div class="buttons">';
  
  //Age restriction in effect?
  if ($context['show_coppa']) {
    echo '
			 <input class="button two-buttons" type="submit" name="accept_agreement" value="', $context['coppa_agree_above'], '" />
			 <input class="button two-buttons" type="submit" name="accept_agreement_coppa" value="', $context['coppa_agree_below'], '" />';
  } else {
    echo '
		    <button class="button" name="accept_agreement">' . $txt['agreement_agree'] . '</button>';
  }
  echo '
      </div>';
  
  echo '
		  <input type="hidden" name="step" value="1" />
	  </form>';
}

//Before registering - get their information
function template_registration_form() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  //Make sure they've agreed to the terms and conditions
  echo '
    <script type="text/javascript" src="' . $settings['theme_url'] . '/scripts/register.js"></script>
    <script type="text/javascript"><!-- // --><![CDATA[
    	function verifyAgree()
    	{
    		if (currentAuthMethod == \'passwd\' && document.forms.creator.smf_autov_pwmain.value != document.forms.creator.smf_autov_pwverify.value)
    		{
    			alert("', $txt['register_passwords_differ_js'], '");
    			return false;
    		}

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
  
  echo '
    // ]]></script>';
  
  //Any errors?
  if (!empty($context['registration_errors'])) {
    echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['registration_errors']), '</div>
    </div>';
  }
  
  echo '
    <form action="', $scripturl, '?action=register2" method="post" accept-charset="', $context['character_set'], '" name="creator" id="creator" onsubmit="return verifyAgree();">';
  
  //User details
  echo '<div class="header">User</div>';
  
  echo '<div class="no-left-padding input-container" style="padding-top: 13px;">';
  echo '<span class="input-label">' . $txt['username'] . '</span>';
  echo '<input type="text" name="user" id="smf_autov_username" size="30" tabindex="', $context['tabindex']++, '" maxlength="25" value="', isset($context['username']) ? $context['username'] : '', '" />';
  echo '<span id="smf_autov_username_div" style="display: none;">
          <a id="smf_autov_username_link" href="#">
            <img id="smf_autov_username_img" src="', $settings['images_url'], '/icons/field_check.png" alt="*" />
          </a>
        </span>';
  echo '</div>';
  
  echo '<div class="no-left-padding input-container">';
  echo '<span class="input-label">' . $txt['email'] . '</span>';
  echo '<input type="text" name="email" id="smf_autov_reserve1" size="30" tabindex="', $context['tabindex']++, '" value="', isset($context['email']) ? $context['email'] : '', '" />';
  echo '</div>';
  
  //Password
  echo '<div class="header">' . $txt['password'] . '</div>';
  
  echo '<div class="no-left-padding input-container" style="padding-top: 13px;">';
  echo '<span class="input-label">' . $txt['iChoose'] . '</span>';
  echo '<input type="password" name="passwrd1" id="smf_autov_pwmain" size="30" tabindex="', $context['tabindex']++, '" />';
  echo '</div>';
  
  echo '<div class="no-left-padding input-container">';
  echo '<span class="input-label">' . $txt['iVerify'] . '</span>';
  echo '<input type="password" name="passwrd2" id="smf_autov_pwverify" size="30" tabindex="', $context['tabindex']++, '" />';
  echo '</div>';
  
  //Verification control
  if ($context['visual_verification']) {
    echo '<div class="header">' . $txt['iVerification'] . '</div>';
    echo '<div class="no-left-padding input-container" style="padding-top: 13px;">';
    echo '<span class="input-label">' . $txt['iCode'] . '</span>';
    echo template_control_verification($context['visual_verification_id'], 'all');
    echo '</div>';
    echo '<div class="no-left-padding input-container">';
    echo '<span class="input-label">' . $txt['iVerify'] . '</span>';
    echo '<input type="text" tabindex="', $context['tabindex']++, '" name="register_vv[code]" />';
    echo '</div>';
  }
  
  //Register button
  echo '
      <div class="child buttons">
        <button name="regSubmit" class="button" type="submit">', $txt['register'], '</button>
      </div>';
  
  echo '
    </form>';
  
  //Clever registration stuff
  echo '
    <script type="text/javascript"><!-- // --><![CDATA[
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
    	//Update the authentication status.
    	updateAuthMethod();
    // ]]></script>';
}

//After registration... all done ;)
function template_after() {
  global $context, $settings, $options, $txt, $scripturl;
  
  echo '
    <div class="header">', $context['description'], '</div>
    <div class="child buttons">
      <button class="button" onclick="go(\'home\');">', $txt['iDone'], '</button>
    </div>';
}

function template_coppa() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_coppa_form() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_verification_sound() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_admin_register() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_edit_agreement() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_edit_reserved_words() {
  global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

?>