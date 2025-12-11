<?php
session_start();
require_once __DIR__ . '/../model/libs/dompdf/autoload.inc.php';
require_once __DIR__ . '/../controller/publicationC.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

// récupérer les données
$pubC = new publicationController();
$publications = $pubC->list_pub_all(); // <-- la bonne fonction

$html = "<h1 style='text-align:center;'>Liste des Publications</h1>";
$html .= "<table border='1' width='100%' cellspacing='0' cellpadding='5'>
            <tr style='background:#eee;'>
                <th>ID</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Budget</th>
                <th>Date</th>
            </tr>";

foreach ($publications as $pub) {
    $html .= "<tr>
                <td>{$pub['id_pub']}</td>
                <td>{$pub['nom_pub']}</td>
                <td>{$pub['categorie']}</td>
                <td>{$pub['budget']}</td>
                <td>{$pub['date_pub']}</td>
              </tr>";
}

$html .= "</table>";

// PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("publications.pdf", ["Attachment" => true]);
