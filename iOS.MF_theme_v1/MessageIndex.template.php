<?php


/* View all the topics and child boards within a board */

function template_main() {
  global $context, $settings, $options, $scripturl, $txt, $modSettings;
  
  echo '<div class="child buttons" id="new-topic">
    <button class="button" onclick="$.mobile.changePage(\'', $scripturl, '?action=post;board=', $context['current_board'], '.0  \');">', $txt['new_topic'], '</button>
  </div>';
  
  //Display child boards
  if (!empty($context['boards']) && (!empty($options['show_children']) || $context['start'] == 0)) {
    echo '<ul class="content-list">';
    foreach ($context['boards'] as $board) {
      echo '<li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $board['href'] . '\');">';
      echo '<div class="sticky child-board"></div>';
      echo '<div class="title', ($context['user']['is_logged'] && ($board['new'] || $board['children_new'])) ? ' sticky-short-title' : '', '">', $board['name'], '</div>';
      if ($context['user']['is_logged'] && ($board['new'] || $board['children_new'])) {
        echo '<div class="new">' . $txt['new_button'] . '</div>';
      }
      
      echo '<div class="description">', $board['last_post']['member']['name'], ', ', $board['last_post']['time'] == 'N/A' ? $txt['no'] . ' ' . $txt['topics'] : iPhoneTime($board['last_post']['timestamp']), '</div>';
    }
    echo '</ul>';
  }
  
  $topic_sticky_count = 0;
  foreach ($context['topics'] as $topic) {
    if ($topic['is_sticky']) {
      $topic_sticky_count++;
    }
  }
  
  if ($topic_sticky_count) {
    echo '<ul class="content-list">';
    foreach ($context['topics'] as $topic) {
      if ($topic['is_sticky']) {
        echo '<li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $topic['first_post']['href'] . '\');">';
        echo '<div class="sticky"></div>
        <div class="title', ($topic['new']) ? ' sticky-short-title' : '', '">', $topic['first_post']['subject'], '</div>';
        if ($topic['new'] && $context['user']['is_logged']) {
          echo '<div class="new">' . $txt['new_button'] . '</div>';
        }
        echo '<div class="description">';
        echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name'] . ', ' . iPhoneTime($topic['last_post']['timestamp']), '</div>
        </li>';
      }
    }
    echo '</ul>';
  }
  
  if (count($context['topics']) - $topic_sticky_count) {
    echo '
  
  <ul class="content-list">';
    
    foreach ($context['topics'] as $topic) {
      if (!$topic['is_sticky']) {
        
        echo '<li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $topic['first_post']['href'] . '\')">';
        echo '<div class="title', ($topic['new']) ? ' short-title' : '', '">', $topic['first_post']['subject'], '</div>';
        if ($topic['new'] && $context['user']['is_logged']) {
          echo '<div class="new">' . $txt['new_button'] . '</div>';
        }
        echo '
        <div class="description">';
        echo '', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name'] . ', ' . iPhoneTime($topic['last_post']['timestamp']), '</div>
        </li>';
      }
    }
    
    echo '</ul>';
  }
  
  require_once ($settings[theme_dir] . '/ThemeControls.php');
  template_control_paging();
}
?>