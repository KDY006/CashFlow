-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 02:06 PM
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
  `type` enum('anomaly','forecast','advice') NOT NULL,
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
(59, 1, 'forecast', 'Nếu tình trạng thu nhập bằng 0 và vẫn có chi tiêu như vậy tiếp diễn, tài khoản của bạn sẽ nhanh chóng cạn kiệt hoặc bạn sẽ phải dùng đến tiền tiết kiệm/vay mượn, gây áp lực lớn về tài chính trong dài hạn.', 0, '2026-05-05 00:15:44');

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
(1, 1, 9, 1000000.00, 4, '2026', '2026-04-28 21:49:21'),
(2, 1, 10, 500000.00, 4, '2026', '2026-04-28 22:20:29'),
(4, 1, 13, 500000.00, 4, '2026', '2026-04-29 08:00:00'),
(5, 1, 14, 1000000.00, 4, '2026', '2026-04-29 08:00:00'),
(8, 1, 9, 1000000.00, 5, '2026', '2026-05-05 00:35:19'),
(9, 1, 10, 500000.00, 5, '2026', '2026-05-05 00:35:19'),
(10, 1, 13, 500000.00, 5, '2026', '2026-05-05 00:35:19'),
(11, 1, 14, 1000000.00, 5, '2026', '2026-05-05 00:35:19');

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
(8, 1, NULL, 'Lương', 'income', '2026-04-28 21:48:18'),
(9, 1, NULL, 'Ăn bữa', 'expense', '2026-04-28 21:48:33'),
(10, 1, NULL, 'Uống nước', 'expense', '2026-04-28 21:48:37'),
(11, 1, NULL, 'Tip', 'income', '2026-04-28 21:48:44'),
(13, 1, NULL, 'Học tập', 'expense', '2026-04-29 00:00:00'),
(14, 1, NULL, 'Mua sắm', 'expense', '2026-04-29 00:00:00'),
(15, 1, NULL, 'Freelance', 'income', '2026-04-29 00:00:00'),
(16, 1, NULL, 'Sức khỏe', 'expense', '2026-04-29 00:00:00'),
(21, 1, NULL, 'Khác', 'expense', '2026-04-30 00:26:18'),
(22, 1, NULL, 'Đi chơi với bồ', 'expense', '2026-04-30 00:35:11'),
(23, 1, NULL, 'Chi phí cố định', 'expense', '2026-05-09 18:45:25'),
(24, 1, NULL, 'Chi phí phát sinh', 'expense', '2026-05-09 18:45:25'),
(25, 1, NULL, 'Đầu tư tiết kiệm', 'expense', '2026-05-09 18:45:25'),
(26, 1, NULL, 'Chi tiêu - Sinh hoạt', 'expense', '2026-05-09 18:45:25'),
(27, 1, 23, 'Hóa đơn', 'expense', '2026-05-09 18:45:25'),
(28, 1, 23, 'Nhà cửa', 'expense', '2026-05-09 18:45:25'),
(29, 1, 23, 'Người thân', 'expense', '2026-05-09 18:45:25'),
(30, 1, 24, 'Mua sắm', 'expense', '2026-05-09 18:45:25'),
(31, 1, 24, 'Giải trí', 'expense', '2026-05-09 18:45:25'),
(32, 1, 24, 'Làm đẹp', 'expense', '2026-05-09 18:45:25'),
(33, 1, 24, 'Sức khỏe', 'expense', '2026-05-09 18:45:25'),
(34, 1, 24, 'Từ thiện', 'expense', '2026-05-09 18:45:25'),
(35, 1, 25, 'Đầu tư', 'expense', '2026-05-09 18:45:25'),
(36, 1, 25, 'Học tập', 'expense', '2026-05-09 18:45:25'),
(37, 1, 26, 'Chợ, siêu thị', 'expense', '2026-05-09 18:45:25'),
(38, 1, 26, 'Ăn uống', 'expense', '2026-05-09 18:45:25'),
(39, 1, 26, 'Di chuyển', 'expense', '2026-05-09 18:45:25');

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
(3, 1, 9, 35000.00, '2026-04-28 00:00:00', 'Ăn trưa', '2026-04-28 22:46:08'),
(4, 1, 9, 32000.00, '2026-04-28 00:00:00', 'Ăn tối', '2026-04-28 22:46:32'),
(5, 1, 11, 500000.00, '2026-04-28 00:00:00', 'Anh Trân web bán hàng', '2026-04-28 22:46:47'),
(8, 1, 9, 30000.00, '2026-04-27 00:00:00', 'Ăn trưa một mình', '2026-04-28 23:48:29'),
(9, 1, 8, 3000000.00, '2026-04-06 00:00:00', 'Lương SSMC tháng 3', '2026-04-28 23:49:09'),
(10, 1, 9, 6000.00, '2026-04-27 00:00:00', 'Mua trứng', '2026-04-28 23:49:40'),
(11, 1, 10, 15000.00, '2026-04-19 00:00:00', 'cà phê', '2026-04-28 23:50:17'),
(34, 1, 15, 1500000.00, '2026-04-10 00:00:00', 'Nhận tiền code dạo giao diện Web', '2026-04-29 08:00:00'),
(35, 1, 11, 200000.00, '2026-04-15 00:00:00', 'Tool dự báo tài chính', '2026-04-29 08:00:00'),
(36, 1, 13, 120000.00, '2026-04-12 00:00:00', 'Mua giáo trình Lập trình Web', '2026-04-29 08:00:00'),
(37, 1, 10, 35000.00, '2026-04-14 00:00:00', 'Cà phê Highland làm báo cáo Lab', '2026-04-29 08:00:00'),
(38, 1, 13, 40000.00, '2026-04-18 00:00:00', 'In tài liệu môn Mạng máy tính', '2026-04-29 08:00:00'),
(39, 1, 9, 45000.00, '2026-04-20 00:00:00', 'Cơm trưa căn tin', '2026-04-29 08:00:00'),
(40, 1, 9, 50000.00, '2026-04-22 00:00:00', 'Bún bò xào', '2026-04-29 08:00:00'),
(41, 1, 14, 450000.00, '2026-04-25 00:00:00', 'Mua áo thun local brand', '2026-04-29 08:00:00'),
(42, 1, 16, 80000.00, '2026-04-26 00:00:00', 'Mua thuốc cảm', '2026-04-29 08:00:00'),
(43, 1, 10, 60000.00, '2026-04-29 00:00:00', 'Trà sữa Phúc Long', '2026-04-29 08:00:00'),
(44, 1, 10, 600000.00, '2026-04-29 00:00:00', 'Bao công ty', '2026-04-29 09:04:30'),
(46, 1, 9, 30000.00, '2026-05-04 00:00:00', 'Ăn tối', '2026-05-05 00:15:30'),
(47, 1, 21, 10000000.00, '2026-04-23 00:00:00', 'Bị lừa', '2026-05-05 01:12:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_first_login` tinyint(1) DEFAULT 1,
  `login_token` varchar(64) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `is_first_login`, `login_token`, `created_at`, `updated_at`) VALUES
(1, 'Nguyễn Văn Duy', 'nvduy180706@gmail.com', '$2y$10$xD16aTVcCHE6S2lefFijnOWqm4FrsC0Sh4SD1edx.tg7GaW4BM1sG', 0, NULL, '2026-04-28 21:47:09', '2026-04-28 21:47:50');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

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
