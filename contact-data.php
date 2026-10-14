<?php
function contact_link($service) {
 $pattern=match($service){'whatsapp'=>'%wa.me%','facebook'=>'%facebook.com%','instagram'=>'%instagram.com%',default=>''};
 $q=db()->prepare("SELECT value FROM site_fields WHERE section='contact' AND kind='link' AND label LIKE ? LIMIT 1");$q->execute([$pattern]);return $q->fetchColumn() ?: '#contact';
}
function contact_phone_link() {
 $q=db()->prepare("SELECT value FROM site_fields WHERE section='contact' AND kind='text' AND label='Detail 2' LIMIT 1");$q->execute();return 'tel:'.preg_replace('/[^0-9+]/','',$q->fetchColumn() ?: '+94754067529');
}
