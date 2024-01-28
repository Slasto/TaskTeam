<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("location: /");
    exit();
}

require_once "../private/Database.php";
$stmt = $pdo->prepare("SELECT TeamID, Nome, Descrizione, CodiceInvito, FK_UsernameProprietario FROM `UserInTeam` JOIN `User` ON UserInTeam.UserID = User.ID JOIN `Team` ON UserInTeam.TeamID = Team.ID WHERE Username = :username AND Nome != \"Privato\"");
$stmt->execute([
    "username" => $_SESSION["username"],
]);

$userTeam = ($stmt->fetchAll(PDO::FETCH_ASSOC));
unset($pdo);
unset($stmt);
?>

<!DOCTYPE html>

<head>
    <title>Team</title>
    <link href="/css/output.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="it">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body class="bg-gray-100">
    <object data="/view/SideBar?Title=Team" width="100%" height="100%"></object>
    <main class="mx-auto max-w-7xl py-8 sm:px-6 lg:px-8">
        <!--sezione iniziale-->
        <div class="px-4 sm:px-0">
            <h3 class="text-base font-semibold leading-7 text-gray-900">I tuoi team</h3>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Da qui è puoi visualizzare l'elenco dei tuoi team, gestirli ed aprire la sezione per visualizzare i relativi Todo.<br> Puoi anche creare ed unirti ad altri team</p>
        </div>
        <!-- INIZIO tabella elenco Team INIZIO --->
        <script type="text/javascript">
            function DeleteFromTeam(intID) {

                if (!confirm("Sei sicuro di uscire dal Team?"))
                    return;
                let params = new URLSearchParams();
                params.append("TeamID", intID);
                fetch("/method/Team/RemoveUser.php", {
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
                                alert("Errore l'eliminazione dal Team...");
                                break;
                            case "1":
                                location.href = window.location.protocol + "//" + window.location.host + "/Team";
                                break;
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
            }

            function ManageFromTeam(intID) {
                //il controllo dei permessi è fatto stesso in /ManageTeam.php
                location.href = window.location.protocol + "//" + window.location.host + "/ManageTeam?TeamID=" + intID;
            }

            function OpenTeam(intID) {
                //il controllo dei permessi è fatto stesso in /ViewActivity.php
                location.href = window.location.protocol + "//" + window.location.host + "/ViewActivity?TeamID=" + intID;
            }
        </script>
        <div class="flex my-4 border-t border-gray-200 bg-slate-50">
            <table class="w-full border-collapse rounded-lg shadow-md align-middle">
                <thead>
                    <tr>
                        <th class="border pt flex-auto font-bold">Nome Team</th>
                        <th class="border px-4 py-2 flex-auto font-bold">Descrizione</th>
                        <th class="border px-4 py-2 flex-auto font-bold">Proprietario</th>
                        <th class="border px-4 py-2 w-48 font-bold">Codice invito</th>
                        <th class="border px-4 py-2 w-0 font-bold">Gestione Team</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userTeam as $team) { ?>
                        <!-- Inizio riga -->
                        <tr class="hover:bg-gray-100 focus:bg-gray-300 active:bg-grey-200" tabindex="0">
                            <td class="border px-4 py-2"><?php echo $team["Nome"]; ?></td>
                            <td class="border px-4 py-2"><?php echo $team["Descrizione"]; ?></td>
                            <td class="border px-4 py-2"><?php echo $team["FK_UsernameProprietario"]; ?></td>
                            <td class="border px-4 py-2"><?php echo $team["CodiceInvito"]; ?></td>
                            <td class="border px-4 py-2">
                                <div class="flex space-x-4">
                                    <button onclick="OpenTeam(<?php echo $team["TeamID"]; ?>)" class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-600 focus:outline-none">Apri</button>
                                    <?php if ($team["FK_UsernameProprietario"] === $_SESSION["username"]) { ?>
                                        <button onclick="ManageFromTeam(<?php echo $team["TeamID"]; ?>)" class="px-4 py-2 bg-red-700 text-white rounded hover:bg-red-600 focus:outline-none">Gestisci</button>
                                    <?php } else { ?>
                                        <button onclick="DeleteFromTeam(<?php echo $team["TeamID"]; ?>)" class="px-7 py-2 bg-red-700 text-white rounded mg hover:bg-red-600 focus:outline-none">Esci</button>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                        <!-- Fine riga -->
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <!-- FINE tabella elenco Team FINE --->
        <div class="px-4 pt-5 sm:gap-4 sm:px-0 divide-gray-100">
            <button onclick="showCreateDialog()" class="flex-auto justify-center rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Crea un Team</button>
            <button onclick="showTeamDialog()" class="flex-auto justify-center rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Entra in un Team</button>
        </div>
    </main>


    <!--    INIZIO Dialog per aggiunta ad un gruppo INIZIO   -->
    <script type="text/javascript">
        function showTeamDialog() {
            document.getElementById("TeamDialog").style.display = "block";
        }

        function hideTeamDialog() {
            document.getElementById("TeamDialog").style.display = "none";
            document.getElementById("joinTeamCode").value = ""
        }

        let Code

        function validateTeamCode() {
            Code = document.getElementById("joinTeamCode").value;

            let re = /^[a-zA-Z0-9!@#$%^*_|]{12}$/;
            if (!re.test(Code)) {
                alert("codice non valido");
                return false;
            }
            return true;
        }

        function handleTeamSubmit() {
            let params = new URLSearchParams();
            params.append("TeamCode", Code);

            fetch("/method/Team/addUser.php", {
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
                            alert("Il team non esiste");
                            break;
                        case "1":
                            location.href = window.location.protocol + "//" + window.location.host + "/Team";
                            break;
                        case "2":
                            alert("Sei gia un membro del team!");
                            break;
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        }
    </script>
    <div id="TeamDialog" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <!--Image goes here-->
                                <div class="mx-auto flex h-13 w-13 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <img class="h-8 w-8" src="/icon/Team-add.svg" alt="Team">
                                </div>
                                <!--Text zone-->
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-base font-semibold leading-6 text-gray-900">Entra in un team!</h3>
                                    <form id="formTeam" class="mt-2" action="javascript:handleTeamSubmit()" onsubmit="return validateTeamCode()">
                                        <!--Text box name-->
                                        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                                            <label for="joinTeamCode" class=" space-y-6 text-sm font-medium leading-6 text-gray-900 flex items-center justify-between">Codice
                                                invito</label>
                                            <input id="joinTeamCode" name="joinTeamCode" type="text" required class="block w-full space-y-6 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                                        </div>

                                    </form>

                                </div>
                            </div>
                        </div>
                        <!--Bottom zone with button-->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" form="formTeam" class="inline-flex w-full justify-center rounded-md bg-blue-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 sm:ml-3 sm:w-auto">Entra</button>
                            <button type="button" onclick="hideTeamDialog()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--    FINE Dialog per aggiunta ad un gruppo FINE   -->


    <!--    INIZIO Dialog per la creazione ad un gruppo INIZIO   -->
    <script type="text/javascript">
        function showCreateDialog() {
            document.getElementById("CreateDialog").style.display = "block";
        }

        function hideCreateDialog() {
            document.getElementById("CreateDialog").style.display = "none";
            document.getElementById("NewTeamName").value = ""
            document.getElementById("Description").value = ""
        }

        let Name;
        let Description;

        function validateCreateForm() {
            Name = document.getElementById("NewTeamName").value;
            Description = document.getElementById("Description").value;

            let re = /^[\w\s!@#$%^*_|]{1,32}$/;
            if (!re.test(Name)) {
                alert("Nome non valido");
                return false;
            }

            return true;
        }


        function handleCreateSubmit() {
            let params = new URLSearchParams();
            params.append("TeamName", Name);
            if (Description.replace("\n", '').trim() !== "") {
                params.append("Description", Description);
            }


            fetch("/method/Team/Create.php", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params
                })
                .then(response => response.text())
                .then(data => {
                    switch (data.toString()) {
                        case "-1":
                            alert("Non è stato possibile creare un Team");
                            break;
                        default:
                            location.href = window.location.protocol + "//" + window.location.host + "/Team";
                            break;
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
        }
    </script>
    <div id="CreateDialog" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
        <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <!--Image goes here-->
                                <div class="mx-auto flex h-13 w-13 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <img class="h-8 w-8" src="/icon/Team-add.svg" alt="Team">
                                </div>
                                <!--Text zone-->
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h1 class="text-base font-semibold leading-6 text-gray-900" id="modal-title">Crea un
                                        nuovo Team</h1>
                                    <form id="formTeamCreate" class="mt-2" action="javascript:handleCreateSubmit()" onsubmit="return validateCreateForm()">
                                        <!--Text box name-->
                                        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
                                            <label for="NewTeamName" class="block space-y-6 text-sm font-medium leading-6 text-gray-900  items-center justify-between">Nome
                                                team</label>
                                            <input id="NewTeamName" name="NewTeamName" maxlength="32" type="text" required class="block w-full space-y-6 sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600">
                                        </div>
                                        <!--Text box description-->
                                        <div class="sm:mx-auto sm:w-full sm:max-w-sm mt-3">
                                            <label for="Description" class="block space-y-6 text-sm font-medium leading-6 text-gray-900 ms-center justify-between">Breve
                                                descrizione (opzionale)</label>
                                            <textarea id="Description" name="Description" spellcheck="false" maxlength="255" class="block s-full  sm:text-sm sm:leading-6 rounded-md border-0 p-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600"></textarea>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!--Bottom zone with button-->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" form="formTeamCreate" class="inline-flex w-full justify-center rounded-md bg-blue-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-600 sm:ml-3 sm:w-auto">Crea</button>
                            <button type="button" onclick="hideCreateDialog()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--    FINE Dialog per la creazione ad un gruppo FINE   -->
</body>