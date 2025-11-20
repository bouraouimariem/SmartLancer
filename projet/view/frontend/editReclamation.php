<?php
require_once "../../Model/Reclamation.php";

$model = new Reclamation();

if (!isset($_GET['id'])) {
    die("ID manquant !");
}

$id = $_GET['id'];
$rec = $model->getReclamation($id);

if (!$rec) {
    die("Réclamation introuvable !");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model->updateReclamation($id, $_POST['nom'], $_POST['email'], $_POST['sujet'], $_POST['message']);
    header("Location: success.php?id=$id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la réclamation</title>
</head>
<body>
    <h1>Modifier votre réclamation</h1>

    <form method="POST">
        <input type="text" name="nom" value="<?= htmlspecialchars($rec['nom']) ?>" required><br>
        <input type="email" name="email" value="<?= htmlspecialchars($rec['email']) ?>" required><br>
        <input type="text" name="sujet" value="<?= htmlspecialchars($rec['sujet']) ?>" required><br>
        <textarea name="message" required><?= htmlspecialchars($rec['message']) ?></textarea><br>
        <button type="submit">Modifier ma réclamation</button>
    </form>

    <br>
    <a href="success.php?id=<?= $rec['id'] ?>">Annuler</a>
</body>
</html>
