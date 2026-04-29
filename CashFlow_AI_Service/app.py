import os
import json
from flask import Flask, request, jsonify

# Khai báo theo chuẩn thư viện mới của Google
from google import genai
from google.genai import types

app = Flask(__name__)

# =========================================================================
# CẤU HÌNH GOOGLE GEMINI API (SDK MỚI)
# =========================================================================
GEMINI_API_KEY = "AIzaSyCKOW5L_9quz6cxfqDdgpYHzTk95lqm4zY"

# Khởi tạo Client thế hệ mới
client = genai.Client(api_key=GEMINI_API_KEY)

@app.route('/api/analyze', methods=['POST'])
def analyze():
    data = request.json
    user_id = data.get('user_id')
    transactions = data.get('transactions', [])

    print(f"[*] Đang yêu cầu Google Gemini phân tích cho User ID: {user_id}")

    if not transactions or len(transactions) == 0:
        return jsonify({"insights": [{
            "type": "advice",
            "content": "Hệ thống chưa ghi nhận giao dịch nào của bạn trong tháng này. Hãy bắt đầu ghi chép để tôi có thể phân tích nhé!"
        }]}), 200

    # 1. Tiền xử lý dữ liệu
    total_income = 0
    total_expense = 0
    categories = {}

    for t in transactions:
        amount = float(t.get('amount', 0))
        cat_name = t.get('category_name', 'Khác')
        t_type = t.get('type') 

        if t_type == 'income':
            total_income += amount
        else:
            total_expense += amount
            categories[cat_name] = categories.get(cat_name, 0) + amount

    # 2. Xây dựng Kịch bản (Prompt)
    prompt = f"""
    Bạn là một chuyên gia tư vấn tài chính cá nhân chuyên nghiệp.
    Dữ liệu tài chính trong tháng của khách hàng:
    - Tổng thu nhập: {total_income:,.0f} VNĐ
    - Tổng chi tiêu: {total_expense:,.0f} VNĐ
    - Chi tiết các khoản chi: {json.dumps(categories, ensure_ascii=False)}

    Nhiệm vụ: Phân tích dữ liệu và đưa ra chính xác 3 lời khuyên hoặc nhận định.
    Giọng điệu: Thân thiện, khích lệ, thực tế. Tránh dùng từ ngữ hàn lâm.
    
    YÊU CẦU BẮT BUỘC:
    Trang web của tôi cần nhận dữ liệu dạng JSON Array để parse. 
    Bạn PHẢI trả về đúng định dạng JSON sau:
    [
      {{"type": "anomaly", "content": "câu cảnh báo của bạn"}},
      {{"type": "advice", "content": "lời khuyên của bạn"}},
      {{"type": "forecast", "content": "dự báo của bạn"}}
    ]
    * Giá trị "type" CHỈ ĐƯỢC PHÉP dùng: "anomaly", "forecast", hoặc "advice".
    """

    # 3. Gửi yêu cầu qua SDK mới
    try:
        # Chuyển sang dùng model gemini-2.5-flash thế hệ mới cực nhanh
        response = client.models.generate_content(
            model='gemini-2.5-flash',
            contents=prompt,
            config=types.GenerateContentConfig(
                response_mime_type="application/json",
                temperature=0.7 # Độ sáng tạo vừa phải
            )
        )
        
        # Đọc kết quả JSON trả về
        ai_insights = json.loads(response.text)
        print("[+] Gemini phân tích thành công!")
        return jsonify({"insights": ai_insights}), 200

    except Exception as e:
        print(f"[-] Lỗi khi gọi Gemini API: {str(e)}")
        fallback_insights = [{
            "type": "anomaly",
            "content": f"Lỗi kết nối API thế hệ mới: {str(e)}"
        }]
        return jsonify({"insights": fallback_insights}), 200

if __name__ == '__main__':
    print("🚀 AI Service (SDK Mới) đang chạy tại cổng 5000...")
    app.run(port=5000, debug=True)