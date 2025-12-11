<?php
// Inclure DomPDF
require_once __DIR__ . '/libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Initialisation
$dompdf = new Dompdf();

// Contenu HTML
$html = "
    <h1 style='color:blue;'>Test PDF Réussi 🎉</h1>
    <p>Ceci est un test DomPDF sans Composer.</p>
";

// Charger HTML
$dompdf->loadHtml($html);

// Configuration de la page
$dompdf->setPaper('A4', 'portrait');

// Générer PDF
$dompdf->render();

// Télécharger
$dompdf->stream("test.pdf");
