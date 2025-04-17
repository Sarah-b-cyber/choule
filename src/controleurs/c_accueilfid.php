<?php
require_once 'src/config/twig.php';
require_once 'src/models/class.pdo.inc.php';
require_once 'src/models/ftc.inc.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
$mdp = filter_input(INPUT_POST, 'mdp', FILTER_DEFAULT);
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['idfidele'])) {
    header('Location: index.php?uc=connexion');
    exit();
}

$pdo = PdoChoule::getPdoChoule();
$idFidele = $_SESSION['idfidele'];

// Récupérer les informations du fidèle
$fidele = $pdo->getInfosFidele($login, $mdp); // Fonction à ajouter dans `class.pdo.inc.php`

// Afficher la vue accueilfid.twig
echo $twig->render('accueilfid.twig', [
    'fidele' => $fidele
]);
