</main>

<footer class="text-start">
    <div class="container">
        <div class="row">
            
            <div class="col-md-4 mb-3">
                <h5 class="text-white fw-bold">PerfectMatch</h5>
                <p class="small">The most trusted and advanced matrimonial service dedicated to connecting Bengali hearts worldwide.</p>
            </div>

            <div class="col-md-3 mb-3">
                <h6 class="text-white">Quick Links</h6>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="#">Pricing & Plans</a></li>
                    <li><a href="#">Success Stories</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-3">
                <h6 class="text-white">Support</h6>
                <ul class="list-unstyled">
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Use</a></li>
                </ul>
            </div>

            <div class="col-md-2 mb-3">
                <h6 class="text-white">Follow Us</h6>
                <div class="d-flex">
                    <a href="#" class="me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-twitter fs-5"></i></a>
                    <a href="#" class="me-3"><i class="bi bi-instagram fs-5"></i></a>
                </div>
            </div>
        </div>

        <hr class="text-secondary">

        <div class="row">
            <div class="col-12 text-center">
                <p class="small mb-0">© <?php echo date("Y"); ?> PerfectMatch Matrimony | All Rights Reserved. | Made with ❤️ in Dhaka.</p>
            </div>
        </div>
    </div>
</footer>

<button onclick="topFunction()" id="scrollToTopBtn" title="Go to top">
    <i class="bi bi-arrow-up"></i>
</button>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Get the button element
  let mybutton = document.getElementById("scrollToTopBtn");

  // When the user scrolls down 200px, show the button
  window.onscroll = function() {scrollFunction()};

  function scrollFunction() {
    // Show button if scroll position is past 200 pixels
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }

  // When the user clicks on the button, scroll to the top of the document
  function topFunction() {
    // Use window.scrollTo with 'smooth' behavior for a modern effect
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  }

  document.addEventListener('DOMContentLoaded', function() {
    console.log("PerfectMatch Environment Ready!");
  });
</script>

</body>
</html>