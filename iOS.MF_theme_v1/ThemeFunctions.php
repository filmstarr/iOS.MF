<?php

/*
* Functions for use within the theme.
* This contains some functions which access and modify the database where we want to do something slightly different to the default behaviour or need some extra data.
*/


function unread_post_count() {
  global $context, $smcFunc, $modSettings;
  
  if (!$context['user']['is_logged']) {
    return 0;
  }
  
  // Don't bother to show deleted posts!
  $request = $smcFunc['db_query']('', '
    SELECT b.id_board
    FROM {db_prefix}boards AS b
    ' . (!empty($modSettings['recycle_enable']) && $modSettings['recycle_board'] > 0 ? '
      WHERE b.id_board != {int:recycle_board}' : ''), array('recycle_board' => (int)$modSettings['recycle_board'],));
  $boards = array();
  while ($row = $smcFunc['db_fetch_assoc']($request)) $boards[] = $row['id_board'];
  $smcFunc['db_free_result']($request);
  
  if (empty($boards)) return 0;
  
  $query_this_board = 'id_board IN ({array_int:boards})';
  
  $request = $smcFunc['db_query']('', '
    SELECT MIN(lmr.id_msg)
    FROM {db_prefix}boards AS b
      LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = b.id_board AND lmr.id_member = {int:current_member})
    WHERE {query_see_board}', array('current_member' => $context['user']['id'],));
  list($earliest_msg) = $smcFunc['db_fetch_row']($request);
  $smcFunc['db_free_result']($request);
  
  // This is needed in case of topics marked unread.
  if (empty($earliest_msg)) $earliest_msg = 0;
  else {
    
    // This query is pretty slow, but it's needed to ensure nothing crucial is ignored.
    $request = $smcFunc['db_query']('', '
      SELECT MIN(id_msg)
      FROM {db_prefix}log_topics
      WHERE id_member = {int:current_member}', array('current_member' => $context['user']['id'],));
    list($earliest_msg2) = $smcFunc['db_fetch_row']($request);
    $smcFunc['db_free_result']($request);
    
    // In theory this could be zero, if the first ever post is unread, so fudge it ;)
    if ($earliest_msg2 == 0) $earliest_msg2 = - 1;
    
    $earliest_msg = min($earliest_msg2, $earliest_msg);
  }
  
  $result = $smcFunc['db_query']('', '
    SELECT COUNT(*) unread_count
    FROM {db_prefix}topics AS t
      LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
      LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = t.id_board AND lmr.id_member = {int:current_member})
    WHERE t.' . $query_this_board . (!empty($earliest_msg) ? ' AND t.id_last_msg > {int:earliest_msg}' : '') . '
      AND IFNULL(lt.id_msg, IFNULL(lmr.id_msg, 0)) < t.id_last_msg', array('current_member' => $context['user']['id'], 'earliest_msg' => !empty($earliest_msg) ? $earliest_msg : 0, 'boards' => $boards,));
  
  $result = $smcFunc['db_fetch_assoc']($result);
  return $result['unread_count'];
}

function user_list() {
  global $context, $smcFunc;
  
  if (!$context['user']['is_logged']) {
    return array();
  }
  
  $request = $smcFunc['db_query']('', '
    SELECT member_name
    FROM {db_prefix}members', array());
  $users = array();
  while ($row = $smcFunc['db_fetch_assoc']($request)) $users[] = $row['member_name'];
  $smcFunc['db_free_result']($request);
  
  return $users;
}

// Mark personal messages unread.
function mark_messages_unread($personal_messages = null, $label = null, $owner = null) {
  global $user_info, $context, $smcFunc;
  
  if ($owner === null) $owner = $user_info['id'];
  
  $smcFunc['db_query']('', '
    UPDATE {db_prefix}pm_recipients
    SET is_read = 0
    WHERE id_member = {int:id_member}
      AND (is_read & 1 >= 1)' . ($label === null ? '' : '
      AND FIND_IN_SET({string:label}, labels) != 0') . ($personal_messages !== null ? '
      AND id_pm IN ({array_int:personal_messages})' : ''), array('personal_messages' => $personal_messages, 'id_member' => $owner, 'label' => $label,));
  
  // If something wasn't marked as read, get the number of unread messages remaining.
  if ($smcFunc['db_affected_rows']() > 0) {
    if ($owner == $user_info['id']) {
      foreach ($context['labels'] as $label) $context['labels'][(int)$label['id']]['unread_messages'] = 0;
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
      
      if ($owner != $user_info['id']) continue;
      
      $this_labels = explode(',', $row['labels']);
      foreach ($this_labels as $this_label) $context['labels'][(int)$this_label]['unread_messages']+= $row['num'];
    }
    $smcFunc['db_free_result']($result);
    
    // Need to store all this.
    cache_put_data('labelCounts:' . $owner, $context['labels'], 720);
    updateMemberData($owner, array('unread_messages' => $total_unread));
    
    // If it was for the current member, reflect this in the $user_info array too.
    if ($owner == $user_info['id']) $context['user']['unread_messages'] = $user_info['unread_messages'] = $total_unread;
  }
}

function iPhoneTitle() {
  global $context;
  
  $title = str_replace($context['forum_name_html_safe'] . ' - ', '', $context['page_title_html_safe']);
  
  if ($title == 'Index') $title = $context['forum_name_html_safe'];
  
  $title = str_replace('View the profile of ', '', $title);
  
  $title = str_replace('Set Search Parameters', 'Search', $title);
  
  $title = str_replace('Personal Messages Index', 'Personal Messages', $title);
  
  $title = str_replace('Send message', 'Compose Message', $title);
  
  return $title;
}

function script_navigate_to_message() {
  echo '
  <script type="text/javascript">

    $(document).one("silentscroll", function() {
      if (!navigateToElement(/(msg\d+)/))
      {
        navigateToElement(/(new)/);
      }
    });

    $(document).one("pagecontainertransition", function() {
      if (!navigateToElementOnce(/(msg\d+)/))
      {
        navigateToElementOnce(/(new)/);
      }
    });

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

function get_current_url() {
  $url = @($_SERVER["HTTPS"] != 'on') ? 'http://' . $_SERVER["SERVER_NAME"] : 'https://' . $_SERVER["SERVER_NAME"];
  $url.= ($_SERVER["SERVER_PORT"] !== "80") ? ":" . $_SERVER["SERVER_PORT"] : "";
  $url.= $_SERVER["REQUEST_URI"];
  return $url;
}

function iPhoneTime($time) {
  global $txt;
  
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

function short1($ret) {
  $ret = ' ' . $ret;
  $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='$2'>$2</a>", $ret);
  $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "$1<a href='http://$2'>$2</a>", $ret);
  short2($ret);
  $ret = preg_replace("#(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)#i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $ret);
  $ret = substr($ret, 1);
  return ($ret);
}

function short2(&$ret) {
  
  $links = explode('<a', $ret);
  $countlinks = count($links);
  for ($i = 0; $i < $countlinks; $i++) {
    $link = $links[$i];
    
    $link = (preg_match('#(.*)(href=")#is', $link)) ? '<a' . $link : $link;
    
    $begin = strpos($link, '>') + 1;
    $end = strpos($link, '<', $begin);
    $length = $end - $begin;
    $urlname = substr($link, $begin, $length);
    
    $chunked = (strlen(str_replace('http://', '', $urlname)) > 28 && preg_match('#^(http://|ftp://|www\.)#is', $urlname)) ? substr_replace(str_replace('http://', '', $urlname), '.....', 12, -12) : $urlname;
    $ret = str_replace('>' . $urlname . '<', '>' . $chunked . '<', $ret);
  }
}

?>