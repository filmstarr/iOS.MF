<?php

/*
* Functions for use within the theme.
* This contains some functions which access and modify the database where we want to do something slightly different to the default behaviour or need some extra data.
*/


//Get a count of the total number of unread posts for the current user
function unread_topic_count() {
  global $context, $smcFunc, $modSettings;
  
  if (!$context['user']['is_logged']) {
    return 0;
  }
  
  //Get a list of boards, excluding the recycle bin
  $request = $smcFunc['db_query']('', '
    SELECT b.id_board
    FROM {db_prefix}boards AS b
    ' . (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? '
    WHERE b.id_board != {int:recycle_board}' : ''), array('recycle_board' => (int)$modSettings['recycle_board'],));

  $boards = array();
  while ($row = $smcFunc['db_fetch_assoc']($request)) {
    $boards[] = $row['id_board'];
  }
  $smcFunc['db_free_result']($request);

  //No boards to look in  
  if (empty($boards)) {
    return 0;
  }
  
  //Find the earliest message that we need to count from
  $request = $smcFunc['db_query']('', '
    SELECT MIN(lmr.id_msg)
    FROM {db_prefix}boards AS b
      LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = b.id_board AND lmr.id_member = {int:current_member})
    WHERE {query_see_board}', array('current_member' => $context['user']['id'],));
  list($earliest_msg) = $smcFunc['db_fetch_row']($request);
  $smcFunc['db_free_result']($request);
  
  //This is needed in case of topics marked unread.
  if (empty($earliest_msg)) {
    $earliest_msg = 0;
  } else {
    //This query is pretty slow, but it's needed to ensure nothing crucial is ignored.
    $request = $smcFunc['db_query']('', '
      SELECT MIN(id_msg)
      FROM {db_prefix}log_topics
      WHERE id_member = {int:current_member}', array('current_member' => $context['user']['id'],));
    list($earliest_msg2) = $smcFunc['db_fetch_row']($request);
    $smcFunc['db_free_result']($request);
    
    //In theory this could be zero, if the first ever post is unread, so fudge it ;)
    if ($earliest_msg2 == 0) {
      $earliest_msg2 = - 1;
    }
    
    $earliest_msg = min($earliest_msg2, $earliest_msg);
  }
  
  //Count the unread messages
  $result = $smcFunc['db_query']('', '
    SELECT COUNT(*) unread_count
    FROM {db_prefix}topics AS t
      LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
      LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = t.id_board AND lmr.id_member = {int:current_member})
    WHERE t.id_board IN ({array_int:boards})' . (!empty($earliest_msg) ? ' AND t.id_last_msg > {int:earliest_msg}' : '') . '
      AND IFNULL(lt.id_msg, IFNULL(lmr.id_msg, 0)) < t.id_last_msg', array('current_member' => $context['user']['id'], 'earliest_msg' => !empty($earliest_msg) ? $earliest_msg : 0, 'boards' => $boards,));
  
  $result = $smcFunc['db_fetch_assoc']($result);
  return $result['unread_count'];
}

//Return a list of forum users
function user_list() {
  global $context, $smcFunc;
  
  if (!$context['user']['is_logged']) {
    return array();
  }
  
  $request = $smcFunc['db_query']('', '
    SELECT member_name
    FROM {db_prefix}members', array());
  $users = array();
  while ($row = $smcFunc['db_fetch_assoc']($request)) {
    $users[] = $row['member_name'];
  }
  $smcFunc['db_free_result']($request);
  
  return $users;
}

//Mark the specified personal messages as unread
function mark_messages_unread($personal_messages = null, $label = null, $owner = null) {
  global $user_info, $context, $smcFunc;
  
  if ($owner === null) {
    $owner = $user_info['id'];
  }
  
  $smcFunc['db_query']('', '
    UPDATE {db_prefix}pm_recipients
    SET is_read = 0
    WHERE id_member = {int:id_member}
      AND (is_read & 1 >= 1)' . ($label === null ? '' : '
      AND FIND_IN_SET({string:label}, labels) != 0') . ($personal_messages !== null ? '
      AND id_pm IN ({array_int:personal_messages})' : ''), array('personal_messages' => $personal_messages, 'id_member' => $owner, 'label' => $label,));
  
  //If something wasn't marked as read, get the number of unread messages remaining.
  if ($smcFunc['db_affected_rows']() > 0) {
    if ($owner == $user_info['id']) {
      foreach ($context['labels'] as $label) {
        $context['labels'][(int)$label['id']]['unread_messages'] = 0;
      }
    }
    
    $result = $smcFunc['db_query']('', '
      SELECT labels, COUNT(*) AS num
      FROM {db_prefix}pm_recipients
      WHERE id_member = {int:id_member}
        AND NOT (is_read & 1 >= 1)
        AND deleted = {int:is_not_deleted}
      GROUP BY labels', array('id_member' => $owner, 'is_not_deleted' => 0,));
    $total_unread = 0;

    while ($row = $smcFunc['db_fetch_assoc']($result)) {
      $total_unread+= $row['num'];
      
      if ($owner != $user_info['id']) {
        continue;
      }
      
      $this_labels = explode(',', $row['labels']);
      foreach ($this_labels as $this_label) {
        $context['labels'][(int)$this_label]['unread_messages'] += $row['num'];
      }
    }
    $smcFunc['db_free_result']($result);
    
    //Need to store all this
    cache_put_data('labelCounts:' . $owner, $context['labels'], 720);
    updateMemberData($owner, array('unread_messages' => $total_unread));
    
    //If it was for the current member, reflect this in the $user_info array too
    if ($owner == $user_info['id']) $context['user']['unread_messages'] = $user_info['unread_messages'] = $total_unread;
  }
}

//Make some adjustments to the forum title
function parse_title() {
  global $context;
  
  $title = str_replace($context['forum_name_html_safe'] . ' - ', '', $context['page_title_html_safe']);
  
  if ($title == 'Index') {
    $title = $context['forum_name_html_safe'];
  }
  
  $title = str_replace('View the profile of ', '', $title);
  $title = str_replace('Set Search Parameters', 'Search', $title);
  $title = str_replace('Personal Messages Index', 'Personal Messages', $title);
  $title = str_replace('Send message', 'Compose Message', $title);
  
  return $title;
}

//Script to navigate to a msg or new element if it is specified in the URL
function script_navigate_to_message() {
  echo '
  <script type="text/javascript">

    //silentscroll is only called when the page is loaded, so we will always want to navigate the element in question
    $(document).one("silentscroll", function() {
      if (!navigateToElement(/(msg\d+)/))
      {
        navigateToElement(/(new)/);
      }
    });

    //pagecontainertransition is called when moving forward and back through the history, so we only want to do this navigation once
    $(document).one("pagecontainertransition", function() {
      if (!navigateToElementOnce(/(msg\d+)/))
      {
        navigateToElementOnce(/(new)/);
      }
    });

    //Navigate to an element if we can find it
    var navigateToElement = function(regex) {
      var elementMatch = location.search.substring(1).match(regex);
      if (elementMatch)
      {
        var elementId = elementMatch[0];
        if (elementId && $("#"+ elementId).length)
        {
          $("#"+ elementId)[0].scrollIntoView(true);
          return true;
        }
      }
      return false;
    };

    //Navigate to an element if we can find it, but only once (not when moving forward and back through the history)
    var navigateToElementOnce = function(regex) {
      var elementMatch = location.search.substring(1).match(regex);
      if (elementMatch)
      {
        var elementId = elementMatch[0];
        var state = window.history.state;
        if (elementId && $("#"+ elementId).length && (!state.hasOwnProperty("preventNavigationToPost")))
        {
          $("#"+ elementId)[0].scrollIntoView(true);
          state.preventNavigationToPost = true;
          history.replaceState(state, "", document.URL);
          return true;
        }
      }
      return false;
    };

  </script>';
}

//Hide the toolbar when the keyboard is shown on an iOS or Android device
function script_hide_toolbar() {
  global $context;
  
  echo '<script type="text/javascript">
      $(function(){
        $(".editor").last().autosize().resize();
        $(".classic").last().hide();
        
        //Deal with the race condition between iOS keyboard showing and the focus event firing
        if(/iPhone|iPod|Android|iPad/.test(window.navigator.platform)){
          var jqElement = $(".editor").last();
          jqElement.attr("disabled", true);

          jqElement.on("tap", function(event) {
            if (event.target.id == "', $context['post_box_name'], '") {
              if (!$(event.target).is(":focus")) {

                // Hide toolbar
                $(".toolbar").css("display", "none");
                $("#copyright").css("margin-bottom", "4px");

                //Enable and focus textbox
                $(event.target).removeAttr("disabled");
                $(event.target).focus();

                //Move caret to end
                jqElement.get(0).setSelectionRange(jqElement.val().length, jqElement.val().length);
              }
            }
          });

          jqElement.on("blur", function(e) {
            jqElement.attr("disabled", true);
          });
        }
      });
    </script>';  
}

//Get the current URL, this is surprisingly complex in PHP!
function get_current_url() {
  $url = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
  $url.= ($_SERVER["SERVER_PORT"] !== "80") ? ":" . $_SERVER["SERVER_PORT"] : "";
  $url.= $_SERVER["REQUEST_URI"];
  return $url;
}

//Take a time and turn it into the time elaspsed since
function parse_time($time) {
  global $txt;
  
  //The time since the input time in seconds
  $diff = forum_time() - $time;
  
  if ($diff < 60) return $diff . ' ' . $txt['iSecondsAgo'];
  elseif (round($diff / 60) == 1) return '1 ' . $txt['iMinuteAgo'];
  elseif ($diff > 59 && $diff < 3600) return round($diff / 60) . ' ' . $txt['iMinutesAgo'];
  elseif (round($diff / 60 / 60) == 1) return '1 ' . $txt['iHourAgo'];
  elseif (round($diff / 60 / 60) > 1 && round($diff / 60 / 60) < 24) return round($diff / 60 / 60) . ' ' . $txt['iHoursAgo'];
  elseif (round($diff / 60 / 60 / 24) == 1) return '1 ' . $txt['iDayAgo'];
  elseif (round($diff / 60 / 60 / 24) > 1 && round($diff / 60 / 60 / 24) < 7) return round($diff / 60 / 60 / 24) . ' ' . $txt['iDaysAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7) == 1) return '1 ' . $txt['iWeekAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7) > 1) return round($diff / 60 / 60 / 24 / 7) . ' ' . $txt['iWeeksAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7 / 4) == 1) return '1 ' . $txt['iMonthAgo'];
  elseif (round($diff / 60 / 60 / 24 / 7 / 4) > 1) return round($diff / 60 / 60 / 24 / 7) . ' ' . $txt['iMonthsAgo'];
  else return $diff;
}

//Parse a message
function parse_message($message) {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  //Shorten any links in the message
  $message = ' ' . $message;
  $message = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='$2'>$2</a>", $message);
  $message = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='http://$2'>$2</a>", $message);
  $links = explode('<a', $message);
  $countlinks = count($links);
  for ($i = 0; $i < $countlinks; $i++) {
    $link = $links[$i];
    
    $link = (preg_match('#(.*)(href=")#is', $link)) ? '<a' . $link : $link;
    
    $begin = strpos($link, '>') + 1;
    $end = strpos($link, '<', $begin);
    $length = $end - $begin;
    $urlname = substr($link, $begin, $length);
    
    $chunked = (strlen(str_replace('http://', '', $urlname)) > 28 && preg_match('#^(http://|ftp://|www\.)#is', $urlname)) ? substr_replace(str_replace('http://', '', $urlname), '.....', 12, -12) : $urlname;
    $message = str_replace('>' . $urlname . '<', '>' . $chunked . '<', $message);
  }
  $message = preg_replace("#(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $message);
  $message = substr($message, 1);

  //Replace default smilies with retina smilies
  $message = str_replace(rtrim($scripturl, '/index.php') . '/Smileys/default/', $settings['theme_url'] . '/images/SkypeEmoticons/', $message);

  //Unbold "Today" text in quotes
  $message = str_replace('<strong>Today</strong>', 'Today', $message);
  return ($message);
}

?>