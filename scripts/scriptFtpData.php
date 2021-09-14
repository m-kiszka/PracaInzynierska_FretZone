<?php
//FTP DANE
$ftp_server = "52.233.172.209";
$ftp_conn = ftp_connect($ftp_server,21) or die("Błąd połączenia z FTP.");
$ftp_username="fz_root";
$ftp_userpass="Git@ry2020!!";
$ftp_login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
ftp_pasv($ftp_conn, true)
//FTP DANE
?>
