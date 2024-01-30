<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("location: /");
    exit();
}

if (!isset($_GET['TeamID']) || !isset($_GET['ActivityID'])) {
    header("HTTP/1.0 400 Bad Request");
    header("location: /Team");
    exit();
}

$TeamID = $_GET["TeamID"];
$ActivityID = $_GET["ActivityID"];

if (!is_numeric($TeamID) || !is_numeric($ActivityID)) {
    header("HTTP/1.0 400 Bad Request");
    header("location: /Team");
    exit();
};
$TeamID = intval($TeamID);
$ActivityID = intval($ActivityID);


require_once "../private/Database.php";

//è l'utente membro del team?
$stmt = $pdo->prepare("SELECT ID FROM UserInTeam WHERE UserID = :userId AND TeamID = :tid");
$stmt->execute([
    "userId" => $_SESSION["user_id"],
    "tid" => $TeamID
]);

if (!($stmt->fetchAll(PDO::FETCH_ASSOC))) {
    header("HTTP/1.0 403 Forbidden");
    header("location: /Team");
    exit();
};

// Esiste l'attivita + Prendo i dati di essa
$stmt = $pdo->prepare("SELECT * FROM Attivita WHERE ID=:id AND FK_TeamID = :TeamID");
$stmt->execute([
    "id" => $ActivityID,
    "TeamID" => $TeamID
]);

$activityData = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
if (!$activityData) { // esiste l'attivita?
    header("HTTP/1.0 400 Bad Request");
    header("location: /ViewActivity?TeamID=" . $TeamID);
    exit();
}


$stmt = $pdo->prepare("SELECT FK_UsernameProprietario,Nome FROM Team WHERE ID = :tid");
$stmt->execute([
    "tid" => $TeamID
]);
$Team = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

if ($Team["FK_UsernameProprietario"] === $_SESSION['username']) {
    $stmt = $pdo->prepare("SELECT User.ID, User.Username FROM UserInTeam JOIN `User` ON UserInTeam.UserID = User.ID WHERE TeamID = :Tid AND User.ID != :Uid");
    $stmt->execute([
        "Tid" => $TeamID,
        "Uid" => $_SESSION["user_id"]
    ]);
    $UserInTeam = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

unset($pdo);
unset($stmt);
?>

<!DOCTYPE html>

<head>
    <title>Attivita team: <?php echo $Team["Nome"] ?></title>
    <link href="/css/output.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="it">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="bg-gray-100">
    <script type="text/javascript">
        let TeamName;
        let Description;

        //TODO: Input Sanitization
        //Validazione form
        function validateFrom() {
            TeamName = document.getElementById("Nome").value;
            Description = document.getElementById("Description").value;

            if (!/^[\w\s!@#$%^*\-_|]{1,32}$/.test(TeamName)) {
                alert("Nome team non valido!\nNon tutti i caratteri speciali sono consentiti")
                return false;
            }


            re = /^[\u00C0-\u017Fa-zA-Z\s!@#$%^*\-_|0-9]{0,255}$/
            if (!re.test(Description)) {
                alert("Descrizione non valida non valido, Non tutti i caratteri speciali sono consentiti");
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
            fetch("/method/Team/Edit.php", {
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
            if (!confirm("Sei sicuro?"))
                return;

            let params = new URLSearchParams();
            params.append("From", "<?php echo $TeamID; ?>");
            params.append("User", Username);

            fetch("/method/Team/ForceRemoveUser.php", {
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
                        location.reload();
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
    </script>
    <!-- Sidebar -->
    <object data="/view/SideBar?Title=Dettaglio%20attivita" width="100%" height="100%"></object>

    <!-- Contenuto principale -->
    <form id="Form" action="javascript:handleSubmitTeam()" method="POST" onsubmit="return validateFrom()" class="mx-auto max-w-7xl py-8 sm:px-6 lg:px-8">
        <!--sezione iniziale-->
        <div class="px-4 sm:px-0">
            <h3 class="text-base font-semibold leading-7 text-gray-900">Gestione Attivita</h3>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Da qui è possibile visualizzare e modificare tutti i campi relativi ad un attivita.</p>
        </div>

        <!--Titolo attivita-->
        <div class="mt-6 border-t border-gray-100">
            <dl class="divide-y divide-gray-100">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-bold leading-6 text-gray-900">Titolo:</dt>
                    <input id="titolo" name="titolo" type="text" maxlength="255" value="<?php echo $activityData["Titolo"] ?>" required class="w-auto flex-auto bg-slate-50 rounded-md border-0 p-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                </div>
            </dl>
        </div>

        <!--Descrizione-->
        <div class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <div class="text-sm font-bold leading-6 text-gray-900">Descrizione:</div>
                <textarea id="Description" name="Description" spellcheck="false" maxlength="255" class="block s-full bg-slate-50 h-36 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600"><?php echo $activityData["Descrizione"] ?></textarea>
            </div>
        </div>

        <!--Scadenza + data completamento e da chi-->
        <div class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <div class="text-sm font-bold leading-6 text-gray-900">Dati sulla scadenza</div>
                <textarea id="Description" name="Description" spellcheck="false" maxlength="255" class="block s-full bg-slate-50 h-36 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600"><?php echo $activityData["Descrizione"] ?></textarea>
            </div>

        <!--Assegnazione-->
            <div class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <div class="text-sm font-bold leading-6 text-gray-900">Assegnazione attivita</div>
                <textarea id="Description" name="Description" spellcheck="false" maxlength="255" class="block s-full bg-slate-50 h-36 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600"><?php echo $activityData["Descrizione"] ?></textarea>
            </div>

        <div class="px-4 pt-2 sm:gap-4 sm:px-0 border-t divide-gray-100">
                <button type="submit" class="flex-auto justify-center mt-2 rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Aggiorna dati team</button>
                <button onclick="handleDeleteTeam()" type="button" class="flex-auto justify-center rounded-md bg-red-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Elimina Team</button>
            </div>
    </form>
</body>