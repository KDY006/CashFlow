import os
import json
from flask import Flask, request, jsonify
from dotenv import load_dotenv

# Khai báo theo chuẩn thư viện mới của Google
from google import genai
from google.genai import types

load_dotenv(dotenv_path="../.env")

app = Flask(__name__)

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
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

    # 1. Tiền xử lý dữ liệu & TRÍCH XUẤT GHI CHÚ
    total_income = 0
    total_expense = 0
    categories = {}
    detailed_notes = [] # Mảng chứa các giao dịch có ghi chú

    for t in transactions:
        amount = float(t.get('amount', 0))
        cat_name = t.get('category_name', 'Khác')
        t_type = t.get('type') 
        note = t.get('note', '').strip() # Lấy ghi chú, loại bỏ khoảng trắng thừa

        if t_type == 'income':
            total_income += amount
            if note: # Nếu có ghi chú thì đưa vào danh sách AI cần đọc
                detailed_notes.append(f"[Thu] {cat_name} ({amount:,.0f}đ): {note}")
        else:
            total_expense += amount
            categories[cat_name] = categories.get(cat_name, 0) + amount
            if note: # Nếu có ghi chú thì đưa vào danh sách AI cần đọc
                detailed_notes.append(f"[Chi] {cat_name} ({amount:,.0f}đ): {note}")

    # Chuyển mảng ghi chú thành 1 đoạn văn bản, nếu không có ghi chú nào thì để trống
    notes_context = "\n".join(detailed_notes) if detailed_notes else "Khách hàng không để lại ghi chú chi tiết nào trong tháng này."

    # 2. Xây dựng Kịch bản (Prompt) - Nâng cấp để đọc ghi chú
    prompt = f"""
    Bạn là một chuyên gia tư vấn tài chính cá nhân chuyên nghiệp.
    Dữ liệu tài chính trong tháng của khách hàng:
    - Tổng thu nhập: {total_income:,.0f} VNĐ
    - Tổng chi tiêu: {total_expense:,.0f} VNĐ
    - Phân bổ các khoản chi: {json.dumps(categories, ensure_ascii=False)}

    ĐẶC BIỆT LƯU Ý - Dưới đây là các giao dịch có ghi chú chi tiết của khách hàng:
    {notes_context}

    Nhiệm vụ: 
    Hãy phân tích tổng quan dòng tiền VÀ đọc kỹ các ghi chú để hiểu ngữ cảnh, thói quen tiêu dùng. Sau đó đưa ra chính xác 3 nhận định/lời khuyên. 
    Nếu trong ghi chú có các khoản chi tiêu lãng phí, hãy nhắc nhở. Nếu có các khoản thu nhập nỗ lực (như làm thêm, freelance), hãy khích lệ.
    Giọng điệu: Thân thiện, thấu hiểu, thực tế. Tránh dùng từ ngữ hàn lâm.
    
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
        response = client.models.generate_content(
            model='gemini-2.5-flash',
            contents=prompt,
            config=types.GenerateContentConfig(
                response_mime_type="application/json",
                temperature=0.7 
            )
        )
        
        ai_insights = json.loads(response.text)
        print("[+] Gemini phân tích thành công!")
        return jsonify({"insights": ai_insights}), 200

    except Exception as e:
        print(f"[-] Lỗi khi gọi Gemini API: {str(e)}")
        # TRẢ VỀ LỖI RÕ RÀNG THAY VÌ GIẢ MẠO THÀNH INSIGHT
        return jsonify({
            "error": "Hệ thống AI đang quá tải hoặc gặp sự cố kết nối. Vui lòng thử lại sau 1 phút!"
        }), 500

if __name__ == '__main__':
    print("🚀 AI Service đang chạy tại cổng 5000...")
    app.run(port=5000, debug=True)