<?php
require_once 'db.php';
$faker = Faker\Factory::create('vi_VN');
$pdo = getDBConnection();

// 1. Thêm Cơ sở mẫu
$branches = [
    ['branch_name' => 'Cơ sở chính Tân Phú', 'address' => '140 Lê Trọng Tấn, Tân Phú, TP.HCM'],
    ['branch_name' => 'Trung tâm Liên kết Quận 1', 'address' => 'Nguyễn Văn Cừ, Quận 1, TP.HCM']
];

foreach ($branches as $b) {
    $stmt = $pdo->prepare("INSERT INTO branches (branch_name, address) VALUES (?, ?)");
    $stmt->execute([$b['branch_name'], $b['address']]);
}

// 2. Thêm Giảng viên mẫu
for ($i = 0; $i < 5; $i++) {
    $stmt = $pdo->prepare("INSERT INTO teachers (full_name, email, phone, expertise) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $faker->name,
        $faker->unique()->email,
        $faker->phoneNumber,
        $faker->randomElement(['Công nghệ Web', 'Cơ sở dữ liệu', 'Lập trình OOP', 'Trí tuệ nhân tạo'])
    ]);
}

echo "<h3>Đã sinh dữ liệu mẫu bằng Faker thành công! Hãy F5 lại trang quản lý.</h3>";