<?php
require_once 'vendor/autoload.php'; 

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('src/vues'); 

$twig = new \Twig\Environment($loader, [
    'cache' => false, // Mettre en cache pour la prod, false pour le dev
    'debug' => true   // Active le mode debug
]);
$twig->addExtension(new \Twig\Extension\DebugExtension()); // Ajoute l'extension debug


return $twig; // On retourne l'objet $twig
