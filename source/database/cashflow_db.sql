-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2026 at 07:48 PM
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
  `type` enum('warning','advice','forecast','summary') NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ai_insights`
--

INSERT INTO `ai_insights` (`id`, `user_id`, `type`, `content`, `is_read`, `created_at`) VALUES
(1, 1, 'warning', 'Tháng này bạn đã chi ra tổng cộng 353.000 VNĐ. Chú ý: Mục \'Đi chơi với bồ\' đang chiếm nhiều nhất với 235.000 VNĐ.', 0, '2026-04-29 01:10:14'),
(2, 1, 'advice', 'Lời khuyên: Bạn nên xem xét lại các khoản chi trong nhóm \'Đi chơi với bồ\'. Nếu cắt giảm được 15% ở nhóm này, bạn sẽ có thêm quỹ dự phòng cho tháng sau.', 0, '2026-04-29 01:10:14'),
(3, 1, 'warning', 'Tháng này bạn đã chi ra tổng cộng 353.000 VNĐ. Chú ý: Mục \'Đi chơi với bồ\' đang chiếm nhiều nhất với 235.000 VNĐ.', 0, '2026-04-29 01:10:20'),
(4, 1, 'advice', 'Lời khuyên: Bạn nên xem xét lại các khoản chi trong nhóm \'Đi chơi với bồ\'. Nếu cắt giảm được 15% ở nhóm này, bạn sẽ có thêm quỹ dự phòng cho tháng sau.', 0, '2026-04-29 01:10:20'),
(5, 1, 'advice', 'Hệ thống nhận thấy bạn chi khá nhiều cho mục \"Uống nước\" và cà phê. Bạn có thể cân nhắc việc tự pha chế ở nhà để tiết kiệm quỹ tiền mặt nhé.', 0, '2026-04-28 09:00:00'),
(7, 1, 'forecast', 'Với tiến độ chi tiêu hiện tại và khoản thu nhập thêm từ Freelance, dự kiến bạn sẽ kết thúc tháng 4/2026 với số dư khả dụng khoảng 3.200.000đ. Giữ vững phong độ nhé!', 0, '2026-04-29 11:30:00'),
(8, 1, 'warning', 'Tháng này bạn đã chi ra tổng cộng 998.000 VNĐ. Chú ý: Mục \'Mua sắm\' đang chiếm nhiều nhất với 450.000 VNĐ.', 0, '2026-04-29 07:43:33'),
(9, 1, 'advice', 'Lời khuyên: Bạn nên xem xét lại các khoản chi trong nhóm \'Mua sắm\'. Nếu cắt giảm được 15% ở nhóm này, bạn sẽ có thêm quỹ dự phòng cho tháng sau.', 0, '2026-04-29 07:43:33'),
(10, 1, 'warning', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 0, '2026-04-29 07:51:46'),
(11, 1, 'warning', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 0, '2026-04-29 07:51:59'),
(12, 1, 'warning', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 0, '2026-04-29 07:54:05'),
(13, 1, 'warning', 'Hệ thống AI đang quá tải hoặc cấu hình API Key chưa đúng. Vui lòng thử lại sau.', 0, '2026-04-29 07:54:20'),
(14, 1, 'warning', 'Bạn đang có một tỷ lệ tiết kiệm rất ấn tượng, lên đến hơn 80% thu nhập! Đây là một thành tích tuyệt vời, tuy nhiên, hãy cùng xem lại một chút liệu mình đã ghi nhận đầy đủ tất cả các khoản chi tiêu nhỏ trong tháng chưa nhé, để đảm bảo bức tranh tài chính là hoàn chỉnh nhất.', 0, '2026-04-29 07:57:11'),
(15, 1, 'advice', 'Với khoản tiết kiệm lớn như vậy, bạn có thể nghĩ đến việc bắt đầu xây dựng một quỹ khẩn cấp hoặc đầu tư nhỏ để tiền của mình \'làm việc\' hiệu quả hơn. Đây là thời điểm tốt để đặt ra các mục tiêu tài chính dài hạn!', 0, '2026-04-29 07:57:11'),
(16, 1, 'forecast', 'Nếu bạn duy trì được mức tiết kiệm này, bạn sẽ nhanh chóng đạt được các mục tiêu tài chính lớn như mua nhà, xe, hoặc nghỉ hưu sớm. Tương lai tài chính của bạn đang rất sáng sủa!', 0, '2026-04-29 07:57:11'),
(18, 1, 'advice', 'Bạn đang quản lý tài chính rất xuất sắc với tỷ lệ tiết kiệm ấn tượng gần 70% tổng thu nhập! Để tối ưu hơn nữa, bạn có thể thử chuyển một phần nhỏ từ khoản \'Uống nước\' sang các khoản đầu tư nhỏ hoặc xây dựng quỹ khẩn cấp để tiền của bạn \'làm việc\' hiệu quả hơn nhé.', 0, '2026-04-29 09:06:41'),
(19, 1, 'forecast', 'Với khả năng tiết kiệm mạnh mẽ như hiện tại, nếu bạn duy trì được thói quen này, tương lai tài chính của bạn sẽ rất vững vàng. Bạn hoàn toàn có thể đạt được các mục tiêu lớn như mua sắm tài sản, đầu tư dài hạn hay thậm chí là nghỉ hưu sớm hơn dự kiến đấy!', 0, '2026-04-29 09:06:41'),
(25, 1, 'advice', 'Với tỷ lệ tiết kiệm rất tốt như hiện tại, bạn có thể bắt đầu nghĩ đến việc phân bổ số tiền dư vào các quỹ khẩn cấp hoặc bắt đầu tìm hiểu các kênh đầu tư nhỏ để tiền của bạn \'sinh lời\' nhé!', 0, '2026-04-29 14:41:54'),
(26, 1, 'forecast', 'Nếu bạn tiếp tục duy trì thói quen chi tiêu hợp lý và tiết kiệm tốt như vậy, bạn sẽ sớm xây dựng được một nền tảng tài chính vững vàng, giúp bạn thực hiện được nhiều dự định lớn trong tương lai.', 0, '2026-04-29 14:41:54'),
(35, 1, 'advice', 'Với số tiền tiết kiệm lên đến 3.602.000 VNĐ (khoảng 69% tổng thu nhập), bạn đang có một nền tảng tài chính cực kỳ vững chắc! Đây là thời điểm tuyệt vời để bạn bắt đầu xây dựng quỹ khẩn cấp hoặc tìm hiểu các kênh đầu tư nhỏ để tiền của bạn sinh lời thêm.', 0, '2026-04-29 23:58:36'),
(36, 1, 'forecast', 'Nếu bạn tiếp tục duy trì được thói quen chi tiêu hợp lý và tiết kiệm hiệu quả như hiện tại, mình tin rằng bạn sẽ tích lũy được một khoản đáng kể trong vài tháng tới, giúp bạn tự tin hơn để thực hiện các mục tiêu tài chính lớn hơn trong tương lai.', 0, '2026-04-29 23:58:36'),
(41, 1, 'warning', 'Chào bạn! Nhìn vào chi tiêu tháng này, mình thấy khoản \'Uống nước\' lên đến 710,000 VNĐ là khá cao so với tổng chi tiêu (chiếm gần một nửa đó). Đây là một khoản chi đáng để bạn xem xét kỹ hơn, liệu có cách nào để tiết kiệm hơn ở hạng mục này không nhé?', 0, '2026-04-30 00:06:50'),
(42, 1, 'advice', 'Bạn đang có một khoản tiết kiệm rất ấn tượng, lên đến 3,602,000 VNĐ, chiếm gần 70% tổng thu nhập! Đây là một thành tích tuyệt vời. Với số tiền này, bạn có thể bắt đầu nghĩ đến việc lập một quỹ khẩn cấp vững chắc hoặc đầu tư để tiền của bạn \'làm việc\' hiệu quả hơn nữa nhé.', 0, '2026-04-30 00:06:50'),
(43, 1, 'forecast', 'Nếu bạn tiếp tục duy trì được thói quen chi tiêu hợp lý và tiết kiệm hiệu quả như thế này, mình tin rằng bạn sẽ nhanh chóng đạt được các mục tiêu tài chính cá nhân và xây dựng được một nền tảng tài chính vững vàng cho tương lai!', 0, '2026-04-30 00:06:50'),
(44, 1, 'warning', 'Chào bạn! Nhìn vào số liệu, có vẻ như tháng này bạn đang chi tiêu vượt quá thu nhập khá nhiều, với mức thâm hụt lên tới hơn 6 triệu đồng. Đặc biệt, khoản \'Khác\' chiếm đến 10 triệu đồng là một con số rất lớn, cần được xem xét kỹ lưỡng đó nhé.', 0, '2026-04-30 00:26:44'),
(45, 1, 'advice', 'Lời khuyên chân thành là bạn hãy thử dành thời gian phân loại rõ ràng hơn khoản \'Khác\' 10 triệu đồng này. Việc biết chính xác tiền của mình đi đâu sẽ giúp bạn dễ dàng tìm ra những mục có thể tiết kiệm hoặc cắt giảm, từ đó cân đối lại chi tiêu hiệu quả hơn.', 0, '2026-04-30 00:26:44'),
(46, 1, 'forecast', 'Nếu tình hình chi tiêu vượt thu nhập như hiện tại tiếp diễn mà không có điều chỉnh, rất có thể bạn sẽ phải đối mặt với áp lực tài chính lớn, thậm chí là phát sinh nợ hoặc dần cạn kiệt các khoản dự phòng đấy. Nhưng đừng lo, chúng ta hoàn toàn có thể thay đổi được!', 0, '2026-04-30 00:26:44'),
(54, 1, 'warning', 'Tháng này, điểm đáng lo ngại nhất là khoản chi 10,000,000 VNĐ do bị lừa. Đây là một con số rất lớn, khiến dòng tiền của bạn thâm hụt nghiêm trọng. Hãy cực kỳ cẩn trọng hơn với các giao dịch tài chính và thông tin cá nhân để tránh rủi ro tương tự trong tương lai nhé!', 0, '2026-04-30 01:04:54'),
(55, 1, 'advice', 'Bên cạnh sự cố không mong muốn, mình thấy khoản chi \'Uống nước\' khá cao, đặc biệt là 600,000 VNĐ cho việc \'Bao công ty\'. Việc này tuy thể hiện sự hào phóng nhưng có thể cân nhắc lại để tối ưu chi tiêu, đặc biệt khi dòng tiền đang bị âm. Các khoản mua sắm như áo thun local brand cũng có thể được xem xét kỹ hơn trong những tháng tới để tập trung vào việc tiết kiệm.', 0, '2026-04-30 01:04:54'),
(56, 1, 'forecast', 'Mình rất khuyến khích bạn tiếp tục phát huy các nguồn thu nhập phụ như freelance code dạo và tiền tip từ Anh Trân hay tool dự báo tài chính. Đây là những nỗ lực rất đáng khen và là chìa khóa để bạn cải thiện tình hình tài chính. Mình dự báo rằng với sự chủ động này, dòng tiền của bạn hoàn toàn có thể trở nên ổn định và thậm chí có dư trong những tháng tới, đặc biệt khi các khoản chi bất ngờ được kiểm soát tốt hơn.', 0, '2026-04-30 01:04:54'),
(57, 1, 'warning', 'Tháng này, bạn không có bất kỳ khoản thu nhập nào nhưng vẫn chi tiêu 30.000đ cho bữa tối. Đây là một điểm cần chú ý ngay lập tức vì dòng tiền của bạn đang bị âm.', 0, '2026-05-05 00:15:44'),
(58, 1, 'advice', 'Để đảm bảo ổn định tài chính, bạn nên ưu tiên tìm kiếm nguồn thu nhập. Với khoản chi 30.000đ cho một bữa ăn khi chưa có thu nhập, bạn có thể cân nhắc tiết chế hơn hoặc xem xét lại tính cần thiết của những khoản chi tương tự trong tương lai nhé.', 0, '2026-05-05 00:15:44'),
(59, 1, 'forecast', 'Nếu tình trạng thu nhập bằng 0 và vẫn có chi tiêu như vậy tiếp diễn, tài khoản của bạn sẽ nhanh chóng cạn kiệt hoặc bạn sẽ phải dùng đến tiền tiết kiệm/vay mượn, gây áp lực lớn về tài chính trong dài hạn.', 0, '2026-05-05 00:15:44'),
(60, 1, 'summary', 'Xin chào! Tôi là Cố vấn Tài chính AI của CashFlow. Dưới đây là tình hình tài chính tháng này của Bạn:<br><br>\n<b><span style=\"color:green;\">Tổng thu: 18,500,000 đ</span></b><br>\n<b><span style=\"color:red;\">Tổng chi: 8,140,000 đ</span></b><br>\n<b><span style=\"color:blue;\">Số dư: 10,360,000 đ</span></b><br><br>\nTình hình tài chính của Bạn tháng này rất tích cực với khoản <span style=\"color:blue;\">thặng dư lớn</span>. Thu nhập chính đến từ <b>Lương</b> và <b>Freelance</b>.<br>\nVề chi tiêu, các khoản lớn nhất là <b>Nhà cửa</b> (tiền thuê nhà), <b>Đầu tư</b> (chứng khoán) và <b>Mua sắm</b>. Bạn đã duy trì được thói quen tiết kiệm/đầu tư rất tốt!', 0, '2026-05-10 22:37:06'),
(61, 1, 'warning', '<p>Chào Bạn,</p>\n<p>Tôi là Cố vấn Tài chính AI của CashFlow. Dưới đây là tóm tắt tình hình tài chính tháng này của Bạn:</p>\n\n<p><b>1. Tình hình thu chi tổng quan:</b></p>\n<ul>\n    <li>Tổng thu nhập: <span style=\"color:green;\"><b>18,500,000 đ</b></span></li>\n    <li>Tổng chi tiêu: <span style=\"color:red;\"><b>8,170,000 đ</b></span></li>\n    <li>Tiết kiệm/Dư ra: <span style=\"color:blue;\"><b>10,330,000 đ</b></span></li>\n    <li>Tình hình tài chính của Bạn rất tích cực với khoản dư đáng kể.</li>\n</ul>\n\n<p><b>2. Thói quen tiêu dùng nổi bật:</b></p>\n<ul>\n    <li>Các khoản chi lớn nhất tập trung vào <span style=\"color:purple;\"><b>Nhà cửa (3,000,000 đ)</b></span> và <span style=\"color:purple;\"><b>Đầu tư (2,000,000 đ)</b></span>, cho thấy Bạn có kế hoạch tài chính dài hạn.</li>\n    <li>Bạn cũng chi cho <span style=\"color:purple;\"><b>Mua sắm (850,000 đ)</b></span> và <span style=\"color:purple;\"><b>Học tập (500,000 đ)</b></span>, thể hiện sự quan tâm đến bản thân và phát triển cá nhân.</li>\n    <li>Các chi phí thiết yếu như <span style=\"color:purple;\"><b>Ăn uống (230,000 đ)</b></span> và <span style=\"color:purple;\"><b>Di chuyển (90,000 đ)</b></span> được quản lý ở mức hợp lý.</li>\n</ul>\n\n<p><b>Lời khuyên:</b> Hãy tiếp tục phát huy thói quen tiết kiệm và đầu tư hiệu quả này để xây dựng nền tảng tài chính vững chắc, Bạn nhé!</p>', 0, '2026-05-11 15:31:37'),
(62, 1, 'advice', 'Chào Bạn,<br>\n<br>\nVới vai trò là Cố vấn Tài chính AI từ hệ thống CashFlow, Tôi đã phân tích tình hình tài chính của Bạn trong kỳ gần đây và nhận thấy Bạn đang quản lý dòng tiền rất hiệu quả. <br>\n<br>\n<b>Tình hình tài chính tổng quan:</b><br>\n<ul>\n    <li>Tổng thu nhập: <b>18,500,000 đ</b></li>\n    <li>Tổng chi tiêu: <b>8,176,000 đ</b></li>\n    <li>Số dư tiền mặt: <b>10,324,000 đ</b></li>\n</ul>\n<br>\nBạn đang có một số dư tiền mặt rất ấn tượng, cho thấy khả năng kiếm tiền và quản lý chi tiêu cá nhân xuất sắc. Đây là một nền tảng vững chắc để đạt được các mục tiêu tài chính lớn hơn.<br>\n<br>\n<b>Phân tích chi tiêu chi tiết:</b><br>\nDưới đây là các khoản chi tiêu lớn nhất của Bạn theo từng danh mục:<br>\n<ul>\n    <li><b>Nhà cửa:</b> 3,000,000 đ (Thuê nhà) - Đây là khoản chi cố định và thiết yếu.</li>\n    <li><b>Đầu tư:</b> 2,000,000 đ (Chuyển vào quỹ chứng khoán) - Một khoản chi rất tốt, cho thấy Bạn đang có kế hoạch phát triển tài sản.</li>\n    <li><b>Mua sắm:</b> 850,000 đ (Mua áo sơ mi và quần jean mới) - Chi tiêu cho nhu cầu cá nhân.</li>\n    <li><b>Hóa đơn:</b> 650,000 đ (Tiền điện) - Chi phí sinh hoạt cơ bản.</li>\n    <li><b>Học tập:</b> 500,000 đ (Khóa học lập trình Web) - Khoản đầu tư vào bản thân, rất đáng giá.</li>\n    <li><b>Chợ, siêu thị:</b> 450,000 đ (Mua đồ ăn tuần) - Chi phí sinh hoạt hàng ngày.</li>\n    <li>Các danh mục khác như Sức khỏe, Ăn uống, Giải trí, Di chuyển chiếm phần nhỏ hơn trong tổng chi.</li>\n</ul>\n<br>\n<b>Lời khuyên hữu ích để tối ưu dòng tiền:</b><br>\nMặc dù Bạn đang có một dòng tiền cực kỳ khỏe mạnh, Tôi có một vài gợi ý nhỏ để giúp Bạn tối ưu hóa hơn nữa và đạt được các mục tiêu tài chính nhanh hơn:<br>\n<ul>\n    <li><b>Tăng cường đầu tư:</b> Với số dư tiền mặt lớn (hơn 10 triệu đồng mỗi tháng), Bạn có thể cân nhắc tăng cường khoản đầu tư hàng tháng vào quỹ chứng khoán hoặc các kênh đầu tư khác phù hợp với mục tiêu và mức độ chấp nhận rủi ro của Bạn. Việc này sẽ giúp tiền của Bạn \"làm việc\" hiệu quả hơn và gia tăng tài sản theo thời gian.</li>\n    <li><b>Đánh giá chi tiêu mua sắm:</b> Khoản 850,000 đ cho \"áo sơ mi và quần jean mới\" là khá lớn cho một lần mua sắm cá nhân. Bạn có thể xem xét:\n        <ul>\n            <li>Lập ngân sách cụ thể cho danh mục mua sắm để đảm bảo chi tiêu có kế hoạch và tránh lãng phí.</li>\n            <li>Tìm kiếm các chương trình khuyến mãi, giảm giá hoặc cân nhắc mua sắm vào các dịp đặc biệt để tối ưu chi phí.</li>\n            <li>Đánh giá lại nhu cầu thực sự trước khi mua để đảm bảo mỗi khoản chi đều mang lại giá trị và sự cần thiết tối ưu.</li>\n        </ul>\n    </li>\n    <li><b>Lên kế hoạch cho số tiền dư:</b> Bạn có một khoản tiền dư rất lớn mỗi tháng. Hãy đặt ra các mục tiêu cụ thể cho khoản tiền này, ví dụ:\n        <ul>\n            <li>Xây dựng quỹ khẩn cấp vững chắc (nếu chưa có đủ 3-6 tháng chi phí sinh hoạt).</li>\n            <li>Tiết kiệm cho mục tiêu dài hạn (mua nhà, mua xe, du lịch, học vấn cao hơn).</li>\n            <li>Đa dạng hóa danh mục đầu tư để giảm thiểu rủi ro và tăng cơ hội sinh lời.</li>\n        </ul>\n        Việc có một kế hoạch rõ ràng sẽ giúp Bạn sử dụng số tiền dư một cách có chủ đích và hiệu quả hơn, biến nó thành công cụ để đạt được các ước mơ tài chính.\n    </li>\n    <li><b>Tiếp tục duy trì thói quen tốt:</b> Bạn đang có những khoản chi rất hợp lý cho Học tập (đầu tư vào bản thân) và các chi phí sinh hoạt thiết yếu được kiểm soát tốt. Thu nhập từ công việc Freelance cũng là một điểm cộng lớn. Hãy tiếp tục phát huy những thói quen quản lý tài chính tích cực này để duy trì sự ổn định và phát triển tài chính.</li>\n</ul>\n<br>\nBạn đang đi đúng hướng trên con đường tài chính cá nhân. Hãy tiếp tục theo dõi và điều chỉnh kế hoạch để đạt được sự thịnh vượng bền vững nhé!<br>\n<br>\nTrân trọng,<br>\nCố vấn Tài chính AI của hệ thống CashFlow', 0, '2026-05-11 16:05:47'),
(66, 1, 'forecast', 'Tôi là Cố vấn Tài chính AI của hệ thống CashFlow.<br><br>\nDựa trên dữ liệu giao dịch của Bạn từ ngày 01/05/2026 đến 11/05/2026, Tôi xin đưa ra dự báo về tình hình tài chính của Bạn đến cuối tháng 05/2026.<br><br>\n\n<b>1. Tình hình tài chính hiện tại (tính đến 11/05/2026):</b>\n<ul>\n    <li>Tổng thu nhập: 0 đ</li>\n    <li>Tổng chi tiêu: 8.303.000 đ</li>\n</ul>\nTrong 11 ngày đầu tháng, các khoản chi tiêu lớn đã diễn ra bao gồm:\n<ul>\n    <li>Chi phí cố định/định kỳ:\n        <ul>\n            <li>Thuê nhà: 3.000.000 đ (đã chi ngày 05/05)</li>\n            <li>Hóa đơn tiền điện: 650.000 đ (đã chi ngày 02/05)</li>\n        </ul>\n    </li>\n    <li>Chi phí lớn khác:\n        <ul>\n            <li>Đầu tư chứng khoán: 2.000.000 đ (đã chi ngày 02/05)</li>\n            <li>Mua sắm (áo sơ mi và quần jean): 850.000 đ (đã chi ngày 07/05)</li>\n            <li>Khóa học lập trình Web: 500.000 đ (đã chi ngày 08/05)</li>\n        </ul>\n    </li>\n    <li>Chi phí sinh hoạt hàng tuần/hàng ngày:\n        <ul>\n            <li>Chợ, siêu thị: 450.000 đ (đã chi ngày 04/05)</li>\n            <li>Sức khỏe (thuốc cảm cúm): 250.000 đ (đã chi ngày 09/05)</li>\n            <li>Giải trí (xem phim): 150.000 đ (đã chi ngày 08/05)</li>\n            <li>Ăn uống: 307.000 đ</li>\n            <li>Di chuyển: 96.000 đ</li>\n        </ul>\n    </li>\n</ul>\n<br>\n\n<b>2. DỰ BÁO tình hình tài chính cuối tháng 05/2026:</b>\n<br>\nVới tổng thu nhập hiện tại là 0 đ và các khoản chi tiêu lớn đã phát sinh, tình hình tài chính của Bạn đang ở mức đáng báo động.\n<br>\nDựa trên thói quen chi tiêu trong 11 ngày đầu tháng, Tôi dự kiến các khoản chi tiêu khác sẽ tiếp tục phát sinh trong 20 ngày còn lại của tháng 05/2026 như sau:\n<ul>\n    <li><b>Chi tiêu hàng ngày (ăn uống, đi lại):</b> Trung bình khoảng 36.600 đ/ngày. Dự kiến thêm khoảng 732.000 đ (36.600 đ x 20 ngày).</li>\n    <li><b>Chi tiêu mua sắm cho chợ, siêu thị:</b> Với tần suất khoảng 1 lần/tuần, dự kiến Bạn sẽ có thêm khoảng 3 lần chi tiêu này, tương đương 1.350.000 đ (450.000 đ x 3 lần).</li>\n    <li><b>Các chi phí phát sinh khác:</b> Có thể bao gồm giải trí, sức khỏe hoặc các khoản mua sắm nhỏ khác. Tôi sẽ không dự báo cụ thể số tiền nhưng Bạn nên cân nhắc vì chúng có thể làm tăng thêm gánh nặng tài chính.</li>\n</ul>\n<br>\nTổng chi tiêu dự kiến đến cuối tháng 05/2026 có thể lên đến khoảng <b>10.385.000 đ</b> (8.303.000 đ đã chi + 2.082.000 đ dự kiến).\n<br>\nVới tổng thu nhập 0 đ, Bạn đang đối mặt với mức thâm hụt tài chính rất lớn. Tôi khẩn cấp khuyến nghị Bạn cần có nguồn thu nhập để cân bằng các khoản chi này.\n<br><br>\n\n<b>3. Các khoản chi sắp tới Bạn cần lưu ý:</b>\n<br>\nDựa trên thói quen chi tiêu, Tôi nhận thấy các khoản sau có thể tiếp tục phát sinh hoặc là các khoản cố định hàng tháng cần chuẩn bị cho các tháng tiếp theo:\n<ul>\n    <li><b>Ăn uống và Di chuyển:</b> Đây là các chi phí hàng ngày và sẽ tiếp tục phát sinh đều đặn. Bạn có thể xem xét cắt giảm các khoản ăn vặt, ăn ngoài không cần thiết để tiết kiệm.</li>\n    <li><b>Chợ, siêu thị:</b> Chi phí này thường xuyên (khoảng 1 lần/tuần). Bạn có thể lập danh sách mua sắm để tránh chi tiêu lãng phí và mua các mặt hàng thiết yếu.</li>\n    <li><b>Sức khỏe:</b> Chi phí mua thuốc cảm cúm là một khoản chi đột xuất. Bạn nên dự phòng một quỹ khẩn cấp cho các trường hợp tương tự để không ảnh hưởng đến ngân sách hàng ngày.</li>\n    <li><b>Giải trí:</b> Việc xem phim rạp là khoản chi không thiết yếu. Bạn có thể cân nhắc tần suất hoặc tìm các hình thức giải trí tiết kiệm hơn trong giai đoạn này.</li>\n    <li><b>Mua sắm:</b> Ngoài chụp ảnh thẻ, khoản mua sắm áo quần mới 850.000 đ là khá lớn. Hãy xem xét nhu cầu thực sự trước khi mua sắm các mặt hàng giá trị cao.</li>\n    <li><b>Học tập và Đầu tư:</b> Đây là những khoản chi mang tính đầu tư cho bản thân và tương lai. Tuy nhiên, với tình hình tài chính hiện tại, Bạn cần đảm bảo có đủ nguồn thu nhập trước khi tiếp tục các khoản chi này hoặc cân nhắc tạm hoãn nếu chưa thực sự cần thiết.</li>\n    <li><b>Các hóa đơn và tiền thuê nhà:</b> Mặc dù đã chi trả cho tháng 5, Bạn cần chuẩn bị cho các khoản chi cố định này vào đầu các tháng tiếp theo.</li>\n</ul>\n<br>\n<b>Khuyến nghị chung:</b>\n<br>\nTôi đặc biệt nhấn mạnh rằng việc không có nguồn thu nhập trong khi chi tiêu liên tục là rất rủi ro. Bạn cần khẩn trương tìm kiếm nguồn thu nhập để trang trải các chi phí đã phát sinh và dự kiến. Đồng thời, hãy rà soát lại tất cả các khoản chi, đặc biệt là các khoản không thiết yếu, để cắt giảm tối đa cho đến khi tình hình tài chính ổn định hơn.\n<br>\nBạn có muốn Tôi giúp phân tích sâu hơn về một hạng mục chi tiêu cụ thể nào không?', 0, '2026-05-12 07:02:07'),
(67, 1, 'warning', '<b>TỔNG QUÁT:</b><br>\nTình hình tài chính của bạn đang ở mức <b>BÁO ĐỘNG ĐỎ</b>. Bạn đang chi tiêu vượt thu với tổng chi (4.849.000 đ) cao hơn tổng thu (4.172.490 đ), dẫn đến thâm hụt 676.510 đ. Việc chi tiêu vượt ngân sách ngay khi vừa nhận lương là dấu hiệu của việc quản lý dòng tiền thiếu kỷ luật.<br><br>\n\n<b>CỤ THỂ:</b><br>\n<ul>\n<li><b>Đầu tư quá đà:</b> Khoản chi 2.000.000 đ vào chứng khoán chiếm gần 48% thu nhập, gây áp lực lớn lên dòng tiền hàng ngày.</li>\n<li><b>Mua sắm thiếu kiểm soát:</b> Bạn đã chi 956.000 đ cho mua sắm (áo quần, ảnh thẻ), chiếm 23% thu nhập. Đây là danh mục cần cắt giảm ngay.</li>\n<li><b>Chi phí phát sinh:</b> Các khoản chi nhỏ lẻ cho ăn uống, ăn vặt dù không lớn nhưng cộng dồn lại tạo ra gánh nặng cho ngân sách.</li>\n<li><b>Dòng tiền âm:</b> Bạn đã tiêu sạch thu nhập tháng 4 và đang phải sử dụng đến khoản dự phòng hoặc thấu chi.</li>\n</ul><br>\n\n<b>KẾT LUẬN:</b><br>\nBạn cần thực hiện ngay 3 hành động: <b>(1) Dừng mọi hoạt động mua sắm không thiết yếu</b> cho đến hết tháng. <b>(2) Đánh giá lại tỷ lệ đầu tư</b>, không nên đầu tư quá 10-20% thu nhập khi thu nhập chưa ổn định. <b>(3) Thiết lập hạn mức chi tiêu hàng ngày</b> (ví dụ: tối đa 50.000 đ/ngày cho ăn uống) để tránh tình trạng \"vung tay quá trán\". Nếu không thay đổi, bạn sẽ sớm rơi vào bẫy nợ nần.', 0, '2026-05-13 00:23:59'),
(68, 1, 'summary', '<b>TỔNG QUÁT:</b><br>\nTài chính của bạn đang ở trạng thái <b>âm</b>. Bạn đã chi tiêu vượt quá số tiền thu nhập trong tháng, dẫn đến thâm hụt ngân sách.<br><br>\n\n<b>CỤ THỂ:</b><br>\n<ul>\n<li>Tổng thu: 4.172.490 đ | Tổng chi: 4.849.000 đ (Thâm hụt: 676.510 đ).</li>\n<li>Khoản chi lớn nhất: Đầu tư chứng khoán (2.000.000 đ), chiếm gần 50% thu nhập.</li>\n<li>Mua sắm & Giải trí: Chiếm tỷ trọng cao (hơn 1 triệu đ) so với tổng thu nhập.</li>\n<li>Chi tiêu nhỏ lẻ: Ăn uống và di chuyển diễn ra hàng ngày, tích tiểu thành đại.</li>\n</ul><br>\n\n<b>KẾT LUẬN:</b><br>\nBạn đang đầu tư rất mạnh tay (chiếm 50% thu nhập), điều này tốt cho tương lai nhưng gây áp lực lên dòng tiền hiện tại. <b>Lời khuyên:</b> Hãy cắt giảm chi phí mua sắm không cần thiết và điều chỉnh lại quỹ đầu tư cho phù hợp với khả năng tài chính thực tế để tránh mất cân đối dòng tiền.', 0, '2026-05-13 00:24:10');

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
(9, 1, 13, 5000000.00, 5, '2026', '2026-05-09 19:26:27'),
(10, 1, 16, 3000000.00, 6, '2026', '2026-05-11 16:53:41'),
(11, 1, 15, 2000000.00, 6, '2026', '2026-05-11 16:53:41'),
(12, 1, 17, 1000000.00, 6, '2026', '2026-05-11 16:53:41'),
(13, 1, 5, 1500000.00, 6, '2026', '2026-05-11 16:53:41'),
(14, 1, 6, 3000000.00, 6, '2026', '2026-05-11 16:53:41'),
(15, 1, 8, 2000000.00, 6, '2026', '2026-05-11 16:53:41'),
(16, 1, 9, 1000000.00, 6, '2026', '2026-05-11 16:53:41'),
(17, 1, 13, 5000000.00, 6, '2026', '2026-05-11 16:53:41'),
(25, 1, 16, 3000000.00, 7, '2026', '2026-05-11 16:53:41'),
(26, 1, 15, 2000000.00, 7, '2026', '2026-05-11 16:53:41'),
(27, 1, 17, 1000000.00, 7, '2026', '2026-05-11 16:53:41'),
(28, 1, 5, 1500000.00, 7, '2026', '2026-05-11 16:53:41'),
(29, 1, 6, 3000000.00, 7, '2026', '2026-05-11 16:53:41'),
(30, 1, 8, 2000000.00, 7, '2026', '2026-05-11 16:53:41'),
(31, 1, 9, 1000000.00, 7, '2026', '2026-05-11 16:53:41'),
(32, 1, 13, 5000000.00, 7, '2026', '2026-05-11 16:53:41'),
(40, 1, 16, 3000000.00, 8, '2026', '2026-05-11 16:53:42'),
(41, 1, 15, 2000000.00, 8, '2026', '2026-05-11 16:53:42'),
(42, 1, 17, 1000000.00, 8, '2026', '2026-05-11 16:53:42'),
(43, 1, 5, 1500000.00, 8, '2026', '2026-05-11 16:53:42'),
(44, 1, 6, 3000000.00, 8, '2026', '2026-05-11 16:53:42'),
(45, 1, 8, 2000000.00, 8, '2026', '2026-05-11 16:53:42'),
(46, 1, 9, 1000000.00, 8, '2026', '2026-05-11 16:53:42'),
(47, 1, 13, 5000000.00, 8, '2026', '2026-05-11 16:53:42');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `user_id`, `parent_id`, `name`, `type`, `created_at`) VALUES
(1, NULL, NULL, 'Chi phí cố định', 'expense', '2026-05-09 19:12:44'),
(2, NULL, NULL, 'Chi phí phát sinh', 'expense', '2026-05-09 19:12:44'),
(3, NULL, NULL, 'Đầu tư tiết kiệm', 'expense', '2026-05-09 19:12:44'),
(4, NULL, NULL, 'Chi tiêu - Sinh hoạt', 'expense', '2026-05-09 19:12:44'),
(5, NULL, 1, 'Hóa đơn', 'expense', '2026-05-09 19:12:44'),
(6, NULL, 1, 'Nhà cửa', 'expense', '2026-05-09 19:12:44'),
(7, NULL, 1, 'Người thân', 'expense', '2026-05-09 19:12:44'),
(8, NULL, 2, 'Mua sắm', 'expense', '2026-05-09 19:12:44'),
(9, NULL, 2, 'Giải trí', 'expense', '2026-05-09 19:12:44'),
(10, NULL, 2, 'Làm đẹp', 'expense', '2026-05-09 19:12:44'),
(11, NULL, 2, 'Sức khỏe', 'expense', '2026-05-09 19:12:44'),
(12, NULL, 2, 'Từ thiện', 'expense', '2026-05-09 19:12:44'),
(13, NULL, 3, 'Đầu tư', 'expense', '2026-05-09 19:12:44'),
(14, NULL, 3, 'Học tập', 'expense', '2026-05-09 19:12:44'),
(15, NULL, 4, 'Chợ, siêu thị', 'expense', '2026-05-09 19:12:44'),
(16, NULL, 4, 'Ăn uống', 'expense', '2026-05-09 19:12:44'),
(17, NULL, 4, 'Di chuyển', 'expense', '2026-05-09 19:12:44'),
(18, NULL, NULL, 'Lương', 'income', '2026-05-09 19:12:44'),
(19, NULL, NULL, 'Tiền Tip / Thưởng', 'income', '2026-05-09 19:12:44'),
(20, NULL, NULL, 'Freelance', 'income', '2026-05-09 19:12:44'),
(21, NULL, NULL, 'Thu nhập khác', 'income', '2026-05-09 19:12:44');

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
(11, 1, '2026-05-06', 'Nhận lương', 'none', '2026-05-11 21:50:30');

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
(3, 1, 16, 45000.00, '2026-05-01 12:00:00', 'Ăn trưa bún bò', '2026-05-09 19:14:44'),
(4, 1, 16, 120000.00, '2026-05-02 19:30:00', 'Ăn tối với bạn bè', '2026-05-09 19:14:44'),
(5, 1, 17, 60000.00, '2026-05-03 08:00:00', 'Đổ xăng xe máy', '2026-05-09 19:14:44'),
(6, 1, 15, 450000.00, '2026-05-04 17:45:00', 'Đi siêu thị Coopmart mua đồ ăn tuần', '2026-05-09 19:14:44'),
(7, 1, 16, 35000.00, '2026-05-06 07:30:00', 'Cà phê sáng', '2026-05-09 19:14:44'),
(8, 1, 5, 100000.00, '2026-05-02 10:00:00', 'Đóng tiền điện tháng 4', '2026-05-09 19:14:44'),
(10, 1, 8, 850000.00, '2026-05-07 20:15:00', 'Mua áo sơ mi và quần jean mới', '2026-05-09 19:14:44'),
(11, 1, 9, 150000.00, '2026-05-08 21:00:00', 'Xem phim rạp CGV', '2026-05-09 19:14:44'),
(12, 1, 11, 250000.00, '2026-05-09 10:30:00', 'Mua thuốc cảm cúm', '2026-05-09 19:14:44'),
(13, 1, 13, 2000000.00, '2026-05-02 08:00:00', 'Chuyển tiền vào quỹ chứng khoán', '2026-05-09 19:14:44'),
(14, 1, 14, 500000.00, '2026-05-08 15:00:00', 'Mua khóa học lập trình Web', '2026-05-09 19:14:44'),
(15, 1, 16, 30000.00, '2026-05-09 22:50:00', 'Ăn trưa căn tin', '2026-05-09 22:50:31'),
(16, 1, 17, 30000.00, '2026-05-10 22:39:00', 'Đổ xăng', '2026-05-10 22:40:18'),
(26, 1, 17, 6000.00, '2026-05-11 21:35:00', 'Bus', '2026-05-11 21:35:58'),
(27, 1, 16, 15000.00, '2026-05-11 21:35:00', 'Ăn tối cơm chay', '2026-05-11 21:36:38'),
(28, 1, 8, 50000.00, '2026-05-11 21:36:00', 'chụp ảnh thẻ', '2026-05-11 21:38:27'),
(30, 1, 16, 32000.00, '2026-05-01 08:47:00', 'Ăn sáng 2 ổ bánh mì', '2026-05-11 21:48:37'),
(31, 1, 16, 12000.00, '2026-05-01 20:49:00', 'Mua nước mía', '2026-05-11 21:50:08'),
(32, 1, 16, 18000.00, '2026-05-11 22:02:00', 'ăn vặt đêm', '2026-05-11 22:02:25'),
(33, 1, 16, 10000.00, '2026-05-12 07:02:00', 'ăn sáng', '2026-05-12 07:03:24'),
(34, 1, 18, 4172490.00, '2026-05-06 07:37:00', 'Lương tháng 4 SSMC', '2026-05-12 07:38:05'),
(35, 1, 17, 30000.00, '2026-05-12 07:53:00', 'đổ xăng', '2026-05-12 07:53:42'),
(36, 1, 8, 56000.00, '2026-05-12 08:37:00', 'mua áo', '2026-05-12 08:37:41');

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
(1, 'Nguyễn Văn Duy', 'avatar_6a01bbbe83e2f_1778498494.jpg', 'nvduy180706@gmail.com', '$2y$10$oGEdQteEJTqaGcPurIM7M.jta386S114QeXbv0KpVuBF5eoxh3qmG', 0, NULL, '2026-04-28 21:47:09', '2026-05-13 00:46:36', '2026-05-12 19:24:10'),
(5, 'Teest', '', 'kdyforwork@gmail.com', '$2y$10$/86jRY09EO/v95Qi.hvZ/OCA/GDasyyVc/IqgGEWWregFdKZHIQgO', 0, NULL, '2026-05-11 19:06:08', '2026-05-13 00:47:42', NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `daily_notes`
--
ALTER TABLE `daily_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
