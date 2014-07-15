<?php

/*
* Deal with the viewing and sending of persanal messages
*/


require_once ($settings[theme_dir] . '/ThemeControls.php');
require_once ($settings[theme_dir] . '/ThemeFunctions.php');

function template_pm_above() {
}

function template_pm_below() {
}

function template_folder() {
  global $context, $settings, $options, $scripturl, $modSettings, $txt;
  
  $messageCount = 0;
  
  if ($context['display_mode'] != 0 && strpos($_SERVER['REQUEST_URI'], 'pmsg') == false) {
    echo '<div class="child buttons no-left-padding">
      <button class="button" style="width: 150px;" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send\');">Compose Message</button>
    </div>';
    
    while ($message = $context['get_pmessage']('subject')) {
      if ($messageCount == 0) {
        echo '<ul class="content-list">';
      }
      
      echo '
      <li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $scripturl . '?action=pm;pmsg=' . $message['id'] . ';msg' . $message['id'] . '#msg' . $message['id'] . '\');">';
      echo '<div class="title', ($message['is_unread'] ? ' short-title' : ''), '">', ($context['display_mode'] == 2 ? preg_replace('/\bRe: /', '', $message['subject']) : $message['subject']), '</div>';
      if ($message['is_unread']) {
        echo '<div class="new">' . $txt['new_button'] . '</div>';
      }
      
      //Get a list of everyone in this message
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
      
      echo '<div class="description">', $members, $message['time'] == 'N/A' ? $txt['no'] . ' ' . $txt['topics'] : iPhoneTime($message['timestamp']), '</div>
      </li>';
      
      $messageCount++;
    }
    
    if ($messageCount != 0) {
      echo '</ul>';
    }
    
    //We're not actually going to show any messages here, so let's set them back to unread.
    $unreadNewPosts = array();
    while ($message = $context['get_pmessage']('message')) {
      if ($message['is_unread']) {
        $unreadNewPosts[] = $message['id'];
      }
    }
    if (count($unreadNewPosts) >= 1) {
      mark_messages_unread($unreadNewPosts);
    }
    
    if ($messageCount == 0) echo '
    <div id="unread-link" style="padding-top: 0 !important;">
      ', $txt['msg_alert_none'], '
    </div>';
    
    template_control_paging();
  } else {
    if ($context['display_mode'] == 0) {
      echo '<div class="child buttons no-left-padding">
        <button class="button" style="width: 150px;" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send\');">Send Message</button>
      </div>';
    }
    
    $subject = "";
    if (empty($settings['show_user_images']) || !empty($options['show_no_avatars'])) {
      echo '<style type="text/css">
        .message { min-height: initial !important; }
        .avatar { display: none; }
        .message-time { margin-bottom: 5px !important; }
      </style>';
    }
    
    echo '<script type="text/javascript">  
      $(function() {
        function handler(event) {
          event.stopPropagation();
        }
        $("a").click(handler);
      });
    </script>';
    
    navigate_to_message_script();
    
    while ($message = $context['get_pmessage']('message')) {
      if ($messageCount == 0) {
        echo '<ul class="content-list ', ($context['display_mode'] != 0 ? ' first-content' : ''), '">';
      }
      echo '
        <li>
          <a id="msg', $message['id'], '"></a>';
      echo '
          <div>
            <button class="button slim-button edit-delete" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != - 1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';u=all\');" >', $txt['reply'], ' ', $txt['all'], '</button>
            <button class="button slim-button edit-delete" onclick="if (confirm(\'', $txt['remove_message'], '?\')) { $.mobile.changePage(\'', $scripturl, '?action=pm;sa=pmactions;pm_actions[', $message['id'], ']=delete;f=', $context['folder'], ';start=', $context['start'], $context['current_label_id'] != - 1 ? ';l=' . $context['current_label_id'] : '', ';', $context['session_var'], '=', $context['session_id'], '\'); }"> ', $txt['remove'], ' </button>
          </div>';
      
      // Show who the message was sent to.
      echo '<div class="description" style="font-style: italic; margin-bottom: -1px; margin-top: 2px;"> ', $txt['sent_to'], ': ';
      
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
      echo '</div>';
      
      echo '<div class="poster-info" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', isset($message['member']['href']) ? $message['member']['href'] : '', '\')"><span class="name">', $message['member']['name'], '</span>';
      if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) if (empty($message['member']['avatar']['image'])) {
        echo '<div class="avatar" style="background: url(' . $settings['theme_url'] . '/images/noavatar.png) #F5F5F5 center no-repeat;"></div>';
      } else {
        echo '<div class="avatar" style="background: url(' . str_replace(' ', '%20', $message['member']['avatar']['href']) . ') #fff center no-repeat;"></div>';
      }
      echo '
        
          </div>
          
          <div class="message" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != - 1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';quote', $context['folder'] == 'sent' ? '' : ';u=all\');">
            <span class="message-time" style="font-style: italic;font-size:11px;display:inline-block;margin-bottom:3px;">', str_replace('strong', 'span', $message['time']), '</span><br />
          ', str_replace(rtrim($scripturl, '/index.php') . '/Smileys/default/', $settings['theme_url'] . '/images/SkypeEmoticons/', str_replace('<strong>Today</strong>', 'Today', short1($message['body']))), '
          </div>

        </li>';
      
      $subject = $message['subject'];
      $messageCount++;
    }
    
    if ($messageCount != 0) {
      echo '</ul>';
    }
    
    if (strpos($_SERVER['REQUEST_URI'], 'pmsg') == true) {
      echo '
      <script type="text/javascript">
        $(function(){
          $(".the-title").last().html("', ($context['display_mode'] == 2 ? preg_replace('/\bRe: /', '', $subject) : $subject), '");
        });
      </script>';
    }
    
    if ($messageCount == 0) echo '
    <div id="unread-link" style="padding-top: 0 !important;">
      ', $txt['msg_alert_none'], '
    </div>';
    
    if ($context['display_mode'] == 0) {
      template_control_paging();
    }
  }
}

function template_send() {
  global $context, $settings, $options, $scripturl, $modSettings, $txt;
  
  echo '<script type="text/javascript">
      $(function(){
        $(".editor").last().autosize().resize();
        $(".classic").last().hide();
        
        //Deal with the race condition between iOS keyboard showing and the focus event firing
        if(/iPhone|iPod|Android|iPad/.test(window.navigator.platform)){
          var jqElement = $(".editor").last();
          jqElement.attr("disabled", true);

          jqElement.on("tap", function(event) {
            if (event.target.id == "', $context['post_box_name'], '") {
              if (!$(event.target).is(":focus")) {

                // Hide toolbar
                $(".toolbar").css("display", "none");
                $("#copyright").css("margin-bottom", "4px");

                //Enable and focus textbox
                $(event.target).removeAttr("disabled");
                $(event.target).focus();

                //Move caret to end
                jqElement.get(0).setSelectionRange(jqElement.val().length, jqElement.val().length);
              }
            }
          });

          jqElement.on("blur", function(e) {
            jqElement.attr("disabled", true);
          });
        }
      });

    </script>';
  
  echo '<form data-ajax="false" action="', $scripturl, '?action=pm;sa=send2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" class="flow_hidden" onsubmit="submitonce(this);smc_saveEntities(\'postmodify\', [\'subject\', \'message\']);">';
  
  if (!empty($context['post_error']['messages']) && count($context['post_error']['messages'])) {
    echo '<div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['post_error']['messages']), '</div></div>';
    echo '<style type="text/css"> #new-topic { padding-top: 9px; } </style>';
  }
  
  if (empty($context['to_value'])) {
    echo '<div id="new-topic" class="input-container">';
    echo '<span class="input-label">' . $txt['iTo'] . '</span>';
    
    //Users drop down list
    $users = user_list();
    echo '<select name="to" tabindex="', $context['tabindex']++, '" form="postmodify" style="padding-left: 4px;">';
    echo '<option></option>';
    foreach ($users as $user) {
      echo '<option>' . $user . '</option>';
    }
    echo '</select>';
    echo '</div>';
  } else {
    echo '<input type="hidden" name="to" value="', $context['to_value'], '" />';
  }
  
  if (empty($context['subject']) || $context['subject'] == $txt['no_subject']) {
    echo '<div id="new-topic" class="input-container">';
    echo '<span class="input-label">Subject</span>';
    echo '<input type="text" tabindex="', $context['tabindex']++, '" name="subject" value="" maxlength="50" />';
    echo '</div>';
  } else {
    echo '<input type="hidden" name="subject" value="', $context['subject'], '" />';
    echo '
    <script type="text/javascript">
      $(function(){
        $(".the-title").last().html("', $context['subject'], '");
      });
    </script>';
  }
  
  echo '
    <div id="post-container" class="input-container">
      <div class="new-post">
           ', template_control_richedit($context['post_box_name']), '
      </div>
    </div>';
  
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
  
  echo '<div class="child buttons">
  
  <button class="button" type="submit" onclick="$(\'.editor\').last().blur(); $(\'.editor\').last().removeAttr(\'disabled\'); $(\'.ui-loader\').loader(\'show\'); if ($(\'input[name=subject]\').val() == \'\') { $(\'input[name=subject]\').val(\'Sent from iOS.MF\'); } ">', $txt['iSend'], '</button>

  </div>';
  
  echo '
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