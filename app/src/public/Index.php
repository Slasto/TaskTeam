<?php
session_start();

if (isset($_SESSION["logged_in"])) {
    header("location: /home");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $userID = validateCredentials($username, $password);

    if ($userID > 0) {
        $_SESSION["username"] = $username;
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $userID;
        header("Location: Home");
        exit();
    } else {
        $err = "Username o password non validi";
    }
}

function validateCredentials($username, $password)
{
    require_once "../private/Database.php";
    $stmt = $pdo->prepare('SELECT ID,HashPW FROM User WHERE Username = :username');
    $stmt->execute([':username' => $username]);
    $userData = ($stmt->fetchAll(PDO::FETCH_ASSOC));
    unset($pdo);
    unset($stmt);

    if (!$userData) //array è vuoto
        return -1;
    $userData = $userData[0];
    if (!password_verify($password, $userData["HashPW"])) //hash non corrisponde
        return -1;

    return $userData["ID"];
}

/*TODO: implementare login sicuro:
    Username->
  <-Random,Salt
    hash(Random,hash(Password,Salt)->
*/
?>
<!DOCTYPE html>

<head>
    <title>Login</title>
    <link href="./css/output.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta http-equiv="Content-Language" content="it">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img class="mx-auto h-12" src="./favicon.ico" alt="Your Company">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Accedi al tuo account</h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="/" method="POST">
                <div>
                    <label for="username" class="text-sm font-medium leading-6 text-gray-900 flex items-center justify-between">Username</label>
                    <div class="mt-2">
                        <input id="username" name="username" type="text" maxlength="32" autocomplete="username" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="password" class="text-sm font-medium leading-6 text-gray-900 flex items-center justify-between">Password</label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit" class="flex w-full justify-center rounded-md bg-blue-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-blue-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">Sign in</button>
                </div>
            </form>

            <?php if (isset($err)) { ?>
                <br>
                <div class="flex bg-red-100 rounded-lg p-4 mb-4 text-sm text-red-700" role="alert">
                    <svg class="w-5 h-5 inline mr-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <span class="font-medium">Errore!</span> <?php echo $err; ?>
                    </div>
                </div>
            <?php } ?>
            <p class="mt-10 text-center text-sm text-gray-500">
                Non sei registrato?
                <a href="Register" class="font-semibold leading-6 text-blue-400 hover:text-blue-600">Puoi farlo da qui!</a>
            </p>

        </div>
    </div>
</body>