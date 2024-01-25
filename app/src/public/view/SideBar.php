<!DOCTYPE html>
<html class="h-full bg-gray-100">

<head>
  <!--<script src="https://cdn.tailwindcss.com"></script>-->
  <link href="../css/output.css" rel="stylesheet">
</head>

<body class="h-full">
  <div class="min-h-full">
    <nav class="bg-gray-800">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">

            <div class="flex-shrink-0">
              <img class="h-8 w-8" src="../favicon.ico" alt="Your Company">
            </div>

            <div class="md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
                <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Area riservata</a>
                <a href="#" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Team</a>
                <a href="../Account" class="text-gray-300 hover:bg-gray-700 hover:text-white rounded-md px-3 py-2 text-sm font-medium">Profilo</a>
              </div>
            </div>

            <!--Menu sinistro--->
          </div>
          <div class="ml-4 flex items-center md:ml-6">
            <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!--White Bar-->
    <header class="bg-white shadow">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 id="WhiteboardTxt" class="text-3xl font-bold tracking-tight text-gray-900">PlaceHolder</h1>
      </div>
    </header>
  </div>
  <script>
    var params = new URLSearchParams(window.location.search);
    var embeddedBy = params.get('Title');
    document.getElementById("WhiteboardTxt").innerText = embeddedBy;
  </script>
</body>

</html>