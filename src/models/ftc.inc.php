<?php

/**
 * Fonctions pour l'application Choule.
 *
 * Utilise PDO pour interagir avec la base de données `choule_db`.
 *
 * PHP Version 8+
 */
/**
 * Teste si un quelconque fidele est connecté
 *
 * @return vrai ou faux
 */
function estConnecte()
{
    return isset($_SESSION['idfidele']);
}

/**
 * Enregistre dans une variable session les infos d'un fidele
 *
 * @param String $idfidele ID du fidele
 * @param String $nom        Nom du fidele
 * @param String $prenom     Prénom du visiteur
 *
 * @return null
 */
function connecter($idfidele, $nom, $prenom, $role)
{
    $_SESSION['idfidele'] = $idfidele;
    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['role'] = $role;
}