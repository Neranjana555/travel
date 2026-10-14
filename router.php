<?php
$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH) ?: '/');
if(preg_match('~(?:^|/)(storage|\.|config\.php|app\.php|seed\.json|seed-builder\.php|verify\.php|README\.md|router\.php|tour-section\.php|review-section\.php|site-data\.php|admin-data\.php|admin-tours\.php|contact-data\.php|tour-posters\.php)(?:/|$)~i',$path) || str_contains($path,'..')) { http_response_code(404); exit('Not found'); }
if($path==='/') { require __DIR__.'/index.php'; return true; }
if(is_file(__DIR__.$path)) return false;
http_response_code(404); echo 'Not found';
