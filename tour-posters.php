<?php
function upload_tour_poster($field='poster') {
 $file=$_FILES[$field]??null;
 if(!$file || ($file['error']??UPLOAD_ERR_NO_FILE)===UPLOAD_ERR_NO_FILE) return '';
 if(!is_scalar($file['error']) || $file['error']!==UPLOAD_ERR_OK || $file['size']>5*1024*1024) throw new RuntimeException('Choose a poster smaller than 5 MB.');
 $tmp=$file['tmp_name'];$mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);$types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
 if(!isset($types[$mime]) || !getimagesize($tmp)) throw new RuntimeException('Choose a JPG, PNG or WebP itinerary poster.');
 $folder=__DIR__.'/assets/uploads';if(!is_dir($folder))mkdir($folder,0755,true);
 $path='assets/uploads/poster-'.bin2hex(random_bytes(16)).'.'.$types[$mime];
 if(!move_uploaded_file($tmp,__DIR__.'/'.$path)) throw new RuntimeException('The poster could not be saved. Please try again.');
 return $path;
}
function cleanup_tour_poster($path) {
 if(!preg_match('~^assets/uploads/poster-[a-f0-9]{32}\.(jpg|png|webp)$~',$path))return;
 $q=db()->prepare('SELECT (SELECT COUNT(*) FROM tours WHERE poster=?) + (SELECT COUNT(*) FROM site_fields WHERE value=?)');$q->execute([$path,$path]);
 if(!$q->fetchColumn() && is_file(__DIR__.'/'.$path)) @unlink(__DIR__.'/'.$path);
}
function update_tour_poster() {
 $id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT);
 if($id===false||$id<1)throw new RuntimeException('Choose a valid tour plan.');
 $q=db()->prepare('SELECT poster FROM tours WHERE id=?');$q->execute([$id]);$old=$q->fetchColumn();
 if($old===false)throw new RuntimeException('This tour plan no longer exists.');
 $remove=($_POST['poster_operation']??'')==='remove';$new=$remove?'':upload_tour_poster();
 if(!$remove && !$new)throw new RuntimeException('Choose an itinerary poster, then click Save poster.');
 try { $q=db()->prepare('UPDATE tours SET poster=? WHERE id=?');$q->execute([$new,$id]); }
 catch(Throwable $ex) { if($new)cleanup_tour_poster($new);throw $ex; }
 if($old!==$new)cleanup_tour_poster($old);
 return $remove?'Itinerary poster removed.':'Itinerary poster saved.';
}
