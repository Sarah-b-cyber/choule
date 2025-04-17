<?php
// Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php'; // Charger Stripe
require_once 'src/models/class.pdo.inc.php';
require_once 'src/config/twig.php'; 
$twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader('src/vues'));

// Charger la configuration Stripe
$config = require 'src/config/stripe.php';
\Stripe\Stripe::setApiKey($config['secret_key']);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['idfidele'])) {
    header('Location: index.php');
    exit();
}

// Récupération de l'action demandée
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'afficher';

switch ($action) {
    case 'afficher':
        try {
            $pdo = PdoChoule::getPdoChoule();
            $idFidele = $_SESSION['idfidele'];
            $promesses = $pdo->getPromessesDons($idFidele);
        } catch (PDOException $e) {
            die("Erreur lors de la récupération des dons : " . $e->getMessage());
        }

        echo $twig->render('v_dons.twig', [
            'promesses' => $promesses
        ]);
        break;

    case 'nouveau':
        echo $twig->render('v_formulaire.twig');
        break;


    

    case 'paiement':
            // Vérifier si les paramètres sont bien définis
            $montant = filter_input(INPUT_GET, 'montant', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $idDon = filter_input(INPUT_GET, 'id_don', FILTER_VALIDATE_INT);
        
            if ($montant && $idDon) {
                // Si les paramètres sont valides, afficher la page de choix de paiement
                echo $twig->render('v_paiement.twig', [
                    'montant' => $montant,
                    'id_don' => $idDon
                ]);
            } else {
                
                // Si les paramètres ne sont pas valides, rediriger vers l'accueil
                header('Location: index.php');
                exit();
            }
            break;

            case 'traiter':
                $pdo = PdoChoule::getPdoChoule(); // Assurez-vous que l'instance PDO est créée avant l'utilisation
                $userId = $_SESSION['idfidele'] ?? null;
            
                // Sanitize inputs avec des vérifications sécurisées
                $amount = filter_input(INPUT_POST, 'montant', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                $date = !empty($_POST['date']) ? filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS) : date('Y-m-d H:i:s');  // Défaut à la date actuelle
                $paymentMethod = filter_input(INPUT_POST, 'methode', FILTER_SANITIZE_SPECIAL_CHARS);
                $mitsva = filter_input(INPUT_POST, 'mitsva', FILTER_SANITIZE_SPECIAL_CHARS);
                $raison = filter_input(INPUT_POST, 'raison', FILTER_SANITIZE_SPECIAL_CHARS);
                $status = "en cours"; // Exemple d'ajout de statut pour garder un champ cohérent dans la base
            
                $erreurs = [];
            
                // Vérifications de base des champs obligatoires
                if (!$userId) {
                    $erreurs[] = "Utilisateur non identifié.";
                }
                if (!$amount || $amount <= 0) {
                    $erreurs[] = "Le montant doit être supérieur à 0.";
                }
                if (!in_array($paymentMethod, ['carte', 'paypal', 'especes', 'cheque'], true)) {
                    $erreurs[] = "Méthode de paiement invalide.";
                }
            
                // Vérifier que toutes les valeurs nécessaires sont présentes et valides
                if (empty($erreurs)) {
                    $success = $pdo->enregistrerDon($userId, $amount, $mitsva, $raison, $date, $paymentMethod);
            
                    if ($success) {
                        header("Location: index.php?uc=dons&action=afficher&message=Don enregistré avec succès");
                        exit();
                    } else {
                        $erreurs[] = "Erreur lors de l'enregistrement du don.";
                    }
                }
            
                // Affichage des erreurs via Twig
                echo $twig->render('v_formulaire.twig', ['erreurs' => $erreurs]);
                break;
            
        
}
            