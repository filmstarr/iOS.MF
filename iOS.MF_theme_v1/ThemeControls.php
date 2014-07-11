<?php

//Generate a paging control
function template_control_paging($pageControl = null) {
  global $context, $txt;
  
  if ($pageControl) {
    parse_page_control($pageControl);
  }
  
  echo '

  <script type="text/javascript">

    $(function(){

      Hammer($(".previous-page").last()).on("tap", function(event) {
        $.mobile.changePage(\'', $context['links']['prev'], '\');
      });

      Hammer($(".next-page").last()).on("tap", function(event) {
        $.mobile.changePage(\'', $context['links']['next'], '\');
      });

      Hammer($(".previous-page").last()).on("hold", function(event) {
        $.mobile.changePage(\'', $context['links']['first'], '\');
      });

      Hammer($(".next-page").last()).on("hold", function(event) {
        $.mobile.changePage(\'', $context['links']['last'], '\');
      });

    });

  </script>


  <div id="page-buttons" class="page buttons">
  
  <button id="previous-page" class="previous-page button" ', $context['page_info']['current_page'] == 1 ? 'disabled="disabled"' : '', '>', $txt['iPrev'], '</button>
  
  <button id="page-count">', $txt['iPage'], ' ', $context['page_info']['current_page'], ' ', $txt['iOf'], ' ', ($context['page_info']['num_pages'] == 0) ? '1' : $context['page_info']['num_pages'], '</button>
  
  <button id="next-page" class="next-page button" ', ($context['page_info']['current_page'] == $context['page_info']['num_pages'] || $context['page_info']['num_pages'] == 0) ? 'disabled="disabled"' : '', '>', $txt['iNext'], '</button>
  
  </div>';
}

//Backward engineer paging links and details from page contol
function parse_page_control($pageControl) {
  global $context;
  
  $currentPageStart = strrpos($pageControl, '<strong>') + 8;
  $currentPageEnd = strrpos($pageControl, '</strong>');
  $currentPage = substr($pageControl, $currentPageStart, $currentPageEnd - $currentPageStart);
  
  $lastLinkEnd = strrpos($pageControl, '</a>');
  $lastLinkStart = $lastLinkEnd ? strrpos($pageControl, '>', -strlen($pageControl) + $lastLinkEnd) + 1 : false;
  $lastLinkedPage = $lastLinkStart && $lastLinkEnd ? substr($pageControl, $lastLinkStart, $lastLinkEnd - $lastLinkStart) : 1;
  
  $lastPageCountEnd = $lastLinkStart ? $lastLinkStart - 2 : false;
  $lastPageCountStart = $lastPageCountEnd ? strrpos($pageControl, 'start=', -strlen($pageControl) + $lastPageCountEnd) + 6 : false;
  $lastPageCount = $lastPageCountStart && $lastPageCountEnd ? substr($pageControl, $lastPageCountStart, $lastPageCountEnd - $lastPageCountStart) : 1;
  $pageSize = $lastPageCount / ($lastLinkedPage - 1);
  
  $firstPage = 1;
  $lastPage = $currentPage > $lastLinkedPage ? $currentPage : $lastLinkedPage;
  
  $previousPage = $currentPage >= 2 ? $currentPage - 1 : 1;
  $nextPage = $currentPage == $lastPage ? $lastPage : $currentPage + 1;
  
  $urlStart = strrpos($pageControl, 'href="') + 6;
  $urlEnd = $urlStart ? strrpos($pageControl, '"', $urlStart) : false;
  $url = $urlStart && $urlEnd ? substr($pageControl, $urlStart, $urlEnd - $urlStart) : '';
  $url = substr($url, 0, strrpos($url, ';')) . ';start=';
  
  $context['links'] = array('first' => $url . (($firstPage - 1) * $pageSize), 'prev' => $url . (($previousPage - 1) * $pageSize), 'next' => $url . (($nextPage - 1) * $pageSize), 'last' => $url . (($lastPage - 1) * $pageSize),);
  
  $context['page_info'] = array('current_page' => $currentPage, 'num_pages' => $lastPage,);
}
?>