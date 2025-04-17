<?php 
require_once 'src/config/twig.php';
require_once 'src/models/class.pdo.inc.php';
require_once 'src/models/ftc.inc.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader('src/vues'));

$action = filter_input(INPUT_GET, 'action', FILTER_DEFAULT) ?? 'demandeConnexion';

// Définition du chemin pour la connexion
$path_connexion = 'index.php?uc=connexion&action=valideConnexion';

switch ($action) {
    case 'demandeConnexion':
        echo $twig->render('v_connexion.twig', [
            'path_connexion' => $path_connexion
        ]);
        break;

    case 'valideConnexion':
        $login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
        $mdp = filter_input(INPUT_POST, 'mdp', FILTER_DEFAULT);
   
        
        $erreurs = [];

        if (!$login || !$mdp) {
            $erreurs[] = 'Veuillez remplir tous les champs';
        } else {
            $pdo = PdoChoule::getPdoChoule();
            $utilisateur = $pdo->getInfosFidele($login);



              // Vérifie si l'utilisateur existe et si le mot de passe est correct
              if (!$utilisateur || !password_verify($mdp, $utilisateur['password'])) {
                $erreurs[] = 'Login ou mot de passe incorrect';
            } else {
                // Connexion réussie
                unset($utilisateur['password']); // Sécurité : ne pas stocker le hash en session
                connecter($utilisateur['id'], $utilisateur['name'], $utilisateur['prenom'], $utilisateur['role']); 
                // Redirection selon le rôle
                if ($utilisateur['role'] === 'admin') {
                    header('Location: index.php?uc=admin');
                } else {
                    header('Location: index.php?uc=accueilfid');
                }
                exit();
            }
        }

        echo $twig->render('v_connexion.twig', [
            'erreurs' => $erreurs,
            'path_connexion' => $path_connexion
        ]);
        break;

    default:
        echo $twig->render('v_connexion.twig', [
            'path_connexion' => $path_connexion
        ]);
        break;
}