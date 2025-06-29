<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $data = json_decode(file_get_contents('php://input'), true);

  $product = $data['product'];
  $qty = $data['qty'];
  $total = $data['total'];
  $method = $data['method'];

  $sql = "INSERT INTO sales (product_name, quantity, total_price, payment_method) VALUES (?, ?, ?, ?)";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sids", $product, $qty, $total, $method);
  $stmt->execute();

  echo json_encode(['status' => 'success']);
  exit(); // หยุดไม่ให้แสดง HTML ด้านล่าง
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <title>POS ขายตั๋วทำบุญ</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/235/235873.png" type="image/x-icon">
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

  <!-- Font Prompt -->
  <link href="https://fonts.googleapis.com/css2?family=Prompt&display=swap" rel="stylesheet">

  <!-- SweetAlert2 -->
  <link rel="manifest" href="/manifest.json">

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
   body {
  font-family: 'Prompt', sans-serif;  
  background: linear-gradient(to right, #e3f2fd, #ffffff);
  padding: 40px;
  color: #333;
}

h1 {
  font-weight: 600;
  color: #007bff;
}

.product-card {
  border: 2px solid #dee2e6;
  border-radius: 1rem;
  padding: 10px;
  transition: 0.3s;
  background-color: white;
  box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

.product-card:hover {
  transform: scale(1.03);
  box-shadow: 0 8px 20px rgba(0,0,0,0.1);
}

.product-icon {
  font-size: 80px;
  color: #007bff;
  cursor: pointer;
  transition: transform 0.2s;
}

.product-icon:hover {
  transform: scale(1.1);
  color: #0056b3;
}

.quantity-box {
  font-size: 2.5rem;
  font-weight: bold;
  width: 100px;
  height: 70px;
  line-height: 70px;
  background-color: #f8f9fa;
  border-radius: 10px;
  border: 1px solid #ced4da;
}

.numpad button {
  width: 100%;
  font-size: 1.5rem;
  padding: 20px;
  margin-bottom: 10px;
  border-radius: 12px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  transition: background-color 0.2s, transform 0.2s;
}

.numpad button:hover {
  background-color: #e2e6ea;
  transform: translateY(-2px);
}
.product-card img {
  max-width: 100%;   /* ให้รูปภาพมีขนาดพอดีกับขนาดกล่อง */
  max-height: 150px; /* ปรับขนาดสูงสุดที่ต้องการ */
  object-fit: contain; /* ให้การแสดงผลของภาพไม่ถูกตัด */
  border-radius: 8px;  /* ทำมุมให้โค้งนิดหน่อย */
}


  </style>
</head>
<body>
<?php include 'nav.php'; ?>

<br>
  <div class="container text-center">
    <h1 class="mb-4">วัดคลองเจริญ</h1>

    <!-- Icon สินค้า -->
    <div class="col-6 col-md-4 col-lg-3">
  <div class="product-card" onclick="openModal('ตั๋วจับฉลาก', 20)">
    <img src="t.png" alt="">
    <p class="mt-3"><b>ตั๋ว 20บาท</b></p>
  </div>
  
</div>

  </div>

  <!-- Modal เลือกจำนวน -->
  <div class="modal fade" id="ticketModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">เลือกจำนวน</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">

          <div class="text-center">
            <div class="d-flex justify-content-center align-items-center mb-3">
              <button class="btn btn-danger me-2" onclick="adjustQty(-1)">
                <i class="fas fa-minus"></i>
              </button>
              <div class="quantity-box" id="ticketQty">1</div>
              <button class="btn btn-success ms-2" onclick="adjustQty(1)">
                <i class="fas fa-plus"></i>
              </button>
            </div>

            <h2>ราคาทั้งหมด: <b><span id="totalPrice">20</span></b> บาท</h2>

            <!-- Numpad -->
            <div class="row numpad">
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(1)">1</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(2)">2</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(3)">3</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(4)">4</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(5)">5</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(6)">6</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(7)">7</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(8)">8</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(9)">9</button></div>
              <div class="col-4"><button class="btn btn-danger" onclick="clearQty()">ลบ</button></div>
              <div class="col-4"><button class="btn btn-light" onclick="inputNumber(0)">0</button></div>
              <div class="col-4"><button class="btn btn-primary" onclick="confirmOrder()">ยืนยัน</button></div>
            </div>

          </div>

        </div>
      </div>
    </div>
  </div>
  <?php include 'footer.php'; ?>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  let currentProduct = '';
  let pricePerUnit = 0;
  let qty = 1;

  const ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));

  function openModal(productName, price) {
    currentProduct = productName;
    pricePerUnit = price;
    qty = 1;
    document.getElementById('modalTitle').textContent = productName;
    document.getElementById('ticketQty').textContent = qty;
    document.getElementById('totalPrice').textContent = qty * price;
    ticketModal.show();
  }

  function adjustQty(change) {
    qty += change;
    if (qty < 1) qty = 1;
    updateDisplay();
  }

  function updateDisplay() {
    document.getElementById('ticketQty').textContent = qty;
    document.getElementById('totalPrice').textContent = qty * pricePerUnit;
  }

  function inputNumber(num) {
    if (qty.toString().length >= 3) return;
    if (qty === 0) {
      qty = num;
    } else {
      qty = parseInt(qty.toString() + num);
    }
    updateDisplay();
  }

  function clearQty() {
    qty = 0;
    updateDisplay();
  }

  function confirmOrder() {
    ticketModal.hide();

    // แสดงการเลือกวิธีการชำระเงิน
    Swal.fire({
      title: 'เลือกวิธีชำระเงิน',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'เงินสด 💵',
      cancelButtonText: 'แสกน QR 📱',
      reverseButtons: true,
      showCloseButton: true, // เพิ่มปุ่ม x สำหรับปิด
      closeButtonHtml: '&times;', // ปรับให้เป็นตัว x
    }).then((result) => {
      let method = result.isConfirmed ? 'เงินสด' : 'แสกน';  // เลือกวิธีการชำระเงิน
      let orderData = {
        product: currentProduct,
        qty: qty,
        total: qty * pricePerUnit,
        method: method
      };

      // ส่งข้อมูลไปยัง PHP
      fetch(window.location.href, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(orderData)
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: `ชำระด้วย ${method}`,
            html: `คุณซื้อ ${qty} x ${currentProduct} = <b>${qty * pricePerUnit} บาท</b>`,
            showConfirmButton: false,
            timer: 1500
          });
        }
      });
    });
  }
</script>


</body>
</html>
