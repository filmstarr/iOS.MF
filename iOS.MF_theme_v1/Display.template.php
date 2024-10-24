<?php

/*
* View messages and polls within a topic
*
* License: http://www.opensource.org/licenses/mit-license.php
*/

global $settings;

require_once ($settings['theme_dir'] . '/ThemeControls.php');
require_once ($settings['theme_dir'] . '/ThemeFunctions.php');

function template_main() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
    
  //Message CSS for when we aren't showing any avatars
  if (empty($settings['show_user_images']) || !empty($options['show_no_avatars'])) {
    echo '
      <style type="text/css">
        .message { min-height: initial !important; }
        .avatar { display: none; }
        .message-time { margin-bottom: 5px !important; }
      </style>';
  }
  
  //Page specific javascript
  echo '
    <script type="text/javascript">
      $(function() {';
  
  //Show images within page in a lightbox
  echo '
        $(".lightbox-image").magnificPopup({ 
          type: "image",
          showCloseBtn: false,
        });';
  
  //Prevent links from bubbling up and calling the quote functionality or other onclick events of parent elements
  echo '
        function handler(event) {
          event.stopPropagation();
        }
        $("a").click(handler);';
  
  //Add quoting call on messages
  if (!isset($_COOKIE['disablequoting']) && $context['can_reply']) {
    echo '
        $(".message").each(function() {
          $(this).on("click", function() { $(this).parent().addClass("clicked"); });
        });';
  }
  
  echo '
      });
    </script>';
  
  //If a certain message is requested in the URL then navigate to it (jQuery Mobile doesn't seem to honour the fragment identifier)
  script_navigate_to_message();
  
  //Add quick reply functionality. This is added to the header bar
  if ($context['can_reply']) {
    template_control_quick_reply();
  }
  
  //Quoting and reply buttons
  echo '  
    <div class="buttons">
      <button class="button two-buttons" id="quoting" onclick="toggleQuoting();">', (isset($_COOKIE['disablequoting'])) ? $txt['iQuoting'] . ' ' . $txt['iOff'] : $txt['iQuoting'] . ' ' . $txt['iOn'], '</button>
      <button class="button two-buttons" onclick="$.mobile.changePage(\'' . $scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';num_replies=' . $context['num_replies'] . '\');">', $txt['reply'], '</button>
    </div>';
  
  //Display the poll if one exists
  if ($context['is_poll']) {
    echo '
      <div id="poll">
        <div>
          <div class="catbg">
            <div class="sticky"></div>', $txt['poll'], '
          </div>
        </div>
        <div class="windowbg">
          <div id="poll-options">
            <div id="poll-question">
              ', $context['poll']['question'], '
            </div>';
    
    //Are they not allowed to vote but allowed to view the options?
    if ($context['poll']['show_results'] || !$context['allow_vote']) {
      echo '
            <div class="options">';
      
      //Show each option with its corresponding percentage
      foreach ($context['poll']['options'] as $option) {
        if ($context['allow_poll_view']) {
          echo '
              <div class="middle-text', $option['voted_this'] ? ' voted' : '', '">', $option['votes'], ' (', $option['percent'], '%) - ', $option['option'], '
              </div>';
        }
      }
      echo '
            </div>';
    }
    //They are allowed to vote! Go to it!
    else {
      echo '
            <form action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';
      
      //Show a warning if they are allowed more than one option
      if ($context['poll']['allowed_warning']) {
        echo '
              <p class="poll-message">', $context['poll']['allowed_warning'], '
              </p>';
      }
      
      echo '
              <div class="options">';
      
      //Show each option with its button - a radio likely
      foreach ($context['poll']['options'] as $option) {
        echo '
                <div class="middle-text">', $option['vote_button'], ' <label for="', $option['id'], '">', $option['option'], '</label></div>';
      }
      
      echo '
              </div>
              <div class="submit-button">
                <input class="button slim-button" type="submit" value="', $txt['poll_vote'], '" />
                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
              </div>
            </form>';
    }
    
    //Is the clock ticking?
    if (!empty($context['poll']['expire_time'])) {
      echo '
            <p class="poll-message" style="padding-top: 2px;">', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ': ', $context['poll']['expire_time'], '</p>';
    }
    
    echo '
          </div>
        </div>
      </div>';
  }
  
  //If the first post to display is also the newest one then output the "new" tag before the list of messages
  echo $context['first_new_message'] ? '<a id="new"></a>' : '';
  
  //This is the main content of the page: a list of all the messages
  echo '
    <ul class="content-list">';

  while ($message = $context['get_message']()) {
    
    if (!in_array($message['member']['id'], $context['user']['ignoreusers'])) {
      echo '
      <li>
        <a id="msg', $message['id'], '"></a>', ($message['first_new'] && !$context['first_new_message'] ? '<a id="new"></a>' : '');

      //Modify and remove buttons
      echo '
        <div>';
     
      if ($message['can_modify']) {
        echo '
          <button class="button slim-button edit-delete" onclick="$.mobile.changePage(\'', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';', $context['session_var'], '=', $context['session_id'], '\');"> ' . $txt['modify'] . '</button>';
      }
      
      if ($message['can_remove']) {
        echo '
          <button class="button slim-button edit-delete" onclick="if (confirm(\'', $txt['remove_message'], '?\')) { $.mobile.changePage(\'', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '\'); }"> ', $txt['remove'], ' </button>';
      }

      echo '
        </div>';
  
      //Display some information about the user who posted this message
      echo '
        <div class="poster-info" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', isset($message['member']['href']) ? $message['member']['href'] : '', '\')">
          <span class="name">', $message['member']['name'], '</span>';

      if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) {
        if (empty($message['member']['avatar']['image'])) {
          echo '
          <div class="avatar" style="background: url(' . $settings['theme_url'] . '/images/noavatar.png) #F5F5F5 center no-repeat;"></div>';
        } else {
          echo '
          <div class="avatar" style="background: url(' . str_replace(' ', '%20', $message['member']['avatar']['href']) . ') #fff center no-repeat;"></div>';
        }
      }

      echo '
        </div>';

      //The main message content (date/time posted, then the message)
      echo '
        <div class="message"', (!isset($_COOKIE['disablequoting']) && $context['can_reply']) ? '  onclick="$.mobile.changePage(\'' . $scripturl . '?action=post;quote=' . $message['id'] . ';topic=' . $context['current_topic'] . '.' . $context['start'] . ';num_replies=' . $context['num_replies'] . ';' . $context['session_var'] . '=' . $context['session_id'] . '\')"' : '', '>
          <span class="message-time" style="font-style: italic;font-size:11px;display:inline-block;margin-bottom:3px;">', str_replace('strong', 'span', $message['time']), '</span><br />';

      //The message body, replace smilies with retina smilies included in the theme and remove bold from "Today"
      echo
        parse_message($message['body']);
      
      //Attachments
      if (!empty($message['attachment'])) {
        echo '
          <hr>
          <div id="msg_', $message['id'], '_footer" class="attachments smalltext">
            <div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';
        
        //Loop through and show attachments
        $last_approved_state = 1;
        foreach ($message['attachment'] as $attachment) {
          
          // Show a special box for unapproved attachments...
          if ($attachment['is_approved'] != $last_approved_state) {
            $last_approved_state = 0;
            echo '
              <fieldset>
                <legend>', $txt['attach_awaiting_approve'];
            
            if ($context['can_approve']) {
              echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';
            }
            
            echo '
                </legend>';
          }

          //Show a thumbnail if it's an image and we have one available          
          if ($attachment['is_image']) {
            if ($attachment['thumbnail']['has_thumb']) {
              echo '
                <a href="', $attachment['href'], ';image" rel="external" class="lightbox-image" id="link_', $attachment['id'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
            }
            else {
              echo '
                <a href="', $attachment['href'], ';image" rel="external" class="lightbox-image" id="link_', $attachment['id'], '"><img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/></a><br />';
            }
          }

          //The link to the attachment          
          echo '
                <img width="11" height="11" style="position:relative; top: 1px; padding-right: 2px;" src="' . $settings['images_url'] . '/icons/files.png" alt="*" />
                <a rel="external" ', ($attachment['is_image'] ? 'class="lightbox-image"' : 'target="_blank"'), ' href="', $attachment['href'], ($attachment['is_image'] ? '' : ';openFileInNewWindow=' . urlencode($attachment['name'])), '">' . $attachment['name'] . '</a>';
          
          //Approve the attachment?
          if (!$attachment['is_approved'] && $context['can_approve']) {
            echo '
                [<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>]';
          }

          //Some details about the attachment
          echo '
                <br />(', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' - ' . $txt['attach_viewed'] : ' - ' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.)<br />';
        }
        
        //If we had unapproved attachments clean up.
        if ($last_approved_state == 0) {
          echo '
              </fieldset>';
        }
        
        echo '
            </div>
          </div>';
      }
      echo '
        </div>
      </li>';
    }
  }
  
  echo '
    </ul>';

  //Finally, show our paging buttons at the bottom of the page  
  template_control_paging();
}

?>