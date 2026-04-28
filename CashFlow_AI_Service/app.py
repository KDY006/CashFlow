from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/api/analyze', methods=['POST'])
def analyze():
    # 1. Nhận dữ liệu TỪ TÀI KHOẢN CỦA BẠN do PHP gửi sang
    data = request.json
    user_id = data.get('user_id')
    transactions = data.get('transactions', [])

    print(f"[*] Đang phân tích dữ liệu thật cho User ID: {user_id}")
    insights = []

    # 2. Xử lý logic nếu chưa có dữ liệu
    if not transactions or len(transactions) == 0:
        insights.append({
            "type": "advice",
            "content": "Hệ thống chưa ghi nhận giao dịch nào của bạn trong tháng này. Hãy bắt đầu ghi chép để AI có thể tư vấn nhé!"
        })
        return jsonify({"insights": insights}), 200

    # 3. LOGIC PHÂN TÍCH THẬT (Basic Logic)
    total_expense = 0
    category_totals = {}

    # Quét qua toàn bộ giao dịch PHP gửi sang
    for t in transactions:
        if t.get('type') == 'expense':  # Chỉ tính các khoản chi
            amount = float(t.get('amount', 0))
            total_expense += amount
            
            # Cộng dồn tiền theo danh mục
            cat_name = t.get('category_name', 'Khác')
            category_totals[cat_name] = category_totals.get(cat_name, 0) + amount

    # 4. Sinh ra lời khuyên DỰA TRÊN DỮ LIỆU THẬT
    if total_expense > 0:
        # Tìm danh mục tiêu nhiều tiền nhất
        top_category = max(category_totals, key=category_totals.get)
        top_amount = category_totals[top_category]
        
        # Format số tiền kiểu VNĐ (ví dụ: 1,500,000)
        formatted_total = "{:,.0f}".format(total_expense).replace(',', '.')
        formatted_top_amt = "{:,.0f}".format(top_amount).replace(',', '.')

        insights.append({
            "type": "anomaly",
            "content": f"Tháng này bạn đã chi ra tổng cộng {formatted_total} VNĐ. Chú ý: Mục '{top_category}' đang chiếm nhiều nhất với {formatted_top_amt} VNĐ."
        })
        
        insights.append({
            "type": "advice",
            "content": f"Lời khuyên: Bạn nên xem xét lại các khoản chi trong nhóm '{top_category}'. Nếu cắt giảm được 15% ở nhóm này, bạn sẽ có thêm quỹ dự phòng cho tháng sau."
        })
    else:
        insights.append({
            "type": "forecast",
            "content": "Bạn đang quản lý tiền rất tốt! Hệ thống nhận thấy bạn chỉ có các khoản thu mà chưa phát sinh khoản chi tiêu nào."
        })

    # Trả kết quả về cho PHP
    return jsonify({"insights": insights}), 200

if __name__ == '__main__':
    print("🚀 AI Service (Real Data Mode) đang chạy tại cổng 5000...")
    app.run(port=5000, debug=True)