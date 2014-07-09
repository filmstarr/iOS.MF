<?php
// Version: 2.0 RC4; Login

function template_login()
{
	global $context, $scripturl, $settings, $txt;

	if (!empty($context['login_errors']))
		echo '<div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['login_errors']), '</div></div>';
	if (isset($context['description']))
		echo '<div class="errors"><div style="margin-top: 6px;">*', $context['description'], '</div></div>';

  echo '<div class="quickLogin" style="display: block;">
  <form data-ajax="false" action="', $scripturl, '?action=login2" name="frmLogin" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>

<div class="noLeftPadding inputContainer padTop">';
  echo'<span class="inputLabel">'. $txt['username'] .'</span>';
  echo'<input class="user" type="text" tabindex="', $context['tabindex']++, '" name="user" />
</div>
<div class="noLeftPadding inputContainer padTop">';
  echo'<span class="inputLabel">'. $txt['password'] .'</span>';
  echo'<input type="password" tabindex="', $context['tabindex']++, '" name="passwrd" />
</div>
<div class="noLeftPadding inputContainer padTop">';
  echo'<span class="inputLabel">'. $txt['iRemember'] .'</span>';
  echo'<input type="checkbox" checked="checked" name="cookieneverexp" value="1" />
</div>
    
  <input type="hidden" name="hash_passwrd" value="" />
  <div class="buttons" style="margin-top: -9px; padding-bottom: 5px; margin-bottom: 6px;">
    <button onclick="$(\'.ui-loader\').last().show();" class="button twobuttons" type="submit">' . $txt['login'] . '</button>
    <button class="button twobuttons" type="button" onclick="go(\'register\')">'. $txt['register'] .'</button>
  </div>
  </form>
  </div>';
}

function template_kick_guest(){
global $txt;
echo '<div class="errors"><div style="margin-top: 6px;">*', empty($context['kick_message']) ? $txt['only_members_can_access'] : $context['kick_message'],'</div></div>';
template_login();
}

?>