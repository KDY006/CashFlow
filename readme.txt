====================================================================
HƯỚNG DẪN CÀI ĐẶT VÀ SỬ DỤNG HỆ THỐNG CASHFLOW
====================================================================
- Tên dự án: CashFlow - Quản lý Chi tiêu thông minh tích hợp AI
- Nhóm thực hiện: CashFlow Team
- Kho lưu trữ: https://github.com/KDY006/CashFlow.git

--------------------------------------------------------------------
1. YÊU CẦU MÔI TRƯỜNG CÀI ĐẶT
--------------------------------------------------------------------
- Nền tảng: PHP >= 8.1, MySQL/MariaDB.
- Python: >= 3.9 (để chạy server AI).
- Máy chủ ảo: Khuyến nghị sử dụng XAMPP bản mới nhất.

--------------------------------------------------------------------
2. CÁC BƯỚC CÀI ĐẶT CHI TIẾT
--------------------------------------------------------------------
Bước 1: Đưa mã nguồn vào Web Server
- Giải nén đồ án vào thư mục C:\xampp\htdocs\CashFlow\.
- Cấu trúc nộp bài gồm thư mục "source" chứa mã nguồn thực thi.

Bước 2: Khởi động máy chủ ảo
- Mở XAMPP Control Panel.
- Bấm "Start" cho module Apache và MySQL.

Bước 3: Thiết lập Cơ sở dữ liệu (Database)
- Truy cập: http://localhost/phpmyadmin
- Tạo Database mới tên là: cashflow_db (utf8mb4_general_ci).
- Nhập file "cashflow_db.sql" (nằm trong source/database/).

Bước 4: Cấu hình môi trường (.env)
- Vào thư mục "source", đổi tên file ".env.example" thành ".env".
- Mở file .env và điền GEMINI_API_KEY (lấy từ Google AI Studio).

Bước 5: Chạy AI Service (Python)
- Mở Terminal tại thư mục source/CashFlow_AI_Service/.
- Chạy lệnh: pip install flask google-genai python-dotenv
- Chạy lệnh: python app.py (Giữ cửa sổ này luôn mở).

--------------------------------------------------------------------
3. HƯỚNG DẪN ĐĂNG NHẬP VÀ TEST HỆ THỐNG
--------------------------------------------------------------------
* ĐƯỜNG DẪN TRUY CẬP: http://localhost/CashFlow/source/

Nhóm đã chuẩn bị sẵn 2 tài khoản test:

[TÀI KHOẢN 1] - QUẢN TRỊ VIÊN
- Email: nvduy180706@gmail.com
- Mật khẩu: 123456

[TÀI KHOẢN 2] - NGƯỜI DÙNG
- Email: kdyforwork@gmail.com
- Mật khẩu: 123456

--------------------------------------------------------------------
4. GHI CHÚ BẢO MẬT
--------------------------------------------------------------------
- Hệ thống có tính năng gửi email xác thực và link kích hoạt tài khoản có thời hạn (5 phút).
- Người dùng mới bắt buộc phải đổi mật khẩu ở lần đăng nhập đầu tiên.

====================================================================
Kính chúc Thầy/Cô có trải nghiệm tốt nhất với sản phẩm của nhóm!