<?php


/* This is the error view which we will see when something unexpected happens */

function template_fatal_error() {
  global $context, $settings, $options, $txt;
  
	echo '<div class="errors"><div style="margin-top: 6px;">*', $context['error_message'] , '</div></div>';

}
?>