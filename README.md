# HUIT Schedule Hub

Ứng dụng web hỗ trợ Phòng Đào tạo quản lý và phân lịch giảng dạy đa cơ sở một cách thông minh. Hệ thống giúp tối ưu hóa thời gian di chuyển của giảng viên và cung cấp cổng tra cứu lịch dạy trực tuyến.

## Tính năng chính

- **Quản lý đa cơ sở**  
  Theo dõi và quản lý thông tin chi tiết các cơ sở giảng dạy của trường.

- **Quản lý giảng viên**  
  Lưu trữ thông tin cá nhân, liên hệ và chuyên môn của từng giảng viên.

- **Phân lịch thông minh & Chống trùng lịch**  
  Tự động kiểm tra thời gian biểu (thứ, tiết, buổi) của giảng viên và cảnh báo khi xảy ra chồng chéo lịch dạy.

- **Bộ lọc trực quan**  
  Hỗ trợ lọc nhanh thời khóa biểu theo Cơ sở hoặc theo Giảng viên.

## Yêu cầu hệ thống

- PHP >= 8.0
- Composer
- MySQL / MariaDB (khuyến nghị sử dụng Laragon hoặc XAMPP)

### Thư viện sử dụng (Composer)

| Package              | Mục đích                                      |
|----------------------|-----------------------------------------------|
| `vlucas/phpdotenv`   | Quản lý biến môi trường cấu hình Database     |
| `fakerphp/faker`     | Sinh dữ liệu mẫu cho giảng viên và lịch dạy   |

## Hướng dẫn cài đặt

### 1. Clone dự án

```bash
git clone <repository-url> huit-schedule
cd huit-schedule
```

Hoặc tải mã nguồn về thư mục web server (ví dụ: `laragon/www/huit-schedule`).

### 2. Cài đặt thư viện

```bash
composer install
```

### 3. Cấu hình môi trường

Sao chép file mẫu:

```bash
cp .env.example .env
```

Cập nhật thông tin kết nối database trong file `.env`:

```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=huit_db
DB_USER=root
DB_PASS=
```

### 4. Khởi tạo cơ sở dữ liệu

1. Tạo database mới tên `huit_db` trên MySQL.
2. Tạo các bảng: `branches`, `teachers`, `schedules` theo cấu trúc của dự án.
3. Chạy file `seed.php` để tạo dữ liệu mẫu:

```bash
php seed.php
```

### 5. Chạy ứng dụng

Truy cập ứng dụng qua trình duyệt:

- `http://localhost:8080/huit-schedule/`
- hoặc `http://huit-schedule.test` (nếu dùng virtual host)

## License

Dự án này được phát hành dưới giấy phép **MIT License**. Xem file [LICENSE](LICENSE) để biết thêm chi tiết.
```