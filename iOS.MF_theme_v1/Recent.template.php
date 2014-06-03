<?php
// Version: 2.0 RC4; Recent

function template_main()
{
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  echo '
  <ul id="recent" class="content2 firstContent">';

  foreach ($context['posts'] as $message)
  {
      
  echo '<li onclick="this.className = \'clicked\';">
  
          <div class="postDetails">', $message['counter'] . '. ' . $message['board']['link'] . ' / <a href="' , $scripturl , '?topic=',$message['topic'],'">' , $message['subject'] , '</a>
          </div>
  
  <div>';
      
    if ($message['can_reply'])
    {
      echo '
        <div class="quickbuttons_wrap">
          <ul class="reset smalltext quickbuttons">

            <a href="', $scripturl, '?action=post;topic=', $message['topic'], '.', $message['start'], ';quote=', $message['id'], '"><button class="button slimbutton" id="editdel">', $txt['quote'], '</button></a>
            <a href="', $scripturl, '?action=post;topic=', $message['topic'], '.', $message['start'], '"><button class="button slimbutton" id="editdel">', $txt['reply'], '</button></a>

          </ul>
        </div>';
    }
      
      
// Can the user modify the contents of this post?
      if ($message['can_modify'])
         echo '
                  <a href="', $scripturl, '?action=post;msg=', $message['id'], ';topic=', $context['current_topic'], '.', $context['start'], ';', $context['session_var'], '=', $context['session_id'], '"><button class="button slimbutton" id="editdel"> '. $txt['modify'].' </button></a>';         
   // How about... even... remove it entirely?!
      if ($message['can_remove'])
         echo '
               <a href="', $scripturl, '?action=deletemsg;topic=', $context['current_topic'], '.', $context['start'], ';msg=', $message['id'], ';', $context['session_var'], '=', $context['session_id'], '" onclick="return confirm(\'', $txt['remove_message'], '?\');"><button class="button slimbutton" id="editdel"> ', $txt['remove'],' </button></a>';
echo '</div>
  
      <div class="posterinfo" onclick="window.location.href=\'', isset($message['poster']['href']) ? $message['poster']['href'] : '' ,'\'"><span class="name">', $message['poster']['name'] ,'</span>';
      if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']))
        if (empty($message['poster']['avatar'])) {
          echo '<div id="avatar" style="background: url('.$settings['theme_url'].'/images/noavatar.png) #fff center no-repeat;"></div>';
        }
        else {
						echo '<div id="avatar" style="background: url('.str_replace(' ','%20', $message['poster']['avatar_href']).') #fff center no-repeat;"></div>';
        }
      echo '
    
    </div>
        <div class="message" onclick="window.location.href=\''. $message['href'] . '\';">
        <span class="message_time" style="font-style: italic;font-size:11px;display:inline-block;margin-bottom:3px;">', str_replace('strong','span',$message['time']) ,'</span><br />
    ', str_replace(rtrim($scripturl,'/index.php') . '/Smileys/default/', $settings['theme_url'] . '/images/SkypeEmoticons/',str_replace('<strong>Today</strong>','Today',short1($message['message'])));

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
                    <a href="', $attachment['href'], ';image" id="link_', $attachment['id'], '" onclick="', $attachment['thumbnail']['javascript'], '"><img src="', $attachment['thumbnail']['href'], '" alt="" id="thumb_', $attachment['id'], '" /></a><br />';
          else
            echo '
                    <img src="' . $attachment['href'] . ';image" alt="" width="' . $attachment['width'] . '" height="' . $attachment['height'] . '"/><br />';
        }
        echo '
                    <img style="position:relative; top:-2px;" src="' . $settings['images_url'] . '/attachment.png" align="middle" alt="*" />&nbsp;<a href="' . $attachment['href'] . '">' . $attachment['name'] . '</a> ';

        if (!$attachment['is_approved'] && $context['can_approve'])
          echo '
                    [<a href="', $scripturl, '?action=attachapprove;sa=approve;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['approve'], '</a>]&nbsp;|&nbsp;[<a href="', $scripturl, '?action=attachapprove;sa=reject;aid=', $attachment['id'], ';', $context['session_var'], '=', $context['session_id'], '">', $txt['delete'], '</a>] ';
        echo '
                    <br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(', $attachment['size'], ($attachment['is_image'] ? ', ' . $attachment['real_width'] . 'x' . $attachment['real_height'] . ' - ' . $txt['attach_viewed'] : ' - ' . $txt['attach_downloaded']) . ' ' . $attachment['downloads'] . ' ' . $txt['attach_times'] . '.)<br />';
      }

      // If we had unapproved attachments clean up.
      if ($last_approved_state == 0)
        echo '
                  </fieldset>';

      echo '
                </div>
              </div>';
    }
    echo '</div>';
    echo '</li>';      
  }
    
  echo '</ul>';
		
  echo '<div class="page buttons">
  
  <button class="button" onclick="window.location.href=\'', $context['links']['prev'] ,'\';" ', $context['page_info']['current_page']==1 ? 'disabled="disabled"' : '', '>', $txt['iPrev'], '</button>
  
  <button id="pagecount">', $txt['iPage'], ' ', $context['page_info']['current_page'] ,' ', $txt['iOf'] ,' ', ($context['page_info']['num_pages']==0) ? '1' : $context['page_info']['num_pages'] ,'</button>
  
  <button class="button" onclick="window.location.href=\'', $context['links']['next'] ,'\';" ', ($context['page_info']['current_page']==$context['page_info']['num_pages']||$context['page_info']['num_pages']==0) ? 'disabled="disabled"' : '', '>', $txt['iNext'], '</button>
  
  </div>';
		
}

function template_unread()
{
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
    
  $topic_sticky_count = 0;
  foreach ($context['topics'] as $topic)
    {if($topic['is_sticky']){
    $topic_sticky_count++;
    }}
  
  $i = 0;
    if($topic_sticky_count)
    foreach ($context['topics'] as $topic)
    {if($topic['is_sticky']){
    
    $i++;
    
    if ($i==1)echo'
  
      <ul class="content2 firstContent">';

        echo'
  
    <li onclick="this.className = \'clicked\'; window.location.href=\''. $topic['new_href'] .'\'">';
    echo '<div class="sticky"></div>';
    echo '<div class="title stickyShortTitle">', $topic['first_post']['subject'] ,'</div>';
    echo '<div class="new">'. $txt['new_button'] .'</div>';
    
    echo '<div class="description">';
    echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name']. ', '. iPhoneTime($topic['last_post']['timestamp']) , '</div>';
    echo '</li>';
    
    
    }

    }
    if ($i==$topic_sticky_count)
    echo
    '
  
  </ul>
  
  ';    
  $somma = $i;  
  if(count($context['topics'])-$topic_sticky_count){
  echo'
  
  <ul class="content2' , (!$topic_sticky_count ? ' firstContent' : '') , '">';
  
  $i = 0;
  
    foreach ($context['topics'] as $topic)
    {if(!$topic['is_sticky']){
    
    $i++;
    
    
      echo'
  
        <li onclick="this.className = \'clicked\'; window.location.href=\''. $topic['new_href'] .'\'">';
    echo '<div class="title shortTitle">', $topic['first_post']['subject'] ,'</div>';
    echo '<div class="new">'. $txt['new_button'] .'</div>';
    echo '<div class="description">';
    echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name']. ', '. iPhoneTime($topic['last_post']['timestamp']) , '</div>';
    echo '</li>';
    
    
    }}
    $somma = count($somma + $i);
  echo
    '
  
  </ul>


  ';  
  }
  if ($somma==0)
    echo '
        <div id="unreadlink">
          ', $context['showing_all_topics'] ? $txt['msg_alert_none'] : $txt['unread_topics_visit_none'], '
        </div>';

    echo'
      <div class="page buttons">
        <button class="button" onclick="window.location.href=\'', $context['links']['prev'] ,'\';" ', $context['page_info']['current_page']==1 ? 'disabled="disabled"' : '', '>', $txt['iPrev'], '</button>
        <button id="pagecount">', $txt['iPage'], ' ', $context['page_info']['current_page'] ,' ', $txt['iOf'] ,' ', ($context['page_info']['num_pages']==0) ? '1' : $context['page_info']['num_pages'] ,'</button>
        <button class="button" onclick="window.location.href=\'', $context['links']['next'] ,'\';" ', ($context['page_info']['current_page']==$context['page_info']['num_pages']||$context['page_info']['num_pages']==0) ? 'disabled="disabled"' : '', '>', $txt['iNext'], '</button>
      </div>';
  
  echo '<div class="buttons">
    <a class="button recentPosts" href="', $scripturl, '?action=recent">Recent Posts</a>
  </div>';
}

?>