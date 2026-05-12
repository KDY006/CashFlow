====================================================================
HƯỚNG DẪN CÀI ĐẶT VÀ SỬ DỤNG HỆ THỐNG 3D PHONE POS
====================================================================
- Tên dự án: Hệ thống Quản lý Bán hàng Điện thoại 3D Phone POS
- Nhóm thực hiện: 3D
- Mã nhóm: DAW14
- Link Video Demo: https://www.youtube.com/watch?v=2QCHeJnQZRg
- Link Kho lưu trữ (GitLab): https://gitlab.duthu.net/S52400010/3d-phone-pos.git

--------------------------------------------------------------------
1. YÊU CẦU MÔI TRƯỜNG CÀI ĐẶT
--------------------------------------------------------------------
- Nền tảng: Hệ thống yêu cầu PHP >= 8.0 (Do sử dụng cú pháp mới của PHP 8) và MySQL/MariaDB.
- Máy chủ ảo: Khuyến nghị sử dụng XAMPP / WAMP / Laragon bản mới nhất.
- Trình duyệt: Google Chrome, Microsoft Edge, Safari.

--------------------------------------------------------------------
2. CÁC BƯỚC CÀI ĐẶT CHI TIẾT
--------------------------------------------------------------------
Bước 1: Tải mã nguồn về máy
Giáo viên có thể chọn 1 trong 2 cách sau để lấy toàn bộ dự án về máy:

  - CÁCH 1 (Dùng Git): Mở Terminal / Command Prompt (CMD) và chạy lệnh:
    cd C:\xampp\htdocs
    git clone https://gitlab.duthu.net/S52400010/3d-phone-pos.git repo-3d-web

  - CÁCH 2 (Tải file nén ZIP): Giải nén file đồ án nộp trên hệ thống ra một thư mục bất kỳ.

Bước 2: Đưa mã nguồn vào Web Server
- Cấu trúc dự án của nhóm bao gồm các file tài liệu, sơ đồ (diagram) và thư mục source code thực thi.
- Giáo viên vui lòng vào bên trong thư mục dự án vừa tải về, COPY duy nhất thư mục có tên là "source".
- Dán thư mục "source" đó vào đường dẫn gốc của máy chủ ảo (C:\xampp\htdocs\).
- Đổi tên thư mục vừa dán từ "source" thành "3d-phone-pos".

Bước 3: Khởi động máy chủ ảo
- Mở XAMPP Control Panel.
- Bấm "Start" cho 2 module là "Apache" và "MySQL".

Bước 4: Thiết lập Cơ sở dữ liệu (Database)
- Truy cập trình quản lý CSDL tại trình duyệt: 
  http://localhost/phpmyadmin
- Tạo một Database mới với tên chính xác là: 3d_phone_pos
  (Collation chọn: utf8mb4_general_ci).
- Bấm vào Database vừa tạo, chọn tab "Import" (Nhập).
- Bấm "Choose File" và chọn file "3d_phone_pos.sql" (File này nằm trong thư mục C:\xampp\htdocs\3d-phone-pos\database\).
- Nhấn "Import" (hoặc Go) ở cuối trang để nạp dữ liệu.

Bước 5: Cấu hình môi trường (.env)
- Tại thư mục project (C:\xampp\htdocs\3d-phone-pos\), tìm file ".env.example".
- Đổi tên file này thành ".env".
- Lưu ý về cấu hình bên trong file .env:
  + [Database]: Hệ thống đã cấu hình sẵn DB_USER=root và DB_PASS= rỗng, hoàn toàn khớp với mặc định của XAMPP. Giáo viên không cần sửa gì thêm.
  + [SMTP Email]: Nhóm đã tích hợp sẵn tài khoản email test của nhóm để gửi thư cấp tài khoản tự động. Giáo viên có thể giữ nguyên để test ngay lập tức chức năng này mà không cần thiết lập App Password của riêng mình.

--------------------------------------------------------------------
3. HƯỚNG DẪN ĐĂNG NHẬP VÀ TEST HỆ THỐNG
--------------------------------------------------------------------
* ĐƯỜNG DẪN TRUY CẬP HỆ THỐNG: 
  http://localhost/3d-phone-pos/

Để thuận tiện cho việc chấm điểm, nhóm đã chuẩn bị sẵn 2 tài khoản với 2 vai trò khác nhau:

[TÀI KHOẢN 1] - QUẢN TRỊ VIÊN (ADMIN - Toàn quyền hệ thống)
- Tên đăng nhập : admin
- Mật khẩu      : admin

[TÀI KHOẢN 2] - NHÂN VIÊN BÁN HÀNG (SALES - Quyền giới hạn)
- Tên đăng nhập : bongrong009kk
- Mật khẩu      : 123456789

--------------------------------------------------------------------
4. GHI CHÚ QUAN TRỌNG VỀ LUỒNG TEST BẢO MẬT
--------------------------------------------------------------------
- Luồng cấp tài khoản mới: Khi Admin sử dụng chức năng "Thêm Nhân viên", hệ thống sẽ gửi một email thực tế chứa link đăng nhập an toàn đến email của nhân viên. Link này chỉ có hiệu lực đúng 1 PHÚT.
- Mật khẩu mặc định: Mật khẩu mặc định cho nhân viên mới ở lần đăng nhập đầu tiên luôn là Mã số sinh viên của trưởng nhóm: 52400010.
- Cơ chế ép đổi mật khẩu: Ngay khi click vào link và đăng nhập bằng mật khẩu mặc định, hệ thống sẽ khóa các chức năng khác và nhân viên phải đổi mật khẩu mới để đảm bảo an toàn hệ thống.

====================================================================
Kính chúc Thầy/Cô có trải nghiệm tốt nhất với sản phẩm của nhóm!