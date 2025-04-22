# Hệ Thống Quản Lý Bán Hàng

Ứng dụng web để quản lý sản phẩm, đơn hàng và báo cáo bán hàng.

## Tính Năng

### Quản lý sản phẩm
- Xem danh sách sản phẩm với phân trang
- Thêm sản phẩm mới với thông tin chi tiết
- Chỉnh sửa thông tin sản phẩm
- Theo dõi tồn kho sản phẩm
- Lọc sản phẩm theo danh mục

### Quản lý đơn hàng
- Xem danh sách đơn hàng với phân trang
- Tạo đơn hàng mới cho khách hàng
- Thêm nhiều sản phẩm vào đơn hàng
- Xem chi tiết đơn hàng
- In hóa đơn đơn hàng

### Quản lý khách hàng
- Lưu trữ thông tin khách hàng
- Xem lịch sử mua hàng của khách hàng

### Báo cáo và thống kê
- Báo cáo doanh số bán hàng
- Thống kê theo ngày/tháng/năm
- Báo cáo mua hàng của khách hàng
- Lọc báo cáo theo khoảng thời gian

### Hệ thống xác thực
- Đăng nhập an toàn
- Phân quyền người dùng
- Đăng xuất

### Tiện ích
- Giao diện thân thiện, đáp ứng
- Phân trang trong tất cả các danh sách
- In hóa đơn/báo cáo
- Tìm kiếm và lọc dữ liệu

### Component
- **Pagination**: Component phân trang có thể tái sử dụng, hỗ trợ điều hướng First/Previous/Next/Last
  - Sử dụng: Đơn giản hóa việc hiển thị điều khiển phân trang trên các trang danh sách
  - Ví dụ: `<?php include VIEWS_PATH . '/components/pagination.php'; ?>`
- **Invoice Template**: Template hóa đơn có thể in
  - Sử dụng: In hóa đơn đẹp, chuyên nghiệp cho đơn hàng

## Yêu Cầu Hệ Thống

- PHP 5.6+
- MySQL 5.0+
- Máy chủ web (Apache/Nginx)
- XAMPP/WAMP/LAMP (cho môi trường phát triển)

## Cài Đặt

1. Clone hoặc tải xuống mã nguồn
   ```
   git clone https://github.com/thekids1002/sales_managerment.git
   ```
   Hoặc giải nén file zip vào thư mục web server của bạn (htdocs nếu dùng XAMPP).

2. Tạo cơ sở dữ liệu
   - Tạo một cơ sở dữ liệu MySQL mới
   - Nhập file SQL từ thư mục `database/` vào cơ sở dữ liệu đã tạo

3. Cấu hình kết nối cơ sở dữ liệu
   - Mở file `config/database.php`
   - Chỉnh sửa các thông số kết nối:
   ```php
   <?php
   return [
       'host'     => 'localhost',     // Địa chỉ máy chủ MySQL
       'username' => 'root',          // Tên người dùng MySQL (mặc định: root)
       'password' => '',              // Mật khẩu MySQL (mặc định trống cho XAMPP)
       'database' => 'sale_management', // Tên cơ sở dữ liệu đã tạo
       'charset'  => 'utf8mb4',       // Bộ ký tự, giữ nguyên nếu không cần thiết
       'port'     => 3306             // Cổng MySQL (mặc định: 3306)
   ];
   ```
   
4. Cấu hình URL cơ sở
   - Mở file `config/config.php`
   - Sửa giá trị `base_url` thành URL của ứng dụng web:
   ```php
   <?php
   return [
       // URL cơ sở của ứng dụng
       'base_url' => 'http://localhost/sale_managerment', // Thay đổi theo cấu hình máy chủ web
       
       // Các cài đặt khác... (giữ nguyên)
   ];
   ```
   - Nếu bạn cài đặt trong một thư mục khác, hãy điều chỉnh URL tương ứng
   - Ví dụ: `http://localhost/my-folder/sale_managerment`

5. Phân quyền thư mục
   - Đảm bảo thư mục `logs/` có quyền ghi

6. Truy cập ứng dụng
   - Mở trình duyệt web và truy cập URL bạn đã cấu hình ở bước 4
   - Ví dụ: `http://localhost/sale_managerment`

## Thông Tin Đăng Nhập Mặc Định

- Tên đăng nhập: admin
- Mật khẩu: admin123

## Cấu Trúc Dự Án

```
sale_managerment/
├── app/                  # Mã nguồn ứng dụng
│   ├── controllers/      # Các lớp điều khiển
│   ├── models/           # Các lớp model
│   └── views/            # Các template giao diện
├── config/               # Tệp cấu hình
│   ├── config.php        # Cấu hình ứng dụng (base_url,...)
│   └── database.php      # Cấu hình cơ sở dữ liệu
├── database/             # Tệp SQL và migration
├── logs/                 # Tệp log
├── public/               # Tệp truy cập công khai
│   ├── css/              # Tệp CSS
│   ├── js/               # Tệp JavaScript
│   └── index.php         # Điểm vào
├── src/                  # Tệp hệ thống cốt lõi
└── README.md             # Tài liệu dự án
```

## Xử Lý Sự Cố

- **Lỗi kết nối cơ sở dữ liệu**: Kiểm tra lại thông tin trong `config/database.php`
- **Lỗi đường dẫn**: Đảm bảo `base_url` được cấu hình chính xác trong `config/config.php`
- **Lỗi quyền truy cập**: Kiểm tra quyền ghi cho thư mục `logs/`
- **Trang trắng**: Kiểm tra log lỗi trong thư mục `logs/` hoặc log của máy chủ web