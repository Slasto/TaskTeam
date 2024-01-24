<!DOCTYPE html>

<head>
    <link href="./output.css" rel="stylesheet">
    <title>Registrazione</title>
</head>

<body>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-12 w-auto" src="./Logo.svg" alt="Your Company">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Registrazione</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="javascript:handleSubmit()" method="POST" onsubmit="return validateForm()">
                <div>
                    <label for="username" class="block text-sm font-medium leading-6 text-gray-900">Nome Utente</label>
                    <div class="mt-2">
                        <input id="username" name="username" type="text" autocomplete="username" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" autocomplete="email" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                    </div>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="new-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
                    </div>
                    <div class="mt-2">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-blue-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Sign in</button>
                </div>
            </form>
        </div>
    </div>
    <script>
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

            // Verifica la lunghezza della password
            if (password.length <= 12) {
                alert("La password deve avere più di 12 caratteri.");
                return false;
            }

            // Verifica che la password e la conferma della password siano uguali
            if (password !== confirmPassword) {
                alert("Le password non corrispondono.");
                return false;
            }

            // Verifica che l'email sia valida
            let re = /\S+@\S+\.\S+/;
            if (!re.test(email)) {
                alert("Devi inserire un indirizzo email valido.");
                return false;
            }
        }

        function handleSubmit() {
            fetch("/CreateAccount.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    username: username,
                    email: email,
                    password: password,
                    confirmPassword: confirmPassword
                })
            }).then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            }).then(data => {
                switch (data) {
                    case "Email":
                        alert("La email usata è gia reggistrata nel sistema");
                        break;
                    case "Created":
                        alert("Account creato, procedi con il login");
                        location.href = window.location.protocol + "//" + window.location.host
                        break;
                }
            }).catch(error => {
                console.error('error!', error);
            });
        }
    </script>
</body>