<?php 
require_once 'src/config/twig.php';

class HorairesController {
    public function afficher() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $twig; // Assure-toi que Twig est bien accessible

        // Définir le chemin de l'image et du fichier à télécharger
        $image_horaires = "src/image/horaires.jpg";
        $fichier_horaires = "src/image/horaires.pdf";

        // Vérifier si l'utilisateur est un admin
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

        // Rendu de la vue avec les variables
        echo $twig->render('v_horaires.twig', [
            'image_horaires' => $image_horaires,
            'fichier_horaires' => $fichier_horaires,
            'isAdmin' => $isAdmin,
            'message' => $_SESSION['message'] ?? null,
            'erreur' => $_SESSION['erreur'] ?? null
        ]);

        // Nettoyage des messages de session après affichage
        unset($_SESSION['message'], $_SESSION['erreur']);
    }
    public function upload() {
        session_start(); // Démarrer la session si nécessaire
    
        // Vérifie si un fichier a été envoyé
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_FILES['horaire_pdf']) && $_FILES['horaire_pdf']['error'] === UPLOAD_ERR_OK) {
                $image = 'src/image';  // Dossier où stocker les fichiers
                $uploadFile = $image . '/horaires.pdf'; // Nom fixe pour le fichier
    
                // Vérifie que le fichier est bien un PDF
                $fileType = mime_content_type($_FILES['horaire_pdf']['tmp_name']);
                if ($fileType !== 'application/pdf') {
                    $_SESSION['erreur'] = "Le fichier doit être un PDF.";
                    header("Location: index.php?uc=horaire");
                    exit();
                }
    
                // Déplacer le fichier vers le dossier des uploads
                if (move_uploaded_file($_FILES['horaire_pdf']['tmp_name'], $uploadFile)) {
                    $_SESSION['message'] = "Horaires mis à jour avec succès !";
                } else {
                    $error = error_get_last(); // Récupère la dernière erreur PHP
                    $_SESSION['erreur'] = "Erreur lors du téléversement.";
                }
            } else {
                $_SESSION['erreur'] = "Erreur lors de l'upload ou aucun fichier reçu.";
            }
        }
    
        // Redirige après traitement
        header("Location: index.php?uc=horaire");
        exit();
    }
}   