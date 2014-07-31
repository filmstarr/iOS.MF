<?php

/*
* Search templates
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


require_once ($settings['theme_dir'] . '/ThemeControls.php');

//Perform a search
function template_main() {

  //Show the quick search in the header
  echo '
    <script type="text/javascript">
      $(document).one("pagecontainershow", function() {
        $("#search-bar").show();
        $(".show-hide-search").last().get(0).className = "close-icon";
      });
    </script>';

  //Show any errors
  if (!empty($context['search_errors'])) {
    echo '
      <div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['search_errors']['messages']), '</div></div>';
  } else {
    echo '
      <div style="height:3px;"></div>';
  }

}

//Show the results of a search
function template_results() {
  global $context, $settings, $options, $txt, $scripturl;

  //Show the quick search in the header
  echo '
    <script type="text/javascript">
      $(document).one("pagecontainershow", function() {
        $("#search-bar").show();
        $(".show-hide-search").last().get(0).className = "close-icon";
      });
    </script>';

  if (empty($context['topics'])) {
    //No results to show
    echo '
    <div class="errors"><div style="margin-top: 6px;">*', $txt['search_no_results'] , '</div></div>';
  } else {
    //Show a list of all the search results
    echo '
    <ul class="content-list first-content">';

    $i = 0;
    while ($topic = $context['get_topics']()) {
      foreach ($topic['matches'] as $message) {
        $i++;
        
        echo '
      <li onclick="this.className = \'clicked\'; $.mobile.changePage(\'' . $topic['first_post']['href'] . '\');">
        <div class="title', ($topic['new']) ? ' short-title' : '', '">', $topic['first_post']['subject'], '</div>';
        if ($topic['new'] && $context['user']['is_logged']) {
          echo '
        <div class="new">' . $txt['iNew'] . '</div>';
        }
        echo '
        <div class="description">', ($topic['is_locked']) ? $txt['locked_topic'] : $topic['last_post']['member']['name'] . ', ' . parse_time($topic['last_post']['timestamp']), '</div>
      </li>';
      }
    }
    echo '
    </ul>';
    
    //Paging buttons
    template_control_paging($context['page_index']);
  }
}

?>