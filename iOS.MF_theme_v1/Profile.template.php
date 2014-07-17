<?php

/*
* User profile templates. We won't provide the ability to edit the users profile here, users should go to a different theme to do this
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
      <div id="username"><h3>', $context['member']['name'], '</h3><h4>', $context['member']['group'], '</h4></div>
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
        <div class="field-info">', $context['member']['registered'], ' </div>
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
  //Set the title
  echo '
    <script type="text/javascript">
      $(function(){
        $(".the-title").last().html("iOS.MF");
      });
    </script>';

  //Show some theme information
  echo '
    <h2>Welcome to iOS.MF</h2>
    <ul class="readme">
      <li>
        An iOS optimised theme for iPhone, iPad and iPod Touch.
      </li>
      <li>
        Please use the forum default theme for full access to settings and further functionality.
      </li>
    </ul>

    <h2>Gestures and Navigation</h2>
    <ul class="readme" style="margin-bottom: 4px;">
      <li>
        Swipe left to go back.
      </li>
      <li>
        Swipe right to go forward.
      </li>
      <li>
        Press and hold the previous/next page buttons to go to first/last page respectively.
      </li>
      <li>
        Tap on the topic title to access quick reply.
      </li>
      <li>
        Tap the default theme button below to go to the forum default theme. Press and hold the default theme button to go to the SMF default theme.
      </li>
    </ul>';
}
?>