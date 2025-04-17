/*case 'telechargerCerfa':
            require_once 'src/models/class.pdo.inc.php'; // Chargement de la classe PDO
            require_once 'vendor/autoload.php'; // Chargement de TCPDF
        
            $id_don = filter_input(INPUT_GET, 'id_don', FILTER_VALIDATE_INT);
            if (!$id_don) {
                die("ID de don invalide.");
            }
        
            $pdo = PdoChoule::getPdoChoule(); // Obtenir l'instance
            $don = $pdo->getDonById($id_don);

            if (!$don || $don['status'] !== 'validé') {
                die("Reçu indisponible.");
            }
        
            // Création du PDF
            $pdf = new TCPDF();
            $pdf->SetAutoPageBreak(true);
            $pdf->AddPage();
            $pdf->SetFont('helvetica', '', 12);
        
            // Contenu du CERFA
            $html = "
            <h1 style='text-align:center;'>Reçu Fiscal - CERFA</h1>
            
            <p><strong>Montant :</strong> {$don['amount']} €</p>
            <p><strong>Date :</strong> {$don['created_at']}</p>
            <p><strong>Organisme :</strong> Oratoire du Palais</p>
            <p>Ce document certifie que votre don est bien enregistré et ouvre droit à une réduction fiscale.</p>";
        
            $pdf->writeHTML($html, true, false, true, false, '');
        
            // Téléchargement du fichier PDF
            $pdf->Output("recu_cerfa_{$id_don}.pdf", 'D');
            exit;
        
            
        break;*/



        /* case 'paiement_stripe':
            $amount = filter_input(INPUT_GET, 'montant', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        
            try {
                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => $amount * 100, // Stripe prend les montants en centimes
                    'currency' => 'eur',
                    'metadata' => ['user_id' => $_SESSION['idfidele']]
                ]);
        
                echo $twig->render('paiement_stripe.twig', [
                    'clientSecret' => $paymentIntent->client_secret, // Clé secrète pour le paiement
                    'publishableKey' => $config['publishable_key'] // Clé publique pour le frontend
                ]);
            } catch (Exception $e) {
                echo $twig->render('v_formulaire.twig', [
                    'erreurs' => ["Erreur Stripe : " . $e->getMessage()]
                ]);
            }
            exit();
        

    case 'paiement_paypal':
        $amount = filter_input(INPUT_GET, 'montant', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        // Afficher la page de paiement par PayPal
        echo $twig->render('paiement_paypal.twig', [
            'montant' => $amount
        ]);
        break;

    case 'confirmation_stripe':
        echo $twig->render('v_confirmation.twig', [
            'message' => 'Paiement réussi avec Stripe !'
        ]);
        exit();

    
        
        
    default:
        header('Location: index.php');
        exit();
}*/

oui regarde ma mon controller don tt est bon <?php
// Démarrer la session uniquement si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php'; // Charger Stripe
require_once 'src/models/class.pdo.inc.php';
$twig = require_once 'src/config/twig.php';

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
        $userId = $_SESSION['idfidele'] ?? null;
        $amount = filter_input(INPUT_POST, 'montant', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $paymentMethod = filter_input(INPUT_POST, 'methode_paiement', FILTER_SANITIZE_SPECIAL_CHARS);
        $actionType = filter_input(INPUT_POST, 'action_type', FILTER_DEFAULT);

        $erreurs = [];

        if (!$userId) {
            $erreurs[] = "Utilisateur non identifié.";
        }
        if (!$amount || $amount <= 0) {
            $erreurs[] = "Le montant doit être supérieur à 0.";
        }
        if (!in_array($paymentMethod, ['carte', 'paypal', 'virement', 'especes', 'cheque'], true)) {
            $erreurs[] = "Méthode de paiement invalide.";
        }

        if (empty($erreurs)) {
            $pdo = PdoChoule::getPdoChoule();

            if ($actionType === "enregistrer") {
                $success = $pdo->enregistrerDon($userId, $amount, $paymentMethod);

                if ($success) {
                    header("Location: index.php?uc=dons&action=afficher&message=Don enregistré avec succès");
                    exit();
                } else {
                    $erreurs[] = "Erreur lors de l'enregistrement du don.";
                }
            } elseif ($actionType === "regler") {
                if ($paymentMethod === 'carte') {
                    header("Location: index.php?uc=dons&action=paiement_stripe&montant=$amount");
                    exit();
                } elseif ($paymentMethod === 'paypal') {
                    header("Location: index.php?uc=dons&action=paiement_paypal&montant=$amount");
                    exit();
                } else {
                    $erreurs[] = "Méthode de paiement non valide pour un règlement immédiat.";
                }
            }
        }

        echo $twig->render('v_formulaire.twig', [
            'erreurs' => $erreurs
        ]);
        break;

    }