<?php
$conn = mysqli_connect("localhost","root","","simkes_desa");

if(!$conn){
    die("Koneksi gagal : ".mysqli_connect_error());
}

?>