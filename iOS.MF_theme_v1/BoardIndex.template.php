<?php

// Version: 2.0 RC4; BoardIndex

function template_main() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  $firstContent = true;
  foreach ($context['categories'] as $category) {
    echo '<ul class="content-list', $firstContent ? ' first-content">' : '">';
    $firstContent = false;
    $i = 0;
    foreach ($category['boards'] as $board) {
      $i++;
      echo '<li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $board['href'] . '\');">';
      echo '<div class="title', ($context['user']['is_logged'] && ($board['new'] || $board['children_new'])) ? ' short-title' : '', '">', $board['name'], '</div>';
      if ($context['user']['is_logged'] && ($board['new'] || $board['children_new'])) {
        echo '<div class="new">' . $txt['new_button'] . '</div>';
      }
      
      echo '<div class="description">', $board['last_post']['member']['name'], ', ', $board['last_post']['time'] == 'N/A' ? $txt['no'] . ' ' . $txt['topics'] : iPhoneTime($board['last_post']['timestamp']), '</div>
    </li>
    ';
    }
    
    echo '
  </ul>';
  }
  
  // "Users online" - in order of activity.
  
  echo '<div class="content-list">';
  
  // Assuming there ARE users online... each user in users_online has an id, username, name, group, href, and link.
  
  if (!empty($context['users_online'])) {
    echo '<span class="mieilink">', implode(', ', $context['list_users_online']);
    echo '</span>';
    
    // Showing membergroups?
    
    if (!empty($settings['show_group_key']) && !empty($context['membergroups'])) echo '
              <br />[' . implode(']&nbsp;&nbsp;[', $context['membergroups']) . ']';
  }
  
  echo '</div>';
}
?>