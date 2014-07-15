<?php

/*
* Handle logging into the forum. We will also use a quick login form located within the main index.template.php file
*/


function template_login() {
  global $context, $scripturl, $settings, $txt;
  
  if (!empty($context['login_errors'])) echo '<div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['login_errors']), '</div></div>';
  if (isset($context['description'])) echo '<div class="errors"><div style="margin-top: 6px;">*', $context['description'], '</div></div>';
  
  echo '<div class="quick-login" style="display: block;">
  <form data-ajax="false" action="', $scripturl, '?action=login2" name="frmLogin" method="post" accept-charset="', $context['character_set'], '" ', empty($context['disable_login_hashing']) ? ' onsubmit="hashLoginPassword(this, \'' . $context['session_id'] . '\');"' : '', '>

<div class="no-left-padding input-container pad-top">';
  echo '<span class="input-label">' . $txt['username'] . '</span>';
  echo '<input class="user" type="text" tabindex="', $context['tabindex']++, '" name="user" />
</div>
<div class="no-left-padding input-container pad-top">';
  echo '<span class="input-label">' . $txt['password'] . '</span>';
  echo '<input type="password" tabindex="', $context['tabindex']++, '" name="passwrd" />
</div>
<div class="no-left-padding input-container pad-top">';
  echo '<span class="input-label">' . $txt['iRemember'] . '</span>';
  echo '<input type="checkbox" checked="checked" name="cookieneverexp" value="1" />
</div>
    
  <input type="hidden" name="hash_passwrd" value="" />
  <div class="buttons" style="margin-top: -9px; padding-bottom: 5px; margin-bottom: 6px;">
    <button onclick="$(\'.ui-loader\').loader(\'show\');" class="button two-buttons" type="submit">' . $txt['login'] . '</button>
    <button class="button two-buttons" type="button" onclick="go(\'register\')">' . $txt['register'] . '</button>
  </div>
  </form>
  </div>';
}

function template_kick_guest() {
  global $txt;
  echo '<div class="errors"><div style="margin-top: 6px;">*', empty($context['kick_message']) ? $txt['only_members_can_access'] : $context['kick_message'], '</div></div>';
  template_login();
}
?>