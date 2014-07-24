<?php

/*
* Generic controls template
*
* License: http://www.opensource.org/licenses/mit-license.php
*/


//textarea used when posting messages (not used in quick reply however)
function template_control_richedit($editorId) {
  global $context;
  
  $editorContext = & $context['controls']['richedit'][$editorId];
  echo '<textarea class="editor" name="', $editorId, '" id="', $editorId, '" rows="', $editorContext['rows'], '" cols="', $editorContext['columns'], '" tabindex="', $context['tabindex']++, '" style="width: 100%;', isset($context['post_error']['no_message']) || isset($context['post_error']['long_message']) ? 'border: 1px solid red;' : '', '">', $editorContext['value'], '</textarea>';
}

//Verification control used throughout the theme
function template_control_verification($verifyId, $displayType = 'all', $reset = false) {
  global $context, $settings, $options, $txt, $modSettings;
  
  $verification = "";
  
  $verifyContent = & $context['controls']['verification'][$verifyId];
  
  //Keep track of where we are
  if (empty($verifyContent['tracking']) || $reset) {
    $verifyContent['tracking'] = 0;
  }
  
  //How many items are there to display in total
  $totalItems = count($verifyContent['questions']) + ($verifyContent['show_visual'] ? 1 : 0);
  
  // If we've gone too far, stop
  if ($verifyContent['tracking'] > $totalItems) return false;
  
  // Loop through each item to show them
  for ($i = 0; $i < $totalItems; $i++) {
    
    // If we're after a single item only show it if we're in the right place
    if ($displayType == 'single' && $verifyContent['tracking'] != $i) continue;
    
    // Do the actual stuff - image first?
    if ($i == 0 && $verifyContent['show_visual']) {
      if ($context['use_graphic_library']) {
        $verification.= '
          <img class="verification-image" height="30" src="' . $verifyContent['image_href'] . '" alt="' . $txt['visual_verification_description'] . '" id="verification-image_' . $verifyId . '" />';
      } else {
        $verification.= '
          <img class="verification-image" src="' . $verifyContent['image_href'] . ';letter=1" alt="' . $txt['visual_verification_description'] . '" id="verification-image_' . $verifyId . '_1" />
          <img class="verification-image" src="' . $verifyContent['image_href'] . ';letter=2" alt="' . $txt['visual_verification_description'] . '" id="verification-image_' . $verifyId . '_2" />
          <img class="verification-image" src="' . $verifyContent['image_href'] . ';letter=3" alt="' . $txt['visual_verification_description'] . '" id="verification-image_' . $verifyId . '_3" />
          <img class="verification-image" src="' . $verifyContent['image_href'] . ';letter=4" alt="' . $txt['visual_verification_description'] . '" id="verification-image_' . $verifyId . '_4" />
          <img class="verification-image" src="' . $verifyContent['image_href'] . ';letter=5" alt="' . $txt['visual_verification_description'] . '" id="verification-image_' . $verifyId . '_5" />';
      }
    } else {
      
      // Where in the question array is this question?
      $qIndex = $verifyContent['show_visual'] ? $i - 1 : $i;
      
      $verification .= '
        <div class="smalltext">
          ' . $verifyContent['questions'][$qIndex]['q'] . ':<br />
          <input type="text" name="' . $verifyId . '_vv[q][' . $verifyContent['questions'][$qIndex]['id'] . ']" size="30" value="' . $verifyContent['questions'][$qIndex]['a'] . '" ' . ($verifyContent['questions'][$qIndex]['is_error'] ? 'style="border: 1px red solid;"' : '') . ' tabindex="' . $context['tabindex']++ . '" />
        </div>';
    }
    
    // If we were displaying just one and we did it, break
    if ($displayType == 'single' && $verifyContent['tracking'] == $i) {
      break;
    }
  }
  
  // Assume we found something, always
  $verifyContent['tracking']++;
  
  return $verification;
}

?>