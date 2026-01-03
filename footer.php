<?php
// Common footer include — shows a small footer and loads Bootstrap JS bundle
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<footer class="site-footer mt-5 pt-4">
  <div class="container">
    <div class="row">
      <div class="col-md-3 mb-3">
        <h6 class="mb-2">About</h6>
        <p class="small text-muted" style="font-size: small;">Online Exam Portal helps institutions run secure online tests, manage subjects, questions and results.</p>
      </div>
      <div class="col-md-3 mb-3">
        <h6 class="mb-2">Resources</h6>
        <ul class="list-unstyled small" style="font-size: small;">
          <li><a href="#" class="text-muted">How it works</a></li>
          <li><a href="#" class="text-muted">Documentation</a></li>
          <li><a href="#" class="text-muted">Pricing</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-3">
        <h6 class="mb-2">Support</h6>
        <ul class="list-unstyled small" style="font-size: small;">
          <li><a href="#" class="text-muted">Help Center</a></li>
          <li><a href="#" class="text-muted">Contact Us</a></li>
          <li><a href="#" class="text-muted">FAQ</a></li>
        </ul>
      </div>
      <div class="col-md-3 mb-3">
        <h6 class="mb-2">Legal</h6>
        <ul class="list-unstyled small" style="font-size: small;">
          <li><a href="#" class="text-muted">Terms of Service</a></li>
          <li><a href="#" class="text-muted">Privacy Policy</a></li>
        </ul>
        <div class="mb-2" style="font-size: small; color: black;">
          <a href="#" class="text-muted me-2">Twitter</a>
          <a href="#" class="text-muted me-2">Facebook</a>
          <a href="#" class="text-muted">LinkedIn</a>
        </div>
      </div>
    </div>
    <hr class="border-secondary" / style="font-size: small;">
    <div class="d-flex justify-content-between align-items-center pb-3">
      <div class="small text-muted">&copy; <?= date('Y') ?> Online Exam Portal</div>
      <div class="small text-muted">Built with ❤️ for learning</div>
    </div>
  </div>
</footer>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
