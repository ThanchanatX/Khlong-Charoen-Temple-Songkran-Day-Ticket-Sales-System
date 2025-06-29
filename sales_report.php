<?php
require 'db_connection.php';

// รวมยอดขายทั้งหมด
$totalResult = $conn->query("SELECT 
    SUM(quantity) as total_qty, 
    SUM(total_price) as total_all,
    SUM(CASE WHEN payment_method = 'เงินสด' THEN total_price ELSE 0 END) as total_cash,
    SUM(CASE WHEN payment_method = 'แสกน' THEN total_price ELSE 0 END) as total_scan
    FROM sales
");

$summary = $totalResult->fetch_assoc();

// รายการขายทั้งหมด
$salesResult = $conn->query("SELECT * FROM sales ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>รายงานยอดขาย</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/235/235873.png" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">
  <link rel="manifest" href="/manifest.json">


  <style>
    body {
      font-family: 'Prompt', sans-serif;
      padding: 40px;
      background-color: #f1f1f1;
    }

    h1 {
      font-size: 2.5rem;
      font-weight: 600;
      color: #333;
    }

    .summary-box {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
      transition: transform 0.3s ease-in-out;
    }

    .summary-box:hover {
      transform: translateY(-5px);
    }

    .summary-box h5 {
      margin-bottom: 10px;
      color: #555;
      font-weight: 500;
    }

    .summary-box p {
      font-size: 1.25rem;
      font-weight: bold;
    }

    .table {
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .table th, .table td {
      vertical-align: middle;
    }

    .table th {
      background-color: #007bff;
      color: #fff;
      font-weight: 600;
    }

    .table td {
      text-align: center;
      font-size: 1rem;
    }

    .table-hover tbody tr:hover {
      background-color: #f8f9fa;
    }

    .btn-danger {
      background-color: #e74a3b;
      border-color: #e74a3b;
    }

    .btn-danger:hover {
      background-color: #c0392b;
      border-color: #c0392b;
    }

    @media (max-width: 768px) {
      .summary-box {
        margin-bottom: 15px;
      }

      .table {
        font-size: 0.9rem;
      }
    }

  </style>
</head>
<body>
<?php include 'nav.php'; ?>
<br>
  <div class="container">
    <h2 class="mb-4">รายงานยอดขาย</h2>

    <!-- สรุปยอด -->
    <div class="row text-center">
      <div class="col-md-4 summary-box">
        <h5>จำนวนตั๋วทั้งหมด</h5>
        <p class="fs-4"><?= $summary['total_qty'] ?> ใบ</p>
      </div>
      <div class="col-md-4 summary-box">
        <h5>ยอดขายเงินสด</h5>
        <p class="fs-4 text-success"><?= number_format($summary['total_cash'], 2) ?> บาท</p>
      </div>
      <div class="col-md-4 summary-box">
        <h5>ยอดขายแสกน</h5>
        <p class="fs-4 text-primary"><?= number_format($summary['total_scan'], 2) ?> บาท</p>
      </div>
    </div>

    <!-- กราฟยอดขาย -->
    <div class="row mt-5">
      <div class="col-md-12">
        <canvas id="salesChart"></canvas>
      </div>
    </div>

    <!-- ตารางรายการขาย -->
    <div class="table-responsive mt-4">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>วันที่-เวลา</th>
            <th>ชื่อสินค้า</th>
            <th>จำนวนตั๋ว</th>
            <th>ราคารวม</th>
            <th>วิธีชำระเงิน</th>
            <th>ลบ</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $salesResult->fetch_assoc()): ?>
            <tr id="row-<?= $row['id'] ?>">
              <td><?= $row['id'] ?></td>
              <td><?= $row['created_at'] ?></td>
              <td><?= htmlspecialchars($row['product_name']) ?></td>
              <td><?= $row['quantity'] ?></td>
              <td><?= number_format($row['total_price'], 2) ?></td>
              <td><?= $row['payment_method'] ?></td>
              <td>
                <button class="btn btn-danger btn-sm" onclick="deleteSale(<?= $row['id'] ?>)">
                  ลบ
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php include 'footer.php'; ?>
  <script>
  // สร้างกราฟ
  const ctx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(ctx, {
    type: 'line',  // ใช้กราฟเส้น
    data: {
      labels: ['เงินสด', 'แสกน'],  // แท็กสำหรับกราฟ
      datasets: [
        {
          label: 'ยอดขายเงินสด',  // ชื่อของเส้นกราฟเงินสด
          data: [0, <?= $summary['total_cash'] ?>],  // เริ่มจาก 0 แล้วขึ้นไปยอดขายเงินสด
          backgroundColor: 'rgba(40, 167, 69, 0.2)',  // สีพื้นหลังของกราฟเงินสด
          borderColor: '#28a745',  // สีเส้นกราฟเงินสด
          borderWidth: 2,
          fill: true,  // กราฟมีพื้นหลัง
          tension: 0.4  // ความโค้งของเส้น
        },
        {
          label: 'ยอดขายแสกน',  // ชื่อของเส้นกราฟแสกน
          data: [0, <?= $summary['total_scan'] ?>],  // เริ่มจาก 0 แล้วขึ้นไปยอดขายแสกน
          backgroundColor: 'rgba(0, 123, 255, 0.2)',  // สีพื้นหลังของกราฟแสกน
          borderColor: '#007bff',  // สีเส้นกราฟแสกน
          borderWidth: 2,
          fill: true,  // กราฟมีพื้นหลัง
          tension: 0.4  // ความโค้งของเส้น
        }
      ]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true  // เริ่มที่ 0
        }
      },
      responsive: true  // กราฟจะตอบสนองต่อขนาดหน้าจอ
    }
  });

  // ฟังก์ชั่นลบรายการ
  function deleteSale(id) {
    Swal.fire({
      title: 'ยืนยันการลบ?',
      text: "คุณต้องการลบรายการนี้หรือไม่",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#aaa',
      confirmButtonText: 'ใช่!',
      cancelButtonText: 'ยกเลิก'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('delete_sale.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            document.getElementById('row-' + id).remove();
            Swal.fire('ลบแล้ว!', 'รายการถูกลบเรียบร้อยแล้ว', 'success');
          } else {
            Swal.fire('ผิดพลาด!', 'ไม่สามารถลบได้', 'error');
          }
        });
      }
    });
  }
</script>



</body>
</html>
