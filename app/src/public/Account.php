<?php
session_start();
if (!isset($_SESSION["logged_in"])) {
    header("location: /");
    exit();
}

require_once "../private/Database.php";
$stmt = $pdo->prepare("SELECT Email FROM User WHERE Username = :username AND ID = :id");
$stmt->execute([
    "username" => $_SESSION["username"],
    "id" => $_SESSION["user_id"]
]);
$userProfile = ($stmt->fetchAll(PDO::FETCH_ASSOC))[0];
unset($pdo);
unset($stmt);

if (!$userProfile) { //array è vuoto
    session_destroy();
    header("location: /");
}
?>

<!DOCTYPE html>

<head>
    <title>Dashboard</title>
    <!--<script src="https://cdn.tailwindcss.com"></script>-->
    <link href="/css/output.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="it">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <script type="text/javascript">
        let NewEmail;
        let password;
        //Validazione form
        function validateEmail() {
            // Ottieni i email dal form
            NewEmail = document.getElementById('email').value;
            let OldEmail = "<?php echo $userProfile["Email"] ?>"

            if (NewEmail === OldEmail) {
                alert("Non hai modificato la mail");
                return false
            }

            // Verifica che l email sia valida
            let re = /^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}){1,50}$/;
            if (!re.test(NewEmail)) {
                alert("Indirizzo email non valido");
                return false; //
            }
            return true;
        }

        function validatePassword() {
            // Ottieni i email dal form
            password = document.getElementById('password').value;
            let confirm_password = document.getElementById('confirm_password').value;


            let re = /^[\w\s!@#$%^&*?.]{12,255}$/
            if (!re.test(password)) {
                alert("La lunghezza della password deve essere tra 12 e 255 caratteri\nNon deve contenere caratteri accentati\nI caratteri speciali consentiti !@#$%^&*?.");
                return false;
            }

            if (password !== confirm_password) {
                alert("Le 2 password non corrispondono");
                return false
            }
            return true;
        }

        function handleSubmit(What) {
            //Creazione del body
            let params = new URLSearchParams();
            switch (What) {
                case "Email":
                    params.append("email", NewEmail);
                    break;
                case "Password":
                    params.append("password", password);
                    break;
                default: //mh...
                    return;
                    break;
            }

            fetch("/method/User/ModifyProfile.php?change=".concat(What), {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: params
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            }).then(data => {
                //let anw = JSON.parse(data);
                switch (data.stato) {
                    case 0:
                        alert(data.messaggio);
                        break;
                    case 1:
                        alert(data.messaggio);
                        if (What === "Password") {
                            document.getElementById("password").value = "";
                            document.getElementById("confirm_password").value = "";
                        }
                        break;
                }
            }).catch(error => {
                console.error('error!', error);
            });
        }

        function handleDeleteAccount() {
            let boolConfirm = confirm("Sei sicuro?\nQuesta azione non è reversibile e cancellera tutti i team di cui sei proprietario");
            if (!boolConfirm)
                return;

            fetch("/method/User/Delete.php")
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                }).then(body => {
                    if (body === '1') {
                        alert("Account eliminato con successo");
                        location.href = window.location.protocol + "//" + window.location.host + "/logout.php";
                    } else if (body === '0')
                        alert("Errore durante l'elimination, non si dispone del autorizzazione necessaria");
                }).catch(error => {
                    console.error('error!', error);
                });
        }
    </script>
    <!-- Sidebar -->
    <object data="/view/SideBar?Title=Account" width="100%" height="100%"></object>
    <!-- Contenuto principale -->
    <main>
        <div class="mx-auto max-w-7xl py-8 sm:px-6 lg:px-8">
            <!--sezione iniziale-->
            <div class="px-4 sm:px-0">
                <h3 class="text-base font-semibold leading-7 text-gray-900">Profilo personale</h3>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-gray-500">Da qui è possibile visualizzare e modificare alcuni dati del profilo.</p>
            </div>
            <!--Username-->
            <div class="mt-6 border-t border-gray-100">
                <dl class="divide-y divide-gray-100">
                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                        <dt class="text-sm font-bold leading-6 text-gray-900">Username:</dt>
                        <dd class="mt-3 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?php echo $_SESSION["username"] ?></dd>
                    </div>
                </dl>
            </div>

            <!--Email-->
            <form class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Email')" method="POST" onsubmit="return validateEmail()">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <div class="text-sm font-bold leading-6 text-gray-900">Email:</div>
                    <input id="email" name="email" type="email" autocomplete="email" value="<?php echo $userProfile["Email"] ?>" required class="block flex-auto rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                </div>
                <button type="submit" class="flex-auto justify-center mt-2 rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Cambia email</button>
            </form>


            <!--Password-->
            <form class="mt-6 border-t divide-y divide-gray-100" action="javascript:handleSubmit('Password')" method="POST" onsubmit="return validatePassword()">
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <div class="text-sm font-bold leading-6 text-gray-900">Cambia password:</div>
                    <!--Password 1-->
                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-700">Nuova password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" autocomplete="new-password" required class="block flex-auto rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    <!--Password 2-->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-700">Conferma Password</label>
                        <div class="mt-2">
                            <input id="confirm_password" name="confirm_password" type="password" autocomplete="current-password" required class="block flex-auto rounded-md border-0 py-1.5 text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                    <!--Password Confirm button-->
                    <div>
                        <button type="submit" class="flex-auto justify-center mt-2 rounded-md bg-blue-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Cambia password</button>
                    </div>
                </div>
            </form>
            <div class="px-4 pt-2 sm:gap-4 sm:px-0 border-t divide-gray-100">
                <button onclick="handleDeleteAccount()" class="flex-auto justify-center rounded-md bg-red-800 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-red-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Elimina account</button>
            </div>
        </div>
    </main>
</body>