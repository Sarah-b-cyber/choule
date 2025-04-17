<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oratoire du Palais</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Google Fonts (Poppins pour une typographie moderne) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Lien vers le fichier CSS personnalisé -->
    <link rel="stylesheet" href="src/styles/css/style.css">
    
    <!-- Font Awesome (pour l'icône mobile) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <img src="src/image/logo.jpg" alt="Oratoire du Palais">
            </a>

            <!-- Bouton pour mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars text-white"></i>
            </button>

            <!-- Liens de navigation -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">  <!-- ms-auto pour aligner à droite -->
                    <li class="nav-item"><a class="nav-link" href="index.php#oratoire">Oratoire</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#horaire">Horaires</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#activites">Activités</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#collel">Collel</a></li>
                </ul>
                
                <!-- Bouton de connexion -->
                <a href="index.php?uc=connexion" class="btn btn-connexion ms-3">Connexion</a>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

