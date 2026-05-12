/**
 * Tệp: GUI/assets/js/app.js
 * Chứa các hàm tiện ích dùng chung cho toàn bộ hệ thống CashFlow
 */

// 1. Thuật toán đồng bộ màu sắc danh mục
window.getCategoryColor = function(name) {
    if (!name) return '#999999';
    const colors = [
        '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#F06292', 
        '#AED581', '#7986CB', '#4DB6AC', '#FFB74D', '#A1887F', 
        '#90A4AE', '#BA68C8', '#FF8A65', '#4DD0E1', '#81C784'
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) { 
        hash = name.charCodeAt(i) + ((hash << 5) - hash); 
    }
    return colors[Math.abs(hash) % colors.length];
};

// 2. Format số tiền có dấu chấm (Ví dụ: 1000000 -> 1.000.000)
window.formatNumberInput = function(value) {
    if (!value) return "0";
    value = value.toString().replace(/\D/g, ""); 
    return value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

// 3. Đọc số tiền thành chữ Tiếng Việt
window.readVietnameseNumber = function(number) {
    if (!number || number == 0) return "";
    const units = ["", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"];
    const levels = ["", "nghìn", "triệu", "tỷ", "nghìn tỷ", "triệu tỷ"];
    
    function readThreeDigits(num, isFirst) {
        let hundred = Math.floor(num / 100); let ten = Math.floor((num % 100) / 10); let unit = num % 10; let res = "";
        if (hundred > 0 || !isFirst) res += units[hundred] + " trăm ";
        if (ten > 1) { res += units[ten] + " mươi "; if (unit == 1) res += "mốt "; else if (unit == 5) res += "lăm "; else if (unit > 0) res += units[unit] + " "; } 
        else if (ten == 1) { res += "mười "; if (unit == 5) res += "lăm "; else if (unit > 0) res += units[unit] + " "; } 
        else if (unit > 0) { if (!isFirst || hundred > 0) res += "linh " + units[unit] + " "; else res += units[unit] + " "; }
        return res;
    }
    
    let str = ""; let i = 0; let temp = number;
    do {
        let block = temp % 1000;
        if (block > 0) { let s = readThreeDigits(block, temp < 1000); str = s + levels[i] + " " + str; }
        temp = Math.floor(temp / 1000); i++;
    } while (temp > 0);
    return str.trim().charAt(0).toUpperCase() + str.trim().slice(1) + " đồng";
};