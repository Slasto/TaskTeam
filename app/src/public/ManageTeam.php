<?php
session_start();
if (!isset($_SESSION["logged_in"]) || !isset($_GET["TeamID"]) || !is_numeric($_GET["TeamID"])) {
    header("location: /Team");
    exit();
}
$TeamID = intval($_GET["TeamID"]);



require_once "../private/Database.php";
$stmt = $pdo->prepare("SELECT * FROM Team WHERE ID = :TeamID AND  FK_UsernameProprietario = :Username AND Nome!=\"Privato\"");
$stmt->execute([
    "TeamID" => $_GET["TeamID"],
    "Username" => $_SESSION["username"]

]);
$TeamData = ($stmt->fetchAll(PDO::FETCH_ASSOC));

// Il è team riservato al solo utente e appartiene a chi ha fatto la richiesta?
if (!$TeamData) {
    header("location: /Team");
    exit();
}
$TeamData = $TeamData[0];

$stmt = $pdo->prepare("SELECT User.ID, User.Username FROM UserInTeam JOIN `User` ON UserInTeam.UserID = User.ID WHERE TeamID = :Tid AND User.ID != :Uid");
$stmt->execute([
    "Tid" => $TeamID,
    "Uid" => $_SESSION["user_id"]

]);

$UserInTeam = $stmt->fetchAll(PDO::FETCH_ASSOC);

//Allora puoi gestire il team...
unset($pdo);
unset($stmt);
?>

<!DOCTYPE html>

<head>
    <title>Gestione Team</title>
    <link href="/css/output.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="it">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="bg-gray-100">
    <script type="text/javascript">
        let TeamName;
        let Description;

        //Validazione form
        function validateFrom() {
            TeamName = document.getElementById("Nome").value;
            Description = document.getElementById("Description").value;

            if (!/^[\w\s!@#$%^*_|]{1,32}$/.test(TeamName)) {
                alert("Nome team non valido!\nDeve essere di massimo 12 caratteri e non tutti i caratteri speciali sono consentiti")
                return false;
            }
            return true;
        }

        function handleSubmitTeam() {
            //Creazione del body
            let params = new URLSearchParams();
            params.append("TeamID", <?php echo $TeamData["ID"] ?>)
            params.append("TeamName", TeamName)
            params.append("TeamDescription", Description)
            fetch("/method/Team/ModifyTeam.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: params
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            }).then(data => {
                switch (data) {
                    case "1":
                        alert("Modifiche avvenute con successo");
                        break;
                    default:
                    case "0": //frontend e backend fanno gli stessi controlli sui dati, questo caso non si dovrebbe mai verificare
                        alert("Errore! dati inseriti non validi");
                        break;
                    case "-1":
                        alert("Errore! non si dispone del autorizzazione necessaria per modificare i dati di questo team");
                        break;
                }
            }).catch(error => {
                console.error('error!', error);
            });
        }

        function handleDeleteTeam() {
            if (!confirm("Sei sicuro?\nQuesta azione non sarà reversibile"))
                return;

            let intID = <?php echo $TeamID ?>

            let params = new URLSearchParams();
            params.append("TeamID", intID)
            fetch("/method/Team/Delete.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: params
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            }).then(data => {
                switch (data) {
                    case "1":
                        alert("Cancellazione avvenuta con successo");
                        location.href = window.location.protocol + "//" + window.location.host + "/Team";
                        break;
                    default:
                    case "0": //frontend e backend fanno gli stessi controlli sui dati, questo caso non si dovrebbe mai verificare
                        alert("Errore durante la cancellazione del team, la invitiamo a riprovare più tradi");
                        break;
                }
            }).catch(error => {
                console.error('error!', error);
            });
        }

        function deleteUser(Username) {
            
        }
    </script>
    <!-- Sidebar -->
    <object data="/view/SideBar?Title=Team" width="100%" height="100%"></object>

    <!-- Contenuto principale -->
    <form id="FormTeam" action="javascript:handleSubmitTeam()" method="POST" onsubmit="return validateFrom()" class="mx-auto max-w-7xl py-8 sm:px-6 lg:px-8">
        <!--sezione iniziale-->
        <div class="px-4 sm:px-0">
            <h3 class="text-base font-semibold leading-7 text-gray-900">Gestione Team</h3>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Da qui è possibile modificare alcuni dati relativo al tuo team specificato.</p>
        </div>

        <!--Nome Team-->
        <div class="mt-6 border-t border-gray-100">
            <dl class="divide-y divide-gray-100">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-bold leading-6 text-gray-900">Nome Team:</dt>
                    <input id="Nome" name="Nome" type="text" maxlength="32" value="<?php echo $TeamData["Nome"] ?>" required class="block flex-auto bg-slate-50 rounded-md border-0 p-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                </div>
            </dl>
        </div>

        <!--Descrizione-->
        <div class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <div class="text-sm font-bold leading-6 text-gray-900">Descrizione:</div>
                <textarea id="Description" name="Description" spellcheck="false" maxlength="255" class="block s-full bg-slate-50 h-36 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600"><?php echo $TeamData["Descrizione"] ?></textarea>
            </div>
        </div>
        <div class="px-4 pt-2 sm:gap-4 sm:px-0 border-t divide-gray-100">
            <button type="submit" class="flex-auto justify-center mt-2 rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Aggiorna dati team</button>
            <button onclick="handleDeleteTeam()" type="button" class="flex-auto justify-center rounded-md bg-red-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Elimina Team</button>
        </div>
    </form>

    <!-- Lista partecipanti-->
    <div class="mx-auto max-w-7xl py-8 sm:px-6 lg:px-8">
        <div class="px-4 sm:px-0">
            <h3 class="text-base font-semibold leading-7 text-gray-900">Elenco membri del Team</h3>
            <?php if ($UserInTeam) { ?>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Da qui è possibile rimuovere gli altri utenti dal tuo team</p>
            <?php } else { ?>
                <div class="mt-1 max-w-2xl text-sm leading-6 text-red-800">In questo Team non ci sono ancora altri utenti!</div>
            <?php } ?>
        </div>

        <div class="max-w-sm bg-slate-50 my-10 rounded-md">
            <?php foreach ($UserInTeam as $user) { ?>
                <!-- Elemento lista partecipanti-->
                <ul class="divide-y rounded-md divide-gray-200">
                    <li class="p-3 flex justify-between items-center user-card">
                        <div class="flex items-center">
                            <img class="w-10 h-10 rounded-full bg-blue-00" src="/icon/profile-pic.png" alt="User">
                            <span class="ml-3 font-medium"><?php echo $user["Username"] ?></span>
                        </div>
                        <div>
                            <button class="rounded-md hover:border-collapse shadow-sm border p-0.5 hover:bg-red-300" onclick="deleteUser(<?php echo $user["Username"] ?>)">
                                <img class="size-7 bg-blue-00" src="/icon/Remove-user.svg" alt="Elimina">
                            </button>
                        </div>
                    </li>
                </ul>
            <?php } ?>
        </div>
    </div>

</body>