<!DOCTYPE html>

<head>
    <link href="/css/output.css" rel="stylesheet">
    <title>Registrazione</title>
</head>

<body>
    <script type="text/javascript">
        let usernameM;
        let email;
        let password;
        let confirmPassword;
        //Validazione form
        function validateForm() {
            // Ottieni i valori dei campi del form
            username = document.getElementById('username').value;
            email = document.getElementById('email').value;
            password = document.getElementById('password').value;
            confirmPassword = document.getElementById('confirm_password').value;

            // Controlla se i campi sono vuoti
            if (username === '' || email === '' || password === '' || confirmPassword === '') {
                alert("Tutti i campi devono essere riempiti.");
                return false;
            }

            // Verifica che l'email sia valida
            let re = /^([a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}){1,50}$/;
            if (!re.test(email)) {
                alert("Indirizzo email non valido");
                return false;
            }

            // Verifica che l'Username non contenga caratteri speciali

            re = /^[a-zA-Z ]{1,32}$/;
            if (!re.test(username)) {
                alert("L'Username non può: contenere caratteri speciali e accentati, ed essere più lungo di 32 caratteri");
                return false;
            }

            // Verifica che la password e la conferma della password siano uguali
            if (password !== confirmPassword) {
                alert("Le password non corrispondono.");
                return false;
            }
            // Verifica che la password e valida
            re = /^[\w\s!@#$%^&*?.]{12,255}$/
            if (!re.test(password)) {
                alert("La lunghezza della password deve essere tra 12 e 255 caratteri\n Non deve contenere caratteri accentati\nI caratteri speciali consentiti !@#$%^&*?.");
                return false;
            }
            return true;
        }

        function handleSubmit() {
            let params = new URLSearchParams();
            params.append("username", username);
            params.append("email", email);
            params.append("password", password);
            fetch("/api/User/Create.php", {
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
                //console.log('Data:', data);
                //let anw = JSON.parse(data);
                switch (data.stato) {
                    case 0:
                        alert(data.messaggio);
                        break;
                    case 1:
                        alert("Account creato, puoi ora effettuare il login");
                        location.href = window.location.protocol + "//" + window.location.host
                        break;
                }
            }).catch(error => {
                console.error('error!', error);
            });
        }
    </script>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-12 w-auto" src="./favicon.ico" alt="Your Company">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Registrazione</h2>
        </div>
        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="javascript:handleSubmit()" onsubmit="return validateForm()">
                <div>
                    <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Username</label>
                    <div class="mt-2">
                        <input id="username" name="username" type="text" autocomplete="username" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>

                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Conferma Password</label>
                    <div class="mt-2">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-blue-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Sign in</button>
                </div>
                <p class="mt-10 text-center text-sm text-gray-500">
                    Già registrato?
                    <a href="Index" class="font-semibold leading-6 text-blue-400 hover:text-blue-600">Accedi da qui!</a>
            </form>
        </div>
    </div>
</body>