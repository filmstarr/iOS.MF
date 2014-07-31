<?php

/*
* Functions for use within the iOS.MF theme.
* This contains some functions which access and modify the database where we want to do something slightly different to the default behaviour or need some extra data.
*
* License: http://www.opensource.org/licenses/mit-license.php
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

?>