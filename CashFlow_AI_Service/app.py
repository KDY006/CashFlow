import os
import json
from flask import Flask, request, jsonify
from dotenv import load_dotenv
from google import genai
from google.genai import types

load_dotenv(dotenv_path="../.env")
app = Flask(__name__)

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
client = genai.Client(api_key=GEMINI_API_KEY)

# ==========================================
# API 1: BÓC TÁCH GIAO DỊCH TỪ VĂN BẢN (QUICK ADD)
# ==========================================
@app.route('/api/parse', methods=['POST'])
def parse_transaction():
    data = request.json
    text = data.get('text', '')
    categories = data.get('categories', [])

    print(f"[*] Đang bóc tách giao dịch: {text}")

    prompt = f"""
    Bạn là AI trích xuất dữ liệu tài chính.
    Câu người dùng nhập: "{text}"
    Danh mục khả dụng: {json.dumps(categories, ensure_ascii=False)}

    Nhiệm vụ: Trích xuất chính xác Số tiền (amount), ID danh mục phù hợp nhất (category_id), và Ghi chú ngắn gọn (note).
    * Quy tắc:
    - Nhận diện các từ lóng: "50k" = 50000, "1 củ" = 1000000.
    - So khớp tên danh mục với danh sách được cung cấp để lấy ID. Nếu không có danh mục nào khớp, trả về null.

    Bạn BẮT BUỘC trả về định dạng JSON thuần túy sau (Không bọc bằng markdown):
    {{"amount": 50000, "category_id": 1, "note": "Ghi chú ngắn gọn"}}
    """

    try:
        response = client.models.generate_content(
            model='gemini-2.5-flash',
            contents=prompt,
            config=types.GenerateContentConfig(
                response_mime_type="application/json",
                temperature=0.1
            )
        )
        return jsonify({"status": True, "data": json.loads(response.text)}), 200
    except Exception as e:
        print(f"[-] Lỗi Parse AI: {str(e)}")
        return jsonify({"status": False, "error": str(e)}), 500

# ==========================================
# API 2: CHATBOT TƯ VẤN (ADVISOR)
# ==========================================
@app.route('/api/chat', methods=['POST'])
def chat_consult():
    data = request.json
    t_type = data.get('type', 'summary')
    transactions = data.get('transactions', [])

    print(f"[*] Đang tư vấn loại: {t_type}")

    total_in = sum(float(t['amount']) for t in transactions if t['type'] == 'income')
    total_out = sum(float(t['amount']) for t in transactions if t['type'] == 'expense')

    cmd_map = {
        'warning': 'Hãy tìm ra các điểm bất thường, các khoản chi tiêu lãng phí và đưa ra CẢNH BÁO gay gắt nhưng mang tính xây dựng.',
        'advice': 'Hãy phân tích số liệu và đưa ra LỜI KHUYÊN hữu ích, thực tế để giúp tối ưu dòng tiền.',
        'forecast': 'Dựa vào thói quen này, hãy DỰ BÁO tình hình tài chính cuối tháng và các khoản chi sắp tới.',
        'summary': 'Hãy TÓM TẮT siêu ngắn gọn tình hình thu chi và thói quen tiêu dùng trong tháng.'
    }

    prompt = f"""
    Bạn là Cố vấn Tài chính AI của hệ thống CashFlow.
    Dữ liệu tháng này: Tổng thu: {total_in:,.0f} đ | Tổng chi: {total_out:,.0f} đ
    Chi tiết giao dịch: {json.dumps(transactions, ensure_ascii=False)}

    Nhiệm vụ: {cmd_map.get(t_type, cmd_map['summary'])}
    Yêu cầu trình bày:
    1. Xưng hô "Tôi" (Trợ lý AI) và "Bạn" (Người dùng).
    2. Format kết quả bằng HTML thuần (dùng <b>, <br>, <ul>, <li>, <span style="color:...">). 
    3. KHÔNG bọc kết quả trong markdown (```html). Trả về trực tiếp mã HTML để nhúng vào web.
    """

    try:
        response = client.models.generate_content(
            model='gemini-2.5-flash',
            contents=prompt,
            config=types.GenerateContentConfig(temperature=0.7)
        )
        return jsonify({"status": True, "answer": response.text}), 200
    except Exception as e:
        print(f"[-] Lỗi Chat AI: {str(e)}")
        return jsonify({"status": False, "error": "Hệ thống AI đang quá tải, vui lòng thử lại sau!"}), 500

if __name__ == '__main__':
    print("🚀 AI Service đang chạy tại cổng 5000...")
    app.run(port=5000, debug=True)