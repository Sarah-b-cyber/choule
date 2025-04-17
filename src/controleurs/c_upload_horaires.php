<?php
session_start();

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['isAdmin']) || !$_SESSION['isAdmin']) {
    header("Location: index.php");
    exit;
}

// Vérifier si un fichier a été envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['horaire_pdf'])) {
    $uploadDir = 'uploads/';  // Dossier où stocker les fichiers
    $uploadFile = $uploadDir . 'horaires.pdf'; // Nom fixe pour le fichier

    // Vérifier si l'extension est bien un PDF
    $fileType = mime_content_type($_FILES['horaire_pdf']['tmp_name']);
    if ($fileType !== 'application/pdf') {
        $_SESSION['erreur'] = "Le fichier doit être un PDF.";
        header("Location: index.php?uc=admin");
        exit;
    }

    // Déplacer le fichier vers le dossier des uploads
    if (move_uploaded_file($_FILES['horaire_pdf']['tmp_name'], $uploadFile)) {
        $_SESSION['message'] = "Horaires mis à jour avec succès !";
    } else {
        $_SESSION['erreur'] = "Erreur lors du téléversement.";
    }
}

header("Location: index.php?uc=admin");
exit;
?>





<?php 
require_once 'src/config/twig.php';

class HorairesController {
    public function afficher() {
        global $twig;

        // Définir le chemin du fichier des horaires
        $fichier_horaires = "src/image/horaires.pdf"; // Le PDF importé sera stocké ici

        // Vérifier si le fichier existe
        $fichierExiste = file_exists($fichier_horaires);

        // Rendre la vue avec les variables nécessaires
        echo $twig->render('v_horaires.twig', [
            'fichier_horaires' => $fichierExiste ? $fichier_horaires : null,
            'isAdmin' => isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin',
            'message' => $_SESSION['message'] ?? null,
            'erreur' => $_SESSION['erreur'] ?? null
        ]);

        // Nettoyer les messages après affichage
        unset($_SESSION['message'], $_SESSION['erreur']);
    }

    public function upload() {
        session_start(); // Démarrer la session si nécessaire

        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['erreur'] = "Vous n'avez pas les droits pour effectuer cette action.";
            header("Location: index.php?uc=horaire");
            exit();
        }

        // Vérifier si un fichier a été envoyé
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['horaire_pdf'])) {
            $image = 'src/image';  // Dossier où stocker les fichiers
            $uploadFile = $image . 'horaires.pdf'; // Nom fixe pour le fichier

            // Vérifier que le fichier est bien un PDF
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
                $_SESSION['erreur'] = "Erreur lors du téléversement.";
            }
        } else {
            $_SESSION['erreur'] = "Aucun fichier reçu.";
        }

        header("Location: index.php?uc=horaire");
        exit();
    }
}






<?php 
require_once 'src/config/twig.php';

class HorairesController {
    public function afficher() {
        global $twig;

        // Définir le chemin du fichier des horaires
        $fichier_horaires = "src/image/horaires.pdf"; // Le PDF importé sera stocké ici

        // Vérifier si le fichier existe
        $fichierExiste = file_exists($fichier_horaires);

        // Rendre la vue avec les variables nécessaires
        echo $twig->render('v_horaires.twig', [
            'fichier_horaires' => $fichierExiste ? $fichier_horaires : null,
            'isAdmin' => isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin',
            'message' => $_SESSION['message'] ?? null,
            'erreur' => $_SESSION['erreur'] ?? null
        ]);

        // Nettoyer les messages après affichage
        unset($_SESSION['message'], $_SESSION['erreur']);
    }

    public function upload() {
        session_start(); // Démarrer la session si nécessaire

        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            $_SESSION['erreur'] = "Vous n'avez pas les droits pour effectuer cette action.";
            header("Location: index.php?uc=horaire");
            exit();
        }

        // Vérifier si un fichier a été envoyé
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['horaire_pdf'])) {
            $image = 'src/image';  // Dossier où stocker les fichiers
            $uploadFile = $image . 'horaires.pdf'; // Nom fixe pour le fichier

            // Vérifier que le fichier est bien un PDF
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
                $_SESSION['erreur'] = "Erreur lors du téléversement.";
            }
        } else {
            $_SESSION['erreur'] = "Aucun fichier reçu.";
        }

        header("Location: index.php?uc=horaire");
        exit();
    }
}
