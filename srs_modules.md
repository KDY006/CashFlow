# Đặc tả Yêu cầu Phần mềm (SRS) - Hệ thống CashFlow

Tài liệu này đặc tả chi tiết các yêu cầu chức năng (FR) và phi chức năng (NFR) cho 7 module cốt lõi của hệ thống.

---

## Module 1: Hệ thống & Xác thực (Authentication & User Management)

### 1.1 Yêu cầu chức năng (Functional Requirements)
| Mã FR | Tên chức năng | Mô tả chi tiết | Tiêu chí chấp nhận |
| :--- | :--- | :--- | :--- |
| **FR-101** | Đăng ký tài khoản | Tạo tài khoản bằng Email và Tên. | Gửi email kích hoạt tài khoản. Không trùng email. |
| **FR-102** | Đăng nhập | Truy cập bằng Email/Mật khẩu. | Sai thông tin báo lỗi. Bắt buộc đổi MK nếu lần đầu. |
| **FR-103** | Thiết lập mật khẩu | Bắt buộc đổi mật khẩu an toàn. | MK tối thiểu 6 ký tự. Băm bằng Bcrypt. |
| **FR-104** | Quên mật khẩu | Gửi mail đặt lại mật khẩu. | Link xác thực an toàn qua Email. |
| **FR-105** | Quản lý hồ sơ | Cập nhật avatar/thông tin. | Upload ảnh hợp lệ, tự động đổi tên file. |

### 1.2 Yêu cầu phi chức năng (Non-functional Requirements)
*   **Security (NFR-101):** Toàn bộ mật khẩu phải được băm bằng Bcrypt trước khi lưu vào CSDL.
*   **Security (NFR-102):** Sử dụng CSRF Token cho mọi yêu cầu thay đổi dữ liệu (POST).
*   **Usability (NFR-103):** Thông báo lỗi đăng nhập phải rõ ràng nhưng không tiết lộ lý do cụ thể (tránh dò tìm tài khoản).

---

## Module 2: Quản lý Giao dịch (Transaction Management)

### 2.1 Yêu cầu chức năng (Functional Requirements)
| Mã FR | Tên chức năng | Mô tả chi tiết | Tiêu chí chấp nhận |
| :--- | :--- | :--- | :--- |
| **FR-201** | Thêm giao dịch | Nhập số tiền, danh mục, ngày, note. | Số tiền > 0. Tự động cập nhật số dư. |
| **FR-202** | Danh sách giao dịch | Hiển thị lịch sử Thu/Chi. | Phân biệt màu sắc Thu (Xanh) - Chi (Đỏ). |
| **FR-203** | Sửa/Xóa giao dịch | Điều chỉnh thông tin sai. | Cập nhật số dư lập tức. Bảo mật quyền sở hữu. |
| **FR-204** | Tìm kiếm & Lọc | Lọc theo ngày, loại, từ khóa. | Kết quả trả về qua AJAX (không load trang). |

### 2.2 Yêu cầu phi chức năng (Non-functional Requirements)
*   **Integrity (NFR-201):** Số tiền giao dịch phải sử dụng kiểu dữ liệu `decimal(15,2)` để tránh sai số làm tròn.
*   **Performance (NFR-202):** Tìm kiếm giao dịch phải có thời gian phản hồi dưới 1 giây với dữ liệu 10,000 dòng.
*   **Performance (NFR-203):** Sử dụng Index trên cột `transaction_date` và `user_id` để tối ưu truy vấn.

---

## Module 3: Quản lý Danh mục (Category Management)

### 3.1 Yêu cầu chức năng (Functional Requirements)
| Mã FR | Tên chức năng | Mô tả chi tiết | Tiêu chí chấp nhận |
| :--- | :--- | :--- | :--- |
| **FR-301** | Danh mục hệ thống | Các danh mục mặc định. | Người dùng không được xóa/sửa mục hệ thống. |
| **FR-302** | Danh mục cá nhân | Người dùng tự tạo thêm. | Hỗ trợ cấu trúc phân cấp (Cha - Con). |
| **FR-303** | Sửa/Xóa danh mục | Tùy chỉnh danh mục riêng. | Không cho xóa nếu đang có giao dịch liên kết. |

### 3.2 Yêu cầu phi chức năng (Non-functional Requirements)
*   **Reliability (NFR-301):** Hệ thống phải duy trì tính toàn vẹn tham chiếu (Foreign Key) khi thao tác với danh mục.
*   **Usability (NFR-302):** Giao diện chọn danh mục phải trực quan, thể hiện rõ cấp bậc Cha-Con.

---

## Module 4: Lập kế hoạch & Ngân sách (Budget Planning)

### 4.1 Yêu cầu chức năng (Functional Requirements)
| Mã FR | Tên chức năng | Mô tả chi tiết | Tiêu chí chấp nhận |
| :--- | :--- | :--- | :--- |
| **FR-401** | Thiết lập ngân sách | Đặt hạn mức chi tiêu theo tháng. | Mỗi danh mục có tối đa 1 hạn mức/tháng. |
| **FR-402** | Cảnh báo vượt hạn | Theo dõi tiến độ chi tiêu. | Thanh Progress bar chuyển đỏ khi chi > 100%. |
| **FR-403** | Tự động thừa kế | Copy ngân sách sang tháng mới. | Tự thực hiện khi người dùng xem tháng chưa cấu hình. |

### 4.2 Yêu cầu phi chức năng (Non-functional Requirements)
*   **Accuracy (NFR-401):** Tỷ lệ phần trăm sử dụng ngân sách phải được tính toán chính xác dựa trên tổng chi thực tế của danh mục đó trong tháng.
*   **Performance (NFR-402):** Tính toán ngân sách phải được thực hiện ở phía Server để đảm bảo tính nhất quán.

---

## Module 5: Thống kê & Phân tích (Analytics & Reporting)

### 5.1 Yêu cầu chức năng (Functional Requirements)
| Mã FR | Tên chức năng | Mô tả chi tiết | Tiêu chí chấp nhận |
| :--- | :--- | :--- | :--- |
| **FR-501** | Dashboard Tổng quan | Xem Thu, Chi, Số dư hiện tại. | Cập nhật số liệu Real-time. |
| **FR-502** | Biểu đồ Cơ cấu | Tỷ trọng chi tiêu theo mục. | Biểu đồ tròn Chart.js sinh động, có chú thích. |
| **FR-503** | Biểu đồ Dòng tiền | Xu hướng tài chính qua thời gian. | Biểu đồ đường, hỗ trợ lọc theo tuần/tháng. |

### 5.2 Yêu cầu phi chức năng (Non-functional Requirements)
*   **Usability (NFR-501):** Biểu đồ phải tương thích hoàn hảo trên các kích thước màn hình khác nhau (Responsive).
*   **Performance (NFR-502):** Sử dụng các câu lệnh SQL tối ưu (như Pivot/Aggregation) để giảm tải cho Web Server.

---

## Module 6: Lịch & Ghi chú (Calendar & Daily Notes)

### 6.1 Yêu cầu chức năng (Functional Requirements)
| Mã FR | Tên chức năng | Mô tả chi tiết | Tiêu chí chấp nhận |
| :--- | :--- | :--- | :--- |
| **FR-601** | Lịch giao dịch | Xem biến động tiền theo ngày. | Hiển thị popup chi tiết khi click vào ngày. |
| **FR-602** | Ghi chú tài chính | Lưu lời nhắc cho ngày cụ thể. | Hiển thị icon ghim trên lịch để nhận diện. |

### 6.2 Yêu cầu phi chức năng (Non-functional Requirements)
*   **Performance (NFR-601):** Dữ liệu giao dịch trên lịch phải được tải bất đồng bộ (Lazy loading) để tránh làm chậm trang.
*   **Usability (NFR-602):** Thao tác chuyển tháng trên lịch phải mượt mà, không giật lag.

---

## Module 7: Cố vấn Tài chính AI (AI Advisor & NLP Service)

### 7.1 Yêu cầu chức năng (Functional Requirements)
| Mã FR | Tên chức năng | Mô tả chi tiết | Tiêu chí chấp nhận |
| :--- | :--- | :--- | :--- |
| **FR-701** | AI Quick Add | Chuyển câu nói thành giao dịch. | "Ăn tối 100k" -> amount=100000, type=expense. |
| **FR-702** | AI Advisor | Tư vấn tài chính chuyên sâu. | Chế độ: Cảnh báo, Lời khuyên, Dự báo, Tóm tắt. |
| **FR-703** | Cấu trúc phản hồi | Ép AI trả lời theo format. | Luôn có 3 phần: TỔNG QUÁT, CỤ THỂ, KẾT LUẬN. |
| **FR-704** | Giới hạn Token | Kiểm soát lượt sử dụng. | Tối đa 3 lượt hỏi/người dùng/ngày. |

### 7.2 Yêu cầu phi chức năng (Non-functional Requirements)
*   **Reliability (NFR-701):** Hệ thống phải thông báo lỗi thân thiện nếu Server Python bị ngắt kết nối hoặc API Gemini bị lỗi.
*   **Security (NFR-702):** API Key của Gemini phải được bảo mật tuyệt đối trong file `.env`, không bao giờ để lộ ở phía Client (JavaScript).
*   **Performance (NFR-703):** Thời gian xử lý của AI không được vượt quá 30 giây cho một yêu cầu phức tạp.
*   **Maintainability (NFR-704):** Tách biệt logic AI thành dịch vụ Python riêng để dễ dàng nâng cấp model AI trong tương lai (ví dụ từ Gemini 1.5 lên 2.0).
