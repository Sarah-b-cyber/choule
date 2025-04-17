<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'src/config/twig.php';
require_once 'src/models/class.pdo.inc.php';

// Vérification de la session
if (!isset($_SESSION['idfidele'])) {
    header('Location: index.php?uc=connexion');
    exit();
}

// Initialisation des variables
$message = '';
$error = '';
$success = false;

// Récupération des données POST (nom et prénom)
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);

// Validation des données
if (!empty($name) && !empty($prenom)) {
    // Connexion à la base de données via la classe PdoChoule
    $pdoChoule = PdoChoule::getPdoChoule();

    // Appel de la méthode pour ajouter un donateur (seulement nom et prénom)
    $success = $pdoChoule->inscrireDonateur($name, $prenom); // Inscrire un donateur avec seulement le nom et prénom
}

// Si l'ajout du donateur est un succès
if ($success) {
    $message = "Donateur ajouté avec succès.";
    echo $twig->render('v_newdonateur.twig', ['message' => $message]);
} else {
    // Si une erreur survient lors de l'ajout
    echo $twig->render('v_newdonateur.twig', ['error' => $error]);
}

exit();
?>
