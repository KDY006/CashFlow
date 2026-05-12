# 💰 CashFlow - Hệ thống Quản lý Chi tiêu tích hợp AI

Hệ thống Quản lý Chi tiêu tích hợp trí tuệ nhân tạo (Google Gemini AI) để phân tích thói quen tiêu dùng và đưa ra lời khuyên tài chính.

*   **Tên dự án**: CashFlow - Smart Financial Management
*   **Kho lưu trữ (GitHub)**: [https://github.com/KDY006/CashFlow.git](https://github.com/KDY006/CashFlow.git)
*   **Công nghệ sử dụng**: PHP 8.x, MySQL, Python (Flask), Gemini AI.

---

## 1. YÊU CẦU MÔI TRƯỜNG CÀI ĐẶT
*   **PHP**: Phiên bản >= 8.1 (Do sử dụng cú pháp mới và định kiểu nghiêm ngặt).
*   **Cơ sở dữ liệu**: MySQL / MariaDB (Khuyên dùng XAMPP bản mới nhất).
*   **Python**: Phiên bản >= 3.9 (Để chạy dịch vụ AI).
*   **Trình duyệt**: Google Chrome, Microsoft Edge, Safari.

---

## 2. CÁC BƯỚC CÀI ĐẶT CHI TIẾT

### Bước 1: Đưa mã nguồn vào Web Server
*   Copy toàn bộ thư mục `CashFlow` vào thư mục `C:\xampp\htdocs\`.
*   Mã nguồn thực thi nằm trong thư mục `source/`.
*   Đường dẫn truy cập chính: [http://localhost/CashFlow/source/](http://localhost/CashFlow/source/)

### Bước 2: Khởi động máy chủ ảo
*   Mở **XAMPP Control Panel**.
*   Nhấn **Start** cho 2 module là **Apache** và **MySQL**.

### Bước 3: Thiết lập Cơ sở dữ liệu (Database)
*   Truy cập: [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
*   Tạo database mới với tên chính xác: `cashflow_db` (Bảng mã `utf8mb4_general_ci`).
*   Chọn database vừa tạo, nhấn vào tab **Import**, chọn file `source/database/cashflow_db.sql` và nhấn **Go**.

### Bước 4: Cấu hình môi trường (.env)
*   Tìm file `.env.example` tại thư mục `source/` và đổi tên thành `.env`.
*   **Cấu hình Database**: Mặc định là `DB_USER=root` và `DB_PASS=` (Rỗng).
*   **Cấu hình AI**:
    1. Truy cập [Google AI Studio](https://aistudio.google.com/) để lấy **API Key**.
    2. Điền API Key vào dòng `GEMINI_API_KEY=`.
    3. Điền tên model vào dòng `GEMINI_API_NAME='gemini-3.1-flash-lite'`. 
*   **Cấu hình Email**: Nhóm đã tích hợp sẵn tài khoản email test để gửi thư xác thực. Bạn có thể giữ nguyên để kiểm tra chức năng này.

### Bước 5: Chạy dịch vụ AI (Python Server)
Dịch vụ này xử lý các yêu cầu chatbot và phân tích dữ liệu.
1.  Mở Terminal (trong VS Code hoặc CMD) tại thư mục `source/CashFlow_AI_Service`.
2.  Cài đặt thư viện: `pip install flask google-genai python-dotenv`.
3.  Chạy server: `python app.py`.
    > **⚠️ Lưu ý**: Phải giữ cửa sổ Terminal này luôn mở khi sử dụng ứng dụng.

---

## 3. HƯỚNG DẪN ĐĂNG NHẬP VÀ TEST HỆ THỐNG
Nhóm đã chuẩn bị sẵn 2 tài khoản với dữ liệu mẫu để thuận tiện cho việc chấm điểm:

| VAI TRÒ | EMAIL ĐĂNG NHẬP | MẬT KHẨU |
| :--- | :--- | :--- |
| **QUẢN TRỊ VIÊN** | `nvduy180706@gmail.com` | `123456` |
| **NGƯỜI DÙNG** | `kdyforwork@gmail.com` | `123456` |

*   **Đường dẫn truy cập**: [http://localhost/CashFlow/source/](http://localhost/CashFlow/source/)

---

## 4. GHI CHÚ QUAN TRỌNG VỀ LUỒNG TEST BẢO MẬT
*   **Xác thực Email**: Khi đăng ký hoặc Admin thêm nhân viên mới, hệ thống sẽ gửi một email thực tế chứa link kích hoạt. Link này chỉ có hiệu lực trong **5 phút**.
*   **Ép đổi mật khẩu**: Người dùng mới truy cập qua link email lần đầu sẽ được yêu cầu thiết lập mật khẩu mới ngay lập tức để đảm bảo an toàn.
*   **Token AI**: Hệ thống giới hạn mỗi người dùng chỉ có **3 lượt** phân tích chuyên sâu mỗi 24 giờ để tối ưu hóa tài nguyên.

---

*Trân trọng cảm ơn Thầy/Cô đã dành thời gian đánh giá sản phẩm của nhóm!*