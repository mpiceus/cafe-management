-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th5 31, 2026 lúc 02:42 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quan_ly_cafe`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_don_nhap`
--

CREATE TABLE `chi_tiet_don_nhap` (
  `ma_chi_tiet_nhap` int(11) NOT NULL,
  `ma_don_nhap` int(11) NOT NULL,
  `ma_nguyen_lieu` int(11) NOT NULL,
  `so_luong` decimal(10,2) NOT NULL CHECK (`so_luong` > 0),
  `don_vi_mua` varchar(10) DEFAULT NULL,
  `so_luong_nhap_kho` decimal(10,2) DEFAULT NULL,
  `don_gia` decimal(10,2) NOT NULL CHECK (`don_gia` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_don_nhap`
--

INSERT INTO `chi_tiet_don_nhap` (`ma_chi_tiet_nhap`, `ma_don_nhap`, `ma_nguyen_lieu`, `so_luong`, `don_vi_mua`, `so_luong_nhap_kho`, `don_gia`) VALUES
(1, 1, 1, 5000.00, NULL, NULL, 220.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_hoa_don`
--

CREATE TABLE `chi_tiet_hoa_don` (
  `ma_chi_tiet` int(11) NOT NULL,
  `ma_hoa_don` int(11) NOT NULL,
  `ma_mon` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL CHECK (`so_luong` > 0),
  `che_do` enum('chi_nong','chi_lanh') DEFAULT NULL,
  `ghi_chu` text DEFAULT NULL,
  `trang_thai_pha_che` enum('cho_pha_che','dang_pha_che','da_hoan_thanh') NOT NULL DEFAULT 'cho_pha_che'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `chi_tiet_hoa_don`
--

INSERT INTO `chi_tiet_hoa_don` (`ma_chi_tiet`, `ma_hoa_don`, `ma_mon`, `so_luong`, `che_do`, `ghi_chu`, `trang_thai_pha_che`) VALUES
(1, 9, 1, 1, 'chi_nong', NULL, 'da_hoan_thanh'),
(2, 10, 1, 1, 'chi_nong', NULL, 'cho_pha_che'),
(3, 11, 1, 1, 'chi_nong', NULL, 'da_hoan_thanh'),
(4, 17, 1, 1, 'chi_nong', NULL, 'da_hoan_thanh'),
(5, 18, 1, 1, 'chi_nong', NULL, 'da_hoan_thanh'),
(6, 20, 1, 1, 'chi_nong', NULL, 'da_hoan_thanh'),
(7, 22, 1, 1, 'chi_nong', NULL, 'da_hoan_thanh'),
(8, 23, 1, 1, 'chi_nong', NULL, 'da_hoan_thanh'),
(9, 28, 2, 1, 'chi_nong', NULL, 'cho_pha_che'),
(10, 28, 1, 1, 'chi_nong', NULL, 'cho_pha_che');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_topping`
--

CREATE TABLE `chi_tiet_topping` (
  `ma_chi_tiet` int(11) NOT NULL,
  `ma_mon` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL DEFAULT 1 CHECK (`so_luong` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chi_tiet_tuy_chinh`
--

CREATE TABLE `chi_tiet_tuy_chinh` (
  `ma_ct` int(11) NOT NULL,
  `ma_nguyen_lieu` int(11) NOT NULL,
  `ti_le` int(11) NOT NULL CHECK (`ti_le` >= 0 and `ti_le` <= 200)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cong_thuc`
--

CREATE TABLE `cong_thuc` (
  `ma_mon` int(11) NOT NULL,
  `ma_nguyen_lieu` int(11) NOT NULL,
  `so_luong` decimal(10,2) NOT NULL CHECK (`so_luong` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_nhap`
--

CREATE TABLE `don_nhap` (
  `ma_don_nhap` int(11) NOT NULL,
  `ma_nha_cung_cap` int(11) NOT NULL,
  `ma_nguoi_dung` int(11) NOT NULL,
  `tong_tien` decimal(10,2) NOT NULL DEFAULT 0.00 CHECK (`tong_tien` >= 0),
  `ngay_nhap` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_nhap`
--

INSERT INTO `don_nhap` (`ma_don_nhap`, `ma_nha_cung_cap`, `ma_nguoi_dung`, `tong_tien`, `ngay_nhap`) VALUES
(1, 1, 1, 2850000.00, '2026-05-23 09:28:14');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `gia_mon`
--

CREATE TABLE `gia_mon` (
  `ma_gia_mon` int(11) NOT NULL,
  `ma_mon` int(11) NOT NULL,
  `gia` decimal(10,2) NOT NULL CHECK (`gia` >= 0),
  `ngay_ap_dung` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `gia_mon`
--

INSERT INTO `gia_mon` (`ma_gia_mon`, `ma_mon`, `gia`, `ngay_ap_dung`) VALUES
(1, 1, 40000.00, '2026-05-18 11:41:07'),
(2, 2, 45000.00, '2026-05-18 11:41:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoa_don`
--

CREATE TABLE `hoa_don` (
  `ma_hoa_don` int(11) NOT NULL,
  `ma_thanh_toan` varchar(50) DEFAULT NULL,
  `ma_nguoi_dung` int(11) NOT NULL,
  `thoi_gian_tao` datetime NOT NULL DEFAULT current_timestamp(),
  `thoi_gian_thanh_toan` timestamp NULL DEFAULT NULL,
  `tong_tien` decimal(10,2) NOT NULL DEFAULT 0.00 CHECK (`tong_tien` >= 0),
  `phuong_thuc_thanh_toan` enum('tien_mat','chuyen_khoan') NOT NULL,
  `trang_thai` enum('dang_tao','da_thanh_toan','da_hoan_thanh') NOT NULL DEFAULT 'dang_tao'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `hoa_don`
--

INSERT INTO `hoa_don` (`ma_hoa_don`, `ma_thanh_toan`, `ma_nguoi_dung`, `thoi_gian_tao`, `thoi_gian_thanh_toan`, `tong_tien`, `phuong_thuc_thanh_toan`, `trang_thai`) VALUES
(9, 'DH9', 1, '2026-05-30 20:22:38', '2026-05-30 13:22:38', 40000.00, 'tien_mat', 'da_hoan_thanh'),
(10, 'DH10', 1, '2026-05-30 20:23:19', NULL, 40000.00, 'chuyen_khoan', 'dang_tao'),
(11, 'DH11', 1, '2026-05-30 23:25:42', '2026-05-30 16:25:42', 40000.00, 'tien_mat', 'da_hoan_thanh'),
(17, 'DH17', 1, '2026-05-30 23:28:32', '2026-05-30 16:28:32', 40000.00, 'tien_mat', 'da_hoan_thanh'),
(18, 'DH18', 1, '2026-05-30 23:28:42', '2026-05-30 16:28:42', 40000.00, 'tien_mat', 'da_hoan_thanh'),
(20, 'DH20', 1, '2026-05-30 23:30:31', '2026-05-30 16:30:31', 40000.00, 'tien_mat', 'da_hoan_thanh'),
(22, 'DH22', 1, '2026-05-30 23:31:13', '2026-05-30 16:31:13', 40000.00, 'tien_mat', 'da_hoan_thanh'),
(23, 'DH23', 1, '2026-05-30 23:31:21', '2026-05-30 16:31:21', 40000.00, 'tien_mat', 'da_hoan_thanh'),
(28, 'DH28', 1, '2026-05-30 23:44:00', NULL, 85000.00, 'chuyen_khoan', 'dang_tao');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loai_mon`
--

CREATE TABLE `loai_mon` (
  `ma_loai_mon` int(11) NOT NULL,
  `ten_loai_mon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `loai_mon`
--

INSERT INTO `loai_mon` (`ma_loai_mon`, `ten_loai_mon`) VALUES
(1, 'Cà phê truyền thống'),
(2, 'Cà phê Ý'),
(3, 'Đồ ăn');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_05_26_000000_create_nguoi_dung_table', 1),
(5, '2026_05_26_010000_create_loai_mon_and_mon_tables', 1),
(6, '2026_05_26_020000_create_gia_mon_table', 1),
(7, '2026_05_27_120000_add_purchase_fields_to_chi_tiet_don_nhap_table', 1),
(8, '2026_05_31_000001_add_sepay_fields_to_hoa_don', 1),
(9, '2026_05_31_000002_create_sepay_transactions_table', 2),
(10, '2026_05_31_000003_create_sepay_refunds_table', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `mon`
--

CREATE TABLE `mon` (
  `ma_mon` int(11) NOT NULL,
  `ma_loai_mon` int(11) NOT NULL,
  `ten_mon` varchar(100) NOT NULL,
  `mo_ta` text DEFAULT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `che_do_phuc_vu` enum('ca_hai','chi_nong','chi_lanh','khong_ap_dung') NOT NULL DEFAULT 'khong_ap_dung',
  `cho_them_topping` tinyint(1) NOT NULL DEFAULT 0,
  `trang_thai` enum('dang_ban','dung_ban') NOT NULL DEFAULT 'dang_ban'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `mon`
--

INSERT INTO `mon` (`ma_mon`, `ma_loai_mon`, `ten_mon`, `mo_ta`, `hinh_anh`, `che_do_phuc_vu`, `cho_them_topping`, `trang_thai`) VALUES
(1, 1, 'Cà phê đen', NULL, NULL, 'ca_hai', 0, 'dang_ban'),
(2, 1, 'Cà phê nâu', NULL, NULL, 'ca_hai', 0, 'dang_ban');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_dung`
--

CREATE TABLE `nguoi_dung` (
  `ma_nguoi_dung` int(11) NOT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `ten_dang_nhap` varchar(50) NOT NULL,
  `mat_khau` varchar(255) NOT NULL,
  `chuc_vu` enum('chu_cua_hang','nhan_vien_order','nhan_vien_pha_che') NOT NULL,
  `trang_thai` enum('hoat_dong','ngung_hoat_dong') NOT NULL DEFAULT 'hoat_dong'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_dung`
--

INSERT INTO `nguoi_dung` (`ma_nguoi_dung`, `ho_ten`, `ten_dang_nhap`, `mat_khau`, `chuc_vu`, `trang_thai`) VALUES
(1, 'Nguyễn Gia Linh', 'admin', '$2y$12$3A1iilOi1U8xWMgEooqP7uStQohiEwFGdbicVURNfRtXNVocMy752', 'chu_cua_hang', 'hoat_dong'),
(2, 'Nguyễn Gia Nhi', 'order01', '$2y$12$3A1iilOi1U8xWMgEooqP7uStQohiEwFGdbicVURNfRtXNVocMy752', 'nhan_vien_order', 'hoat_dong'),
(3, 'Nguyễn Giang', 'phache01', '$2y$12$3A1iilOi1U8xWMgEooqP7uStQohiEwFGdbicVURNfRtXNVocMy752', 'nhan_vien_pha_che', 'hoat_dong');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguyen_lieu`
--

CREATE TABLE `nguyen_lieu` (
  `ma_nguyen_lieu` int(11) NOT NULL,
  `ten_nguyen_lieu` varchar(100) NOT NULL,
  `don_vi_tinh` varchar(50) NOT NULL,
  `ton_kho` decimal(10,2) NOT NULL DEFAULT 0.00 CHECK (`ton_kho` >= 0),
  `so_luong_toi_thieu` decimal(10,2) NOT NULL CHECK (`so_luong_toi_thieu` >= 0),
  `ma_nha_cung_cap` int(11) NOT NULL,
  `ti_le_su_dung` decimal(5,2) NOT NULL DEFAULT 1.00 CHECK (`ti_le_su_dung` > 0 and `ti_le_su_dung` <= 1),
  `duoc_tuy_chinh` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguyen_lieu`
--

INSERT INTO `nguyen_lieu` (`ma_nguyen_lieu`, `ten_nguyen_lieu`, `don_vi_tinh`, `ton_kho`, `so_luong_toi_thieu`, `ma_nha_cung_cap`, `ti_le_su_dung`, `duoc_tuy_chinh`) VALUES
(1, 'Cafe hạt', 'g', 5000.00, 5000.00, 1, 1.00, 0),
(2, 'Sữa tươi', 'ml', 10000.00, 5000.00, 2, 1.00, 1),
(3, 'Sữa đặc', 'g', 5000.00, 3000.00, 2, 1.00, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nha_cung_cap`
--

CREATE TABLE `nha_cung_cap` (
  `ma_nha_cung_cap` int(11) NOT NULL,
  `ten_nha_cung_cap` varchar(100) NOT NULL,
  `so_dien_thoai` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `dia_chi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nha_cung_cap`
--

INSERT INTO `nha_cung_cap` (`ma_nha_cung_cap`, `ten_nha_cung_cap`, `so_dien_thoai`, `email`, `dia_chi`) VALUES
(1, 'Công ty nguyên liệu An Coffee', '0901234567', 'ancoffee@gmail.com', 'Hà Nội'),
(2, 'Fresh Milk Supplier', '0919896689', 'gianhinguyen23@gmail.com', 'TP Hồ Chí Minh'),
(3, 'Matcha Nhật Bản', '0988888888', 'matcha@gmail.com', 'Đà Nẵng'),
(4, 'Vựa Trái Cây Fresh Farm', '0905123456', '25a4043282@hvnh.edu.vn', 'Hà Nội');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sepay_refunds`
--

CREATE TABLE `sepay_refunds` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_hoa_don` int(11) NOT NULL,
  `sepay_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'requested',
  `reason` varchar(255) DEFAULT NULL,
  `response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sepay_transactions`
--

CREATE TABLE `sepay_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_hoa_don` int(11) DEFAULT NULL,
  `sepay_id` varchar(255) NOT NULL,
  `gateway` varchar(100) DEFAULT NULL,
  `transaction_date` timestamp NULL DEFAULT NULL,
  `account_number` varchar(100) DEFAULT NULL,
  `sub_account` varchar(250) DEFAULT NULL,
  `transfer_type` varchar(10) DEFAULT NULL,
  `amount_in` decimal(20,2) NOT NULL DEFAULT 0.00,
  `amount_out` decimal(20,2) NOT NULL DEFAULT 0.00,
  `accumulated` decimal(20,2) NOT NULL DEFAULT 0.00,
  `code` varchar(250) DEFAULT NULL,
  `transaction_content` text DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Chỉ mục cho bảng `chi_tiet_don_nhap`
--
ALTER TABLE `chi_tiet_don_nhap`
  ADD PRIMARY KEY (`ma_chi_tiet_nhap`),
  ADD KEY `fk_ctdn_don_nhap` (`ma_don_nhap`),
  ADD KEY `fk_ctdn_nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Chỉ mục cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD PRIMARY KEY (`ma_chi_tiet`),
  ADD KEY `fk_cthd_hoa_don` (`ma_hoa_don`),
  ADD KEY `fk_cthd_mon` (`ma_mon`);

--
-- Chỉ mục cho bảng `chi_tiet_topping`
--
ALTER TABLE `chi_tiet_topping`
  ADD PRIMARY KEY (`ma_chi_tiet`,`ma_mon`),
  ADD KEY `fk_ctt_mon` (`ma_mon`);

--
-- Chỉ mục cho bảng `chi_tiet_tuy_chinh`
--
ALTER TABLE `chi_tiet_tuy_chinh`
  ADD PRIMARY KEY (`ma_ct`,`ma_nguyen_lieu`),
  ADD KEY `fk_cttc_nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Chỉ mục cho bảng `cong_thuc`
--
ALTER TABLE `cong_thuc`
  ADD PRIMARY KEY (`ma_mon`,`ma_nguyen_lieu`),
  ADD KEY `fk_cong_thuc_nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Chỉ mục cho bảng `don_nhap`
--
ALTER TABLE `don_nhap`
  ADD PRIMARY KEY (`ma_don_nhap`),
  ADD KEY `fk_don_nhap_ncc` (`ma_nha_cung_cap`),
  ADD KEY `fk_don_nhap_nguoi_dung` (`ma_nguoi_dung`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `gia_mon`
--
ALTER TABLE `gia_mon`
  ADD PRIMARY KEY (`ma_gia_mon`),
  ADD KEY `fk_gia_mon_mon` (`ma_mon`);

--
-- Chỉ mục cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD PRIMARY KEY (`ma_hoa_don`),
  ADD UNIQUE KEY `hoa_don_ma_thanh_toan_unique` (`ma_thanh_toan`),
  ADD KEY `fk_hoa_don_nguoi_dung` (`ma_nguoi_dung`);

--
-- Chỉ mục cho bảng `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Chỉ mục cho bảng `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `loai_mon`
--
ALTER TABLE `loai_mon`
  ADD PRIMARY KEY (`ma_loai_mon`),
  ADD UNIQUE KEY `ten_loai_mon` (`ten_loai_mon`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `mon`
--
ALTER TABLE `mon`
  ADD PRIMARY KEY (`ma_mon`),
  ADD KEY `fk_mon_loai_mon` (`ma_loai_mon`);

--
-- Chỉ mục cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  ADD PRIMARY KEY (`ma_nguoi_dung`),
  ADD UNIQUE KEY `ten_dang_nhap` (`ten_dang_nhap`);

--
-- Chỉ mục cho bảng `nguyen_lieu`
--
ALTER TABLE `nguyen_lieu`
  ADD PRIMARY KEY (`ma_nguyen_lieu`),
  ADD KEY `fk_nguyen_lieu_ncc` (`ma_nha_cung_cap`);

--
-- Chỉ mục cho bảng `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  ADD PRIMARY KEY (`ma_nha_cung_cap`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `sepay_refunds`
--
ALTER TABLE `sepay_refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sepay_refunds_ma_hoa_don_foreign` (`ma_hoa_don`),
  ADD KEY `sepay_refunds_sepay_transaction_id_foreign` (`sepay_transaction_id`);

--
-- Chỉ mục cho bảng `sepay_transactions`
--
ALTER TABLE `sepay_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sepay_transactions_sepay_id_unique` (`sepay_id`),
  ADD KEY `sepay_transactions_ma_hoa_don_foreign` (`ma_hoa_don`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `chi_tiet_don_nhap`
--
ALTER TABLE `chi_tiet_don_nhap`
  MODIFY `ma_chi_tiet_nhap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  MODIFY `ma_chi_tiet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `don_nhap`
--
ALTER TABLE `don_nhap`
  MODIFY `ma_don_nhap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `gia_mon`
--
ALTER TABLE `gia_mon`
  MODIFY `ma_gia_mon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  MODIFY `ma_hoa_don` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loai_mon`
--
ALTER TABLE `loai_mon`
  MODIFY `ma_loai_mon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `mon`
--
ALTER TABLE `mon`
  MODIFY `ma_mon` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `nguoi_dung`
--
ALTER TABLE `nguoi_dung`
  MODIFY `ma_nguoi_dung` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT cho bảng `nguyen_lieu`
--
ALTER TABLE `nguyen_lieu`
  MODIFY `ma_nguyen_lieu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `nha_cung_cap`
--
ALTER TABLE `nha_cung_cap`
  MODIFY `ma_nha_cung_cap` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `sepay_refunds`
--
ALTER TABLE `sepay_refunds`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `sepay_transactions`
--
ALTER TABLE `sepay_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chi_tiet_don_nhap`
--
ALTER TABLE `chi_tiet_don_nhap`
  ADD CONSTRAINT `fk_ctdn_don_nhap` FOREIGN KEY (`ma_don_nhap`) REFERENCES `don_nhap` (`ma_don_nhap`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ctdn_nguyen_lieu` FOREIGN KEY (`ma_nguyen_lieu`) REFERENCES `nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Các ràng buộc cho bảng `chi_tiet_hoa_don`
--
ALTER TABLE `chi_tiet_hoa_don`
  ADD CONSTRAINT `fk_cthd_hoa_don` FOREIGN KEY (`ma_hoa_don`) REFERENCES `hoa_don` (`ma_hoa_don`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cthd_mon` FOREIGN KEY (`ma_mon`) REFERENCES `mon` (`ma_mon`);

--
-- Các ràng buộc cho bảng `chi_tiet_topping`
--
ALTER TABLE `chi_tiet_topping`
  ADD CONSTRAINT `fk_ctt_cthd` FOREIGN KEY (`ma_chi_tiet`) REFERENCES `chi_tiet_hoa_don` (`ma_chi_tiet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ctt_mon` FOREIGN KEY (`ma_mon`) REFERENCES `mon` (`ma_mon`);

--
-- Các ràng buộc cho bảng `chi_tiet_tuy_chinh`
--
ALTER TABLE `chi_tiet_tuy_chinh`
  ADD CONSTRAINT `fk_cttc_cthd` FOREIGN KEY (`ma_ct`) REFERENCES `chi_tiet_hoa_don` (`ma_chi_tiet`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cttc_nguyen_lieu` FOREIGN KEY (`ma_nguyen_lieu`) REFERENCES `nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Các ràng buộc cho bảng `cong_thuc`
--
ALTER TABLE `cong_thuc`
  ADD CONSTRAINT `fk_cong_thuc_mon` FOREIGN KEY (`ma_mon`) REFERENCES `mon` (`ma_mon`),
  ADD CONSTRAINT `fk_cong_thuc_nguyen_lieu` FOREIGN KEY (`ma_nguyen_lieu`) REFERENCES `nguyen_lieu` (`ma_nguyen_lieu`);

--
-- Các ràng buộc cho bảng `don_nhap`
--
ALTER TABLE `don_nhap`
  ADD CONSTRAINT `fk_don_nhap_ncc` FOREIGN KEY (`ma_nha_cung_cap`) REFERENCES `nha_cung_cap` (`ma_nha_cung_cap`),
  ADD CONSTRAINT `fk_don_nhap_nguoi_dung` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `gia_mon`
--
ALTER TABLE `gia_mon`
  ADD CONSTRAINT `fk_gia_mon_mon` FOREIGN KEY (`ma_mon`) REFERENCES `mon` (`ma_mon`);

--
-- Các ràng buộc cho bảng `hoa_don`
--
ALTER TABLE `hoa_don`
  ADD CONSTRAINT `fk_hoa_don_nguoi_dung` FOREIGN KEY (`ma_nguoi_dung`) REFERENCES `nguoi_dung` (`ma_nguoi_dung`);

--
-- Các ràng buộc cho bảng `mon`
--
ALTER TABLE `mon`
  ADD CONSTRAINT `fk_mon_loai_mon` FOREIGN KEY (`ma_loai_mon`) REFERENCES `loai_mon` (`ma_loai_mon`);

--
-- Các ràng buộc cho bảng `nguyen_lieu`
--
ALTER TABLE `nguyen_lieu`
  ADD CONSTRAINT `fk_nguyen_lieu_ncc` FOREIGN KEY (`ma_nha_cung_cap`) REFERENCES `nha_cung_cap` (`ma_nha_cung_cap`);

--
-- Các ràng buộc cho bảng `sepay_refunds`
--
ALTER TABLE `sepay_refunds`
  ADD CONSTRAINT `sepay_refunds_ma_hoa_don_foreign` FOREIGN KEY (`ma_hoa_don`) REFERENCES `hoa_don` (`ma_hoa_don`) ON DELETE CASCADE,
  ADD CONSTRAINT `sepay_refunds_sepay_transaction_id_foreign` FOREIGN KEY (`sepay_transaction_id`) REFERENCES `sepay_transactions` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `sepay_transactions`
--
ALTER TABLE `sepay_transactions`
  ADD CONSTRAINT `sepay_transactions_ma_hoa_don_foreign` FOREIGN KEY (`ma_hoa_don`) REFERENCES `hoa_don` (`ma_hoa_don`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
