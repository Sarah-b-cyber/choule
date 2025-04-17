<?php
require_once 'src/config/twig.php';
require_once 'src/models/class.pdo.inc.php';
require 'vendor/autoload.php'; // Charger PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie que l'utilisateur est administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php?uc=connexion');
    exit();
}

$pdo = PdoChoule::getPdoChoule();
$action = $_GET['action'] ?? 'view';

// Recherche d'un donateur si action = 'search_donateur'
if ($action === 'search_donateur') {
    $searchTerm = trim($_GET['search_term'] ?? '');
    $dons = [];

    if (!empty($searchTerm)) {
        $pdo = PdoChoule::getPdoChoule();
        
        // Appel de la méthode getsearch() avec le terme de recherche
        $dons = $pdo->getsearch($searchTerm);
    }

    // Passer les dons à la vue Twig
    echo $twig->render('v_accueilad.twig', ['dons' => $dons, 'search_term' => $searchTerm]);
    exit();
}
if ($action === 'view') {
    $dons = $pdo->getAllDons();
    echo $twig->render('v_accueilad.twig', ['dons' => $dons]);

  
    }elseif ($action === 'add_don') {
    $pdo = PdoChoule::getPdoChoule();  // Obtenir l'instance de la classe
    $donateurs = $pdo->getDonateurs(); // Récupérer la liste des donateurs
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS); // Ajout du prénom
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
        $date = !empty($_POST['date']) ? filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $paymentMethod = !empty($_POST['methode']) ? filter_input(INPUT_POST, 'methode', FILTER_SANITIZE_SPECIAL_CHARS) : null;
        $mitsva = filter_input(INPUT_POST, 'mitsva', FILTER_SANITIZE_SPECIAL_CHARS);
        $raison = filter_input(INPUT_POST, 'raison', FILTER_SANITIZE_SPECIAL_CHARS);
        $full_name = filter_input(INPUT_POST, 'donateur', FILTER_SANITIZE_SPECIAL_CHARS);
        list($nom, $prenom) = explode(' ', $full_name, 2);
        $parts = explode(' ', $full_name);
        $nom = $parts[0];  
        $prenom = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
        

        if ($nom && $prenom && $montant  && $mitsva && $raison && $status) { // Vérifie que toutes les valeurs sont présentes
            $success = $pdo->addDonByName($nom, $prenom, $montant, $mitsva, $raison, $date, $paymentMethod, $status);
            if ($success) {
                header('Location: index.php?uc=admin&success=add');
            } else {

                header('Location: index.php?uc=admin&error=user_not_found');
               
            }
            exit();
        }
    }
    echo $twig->render('formdons.twig', ['donateurs' => $donateurs]); // Afficher la vue avec les donateurs
    
} elseif ($action === 'delete_don') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $pdo->deleteDon($id);
    }
    header('Location: index.php?uc=admin2&success=delete');
    

    
    }elseif ($action === 'export_excel') {
    
        $dons = $pdo->getAllDonsb(); // Récupère les dons depuis la BDD
    
        // Créer un nouveau fichier Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Ajouter les en-têtes avec du style
        $headers = ['A1' => 'Nom du Donateur', 'B1' => 'Montant (€)', 'C1' => 'Date', 
                    'D1' => 'Méthode de paiement', 'E1' => 'Mitsva', 'F1' => 'Raison', 'G1' => 'Status'];
    
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            
            // Mettre en gras et en jaune
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFF00');
            
            // Centrer le texte
            $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Ajouter des bordures
            $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }
    
        // Ajouter les données des dons
        $row = 2;
        foreach ($dons as $don) {
            $sheet->setCellValue('A' . $row, $don['nom_donateur'] . ' ' . $don['prenom']);
            $sheet->setCellValue('B' . $row, $don['montant']);
            $sheet->setCellValue('C' . $row, $don['date']);
            $sheet->setCellValue('D' . $row, $don['payment_method']);
            $sheet->setCellValue('E' . $row, $don['mitsva']);
            $sheet->setCellValue('F' . $row, $don['raison']);
            $sheet->setCellValue('G' . $row, $don['status']);
            $row++;
        }
    
        // Ajuster la largeur automatique des colonnes
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    
        // Générer le fichier Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'dons.xlsx';
    
        // Nettoyer le buffer pour éviter les erreurs
        ob_clean();
    
        // Configurer l'en-tête HTTP pour le téléchargement
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');
    
        // Envoyer le fichier Excel au navigateur
        $writer->save('php://output');
        exit();
    
    


}elseif ($action === 'edit_don') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_SPECIAL_CHARS);
        $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_SPECIAL_CHARS);
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_FLOAT);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_SPECIAL_CHARS);
        $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($nom && $prenom && $montant && $date && $status) {
            $pdo->updateDonById($id, $nom, $prenom, $montant, $date, $status);
            header('Location: index.php?uc=admin&success=update');
            exit();
        }
    }

    $don = $pdo->getDonId($id);

    if (!$don) {
        header('Location: index.php?uc=admin&error=notfound');
        exit();
    }

    echo $twig->render('form_edit_don.twig', ['don' => $don]);
}
?>


