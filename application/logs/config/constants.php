<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/

define('ID_MULTIPLICAR_VOLUMEN', 7);
define('INTENTOS_RESPUESTA', 11);
define('IMPORTE_COBRO_CAMPANIA', 14);

define('PENDIENTE', 'Aprobaci&oacute;n pendiente');
define('CAMPANIA_APROBADA', 'Aprobada');
define('CAMPANIA_NO_APROBADA', 'No aprobada');
define('PENDIENTE_PAUSA', 'Pendiente de pausa');

define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

define('MENSAJE_MANTENIMIENTO_ANUNCIANTES', 17);
define('MENSAJE_MANTENIMIENTO_CREAR_CAMPANIAS_ANUNCIANTES', 25);
define('MENSAJE_MANTENIMIENTO_DUPLICAR_CAMPANIAS_ANUNCIANTES', 26);
define('MENSAJE_MANTENIMIENTO_OBTENER_REPORTES_ANUNCIANTES', 27);
define('MENSAJE_MANTENIMIENTO_INVENTARIO_ANUNCIANTES', 28);

define('FORMATO_LS', 9);
define('FORMATO_L', 10);
define('FORMATO_S', 11);
define('FORMATO_VIDEO_ZOCALO', 23);
define('FORMATO_VIDEO_VIRAL', 24);

define('EMAIL_ANUNCIANTES', 'account@mediafem.com');
define('TELEFONO_ANUNCIANTES', '(+5411) 4243-4000');

define('ID_DEPARTAMENTO_TRAFICKER', 7);

define('CAMBIO_FIJO_ARGENTINA', 21);
define('CAMBIO_FIJO_MEXICO', 22);

define('CTR_STANDARD', 23);

define('VALOR_PUBLINOTA', 24);

define('HISTORIAL_STRING', 'EZlmKNMB7O');

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/* End of file constants.php */
/* Location: ./application/config/constants.php */