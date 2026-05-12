# Sơ đồ Thực thể liên kết (ERD) - Hệ thống CashFlow

Dưới đây là sơ đồ ERD mô tả cấu trúc cơ sở dữ liệu của hệ thống, được vẽ bằng cú pháp Mermaid. Bạn có thể copy mã này dán vào Notion, Obsidian hoặc các trình duyệt hỗ trợ Mermaid (như GitHub, GitLab) để xem trực quan.

```mermaid
erDiagram
    USERS {
        int id PK
        varchar full_name
        varchar avatar_url
        varchar email UK "Unique"
        varchar password_hash
        boolean is_first_login
        varchar login_token
        datetime created_at
        datetime updated_at
        datetime last_ai_consult_at
    }

    CATEGORIES {
        int id PK
        int user_id FK "Nullable"
        int parent_id FK "Nullable"
        varchar name
        enum type "'income', 'expense'"
        datetime created_at
    }

    TRANSACTIONS {
        int id PK
        int user_id FK
        int category_id FK
        decimal amount
        datetime transaction_date
        text note
        datetime created_at
    }

    BUDGETS {
        int id PK
        int user_id FK
        int category_id FK
        decimal amount_limit
        tinyint month "1-12"
        year year
        datetime created_at
    }

    AI_INSIGHTS {
        int id PK
        int user_id FK
        enum type "'warning', 'advice', 'forecast', 'summary'"
        text content
        boolean is_read
        datetime created_at
    }

    DAILY_NOTES {
        int id PK
        int user_id FK
        date note_date
        text content
        enum pin_type "'none', 'weekly', 'monthly'"
        datetime created_at
    }

    %% Quan hệ (Relationships)
    USERS ||--o{ CATEGORIES : "tạo (null = hệ thống)"
    CATEGORIES ||--o{ CATEGORIES : "có danh mục con"
    
    USERS ||--o{ TRANSACTIONS : "thực hiện"
    CATEGORIES ||--o{ TRANSACTIONS : "được phân loại"
    
    USERS ||--o{ BUDGETS : "thiết lập"
    CATEGORIES ||--o{ BUDGETS : "áp dụng cho"
    
    USERS ||--o{ AI_INSIGHTS : "nhận phân tích"
    
    USERS ||--o{ DAILY_NOTES : "tạo ghi chú"

```
