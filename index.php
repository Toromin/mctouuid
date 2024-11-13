<?php
function getUserData($username) {
    $url = "https://api.mojang.com/users/profiles/minecraft/" . $username;
    $response = file_get_contents($url);
    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernames = preg_split('/[\s,]+/', $_POST['usernames']);
    $existingJson = json_decode($_POST['existingJson'], true);
    $results = is_array($existingJson) ? $existingJson : [];

    foreach ($usernames as $username) {
        $username = trim($username);
        $data = getUserData($username);
        if ($data) {
            $results[] = [
                'uuid' => $data['id'],
                'name' => $data['name']
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Mojang mc to uuid</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 10px;
            color: #555;
        }
        textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        #results {
            margin-top: 20px;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        #copyButton {
            margin-top: 10px;
            background-color: #28a745;
        }
        #copyButton:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>mctouuid</h1>
        <form method="post">
            <label for="usernames">Benutzernamen (durch Leerzeichen, Zeilenumbrüche oder Kommas getrennt):</label>
            <textarea id="usernames" name="usernames" rows="4" cols="50" required></textarea>
            <label for="existingJson">Bestehende JSON-Liste:</label>
            <textarea id="existingJson" name="existingJson" rows="4" cols="50"></textarea>
            <br>
            <button type="submit">Senden</button>
        </form>

        <?php if (!empty($results)): ?>
            <div id="results">
                <h2>Ergebnisse</h2>
                <pre id="jsonResults"><?php echo json_encode($results, JSON_PRETTY_PRINT); ?></pre>
                <button id="copyButton" onclick="copyToClipboard()">Ergebnisse kopieren</button>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("jsonResults").innerText;
            navigator.clipboard.writeText(copyText).then(function() {
                alert("Ergebnisse wurden kopiert!");
            }, function(err) {
                alert("Fehler beim Kopieren der Ergebnisse: ", err);
            });
        }
    </script>
</body>
</html>
