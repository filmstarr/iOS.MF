<?php
// Version: 2.0 RC4; PersonalMessage

function template_pm_above()
{
}


function template_pm_below()
{
}

function template_folder()
{
  global $context, $settings, $options, $scripturl, $modSettings, $txt;

  if (empty($settings['show_user_images']) || !empty($options['show_no_avatars']))
  {
    echo '<style>
      .message { min-height: initial !important; }
      #avatar { display: none; }
      .message_time { margin-bottom: 5px !important; }
    </style>';
  }

  echo '<script>  
    $(function() {
      function handler(event) {
        event.stopPropagation();
      }
      $("a").click(handler);
    });
  </script>';

  echo '<div class="child buttons noLeftPadding">
    <button class="button" style="width: 150px;" onclick="$.mobile.changePage(\'' , $scripturl , '?action=pm;sa=send\');">Compose Message</button>
  </div>';

  echo '<ul class="content2">';

    while ($message = $context['get_pmessage']('message'))
    {
      echo'
        <li>';
          // Comment out subject stuff
          //echo '<div class="postDetails">' , $message['counter'] + 1 , '. ', $message['subject'] ,'</div>';
          echo '
          <div>
            <button class="button slimbutton" id="editdel" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != -1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';u=', $message['member']['id'], '\');" >', $txt['reply'] ,'</button>
            <button class="button slimbutton" id="editdel" onclick="if (confirm(\'', $txt['remove_message'], '?\')) { $.mobile.changePage(\'', $scripturl, '?action=pm;sa=pmactions;pm_actions[', $message['id'], ']=delete;f=', $context['folder'], ';start=', $context['start'], $context['current_label_id'] != -1 ? ';l=' . $context['current_label_id'] : '', ';', $context['session_var'], '=', $context['session_id'], '\'); }"> ', $txt['remove'],' </button>
          </div>

          <div class="posterinfo" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', isset($message['member']['href']) ? $message['member']['href'] : '' ,'\')"><span class="name">', $message['member']['name'] ,'</span>';
          if (!empty($settings['show_user_images']) && empty($options['show_no_avatars']))
            if (empty($message['member']['avatar']['image'])) {
              echo '<div id="avatar" style="background: url('.$settings['theme_url'].'/images/noavatar.png) #F5F5F5 center no-repeat;"></div>';
            }
            else {
              echo '<div id="avatar" style="background: url('.str_replace(' ','%20', $message['member']['avatar']['href']).') #fff center no-repeat;"></div>';
            }
          echo '
        
          </div>
          

          <div class="message" onclick="$(this).parent().addClass(\'clicked\'); $.mobile.changePage(\'', $scripturl, '?action=pm;sa=send;f=', $context['folder'], $context['current_label_id'] != -1 ? ';l=' . $context['current_label_id'] : '', ';pmsg=', $message['id'], ';quote', $context['folder'] == 'sent' ? '' : ';u=' . $message['member']['id'], '\');">
            <span class="message_time" style="font-style: italic;font-size:11px;display:inline-block;margin-bottom:3px;">', str_replace('strong','span',$message['time']) ,'</span><br />
          ', str_replace(rtrim($scripturl,'/index.php') . '/Smileys/default/', $settings['theme_url'] . '/images/SkypeEmoticons/',str_replace('<strong>Today</strong>','Today',short1($message['body']))) ,'
          </div>

        </li>';
    }

  echo '</ul>';
    
  require_once ($settings[theme_dir].'/ThemeControls.php');
  template_control_paging();
}


function template_send()
{
  global $context, $settings, $options, $scripturl, $modSettings, $txt;

  echo '<script>
      $(function(){
        $(".editor").last().autosize().resize();
        $(".classic").last().hide();
        
        // Comment out subject stuff
        //$(".theTitle").last().html("', (empty($context['subject']) ? 'New Topic' : $context['subject']),'");

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

  echo '<form data-ajax="false" action="', $scripturl, '?action=pm;sa=send2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" class="flow_hidden" onsubmit="submitonce(this);smc_saveEntities(\'postmodify\', [\'subject\', \'message\']);">';

  if(!empty($context['post_error']['messages']) && count($context['post_error']['messages']))    
  {
    echo '<div class="errors"><div style="margin-top: 6px;">*', implode('</div><div style="margin-top: 6px;">*', $context['post_error']['messages']), '</div></div>';
    echo '<style> #newTopic { padding-top: 9px; } </style>';        
  }

  // Comment out subject stuff and hide subject
  // echo '<div id="newTopic" class="inputContainer">';
  //   echo '<span class="inputLabel">Topic</span>';
     echo '<input type="hidden" tabindex="', $context['tabindex']++, '" name="subject" value="Sent from iOS.MF" maxlength="50" />';
  // echo '</div>';

  echo '<div id="newTopic" class="inputContainer">';
    echo '<span class="inputLabel">'. $txt['iTo'] .'</span>';
    //Users drop down list
    require_once ($settings[theme_dir].'/ThemeFunctions.php');
    $users = UserList();
    echo '<select name="to" tabindex="', $context['tabindex']++, '" form="postmodify">';
    echo '<option></option>';
    foreach ($users as $user) {
      echo '<option ' . (strpos($context['to_value'], $user) ? 'selected' : '') . '>' . $user . '</option>';
    }
    echo '</select>';
  echo '</div>';

    
  echo'
    <div id="postContainer" class="inputContainer">
      <div class="newPost">
           ',template_control_richedit($context['post_box_name'], 'message'),'
      </div>
    </div>'; 

  if($context['require_verification'])
  {
    echo '<div class="noLeftPadding inputContainer">';
    echo '<span class="inputLabel">Code</span>';
    echo template_control_verification($context['visual_verification_id'], 'all');
    echo '</div>';
    echo '<div class="noLeftPadding inputContainer">';
    echo '<span class="inputLabel">Verify</span>';
    echo '<input type="text" tabindex="', $context['tabindex']++, '" name="pm_vv[code]" />';
    echo '</div>';
  }
  
  echo '<div class="child buttons">
  
  <button class="button" type="submit" onclick="$(\'.editor\').last().blur(); $(\'.editor\').last().removeAttr(\'disabled\'); $(\'.ui-loader\').last().show();">', $txt['iPost'] ,'</button>

  </div>';

  echo '
  <input type="hidden" name="', $context['session_var'], '" value="', $context['session_id'], '" />
  <input type="hidden" name="seqnum" value="', $context['form_sequence_number'], '" />
  <input type="hidden" name="replied_to" value="', !empty($context['quoted_message']['id']) ? $context['quoted_message']['id'] : 0, '" />
  <input type="hidden" name="pm_head" value="', !empty($context['quoted_message']['pm_head']) ? $context['quoted_message']['pm_head'] : 0, '" />
  <input type="hidden" name="f" value="', isset($context['folder']) ? $context['folder'] : '', '" />
  <input type="hidden" name="l" value="', isset($context['current_label_id']) ? $context['current_label_id'] : -1, '" />

  </form>
';

}
?>