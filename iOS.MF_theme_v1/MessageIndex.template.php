<?php
// Version: 2.0 RC4; MessageIndex

function template_main()
{
  global $context, $settings, $options, $scripturl, $txt, $modSettings;

  if($context['user']['is_guest'])
  echo '  

  
  <div class="buttons">
  
  </div>';
  else
  echo '  

  
    <div class="child buttons" id="newtopic">
  
  <button class="button" onclick="window.location.href=\'', $scripturl , '?action=post;board=' , $context['current_board'] , '.0  \';">', $txt['new_topic'], '</button>
  
  </div>';
  
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
  
  <ul class="content2">';

        echo'<li onclick="this.className = \'clicked\'; window.location.href=\''. $topic['first_post']['href'] .'\';">';
    echo '<div class="sticky"></div>
    <div class="title', ($topic['new']) ? ' stickyShortTitle' : '' ,'">', $topic['first_post']['subject'] ,'</div>';
    if ($topic['new']&&$context['user']['is_logged']) {
      echo '<div class="new">'. $txt['new_button'] .'</div>';
    }
    echo'<div class="description">';
    echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name'] . ', '. iPhoneTime($topic['last_post']['timestamp']) , '</div>
    </li>';
    
    
    }

    }
    if ($i==$topic_sticky_count)
    echo
    '
  
  </ul>
  
  ';    
  
  if(count($context['topics'])-$topic_sticky_count){
  echo'
  
  <ul class="content2">';
  
  $i = 0;
  
    foreach ($context['topics'] as $topic)
    {if(!$topic['is_sticky']){
    
    $i++;
    
    
      echo'
  
    <li onclick="this.className = \'clicked\'; window.location.href=\''. $topic['first_post']['href'] .'\'">';
    echo '<div class="title', ($topic['new']) ? ' shortTitle' : '' ,'">', $topic['first_post']['subject'] ,'</div>';
    if ($topic['new']&&$context['user']['is_logged']) {
      echo '<div class="new">'. $txt['new_button'] .'</div>';
    }
    echo '
    <div class="description">';
      echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name']. ', '. iPhoneTime($topic['last_post']['timestamp']) , '</div>
    </li>';
    
    
    }}
    
  echo
    '
  
  </ul>


  ';  
  }
  
  echo'  
  
  <div class="page buttons">
  
  <button  class="button" onclick="window.location.href=\'', $context['links']['prev'] ,'\';" ', $context['page_info']['current_page']==1 ? 'disabled="disabled"' : '', '>', $txt['iPrev'], '</button>
  
  <button id="pagecount">', $txt['iPage'], ' ', $context['page_info']['current_page'] ,' ', $txt['iOf'] ,' ', ($context['page_info']['num_pages']==0) ? '1' : $context['page_info']['num_pages'] ,'</button>
  
  
  <button  class="button" onclick="window.location.href=\'', $context['links']['next'] ,'\';" ', ($context['page_info']['current_page']==$context['page_info']['num_pages']||$context['page_info']['num_pages']==0) ? 'disabled="disabled"' : '', '>', $txt['iNext'], '</button>
  
  
  </div>
';
}

?>