<?php

// Version: 2.0 RC4; Search

function template_main() {
}

function template_results() {
  global $context, $settings, $options, $txt, $scripturl;
  
  if (empty($context['topics'])) {
    echo '<h3 id="noSearchResults">', $txt['search_no_results'], '</h3><style type="text/css">#searchbar{

  display: block;

}</style>';
  } else {
    $i = 0;
    
    echo '
       <ul class="content-list first-content">';
    
    while ($topic = $context['get_topics']()) {
      
      foreach ($topic['matches'] as $message) {
        $i++;
        
        echo '<li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $topic['first_post']['href'] . '\');">';
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
    
    require_once ($settings[theme_dir] . '/ThemeControls.php');
    template_control_paging($context['page_index']);
  }
}
?>