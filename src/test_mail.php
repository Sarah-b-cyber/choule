<?php
require 'src/models/mail.php'; // Assure-toi que c'est le bon chemin vers ta fonction envoyerMail()

$destinataire = 'tonemaildest@gmail.com'; // Mets un vrai mail ici
$sujet = 'Test de mail';
$contenu = '<h1>Bonjour !</h1><p>Ceci est un test de l’envoi de mail avec PHPMailer.</p>';

if (envoyerMail($destinataire, $sujet, $contenu)) {
    echo "✅ Email envoyé avec succès à $destinataire";
} else {
    echo "❌ Échec de l'envoi du mail. Regarde les logs PHP.";
}
?>
