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

    while ($message = $context['get_pmessage']('message'))
    {
    

  echo'
  <h2>', $message['subject'] ,'</h2>
  
  <ul class="posts">
    
    <li>
    
    <div class="posterinfo" onclick="$.mobile.changePage(\'', $message['member']['href'] ,'\')">
      <span class="name">', $message['member']['name'] ,'</span>';
      if (!$message['member']['avatar']['href']) {
        echo '<div id="avatar" style="background: url('.$settings['theme_url'].'/images/noavatar.png) #F5F5F5 center no-repeat !important;"></div>';
      }
      else {
        echo '<div id="avatar" style="background: url('.str_replace(' ','%20', $message['member']['avatar']['href']).') #fff center no-repeat !important;"></div>';
      }
      echo '
    </div>
    
    <div class="last message" onclick="$.mobile.changePage(\'', $scripturl, '?action=pm;sa=send;f=inbox;pmsg=',$message['id'],';u=',$message['member']['id'],'\');"><span style="font-weight:bold;font-size:11px;">', $message['time'] ,'</span><br />
      ', short1($message['body']), '
    </div>
    
    <br style="clear:both;" />
      
    </li>
  </ul>';
      
    }
    
  require_once ($settings[theme_dir].'/ThemeControls.php');
  template_control_paging();
}


function template_send()
{
  global $context, $settings, $options, $scripturl, $modSettings, $txt;

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