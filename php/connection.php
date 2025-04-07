<?php
  $connect = mysqli_connect(
    'localhost',
    'root',
    '',
    'walmart_app'
  );

  if (!$connect) {
    echo 'Error Code: ' . mysqli_connect_errno();
    echo 'Error Message: ' . mysqli_connect_error();
    // die("Connection Failed:" . mysqli_connect_error());
    exit;
  }
?>
