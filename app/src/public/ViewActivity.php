<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
  header("location: /");
  exit();
}
?>

<!DOCTYPE html>

<head>
  <title>Dashboard</title>
  <link href="/css/output.css" rel="stylesheet">
  <meta charset="UTF-8">
  <meta http-equiv="Content-Language" content="it">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
  <!-- Sidebar -->
  <object data="/view/SideBar?Title=Area%20riservata" width="100%" height="100%"></object>
  <!-- Contenuto principale -->
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
      <!-- Your content -->sdffasfdasfasd
    </div>
</body>