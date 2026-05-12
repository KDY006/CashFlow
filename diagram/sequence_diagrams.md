# Các Sơ đồ Tuần tự (Sequence Diagram) - Hệ thống CashFlow

Trong một báo cáo đồ án chuẩn Kỹ thuật Phần mềm (SE), việc vẽ **tất cả** các chức năng nhỏ lẻ (như sửa/xóa từng mục) thường làm báo cáo bị loãng và lặp lại. Thay vào đó, khái niệm "đầy đủ" nên được hiểu là vẽ đủ các **luồng nghiệp vụ cốt lõi và phức tạp nhất** đại diện cho toàn bộ hệ thống.

Dưới đây là 4 sơ đồ Sequence Diagram bao phủ toàn bộ các trụ cột của CashFlow. Bạn có thể copy mã Mermaid này để đưa vào báo cáo.

---

### 1. Luồng Xác thực (Đăng nhập & Kiểm tra thiết lập lần đầu)
Luồng này thể hiện cơ chế kiểm tra bảo mật (Bcrypt) và chuyển hướng người dùng nếu họ đăng nhập lần đầu qua email.

```mermaid
sequenceDiagram
    actor User
    participant GUI as Giao diện (View)
    participant Ctrl as UserController
    participant BUS as UserBUS
    participant DAL as UserDAL
    participant DB as MySQL Database

    User->>GUI: Nhập Email & Mật khẩu
    GUI->>Ctrl: Gửi thông tin (POST /login)
    Ctrl->>BUS: Gọi hàm login(email, password)
    BUS->>DAL: getUserByEmail(email)
    DAL->>DB: SELECT * FROM users WHERE email = ?
    DB-->>DAL: Trả về dữ liệu User (hoặc null)
    DAL-->>BUS: Dữ liệu UserDTO
    
    alt Email không tồn tại
        BUS-->>Ctrl: Lỗi: "Email không tồn tại"
        Ctrl-->>GUI: Hiển thị thông báo lỗi
    else Email hợp lệ
        BUS->>BUS: password_verify(password, hash)
        
        alt Sai mật khẩu
            BUS-->>Ctrl: Lỗi: "Mật khẩu không chính xác"
            Ctrl-->>GUI: Hiển thị thông báo lỗi
        else Đúng mật khẩu
            BUS->>DAL: getUserById(id)
            DAL->>DB: Truy vấn dữ liệu chi tiết
            DB-->>DAL: Dữ liệu chi tiết
            DAL-->>BUS: Dữ liệu chi tiết
            
            alt is_first_login == true
                BUS-->>Ctrl: Cảnh báo: "Tài khoản cần đổi mật khẩu"
                Ctrl-->>GUI: Chuyển hướng sang trang Reset Password
            else is_first_login == false
                BUS-->>Ctrl: Đăng nhập thành công, khởi tạo Session
                Ctrl-->>GUI: Chuyển hướng vào Dashboard
            end
        end
    end
```

---

### 2. Luồng Ghi chép Giao dịch Thủ công (CRUD cơ bản)
Đại diện cho kiến trúc 3 lớp tiêu chuẩn khi tương tác với CSDL.

```mermaid
sequenceDiagram
    actor User
    participant GUI as Giao diện (AJAX)
    participant Ctrl as TransactionController
    participant BUS as TransactionBUS
    participant DAL as TransactionDAL
    participant DB as MySQL Database

    User->>GUI: Điền form (Số tiền, Danh mục, Ngày, Ghi chú)
    User->>GUI: Bấm "Lưu giao dịch"
    GUI->>Ctrl: Gửi request (AJAX POST)
    Ctrl->>BUS: Gọi hàm createTransaction(data)
    
    BUS->>BUS: Validate dữ liệu (số âm, định dạng ngày...)
    
    alt Dữ liệu không hợp lệ
        BUS-->>Ctrl: Lỗi Validation
        Ctrl-->>GUI: JSON: {status: false, message}
    else Dữ liệu hợp lệ
        BUS->>DAL: insert(TransactionDTO)
        DAL->>DB: INSERT INTO transactions...
        DB-->>DAL: ID giao dịch mới
        DAL-->>BUS: true (Thành công)
        BUS-->>Ctrl: Thành công
        Ctrl-->>GUI: JSON: {status: true}
        GUI->>User: Cập nhật UI & Hiển thị thông báo Toast
    end
```

---

### 3. Luồng AI Quick Add (Bóc tách dữ liệu bằng NLP)
Luồng giao tiếp đa dịch vụ: **PHP Backend <-> Python AI Service <-> Google Gemini**.

```mermaid
sequenceDiagram
    actor User
    participant GUI as Giao diện
    participant Ctrl as AiController
    participant BUS as AiAdvisorBUS
    participant Py as Python Service (Flask)
    participant Gemini as Google Gemini API
    participant DAL as TransactionDAL

    User->>GUI: Nhập "Sáng nay ăn phở 50k"
    GUI->>Ctrl: AJAX POST /ai_quick_add
    Ctrl->>BUS: Gọi hàm parseNaturalLanguage(text)
    
    BUS->>Py: cURL POST /parse_transaction (Kèm text)
    Py->>Gemini: Gửi Prompt bóc tách JSON
    Gemini-->>Py: JSON {amount: 50000, category: "Ăn uống", note: "ăn phở"}
    Py-->>BUS: JSON Response
    
    alt Lỗi hoặc Timeout
        BUS-->>Ctrl: Lỗi: "AI không phản hồi"
        Ctrl-->>GUI: Hiển thị lỗi
    else Dữ liệu hợp lệ
        BUS->>DAL: Lưu giao dịch tự động
        DAL-->>BUS: Thành công
        BUS-->>Ctrl: JSON dữ liệu bóc tách
        Ctrl-->>GUI: Cập nhật giao diện giỏ hàng/lịch sử
    end
```

---

### 4. Luồng AI Advisor (Cố vấn Tài chính)
Thể hiện cách hệ thống tổng hợp dữ liệu làm "Ngữ cảnh" (Context) cho AI.

```mermaid
sequenceDiagram
    actor User
    participant GUI as Giao diện AI Advisor
    participant Ctrl as AiController
    participant BUS as AiAdvisorBUS
    participant TransDAL as TransactionDAL
    participant InsightDAL as AiInsightDAL
    participant Py as Python Service
    participant Gemini as Gemini API

    User->>GUI: Chọn loại tư vấn (Ví dụ: "Lời khuyên")
    GUI->>Ctrl: AJAX POST /get_advice
    Ctrl->>BUS: chatConsult(user_id, type)
    
    BUS->>TransDAL: getMonthlyTransactions(user_id)
    TransDAL-->>BUS: Dữ liệu Thu/Chi tháng hiện tại
    
    BUS->>BUS: Định dạng dữ liệu thành chuỗi Context
    
    BUS->>Py: cURL POST /chat (Kèm Context & Type)
    Py->>Gemini: Gửi Prompt (Yêu cầu trả về 3 phần Tổng quát - Cụ thể - Kết luận)
    Gemini-->>Py: Trả về nội dung tư vấn (Text/HTML)
    Py-->>BUS: Nội dung tư vấn
    
    BUS->>InsightDAL: Lưu lịch sử vào bảng ai_insights
    InsightDAL-->>BUS: OK
    
    BUS-->>Ctrl: Trả về nội dung AI
    Ctrl-->>GUI: Hiển thị khung Chatbot
```
