<?php
include '../../../controller/publicationC.php';

$publicationC = new publicationController();
$id_user = 1;
$list = $publicationC->list_pub_all();
?>
<?php include 'tete et pied/tete.php'; ?>

<div class="bradcam_area bradcam_bg_1">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="bradcam_text">
                    <h3>admin_page</h3>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="myProjectsContent" class="content-section mt-4">
    <div class="container mt-4">
        <div class="row justify-content-start">
            <div class="col-lg-12">
                <center>
                    <h4 class="section-title">les Publications:</h4>
                </center>
                <div class="container">
                    <div class="row">
                        <?php foreach ($list as $publication) { ?>
                            <div class="col-lg-12 col-md-12">
                                <div class="single_jobs white-bg p-4 shadow-sm rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="mb-0 text-dark"><?php echo $publication['nom_pub']; ?></h4>
                                        <div>
                                            <button class="btn btn-info btn-sm ms-3"
                                                onclick="yourFunction(<?php echo $publication['id_pub']; ?>)">Les
                                                Propositions</button>
                                        </div>
                                        <div class="d-flex">
                                            <a href="tete et pied/delete_publication_admin.php?id_pub=<?php echo $publication['id_pub']; ?>"
                                                class="btn btn-secondary">Supp</a>
                                        </div>
                                    </div>


                                    <div class="jobs_content">
                                        <div class="text mb-3">
                                            <p class="text-muted"><?php echo $publication['description']; ?></p>
                                        </div>
                                        <div class="d-flex justify-content-between text-muted">
                                            <div class="location">
                                                <p><strong>Budget:</strong> <?php echo $publication['budget']; ?>dt</p>
                                            </div>
                                            <div class="location">
                                                <p><i class="fa fa-clock-o"></i> <strong>Délai:</strong>
                                                    <?php echo $publication['delai_requise']; ?></p>
                                            </div>
                                            <div class="location">
                                                <p><i class="fa fa-calendar"></i> <strong>Date:</strong>
                                                    <?php echo $publication['date_pub']; ?></p>
                                            </div>
                                            <div class="location">
                                                <p><i class="fa fa-calendar"></i> <strong>Status:</strong>
                                                    <?php echo $publication['status']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div><br>

                            </div>
                        <?php } ?>



                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php include 'tete et pied/pied.php'; ?>