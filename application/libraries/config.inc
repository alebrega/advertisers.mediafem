<?php
if (!defined('BASE_URI')) {
	//define('BASE_URI', 'http://hb.sand-08.adnxs.net');
        //define('BASE_URI', 'http://api.redvlog.com');
        define('BASE_URI', 'https://api.appnexus.com');
}

//define('DW_USERNAME', 'redvlog-api');
//define('DW_PASSWORD', 'ch@ng3M3');

define('BASE_URI_EPLANNING', 'https://admin.ar.e-planning.net');

define('DW_USERNAME', 'ac@redvlog.com');
define('DW_PASSWORD', 'mediafem14M!');

//define('DW_USERNAME', 'admin_redvlog');
//define('DW_PASSWORD', '57c67eb9');

//define('DW_USERNAME', 'apiredvlog');
//define('DW_PASSWORD', '15f2c302');


function ObtenerNavegador($user_agent) {
     $navegadores = array(
          'Opera' => 'Opera',
          'Mozilla Firefox'=> '(Firebird)|(Firefox)',
          'Galeon' => 'Galeon',
          'Mozilla'=>'Gecko',
          'MyIE'=>'MyIE',
          'Lynx' => 'Lynx',
          'Netscape' => '(Mozilla/4\.75)|(Netscape6)|(Mozilla/4\.08)|(Mozilla/4\.5)|(Mozilla/4\.6)|(Mozilla/4\.79)',
          'Konqueror'=>'Konqueror',
          'Internet Explorer 7' => '(MSIE 7\.[0-9]+)',
          'Internet Explorer 6' => '(MSIE 6\.[0-9]+)',
          'Internet Explorer 5' => '(MSIE 5\.[0-9]+)',
          'Internet Explorer 4' => '(MSIE 4\.[0-9]+)',
);
foreach($navegadores as $navegador=>$pattern){
       if (eregi($pattern, $user_agent))
       return $navegador;
    }
return 'Desconocido';
}
?>
