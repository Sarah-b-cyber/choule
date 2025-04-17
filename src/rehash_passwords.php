<?php 
require_once 'models/class.pdo.inc.php'; // Inclure ton fichier de connexion PDO

try {
    $pdo = PdoChoule::getPdoChoule(); // Connexion à la BDD

    // Récupérer tous les utilisateurs qui ont encore des mots de passe en clair (moins de 60 caractères)
    $sql = "SELECT id, password FROM utilisateurs WHERE LENGTH(password) < 60";
    $stmt =  PdoChoule::getPdoChoule(); // Récupère l'instance PDO // Exécuter la requête
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer les résultats

    if (empty($users)) {
        echo "Tous les mots de passe sont déjà sécurisés ! ✅";
        exit();
    }

    foreach ($users as $user) {
        $newHash = password_hash($user['password'], PASSWORD_BCRYPT); // Hasher le mdp en BCRYPT
        
        // Mettre à jour le mot de passe dans la base de données
        $updateSql = "UPDATE utilisateurs SET password = ? WHERE id = ?";
        $updateStmt = $pdo->prepare($updateSql);
        $updateStmt->execute([$newHash, $user['id']]);

        echo "Mot de passe de l'utilisateur ID {$user['id']} mis à jour ✅<br>";
    }

    echo "<br><strong>Tous les mots de passe ont été mis à jour ! 🚀</strong>";

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
