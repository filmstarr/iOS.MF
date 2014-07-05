<?php
// Version: 2.0 RC4; Display

function template_main()
{
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  $ignoredMsgs = array();
  $messageIDs = array();

  if (empty($settings['show_user_images']) || !empty($options['show_no_avatars']))
  {
    echo '<style>
      .message { min-height: initial !important; }
      #avatar { display: none; }
      .message_time { margin-bottom: 5px !important; }
    </style>';
  }

  echo '<script>  
    $(function() {
      $(".lightbox-image").magnificPopup({ 
        type: "image",
        showCloseBtn: false,
      });

      function handler(event) {
        event.stopPropagation();
      }
      $("a").click(handler);';

      if (!isset($_COOKIE['disablequoting'])&&$context['can_reply'])
      {
        echo'
          $(".message").each(function() {
            $(this).on("click", function() { $(this).parent().addClass("clicked"); });
          });';
      }

    echo '
    });
  </script>';

  require_once ($settings[theme_dir].'/ThemeFunctions.php');
  NavigateToMessageScript();

  if ($context['can_reply'])
  {
    quick_reply();
  }
    
  echo'  
    <div class="buttons">

  <button class="button twobuttons" id="quoting" onclick="quoting();">', (isset($_COOKIE['disablequoting'])) ? $txt['iQuoting'].' '.$txt['iOff']:$txt['iQuoting'].' '.$txt['iOn'], '</button>

  <button class="button twobuttons" onclick="$.mobile.changePage(\''.$scripturl . '?action=post;topic=' . $context['current_topic'] . '.' . $context['start'] . ';num_replies=' . $context['num_replies'].'\');">', $txt['reply'] ,'</button>'; echo'
    </div>';
  
  // Is this topic also a poll?
  if ($context['is_poll'])
  {
    echo '
      <div id="poll">
        <div class="cat_bar">
          <h3 class="catbg">
            <div class="sticky"></div>', $txt['poll'], '
          </h3>
        </div>
        <div class="windowbg">

          <div id="poll_options">
            <div id="pollquestion">
              ', $context['poll']['question'], '
            </div>';

    // Are they not allowed to vote but allowed to view the options?
    if ($context['poll']['show_results'] || !$context['allow_vote'])
    {
      echo '
          <dl class="options">';

      // Show each option with its corresponding percentage bar.
      foreach ($context['poll']['options'] as $option)
      {
        if ($context['allow_poll_view'])
          echo '<div class="middletext', $option['voted_this'] ? ' voted' : '', '">' , $option['votes'], ' (', $option['percent'], '%) - ' , $option['option'], '</div>';
      }
    }
    // They are allowed to vote! Go to it!
    else
    {
      echo '
            <form action="', $scripturl, '?action=vote;topic=', $context['current_topic'], '.', $context['start'], ';poll=', $context['poll']['id'], '" method="post" accept-charset="', $context['character_set'], '">';

      // Show a warning if they are allowed more than one option.
      if ($context['poll']['allowed_warning'])
        echo '
              <p class="smallpadding">', $context['poll']['allowed_warning'], '</p>';

      echo '
              <ul class="reset options">';

      // Show each option with its button - a radio likely.
      foreach ($context['poll']['options'] as $option)
        echo '
                <li class="middletext">', $option['vote_button'], ' <label for="', $option['id'], '">', $option['option'], '</label></li>';

      echo '
              </ul>
              <div class="submitbutton">
                <input class="button slimbutton" type="submit" value="', $txt['poll_vote'], '" />
                <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
              </div>
            </form>';
    }

    // Is the clock ticking?
    if (!empty($context['poll']['expire_time']))
      echo '
            <p>', ($context['poll']['is_expired'] ? $txt['poll_expired_on'] : $txt['poll_expires_on']), ': ', $context['poll']['expire_time'], '</p>';

    echo '
          </div>

        </div>
      </div>
      <div id="pollmoderation">';

    // Build the poll moderation button array.
    $poll_buttons = array(
      'vote' => array('test' => 'allow_return_vote', 'text' => 'poll_return_vote', 'image' => 'poll_options.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start']),
      'results' => array('test' => 'show_view_results_button', 'text' => 'poll_results', 'image' => 'poll_results.gif', 'lang' => true, 'url' => $scripturl . '?topic=' . $context['current_topic'] . '.' . $context['start'] . ';viewresults'),
      'change_vote' => array('test' => 'allow_change_vote', 'text' => 'poll_change_vote', 'image' => 'poll_change_vote.gif', 'lang' => true, 'url' => $scripturl . '?action=vote;topic=' . $context['current_topic'] . '.' . $context['start'] . ';poll=' . $context['poll']['id'] . ';' . $context['session_var'] . '=' . $context['session_id']),
      'lock' => array('test' => 'allow_lock_poll', 'text' => (!$context['poll']['is_locked'] ? 'poll_lock' : 'poll_unlock'), 'image' => 'poll_lock.gif', 'lang' => true, 'url' => $scripturl . '?action=lockvoting;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
      'edit' => array('test' => 'allow_edit_poll', 'text' => 'poll_edit', 'image' => 'poll_edit.gif', 'lang' => true, 'url' => $scripturl . '?action=editpoll;topic=' . $context['current_topic'] . '.' . $context['start']),
      'remove_poll' => array('test' => 'can_remove_poll', 'text' => 'poll_remove', 'image' => 'admin_remove_poll.gif', 'lang' => true, 'custom' => 'onclick="return confirm(\'' . $txt['poll_remove_warn'] . '\');"', 'url' => $scripturl . '?action=removepoll;topic=' . $context['current_topic'] . '.' . $context['start'] . ';' . $context['session_var'] . '=' . $context['session_id']),
    );

    template_button_strip($poll_buttons);

    echo '
      </div>';
  }  

  //echo '<a id="msg', $context['first_message'], '"></a>', $context['first_new_message'] ? '<a id="new"></a>' : '';    
  echo $context['first_new_message'] ? '<a id="new"></a>' : '';    

  echo'<ul class="content2">';
    
  $i=0;
  
  while ($message = $context['get_message']())
  {    
    $ignoring = false;
    $messageIDs[] = $message['id'];

    if (!in_array($message['member']['id'], $context['user']['ignoreusers']))
    {
    $i++;
        
  echo '<li>
   <a id="msg', $message['id'], '"></a>', $message['first_new'] ? '<a id="new"></a>' : '' , '   
   
  <div>';
// Can the user modify the contents of this post?
      if ($message['can_modify'])
         echo '
                  <button class="button slimbutton" id="editdel" onclick="$.mobile.changePage(\'', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';', $context['session_var'], '=', $context['session_id'], '\');"> '. $txt['modify'].' </button>';
   // How about... even... remove it entirely?!
      if ($message['can_remove'])
         echo '
              <button class="button slimbutton" id="editdel" onclick="if (confirm(\'', $txt['remove_message'], '?\')) { $.mobile.changePage(\'', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '\'); }"> ', $txt['remove'],' </button>';
    echo '</div>
  
      <div class="posterinfo" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', isset($message['member']['href']) ? $message['member']['href'] : '' ,'\')"><span class="name">', $message['member']['name'] ,'</span>';
      if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']))
        if (empty($message['member']['avatar']['image'])) {
          echo '<div id="avatar" style="background: url('.$settings['theme_url'].'/images/noavatar.png) #F5F5F5 center no-repeat;"></div>';
        }
        else {
          echo '<div id="avatar" style="background: url('.str_replace(' ','%20', $message['member']['avatar']['href']).') #fff center no-repeat;"></div>';
        }
      echo '
    
    </div>
        <div class="message"', (!isset($_COOKIE['disablequoting'])&&$context['can_reply']) ? '  onclick="$.mobile.changePage(\''. $scripturl. '?action=post;quote='. $message['id'].
     ';topic='. $context['current_topic'].
        '.'. $context['start']. ';num_replies='. $context['num_replies']. ';'. $context['session_var']. '='. $context['session_id']. '\')"':'','><span class="message_time" style="font-style: italic;font-size:11px;display:inline-block;margin-bottom:3px;">', str_replace('strong','span',$message['time']) ,'</span><br />
    ', str_replace(rtrim($scripturl,'/index.php') . '/Smileys/default/', $settings['theme_url'] . '/images/SkypeEmoticons/',str_replace('<strong>Today</strong>','Today',short1($message['body'])));

    // Assuming there are attachments...
    if (!empty($message['attachment']))
    {

      echo '<hr>
              <div id="msg_', $message['id'], '_footer" class="attachments smalltext">
                <div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';

      $last_approved_state = 1;
      foreach ($message['attachment'] as $attachment)
      {
        // Show a special box for unapproved attachments...
        if ($attachment['is_approved'] != $last_approved_state)
        {
          $last_approved_state = 0;
          echo '
                  <fieldset>
                    <legend>', $txt['attach_awaiting_approve'];

          if ($context['can_approve'])
            echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';

          echo '</legend>';
        }

        if ($attachment['is_image'])
        {
          if ($attachment['thumbnail']['has_thumb'])
            echo '
                    <a href="', $attachment['href'], ';image" rel="external" class="lightbox-image" id="link_', $attachment['id'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
          else
            echo '
                    <a href="', $attachment['href'], ';image" rel="external" class="lightbox-image" id="link_', $attachment['id'], '"><img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/></a><br />';
        }

        echo '
                    <img width="11px" height="11px" style="position:relative; top:-5px;" src="' . $settings['images_url'] . '/icons/files.png" align="middle" alt="*" />&nbsp;<a rel="external" ', ($attachment['is_image'] ? 'class="lightbox-image"' : 'target="_blank"'), ' href="' , $attachment['href'] , ($attachment['is_image'] ? '' : ';openFileInNewWindow=' . urlencode($attachment['name'])),  '">' . $attachment['name'] . '</a> ';

        if (!$attachment['is_approved'] && $context['can_approve'])
          echo '
                    [<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
        echo '
                    <br />(', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' - ' . $txt['attach_viewed'] : ' - ' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.)<br />';
      }

      // If we had unapproved attachments clean up.
      if ($last_approved_state == 0)
        echo '
                  </fieldset>';

      echo '
                </div>
              </div>';
    }
    echo '
    </div>
      </li>
    
    ';
    }
  }
  
  echo'</ul>';

  require_once ($settings[theme_dir].'/ThemeControls.php');
  template_control_paging();
}
  
function quick_reply()
{    
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
        
    $quickReply = '<script type="text/javascript">

    $(function(){
      $(".editor").last().autosize();
    });

    var toggleQuickReply = function() {
      if ($("#quickReply").is(":visible"))
      {
        $("#message").blur();
        $("#quickReply").hide();      
      }
      else
      {
        $("#quickReply").show();
        $("#message").focus();
      }
      setTopMargin();
    };

    var title = $(".theTitle").last().get(0);
    title.onclick = toggleQuickReply;
    title.style.color = "#007AFF";

    </script>';
    
    $quickReply .= '<div id="quickReply">';
    $quickReply .= '<form action="' . $scripturl . '?action=post2;' . (empty($context['current_board']) ? '' : 'board=') . $context['current_board'] . '.new#new" method="post" accept-charset="' . $context['character_set'] . '" name="postmodify" id="postmodify" onsubmit="submitonce(this);smc_saveEntities(\"postmodify\", [\"subject\", \"' . $context['post_box_name'] . '\", \"guestname\", \"evtitle\", \"question\"], \"options\");" enctype="multipart/form-data" style="margin: 0;">';

    $quickReply .='
  <div id="postContainer" class="inputContainer" style="padding-bottom: 0;">
    <div class="newPost">
      <textarea class="editor" name="message" id="message" rows="1" cols="60" tabindex="2" style="width: 100%; height: 16px; overflow: hidden; word-wrap: break-word; resize: horizontal;"></textarea>
    </div>
  </div>';

  // Guests have to put in their name and email...
  if (!$context['user']['is_logged'] && isset($context['name']) && isset($context['email']))
  {
    $quickReply .= '<div class="noLeftPadding inputContainer padTop">';
    $quickReply .= '<span class="inputLabel">'. $txt['username'] .'</span>';
    $quickReply .= '<input type="text" name="guestname" size="25" value="' . $context['name'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
    $quickReply .= '<span id="smf_autov_username_div" style="display: none;">
            <a id="smf_autov_username_link" href="#">
              <img id="smf_autov_username_img" src="' . $settings['images_url'] . '/icons/field_check.png" alt="*" />
            </a>
          </span>';
    $quickReply .= '</div>';

    if (empty($modSettings['guest_post_no_email']))
    {
      $quickReply .= '<div class="noLeftPadding inputContainer padTop">';
      $quickReply .= '<span class="inputLabel">'. $txt['email'] .'</span>';
      $quickReply .= '<input type="text" name="email" size="25" value="' . $context['email'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
      $quickReply .= '</div>';
    }
  }

  if($context['require_verification'])
    {
      $quickReply .= '<div class="noLeftPadding inputContainer padTop">';
      $quickReply .= '<span class="inputLabel">Code</span>';
      $quickReply .= template_control_verification($context['visual_verification_id'], 'all');
      $quickReply .= '</div>';
      $quickReply .= '<div class="noLeftPadding inputContainer padTop">';
      $quickReply .= '<span class="inputLabel">Verify</span>';
      $quickReply .= '<input type="text" tabindex="' . $context['tabindex']++ . '" name="post_vv[code]" />';
      $quickReply .= '</div>';
  }
  
  $quickReply .= '<div class="child buttons">
  
  <button class="button" type="submit">' . $txt['iPost'] . '</button>

  </div>';
  
  if (isset($context['num_replies']))
    $quickReply .= '<input type="hidden" name="num_replies" value="' . $context['num_replies'] . '" />';

  if(!empty($context['subject'] ))
  {
    $quickReply .= '<input type="hidden" name="subject" value="' . $context['subject'] . '" />';
  }
    
  $quickReply .= '
      <input type="hidden" name="additional_options" value="' . ($context['show_additional_options'] ? 1 : 0) . '" />
      <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
      <input type="hidden" name="seqnum" value="' . $context['form_sequence_number'] . '" />
      <input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
      <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />    
      <input type="hidden" name="goback" value="' . $options['return_to_post'] . '" />
    </form>';
    
  $quickReply .= '</div>';

  echo '<script>
    $(function() {
      $(".topbar").last().append(' , json_encode($quickReply) , ');
    });
  </script>';
}

?>