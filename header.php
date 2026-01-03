<?php
// Simple site header: shows the site title with minimal CSS.
// This include intentionally contains only the header markup and inline styles.
// Place this at the top of the <body> in pages where a simple site header is desired.
?>
<style>
  .site-header {
    background: #212529; /* dark */
    color: #fff;
    padding: 12px 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  .site-header .brand {
    font-weight: 700;
    font-size: 1.25rem;
    letter-spacing: 0.5px;
  }
  .site-header .actions a {
    color: #fff;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 6px;
    background: rgba(255,255,255,0.06);
  }
  .site-header .actions a:hover { background: rgba(255,255,255,0.12); color: #fff; }
  @media (max-width: 576px) {
    .site-header .brand { font-size: 1rem; }
  }
</style>
<?php
// Don't show the home action when we're already on the index page
$showHome = true;
$path = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
$basename = basename($path);
if ($basename === '' || $basename === 'index.php' || $path === '/online_exam' || $path === '/online_exam/') {
    $showHome = false;
}
?>
<header class="site-header">
  <div class="container d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
      <?php if ($showHome): ?>
      <div class="actions me-3">
        <a href="/online_exam/" aria-label="Go to homepage">
          <!-- simple arrow icon -->
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M4.5 8H13" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7.5 5L4 8l3.5 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>
      <?php endif; ?>
      <div class="brand" style="font-size: 56px; color: lightcoral; font-family: 'Courier New', Courier, monospace;">Online Exam Portal</div>
    </div>
    <div></div>
  </div>
</header>
