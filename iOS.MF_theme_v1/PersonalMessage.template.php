<?php

/*
* Deal with the viewing and sending of persanal messages
*/


require_once ($settings[theme_dir] . '/ThemeControls.php');
require_once ($settings[theme_dir] . '/ThemeFunctions.php');

//We won't show anything above or below the personal message template
function template_pm_above() {
}

function template_pm_below() {
}

//This function shows both message folders (similar to a forum topic) or the actual messages (similar to forum messages) depending on the user options and resource requested
function template_folder() {
  global $context, $settings, $options, $scripturl, $modSettings, $txt;
  
  $messageCount = 0;
  
  //Here we are showing a list of the message folders available to view
  //display mode == 1 - one at a time
  //display mode == 2 - as a conversation
  if ($context['display_mode'] != 0 && strpos($_SERVER['REQUEST_URI'], 'pmsg') == false) {

    //Compose new message button
    echo '
        <div class="child buttons no-left-padding">
          <button class="button" style="width: 150px;" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send\');">Compose Message</button>
        </div>';

    //Output a list of the message folders    
    while ($message = $context['get_pmessage']('subject')) {
      if ($messageCount == 0) {
        echo '
        <ul class="content-list">';
      }
      echo '
          <li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $scripturl . '?action=pm;pmsg=' . $message['id'] . ';msg' . $message['id'] . '#msg' . $message['id'] . '\');">
            <div class="title', ($message['is_unread'] ? ' short-title' : ''), '">', ($context['display_mode'] == 2 ? preg_replace('/\bRe: /', '', $message['subject']) : $message['subject']), '</div>';
      if ($message['is_unread']) {
        echo '
            <div class="new">' . $txt['new_button'] . '</div>';
      }
      
      //Output a list of everyone in the last message and when it was posted
      $members = '';
      $selfie = false;
      foreach (array_merge($message['recipients']['to'], array($message['member']['name'])) as $recipient) {
        preg_match('/\>(.*)\</', $recipient, $match);
        if (count($match) >= 2) {
          $recipient = $match[1];
        }
        
        if ($recipient != $context['user']['username']) {
          $members = $member . $recipient . ', ';
        } else {
          $selfie = true;
        }
      }
      if ($members == '' && $selfie) {
        $members = $context['user']['username'] . ', ';
      }
      echo '
            <div class="description">', $members, $message['time'] == 'N/A' ? $txt['no'] . ' ' . $txt['topics'] : parse_time($message['timestamp']), '</div>
          </li>';
      
      $messageCount++;
    }
    if ($messageCount != 0) {
      echo '
        </ul>';
    }
    
    //Unlike the default SMF theme we're not actually going to show any messages here, so we need to set them back to unread
    $unreadNewPosts = array();
    while ($message = $context['get_pmessage']('message')) {
      if ($message['is_unread']) {
        $unreadNewPosts[] = $message['id'];
      }
    }
    if (count($unreadNewPosts) >= 1) {
      mark_messages_unread($unreadNewPosts);
    }
    
    //We don't have any message folders to show
    if ($messageCount == 0) {
      echo '
        <div id="unread-link" style="padding-top: 0 !important;">
          ', $txt['msg_alert_none'], '
        </div>';
    }
    
    //Show our paging buttons
    template_control_paging();
  } else {

    //Here we are showing some actual messages; either the display mode is to show all at once and we have no folders, or we've drilled down to a message or conversation from a folder
    //display mode == 0 - all at once

    //If we are showing all messages at once then we need a compose new message button
    if ($context['display_mode'] == 0) {
      echo '
        <div class="child buttons no-left-padding">
          <button class="button" style="width: 150px;" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send\');">Compose Message</button>
        </div>';
    }

    //Add some CSS to style the page when we're not showing avatars
    if (empty($settings['show_user_images']) || !empty($options['show_no_avatars'])) {
      echo '
        <style type="text/css">
          .message { min-height: initial !important; }
          .avatar { display: none; }
          .message-time { margin-bottom: 5px !important; }
        </style>';
    }

    //Prevent links from bubbling up and calling the quote functionality or other onclick events of parent elements   
    echo '
        <script type="text/javascript">  
          $(function() {
            function handler(event) {
              event.stopPropagation();
            }
            $("a").click(handler);
          });
        </script>';
    
    //If a certain message is requested in the URL then navigate to it (jQuery Mobile doesn't seem to honour the fragment identifier)
    script_navigate_to_message();
    
    //Loop through and output all the relevant messages
    $subject = "";
    while ($message = $context['get_pmessage']('message')) {
      if ($messageCount == 0) {
        echo '
        <ul class="content-list ', ($context['display_mode'] != 0 ? ' first-content' : ''), '">';
      }
      echo '
          <li>
            <a id="msg', $message['id'], '"></a>';

      //Reply and delete buttons
      echo '
            <div>
              <button class="button slim-button edit-delete" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != - 1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';u=all\');" >', $txt['reply'], ' ', $txt['all'], '</button>
              <button class="button slim-button edit-delete" onclick="if (confirm(\'', $txt['remove_message'], '?\')) { $.mobile.changePage(\'', $scripturl, '?action=pm;sa=pmactions;pm_actions[', $message['id'], ']=delete;f=', $context['folder'], ';start=', $context['start'], $context['current_label_id'] != - 1 ? ';l=' . $context['current_label_id'] : '', ';', $context['session_var'], '=', $context['session_id'], '\'); }"> ', $txt['remove'], ' </button>
            </div>';
      
      // Show who the message was sent to.
      echo '
            <div class="description" style="font-style: italic; margin-bottom: -1px; margin-top: 2px;"> ', $txt['sent_to'], ': ';
      
      // People it was sent directly to....
      if (!empty($message['recipients']['to'])) {
        $first = true;
        foreach ($message['recipients']['to'] as $recipient) {
          preg_match('/\>(.*)\</', $recipient, $match);
          if (count($match) >= 2) {
            $recipient = $match[1];
          }
          echo ($first ? '' : ', ') . $recipient;
          $first = false;
        }
      }
      // Otherwise, we're just going to say "some people"...
      elseif ($context['folder'] != 'sent') {
        echo '(', $txt['pm_undisclosed_recipients'], ')';
      }
      echo '
            </div>';
      
      //Some information about the user who sent this message
      echo '
            <div class="poster-info" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', isset($message['member']['href']) ? $message['member']['href'] : '', '\')">
              <span class="name">', $message['member']['name'], '</span>';
      if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) if (empty($message['member']['avatar']['image'])) {
        echo '
              <div class="avatar" style="background: url(' . $settings['theme_url'] . '/images/noavatar.png) #F5F5F5 center no-repeat;"></div>';
      } else {
        echo '
              <div class="avatar" style="background: url(' . str_replace(' ', '%20', $message['member']['avatar']['href']) . ') #fff center no-repeat;"></div>';
      }
      echo '
            </div>';

      //Then the actual message
      echo '
            <div class="message" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != - 1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';quote', $context['folder'] == 'sent' ? '' : ';u=all\');">
              <span class="message-time" style="font-style: italic;font-size:11px;display:inline-block;margin-bottom:3px;">', str_replace('strong', 'span', $message['time']), '</span><br />
              ', parse_message($message['body']), '
            </div>
        </li>';
      
      //Pickup the subject of the last message encountered
      $subject = $message['subject'];
      $messageCount++;
    }
    
    if ($messageCount != 0) {
      echo '
      </ul>';
    }
    
    //Set the title via javascript if we're viewing a specific message
    if (strpos($_SERVER['REQUEST_URI'], 'pmsg') == true) {
      echo '
      <script type="text/javascript">
        $(function(){
          $(".the-title").last().html("', ($context['display_mode'] == 2 ? preg_replace('/\bRe: /', '', $subject) : $subject), '");
        });
      </script>';
    }
    
    //No messages to show
    if ($messageCount == 0) echo '
    <div id="unread-link" style="padding-top: 0 !important;">
      ', $txt['msg_alert_none'], '
    </div>';
    
    //Paging buttons
    if ($context['display_mode'] == 0) {
      template_control_paging();
    }
  }
}

//Form used to send a personal message to another user
function template_send() {
  global $context, $settings, $options, $scripturl, $modSettings, $txt;
  
  //Hide the toolbar when showing the keyboard on a mobile device
  script_hide_toolbar();
  
  echo '
    <form data-ajax="false" action="', $scripturl, '?action=pm;sa=send2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" class="flow_hidden" onsubmit="submitonce(this);smc_saveEntities(\'postmodify\', [\'subject\', \'message\']);">';
  
  //Show any errors
  if (!empty($context['post_error']['messages']) && count($context['post_error']['messages'])) {
    echo '
      <div class="errors">
        <div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['post_error']['messages']), '</div>
      </div>
      <style type="text/css"> #new-topic { padding-top: 9px; } </style>';
  }

  //If there were errors (excuding no recipients selected) then show who we're trying to send the message to as a text box in case there was a problem with that
  if (!empty($context['post_error']['messages']) && count($context['post_error']['messages']) && !empty($context['to_value'])) {
    echo '
      <div id="new-topic" class="input-container">
        <span class="input-label">' . $txt['iTo'] . '</span>';
    echo
       '<input type="text" tabindex="', $context['tabindex']++, '" name="to" value="', $context['to_value'], '" maxlength="50" />';
    echo '
      </div>';        
  }
  //There are no errors so we can just show the usual recipient selection controls
  else {
    //If we're sending a new message then we need to know who to send it to
    if (empty($context['to_value'])) {
      echo '
        <div id="new-topic" class="input-container">
          <span class="input-label">' . $txt['iTo'] . '</span>';

      //The user drop down list has been disabled so we'll just show a simple text input
      if (isset($settings['replace_PM_ddl_with_text_input']) && $settings['replace_PM_ddl_with_text_input']) {
        echo '<input type="text" tabindex="', $context['tabindex']++, '" name="to" value="" maxlength="50" />';
      }
      //Show a drop down list of all the users
      else {
        $users = user_list();
        echo '<select class="user-list" tabindex="', $context['tabindex']++, '" style="padding-left: 4px;" onchange="if (this.selectedIndex) {addToUser();}">';
        echo '<option></option>';
        foreach ($users as $user) {
          echo '<option>' . $user . '</option>';
        }
        echo '</select>';

        //Javascript functions to add a new user to the recipient list and update the form input
        echo '
          <script type="text/javascript">
            var addToUser = function() {
              var user = $(".user-list").last().find(":selected");
              var userSpan = \'<span onclick="removeToUser(this);">\' + user.text() + \'</span>\';
              var toUserList = $(".to-user-list").last();
              if (toUserList.html().indexOf(userSpan) == -1) {
                toUserList.append(userSpan);
              }

              setToUserString();
              
              toUserList.addClass("small-pad-top");
              
              user.remove();
            };

            var removeToUser = function(element) {
              element.remove();

              var toUserList = $(".to-user-list").last();
              if (toUserList.html() == "") {
                toUserList.removeClass("small-pad-top");
              }

              setToUserString();

              var userList = $(".user-list").last();
              userList.append("<option>" + element.innerHTML + "</option>");

              var selectList = userList.children();
              selectList.sort(function(a,b){
                return a.value.localeCompare(b.value);
              });
              userList.html(selectList);
            };

            var setToUserString = function() {
              var toUserList = $(".to-user-list").last();
              var userString = "";
              toUserList.children().each(
                function() {
                  userString += this.innerHTML + ","
                }
              );
              document.getElementsByName("to")[0].value = userString;
            };
          </script>';

        echo '
          <div class="to-user-list"></div>
          <input type="hidden" name="to" value="', $context['to_value'], '" />';
      }

      echo '
        </div>';

    } else {
      //Otherwise we're replying to someone so we'll keep this hidden
      echo '
        <input type="hidden" name="to" value="', $context['to_value'], '" />';
    }
  }
  
  //What's the subject of this message?
  if (empty($context['subject']) || $context['subject'] == $txt['no_subject']) {
    echo '
      <div id="new-topic" class="input-container">';
    echo '
        <span class="input-label">Subject</span>';
    echo '<input type="text" tabindex="', $context['tabindex']++, '" name="subject" value="" maxlength="50" />';
    echo '
      </div>';
  } else {
    //If we've already got one then let's use it for the page title
    echo '
      <input type="hidden" name="subject" value="', $context['subject'], '" />';
    echo '
      <script type="text/javascript">
        $(function(){
          $(".the-title").last().html("', $context['subject'], '");
        });
      </script>';
  }
  
  //This is the container for our message
  echo '
      <div id="post-container" class="input-container">
        <div class="new-post">
          ', template_control_richedit($context['post_box_name']), '
        </div>
      </div>';
  
  //Verification control
  if ($context['require_verification']) {
    echo '<div class="no-left-padding input-container">';
    echo '<span class="input-label">Code</span>';
    echo template_control_verification($context['visual_verification_id'], 'all');
    echo '</div>';
    echo '<div class="no-left-padding input-container">';
    echo '<span class="input-label">Verify</span>';
    echo '<input type="text" tabindex="', $context['tabindex']++, '" name="pm_vv[code]" />';
    echo '</div>';
  }
  
  //Submit button and other inputs
  echo '
    <div class="child buttons">
      <button class="button" type="submit" onclick="$(\'.editor\').last().blur(); $(\'.editor\').last().removeAttr(\'disabled\'); $(\'.ui-loader\').loader(\'show\'); if ($(\'input[name=subject]\').val() == \'\') { $(\'input[name=subject]\').val(\'Sent from iOS.MF\'); } ">', $txt['iSend'], '</button>
    </div>
    <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
    <input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
    <input type="hidden" name="replied_to" value="', !empty($context['quoted_message']['id']) ? $context['quoted_message']['id'] : 0, '" />
    <input type="hidden" name="pm_head" value="', !empty($context['quoted_message']['pm_head']) ? $context['quoted_message']['pm_head'] : 0, '" />
    <input type="hidden" name="f" value="', isset($context['folder']) ? $context['folder'] : '', '" />
    <input type="hidden" name="l" value="', isset($context['current_label_id']) ? $context['current_label_id'] : -1, '" />
    <input type="hidden" name="outbox" value="', $context['copy_to_outbox'] ? '1' : '0', '" />
  </form>';
}
?>