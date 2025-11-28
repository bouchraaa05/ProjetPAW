<?php
// === Pour afficher les erreurs (utile pendant le test) ===
error_reporting(E_ALL);
ini_set("display_errors", 1);

// === Vérifier si le formulaire a été envoyé ===
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = $_POST["studentID"] ?? "";
    $lastname = $_POST["lastname"] ?? "";
    $firstname = $_POST["firstname"] ?? "";
    $email = $_POST["email"] ?? "";

    // === Vérification minimale ===
    if (empty($id) || empty($lastname) || empty($firstname) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "Missing fields"]);
        exit;
    }

    // === Charger le fichier JSON ===
    $file = "students.json";

    if (!file_exists($file)) {
        file_put_contents($file, "[]");
    }

    $data = json_decode(file_get_contents($file), true);

    if (!is_array($data)) {
        $data = [];
    }

    // Vérifier si l’étudiant existe déjà
    foreach ($data as $student) {
        if ($student["id"] == $id) {
            echo json_encode(["status" => "error", "message" => "Student already exists"]);
            exit;
        }
    }

    // === Ajouter le nouvel étudiant ===
    $data[] = [
        "id" => $id,
        "lastname" => $lastname,
        "firstname" => $firstname,
        "email" => $email
    ];

    // === Sauvegarder dans students.json ===
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));

    echo json_encode(["status" => "success"]);
    exit;
}
?>
