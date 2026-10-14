<?php
// Fixed admin account. Override these values with environment variables in production.
return [
 'admin_user' => getenv('TRAVENZO_ADMIN_USER') ?: 'admin',
 'admin_password_hash' => getenv('TRAVENZO_ADMIN_HASH') ?: password_hash('Travenzo@2026!', PASSWORD_DEFAULT),
 'email' => 'Travenzotravels@gmail.com',
 'smtp_password' => getenv('TRAVENZO_GMAIL_APP_PASSWORD') ?: 'zmgk epuo numg tmuq',
];
