<?php
// Version: 2.0 RC4; GenericControls

function template_control_richedit($editor_id, $display_controls = 'all')
{
  global $context, $settings, $options, $txt, $modSettings, $scripturl;

  $editor_context = &$context['controls']['richedit'][$editor_id];

  if ($display_controls !== 'all' && !is_array($display_controls))
    $display_controls = array($display_controls);

  if ($display_controls == 'all' || in_array('message', $display_controls))
  {
    echo '<textarea class="editor" name="', $editor_id, '" id="', $editor_id, '" rows="', $editor_context['rows'], '" cols="', $editor_context['columns'], '" tabindex="', $context['tabindex']++, '" style="width: 100%;', isset($context['post_error']['no_message']) || isset($context['post_error']['long_message']) ? 'border: 1px solid red;' : '', '">', $editor_context['value'], '</textarea>';
  }
}


// What's this, verification?!
function template_control_verification($verify_id, $display_type = 'all', $reset = false)
{
  global $context, $settings, $options, $txt, $modSettings;

  $verify_context = &$context['controls']['verification'][$verify_id];

  // Keep track of where we are.
  if (empty($verify_context['tracking']) || $reset)
    $verify_context['tracking'] = 0;

  // How many items are there to display in total.
  $total_items = count($verify_context['questions']) + ($verify_context['show_visual'] ? 1 : 0);

  // If we've gone too far, stop.
  if ($verify_context['tracking'] > $total_items)
    return false;

  // Loop through each item to show them.
  for ($i = 0; $i < $total_items; $i++)
  {
    // If we're after a single item only show it if we're in the right place.
    if ($display_type == 'single' && $verify_context['tracking'] != $i)
      continue;

    // Do the actual stuff - image first?
    if ($i == 0 && $verify_context['show_visual'])
    {
      if ($context['use_graphic_library'])
        echo '
        <img height="30" src="', $verify_context['image_href'], '" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '" />';
      else
        echo '
        <img src="', $verify_context['image_href'], ';letter=1" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_1" />
        <img src="', $verify_context['image_href'], ';letter=2" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_2" />
        <img src="', $verify_context['image_href'], ';letter=3" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_3" />
        <img src="', $verify_context['image_href'], ';letter=4" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_4" />
        <img src="', $verify_context['image_href'], ';letter=5" alt="', $txt['visual_verification_description'], '" id="verification_image_', $verify_id, '_5" />';
      
    }
    else
    {
      // Where in the question array is this question?
      $qIndex = $verify_context['show_visual'] ? $i - 1 : $i;

      echo '
        <div class="smalltext">
          ', $verify_context['questions'][$qIndex]['q'], ':<br />
          <input type="text" name="', $verify_id, '_vv[q][', $verify_context['questions'][$qIndex]['id'], ']" size="30" value="', $verify_context['questions'][$qIndex]['a'], '" ', $verify_context['questions'][$qIndex]['is_error'] ? 'style="border: 1px red solid;"' : '', ' tabindex="', $context['tabindex']++, '" />
        </div>';
    }

    // If we were displaying just one and we did it, break.
    if ($display_type == 'single' && $verify_context['tracking'] == $i)
      break;
  }

  // Assume we found something, always,
  $verify_context['tracking']++;

  // Tell something displaying piecemeal to keep going.
  if ($display_type == 'single')
    return true;
}

?>