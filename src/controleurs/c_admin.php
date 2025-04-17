<?php
require_once 'src/config/twig.php';
require_once 'src/models/class.pdo.inc.php';
require_once 'src/models/ftc.inc.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie que l'utilisateur est administrateur
if (!isset($_SESSION['role'])) {
    header('Location: index.php?uc=connexion');
    exit();
}

// Rediriger les administrateurs vers la vue "v_admin"
if ($_SESSION['role'] === 'admin') {
    echo $twig->render('v_admin.twig');  // Vue spécifique pour les administrateurs
    exit();
}

$pdo = PdoChoule::getPdoChoule();
$action = $_GET['action'] ?? 'view';

$idFidele = $_SESSION['idfidele'];

// Récupérer les informations du fidèle
$fidele = $pdo->getInfosFidele($login, $mdp); // Fonction à ajouter dans `class.pdo.inc.php`

// Afficher la vue accueilfid.twig (utilisateur non admin)
echo $twig->render('accueilfid.twig', [
    'fidele' => $fidele
]);
