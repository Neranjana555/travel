<?php
ini_set('session.use_strict_mode', '1');
session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off','samesite'=>'Lax']);
session_start();
$config = require __DIR__.'/config.php';
function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function db() {
 static $db;
 if ($db) return $db;
 $dir = getenv('TRAVENZO_DATA_DIR') ?: __DIR__.'/storage';
 if (!is_dir($dir)) mkdir($dir,0700,true);
 $db = new PDO('sqlite:'.$dir.'/site.sqlite');
 $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
 $db->exec('PRAGMA busy_timeout=5000');
 $db->exec('CREATE TABLE IF NOT EXISTS content (name TEXT PRIMARY KEY, body TEXT NOT NULL);
 CREATE TABLE IF NOT EXISTS tours (id INTEGER PRIMARY KEY, title TEXT NOT NULL, description TEXT NOT NULL, prices TEXT NOT NULL, days TEXT NOT NULL);
 CREATE TABLE IF NOT EXISTS reviews (id INTEGER PRIMARY KEY, name TEXT NOT NULL, rating INTEGER NOT NULL, body TEXT NOT NULL, approved INTEGER DEFAULT 0, created TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE IF NOT EXISTS enquiries (id INTEGER PRIMARY KEY, name TEXT, email TEXT, phone TEXT, message TEXT, sent INTEGER DEFAULT 0, created TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE IF NOT EXISTS subscribers (email TEXT PRIMARY KEY, created TEXT DEFAULT CURRENT_TIMESTAMP);
 CREATE TABLE IF NOT EXISTS login_attempts (ip TEXT PRIMARY KEY, attempts INTEGER DEFAULT 0, updated INTEGER);');
 if (!(int)$db->query('SELECT COUNT(*) FROM content')->fetchColumn()) {
  $seed = json_decode(file_get_contents(__DIR__.'/seed.json'),true,512,JSON_THROW_ON_ERROR);
  $db->beginTransaction();
  $q=$db->prepare('INSERT OR IGNORE INTO content VALUES (?,?)'); foreach($seed['content'] as $k=>$v) $q->execute([$k,$v]);
  $q=$db->prepare('INSERT OR IGNORE INTO tours(id,title,description,prices,days) VALUES (?,?,?,?,?)'); foreach($seed['tours'] as $t) $q->execute([$t['id'],$t['title'],$t['description'],json_encode($t['prices']),json_encode($t['days'])]);
  $db->commit();
 }
 return $db;
}
function csrf() { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function csrf_field() { return '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function check_csrf() { if(!hash_equals(csrf(),(string)($_POST['csrf']??''))) { http_response_code(403); exit('Your session expired. Reload the page and try again.'); } }
function content($name) { $q=db()->prepare('SELECT body FROM content WHERE name=?'); $q->execute([$name]); $html=$q->fetchColumn() ?: ''; return render_site_data($name,$html); }
function initialize_tour_posters() {
 $columns=db()->query('PRAGMA table_info(tours)')->fetchAll(PDO::FETCH_COLUMN,1);
 if(!in_array('poster',$columns)) {
  db()->exec("ALTER TABLE tours ADD COLUMN poster TEXT NOT NULL DEFAULT ''");
  db()->exec("UPDATE tours SET poster='assets/ten-day-tour.jpeg' WHERE id=2");
 }
}
function tours() { return db()->query('SELECT * FROM tours ORDER BY id')->fetchAll(PDO::FETCH_ASSOC); }
function flash($message) { $_SESSION['flash']=$message; }
function take_flash() { $v=$_SESSION['flash']??''; unset($_SESSION['flash']); return $v; }
function redirect($url) { header('Location: '.$url); exit; }
function limited($key,$seconds=30) { $now=time(); if($now-($_SESSION[$key]??0)<$seconds) return true; $_SESSION[$key]=$now; return false; }
function send_enquiry($name,$email,$phone,$message) {
 global $config;
 if (!$config['smtp_password']) return false;
 $socket=@stream_socket_client('ssl://smtp.gmail.com:465',$errno,$error,15);
 if (!$socket) return false;
 stream_set_timeout($socket,15);
 $read=function() use($socket) { $line=''; do { $part=fgets($socket,1024); if($part===false) throw new RuntimeException('SMTP disconnected'); $line=$part; } while(isset($part[3]) && $part[3]==='-'); return (int)substr($line,0,3); };
 $command=function($cmd,$expected) use($socket,$read) { fwrite($socket,$cmd."\r\n"); if($read()!==$expected) throw new RuntimeException('SMTP rejected command'); };
 try {
  if($read()!==220) throw new RuntimeException('SMTP unavailable');
  $command('EHLO travenzotravels.com',250); $command('AUTH LOGIN',334);
  $command(base64_encode($config['email']),334); $command(base64_encode($config['smtp_password']),235);
  $command('MAIL FROM:<'.$config['email'].'>',250); $command('RCPT TO:<'.$config['email'].'>',250); $command('DATA',354);
  $body="Name: $name\nEmail: $email\nPhone: $phone\n\n$message";
  $headers='From: Travenzo Travels <'.$config['email'].'>'."\r\n".'To: '.$config['email']."\r\n".'Reply-To: '.$email."\r\nSubject: New Travenzo travel enquiry\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n";
  $command($headers.chunk_split(base64_encode($body)).'.',250);
  fwrite($socket,"QUIT\r\n"); fclose($socket); return true;
 } catch(Throwable $ex) { fclose($socket); error_log('Travenzo SMTP: '.$ex->getMessage()); return false; }
}
db();

require __DIR__.'/site-data.php';

require __DIR__.'/contact-data.php';

initialize_tour_posters();

require __DIR__.'/tour-posters.php';
