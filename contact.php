<?php
require __DIR__.'/app.php';
header('Content-Type: application/json');
if($_SERVER['REQUEST_METHOD']!=='POST') { http_response_code(405); echo json_encode(['message'=>'Use the website form.']); exit; }
check_csrf();
function respond($message,$status=200) { http_response_code($status); echo json_encode(['message'=>$message]); exit; }
if(!empty($_POST['website'])) respond('Request received.');
if(limited('submission',20)) respond('Please wait a moment before sending another request.',429);
$email=trim($_POST['email']??'');
if(!filter_var($email,FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/',$email) || strlen($email)>254) respond('Enter a valid email address.',422);
if(($_POST['kind']??'')==='newsletter') { $q=db()->prepare('INSERT OR IGNORE INTO subscribers(email) VALUES(?)'); $q->execute([$email]); respond('You are subscribed. Thank you!'); }
$name=trim($_POST['name']??''); $phone=trim($_POST['phone']??''); $message=trim($_POST['message']??'');
if(!$name || !$phone || !$message || strlen($name)>120 || strlen($phone)>60 || strlen($message)>10000) respond('Please complete all fields within the allowed lengths.',422);
$q=db()->prepare('INSERT INTO enquiries(name,email,phone,message) VALUES(?,?,?,?)'); $q->execute([$name,$email,$phone,$message]); $id=db()->lastInsertId();
$sent=send_enquiry($name,$email,$phone,$message);
if($sent) { $q=db()->prepare('UPDATE enquiries SET sent=1 WHERE id=?'); $q->execute([$id]); }
respond($sent ? 'Your enquiry has been saved and emailed. We will be in touch soon.' : 'Your enquiry has been saved for our team. You can also contact us on WhatsApp.');
