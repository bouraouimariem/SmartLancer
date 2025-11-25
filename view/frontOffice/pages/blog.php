<?php
include_once '../../../controller/blogC.php';
include_once '../../../controller/commentaireC.php';
include 'tete et pied/tetec.php';
$blogC = new BlogController();
$commentaireC = new CommentaireC();
$blogs = $blogC->listBlogs();
?>
<!doctype html>
<html class="no-js" lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SmartLancer - Blog</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Liens CSS -->
    <link rel="shortcut icon" type="image/x-icon" href="../../img/loogo.jpg">
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/font-awesome.min.css">
    <link rel="stylesheet" href="../../css/style.css">

    <style>
      body { background: linear-gradient(135deg,#f9f9ff,#eef3ff); font-family: 'Poppins', sans-serif; }
      .card { border-radius: 16px; overflow: hidden; }
      .card img { height:220px; object-fit:cover; }
      .btn-read { border-radius: 20px; border: 1.5px solid #4a4aff; color:#4a4aff; transition: all .3s; }
      .btn-read:hover { background: linear-gradient(135deg,#4a4aff,#7a5cff); color:#fff; }
      header .logo h1 { display:inline-block; margin-left:8px; font-size:18px; vertical-align: middle; }
      .text-muted {font-size: 0.9rem;color: #555;}

    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header-area ">
            <div id="sticky-header" class="main-header-area">
                <div class="container-fluid ">
                    <div class="header_bottom_border">
                        <div class="row align-items-center">
                            <div class="col-xl-3 col-lg-2">
                                <div class="logo" style="text-align: left; margin-top: 10px;">
                                    
                                    </a>
                                </div>
                            </div>
                            <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="Appointment">
                                    <div class="phone_num d-none d-xl-block">
                                        <a href="../login.php" style="font-size: 16px;">Log in</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mobile_menu d-block d-lg-none"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Contenu principal -->
    <main class="container" style="margin-top: 50px;">
        <h2 class="text-center text-dark">Bienvenue sur le Blog de SmartLancer</h2>
        <p class="text-center text-secondary">Découvrez nos dernières actualités, astuces et projets.</p>

        <div class="row mt-5">
            <?php if (empty($blogs)): ?>
                <div class="col-12">
                    <p class="text-center text-muted">Aucun article pour le moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($blogs as $b): 
    $titre = htmlspecialchars($b['Titre'] ?? 'Sans titre');
    $contenu = htmlspecialchars($b['contenue'] ?? '');
    $image = !empty($b['image']) ? "../../uploads/" . htmlspecialchars($b['image']) : "../../img/blog-placeholder.jpg";
    $date = !empty($b['date_creation']) ? date('d M Y', strtotime($b['date_creation'])) : '';
    $id = $b['id_blog'];

    // ✅ Compter le nombre de commentaires pour ce blog
    $count = $commentaireC->countCommentairesByBlog($id);
?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo $titre; ?>">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title" style="color:#4a4aff;"><?php echo $titre; ?></h5>
                <p class="card-text text-muted" style="flex-grow:1;">
                    <?php echo mb_substr($contenu, 0, 120) . (mb_strlen($contenu) > 120 ? '...' : ''); ?>
                </p>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-secondary">📅 <?php echo $date; ?></small>
                    <small class="text-secondary">👁️ <?php echo intval($b['nb_view'] ?? 0); ?></small>
                </div>
                <small class="text-secondary">💬 <?php echo $count; ?> commentaire<?php echo ($count > 1) ? 's' : ''; ?></small>

                <a href="blog_details.php?id_blog=<?php echo $id; ?>" class="btn btn-read mt-2">📖 Lire plus</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="text-center mt-5 mb-3">
        <p>&copy; 2025 SmartLancer. Tous droits réservés.</p>
    </footer>
</body>
</html>
