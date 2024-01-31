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
    header("location: /Team");
    exit();
};

// Esiste l'attivita + Prendo i dati di essa
$stmt = $pdo->prepare("SELECT * FROM Attivita WHERE ID=:id AND FK_TeamID = :TeamID");
$stmt->execute([
    "id" => $ActivityID,
    "TeamID" => $TeamID
]);

$activityData = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$activityData) { // esiste l'attivita?
    header("HTTP/1.0 400 Bad Request");
    header("location: /ViewActivity?TeamID=" . $TeamID);
    exit();
}
$activityData = $activityData[0];


$stmt = $pdo->prepare("SELECT FK_UsernameProprietario,Nome,CodiceInvito FROM Team WHERE ID = :tid");
$stmt->execute([
    "tid" => $TeamID
]);
$Team = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
$isPrivate = $Team["CodiceInvito"] === null;
if ($isProprietario = $Team["FK_UsernameProprietario"] === $_SESSION['username'] && $Team["CodiceInvito"] != null && !$isPrivate) {
    $stmt = $pdo->prepare("SELECT User.ID, User.Username FROM UserInTeam JOIN `User` ON UserInTeam.UserID = User.ID WHERE TeamID = :Tid ");
    $stmt->execute([
        "Tid" => $TeamID,
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
        let Title;
        let Description;
        let Stato;
        let Scadenza;

        function validateFrom() {
            Title = document.getElementById("titolo").value;
            Description = document.getElementById("Description").value;
            Stato = document.getElementById("stato").value;
            Scadenza = document.getElementById("expire").value;

            if (!/^[\w\s0-9]{1,255}$/.test(Title)) {
                alert("Nome Attivita non valido!\nNon tutti i caratteri speciali sono consentiti")
                return false;
            }

            re = /^[\u00C0-\u017Fa-zA-Z\s!@#$%^*\-_|0-9]{0,255}$/
            if (!re.test(Description)) {
                alert("Descrizione non valida non valido, non tutti i caratteri speciali sono consentiti");
                return false;
            }
            return true;
        }

        function handleSubmit() {
            //Creazione del body
            let params = new URLSearchParams();
            params.append("TeamID", <?php echo $TeamID ?>);
            params.append("AttivitaID", <?php echo $ActivityID ?>);
            params.append("Titolo", Title);
            params.append("Descrizione", Description);
            params.append("Stato", Stato);
            params.append("Scadenza", Scadenza);

            fetch("/method/Attivita/Edit.php", {
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

        function handleDelete() {
            if (!confirm("Sei sicuro?\nQuesta azione non sarà reversibile"))
                return;
            let TeamID = <?php echo $TeamID; ?>;
            let params = new URLSearchParams();
            params.append("TeamID", TeamID);
            params.append("AttivitaID", <?php echo $ActivityID; ?>)
            fetch("/method/Attivita/Delete.php", {
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
                        location.href = window.location.protocol + "//" + window.location.host + "/ViewActivity?TeamID=" + <?php echo $TeamID; ?>;
                        break;
                    default:
                    case "0": //frontend e backend fanno gli stessi controlli sui dati, questo caso non si dovrebbe mai verificare
                        alert("Errore durante l cancellazione del team, la invitiamo a riprovare più tradi");
                        break;
                }
            }).catch(error => {
                console.error('error!', error);
            });
        }
        <?php if (!$isProprietario) { ?>

            function handleModifyAssegnazione(switchTo) {
                let params = new URLSearchParams();
                params.append("TeamID", <?php echo $TeamID; ?>);
                params.append("AttivitaID", <?php echo $ActivityID; ?>)
                params.append("SwitchTo", switchTo);
                fetch("/method/Attivita/EditAssegnazione.php", {
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
                            break;
                        default:
                        case "0": //frontend e backend fanno gli stessi controlli sui dati, questo caso non si dovrebbe mai verificare
                            alert("Errore durante la assegnazione, la invitiamo a riprovare più tradi");
                            break;
                    }
                    location.reload()
                }).catch(error => {
                    console.error('error!', error);
                });
            }
        <?php } ?>
    </script>
    <!-- Sidebar -->
    <object data="/view/SideBar?Title=Dettaglio%20attivita" width="100%" height="100%"></object>

    <!-- Contenuto principale -->
    <form id="Form" action="javascript:handleSubmit()" onsubmit="return validateFrom()" class="mx-auto max-w-7xl py-8 sm:px-6 lg:px-8">
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
                    <input id="titolo" name="titolo" type="text" maxlength="255" value="<?php echo $activityData["Titolo"] ?>" required class="w-auto flex-auto bg-slate-50 rounded-md border-0 p-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                </div>
            </dl>
        </div>

        <!--Descrizione-->
        <div class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <div class="text-sm font-bold leading-6 text-gray-900">Descrizione:</div>
                <textarea id="Description" name="Description" spellcheck="false" maxlength="255" class="block bg-slate-50 h-36 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600"><?php echo $activityData["Descrizione"] ?></textarea>
            </div>
        </div>

        <!--Stato-->
        <div class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
            <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                <div id="statoLabel" class="text-sm font-bold leading-6 text-blue-800">Stato attivita:</div>
                <div>
                    <select id="stato" name="stato" class="w-40 flex-auto bg-slate-50 rounded-md border-0 p-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        <option value="Da fare" <?php echo ($activityData["Stato"] == "Da fare") ? "selected" : ""; ?>>Da fare</option>
                        <option value="In corso" <?php echo ($activityData["Stato"] == "In corso") ? "selected" : ""; ?>>In corso</option>
                        <option value="Fatto" <?php echo ($activityData["Stato"] == "Fatto") ? "selected" : ""; ?>>Fatto</option>
                    </select>
                </div>

            </div>
        </div>

        <!--Scadenza + data completamento e da chi-->
        <div class="px-4 py-6 border-t sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
            <div class="text-sm font-bold leading-6 text-red-800">Dati sulla scadenza</div>
            <div class="flex">
                <input id="expire" value="<?php echo $activityData["Scadenza"] ?>" type="date" min="<?php echo (new DateTime('now', new DateTimeZone('Europe/Rome')))->format('Y-m-d'); ?>" class="w-40 size-fit mt-auto mr-4 space-y-6 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600" />
                <?php if ($activityData["Stato"] === 'Fatto') { ?>
                    <div>
                        <label for="done" class="space-y-6 text-sm font-medium leading-6 text-gray-900 flex items-center justify-between">Fatto in data</label>
                        <input id="done" disabled value="<?php echo $activityData["FattoIl"] ?>" type="done" min="<?php echo $activityData["FattoIl"]  ?>" class="block space-y-6 sm:text-sm sm:leading-6 rounded-md w-40 p-1.5 text-gray-900 shadow-sm" />
                    </div>
                <?php } ?>
            </div>
        </div>
        <!--Assegnazione-->
        <?php if (!$isPrivate) { ?>
            <div class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <div>
                        <div class="text-sm font-bold leading-6 text-gray-900">Assegnazione attivita</div>
                        <div class="text-sm font-light">(nd. questo campo è aggiornato dinamicamente)</div>
                    </div>
                    <?php if ($isProprietario) { ?>
                        <!--- Sta visualizzando il capo-->
                        <select id="Associazione" name="Associazione" selected="<?php echo $activityData['Assegnato'] ?>" class="block w-full bg-slate-50 h-10 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                            <option value="">-</option>
                            <?php foreach ($UserInTeam as $User) { ?>
                                <option value="<?php echo $User['ID']; ?>" <?php echo ($activityData["Assegnato"] == $User['Username'] ? "selected" : "") ?>><?php echo $User['Username']; ?></option>
                            <?php } ?>
                        </select>
                        <script type>
                            document.getElementById("Associazione").onchange = changeListener;

                            function changeListener() {
                                let params = new URLSearchParams();
                                params.append("TeamID", <?php echo $TeamID; ?>);
                                params.append("AttivitaID", <?php echo $ActivityID; ?>)
                                params.append("SwitchTo", 1);
                                params.append("Admin", document.getElementById("Associazione").value);
                                fetch("/method/Attivita/EditAssegnazione.php", {
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
                                            break;
                                        default:
                                        case "0": //frontend e backend fanno gli stessi controlli sui dati, questo caso non si dovrebbe mai verificare
                                            alert("Errore durante l assegnazione, la invitiamo a riprovare più tradi");
                                            break;
                                    }
                                    location.reload()
                                }).catch(error => {
                                    console.error('error!', error);
                                });
                            }
                        </script>
                    <?php } elseif ($activityData["Assegnato"] === $_SESSION['username']) { ?>
                        <!--- Sta visualizzando l'utente in questione-->
                        <div class="flex">
                            <div class="block w-40 bg-slate-50 h-10 sm:text-sm sm:leading-6 rounded-md p-1.5 text-gray-900 shadow-sm">Assegnato a te</div>
                            <?php if ($activityData["Stato"] !== "Fatto") { ?>
                                <button type="button" onclick='handleModifyAssegnazione(-1)' class="flex-auto ml-4 justify-center rounded-md bg-yellow-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-yellow-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Non lo posso più fare io</button>
                            <?php } ?>
                        </div>
                    <?php } elseif ($activityData["Assegnato"] === '' || $activityData["Assegnato"] === null) { ?>
                        <!--- Sta visualizzando un altro utente-->
                        <div class="flex">
                            <div class="block w-40 bg-slate-50 h-10 sm:text-sm sm:leading-6 rounded-md p-1.5 text-gray-900 shadow-sm">-</div>
                            <?php if ($activityData["Stato"] !== "Fatto") { ?>
                                <button type="button" onclick="handleModifyAssegnazione(1)" class="flex-auto ml-4 justify-center rounded-md bg-green-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Lo faccio io</button>
                            <?php } ?>
                        </div>
                    <?php } else { ?>
                        <div class="block w-40 bg-slate-50 h-10 sm:text-sm sm:leading-6 rounded-md p-1.5 text-gray-900 shadow-sm"><?php echo $activityData["Assegnato"] ?></div>
                    <?php } ?>
                </div>
            <?php } ?>
            <!-- Form button --->
            <div class="px-4 pt-2 sm:gap-4 sm:px-0 border-t divide-gray-100">
                <button type="submit" class="flex-auto justify-center mt-2 rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Modifica dati del attivita</button>
                <button onclick="handleDelete()" type="button" class="flex-auto justify-center rounded-md bg-red-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Elimina attivita</button>
            </div>
    </form>
</body>