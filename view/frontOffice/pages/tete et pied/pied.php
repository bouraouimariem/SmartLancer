

<!-- link that opens popup -->
<!-- JS here -->
<script src="../js/vendor/modernizr-3.5.0.min.js"></script>
<script src="../js/vendor/jquery-1.12.4.min.js"></script>
<script src="../js/popper.min.js"></script>
<script src="../js/bootstrap.min.js"></script>
<script src="../js/owl.carousel.min.js"></script>
<script src="../js/isotope.pkgd.min.js"></script>
<script src="../js/ajax-form.js"></script>
<script src="../js/waypoints.min.js"></script>
<script src="../js/jquery.counterup.min.js"></script>
<script src="../js/imagesloaded.pkgd.min.js"></script>
<script src="../js/scrollIt.js"></script>
<script src="../js/jquery.scrollUp.min.js"></script>
<script src="../js/wow.min.js"></script>
<script src="../js/nice-select.min.js"></script>
<script src="../js/jquery.slicknav.min.js"></script>
<script src="../js/jquery.magnific-popup.min.js"></script>
<script src="../js/plugins.js"></script>
<!-- <script src="js/gijgo.min.js"></script> -->
<script src="../js/range.js"></script>
<script src="../js/controle saisie.js"></script>



<!--contact js-->
<script src="../js/contact.js"></script>
<script src="../js/jquery.ajaxchimp.min.js"></script>
<script src="../js/jquery.form.js"></script>
<script src="../js/jquery.validate.min.js"></script>
<script src="../js/mail-script.js"></script>
<script src="../js/boutton_proposition.js"></script>


<script src="../js/main.js"></script>


<script>
    $(function () {
        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 5000,
            values: [0, 5000],
            slide: function (event, ui) {
                $("#amount").val(ui.values[0] + "dt --- " + ui.values[1] + "dt");
            }
        });
        $("#amount").val($("#slider-range").slider("values", 0) + "dt --- " +
            $("#slider-range").slider("values", 1) + "dt");
    });
</script>
<script>
    $(document).ready(function () {
        $("#filter_button").click(function () {
            var rangeValues = $("#slider-range").slider("values");
            var minBudget = rangeValues[0];
            var maxBudget = rangeValues[1];

            $.ajax({
                url: 'projet_client.php', // your PHP file to handle the filter
                type: 'GET',
                data: {
                    min_budget: minBudget,
                    max_budget: maxBudget
                },
                success: function (response) {
                    $("#publication_body").html(response); // Where you want to display the filtered publications
                }
            });
        });
    });
</script>
</body>

</html>