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
        <li>
          <div class="postDetails">', $message['subject'] ,'</div>
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

  if (!empty($context['post_error']['messages']))
  {
    echo '<br /><h4>', implode('<br /><br />', $context['post_error']['messages']), '</h4><br />';
  }


  echo '<form action="', $scripturl, '?action=pm;sa=send2" method="post" accept-charset="', $context['character_set'], '" name="postmodify" id="postmodify" onsubmit="submitonce(this);smc_saveEntities(\'postmodify\', [\'subject\', \'message\']);">';
  
  echo'  
  <ul class="login">
    <li>
      <div class="field">
        <div class="fieldname">'. $txt['iTo'] .'</div>
        <div class="fieldinfo"><input type="text" name="to" id="to_control" value="', $context['to_value'], '" tabindex="', $context['tabindex']++, '" size="40" /></div>
      </div>
    </li>
    <li>
      <div class="', !$context['require_verification'] ? 'last ':'','field">
        <div class="fieldname">'. $txt['iSubject'] .'</div>
        <div class="fieldinfo"><input type="text" name="subject" value="', $context['subject'], '" tabindex="', $context['tabindex']++, '" size="40" maxlength="50" /></div>
      </div>
    </li>';
    
if($context['require_verification'])
echo'
    <li>
      <div class="verification field">
        <div class="fieldname">'. $txt['iCode'] .'</div>
        <div class="fieldinfo">',template_control_verification($context['visual_verification_id'], 'all'),'</div>
      </div>
    </li>
    
    <li>
      <div class="last field">
        <div class="fieldname">'. $txt['iVerify'] .'</div>
        <div class="fieldinfo"><input type="text" name="pm_vv[code]" value="" size="30" tabindex="5" />
</div>
      </div>
    </li>  ';  
    
    
    echo'
    
  </ul>
      
  <h4>'. $txt['iMessage'] .'</h4>
  
  <ul class="posts">
  
    <li>
      <div class="last message">';
      
        echo template_control_richedit($context['post_box_name'], 'message');
      
      echo'</div>
    </li>
  
  </ul>

  
  <div class="child buttons">
  
  <button type="submit">'. $txt['iSend'] .'</button>
  
  </div>
  
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