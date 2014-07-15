<?php

// Version: 2.0 RC4; Errors

// !!!
/*	This template file contains only the sub template fatal_error. It is
	shown when an error occurs, and should show at least a back button and
	$context['error_message'].
*/

// Show an error message.....
function template_fatal_error() {
  global $context, $settings, $options, $txt;
  
	echo '<div class="errors"><div style="margin-top: 6px;">*', $context['error_message'] , '</div></div>';

}
?>