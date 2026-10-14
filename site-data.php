<?php
function section_document($html) {
 $doc=new DOMDocument('1.0','UTF-8');
 $previous=libxml_use_internal_errors(true); $doc->loadHTML('<?xml encoding="UTF-8"><div id="site-fragment">'.$html.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD); libxml_clear_errors();libxml_use_internal_errors($previous);
 return $doc;
}
function initialize_site_fields() {
 db()->exec('CREATE TABLE IF NOT EXISTS site_fields (id INTEGER PRIMARY KEY, section TEXT NOT NULL, target TEXT NOT NULL, kind TEXT NOT NULL, label TEXT NOT NULL, value TEXT NOT NULL, UNIQUE(section,target,kind))');
 if((int)db()->query('SELECT COUNT(*) FROM site_fields')->fetchColumn()>0) return;
 $q=db()->prepare('INSERT OR IGNORE INTO site_fields(section,target,kind,label,value) VALUES(?,?,?,?,?)');
 foreach(db()->query('SELECT * FROM content') as $row) {
  $doc=section_document($row['body']); $xpath=new DOMXPath($doc); $count=[];
  foreach($xpath->query('//text()[normalize-space(.) != ""]') as $node) {
   $parent=$node->parentNode; if(!$parent instanceof DOMElement) continue;
   if($xpath->query('ancestor::form|ancestor::script|ancestor::style|ancestor::*[contains(@class,"scroll-down-indicator")]', $node)->length) continue;
   $tag=$parent->tagName; if(!in_array($tag,['h1','h2','h3','h4','p','li','span','strong','a'])) continue;
   $value=trim($node->nodeValue); if($value==='') continue;
   $label=match($tag) {'h1'=>'Main heading','h2'=>'Section title','h3'=>'Name / heading','h4'=>'Feature name','strong'=>'Price','p'=>'Description','li'=>'Detail','a'=>'Button or link text','span'=>'Label',default=>'Text'};
   $count[$label]=($count[$label]??0)+1; $label.=' '.$count[$label];
      $kind='text';
   if($tag==='strong' && $xpath->query('ancestor::*[contains(@class,"price-tier")]', $node)->length) {
    $kind='money';$tier=$xpath->query('ancestor::*[contains(@class,"price-tier")]', $node)->item(0);$people=$xpath->query('.//span',$tier)->item(0)?->textContent??'';$label=$people.' price (USD)';$value=preg_replace('/[^0-9.]/','',$value);
   }
   $q->execute([$row['name'],$node->getNodePath(),$kind,$label,$value]);
  }
  foreach($xpath->query('//img|//iframe|//div[contains(@class,"hero-slider")]|//a[@href]') as $node) {
   if($node->tagName==='img') { $kind='image';$value=$node->getAttribute('src');$label='Photo: '.($node->getAttribute('alt') ?: 'image'); }
   elseif($node->tagName==='iframe') { $kind='map';$value=$node->getAttribute('src');$label='Map embed URL'; }
   elseif($node->tagName==='a') { if($node->getAttribute('href')==='#'||$xpath->query('ancestor::*[contains(@class,"scroll-down-indicator")]', $node)->length) continue; $kind='link';$value=$node->getAttribute('href');$label='Link: '.(trim($node->textContent)?:str_replace('https://','',$value)); }
   else { $kind='background';preg_match('~url\([\x27\x22]?(.*?)[\x27\x22]?\)~',$node->getAttribute('style'),$m);$value=$m[1]??'';$label='Cover photo'; }
   $q->execute([$row['name'],$node->getNodePath(),$kind,$label,$value]);
   if($kind==='image') $q->execute([$row['name'],$node->getNodePath(),'alt','Photo description',$node->getAttribute('alt')]);
  }
 }
}
function render_site_data($section,$html) {
 $doc=section_document($html);$xpath=new DOMXPath($doc);
 $q=db()->prepare('SELECT * FROM site_fields WHERE section=? ORDER BY id');$q->execute([$section]);
 foreach($q as $field) { $node=$xpath->query($field['target'])->item(0); if(!$node) continue;
  if(in_array($field['kind'],['text','money'])) $node->nodeValue=$field['kind']==='money'?'$'.number_format((float)$field['value'],fmod((float)$field['value'],1.0)==0.0?0:2):$field['value'];
  elseif($field['kind']==='background') $node->setAttribute('style',"background-image: url('".$field['value']."');");
  else $node->setAttribute(match($field['kind']) {'image'=>'src','alt'=>'alt','map'=>'src','link'=>'href'},$field['value']);
 }
 $container=$xpath->query('//*[@id="site-fragment"]')->item(0);$out='';foreach($container->childNodes as $child) $out.=$doc->saveHTML($child);return $out;
}
function safe_site_url($value,$kind) {
 if($value==='' || preg_match('/[\x00-\x20\x7f\x22\x27\\\\<>]/',$value)) return false;
 if($kind==='link' && preg_match('~^(#[a-zA-Z0-9_-]+|mailto:[^\s]+@[^\s]+|tel:\+?[0-9-]+)$~',$value)) return true;
 if(preg_match('~^assets/[a-zA-Z0-9_./-]+$~',$value) && !str_contains($value,'..')) return true;
 if(!filter_var($value,FILTER_VALIDATE_URL) || parse_url($value,PHP_URL_SCHEME)!=='https') return false;
 if($kind==='map' && !in_array(parse_url($value,PHP_URL_HOST),['www.google.com','maps.google.com'])) return false;
 return true;
}
function save_site_data() {
 $section=$_POST['section']??''; $q=db()->prepare('SELECT * FROM site_fields WHERE section=?');$q->execute([$section]);$fields=$q->fetchAll(PDO::FETCH_ASSOC);
 if(!$fields) throw new RuntimeException('Choose a valid section.');
 $changes=[]; $newFiles=[];
 try { foreach($fields as $field) {
  $value=trim((string)($_POST['field'][$field['id']]??$field['value']));
  if(strlen($value)>10000) throw new RuntimeException('A field is too long. Please use fewer than 10,000 characters.');
  $file=$_FILES['photo']['error'][$field['id']]??UPLOAD_ERR_NO_FILE;
  if(in_array($field['kind'],['image','background']) && $file!==UPLOAD_ERR_NO_FILE) {
   if($file!==UPLOAD_ERR_OK || ($_FILES['photo']['size'][$field['id']]??0)>5*1024*1024) throw new RuntimeException('Upload a photo smaller than 5 MB.');
   $tmp=$_FILES['photo']['tmp_name'][$field['id']];$mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);$extensions=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
   if(!isset($extensions[$mime])||!getimagesize($tmp)) throw new RuntimeException('Choose a JPG, PNG or WebP photo.');
   if(!is_dir(__DIR__.'/assets/uploads')) mkdir(__DIR__.'/assets/uploads',0755,true);
   $value='assets/uploads/'.bin2hex(random_bytes(16)).'.'.$extensions[$mime];
   if(!move_uploaded_file($tmp,__DIR__.'/'.$value)) throw new RuntimeException('The photo could not be saved.');$newFiles[]=__DIR__.'/'.$value;
  }
  if(in_array($field['kind'],['image','background','map','link']) && !safe_site_url($value,$field['kind'])) throw new RuntimeException('Use a valid HTTPS link or upload a photo. Map links must be Google Maps embed URLs.');
  if($field['kind']==='money' && (!is_numeric($value)||$value<0||$value>1000000)) throw new RuntimeException('Enter a valid package price.');
  $changes[]=[$value,$field['id']];
 }
 db()->beginTransaction(); $q=db()->prepare('UPDATE site_fields SET value=? WHERE id=?');foreach($changes as $change)$q->execute($change);db()->commit();
 } catch(Throwable $error) { if(db()->inTransaction())db()->rollBack();foreach($newFiles as $file)if(is_file($file))unlink($file);throw $error; }
}
initialize_site_fields();
