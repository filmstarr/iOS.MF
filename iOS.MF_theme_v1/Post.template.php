<?php

/*
* Post a new topic on the forum or a reply to an existing topic
*/


function template_main() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  //Set the page title, initialise editor autosizing and hide the default theme button
  echo '
    <script type="text/javascript">
      $(function(){
        $(".the-title").last().html("', (empty($context['subject']) ? 'New Topic' : $context['subject']), '");
        $(".editor").last().autosize().resize();
        $(".classic").last().hide();
      });
    </script>';

  //Hide the toolbar when showing the keyboard on a mobile device
  script_hide_toolbar();

  echo '
    <form data-ajax="false" action="', $scripturl, '?action=', $context['destination'], ';', empty($context['current_board']) ? '' : 'board=' . $context['current_board'], '" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="', ($context['becomes_approved'] ? '' : 'alert(\'' . $txt['js_post_will_require_approval'] . '\');'), 'submitonce(this);smc_saveEntities(\'postmodify\', [\'subject\', \'', $context['post_box_name'], '\', \'guestname\', \'evtitle\', \'question\'], \'options\');" enctype="multipart/form-data" style="margin: 0;">';
  
  //Show any errors
  if (!empty($context['post_error']['messages']) && count($context['post_error']['messages'])) {
    echo '
      <div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['post_error']['messages']), '</div></div>';
    if (empty($context['subject'])) {
      echo '
      <style type="text/css"> .no-left-padding { padding-top: 9px; } </style>';
    } else {
      echo '
      <style type="text/css"> #post-container { padding-top: 9px; } </style>';
    }
  }
  
  //What's the subject of this new topic?
  if (empty($context['subject'])) {
    echo '
      <div class="input-container no-left-padding">
        <span class="input-label">' . $txt['topic'] . '</span>';
    echo '<input type="text" tabindex="1" name="subject" value="' . $context['subject'] . '" />
      </div>';
  }
  
  //This is the container for our message
  echo '
      <div id="post-container" class="input-container">
        <div class="new-post">
          ', template_control_richedit($context['post_box_name']), '
        </div>
      </div>';
  
  //Attachments
  if (!empty($context['current_attachments']) || $context['can_post_attachment']) {
    echo '
      <div id="postAttachment" class="attachment-wrapper">';
    
    //Existing attachments
    if (!empty($context['current_attachments'])) {
      echo '
        <input type="hidden" name="attach_del[]" value="0" />';
      foreach ($context['current_attachments'] as $attachment) {
        echo '
        <div class="attachment">
          <input type="checkbox" id="attachment_', $attachment['id'], '" name="attach_del[]" value="', $attachment['id'], '"', empty($attachment['unchecked']) ? ' checked="checked"' : '', ' /> ', $attachment['name'], (empty($attachment['approved']) ? ' (' . $txt['awaiting_approval'] . ')' : ''), '
        </div>';
      }
    }
    
    //Additional attachments
    if ($context['can_post_attachment']) {
      echo '
        <div style="position: relative;">
          <input type="file" name="attachment[]" id="input-file" style="padding-left: 5px;" onclick="setTimeout(function() { showToolbar(); }, 10);" />
          <div id="input-button-background">
            <button id="input-button" type="button" class="needsclick button" style="height: 19px;" onclick="document.getElementById(\'input-file\').click();this.blur();">' . $txt['iChooseFile'] . '</button>
          </div>
        </div>';
    }
    
    echo '
      </div>';
  }
  
  //Guests have to put in their name and email...
  if (isset($context['name']) && isset($context['email'])) {
    echo '
      <div class="no-left-padding input-container">
        <span class="input-label">' . $txt['username'] . '</span>';
    echo '<input type="text" name="guestname" size="25" value="', $context['name'], '" tabindex="', $context['tabindex']++, '" class="input_text" />
        <span id="smf_autov_username_div" style="display: none;">
          <a id="smf_autov_username_link" href="#">
            <img id="smf_autov_username_img" src="', $settings['images_url'], '/icons/field_check.png" alt="*" />
          </a>
        </span>
      </div>';
    
    if (empty($modSettings['guest_post_no_email'])) {
      echo '
      <div class="no-left-padding input-container">
        <span class="input-label">' . $txt['email'] . '</span>';
      echo '<input type="text" name="email" size="25" value="', $context['email'], '" tabindex="', $context['tabindex']++, '" class="input_text" />
      </div>';
    }
  }
  
  //Verification control
  if ($context['require_verification']) {
    echo '<div class="no-left-padding input-container">';
    echo '<span class="input-label">' . $txt['iCode'] . '</span>';
    echo template_control_verification($context['visual_verification_id'], 'all');
    echo '</div>';
    echo '<div class="no-left-padding input-container">';
    echo '<span class="input-label">' . $txt['iVerify'] . '</span>';
    echo '<input type="text" tabindex="', $context['tabindex']++, '" name="post_vv[code]" />';
    echo '</div>';
  }
  
  //Submit button
  echo '
      <div class="child buttons">
        <button class="button" type="submit" onclick="$(\'.editor\').last().blur(); $(\'.editor\').last().removeAttr(\'disabled\'); $(\'.ui-loader\').loader(\'show\');">', $txt['iPost'], '</button>
      </div>';

  //Other inputs  
  if (isset($context['num_replies'])) {
    echo '
      <input type="hidden" name="num_replies" value="', $context['num_replies'], '" />';
  }
  if (!empty($context['subject'])) {
    echo '
      <input type="hidden" name="subject" value="' . $context['subject'] . '" />';
  }
  echo '
      <input type="hidden" name="additional_options" value="', $context['show_additional_options'] ? 1 : 0, '" />
      <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
      <input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
      <input type="hidden" name="topic" value="', $context['current_topic'], '" />
      <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />    
      <input type="hidden" name="goback" value="', $options['return_to_post'], '" />
    </form>';
}

?>