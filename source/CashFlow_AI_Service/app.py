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

name_model = os.getenv("GEMINI_API_NAME")

@app.route('/api/parse', methods=['POST'])
def parse_transaction():
    data = request.json
    text = data.get('text', '')
    categories = data.get('categories', [])

    prompt = f"""
    Câu người dùng nhập: "{text}"
    Danh mục khả dụng: {json.dumps(categories, ensure_ascii=False)}

    Nhiệm vụ: Trích xuất Số tiền (amount), ID danh mục phù hợp nhất (category_id), và Ghi chú ngắn gọn (note).
    Nhận diện các từ lóng: "50k" = 50000, "1 củ" = 1000000.
    BẮT BUỘC trả về định dạng JSON thuần túy (Không bọc bằng markdown):
    {{"amount": 50000, "category_id": 1, "note": "Ghi chú ngắn gọn"}}
    """
    try:
        response = client.models.generate_content(
            model=name_model, contents=prompt,
            config=types.GenerateContentConfig(response_mime_type="application/json", temperature=0.1)
        )
        return jsonify({"status": True, "data": json.loads(response.text)}), 200
    except Exception as e:
        return jsonify({"status": False, "error": str(e)}), 500

@app.route('/api/chat', methods=['POST'])
def chat_consult():
    data = request.json
    t_type = data.get('type', 'summary')
    transactions = data.get('transactions', [])

    total_in = sum(float(t['amount']) for t in transactions if t['type'] == 'income')
    total_out = sum(float(t['amount']) for t in transactions if t['type'] == 'expense')

    # Đã sửa lại từ ngữ an toàn để tránh bị Gemini chặn
    cmd_map = {
        'warning': 'Hãy tìm ra các điểm bất thường, các khoản chi tiêu không hợp lý và đưa ra CẢNH BÁO nghiêm túc để tối ưu tài chính.',
        'advice': 'Hãy phân tích số liệu và đưa ra LỜI KHUYÊN hữu ích, thực tế để giúp tiết kiệm dòng tiền.',
        'forecast': 'Dựa vào thói quen này, hãy DỰ BÁO tình hình tài chính cuối tháng và các khoản chi sắp tới.',
        'summary': 'Hãy TÓM TẮT siêu ngắn gọn tình hình thu chi và thói quen tiêu dùng trong tháng.'
    }

    prompt = f"""
    Bạn là Cố vấn Tài chính AI của hệ thống CashFlow.
    Tổng thu: {total_in:,.0f} đ | Tổng chi: {total_out:,.0f} đ
    Chi tiết giao dịch: {json.dumps(transactions, ensure_ascii=False)}

    Nhiệm vụ: {cmd_map.get(t_type, cmd_map['summary'])}
    Yêu cầu trình bày:
    1. Xưng hô "Tôi" (Trợ lý AI) và "Bạn" (Người dùng).
    2. Câu trả lời phải NGẮN GỌN, súc tích và bắt buộc chia làm 3 phần rõ rệt:
       - <b>TỔNG QUÁT:</b> (Nhận xét nhanh về tình hình tài chính hiện tại)
       - <b>CỤ THỂ:</b> (Liệt kê các điểm đáng chú ý hoặc con số quan trọng)
       - <b>KẾT LUẬN:</b> (Lời khuyên hoặc dự báo ngắn gọn)
    3. Format kết quả bằng HTML thuần (dùng <b>, <br>, <ul>, <li>). 
    4. KHÔNG bọc kết quả trong markdown (```html).
    """
    try:
        response = client.models.generate_content(
            model=name_model, contents=prompt,
            config=types.GenerateContentConfig(temperature=0.7)
        )
        return jsonify({"status": True, "answer": response.text}), 200
    except Exception as e:
        return jsonify({"status": False, "error": "Hệ thống AI đang quá tải hoặc gặp lỗi bảo mật của Gemini!"}), 500

if __name__ == '__main__':
    app.run(port=5000, debug=False)