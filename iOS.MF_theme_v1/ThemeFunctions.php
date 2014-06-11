<?php

function UnreadPostCount()
{
	  global $context, $smcFunc;

  $request = $smcFunc['db_query']('', '
    SELECT MIN(lmr.id_msg)
    FROM {db_prefix}boards AS b
      LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = b.id_board AND lmr.id_member = {int:current_member})
    WHERE {query_see_board}',
    array(
      'current_member' => $context['user']['id'],
    )
  );
  list ($earliest_msg) = $smcFunc['db_fetch_row']($request);
  $smcFunc['db_free_result']($request);

  // This is needed in case of topics marked unread.
  if (empty($earliest_msg))
    $earliest_msg = 0;
  else
  {
    // This query is pretty slow, but it's needed to ensure nothing crucial is ignored.
    $request = $smcFunc['db_query']('', '
      SELECT MIN(id_msg)
      FROM {db_prefix}log_topics
      WHERE id_member = {int:current_member}',
      array(
        'current_member' => $context['user']['id'],
      )
    );
    list ($earliest_msg2) = $smcFunc['db_fetch_row']($request);
    $smcFunc['db_free_result']($request);

    // In theory this could be zero, if the first ever post is unread, so fudge it ;)
    if ($earliest_msg2 == 0)
      $earliest_msg2 = -1;

    $earliest_msg = min($earliest_msg2, $earliest_msg);
  }

  $result = $smcFunc['db_query']('', '
    SELECT COUNT(*) unread_count
    FROM {db_prefix}topics AS t
      LEFT JOIN {db_prefix}log_topics AS lt ON (lt.id_topic = t.id_topic AND lt.id_member = {int:current_member})
      LEFT JOIN {db_prefix}log_mark_read AS lmr ON (lmr.id_board = t.id_board AND lmr.id_member = {int:current_member})
    WHERE ' . (!empty($earliest_msg) ? 't.id_last_msg > {int:earliest_msg} AND' : '') . '
      IFNULL(lt.id_msg, IFNULL(lmr.id_msg, 0)) < t.id_last_msg',
    array(
      'current_member' => $context['user']['id'],
      'earliest_msg' => !empty($earliest_msg) ? $earliest_msg : 0,
    )
  );

  $result = $smcFunc['db_fetch_assoc']($result);
  return $result['unread_count'];
}

?>