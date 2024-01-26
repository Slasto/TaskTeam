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
  <!--<script src="https://cdn.tailwindcss.com"></script>-->
  <link href="/css/output.css" rel="stylesheet">
</head>

<body>
  <!-- Sidebar -->
  <object data="/view/SideBar?Title=Area%20riservata" width="100%" height="100%"></object>
  <!-- Contenuto principale -->
  <main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
      <!-- Your content -->sdffasfdasfasd
    </div>
  </main>
</body>