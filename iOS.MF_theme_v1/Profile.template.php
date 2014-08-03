<?php

/*
* User profile templates. We won't provide the ability to edit the users profile here, users should go to a different theme to do this
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


//We won't show anything above or below the profile template
function template_profile_above() {
}

function template_profile_below() {
}

//Display a users profile
function template_summary() {
  global $context, $settings, $options, $scripturl, $modSettings, $txt;
  
  echo '
    <div id="profile-header">
      <div class="avatar"', $context['member']['avatar']['href'] ? ' style="background: url(' . str_replace(' ', '%20', $context['member']['avatar']['href']) . ') #fff center no-repeat;"' : '', '></div>
      <div id="username">
        <h3>', $context['member']['name'], '</h3>';
  if (!empty($context['member']['group'])) {
    echo '
        <h4>', $context['member']['group'], '</h4>';
  }
  echo '
      </div>
    </div>
  
    <ul class="profile">
      <li>
        <div class="field" style="border-top: 1px solid #E0E0E0;">
        <div class="field-name">' . $txt['iPosts'] . '</div>
        <div class="field-info">', $context['member']['posts'] . '</div>
        </div>
      </li>', $context['member']['show_email'] == 'yes' || $context['member']['show_email'] == 'yes_permission_override' ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iEmail'] . '</div>
        <div id="email" class="field-info">' . $context['member']['email'] . '</div>
        </div>
      </li>' : '', (!empty($modSettings['titlesEnable']) && !empty($context['member']['title'])) ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iTitle'] . '</div>
        <div class="field-info">' . $context['member']['title'] . '</div>
        </div>
      </li>' : '', ($context['member']['blurb'] != '') ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iText'] . '</div>
        <div class="field-info">' . $context['member']['blurb'] . '</div>
        </div>
      </li>' : '', ($modSettings['karmaMode'] == '1') ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iKarma'] . ' </div>
        <div class="field-info">' . ($context['member']['karma']['good'] - $context['member']['karma']['bad']) . '</div>
        </div>
      </li>' : '', ($modSettings['karmaMode'] == '2') ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iKarma'] . ' </div>
        <div class="field-info">+' . $context['member']['karma']['good'] . '/-' . $context['member']['karma']['bad'] . '</div>
        </div>
      </li>' : '', (!isset($context['disabled_fields']['gender']) && !empty($context['member']['gender']['name'])) ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iGender'] . '</div>
        <div class="field-info">' . $context['member']['gender']['name'] . '</div>
        </div>
      </li>' : '', ($context['member']['age'] > 0) ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iAge'] . '</div>
        <div class="field-info">' . $context['member']['age'] . '</div>
        </div>
      </li>' : '', (!isset($context['disabled_fields']['location']) && !empty($context['member']['location'])) ? '
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iLocation'] . '</div>
        <div class="field-info">' . $context['member']['location'] . '</div>
        </div>
      </li>' : '', '  
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iRegistered'] . '</div>
        <div class="field-info">', str_replace('strong', 'span', $context['member']['registered']), ' </div>
        </div>
      </li>
      <li>
        <div class="field">
        <div class="field-name">' . $txt['iLastActive'] . '</div>
        <div class="field-info">', str_replace('strong', 'span', $context['member']['last_login']), '</div>
        </div>
      </li>
      <li><div class="last field">
        <div class="field-name">' . $txt['iLocalTime'] . '</div>
        <div class="field-info">', $context['member']['local_time'], '</div>
        </div>
      </li>
    </ul>';
}

// Template for editing profile options. We're not going to show that though, let's just give a little info on this theme instead
function template_edit_options()
{
  global $txt;

  //Set the title
  echo '
    <script type="text/javascript">
      $(function(){
        $(".the-title").last().html("iOS.MF");
      });
    </script>';

  //Show some theme information
  echo $txt['iThemeInfo'];
}

function template_showPosts() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}
function template_editBuddies() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_editIgnoreList() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_trackActivity() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_trackIP() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_showPermissions() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_statPanel() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_pm_settings() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_theme_settings() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_notification() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_groupMembership() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_ignoreboards() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_load_warning_variables() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_viewWarning() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_issueWarning() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_deleteAccount() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_save() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_error_message() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_group_manage() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_birthdate() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_signature_modify() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_avatar_select() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_karma_modify() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_timeformat_modify() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_timeoffset_modify() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_theme_pick() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_profile_smiley_pick() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

function template_authentication_method() {
    global $txt;

  echo '
    <div class="errors">
      <div style="margin-top: 6px;">*', $txt['iGoToDefault'], '</div>
    </div>';
}

?>