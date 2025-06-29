<!-- nav.php -->
<style>
  .navbar-custom {
    background: linear-gradient(to right, #0d6efd, #6610f2); /* ไล่สีจากฟ้าไปม่วง */
    border-radius: 0 0 10px 10px;
  }

  .navbar-brand i {
    margin-right: 5px;
  }

  .nav-link {
    color: #ffffffcc !important;
    transition: all 0.3s ease;
    font-weight: 500;
    padding: 8px 15px;
    border-radius: 10px;
  }

  .nav-link:hover,
  .nav-link.active {
    background-color: rgba(255, 255, 255, 0.15);
    color: #ffffff !important;
  }
</style>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold fs-4" href="index.php">
    <i class="fa-solid fa-house"></i> วัดคลองเจริญ
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
            <i class="fas fa-cash-register"></i> หน้าขาย
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'sales_report.php' ? 'active' : '' ?>" href="sales_report.php">
            <i class="fas fa-chart-line"></i> ดูยอดขาย
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
