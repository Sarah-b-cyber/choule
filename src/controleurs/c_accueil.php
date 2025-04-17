<?php
require_once 'src/config/twig.php';

$rabbins = [
    ['nom' => 'Rav Germon', 'image' => 'src/image/rabbin1.jpg'],
    ['nom' => 'Rav Levy', 'image' => 'src/image/rabbin2.png'],
];

$equipe = [
    ['nom' => 'Mr Elharar', 'image' => 'src/image/equipe1.jpg'],
    ['nom' => 'Mr Benizri', 'image' => 'src/image/equipe2.jpg'],
];

$adresse = "17 Impasse Pasteur Vallery Radot, 94000 Créteil";
$contact = "01 40 82 26 44";
$map_url = "https://www.google.com/maps/dir//17+Impasse+Pasteur+Vallery+Radot,+94000+Créteil";
$map_embed_url = "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d5257.46525495781!2d2.4405960768433883!3d48.78699450572763!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e6734ee51173d5%3A0x519330fc361e1793!2s17%20Imp.%20Pasteur%20Vallery%20Radot%2C%2094000%20Cr%C3%A9teil!5e0!3m2!1sfr!2sfr!4v1741250460970!5m2!1sfr!2sfr";

echo $twig->render('v_accueil.twig', [
    'rabbins' => $rabbins,
    'equipe' => $equipe,
    'adresse' => $adresse,
    'contact' => $contact,
    'map_url' => $map_url,
    'map_embed_url' => $map_embed_url
]);

