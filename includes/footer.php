</main>

<footer class="footer">

    <div class="container footer-grid">

        <div>
            <h2>The Literary Nook</h2>

            <p>
                Books, stories, and worlds waiting to be discovered.
            </p>
        </div>

        <div>

            <h3>Explore</h3>

            <a href="<?php echo BASE_URL; ?>footer_links/info.php#about">About</a>
            <a href="<?php echo BASE_URL; ?>footer_links/info.php#contact">Contact</a>
            <a href="<?php echo BASE_URL; ?>footer_links/info.php#privacy">Privacy Policy</a>
            <a href="<?php echo BASE_URL; ?>footer_links/info.php#terms">Terms of Service</a>

        </div>

        <div>

            <h3>Follow</h3>

            <a href="#">Facebook</a>
            <a href="#">Instagram</a>
            <a href="#">Twitter</a>

        </div>

    </div>

    <div class="copyright">

        © <?= date("Y") ?> The Literary Nook. All Rights Reserved.

    </div>

</footer>

<script src="<?= asset('js/navbar.js') ?>"></script>
<script src="<?= asset('js/avatarPreview.js') ?>"></script>
<script src="<?= asset('js/home.js') ?>"></script>
<script src="<?= asset('js/cart.js') ?>"></script>
</body>
</html>