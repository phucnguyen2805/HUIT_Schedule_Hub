<?php
require_once 'db.php';
$pdo = getDBConnection();

// Xử lý thêm lịch dạy mới kèm kiểm tra trùng lịch (Logic thông minh)
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $teacher_id = $_POST['teacher_id'];
    $branch_id = $_POST['branch_id'];
    $subject_name = $_POST['subject_name'];
    $room = $_POST['room'];
    $day_of_week = $_POST['day_of_week'];
    $shift_or_periods = $_POST['shift_or_periods'];

    // Kiểm tra xem giảng viên đã có lịch dạy vào Thứ và Tiết đó chưa (tránh chồng chéo)
    $checkStmt = $pdo->prepare("SELECT * FROM schedules WHERE teacher_id = ? AND day_of_week = ? AND shift_or_periods = ?");
    $checkStmt->execute([$teacher_id, $day_of_week, $shift_or_periods]);
    
    if ($checkStmt->rowCount() > 0) {
        $message = "❌ Cảnh báo: Giảng viên này đã có lịch dạy vào thời gian này (Trùng lịch)!";
    } else {
        $ins = $pdo->prepare("INSERT INTO schedules (teacher_id, branch_id, subject_name, room, day_of_week, shift_or_periods) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$teacher_id, $branch_id, $subject_name, $room, $day_of_week, $shift_or_periods]);
        $message = "✅ Phân lịch thành công!";
    }
}

// Lấy danh sách cơ sở và giảng viên cho bộ lọc và form
$branches = $pdo->query("SELECT * FROM branches")->fetchAll();
$teachers = $pdo->query("SELECT * FROM teachers")->fetchAll();

// Bộ lọc lịch theo Cơ sở hoặc Giảng viên
$filter_branch = $_GET['branch_id'] ?? '';
$filter_teacher = $_GET['teacher_id'] ?? '';

$query = "SELECT s.*, t.full_name, b.branch_name FROM schedules s 
          JOIN teachers t ON s.teacher_id = t.id 
          JOIN branches b ON s.branch_id = b.id WHERE 1=1";
$params = [];

if (!empty($filter_branch)) {
    $query .= " AND s.branch_id = ?";
    $params[] = $filter_branch;
}
if (!empty($filter_teacher)) {
    $query .= " AND s.teacher_id = ?";
    $params[] = $filter_teacher;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$schedules = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>HUIT Schedule Hub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h1 class="mb-4 text-primary">HUIT Schedule Hub - Quản lý Lịch Giảng Dạy Đa Cơ Sở</h1>

    <?php if($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <!-- Form Thêm Lịch Dạy -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-secondary text-white">Thêm Lịch Giảng Dạy Mới</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Giảng Viên</label>
                    <select name="teacher_id" class="form-select" required>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['full_name'] ?> (<?= $t['expertise'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Cơ Sở</label>
                    <select name="branch_id" class="form-select" required>
                        <?php foreach($branches as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= $b['branch_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tên Học Phần</label>
                    <input type="text" name="subject_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Phòng Học</label>
                    <input type="text" name="room" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Thứ trong tuần</label>
                    <select name="day_of_week" class="form-select">
                        <option value="Thứ 2">Thứ 2</option>
                        <option value="Thứ 3">Thứ 3</option>
                        <option value="Thứ 4">Thứ 4</option>
                        <option value="Thứ 5">Thứ 5</option>
                        <option value="Thứ 6">Thứ 6</option>
                        <option value="Thứ 7">Thứ 7</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tiết học / Buổi</label>
                    <input type="text" name="shift_or_periods" class="form-control" placeholder="VD: Tiết 1-3 (Sáng)" required>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="add_schedule" class="btn btn-success w-100">Phân Lịch</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bộ lọc nhanh -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-4">
            <select name="branch_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Lọc theo tất cả Cơ Sở --</option>
                <?php foreach($branches as $b): ?>
                    <option value="<?= $b['id'] ?>" <?= $filter_branch == $b['id'] ? 'selected':'' ?>><?= $b['branch_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="teacher_id" class="form-select" onchange="this.form.submit()">
                <option value="">-- Lọc theo tất cả Giảng Viên --</option>
                <?php foreach($teachers as $t): ?>
                    <option value="<?= $t['id'] ?>" <?= $filter_teacher == $t['id'] ? 'selected':'' ?>><?= $t['full_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <!-- Bảng hiển thị thời khóa biểu -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Giảng Viên</th>
                <th>Cơ Sở</th>
                <th>Học Phần</th>
                <th>Phòng</th>
                <th>Thứ</th>
                <th>Tiết / Buổi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($schedules) > 0): ?>
                <?php foreach($schedules as $sch): ?>
                    <tr>
                        <td><?= $sch['id'] ?></td>
                        <td><?= htmlspecialchars($sch['full_name']) ?></td>
                        <td><?= htmlspecialchars($sch['branch_name']) ?></td>
                        <td><?= htmlspecialchars($sch['subject_name']) ?></td>
                        <td><?= htmlspecialchars($sch['room']) ?></td>
                        <td><?= htmlspecialchars($sch['day_of_week']) ?></td>
                        <td><?= htmlspecialchars($sch['shift_or_periods']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">Không có lịch dạy nào phù hợp.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>