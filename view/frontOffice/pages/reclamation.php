<!doctype html>
<html class="no-js" lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>SmartLancer</title>
    <meta name="description" content="Page de réclamation - Lanci">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="../img/logo.png">

</head>

<body>
    <!-- HEADER -->
    <header>
        <div class="header-area">
            <div id="sticky-header" class="main-header-area">
                <div class="container-fluid">
                    <div class="header_bottom_border">
                        <div class="row align-items-center">
                            <div class="col-xl-3 col-lg-2">
                                <div class="logo" style="text-align: left; margin-top: 10px;">
                                    <a href="../index.php">
                                        <img src="../img/logo.png" alt="logo" width="60">
                                        <h1 style="font-family: 'Poppins', sans-serif; font-size: 20px; color: white; margin-left: 5px;">Lanci</h1>
                                    </a>
                                </div>
                            </div>
                           
                            <div class="col-xl-3 col-lg-3 d-none d-lg-block">
                                <div class="Appointment">
                                    <div class="phone_num d-none d-xl-block">
                                        <a href="login.php" style="font-size: 16px;">Log in</a>
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

    <!-- FORMULAIRE DE RÉCLAMATION -->
    <div class="container mt-5 mb-5">
        <h2 class="text-center mb-4">📝 Déposer une réclamation</h2>

        <form method="POST" action="reclamation.php" class="p-4 shadow rounded bg-light">
            <div class="form-group mb-3">
                <label for="nom">Nom complet</label>
                <input type="text" name="nom" id="nom" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="email">Adresse email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="sujet">Sujet</label>
                <input type="text" name="sujet" id="sujet" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="message">Message</label>
                <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
            </div>

            <div class="text-center">
                <button type="submit" name="envoyer" class="btn btn-primary">Envoyer</button>
            </div>
        </form>

        <?php
        if (isset($_POST['envoyer'])) {
            $nom = $_POST['nom'];
            $email = $_POST['email'];
            $sujet = $_POST['sujet'];
            $message = $_POST['message'];

            echo "<div class='alert alert-success mt-4'>
                    Merci <b>$nom</b>, votre réclamation a été envoyée avec succès !
                </div>";
        }
        ?>
    </div>

    <!-- FOOTER -->
    <footer class="text-cent
