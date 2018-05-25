<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['Sandbox'] = TRUE;
$config['APIVersion'] = '85.0';

$config['APIUsername'] = $config['Sandbox'] ? 'sandbo_1215254764_biz_api1.angelleye.com' : 'pagos_api1.mediafem.com';
$config['APIPassword'] = $config['Sandbox'] ? '1215254774' : '9DGG7945UTDVGJJ5';
$config['APISignature'] = $config['Sandbox'] ? 'AiKZhEEPLJjSIccz.2M.tbyW5YFwAb6E3l6my.pY9br1z2qxKx96W18v' : 'AFcWxV21C7fd0v3bYYYRCpSSRl31At0Ghm4KZEDEU5Bxd38hZSCw0u7j';
$config['DeviceID'] = $config['Sandbox'] ? '' : 'PRODUCTION_DEVICE_ID_GOES_HERE';
$config['ApplicationID'] = $config['Sandbox'] ? 'APP-80W284485P519543T' : 'PRODUCTION_APP_ID_GOES_HERE';
$config['DeveloperEmailAccount'] = $config['Sandbox'] ? 'andrew@angelleye.com' : 'PRODUCTION_DEV_EMAIL_GOES_HERE';
$config['PayFlowUsername'] = $config['Sandbox'] ? 'tester' : 'PRODUCTION_USERNAME_GOGES_HERE';
$config['PayFlowPassword'] = $config['Sandbox'] ? 'Passw0rd~' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['PayFlowVendor'] = $config['Sandbox'] ? 'angelleye' : 'PRODUCTION_VENDOR_GOES_HERE';
$config['PayFlowPartner'] = $config['Sandbox'] ? 'PayPal' : 'PRODUCTION_PARTNER_GOES_HERE';

/*
$config['APIUsername'] = $config['Sandbox'] ? 'pagos_api1.mediafem.com' : 'pagos_api1.mediafem.com';
$config['APIPassword'] = $config['Sandbox'] ? '9DGG7945UTDVGJJ5' : '9DGG7945UTDVGJJ5';
$config['APISignature'] = $config['Sandbox'] ? 'AFcWxV21C7fd0v3bYYYRCpSSRl31At0Ghm4KZEDEU5Bxd38hZSCw0u7j' : 'AFcWxV21C7fd0v3bYYYRCpSSRl31At0Ghm4KZEDEU5Bxd38hZSCw0u7j';
$config['DeviceID'] = $config['Sandbox'] ? '' : 'PRODUCTION_DEVICE_ID_GOES_HERE';
$config['ApplicationID'] = $config['Sandbox'] ? 'APP-80W284485P519543T' : 'PRODUCTION_APP_ID_GOES_HERE';
$config['DeveloperEmailAccount'] = $config['Sandbox'] ? 'andrew@angelleye.com' : 'PRODUCTION_DEV_EMAIL_GOES_HERE';
$config['PayFlowUsername'] = $config['Sandbox'] ? 'tester' : 'PRODUCTION_USERNAME_GOGES_HERE';
$config['PayFlowPassword'] = $config['Sandbox'] ? 'Passw0rd~' : 'PRODUCTION_PASSWORD_GOES_HERE';
$config['PayFlowVendor'] = $config['Sandbox'] ? 'angelleye' : 'PRODUCTION_VENDOR_GOES_HERE';
$config['PayFlowPartner'] = $config['Sandbox'] ? 'PayPal' : 'PRODUCTION_PARTNER_GOES_HERE';
*/
/* End of file paypal.php */
/* Location: ./system/application/config/paypal.php */