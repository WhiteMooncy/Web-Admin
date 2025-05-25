<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../src/css/Style.css">
    <title>Login</title>
</head>
<body>
    <main>
        <div class="container">
        <h2>Iniciar Sesión</h2>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Usuario" required><br>
            <input type="password" name="password" placeholder="Contraseña" required><br>
            <button type="submit">Entrar</button>
        </form>
    </div>
    </main>
</body>
</html>