function removeOnClick(objClass){
var elements = document.getElementsByTagName('div');
  for (i=0; i<elements.length; i++){
    if (elements[i].className==objClass){
      elements[i].onclick=null;
    }
  }
}

function quoting(){

    if(aquoting==0){
    aquoting = 1;    
    // disable quoting
    Set_Cookie( 'disablequoting', '1', '', '/', '', '' );
    document.getElementById('quoting').innerHTML = quotingoff;
    removeOnClick('message');
    removeOnClick('last message');
    }
    else
    // enable quoting
    {
    Delete_Cookie('disablequoting', '/', '', '');
    document.getElementById('quoting').innerHTML = loading;
    window.location.href='';
    }

}