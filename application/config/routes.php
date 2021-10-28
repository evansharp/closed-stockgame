<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';

$route['ticker'] = 'ticker';
$route['buysell'] = 'buysell';
$route['portfolio'] = 'portfolio';
$route['history'] = 'history';
$route['prospectus'] = 'prospectus';
$route['leaderboard'] = 'leaderboard';


$route['admin/(:any)'] = 'admin/index/$1';
//$route['ajax/(:any)'] = 'ajax/$1';
$route['marketupdate'] = 'market/gameTick';

$route['default_controller'] = 'ticker';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['default_controller'] = 'ticker';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
