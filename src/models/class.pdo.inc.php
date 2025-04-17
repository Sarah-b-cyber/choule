<?php 

/**
 * Classe d'accès aux données pour l'application Choule.
 *
 * Utilise PDO pour interagir avec la base de données `choule_db`.
 *
 * PHP Version 8+
 */

class PdoChoule
{
    private static $serveur = 'mysql:host=localhost';
    private static $bdd = 'dbname=choule_db';
    private static $user = 'root';
    private static $mdp = '';
    private static $monPdo;
    private static $monPdoChoule = null;

    private function __construct()
    {
        try {
            self::$monPdo = new PDO(
                self::$serveur . ';' . self::$bdd . ';charset=utf8',
                self::$user,
                self::$mdp,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function getPdoChoule()
    {
        if (self::$monPdoChoule === null) {
            self::$monPdoChoule = new PdoChoule();
        }
        return self::$monPdoChoule;
    }
    public function getPDO()
    {
        return self::$monPdo;
    }

    public function inscrireUtilisateur($login, $mdp, $name, $prenom, $email) 
    {
        $passwordHash = password_hash($mdp, PASSWORD_BCRYPT); // Hachage sécurisé en BCRYPT
    
        $requetePrepare = self::$monPdo->prepare(
            'INSERT INTO users (login, password, name, prenom, email) 
             VALUES (:login, :password, :name, :prenom, :email)'
        );
    
        return $requetePrepare->execute([
            ':login' => $login,
            ':password' => $passwordHash, // ✅ Correction : placeholder cohérent
            ':name' => $name,
            ':prenom' => $prenom,
            ':email' => $email
        ]);
    }
    
    public function inscrireDonateur($name, $prenom)
    {
        $requetePrepare = self::$monPdo->prepare(
            'INSERT INTO users (name, prenom, created_at) 
             VALUES (:name, :prenom, NOW())'
        );

        return $requetePrepare->execute([
            ':name' => $name,
            ':prenom' => $prenom
        ]);
    }


    // Fonction pour vérifier le mot de passe et se connecter
    public function getInfosFidele($login) 
    {
        $requetePrepare = self::$monPdo->prepare(
            'SELECT id, name, prenom, role, password
             FROM users 
             WHERE login = :login'
        );
        $requetePrepare->execute([':login' => $login]);
    
        return $requetePrepare->fetch(); // Retourne l'utilisateur ou false si non trouvé
    }
    


    public function getPromessesDons($userId) {
        $requetePrepare = self::$monPdo->prepare(
            'SELECT id, amount, payment_method, mitsva, raison, date_validation, status
             FROM donations
             WHERE user_id = :userId'
        );
        $requetePrepare->bindParam(':userId', $userId, PDO::PARAM_INT);
        $requetePrepare->execute();
    
        return $requetePrepare->fetchAll();
    }
    

    public function getDonById($id_don) {
        if (!is_numeric($id_don) || $id_don <= 0) {
            return false;
        }

        $requetePrepare = self::$monPdo->prepare(
            "SELECT id, user_id, amount, payment_method, status, created_at 
             FROM donations 
             WHERE id = :id_don"
        );
        $requetePrepare->bindParam(':id_don', $id_don, PDO::PARAM_INT);
        $requetePrepare->execute();
        
        return $requetePrepare->fetch();
    }

    public function getUserById($userId) {
        $stmt = self::$monPdo->prepare(
            "SELECT name, prenom, email, adresse, code_postal, ville, pays, role
             FROM users 
             WHERE id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }

    public function getDonId($id) {
        $stmt = self::$monPdo->prepare("
            SELECT d.id, u.name AS nom, u.prenom, d.amount, d.created_at, d.status
            FROM donations d
            JOIN users u ON d.user_id = u.id
            WHERE d.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    

    public function updateUser($id, $name, $prenom, $email, $adresse, $code_postal, $ville, $pays) { 
        $sql = "UPDATE users 
                SET name = :name, prenom = :prenom, email = :email, 
                    adresse = :adresse, code_postal = :code_postal, 
                    ville = :ville, pays = :pays 
                WHERE id = :id";

        $stmt = self::$monPdo->prepare($sql);
        
        return $stmt->execute([
            ':id' => $id,
            ':name' => $name,
            ':prenom' => $prenom,
            ':email' => $email,
            ':adresse' => $adresse,
            ':code_postal' => $code_postal,
            ':ville' => $ville,
            ':pays' => $pays
        ]);
    }

    public function getAllDons() { 
        $stmt = self::$monPdo->query("
            SELECT d.id, d.amount, d.payment_method, d.status, d.date_validation, d.mitsva, d.raison,
                   u.name, u.prenom, u.email
            FROM donations d
            JOIN users u ON d.user_id = u.id
            ORDER BY u.name ASC , u.prenom ASC
        ");
        
        return $stmt->fetchAll();
    }
    public function getsearch($searchTerm) { 
        $stmt = self::$monPdo->prepare("
           SELECT d.id, d.amount, d.payment_method, d.status, d.date_validation, d.mitsva, d.raison,
                    u.name, u.prenom, u.email
            FROM donations d
            JOIN users u ON d.user_id = u.id
            WHERE LOWER(u.name) LIKE :search OR LOWER(u.prenom) LIKE :search
            ORDER BY u.name ASC, u.prenom ASC
        ");
        
         // Exécuter la requête avec le paramètre :search
    $stmt->execute([':search' => '%' . strtolower($searchTerm) . '%']);

    // Retourner les résultats
    return $stmt->fetchAll();
}
    public function getAllDonsb() { 
        $stmt = self::$monPdo->query("
            SELECT d.id, 
                   d.amount AS montant, 
                   d.payment_method, 
                   d.status, 
                   d.date_validation AS date, 
                   d.mitsva, 
                   d.raison,
                   u.name AS nom_donateur, 
                   u.prenom AS prenom, 
                   u.email
            FROM donations d
            JOIN users u ON d.user_id = u.id
            ORDER BY u.name ASC , u.prenom ASC
        ");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // S'assurer d'un retour en tableau associatif
    }
    public function enregistrerDon($userId, $amount, $mitsva, $raison, $date, $paymentMethod)
    {
        try {
            // Vérifications côté serveur
            if (!is_numeric($amount) || $amount <= 0) {
                throw new Exception("Montant invalide.");
            }
    
            $allowedMethods = ['carte', 'paypal', 'especes', 'cheque'];
            if (!in_array($paymentMethod, $allowedMethods, true)) {
                throw new Exception("Méthode de paiement invalide.");
            }
    
            // Préparation de la requête SQL
            $sql = "INSERT INTO donations (user_id, amount, payment_method, created_at, mitsva, raison)
                    VALUES (:user_id, :amount, :payment_method, :created_at, :mitsva, :raison)";
    
            // Liaison des paramètres
            $stmt = self::$monPdo->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $amount, PDO::PARAM_STR);
            $stmt->bindValue(':payment_method', $paymentMethod, PDO::PARAM_STR);
            $stmt->bindValue(':created_at', $date, PDO::PARAM_STR);  // Correction : correspond à created_at
            $stmt->bindValue(':mitsva', $mitsva, PDO::PARAM_STR);
            $stmt->bindValue(':raison', $raison, PDO::PARAM_STR);
    
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur d'insertion du don: " . $e->getMessage());
            return false;
        }
    }
    
    

    public function addDonByName($nom, $prenom, $montant, $mitsva, $raison, $date, $paymentMethod, $status) {
        try {
            // Étape 1 : Vérifier si l'utilisateur existe (insensible à la casse et aux espaces)
            error_log("Recherche utilisateur avec nom : $nom, prénom : $prenom");
            $stmtUser = self::$monPdo->prepare(
                "SELECT id FROM users WHERE TRIM(LOWER(name)) = TRIM(LOWER(:nom)) 
                                       AND TRIM(LOWER(prenom)) = TRIM(LOWER(:prenom))"
            );
            $stmtUser->execute([
                ':nom' => $nom,
                ':prenom' => $prenom
            ]);
            $user = $stmtUser->fetch();
    
            if (!$user) {
                throw new Exception("Utilisateur non trouvé : $nom $prenom");
            }
    
            $userId = $user['id'];
    
            // Étape 2 : Insérer le don
            error_log("Insertion du don : user_id = $userId, montant = $montant, mitsva = $mitsva, raison = $raison, date = $date, méthode = $paymentMethod, statut= $status");
    
            $stmtDon = self::$monPdo->prepare(
                "INSERT INTO donations (user_id, amount, mitsva, raison, created_at, payment_method, status) 
                 VALUES (:user_id, :montant, :mitsva, :raison, :date, :methode, :status)"
            );
            $success = $stmtDon->execute([
                ':user_id' => $userId,
                ':montant' => (float) $montant,
                ':mitsva' => $mitsva,
                ':raison' => $raison,
                ':date' => !empty($date) ? $date : null,
                ':methode' => !empty($paymentMethod) ? $paymentMethod : null,
                ':status'=>$status
            ]);
    
            if (!$success) {
                error_log("Erreur lors de l'insertion du don pour l'utilisateur ID : $userId");
                return false;
            }
            return true;
    
        } catch (Exception $e) {
            error_log("Erreur : " . $e->getMessage());
            header('Location: index.php?uc=admin&error=user_not_found');
            return false;
        }
    }
    
    
    public function deleteDon($id) {
        $stmt = self::$monPdo->prepare(
            "DELETE FROM donations WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    public function updateDon($id, $userId, $montant, $date) {
        $stmt = self::$monPdo->prepare(
            "UPDATE donations 
             SET user_id = :user_id, amount = :montant, created_at = :date 
             WHERE id = :id"
        );
        return $stmt->execute([
            ':id' => $id,
            ':user_id' => $userId,
            ':montant' => $montant,
            ':date' => $date
        ]);
    }

    public function updateDonById($id, $nom, $prenom, $montant, $date, $status) {
        try {
            // Vérifier si l'utilisateur existe via nom et prénom
            $stmtUser = self::$monPdo->prepare(
                "SELECT id FROM users WHERE name = :nom AND prenom = :prenom"
            );
            $stmtUser->execute([
                ':nom' => $nom,
                ':prenom' => $prenom
            ]);
            $user = $stmtUser->fetch();
    
            if (!$user) {
                throw new Exception("Utilisateur non trouvé.");
            }
    
            $userId = $user['id'];
    
            // Vérification du statut pour éviter les erreurs
            if (!in_array($status, ['en attente', 'validé'])) {
                throw new Exception("Statut invalide.");
            }
    
            // Mise à jour du don avec le statut
            $stmtDon = self::$monPdo->prepare(
                "UPDATE donations 
                 SET user_id = :user_id, amount = :montant, date_validation = :date, status = :status
                 WHERE id = :id"
            );
    
            return $stmtDon->execute([
                ':id' => $id,
                ':user_id' => $userId,
                ':montant' => $montant,
                ':date' => $date,
                ':status' => $status
            ]);
    
        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du don : " . $e->getMessage());
            return false;
        }
    }

    public function getUserByEmail($email) {
        try {
            $stmt = self::$monPdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'utilisateur ou false si non trouvé
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération de l'utilisateur par email : " . $e->getMessage());
            return false;
        }
    }
    
    
    public function resetPassword($newPassword, $userId) {
        $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT); // ✅ Sécurisé avec BCRYPT
    
        $requetePrepare = self::$monPdo->prepare(
            'UPDATE users SET password = :newPassword WHERE id = :userId'
        );
    
        return $requetePrepare->execute([
            ':newPassword' => $passwordHash,
            ':userId' => $userId
        ]);
    }
    

    public function loginExiste($login) {
        $stmt = self::$monPdo->prepare("SELECT id FROM users WHERE login = ?");
        $stmt->execute([$login]);
        return $stmt->fetch() ? true : false;
    }
    
    public function emailExiste($email) {
        $stmt = self::$monPdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ? true : false;
    }
    
    public function getDonateurs() {
        $sql = "SELECT id, CONCAT(name, ' ', prenom) AS full_name FROM users";
        $stmt = self::$monPdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
