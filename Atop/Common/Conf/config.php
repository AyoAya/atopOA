<?php 
 return array (
  'EMAIL_HOST' => 'smtp.exmail.qq.com',
  'EMAIL_PORT' => '465',
  'EMAIL_USERNAME' => 'oa@atoptechnology.com',
  'EMAIL_PASSWORD' => 'Atop123456',
  'EMAIL_FROM_NAME' => '华拓光通信OA系统',
  'MD5_KEY' => 'www.atoptechnology.com',
  'VERIFY_USECURVE' => true,
  'VERIFY_USENOISE' => false,
  'VERIFY_USEIMGBG' => false,
  'UPLOAD_FILETYPEEXTS' => '*.bin;*.jpg;*.png;*.gif;*.bmp;*.jpeg;*.txt;*.zip;',
  'UPLOAD_UPLOADLIMIT' => '5',
  'UPLOAD_FILESIZELIMIT' => '20',
  'PAGE_STATUS_INFO' => true,
  'LOGIN_VERIFY' => 0,
  'DB_TYPE' => 'mysql',
  'DB_HOST' => 'localhost',
  'DB_NAME' => 'atop',
  'DB_USER' => 'root',
  'DB_PWD' => 'root',
  'DB_PORT' => '3306',
  'DB_PREFIX' => 'atop_',
  'MODULE_ALLOW_LIST' => 
  array (
    0 => 'Home',
  ),
  'DEFAULT_MODULE' => 'Home',
  'TMPL_TEMPLATE_SUFFIX' => '.html',
  'DEFAULT_THEME' => 'Default',
  'URL_MODEL' => 1,
  'LIMIT_SIZE' => '10',
  'USER_THEME' => 'Default',
  'URL_ROUTER_ON' => true,
  'URL_ROUTE_RULES' => 
  array (
    '/^Logout$/' => 'index.php/Home/Index/logout',
  ),
);