<?php

function template_control_paging()
{
  global $context, $txt;

  echo '

  <script>

    $(function(){

      Hammer($("#previousPage")).on("tap", function(event) {
        window.location.href=\'', $context['links']['prev'] ,'\';
      });

      Hammer($("#nextPage")).on("tap", function(event) {
        window.location.href=\'', $context['links']['next'] ,'\';
      });

      Hammer($("#previousPage")).on("hold", function(event) {
        window.location.href=\'', $context['links']['first'] ,'\';
      });

      Hammer($("#nextPage")).on("hold", function(event) {
        window.location.href=\'', $context['links']['last'] ,'\';
      });

    });

  </script>


  <div id="pageButtons" class="page buttons">
  
  <button id="previousPage" class="button" ', $context['page_info']['current_page']==1 ? 'disabled="disabled"' : '', '>', $txt['iPrev'], '</button>
  
  <button id="pagecount">', $txt['iPage'], ' ', $context['page_info']['current_page'] ,' ', $txt['iOf'] ,' ', ($context['page_info']['num_pages']==0) ? '1' : $context['page_info']['num_pages'] ,'</button>
  
  <button id="nextPage" class="button" ', ($context['page_info']['current_page']==$context['page_info']['num_pages']||$context['page_info']['num_pages']==0) ? 'disabled="disabled"' : '', '>', $txt['iNext'], '</button>
  
  </div>';
}

?>