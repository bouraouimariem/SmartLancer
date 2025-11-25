<?php
include_once("../../../../controller/blogC.php");
$blogC = new BlogController();

if (isset($_GET['id_blog'])) {
    $blogC->deleteBlog($_GET['id_blog']);
}

// ✅ Correction du chemin de redirection :
header("Location: ../blogs.php");
exit();
?>
