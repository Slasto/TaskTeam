<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
  header("location: /");
  exit();
}

if (!isset($_GET["TeamID"])) {
  header("location: /Team");
  exit();
}

$TeamID = $_GET["TeamID"];
if (!is_numeric($TeamID)) {
  header("location: /Team");
  exit();
}
$TeamID = intval($TeamID);



require_once "../private/Database.php";

//è l'utente nel team?
$stmt = $pdo->prepare("SELECT ID FROM UserInTeam WHERE TeamID = :TeamID AND UserID = :UserID");
$stmt->execute([
  "TeamID" => $TeamID,
  "UserID" => $_SESSION["user_id"]
]);

if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
  header("location: /Team");
  exit();
}

//Get delle attivita
$stmt = $pdo->prepare("SELECT * FROM Attivita WHERE FK_TeamID = :TeamID");
$stmt->execute([
  "TeamID" => $TeamID
]);
$ListaAttivita = $stmt->fetchAll(PDO::FETCH_ASSOC);


//Get del proprietario
$stmt = $pdo->prepare("SELECT Nome,Descrizione,FK_UsernameProprietario FROM Team WHERE ID = :TeamID");
$stmt->execute([
  "TeamID" => $TeamID
]);
$TeamData = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$isAdmin = $TeamData["FK_UsernameProprietario"] == $_SESSION["username"];

function cmp($a, $b)
{
  if ($a["Stato"] > $b["Stato"]) {
    return 1;
  }
  return 0;
}

usort($ListaAttivita, function ($a, $b) {
  $order = array('Da fare', 'In corso', 'Fatto');
  $aIndex = array_search($a["Stato"], $order);
  $bIndex = array_search($b["Stato"], $order);
  if ($aIndex > $bIndex) {
    return 1;
  } elseif ($aIndex < $bIndex) {
    return -1;
  } else {
    return 0;
  }
});
$i = 0;
?>

<!DOCTYPE html>

<head>
  <title><?php echo $TeamData["Nome"] ?></title>
  <link href="/css/output.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <meta charset="UTF-8">
  <meta http-equiv="Content-Language" content="it">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="bg-gray-100">
  <!-- Sidebar -->
  <object data="/view/SideBar?Title=Attivita" width="100%" height="100%"></object>

  <!-- Contenuto principale -->
  <div class="mx-auto max-w-7xl p-6 sm:px-6 lg:px-8 mb-2">
    <div class="px-4 sm:px-0">
      <h1 class="text-lg font-semibold leading-7 text-gray-900"><?php echo $TeamData["Nome"] ?></h1>
      <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500"><?php echo $TeamData["Descrizione"] ?></p>
    </div>
    <button type="button" onclick="showDialog()" class="flex-auto justify-center mt-2 rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mb-4">Nuova attivita</button>

    <!-- 3 colonne Attività --->
    <div class="flex border rounded-lg py-3 shadow-sm">
      <!-- Colonna 1 -->
      <div class="flex-auto mx-3 border rounded-lg bg-white shadow-md">
        <div class="p-4">
          <p class="font-bold">Da fare:</p>
        </div>
        <?php for (; $i < sizeof($ListaAttivita); $i++) {
          if ($ListaAttivita[$i]['Stato'] !== 'Da fare')
            break; ?>
          <div class="p-3 flex mx-1.5 font-medium justify-between items-center border-b">
            <div>
              <span class="text-lg"><?php echo $ListaAttivita[$i]['Titolo'] . PHP_EOL; ?></span>
              <div class="border-t-2 border-dashed"> Scadenza: <?php echo $ListaAttivita[$i]["Scadenza"] ? $ListaAttivita[$i]["Scadenza"] : "non assegnata" ?></div>
              <div> Assegnato a: <?php echo $ListaAttivita[$i]["Assegnato"] ? $ListaAttivita[$i]["Assegnato"] : "non assegnata" ?></div>
            </div>
            <button class="rounded-md hover:border-collapse shadow-md border p-0.5 hover:bg-slate-200" onclick="ModifyDetails()">
              <img class="size-7 bg-blue-00" src="/icon/Modify.svg" alt="mod">
            </button>
          </div>
        <?php } ?>
      </div>

      <!-- Colonna 2 -->
      <div class="flex-auto mx-3 border rounded-lg bg-white shadow-md">
        <div class=" p-4">
          <p class="font-bold">In corso:</p>
        </div>
        <?php for (; $i < sizeof($ListaAttivita); $i++) {
          if ($ListaAttivita[$i]['Stato'] !== 'In corso') {
            break;
          } ?>
          <div class="p-3 flex mx-1.5 font-medium justify-between items-center border-b">
            <div>
              <span class="text-lg"><?php echo $ListaAttivita[$i]['Titolo'] . PHP_EOL; ?></span>
              <div class="border-t-2 border-dashed"> Scadenza: <?php echo $ListaAttivita[$i]["Scadenza"] ? $ListaAttivita[$i]["Scadenza"] : "non assegnata" ?></div>
              <div> Assegnato a: <?php echo $ListaAttivita[$i]["Assegnato"] ? $ListaAttivita[$i]["Assegnato"] : "non assegnata" ?></div>
            </div>
            <button class="rounded-md hover:border-collapse shadow-md border p-0.5 hover:bg-slate-200" onclick="ModifyDetails()">
              <img class="size-7 bg-blue-00" src="/icon/Modify.svg" alt="mod">
            </button>
          </div>
        <?php } ?>
      </div>

      <!-- Colonna 3 -->
      <div class="flex-auto font-medium mx-3 border rounded-lg bg-white shadow-md">
        <div class="p-4">
          <p class="font-bold">Fatto:</p>
        </div class="justify-between">
        <?php for (; $i < sizeof($ListaAttivita); $i++) {
          if ($ListaAttivita[$i]['Stato'] !== 'Fatto')
            break; ?>
          <div class="p-3 flex mx-1.5 font-medium justify-between items-center border-b">
            <div>
              <span class="text-lg"><?php echo $ListaAttivita[$i]['Titolo'] . PHP_EOL; ?></span>
              <div class="border-t-2 border-dashed"> Scadenza: <?php echo $ListaAttivita[$i]["Scadenza"] ? $ListaAttivita[$i]["Scadenza"] : "non assegnata" ?></div>
              <div> Assegnato a: <?php echo $ListaAttivita[$i]["Assegnato"] ? $ListaAttivita[$i]["Assegnato"] : "non assegnata" ?></div>
            </div>
            <button class="rounded-md hover:border-collapse shadow-md border p-0.5 hover:bg-slate-200" onclick="ModifyDetails()">
              <img class="size-7 bg-blue-00" src="/icon/Modify.svg" alt="mod">
            </button>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    function showDialog() {
      document.getElementById("createDialog").style.display = "block";
    }

    function hideDialog() {
      document.getElementById("createDialog").style.display = "none";
      document.getElementById("nomeAttivita").value = "";
      document.getElementById("Description").value = "";
      document.getElementById("Date").value = ""; //Data gia validata nel campo
    }

    let titolo;
    let descrizione;
    let data;

    function validateActivityForm() {
      titolo = document.getElementById("nomeAttivita").value;
      descrizione = document.getElementById("Description").value;
      data = document.getElementById("Date").value; //Data gia validata nel campo

      //controllo titolo
      if (!/^[\w\s0-9]{1,32}$/.test(titolo))
        return false;

      //controllo descrizione
      let re = /^[\u00C0-\u017Fa-zA-Z\s.,?!-_]$/;
      if (!re.test(descrizione))
        return false;


      return true;
    }

    function handleSubmit() {
      let params = new URLSearchParams();
      params.append("Titolo", titolo);
      params.append("Descrizione", descrizione);
      params.append("Data", data);
      params.append("TeamID", <?php echo $TeamID ?>);

      fetch("/method/Attivita/Create.php", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: params
        })
        .then(response => response.text())
        .then(data => {
          switch (data.toString()) {
            default:
            case "0":
              alert("Errore! i dati potrebbero essere scorretti");
              break;
            case "1":
              location.reload();
              break;
            case "-1":
              break;
          }
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    }
  </script>
  <div id="createDialog" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
    <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
      <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
          <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
              <div class="sm:flex sm:items-start">
                <!--Image goes here-->
                <div class="mx-auto flex h-13 w-13 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                  <img class="h-8 w-8" src="/icon/Activity.svg" alt="Team">
                </div>
                <!--Text zone-->
                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                  <h3 class="text-base font-semibold leading-6 text-gray-900">Creazione rapida attivita</h3>
                  <form id="formCreate" class="mt-2" action="javascript:handleSubmit()" onsubmit="validateActivityForm()">

                    <!--Nome-->
                    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                      <label for="nomeAttivita" class="space-y-6 text-sm font-medium leading-6 text-gray-900 flex items-center justify-between">Nome</label>
                      <input id="nomeAttivita" name="nomeAttivita" type="text" maxlength="32" required class="block w-full space-y-6 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                    </div>

                    <!--Data-->
                    <div class="sm:mx-auto sm:w-full sm:max-w-sm mt-2">
                      <label for="Date" class="space-y-6 text-sm font-medium leading-6 text-gray-900 flex items-center justify-between">Scadenza</label>
                      <input id="Date" type="date" min="<?php echo (new DateTime('now', new DateTimeZone('Europe/Rome')))->format('Y-m-d'); ?>" class="block w-full space-y-6 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600" />
                    </div>

                    <!--Descrizione-->
                    <div class="sm:mx-auto sm:w-full sm:max-w-sm mt-3">
                      <label for="Description" class="block space-y-6 text-sm font-medium leading-6 text-gray-900 ms-center justify-between">Descrizione (opzionale)</label>
                      <textarea id="Description" name="Description" spellcheck="false" maxlength="255" class="block s-full  sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600"></textarea>
                    </div>

                  </form>
                </div>
              </div>
            </div>
            <!--Bottom zone with button-->
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
              <button type="submit" form="formCreate" class="inline-flex w-full justify-center rounded-md bg-blue-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 sm:ml-3 sm:w-auto">Crea</button>
              <button type="button" onclick="hideDialog()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>





</body>