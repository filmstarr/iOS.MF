<?php


/* Recent posts and unread posts */

//Recent posts
function template_main() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  echo '<script type="text/javascript">  
    $(function() {
      $(".message").each(function() {
        $(this).on("click", function() { $(this).parent().addClass("clicked"); });
      });
    });
  </script>';
  
  $showingAvatars = false;
  foreach ($context['posts'] as $message) {
    if (!empty($settings['show_user_images']) && empty($options['show_no_avatars'])) {
      if (array_key_exists('avatar', $message['poster'])) {
        $showingAvatars = true;
      }
    }
  }
  
  echo '
  <ul id="recent" class="content-list first-content">';
  
  foreach ($context['posts'] as $message) {
    
    echo '<li>
  
          <div class="post-details">', $message['counter'] . '. ' . $message['board']['link'] . ' / <a href="', $scripturl, '?topic=', $message['topic'], '">', $message['subject'], '</a>
          </div>
  
  <div>';
    
    if ($message['can_reply']) {
      echo '
        <div class="smalltext quickbuttons">

          <button class="button slim-button edit-delete" onclick="$.mobile.changePage(\'', $scripturl, '?action=post;topic=', $message['topic'], '.', $message['start'], ';quote=', $message['id'], '\');">', $txt['quote'], '</button>
          <button class="button slim-button edit-delete" onclick="$.mobile.changePage(\'', $scripturl, '?action=post;topic=', $message['topic'], '.', $message['start'], '\');">', $txt['reply'], '</button>

        </div>';
    }
    
    echo '</div>
  
      <div class="poster-info" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', isset($message['poster']['href']) ? $message['poster']['href'] : '', '\')"><span class="name">', $message['poster']['name'], '</span>';
    if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']) && $showingAvatars) {
      if (array_key_exists('avatar', $message['poster'])) {
        if (empty($message['poster']['avatar'])) {
          echo '<div class="avatar" style="background: url(' . $settings['theme_url'] . '/images/noavatar.png) #F5F5F5 center no-repeat;"></div>';
        } else {
          echo '<div class="avatar" style="background: url(' . str_replace(' ', '%20', $message['poster']['avatar_href']) . ') #fff center no-repeat;"></div>';
        }
      }
    }
    
    echo '
    </div>
        <div class="message" onclick="$.mobile.changePage(\'' . str_replace('#msg', ';new#msg', $message['href']) . '\');" ', ($showingAvatars ? '' : 'style="min-height: initial !important;"'), '>
        <span class="message-time" style="font-style: italic;font-size:11px;display:inline-block;', ($showingAvatars ? 'margin-bottom:3px;' : 'margin-bottom: 5px;'), '">', str_replace('strong', 'span', $message['time']), '</span><br />
    ', str_replace(rtrim($scripturl, '/index.php') . '/Smileys/default/', $settings['theme_url'] . '/images/SkypeEmoticons/', str_replace('<strong>Today</strong>', 'Today', short1($message['message'])));
    
    // Assuming there are attachments...
    if (!empty($message['attachment'])) {
      
      echo '<hr>
              <div id="msg_', $message['id'], '_footer" class="attachments smalltext">
                <div style="overflow: ', $context['browser']['is_firefox'] ? 'visible' : 'auto', ';">';
      
      $last_approved_state = 1;
      foreach ($message['attachment'] as $attachment) {
        
        // Show a special box for unapproved attachments...
        if ($attachment['is_approved'] != $last_approved_state) {
          $last_approved_state = 0;
          echo '
                  <fieldset>
                    <legend>', $txt['attach_awaiting_approve'];
          
          if ($context['can_approve']) echo '&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=all;mid=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve_all'], '</a>]';
          
          echo '</legend>';
        }
        
        if ($attachment['is_image']) {
          if ($attachment['thumbnail']['has_thumb']) echo '
                    <a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
          else echo '
                    <img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/><br />';
        }
        echo '
                    <img style="position:relative; top:-2px;" src="' . $settings['images_url'] . '/attachment.png" align="middle" alt="*" />&nbsp;<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a> ';
        
        if (!$attachment['is_approved'] && $context['can_approve']) echo '
                    [<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
        echo '
                    <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' - ' . $txt['attach_viewed'] : ' - ' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.)<br />';
      }
      
      // If we had unapproved attachments clean up.
      if ($last_approved_state == 0) echo '
                  </fieldset>';
      
      echo '
                </div>
              </div>';
    }
    echo '</div>';
    echo '</li>';
  }
  
  echo '</ul>';
  
  require_once ($settings[theme_dir] . '/ThemeControls.php');
  template_control_paging($context['page_index']);
}

//Unread posts
function template_unread() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  $topic_sticky_count = 0;
  foreach ($context['topics'] as $topic) {
    if ($topic['is_sticky']) {
      $topic_sticky_count++;
    }
  }
  
  $i = 0;
  if ($topic_sticky_count) {
    echo '<ul class="content-list first-content">';
    foreach ($context['topics'] as $topic) {
      if ($topic['is_sticky']) {
        $i++;
        echo '<li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . str_replace('#new', ';new#new', $topic['new_href']) . '\')">';
        echo '<div class="sticky"></div>';
        echo '<div class="title sticky-short-title">', $topic['first_post']['subject'], '</div>';
        echo '<div class="new">' . $txt['new_button'] . '</div>';
        
        echo '<div class="description">';
        echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name'] . ', ' . iPhoneTime($topic['last_post']['timestamp']), '</div>';
        echo '</li>';
      }
    }
    echo '</ul>';
  }
  
  $somma = $i;
  if (count($context['topics']) - $topic_sticky_count) {
    echo '
  
  <ul class="content-list', (!$topic_sticky_count ? ' first-content' : ''), '">';
    
    $i = 0;
    
    foreach ($context['topics'] as $topic) {
      if (!$topic['is_sticky']) {
        
        $i++;
        
        echo '
  
        <li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . str_replace('#new', ';new#new', $topic['new_href']) . '\')">';
        echo '<div class="title short-title">', $topic['first_post']['subject'], '</div>';
        echo '<div class="new">' . $txt['new_button'] . '</div>';
        echo '<div class="description">';
        echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name'] . ', ' . iPhoneTime($topic['last_post']['timestamp']), '</div>';
        echo '</li>';
      }
    }
    $somma = count($somma + $i);
    echo '</ul>';
  }
  if ($somma == 0) echo '
        <div id="unread-link">
          ', $txt['msg_alert_none'], '
        </div>';
  
  require_once ($settings[theme_dir] . '/ThemeControls.php');
  template_control_paging();
  
  echo '<div class="buttons">
    <a class="button market-all-read" href="', $scripturl . '?action=markasread;sa=all;' . $context['session_var'] . '=' . $context['session_id'], '">', $txt['iMarkALLRead'], '</a>
  </div>';
}
?>