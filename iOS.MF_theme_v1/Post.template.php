<?php
// Version: 2.0 RC4; Post

function template_main()
{
  global $context, $settings, $options, $txt, $scripturl, $modSettings;

  echo '<script>
      $(function(){
        $(".editor").last().autosize().resize();
        $(".theTitle").last().html("', (empty($context['subject']) ? 'New Topic' : $context['subject']),'");
        $(".classic").last().hide();

        //Deal with the race condition between iOS keyboard showing and the focus event firing
        var jqElement = $(".editor").last();
        jqElement.attr("disabled", true);

        jqElement.on("tap", function(event) {
          if (event.target.id == "', $context['post_box_name'], '") {
            if (!$(event.target).is(":focus")) {

              // Hide toolbar
              if(/iPhone|iPod|Android|iPad/.test(window.navigator.platform)){
                $(".toolbar").css("display", "none");
                $("#copyright").css("margin-bottom", "4px");
              }

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
      });

    </script>';
      
  echo '<form data-ajax="false" action="', $scripturl, '?action=', $context['destination'], ';', empty($context['current_board']) ? '' : 'board=' . $context['current_board'], '" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="', ($context['becomes_approved'] ? '' : 'alert(\'' . $txt['js_post_will_require_approval'] . '\');'), 'submitonce(this);smc_saveEntities(\'postmodify\', [\'subject\', \'', $context['post_box_name'], '\', \'guestname\', \'evtitle\', \'question\'], \'options\');" enctype="multipart/form-data" style="margin: 0;">';
        
if(!empty($context['post_error']['messages']) && count($context['post_error']['messages']))    
{
  echo '<div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['post_error']['messages']), '</div></div>';
    if(empty($context['subject'])) {
      echo '<style> #newTopic { padding-top: 9px; } </style>';        
    }
    else {
      echo '<style> #postContainer { padding-top: 9px; } </style>';        
    }
}
    
if(empty($context['subject'] ))
{
  echo '<div id="newTopic" class="inputContainer">';
    echo '<span class="inputLabel">Topic</span>';
    echo '<input type="text" tabindex="1" name="subject" value="' . $context['subject'] . '" />';
  echo '</div>';
}    
  
echo'
  <div id="postContainer" class="inputContainer">
    <div class="newPost">
         ',template_control_richedit($context['post_box_name'], 'message'),'
    </div>
  </div>';

  if (!empty($context['current_attachments']) || $context['can_post_attachment'])
  {
    echo '<div id="attachment_wrapper">';

    // If this post already has attachments on it - give information about them.
    if (!empty($context['current_attachments']))
    {
      echo '<input type="hidden" name="attach_del[]" value="0" />';
      foreach ($context['current_attachments'] as $attachment)
        echo '
        <div class="attachment">
          <input type="checkbox" id= "attachment_', $attachment['id'], '" name="attach_del[]" value="', $attachment['id'], '"', empty($attachment['unchecked']) ? ' checked="checked"' : '', ' class="input_check" /> ', $attachment['name'], (empty($attachment['approved']) ? ' (' . $txt['awaiting_approval'] . ')' : ''), '
        </div>';
    }

    // Is the user allowed to post any additional ones? If so give them the boxes to do it!
    if ($context['can_post_attachment'])
    {
      echo '<div style="position: relative;">';
        echo '<input type="file" size="60" name="attachment[]" id="inputfile" style="padding-left: 5px;" />';
        echo '<div id="inputbuttonbackground"><div id="inputbutton" class="needsclick" onclick="document.getElementById(\'inputfile\').click();this.blur();">Choose File</div></div>';
      echo '</div>';
    }
      
    echo '</div>';
  }

  // Guests have to put in their name and email...
  if (isset($context['name']) && isset($context['email']))
  {
    echo '<div class="noLeftPadding inputContainer">';
    echo '<span class="inputLabel">'. $txt['username'] .'</span>';
    echo '<input type="text" name="guestname" size="25" value="', $context['name'], '" tabindex="', $context['tabindex']++, '" class="input_text" />';
    echo '<span id="smf_autov_username_div" style="display: none;">
            <a id="smf_autov_username_link" href="#">
              <img id="smf_autov_username_img" src="', $settings['images_url'], '/icons/field_check.png" alt="*" />
            </a>
          </span>';
    echo '</div>';

    if (empty($modSettings['guest_post_no_email']))
    {
      echo '<div class="noLeftPadding inputContainer">';
      echo '<span class="inputLabel">'. $txt['email'] .'</span>';
      echo '<input type="text" name="email" size="25" value="', $context['email'], '" tabindex="', $context['tabindex']++, '" class="input_text" />';
      echo '</div>';
    }
  }

  if($context['require_verification'])
  {
    echo '<div class="noLeftPadding inputContainer">';
    echo '<span class="inputLabel">Code</span>';
    echo template_control_verification($context['visual_verification_id'], 'all');
    echo '</div>';
    echo '<div class="noLeftPadding inputContainer">';
    echo '<span class="inputLabel">Verify</span>';
    echo '<input type="text" tabindex="', $context['tabindex']++, '" name="post_vv[code]" />';
    echo '</div>';
  }
  
  echo '<div class="child buttons">
  
  <button class="button" type="submit" onclick="$(\'.editor\').last().blur(); $(\'.editor\').last().removeAttr(\'disabled\'); $(\'.ui-loader\').last().show();">', $txt['iPost'] ,'</button>

  </div>';

  
  if (isset($context['num_replies']))
    echo '<input type="hidden" name="num_replies" value="', $context['num_replies'], '" />';

  if(!empty($context['subject'] ))
  {
    echo '<input type="hidden" name="subject" value="' . $context['subject'] . '" />';
  }
    
  echo '
      <input type="hidden" name="additional_options" value="', $context['show_additional_options'] ? 1 : 0, '" />
      <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
      <input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
      <input type="hidden" name="topic" value="', $context['current_topic'], '" />
      <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />    
      <input type="hidden" name="goback" value="', $options['return_to_post'] ,'" />
    </form>';
  
}

?>