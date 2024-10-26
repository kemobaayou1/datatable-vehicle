<?php
$password = 'Khgahmedsaid'; // Replace 'admin' with the desired password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo $hashed_password;


// INSERT INTO auth_users (username, password, role) 
// VALUES ('ahmedsaid', '$2y$10$Es.Q2zdtlWCf0ieMhlB52.hPFXknXOFjCXqanWqXA09XrGpXXzxEO', 'admin');

//password:Khgahmedsaid username: ahmedsaid