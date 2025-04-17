<?php    
require_once 'src/config/twig.php';
require_once 'src/config/mail.php';  
require_once 'src/models/class.pdo.inc.php';
require_once 'src/models/ftc.inc.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


ini_set("display_errors", 1);
error_reporting(E_ALL);

$error = null;
$action = $_GET['action'] ?? 'contact'; // Par défaut, afficher le formulaire de contact

switch ($action) {
    case 'envoyer':
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!empty($_POST["first_name"]) && !empty($_POST["last_name"]) && !empty($_POST["phone"]) && !empty($_POST["email"]) && !empty($_POST["problem_description"])) {

                // Sécurisation des données
                $prenom = htmlspecialchars(trim($_POST["first_name"]));
                $nom = htmlspecialchars(trim($_POST["last_name"]));
                $telephone = htmlspecialchars(trim($_POST["phone"]));
                $email = htmlspecialchars(trim($_POST["email"]));
                $message = nl2br(htmlspecialchars(trim($_POST["problem_description"])));

                // Validation email
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = "L'email n'est pas valide.";
                } 
                // Validation téléphone
                elseif (!preg_match('/^\+?[0-9]{10,15}$/', $telephone)) {
                    $error = "Le numéro de téléphone n'est pas valide.";
                } 
                else {
                    // Contenu de l'email
                    $email_message = "
                        <html>
                        <head>
                            <title>Demande de contact</title>
                        </head>
                        <body>
                            <h2>Nouvelle demande de contact</h2>
                            <p><strong>Nom:</strong> $nom</p>
                            <p><strong>Prénom:</strong> $prenom</p>
                            <p><strong>Téléphone:</strong> $telephone</p>
                            <p><strong>Email:</strong> $email</p>
                            <p><strong>Message:</strong></p>
                            <p>$message</p>
                        </body>
                        </html>
                    ";

                    // Envoi avec PHPMailer
                    if (envoyerMail("sarahbenizri2004@gmail.com", "Nouveau message de contact", $email_message)) {
                        error_log("Email envoyé avec succès à sarahbenizri2004@gmail.com");
                        header('Location: index.php?uc=merci');
                        exit();
                    } else {
                        $error = "❌ Erreur lors de l'envoi de l'email.";
                    }
                }
            } else {
                $error = "Veuillez remplir tous les champs.";
            }
        }
        break;

    case 'contact':
    default:
        // Affichage du formulaire de contact
        break;
}

// Affichage de la vue avec Twig
echo $twig->render('v_contact.twig', [
    'error' => $error ?? null,
    'first_name' => $_POST['first_name'] ?? '',
    'last_name' => $_POST['last_name'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'email' => $_POST['email'] ?? '',
    'problem_description' => $_POST['problem_description'] ?? ''
]);
?>
