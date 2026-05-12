# 💰 CashFlow - Hệ thống Quản lý Chi tiêu thông minh tích hợp AI

Chào mừng bạn đến với dự án **CashFlow**. Đây là hệ thống quản lý tài chính cá nhân được tích hợp trí tuệ nhân tạo (Gemini AI) để giúp người dùng theo dõi thu chi và nhận các lời khuyên tài chính chuyên sâu.

Dưới đây là hướng dẫn chi tiết từng bước để cài đặt và vận hành dự án trên máy tính cục bộ.

---

## 🛠 1. Yêu cầu hệ thống
Trước khi bắt đầu, hãy đảm bảo máy tính của bạn đã cài đặt các công cụ sau:
*   **XAMPP**: Cần thiết để chạy PHP (>= 8.1) và MySQL.
*   **Composer**: Công cụ quản lý thư viện cho PHP. [Tải về tại đây](https://getcomposer.org/).
*   **Python**: Phiên bản >= 3.9 (Cần thiết để chạy Server AI).
*   **Git**: (Tùy chọn) Để tải mã nguồn.

---

## 🚀 2. Các bước cài đặt chi tiết

### Bước 1: Sao chép dự án vào Web Server
1.  Giải nén thư mục dự án (hoặc `git clone`) vào đường dẫn: `C:\xampp\htdocs\CashFlow\`.
2.  Đảm bảo cấu trúc thư mục có dạng: `CashFlow/source/`, `CashFlow/README.md`, v.v.

### Bước 2: Cài đặt thư viện PHP (Composer)
1.  Mở terminal (CMD hoặc PowerShell) và di chuyển vào thư mục `source`:
    ```bash
    cd C:\xampp\htdocs\CashFlow\source
    ```
2.  Chạy lệnh sau để cài đặt các thư viện cần thiết:
    ```bash
    composer install
    ```

### Bước 3: Khởi động XAMPP
1.  Mở ứng dụng **XAMPP Control Panel**.
2.  Nhấn nút **Start** cho hai module: **Apache** và **MySQL**.

### Bước 4: Thiết lập Cơ sở dữ liệu (Database)
1.  Truy cập vào trình quản lý database: [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/).
2.  Nhấn vào nút **"New"** ở cột bên trái để tạo database mới.
3.  Nhập tên database: `cashflow_db` và chọn bảng mã `utf8mb4_general_ci`, sau đó nhấn **Create**.
4.  Chọn database `cashflow_db` vừa tạo, nhấn vào tab **"Import"** ở menu trên cùng.
5.  Nhấn **"Choose File"** và chọn file nằm tại đường dẫn: `C:\xampp\htdocs\CashFlow\source\database\cashflow_db.sql`.
6.  Nhấn nút **"Import"** (hoặc **Go**) ở cuối trang để hoàn tất nạp dữ liệu.

### Bước 5: Cấu hình file môi trường (.env)
1.  Vào thư mục `source`, tìm file có tên `.env.example`.
2.  Đổi tên file đó thành `.env`.
3.  Mở file `.env` bằng trình chỉnh sửa code (Notepad, VS Code, v.v.).
4.  Đảm bảo các cấu hình database là chính xác (mặc định XAMPP thường là user `root` và password rỗng).

### Bước 6: Lấy API Key cho Gemini AI
1.  Truy cập vào [Google AI Studio](https://aistudio.google.com/).
2.  Đăng nhập bằng tài khoản Google.
3.  Nhấn vào **"Get API key"** ở menu bên trái.
4.  Nhấn **"Create API key in new project"**.
5.  Sao chép mã API Key vừa tạo.
6.  Quay lại file `.env` ở Bước 5, tìm dòng `GEMINI_API_KEY=` và dán mã vào sau dấu `=`.

### Bước 7: Cài đặt và Chạy Server AI (Python)
Đây là bước bắt buộc để sử dụng tính năng Chatbot AI.
1.  Mở một cửa sổ terminal mới và di chuyển vào thư mục dịch vụ AI:
    ```bash
    cd C:\xampp\htdocs\CashFlow\source\CashFlow_AI_Service
    ```
2.  Cài đặt các thư viện Python cần thiết:
    ```bash
    pip install flask google-genai python-dotenv
    ```
3.  Khởi động server AI:
    ```bash
    python app.py
    ```
    > **⚠️ Chú ý:** Phải giữ cửa sổ terminal này luôn mở trong khi sử dụng ứng dụng. Server sẽ chạy tại cổng `5000`.

---

## 🔑 3. Hướng dẫn Đăng nhập & Sử dụng
Truy cập vào ứng dụng qua đường dẫn: [http://localhost/CashFlow/source/](http://localhost/CashFlow/source/)

Nhóm đã chuẩn bị sẵn 2 tài khoản để bạn có thể kiểm tra dữ liệu ngay lập tức:

| STT | Email Đăng nhập | Mật khẩu | Ghi chú |
| :--- | :--- | :--- | :--- |
| 1 | `nvduy180706@gmail.com` | `123456` | Tài khoản chính |
| 2 | `kdyforwork@gmail.com` | `123456` | Tài khoản phụ |

---

## 🛡️ 4. Lưu ý về Tính năng Bảo mật
*   **Xác thực qua Email**: Khi đăng ký tài khoản mới, hệ thống sẽ gửi một email thật chứa link kích hoạt. Link này chỉ có hiệu lực trong vòng **5 phút**.
*   **Đổi mật khẩu lần đầu**: Tài khoản mới bắt buộc phải đổi mật khẩu ở lần đăng nhập đầu tiên thông qua link email.
*   **Giới hạn AI**: Mỗi tài khoản chỉ có **3 lượt** chat chuyên sâu mỗi ngày để đảm bảo hiệu năng.

---

*Trân trọng cảm ơn Thầy/Cô đã dành thời gian đánh giá đồ án của nhóm!*