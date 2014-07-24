<?php

/*
* Handle logging into the forum. We will also use a quick login form within the main index.template.php file
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


require_once ($settings[theme_dir] . '/ThemeControls.php');

function template_login() {
  global $context, $scripturl, $settings, $txt;
  
  //Display any errors or messages
  if (!empty($context['login_errors'])) {
    echo '
      <div class="errors">
        <div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['login_errors']), '</div>
      </div>';
  }
  if (isset($context['description'])) {
    echo '
      <div class="errors">
        <div style="margin-top: 6px;">*', $context['description'], '</div>
      </div>';
  }
  
  //Show our login form
  echo '
      <div class="quick-login" style="display: block;">';
  template_control_login_form();
  echo '
      </div>';
}

function template_kick_guest() {
  global $txt;

  //Only members can view this section
  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', empty($context['kick_message']) ? $txt['only_members_can_access'] : $context['kick_message'], '</div>
    </div>';

  //Show the login form in case they already are or want to register  
  template_login();
}

?>