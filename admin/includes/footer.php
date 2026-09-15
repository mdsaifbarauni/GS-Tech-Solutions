<?php

// ==========================================================
// GS TECH SOLUTIONS
// ADMIN FOOTER
// ==========================================================

?>

    <!-- ==================================================
         FOOTER
    =================================================== -->

    <div class="dashboard-footer">

        © <?= date('Y') ?>

        GS Tech Solutions.

        All Rights Reserved.

    </div>


</section>


</main>


<!-- ======================================================
     JAVASCRIPT
======================================================= -->

<script>

    const mobileMenu =
        document.getElementById("mobileMenu");

    const sidebar =
        document.getElementById("sidebar");

    const overlay =
        document.getElementById("sidebarOverlay");


    function openSidebar() {

        if (sidebar) {
            sidebar.classList.add("show");
        }

        if (overlay) {
            overlay.classList.add("show");
        }

    }


    function closeSidebar() {

        if (sidebar) {
            sidebar.classList.remove("show");
        }

        if (overlay) {
            overlay.classList.remove("show");
        }

    }


    if (mobileMenu) {

        mobileMenu.addEventListener(
            "click",
            openSidebar
        );

    }


    if (overlay) {

        overlay.addEventListener(
            "click",
            closeSidebar
        );

    }


    document
        .querySelectorAll(".sidebar-link")
        .forEach(function(link) {

            link.addEventListener(
                "click",
                function() {

                    if (
                        window.innerWidth <= 991
                    ) {

                        closeSidebar();

                    }

                }
            );

        });

</script>


</body>

</html>