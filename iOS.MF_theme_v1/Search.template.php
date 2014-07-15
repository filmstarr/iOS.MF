<?php


/* Search templates */

function template_main() {

  echo '
    <script type="text/javascript">
      $(document).one("pagecontainershow", function() {
        $("#search-bar").show();
        $(".show-hide-search").last().get(0).className = "close-icon";
      });
    </script>';

  if (!empty($context['search_errors'])) {
    echo '<div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['search_errors']['messages']), '</div></div>';
  } else {
    echo '<div style="height:3px;"></div>';
  }

}

function template_results() {
  global $context, $settings, $options, $txt, $scripturl;

    echo '
      <script type="text/javascript">
        $(document).one("pagecontainershow", function() {
          $("#search-bar").show();
          $(".show-hide-search").last().get(0).className = "close-icon";
        });
      </script>';

  if (empty($context['topics'])) {
    echo '<div class="errors"><div style="margin-top: 6px;">*', $txt['search_no_results'] , '</div></div>';
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