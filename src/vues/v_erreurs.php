<?php
/*
 * Vue Erreurs - Affichage des erreurs de manière élégante
 */
?>
<style>
    .alert-container {
        position: fixed; /* Fixé pour éviter qu'il soit caché */
        top: 100px; /* Ajuste la hauteur pour qu'il ne soit pas trop haut */
        left: 50%;
        transform: translateX(-50%);
        width: 80%; /* Largeur adaptée */
        max-width: 600px; /* Largeur max pour éviter trop d'étalement */
        z-index: 1000; /* Assure qu'il est au-dessus des autres éléments */
    }
    .alert {
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #f8d7da; /* Rouge clair pour erreur */
        color: #721c24;
        border: 1px solid #f5c6cb;
        font-size: 16px;
        text-align: center;
    }
    .close-btn {
        background: none;
        border: none;
        font-size: 18px;
        font-weight: bold;
        float: right;
        cursor: pointer;
    }
</style>

<div class="alert-container">
    <div class="alert">
        <button class="close-btn" onclick="this.parentElement.style.display='none'">&times;</button>
        <?php
        foreach ($_REQUEST['erreurs'] as $erreur) {
            echo '<p>' . htmlspecialchars($erreur) . '</p>';
        }
        ?>
    </div>
</div>
