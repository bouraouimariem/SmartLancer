<?php
session_start();
require_once __DIR__ . '/../model/libs/dompdf/autoload.inc.php';
require_once __DIR__ . '/../controller/propositionC.php';

use Dompdf\Dompdf;

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}

$propositionC = new propositionController();
$propositions = $propositionC->list_propo_all(); // Assure-toi que cette fonction récupère bien tous les champs

// Générer HTML
$html = "<h1 style='text-align:center;'>Liste des Propositions</h1>";
$html .= "<table border='1' width='100%' cellpadding='6' style='border-collapse: collapse;'>
            <tr style='background:#eee;'>
                <th>ID</th>
                <th>Utilisateur</th>
                <th>Publication</th>
                <th>Montant</th>
                <th>Délai</th>
                <th>Commentaire</th>
                <th>Date</th>
                <th>Status</th>
            </tr>";

foreach ($propositions as $p) {
    $html .= "<tr>
                <td>{$p['id_propo']}</td>
                <td>{$p['user_nom']}</td>
                <td>{$p['nom_pub']}</td>
                <td>{$p['montant_propo']} DT</td>
                <td>{$p['delai_estime']}</td>
                <td>{$p['commentaire']}</td>
                <td>{$p['date_propo']}</td>
                <td>{$p['status']}</td>
            </tr>";
}

$html .= "</table>";

// Générer PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Télécharger automatiquement le PDF
$dompdf->stream("propositions.pdf", ["Attachment" => true]);
exit();
?>
