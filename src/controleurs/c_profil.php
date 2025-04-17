<?php  
require_once 'src/config/twig.php';
require_once 'src/models/class.pdo.inc.php';

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté avec la bonne variable de session
if (!isset($_SESSION['idfidele'])) {
    header('Location: index.php?uc=connexion'); // Redirige vers connexion et non profil
    exit();
}

// Connexion à la base de données
$pdo = PdoChoule::getPdoChoule();
$user = $pdo->getUserById($_SESSION['idfidele']); // Vérifie que cette fonction existe et récupère bien les infos
$idFidele = $_SESSION['idfidele'];

$action = $_GET['action'] ?? 'view';

if ($action === 'edit') {
    $user = $pdo->getUserById($idFidele);
    echo $twig->render('v_edit_profil.twig', ['user' => $user]);
} elseif ($action === 'update') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $adresse = filter_input(INPUT_POST, 'adresse', FILTER_SANITIZE_SPECIAL_CHARS);
    $code_postal = filter_input(INPUT_POST, 'code_postal', FILTER_SANITIZE_NUMBER_INT);
    $ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_SPECIAL_CHARS);
    $pays = filter_input(INPUT_POST, 'pays', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($name && $prenom && $email && $adresse && $code_postal && $ville && $pays) {
        $pdo->updateUser($idFidele, $name, $prenom, $email, $adresse, $code_postal, $ville, $pays);
        header('Location: index.php?uc=profil&success=1');
        exit();
    } else {
        header('Location: index.php?uc=profil&action=edit&error=1');
        exit();
    }
} else {
    $user = $pdo->getUserById($idFidele);
    echo $twig->render('v_profil.twig', ['user' => $user]);
}


