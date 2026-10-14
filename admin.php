<?php
require __DIR__.'/app.php';
header('Cache-Control: no-store');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf(); $action=$_POST['action']??'';
 if($action==='login') {
  $ip=hash('sha256',$_SERVER['REMOTE_ADDR']??'local'); $q=db()->prepare('SELECT * FROM login_attempts WHERE ip=?'); $q->execute([$ip]); $attempt=$q->fetch(PDO::FETCH_ASSOC);
  if($attempt && $attempt['attempts']>=5 && time()-(int)$attempt['updated']<900) $error='Too many attempts. Try again in 15 minutes.';
  elseif(hash_equals($config['admin_user'],(string)($_POST['username']??'')) && password_verify((string)($_POST['password']??''),$config['admin_password_hash'])) {
   session_regenerate_id(true); $_SESSION['admin']=true; $_SESSION['last_activity']=time(); $_SESSION['csrf']=bin2hex(random_bytes(32)); $q=db()->prepare('DELETE FROM login_attempts WHERE ip=?');$q->execute([$ip]); redirect('admin.php');
  } else { $count=$attempt && time()-(int)$attempt['updated']<900 ? (int)$attempt['attempts']+1 : 1; $q=db()->prepare('INSERT OR REPLACE INTO login_attempts VALUES(?,?,?)'); $q->execute([$ip,$count,time()]); $error='Incorrect username or password.'; }
 } else {
  if(empty($_SESSION['admin']) || time()-($_SESSION['last_activity']??0)>3600) { unset($_SESSION['admin']); redirect('admin.php'); }
  $_SESSION['last_activity']=time();
  if($action==='logout') { $_SESSION=[]; session_regenerate_id(true); redirect('admin.php'); }
  if($action==='site-data') {
   try { save_site_data(); flash('Website data saved.'); redirect('admin.php?tab=content'); }
   catch(Throwable $ex) { $error=$ex->getMessage(); }
  }
  if($action==='tour-poster') {
   try { flash(update_tour_poster()); redirect('admin.php?tab=tours'); } catch(Throwable $ex) { $error=$ex->getMessage(); }
  }
  if($action==='tour') {
   $newPoster='';
   try {
    $days=[];$prices=[];
    foreach((array)($_POST['days']??[]) as $day) {
     $location=trim((string)($day['location']??''));$activities=array_values(array_filter(array_map('trim',explode("\n",(string)($day['activities']??''))),fn($a)=>$a!==''));
     if(!$location || mb_strlen($location)>150 || !$activities || strlen(implode("\n",$activities))>5000) throw new RuntimeException('Each day needs a destination and at least one activity.');
     $days[]=['location'=>$location,'activities'=>$activities];
    }
    if(!count($days)||count($days)>60) throw new RuntimeException('A tour needs between 1 and 60 days.');
    foreach([2,4,6] as $people) { $price=trim((string)($_POST['prices'][$people]??''));if($price==='')continue;if(!is_numeric($price)||$price<0||$price>1000000)throw new RuntimeException('Enter a valid package price.');$prices[$people.' persons']=(float)$price; }
    $title=trim($_POST['title']??'');$description=trim($_POST['description']??'');if(!$title||mb_strlen($title)>150||mb_strlen($description)>2000)throw new RuntimeException('Enter a tour name and a description within the allowed lengths.');
        $id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT);
    if($id===false || $id<0) throw new RuntimeException('Choose a valid tour plan.');
    if($id===0) {
     $newPoster=upload_tour_poster();
     $q=db()->prepare('INSERT INTO tours(title,description,prices,days,poster) VALUES(?,?,?,?,?)');
     $q->execute([$title,$description,json_encode($prices),json_encode($days),$newPoster]);
     flash('Tour plan created and published.');
    } else {
     $check=db()->prepare('SELECT COUNT(*) FROM tours WHERE id=?');$check->execute([$id]);
     if(!$check->fetchColumn()) throw new RuntimeException('This tour plan no longer exists. Reload the page.');
     $q=db()->prepare('UPDATE tours SET title=?,description=?,prices=?,days=? WHERE id=?');
     $q->execute([$title,$description,json_encode($prices),json_encode($days),$id]);flash('Tour plan saved.');
    }
    redirect('admin.php?tab=tours');
   } catch(Throwable $ex) { if($newPoster) cleanup_tour_poster($newPoster); $error=$ex->getMessage(); }
  }
  if($action==='delete-tour') {
   $id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT);
   if($id===false||$id<1) $error='Choose a valid tour plan.';
   else { $lookup=db()->prepare('SELECT poster FROM tours WHERE id=?');$lookup->execute([$id]);$oldPoster=$lookup->fetchColumn() ?: '';$q=db()->prepare('DELETE FROM tours WHERE id=?');$q->execute([$id]);cleanup_tour_poster($oldPoster);flash($q->rowCount()?'Tour plan removed from the website.':'This tour plan has already been removed.');redirect('admin.php?tab=tours'); }
  }
  if($action==='approve'||$action==='hide'||$action==='delete') {
   $q=db()->prepare($action==='delete'?'DELETE FROM reviews WHERE id=?':'UPDATE reviews SET approved='.($action==='approve'?1:0).' WHERE id=?');$q->execute([(int)$_POST['id']]);flash('Review updated.');redirect('admin.php?tab=reviews');
  }
 }
}
if(!empty($_SESSION['admin']) && time()-($_SESSION['last_activity']??0)>3600) unset($_SESSION['admin']);
$logged=!empty($_SESSION['admin']); if($logged) $_SESSION['last_activity']=time();
$tab=in_array($_POST['action']??'',['tour','delete-tour','tour-poster'])?'tours':($_GET['tab']??'overview');
?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex"><title>Admin | Travenzo Travels</title><link rel="stylesheet" href="assets/enhancements.css"><link rel="icon" type="image/jpeg" href="assets/travenzo-logo.jpeg"></head><body class="portal"><header class="portal-header"><a class="brand" href="index.php"><img class="portal-logo" src="assets/travenzo-logo.jpeg" alt="Travenzo Travels Sri Lanka" width="76" height="76"><span>Administration</span></a><a href="index.php" target="_blank" rel="noopener">View website</a></header>
<?php if(!$logged): ?><main class="login-layout"><div class="login-story"><p>Travenzo Travels</p><h1>A remarkable journey starts behind the scenes.</h1><p>Manage your Sri Lanka tours, traveller stories and enquiries in one place.</p></div><section class="editor-panel login-panel"><h2>Welcome back</h2><p>Sign in to manage your website.</p><?php if($error): ?><p class="notice error" role="alert"><?= e($error) ?></p><?php endif ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="login"><label>Username<input name="username" autocomplete="username" required></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button type="submit" class="button">Sign in</button></form></section></main>
<?php else: ?><div class="admin-shell"><aside class="admin-sidebar"><p>Website workspace</p><?php foreach(['overview'=>'Dashboard','content'=>'Website data','tours'=>'Tour plans','reviews'=>'Reviews','enquiries'=>'Enquiries','subscribers'=>'Subscribers'] as $key=>$label): ?><a class="<?= $tab===$key?'active':'' ?>" href="?tab=<?= $key ?>"><?= $label ?></a><?php endforeach ?><form method="post"><?= csrf_field() ?><button name="action" value="logout" class="button secondary">Sign out</button></form></aside><main class="admin-main"><?php if($message=take_flash()): ?><div class="notice" role="status"><?= e($message) ?></div><?php endif ?><?php if($error): ?><div class="notice error" role="alert"><?= e($error) ?></div><?php endif ?>
<?php if($tab==='overview'): ?><div class="page-heading"><p>Your travel business</p><h1>Ready for the next journey.</h1><p>Keep your tours current and your travellers connected.</p></div><div class="stats-grid"><?php foreach(['tours'=>'Tour plans','enquiries'=>'Enquiries','reviews'=>'Reviews','subscribers'=>'Subscribers'] as $table=>$label): ?><a href="?tab=<?= $table ?>"><strong><?= (int)db()->query('SELECT COUNT(*) FROM '.$table)->fetchColumn() ?></strong><span><?= $label ?></span></a><?php endforeach ?></div><section class="editor-panel"><h2>Email delivery</h2><p>From and to: <?= e($config['email']) ?></p><p><?= $config['smtp_password']?'Gmail SMTP is configured.':'Enquiries are stored here. Set TRAVENZO_GMAIL_APP_PASSWORD on your server to enable Gmail delivery.' ?></p></section>
<?php elseif($tab==='content'): include __DIR__.'/admin-data.php'; ?>
<?php elseif($tab==='tours'): include __DIR__.'/admin-tours.php'; ?>
<?php elseif($tab==='reviews'): ?><h1>Review moderation</h1><p>Approve traveller reviews to publish them on the website.</p><?php $rows=db()->query('SELECT * FROM reviews ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC); if(!$rows) echo '<p class="empty-state">No reviews yet.</p>'; foreach($rows as $r): ?><article class="editor-panel"><p><?= $r['approved']?'Published':'Awaiting approval' ?> · <?= (int)$r['rating'] ?>/5</p><h3><?= e($r['name']) ?></h3><p><?= nl2br(e($r['body'])) ?></p><form method="post" class="inline-actions"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="button" name="action" value="<?= $r['approved']?'hide':'approve' ?>"><?= $r['approved']?'Hide review':'Approve review' ?></button><button class="button danger" name="action" value="delete" onclick="return confirm('Delete this review permanently?')">Delete review</button></form></article><?php endforeach ?>
<?php elseif($tab==='enquiries'): ?><h1>Travel enquiries</h1><?php $rows=db()->query('SELECT * FROM enquiries ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC); if(!$rows) echo '<p class="empty-state">New enquiries will appear here.</p>'; foreach($rows as $r): ?><article class="editor-panel"><p><?= e($r['created']) ?> · <?= $r['sent']?'Email delivered':'Saved; email not delivered' ?></p><h3><?= e($r['name']) ?></h3><p><a href="mailto:<?= e($r['email']) ?>"><?= e($r['email']) ?></a> · <?= e($r['phone']) ?></p><p><?= nl2br(e($r['message'])) ?></p></article><?php endforeach ?>
<?php elseif($tab==='subscribers'): ?><h1>Newsletter subscribers</h1><?php $rows=db()->query('SELECT * FROM subscribers ORDER BY created DESC')->fetchAll(PDO::FETCH_ASSOC); if(!$rows) echo '<p class="empty-state">No subscribers yet.</p>'; foreach($rows as $r): ?><p class="editor-panel"><?= e($r['email']) ?> · <?= e($r['created']) ?></p><?php endforeach ?><?php endif ?></main></div><?php endif ?><script src="assets/admin.js"></script></body></html>
