-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 06:10 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cashflow_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ai_insights`
--

CREATE TABLE `ai_insights` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('anomaly','forecast','advice','summary','warning') NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_insights`
--

INSERT INTO `ai_insights` (`id`, `user_id`, `type`, `content`, `is_read`, `created_at`) VALUES
(1, 1, 'anomaly', 'Tháng này bạn đã chi ra tổng cộng 353.000 VNĐ. Chú ý: Mục \'Đi chơi với bồ\' đang chiếm nhiều nhất với 235.000 VNĐ.', 1, '2026-04-29 01:10:14'),
(2, 1, 'advice', 'Lời khuyên: Bạn nên xem xét lại các khoản chi trong nhóm \'Đi chơi với bồ\'. Nếu cắt giảm được 15% ở nhóm này, bạn sẽ có thêm quỹ dự phòng cho tháng sau.', 1, '2026-04-29 01:10:14'),
(3, 1, 'anomaly', 'Tháng này bạn đã chi ra tổng cộng 353.000 VNĐ. Chú ý: Mục \'Đi chơi với bồ\' đang chiếm nhiều nhất với 235.000 VNĐ.', 1, '2026-04-29 01:10:20'),
(4, 1, 'advice', 'Lời khuyên: Bạn nên xem xét lại các khoản chi trong nhóm \'Đi chơi với bồ\'. Nếu cắt giảm được 15% ở nhóm này, bạn sẽ có thêm quỹ dự phòng cho tháng sau.', 1, '2026-04-29 01:10:20'),
(5, 1, 'advice', 'Hệ thống nhận thấy bạn chi khá nhiều cho mục \"Uống nước\" và cà phê. Bạn có thể cân nhắc việc tự pha chế ở nhà để tiết kiệm quỹ tiền mặt nhé.', 1, '2026-04-28 09:00:00'),
(7, 1, 'forecast', 'Với tiến độ chi tiêu hiện tại và khoản thu nhập thêm từ Freelance, dự kiến bạn sẽ kết thúc tháng 4/2026 với số dư khả dụng khoảng 3.200.000đ. Giữ vững phong độ nhé!', 1, '2026-04-29 11:30:00'),
(8, 1, 'anomaly', 'Tháng này bạn đã chi ra tổng cộng 998.000 VNĐ. Chú ý: Mục \'Mua sắm\' đang chiếm nhiều nhất với 450.000 VNĐ.', 1, '2026-04-29 07:43:33'),
(9, 1, 'advice', 'Lời khuyên: Bạn nên xem xét lại các khoản chi trong nhóm \'Mua sắm\'. Nếu cắt giảm được 15% ở nhóm này, bạn sẽ có thêm quỹ dự phòng cho tháng sau.', 1, '2026-04-29 07:43:33'),
(10, 1, 'anomaly', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 1, '2026-04-29 07:51:46'),
(11, 1, 'anomaly', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 1, '2026-04-29 07:51:59'),
(12, 1, 'anomaly', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 1, '2026-04-29 07:54:05'),
(13, 1, 'anomaly', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 1, '2026-04-29 07:54:20'),
(14, 1, 'anomaly', 'Bạn đang có một tỷ lệ tiết kiệm rất ấn tượng, lên đến hơn 80% thu nhập! Đây là một thành tích tuyệt vời, tuy nhiên, hãy cùng xem lại một chút liệu mình đã ghi nhận đầy đủ tất cả các khoản chi tiêu nhỏ trong tháng chưa nhé, để đảm bảo bức tranh tài chính là hoàn chỉnh nhất.', 1, '2026-04-29 07:57:11'),
(15, 1, 'advice', 'Với khoản tiết kiệm lớn như vậy, bạn có thể nghĩ đến việc bắt đầu xây dựng một quỹ khẩn cấp hoặc đầu tư nhỏ để tiền của mình \'làm việc\' hiệu quả hơn. Đây là thời điểm tốt để đặt ra các mục tiêu tài chính dài hạn!', 1, '2026-04-29 07:57:11'),
(16, 1, 'forecast', 'Nếu bạn duy trì được mức tiết kiệm này, bạn sẽ nhanh chóng đạt được các mục tiêu tài chính lớn như mua nhà, xe, hoặc nghỉ hưu sớm. Tương lai tài chính của bạn đang rất sáng sủa!', 1, '2026-04-29 07:57:11'),
(18, 1, 'advice', 'Bạn đang quản lý tài chính rất xuất sắc với tỷ lệ tiết kiệm ấn tượng gần 70% tổng thu nhập! Để tối ưu hơn nữa, bạn có thể thử chuyển một phần nhỏ từ khoản \'Uống nước\' sang các khoản đầu tư nhỏ hoặc xây dựng quỹ khẩn cấp để tiền của bạn \'làm việc\' hiệu quả hơn nhé.', 0, '2026-04-29 09:06:41'),
(19, 1, 'forecast', 'Với khả năng tiết kiệm mạnh mẽ như hiện tại, nếu bạn duy trì được thói quen này, tương lai tài chính của bạn sẽ rất vững vàng. Bạn hoàn toàn có thể đạt được các mục tiêu lớn như mua sắm tài sản, đầu tư dài hạn hay thậm chí là nghỉ hưu sớm hơn dự kiến đấy!', 0, '2026-04-29 09:06:41'),
(25, 1, 'advice', 'Với tỷ lệ tiết kiệm rất tốt như hiện tại, bạn có thể bắt đầu nghĩ đến việc phân bổ số tiền dư vào các quỹ khẩn cấp hoặc bắt đầu tìm hiểu các kênh đầu tư nhỏ để tiền của bạn \'sinh lời\' nhé!', 1, '2026-04-29 14:41:54'),
(26, 1, 'forecast', 'Nếu bạn tiếp tục duy trì thói quen chi tiêu hợp lý và tiết kiệm tốt như vậy, bạn sẽ sớm xây dựng được một nền tảng tài chính vững vàng, giúp bạn thực hiện được nhiều dự định lớn trong tương lai.', 1, '2026-04-29 14:41:54'),
(35, 1, 'advice', 'Với số tiền tiết kiệm lên đến 3.602.000 VNĐ (khoảng 69% tổng thu nhập), bạn đang có một nền tảng tài chính cực kỳ vững chắc! Đây là thời điểm tuyệt vời để bạn bắt đầu xây dựng quỹ khẩn cấp hoặc tìm hiểu các kênh đầu tư nhỏ để tiền của bạn sinh lời thêm.', 1, '2026-04-29 23:58:36'),
(36, 1, 'forecast', 'Nếu bạn tiếp tục duy trì được thói quen chi tiêu hợp lý và tiết kiệm hiệu quả như hiện tại, mình tin rằng bạn sẽ tích lũy được một khoản đáng kể trong vài tháng tới, giúp bạn tự tin hơn để thực hiện các mục tiêu tài chính lớn hơn trong tương lai.', 1, '2026-04-29 23:58:36'),
(41, 1, 'anomaly', 'Chào bạn! Nhìn vào chi tiêu tháng này, mình thấy khoản \'Uống nước\' lên đến 710,000 VNĐ là khá cao so với tổng chi tiêu (chiếm gần một nửa đó). Đây là một khoản chi đáng để bạn xem xét kỹ hơn, liệu có cách nào để tiết kiệm hơn ở hạng mục này không nhé?', 1, '2026-04-30 00:06:50'),
(42, 1, 'advice', 'Bạn đang có một khoản tiết kiệm rất ấn tượng, lên đến 3,602,000 VNĐ, chiếm gần 70% tổng thu nhập! Đây là một thành tích tuyệt vời. Với số tiền này, bạn có thể bắt đầu nghĩ đến việc lập một quỹ khẩn cấp vững chắc hoặc đầu tư để tiền của bạn \'làm việc\' hiệu quả hơn nữa nhé.', 0, '2026-04-30 00:06:50'),
(43, 1, 'forecast', 'Nếu bạn tiếp tục duy trì được thói quen chi tiêu hợp lý và tiết kiệm hiệu quả như thế này, mình tin rằng bạn sẽ nhanh chóng đạt được các mục tiêu tài chính cá nhân và xây dựng được một nền tảng tài chính vững vàng cho tương lai!', 0, '2026-04-30 00:06:50'),
(44, 1, 'anomaly', 'Chào bạn! Nhìn vào số liệu, có vẻ như tháng này bạn đang chi tiêu vượt quá thu nhập khá nhiều, với mức thâm hụt lên tới hơn 6 triệu đồng. Đặc biệt, khoản \'Khác\' chiếm đến 10 triệu đồng là một con số rất lớn, cần được xem xét kỹ lưỡng đó nhé.', 0, '2026-04-30 00:26:44'),
(45, 1, 'advice', 'Lời khuyên chân thành là bạn hãy thử dành thời gian phân loại rõ ràng hơn khoản \'Khác\' 10 triệu đồng này. Việc biết chính xác tiền của mình đi đâu sẽ giúp bạn dễ dàng tìm ra những mục có thể tiết kiệm hoặc cắt giảm, từ đó cân đối lại chi tiêu hiệu quả hơn.', 0, '2026-04-30 00:26:44'),
(46, 1, 'forecast', 'Nếu tình hình chi tiêu vượt thu nhập như hiện tại tiếp diễn mà không có điều chỉnh, rất có thể bạn sẽ phải đối mặt với áp lực tài chính lớn, thậm chí là phát sinh nợ hoặc dần cạn kiệt các khoản dự phòng đấy. Nhưng đừng lo, chúng ta hoàn toàn có thể thay đổi được!', 0, '2026-04-30 00:26:44'),
(54, 1, 'anomaly', 'Tháng này, điểm đáng lo ngại nhất là khoản chi 10,000,000 VNĐ do bị lừa. Đây là một con số rất lớn, khiến dòng tiền của bạn thâm hụt nghiêm trọng. Hãy cực kỳ cẩn trọng hơn với các giao dịch tài chính và thông tin cá nhân để tránh rủi ro tương tự trong tương lai nhé!', 0, '2026-04-30 01:04:54'),
(55, 1, 'advice', 'Bên cạnh sự cố không mong muốn, mình thấy khoản chi \'Uống nước\' khá cao, đặc biệt là 600,000 VNĐ cho việc \'Bao công ty\'. Việc này tuy thể hiện sự hào phóng nhưng có thể cân nhắc lại để tối ưu chi tiêu, đặc biệt khi dòng tiền đang bị âm. Các khoản mua sắm như áo thun local brand cũng có thể được xem xét kỹ hơn trong những tháng tới để tập trung vào việc tiết kiệm.', 0, '2026-04-30 01:04:54'),
(56, 1, 'forecast', 'Mình rất khuyến khích bạn tiếp tục phát huy các nguồn thu nhập phụ như freelance code dạo và tiền tip từ Anh Trân hay tool dự báo tài chính. Đây là những nỗ lực rất đáng khen và là chìa khóa để bạn cải thiện tình hình tài chính. Mình dự báo rằng với sự chủ động này, dòng tiền của bạn hoàn toàn có thể trở nên ổn định và thậm chí có dư trong những tháng tới, đặc biệt khi các khoản chi bất ngờ được kiểm soát tốt hơn.', 0, '2026-04-30 01:04:54'),
(57, 1, 'anomaly', 'Tháng này, bạn không có bất kỳ khoản thu nhập nào nhưng vẫn chi tiêu 30.000đ cho bữa tối. Đây là một điểm cần chú ý ngay lập tức vì dòng tiền của bạn đang bị âm.', 0, '2026-05-05 00:15:44'),
(58, 1, 'advice', 'Để đảm bảo ổn định tài chính, bạn nên ưu tiên tìm kiếm nguồn thu nhập. Với khoản chi 30.000đ cho một bữa ăn khi chưa có thu nhập, bạn có thể cân nhắc tiết chế hơn hoặc xem xét lại tính cần thiết của những khoản chi tương tự trong tương lai nhé.', 0, '2026-05-05 00:15:44'),
(59, 1, 'forecast', 'Nếu tình trạng thu nhập bằng 0 và vẫn có chi tiêu như vậy tiếp diễn, tài khoản của bạn sẽ nhanh chóng cạn kiệt hoặc bạn sẽ phải dùng đến tiền tiết kiệm/vay mượn, gây áp lực lớn về tài chính trong dài hạn.', 0, '2026-05-05 00:15:44'),
(60, 1, 'summary', 'Xin chào! Tôi là Cố vấn Tài chính AI của CashFlow. Dưới đây là tình hình tài chính tháng này của Bạn:<br><br>\n<b><span style=\"color:green;\">Tổng thu: 18,500,000 đ</span></b><br>\n<b><span style=\"color:red;\">Tổng chi: 8,140,000 đ</span></b><br>\n<b><span style=\"color:blue;\">Số dư: 10,360,000 đ</span></b><br><br>\nTình hình tài chính của Bạn tháng này rất tích cực với khoản <span style=\"color:blue;\">thặng dư lớn</span>. Thu nhập chính đến từ <b>Lương</b> và <b>Freelance</b>.<br>\nVề chi tiêu, các khoản lớn nhất là <b>Nhà cửa</b> (tiền thuê nhà), <b>Đầu tư</b> (chứng khoán) và <b>Mua sắm</b>. Bạn đã duy trì được thói quen tiết kiệm/đầu tư rất tốt!', 0, '2026-05-10 22:37:06');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount_limit` decimal(15,2) NOT NULL,
  `month` tinyint(4) NOT NULL CHECK (`month` between 1 and 12),
  `year` year(4) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`id`, `user_id`, `category_id`, `amount_limit`, `month`, `year`, `created_at`) VALUES
(2, 1, 16, 3000000.00, 5, '2026', '2026-05-09 19:26:27'),
(3, 1, 15, 2000000.00, 5, '2026', '2026-05-09 19:26:27'),
(4, 1, 17, 1000000.00, 5, '2026', '2026-05-09 19:26:27'),
(5, 1, 5, 1500000.00, 5, '2026', '2026-05-09 19:26:27'),
(6, 1, 6, 3000000.00, 5, '2026', '2026-05-09 19:26:27'),
(7, 1, 8, 2000000.00, 5, '2026', '2026-05-09 19:26:27'),
(8, 1, 9, 1000000.00, 5, '2026', '2026-05-09 19:26:27'),
(9, 1, 13, 5000000.00, 5, '2026', '2026-05-09 19:26:27');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `parent_id`, `name`, `type`, `created_at`) VALUES
(1, 1, NULL, 'Chi phí cố định', 'expense', '2026-05-09 19:12:44'),
(2, 1, NULL, 'Chi phí phát sinh', 'expense', '2026-05-09 19:12:44'),
(3, 1, NULL, 'Đầu tư tiết kiệm', 'expense', '2026-05-09 19:12:44'),
(4, 1, NULL, 'Chi tiêu - Sinh hoạt', 'expense', '2026-05-09 19:12:44'),
(5, 1, 1, 'Hóa đơn', 'expense', '2026-05-09 19:12:44'),
(6, 1, 1, 'Nhà cửa', 'expense', '2026-05-09 19:12:44'),
(7, 1, 1, 'Người thân', 'expense', '2026-05-09 19:12:44'),
(8, 1, 2, 'Mua sắm', 'expense', '2026-05-09 19:12:44'),
(9, 1, 2, 'Giải trí', 'expense', '2026-05-09 19:12:44'),
(10, 1, 2, 'Làm đẹp', 'expense', '2026-05-09 19:12:44'),
(11, 1, 2, 'Sức khỏe', 'expense', '2026-05-09 19:12:44'),
(12, 1, 2, 'Từ thiện', 'expense', '2026-05-09 19:12:44'),
(13, 1, 3, 'Đầu tư', 'expense', '2026-05-09 19:12:44'),
(14, 1, 3, 'Học tập', 'expense', '2026-05-09 19:12:44'),
(15, 1, 4, 'Chợ, siêu thị', 'expense', '2026-05-09 19:12:44'),
(16, 1, 4, 'Ăn uống', 'expense', '2026-05-09 19:12:44'),
(17, 1, 4, 'Di chuyển', 'expense', '2026-05-09 19:12:44'),
(18, 1, NULL, 'Lương', 'income', '2026-05-09 19:12:44'),
(19, 1, NULL, 'Tiền Tip / Thưởng', 'income', '2026-05-09 19:12:44'),
(20, 1, NULL, 'Freelance', 'income', '2026-05-09 19:12:44'),
(21, 1, NULL, 'Thu nhập khác', 'income', '2026-05-09 19:12:44');

-- --------------------------------------------------------

--
-- Table structure for table `daily_notes`
--

CREATE TABLE `daily_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note_date` date NOT NULL,
  `content` text NOT NULL,
  `pin_type` enum('none','weekly','monthly') NOT NULL DEFAULT 'none',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_notes`
--

INSERT INTO `daily_notes` (`id`, `user_id`, `note_date`, `content`, `pin_type`, `created_at`) VALUES
(1, 1, '2026-05-05', 'đóng tiền nhà', 'none', '2026-05-10 00:02:33'),
(4, 1, '2026-05-01', 'nhận lương', 'none', '2026-05-10 00:03:01');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `transaction_date` datetime NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `category_id`, `amount`, `transaction_date`, `note`, `created_at`) VALUES
(1, 1, 18, 15000000.00, '2026-05-01 08:30:00', 'Lương tháng 4 từ công ty', '2026-05-09 19:14:44'),
(2, 1, 20, 3500000.00, '2026-05-05 14:15:00', 'Nhận tiền code đồ án Freelance', '2026-05-09 19:14:44'),
(3, 1, 16, 45000.00, '2026-05-01 12:00:00', 'Ăn trưa bún bò', '2026-05-09 19:14:44'),
(4, 1, 16, 120000.00, '2026-05-02 19:30:00', 'Ăn tối với bạn bè', '2026-05-09 19:14:44'),
(5, 1, 17, 60000.00, '2026-05-03 08:00:00', 'Đổ xăng xe máy', '2026-05-09 19:14:44'),
(6, 1, 15, 450000.00, '2026-05-04 17:45:00', 'Đi siêu thị Coopmart mua đồ ăn tuần', '2026-05-09 19:14:44'),
(7, 1, 16, 35000.00, '2026-05-06 07:30:00', 'Cà phê sáng', '2026-05-09 19:14:44'),
(8, 1, 5, 650000.00, '2026-05-02 10:00:00', 'Đóng tiền điện tháng 4', '2026-05-09 19:14:44'),
(9, 1, 6, 3000000.00, '2026-05-05 09:00:00', 'Chuyển khoản tiền thuê nhà', '2026-05-09 19:14:44'),
(10, 1, 8, 850000.00, '2026-05-07 20:15:00', 'Mua áo sơ mi và quần jean mới', '2026-05-09 19:14:44'),
(11, 1, 9, 150000.00, '2026-05-08 21:00:00', 'Xem phim rạp CGV', '2026-05-09 19:14:44'),
(12, 1, 11, 250000.00, '2026-05-09 10:30:00', 'Mua thuốc cảm cúm', '2026-05-09 19:14:44'),
(13, 1, 13, 2000000.00, '2026-05-02 08:00:00', 'Chuyển tiền vào quỹ chứng khoán', '2026-05-09 19:14:44'),
(14, 1, 14, 500000.00, '2026-05-08 15:00:00', 'Mua khóa học lập trình Web', '2026-05-09 19:14:44'),
(15, 1, 16, 30000.00, '2026-05-09 22:50:00', 'Ăn trưa căn tin', '2026-05-09 22:50:31'),
(16, 1, 17, 30000.00, '2026-05-10 22:39:00', 'Đổ xăng', '2026-05-10 22:40:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_first_login` tinyint(1) DEFAULT 1,
  `login_token` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_ai_consult_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `avatar_url`, `email`, `password_hash`, `is_first_login`, `login_token`, `created_at`, `updated_at`, `last_ai_consult_at`) VALUES
(1, 'Nguyễn Văn Duy', '/assets/images/avatars/avatar_6a0096e1e5f3d_1778423521.jpg', 'nvduy180706@gmail.com', '$2y$10$xD16aTVcCHE6S2lefFijnOWqm4FrsC0Sh4SD1edx.tg7GaW4BM1sG', 0, NULL, '2026-04-28 21:47:09', '2026-05-10 22:37:06', '2026-05-10 22:37:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_insights`
--
ALTER TABLE `ai_insights`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_budget` (`user_id`,`category_id`,`month`,`year`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_budgets_period` (`month`,`year`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_category_parent` (`parent_id`);

--
-- Indexes for table `daily_notes`
--
ALTER TABLE `daily_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_date_unique` (`user_id`,`note_date`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_transactions_date` (`transaction_date`),
  ADD KEY `idx_transactions_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_login_token` (`login_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_insights`
--
ALTER TABLE `ai_insights`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `daily_notes`
--
ALTER TABLE `daily_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_insights`
--
ALTER TABLE `ai_insights`
  ADD CONSTRAINT `ai_insights_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `budgets_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_category_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
