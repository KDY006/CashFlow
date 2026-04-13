<?php

class FormatHelper {
    
    public static function formatCurrency($amount): string {
        return number_format((float) $amount, 0, ',', '.') . ' đ';
    }

    public static function formatDate($dateString) {
        if (empty($dateString)) return '';
        return date('d/m/Y', strtotime($dateString));
    }

    public static function getTypeColorClass($type) {
        return $type === 'income' ? 'text-success' : 'text-danger';
    }

    public static function getCategoryBadgeColor($categoryId) {
        $colors = ['bg-primary', 'bg-secondary', 'bg-success', 'bg-danger', 'bg-warning text-dark', 'bg-info text-dark', 'bg-dark'];
        return $colors[$categoryId % count($colors)];
    }

    public static function toVietnameseWords($number) {
        if ($number == 0) return 'Không đồng';
        
        $words = ['không', 'một', 'hai', 'ba', 'bốn', 'năm', 'sáu', 'bảy', 'tám', 'chín'];
        $classes = ['', 'nghìn', 'triệu', 'tỷ', 'nghìn tỷ'];
        
        $str = '';
        $number = (string) (int) $number;
        $groups = array_reverse(str_split(strrev($number), 3));
        
        foreach ($groups as $i => $group) {
            $group = strrev($group);
            $len = strlen($group);
            $groupStr = '';
            
            for ($j = 0; $j < $len; $j++) {
                $digit = (int) $group[$j];
                $pos = $len - $j - 1;
                
                if ($pos == 2) {
                    $groupStr .= $words[$digit] . ' trăm ';
                } elseif ($pos == 1) {
                    if ($digit == 0) {
                        if ((int)$group[2] != 0) $groupStr .= 'lẻ ';
                    } elseif ($digit == 1) {
                        $groupStr .= 'mười ';
                    } else {
                        $groupStr .= $words[$digit] . ' mươi ';
                    }
                } elseif ($pos == 0) {
                    if ($digit == 1 && $len > 1 && (int)$group[$j-1] > 1) {
                        $groupStr .= 'mốt ';
                    } elseif ($digit == 5 && $len > 1 && (int)$group[$j-1] > 0) {
                        $groupStr .= 'lăm ';
                    } elseif ($digit > 0 || ($len == 1 && $digit == 0)) {
                        $groupStr .= $words[$digit] . ' ';
                    }
                }
            }
            if ((int)$group > 0) {
                $str .= $groupStr . $classes[count($groups) - 1 - $i] . ' ';
            }
        }
        $result = preg_replace('/\s+/', ' ', trim($str));

        return mb_strtoupper(mb_substr($result, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($result, 1, null, 'UTF-8') . ' đồng';
    }
}
?>