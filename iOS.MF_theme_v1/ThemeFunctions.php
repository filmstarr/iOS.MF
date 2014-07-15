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

function navigate_to_message_script() {
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

function add_quick_reply_to_title() {
  global $context, $settings, $options, $txt, $scripturl, $modSettings;
  
  $quickReply = '<script type="text/javascript">

    $(function(){
      $(".editor").last().autosize();
    });

    var toggleQuickReply = function() {
      if ($("#quick-reply").is(":visible"))
      {
        $("#message").blur();
        $("#quick-reply").hide();      
      }
      else
      {
        $("#quick-reply").show();
        $("#message").focus();
      }
      setTopMargin();
    };

    var title = $(".the-title").last().get(0);
    title.onclick = function() { $(this).fadeTo(200 , 0.3).fadeTo(200 , 1.0); toggleQuickReply();};
    title.style.color = "#007AFF";
    $(".the-title").addClass("quick-reply-title");

    var submitForm = function() {
      submitonce(this);
      smc_saveEntities("postmodify", ["subject", "' . $context['post_box_name'] . '", "guestname", "evtitle", "question"], "options");
    };

    </script>';
  
  $quickReply.= '<div id="quick-reply">';
  $quickReply.= '<form action="' . $scripturl . '?action=post2;' . (empty($context['current_board']) ? '' : 'board=') . $context['current_board'] . '.new#new" method="post" accept-charset="' . $context['character_set'] . '" name="postmodify" id="postmodify" onsubmit="submitForm();" enctype="multipart/form-data" style="margin: 0;">';
  
  $quickReply.= '
  <div id="post-container" class="input-container" style="padding-bottom: 0;">
    <div class="new-post">
      <textarea class="editor" name="message" id="message" rows="1" cols="60" tabindex="2" style="width: 100%; height: 16px; overflow: hidden; word-wrap: break-word; resize: horizontal;"></textarea>
    </div>
  </div>';
  
  // Guests have to put in their name and email...
  if (!$context['user']['is_logged'] && isset($context['name']) && isset($context['email'])) {
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">' . $txt['username'] . '</span>';
    $quickReply.= '<input type="text" name="guestname" size="25" value="' . $context['name'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
    $quickReply.= '<span id="smf_autov_username_div" style="display: none;">
            <a id="smf_autov_username_link" href="#">
              <img id="smf_autov_username_img" src="' . $settings['images_url'] . '/icons/field_check.png" alt="*" />
            </a>
          </span>';
    $quickReply.= '</div>';
    
    if (empty($modSettings['guest_post_no_email'])) {
      $quickReply.= '<div class="no-left-padding input-container pad-top">';
      $quickReply.= '<span class="input-label">' . $txt['email'] . '</span>';
      $quickReply.= '<input type="text" name="email" size="25" value="' . $context['email'] . '" tabindex="' . $context['tabindex']++ . '" class="input_text" />';
      $quickReply.= '</div>';
    }
  }
  
  if ($context['require_verification']) {
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">Code</span>';
    $quickReply.= template_control_verification($context['visual_verification_id'], 'all');
    $quickReply.= '</div>';
    $quickReply.= '<div class="no-left-padding input-container pad-top">';
    $quickReply.= '<span class="input-label">Verify</span>';
    $quickReply.= '<input type="text" tabindex="' . $context['tabindex']++ . '" name="post_vv[code]" />';
    $quickReply.= '</div>';
  }
  
  $quickReply.= '<div class="child buttons">
  
  <button class="button" type="submit">' . $txt['iPost'] . '</button>

  </div>';
  
  if (isset($context['num_replies'])) $quickReply.= '<input type="hidden" name="num_replies" value="' . $context['num_replies'] . '" />';
  
  if (!empty($context['subject'])) {
    $quickReply.= '<input type="hidden" name="subject" value="' . $context['subject'] . '" />';
  }
  
  $quickReply.= '
      <input type="hidden" name="additional_options" value="' . ($context['show_additional_options'] ? 1 : 0) . '" />
      <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />
      <input type="hidden" name="seqnum" value="' . $context['form_sequence_number'] . '" />
      <input type="hidden" name="topic" value="' . $context['current_topic'] . '" />
      <input type="hidden" name="' . $context['session_var'] . '" value="' . $context['session_id'] . '" />    
      <input type="hidden" name="goback" value="' . $options['return_to_post'] . '" />
    </form>';
  
  $quickReply.= '</div>';
  
  echo '<script type="text/javascript">
    $(function() {
      $(".topbar").last().append(', json_encode($quickReply), ');
    });
  </script>';
}

?>