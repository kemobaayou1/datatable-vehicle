<?php
$password = 'hakim'; // Replace 'admin' with the desired password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;


// INSERT INTO auth_users (username, password, role) 
// VALUES ('hakim', '$2y$10$g122cPuXRtyshwHUQEylmOsivvMQwNKxfUTSucqZuykSCz.LDPdR.', 'admin');
