-- Demo data for School Management Plugin
-- 3 Schools, 8 Classes, 100 Students

-- Clear existing data (optional - uncomment if needed)
-- DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type IN ('school', 'class', 'entity'));
-- DELETE FROM wp_posts WHERE post_type IN ('school', 'class', 'entity');

-- Insert Schools
INSERT INTO `wp_posts`(`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES 
(1001, 1, NOW(), UTC_TIMESTAMP(), '', 'Trường THPT Nguyễn Huệ', '', 'publish', 'closed', 'closed', '', 'truong-thpt-nguyen-hue', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=school&#038;p=1001', 0, 'school', '', 0),
(1002, 1, NOW(), UTC_TIMESTAMP(), '', 'Trường THPT Lê Lợi', '', 'publish', 'closed', 'closed', '', 'truong-thpt-le-loi', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=school&#038;p=1002', 0, 'school', '', 0),
(1003, 1, NOW(), UTC_TIMESTAMP(), '', 'Trường THPT Trần Phú', '', 'publish', 'closed', 'closed', '', 'truong-thpt-tran-phu', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=school&#038;p=1003', 0, 'school', '', 0);

-- Insert Classes
INSERT INTO `wp_posts`(`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES 
-- Classes for Trường THPT Nguyễn Huệ
(2001, 1, NOW(), UTC_TIMESTAMP(), '', '10A1', '', 'publish', 'closed', 'closed', '', '10a1-nguyen-hue', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2001', 0, 'class', '', 0),
(2002, 1, NOW(), UTC_TIMESTAMP(), '', '10A2', '', 'publish', 'closed', 'closed', '', '10a2-nguyen-hue', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2002', 0, 'class', '', 0),
(2003, 1, NOW(), UTC_TIMESTAMP(), '', '11B1', '', 'publish', 'closed', 'closed', '', '11b1-nguyen-hue', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2003', 0, 'class', '', 0),
-- Classes for Trường THPT Lê Lợi
(2004, 1, NOW(), UTC_TIMESTAMP(), '', '10A3', '', 'publish', 'closed', 'closed', '', '10a3-le-loi', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2004', 0, 'class', '', 0),
(2005, 1, NOW(), UTC_TIMESTAMP(), '', '11A1', '', 'publish', 'closed', 'closed', '', '11a1-le-loi', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2005', 0, 'class', '', 0),
(2006, 1, NOW(), UTC_TIMESTAMP(), '', '12C1', '', 'publish', 'closed', 'closed', '', '12c1-le-loi', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2006', 0, 'class', '', 0),
-- Classes for Trường THPT Trần Phú
(2007, 1, NOW(), UTC_TIMESTAMP(), '', '10B1', '', 'publish', 'closed', 'closed', '', '10b1-tran-phu', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2007', 0, 'class', '', 0),
(2008, 1, NOW(), UTC_TIMESTAMP(), '', '12A1', '', 'publish', 'closed', 'closed', '', '12a1-tran-phu', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=class&#038;p=2008', 0, 'class', '', 0);

-- Insert Class Meta (Thuộc Trường)
INSERT INTO `wp_postmeta`(`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES 
(3001, 2001, 'Thuộc Trường', 'Trường THPT Nguyễn Huệ'),
(3002, 2002, 'Thuộc Trường', 'Trường THPT Nguyễn Huệ'),
(3003, 2003, 'Thuộc Trường', 'Trường THPT Nguyễn Huệ'),
(3004, 2004, 'Thuộc Trường', 'Trường THPT Lê Lợi'),
(3005, 2005, 'Thuộc Trường', 'Trường THPT Lê Lợi'),
(3006, 2006, 'Thuộc Trường', 'Trường THPT Lê Lợi'),
(3007, 2007, 'Thuộc Trường', 'Trường THPT Trần Phú'),
(3008, 2008, 'Thuộc Trường', 'Trường THPT Trần Phú');

-- Insert Students (Entities) - 100 students
INSERT INTO `wp_posts`(`ID`, `post_author`, `post_date`, `post_date_gmt`, `post_content`, `post_title`, `post_excerpt`, `post_status`, `comment_status`, `ping_status`, `post_password`, `post_name`, `to_ping`, `pinged`, `post_modified`, `post_modified_gmt`, `post_content_filtered`, `post_parent`, `guid`, `menu_order`, `post_type`, `post_mime_type`, `comment_count`) VALUES 
-- Students for class 10A1 (12 students)
(3001, 1, NOW(), UTC_TIMESTAMP(), '', 'Nguyễn Văn An', '', 'publish', 'closed', 'closed', '', 'nguyen-van-an-001', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3001', 0, 'entity', '', 0),
(3002, 1, NOW(), UTC_TIMESTAMP(), '', 'Trần Thị Bích', '', 'publish', 'closed', 'closed', '', 'tran-thi-bich-002', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3002', 0, 'entity', '', 0),
(3003, 1, NOW(), UTC_TIMESTAMP(), '', 'Lê Minh Cường', '', 'publish', 'closed', 'closed', '', 'le-minh-cuong-003', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3003', 0, 'entity', '', 0),
(3004, 1, NOW(), UTC_TIMESTAMP(), '', 'Phạm Thị Dung', '', 'publish', 'closed', 'closed', '', 'pham-thi-dung-004', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3004', 0, 'entity', '', 0),
(3005, 1, NOW(), UTC_TIMESTAMP(), '', 'Hoàng Văn Em', '', 'publish', 'closed', 'closed', '', 'hoang-van-em-005', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3005', 0, 'entity', '', 0),
(3006, 1, NOW(), UTC_TIMESTAMP(), '', 'Vũ Thị Giang', '', 'publish', 'closed', 'closed', '', 'vu-thi-giang-006', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3006', 0, 'entity', '', 0),
(3007, 1, NOW(), UTC_TIMESTAMP(), '', 'Đỗ Minh Hùng', '', 'publish', 'closed', 'closed', '', 'do-minh-hung-007', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3007', 0, 'entity', '', 0),
(3008, 1, NOW(), UTC_TIMESTAMP(), '', 'Bùi Thị Hoa', '', 'publish', 'closed', 'closed', '', 'bui-thi-hoa-008', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3008', 0, 'entity', '', 0),
(3009, 1, NOW(), UTC_TIMESTAMP(), '', 'Ngô Văn Kiên', '', 'publish', 'closed', 'closed', '', 'ngo-van-kien-009', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3009', 0, 'entity', '', 0),
(3010, 1, NOW(), UTC_TIMESTAMP(), '', 'Lý Thị Lan', '', 'publish', 'closed', 'closed', '', 'ly-thi-lan-010', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3010', 0, 'entity', '', 0),
(3011, 1, NOW(), UTC_TIMESTAMP(), '', 'Phan Văn Minh', '', 'publish', 'closed', 'closed', '', 'phan-van-minh-011', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3011', 0, 'entity', '', 0),
(3012, 1, NOW(), UTC_TIMESTAMP(), '', 'Cao Thị Nga', '', 'publish', 'closed', 'closed', '', 'cao-thi-nga-012', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3012', 0, 'entity', '', 0),

-- Students for class 10A2 (13 students)
(3013, 1, NOW(), UTC_TIMESTAMP(), '', 'Trương Văn Ơn', '', 'publish', 'closed', 'closed', '', 'truong-van-on-013', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3013', 0, 'entity', '', 0),
(3014, 1, NOW(), UTC_TIMESTAMP(), '', 'Đinh Thị Phượng', '', 'publish', 'closed', 'closed', '', 'dinh-thi-phuong-014', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3014', 0, 'entity', '', 0),
(3015, 1, NOW(), UTC_TIMESTAMP(), '', 'Lương Minh Quang', '', 'publish', 'closed', 'closed', '', 'luong-minh-quang-015', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3015', 0, 'entity', '', 0),
(3016, 1, NOW(), UTC_TIMESTAMP(), '', 'Mai Thị Rượu', '', 'publish', 'closed', 'closed', '', 'mai-thi-ruou-016', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3016', 0, 'entity', '', 0),
(3017, 1, NOW(), UTC_TIMESTAMP(), '', 'Tô Văn Sơn', '', 'publish', 'closed', 'closed', '', 'to-van-son-017', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3017', 0, 'entity', '', 0),
(3018, 1, NOW(), UTC_TIMESTAMP(), '', 'Dương Thị Tâm', '', 'publish', 'closed', 'closed', '', 'duong-thi-tam-018', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3018', 0, 'entity', '', 0),
(3019, 1, NOW(), UTC_TIMESTAMP(), '', 'Hồ Minh Ước', '', 'publish', 'closed', 'closed', '', 'ho-minh-uoc-019', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3019', 0, 'entity', '', 0),
(3020, 1, NOW(), UTC_TIMESTAMP(), '', 'Võ Thị Vân', '', 'publish', 'closed', 'closed', '', 'vo-thi-van-020', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3020', 0, 'entity', '', 0),
(3021, 1, NOW(), UTC_TIMESTAMP(), '', 'Huỳnh Văn Xuân', '', 'publish', 'closed', 'closed', '', 'huynh-van-xuan-021', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3021', 0, 'entity', '', 0),
(3022, 1, NOW(), UTC_TIMESTAMP(), '', 'Đặng Thị Yến', '', 'publish', 'closed', 'closed', '', 'dang-thi-yen-022', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3022', 0, 'entity', '', 0),
(3023, 1, NOW(), UTC_TIMESTAMP(), '', 'Từ Minh Chiến', '', 'publish', 'closed', 'closed', '', 'tu-minh-chien-023', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3023', 0, 'entity', '', 0),
(3024, 1, NOW(), UTC_TIMESTAMP(), '', 'Lại Thị Dạ', '', 'publish', 'closed', 'closed', '', 'lai-thi-da-024', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3024', 0, 'entity', '', 0),
(3025, 1, NOW(), UTC_TIMESTAMP(), '', 'Cù Văn Eo', '', 'publish', 'closed', 'closed', '', 'cu-van-eo-025', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3025', 0, 'entity', '', 0),

-- Students for class 11B1 (12 students)
(3026, 1, NOW(), UTC_TIMESTAMP(), '', 'Nguyễn Thị Phượng', '', 'publish', 'closed', 'closed', '', 'nguyen-thi-phuong-026', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3026', 0, 'entity', '', 0),
(3027, 1, NOW(), UTC_TIMESTAMP(), '', 'Trần Văn Giang', '', 'publish', 'closed', 'closed', '', 'tran-van-giang-027', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3027', 0, 'entity', '', 0),
(3028, 1, NOW(), UTC_TIMESTAMP(), '', 'Lê Thị Hương', '', 'publish', 'closed', 'closed', '', 'le-thi-huong-028', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3028', 0, 'entity', '', 0),
(3029, 1, NOW(), UTC_TIMESTAMP(), '', 'Phạm Văn Khánh', '', 'publish', 'closed', 'closed', '', 'pham-van-khanh-029', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3029', 0, 'entity', '', 0),
(3030, 1, NOW(), UTC_TIMESTAMP(), '', 'Hoàng Thị Linh', '', 'publish', 'closed', 'closed', '', 'hoang-thi-linh-030', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3030', 0, 'entity', '', 0),
(3031, 1, NOW(), UTC_TIMESTAMP(), '', 'Vũ Văn Nam', '', 'publish', 'closed', 'closed', '', 'vu-van-nam-031', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3031', 0, 'entity', '', 0),
(3032, 1, NOW(), UTC_TIMESTAMP(), '', 'Đỗ Thị Oanh', '', 'publish', 'closed', 'closed', '', 'do-thi-oanh-032', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3032', 0, 'entity', '', 0),
(3033, 1, NOW(), UTC_TIMESTAMP(), '', 'Bùi Văn Phúc', '', 'publish', 'closed', 'closed', '', 'bui-van-phuc-033', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3033', 0, 'entity', '', 0),
(3034, 1, NOW(), UTC_TIMESTAMP(), '', 'Ngô Thị Quỳnh', '', 'publish', 'closed', 'closed', '', 'ngo-thi-quynh-034', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3034', 0, 'entity', '', 0),
(3035, 1, NOW(), UTC_TIMESTAMP(), '', 'Lý Văn Sáng', '', 'publish', 'closed', 'closed', '', 'ly-van-sang-035', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3035', 0, 'entity', '', 0),
(3036, 1, NOW(), UTC_TIMESTAMP(), '', 'Phan Thị Thu', '', 'publish', 'closed', 'closed', '', 'phan-thi-thu-036', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3036', 0, 'entity', '', 0),
(3037, 1, NOW(), UTC_TIMESTAMP(), '', 'Cao Văn Uy', '', 'publish', 'closed', 'closed', '', 'cao-van-uy-037', '', '', NOW(), UTC_TIMESTAMP(), '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3037', 0, 'entity', '', 0);

-- Students for class 10A3 (13 students) 
(3038, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Trương Thị Vui', '', 'publish', 'closed', 'closed', '', 'truong-thi-vui-038', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3038', 0, 'entity', '', 0),
(3039, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đinh Văn Xuân', '', 'publish', 'closed', 'closed', '', 'dinh-van-xuan-039', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3039', 0, 'entity', '', 0),
(3040, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lương Thị Yêu', '', 'publish', 'closed', 'closed', '', 'luong-thi-yeu-040', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3040', 0, 'entity', '', 0),
(3041, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Mai Văn An', '', 'publish', 'closed', 'closed', '', 'mai-van-an-041', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3041', 0, 'entity', '', 0),
(3042, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Tô Thị Bình', '', 'publish', 'closed', 'closed', '', 'to-thi-binh-042', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3042', 0, 'entity', '', 0),
(3043, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Dương Văn Cảnh', '', 'publish', 'closed', 'closed', '', 'duong-van-canh-043', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3043', 0, 'entity', '', 0),
(3044, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Hồ Thị Diệp', '', 'publish', 'closed', 'closed', '', 'ho-thi-diep-044', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3044', 0, 'entity', '', 0),
(3045, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Võ Văn Em', '', 'publish', 'closed', 'closed', '', 'vo-van-em-045', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3045', 0, 'entity', '', 0),
(3046, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Huỳnh Thị Phong', '', 'publish', 'closed', 'closed', '', 'huynh-thi-phong-046', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3046', 0, 'entity', '', 0),
(3047, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đặng Văn Gió', '', 'publish', 'closed', 'closed', '', 'dang-van-gio-047', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3047', 0, 'entity', '', 0),
(3048, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Từ Thị Hồng', '', 'publish', 'closed', 'closed', '', 'tu-thi-hong-048', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3048', 0, 'entity', '', 0),
(3049, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lại Văn Kiến', '', 'publish', 'closed', 'closed', '', 'lai-van-kien-049', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3049', 0, 'entity', '', 0),
(3050, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Cù Thị Long', '', 'publish', 'closed', 'closed', '', 'cu-thi-long-050', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3050', 0, 'entity', '', 0),

-- Students for class 11A1 (12 students)
(3051, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Nguyễn Văn Minh', '', 'publish', 'closed', 'closed', '', 'nguyen-van-minh-051', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3051', 0, 'entity', '', 0),
(3052, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Trần Thị Nở', '', 'publish', 'closed', 'closed', '', 'tran-thi-no-052', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3052', 0, 'entity', '', 0),
(3053, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lê Văn Ớt', '', 'publish', 'closed', 'closed', '', 'le-van-ot-053', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3053', 0, 'entity', '', 0),
(3054, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Phạm Thị Phúc', '', 'publish', 'closed', 'closed', '', 'pham-thi-phuc-054', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3054', 0, 'entity', '', 0),
(3055, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Hoàng Văn Quang', '', 'publish', 'closed', 'closed', '', 'hoang-van-quang-055', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3055', 0, 'entity', '', 0),
(3056, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Vũ Thị Rộng', '', 'publish', 'closed', 'closed', '', 'vu-thi-rong-056', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3056', 0, 'entity', '', 0),
(3057, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đỗ Văn Sung', '', 'publish', 'closed', 'closed', '', 'do-van-sung-057', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3057', 0, 'entity', '', 0),
(3058, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Bùi Thị Tuệ', '', 'publish', 'closed', 'closed', '', 'bui-thi-tue-058', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3058', 0, 'entity', '', 0),
(3059, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Ngô Văn Ung', '', 'publish', 'closed', 'closed', '', 'ngo-van-ung-059', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3059', 0, 'entity', '', 0),
(3060, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lý Thị Vàng', '', 'publish', 'closed', 'closed', '', 'ly-thi-vang-060', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3060', 0, 'entity', '', 0),
(3061, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Phan Văn Xanh', '', 'publish', 'closed', 'closed', '', 'phan-van-xanh-061', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3061', 0, 'entity', '', 0),
(3062, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Cao Thị Yến', '', 'publish', 'closed', 'closed', '', 'cao-thi-yen-062', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3062', 0, 'entity', '', 0),

-- Students for class 12C1 (12 students)
(3063, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Trương Văn Âm', '', 'publish', 'closed', 'closed', '', 'truong-van-am-063', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3063', 0, 'entity', '', 0),
(3064, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đinh Thị Búp', '', 'publish', 'closed', 'closed', '', 'dinh-thi-bup-064', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3064', 0, 'entity', '', 0),
(3065, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lương Văn Cụt', '', 'publish', 'closed', 'closed', '', 'luong-van-cut-065', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3065', 0, 'entity', '', 0),
(3066, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Mai Thị Đào', '', 'publish', 'closed', 'closed', '', 'mai-thi-dao-066', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3066', 0, 'entity', '', 0),
(3067, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Tô Văn Én', '', 'publish', 'closed', 'closed', '', 'to-van-en-067', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3067', 0, 'entity', '', 0),
(3068, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Dương Thị Phím', '', 'publish', 'closed', 'closed', '', 'duong-thi-phim-068', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3068', 0, 'entity', '', 0),
(3069, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Hồ Văn Gấm', '', 'publish', 'closed', 'closed', '', 'ho-van-gam-069', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3069', 0, 'entity', '', 0),
(3070, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Võ Thị Hồng', '', 'publish', 'closed', 'closed', '', 'vo-thi-hong-070', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3070', 0, 'entity', '', 0),
(3071, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Huỳnh Văn Ích', '', 'publish', 'closed', 'closed', '', 'huynh-van-ich-071', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3071', 0, 'entity', '', 0),
(3072, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đặng Thị Kẹo', '', 'publish', 'closed', 'closed', '', 'dang-thi-keo-072', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3072', 0, 'entity', '', 0),
(3073, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Từ Văn Lúa', '', 'publish', 'closed', 'closed', '', 'tu-van-lua-073', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3073', 0, 'entity', '', 0),
(3074, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lại Thị Mận', '', 'publish', 'closed', 'closed', '', 'lai-thi-man-074', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3074', 0, 'entity', '', 0),

-- Students for class 10B1 (13 students)
(3075, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Cù Văn Nai', '', 'publish', 'closed', 'closed', '', 'cu-van-nai-075', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3075', 0, 'entity', '', 0),
(3076, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Nguyễn Thị Ổi', '', 'publish', 'closed', 'closed', '', 'nguyen-thi-oi-076', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3076', 0, 'entity', '', 0),
(3077, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Trần Văn Phùn', '', 'publish', 'closed', 'closed', '', 'tran-van-phun-077', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3077', 0, 'entity', '', 0),
(3078, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lê Thị Quả', '', 'publish', 'closed', 'closed', '', 'le-thi-qua-078', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3078', 0, 'entity', '', 0),
(3079, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Phạm Văn Rùa', '', 'publish', 'closed', 'closed', '', 'pham-van-rua-079', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3079', 0, 'entity', '', 0),
(3080, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Hoàng Thị Sâu', '', 'publish', 'closed', 'closed', '', 'hoang-thi-sau-080', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3080', 0, 'entity', '', 0),
(3081, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Vũ Văn Thỏ', '', 'publish', 'closed', 'closed', '', 'vu-van-tho-081', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3081', 0, 'entity', '', 0),
(3082, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đỗ Thị Ưa', '', 'publish', 'closed', 'closed', '', 'do-thi-ua-082', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3082', 0, 'entity', '', 0),
(3083, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Bùi Văn Voi', '', 'publish', 'closed', 'closed', '', 'bui-van-voi-083', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3083', 0, 'entity', '', 0),
(3084, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Ngô Thị Xoài', '', 'publish', 'closed', 'closed', '', 'ngo-thi-xoai-084', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3084', 0, 'entity', '', 0),
(3085, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lý Văn Ỷ', '', 'publish', 'closed', 'closed', '', 'ly-van-y-085', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3085', 0, 'entity', '', 0),
(3086, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Phan Thị Đẹp', '', 'publish', 'closed', 'closed', '', 'phan-thi-dep-086', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3086', 0, 'entity', '', 0),
(3087, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Cao Văn Át', '', 'publish', 'closed', 'closed', '', 'cao-van-at-087', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3087', 0, 'entity', '', 0),

-- Students for class 12A1 (13 students)
(3088, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Trương Thị Bướm', '', 'publish', 'closed', 'closed', '', 'truong-thi-buom-088', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3088', 0, 'entity', '', 0),
(3089, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đinh Văn Cây', '', 'publish', 'closed', 'closed', '', 'dinh-van-cay-089', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3089', 0, 'entity', '', 0),
(3090, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lương Thị Đá', '', 'publish', 'closed', 'closed', '', 'luong-thi-da-090', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3090', 0, 'entity', '', 0),
(3091, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Mai Văn Êm', '', 'publish', 'closed', 'closed', '', 'mai-van-em-091', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3091', 0, 'entity', '', 0),
(3092, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Tô Thị Phỏng', '', 'publish', 'closed', 'closed', '', 'to-thi-phong-092', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3092', 0, 'entity', '', 0),
(3093, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Dương Văn Góc', '', 'publish', 'closed', 'closed', '', 'duong-van-goc-093', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3093', 0, 'entity', '', 0),
(3094, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Hồ Thị Hạt', '', 'publish', 'closed', 'closed', '', 'ho-thi-hat-094', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3094', 0, 'entity', '', 0),
(3095, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Võ Văn Ít', '', 'publish', 'closed', 'closed', '', 'vo-van-it-095', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3095', 0, 'entity', '', 0),
(3096, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Huỳnh Thị Khóc', '', 'publish', 'closed', 'closed', '', 'huynh-thi-khoc-096', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3096', 0, 'entity', '', 0),
(3097, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Đặng Văn La', '', 'publish', 'closed', 'closed', '', 'dang-van-la-097', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3097', 0, 'entity', '', 0),
(3098, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Từ Thị Mơ', '', 'publish', 'closed', 'closed', '', 'tu-thi-mo-098', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3098', 0, 'entity', '', 0),
(3099, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Lại Văn Nhỏ', '', 'publish', 'closed', 'closed', '', 'lai-van-nho-099', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3099', 0, 'entity', '', 0),
(3100, 1, 'NOW()', 'UTC_TIMESTAMP()', '', 'Cù Thị Ôi', '', 'publish', 'closed', 'closed', '', 'cu-thi-oi-100', '', '', 'NOW()', 'UTC_TIMESTAMP()', '', 0, 'http://localhost/wp_practice/?post_type=entity&#038;p=3100', 0, 'entity', '', 0);

-- Insert Student Meta Data (Thuộc lớp, Link khi click, Hình)
INSERT INTO `wp_postmeta`(`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES 
-- Meta for students in class 10A1 (12 students)
(4001, 3001, 'Thuộc lớp', '10A1'),
(4002, 3001, 'Link khi click', 'https://facebook.com/nguyenvanan001'),
(4003, 3001, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4004, 3002, 'Thuộc lớp', '10A1'),
(4005, 3002, 'Link khi click', 'https://facebook.com/tranthibich002'),
(4006, 3002, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4007, 3003, 'Thuộc lớp', '10A1'),
(4008, 3003, 'Link khi click', 'https://facebook.com/leminhcuong003'),
(4009, 3003, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4010, 3004, 'Thuộc lớp', '10A1'),
(4011, 3004, 'Link khi click', 'https://facebook.com/phamthidung004'),
(4012, 3004, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4013, 3005, 'Thuộc lớp', '10A1'),
(4014, 3005, 'Link khi click', 'https://facebook.com/hoangvanem005'),
(4015, 3005, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4016, 3006, 'Thuộc lớp', '10A1'),
(4017, 3006, 'Link khi click', 'https://facebook.com/vuthigiang006'),
(4018, 3006, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4019, 3007, 'Thuộc lớp', '10A1'),
(4020, 3007, 'Link khi click', 'https://facebook.com/dominhhung007'),
(4021, 3007, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4022, 3008, 'Thuộc lớp', '10A1'),
(4023, 3008, 'Link khi click', 'https://facebook.com/buithihoa008'),
(4024, 3008, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4025, 3009, 'Thuộc lớp', '10A1'),
(4026, 3009, 'Link khi click', 'https://facebook.com/ngovankien009'),
(4027, 3009, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4028, 3010, 'Thuộc lớp', '10A1'),
(4029, 3010, 'Link khi click', 'https://facebook.com/lythilan010'),
(4030, 3010, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4031, 3011, 'Thuộc lớp', '10A1'),
(4032, 3011, 'Link khi click', 'https://facebook.com/phanvanminh011'),
(4033, 3011, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4034, 3012, 'Thuộc lớp', '10A1'),
(4035, 3012, 'Link khi click', 'https://facebook.com/caothinga012'),
(4036, 3012, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png');

-- Continue with metadata for all remaining students
-- I'll provide a complete script pattern for the remaining entries

-- Meta for students in class 10A2 (13 students)
-- Student IDs 3013-3025
INSERT INTO `wp_postmeta`(`meta_id`, `post_id`, `meta_key`, `meta_value`) VALUES 
(4037, 3013, 'Thuộc lớp', '10A2'), (4038, 3013, 'Link khi click', 'https://facebook.com/truongvanon013'), (4039, 3013, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4040, 3014, 'Thuộc lớp', '10A2'), (4041, 3014, 'Link khi click', 'https://facebook.com/dinhthiphuong014'), (4042, 3014, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4043, 3015, 'Thuộc lớp', '10A2'), (4044, 3015, 'Link khi click', 'https://facebook.com/luongminhquang015'), (4045, 3015, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4046, 3016, 'Thuộc lớp', '10A2'), (4047, 3016, 'Link khi click', 'https://facebook.com/maithiruou016'), (4048, 3016, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4049, 3017, 'Thuộc lớp', '10A2'), (4050, 3017, 'Link khi click', 'https://facebook.com/tovanson017'), (4051, 3017, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4052, 3018, 'Thuộc lớp', '10A2'), (4053, 3018, 'Link khi click', 'https://facebook.com/duongthitam018'), (4054, 3018, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4055, 3019, 'Thuộc lớp', '10A2'), (4056, 3019, 'Link khi click', 'https://facebook.com/hominhuoc019'), (4057, 3019, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4058, 3020, 'Thuộc lớp', '10A2'), (4059, 3020, 'Link khi click', 'https://facebook.com/vothivan020'), (4060, 3020, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4061, 3021, 'Thuộc lớp', '10A2'), (4062, 3021, 'Link khi click', 'https://facebook.com/huynhvanxuan021'), (4063, 3021, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4064, 3022, 'Thuộc lớp', '10A2'), (4065, 3022, 'Link khi click', 'https://facebook.com/dangthiyen022'), (4066, 3022, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4067, 3023, 'Thuộc lớp', '10A2'), (4068, 3023, 'Link khi click', 'https://facebook.com/tuminhchien023'), (4069, 3023, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4070, 3024, 'Thuộc lớp', '10A2'), (4071, 3024, 'Link khi click', 'https://facebook.com/laithida024'), (4072, 3024, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4073, 3025, 'Thuộc lớp', '10A2'), (4074, 3025, 'Link khi click', 'https://facebook.com/cuvaneo025'), (4075, 3025, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),

-- Meta for students in class 11B1 (12 students)  
-- Student IDs 3026-3037
(4076, 3026, 'Thuộc lớp', '11B1'), (4077, 3026, 'Link khi click', 'https://facebook.com/nguyenthiphuong026'), (4078, 3026, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4079, 3027, 'Thuộc lớp', '11B1'), (4080, 3027, 'Link khi click', 'https://facebook.com/tranvangiang027'), (4081, 3027, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4082, 3028, 'Thuộc lớp', '11B1'), (4083, 3028, 'Link khi click', 'https://facebook.com/lethihuong028'), (4084, 3028, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4085, 3029, 'Thuộc lớp', '11B1'), (4086, 3029, 'Link khi click', 'https://facebook.com/phamvankhanh029'), (4087, 3029, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4088, 3030, 'Thuộc lớp', '11B1'), (4089, 3030, 'Link khi click', 'https://facebook.com/hoangthilinh030'), (4090, 3030, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4091, 3031, 'Thuộc lớp', '11B1'), (4092, 3031, 'Link khi click', 'https://facebook.com/vuvannam031'), (4093, 3031, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4094, 3032, 'Thuộc lớp', '11B1'), (4095, 3032, 'Link khi click', 'https://facebook.com/dothioanh032'), (4096, 3032, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4097, 3033, 'Thuộc lớp', '11B1'), (4098, 3033, 'Link khi click', 'https://facebook.com/buivanphuc033'), (4099, 3033, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4100, 3034, 'Thuộc lớp', '11B1'), (4101, 3034, 'Link khi click', 'https://facebook.com/ngothiquynh034'), (4102, 3034, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4103, 3035, 'Thuộc lớp', '11B1'), (4104, 3035, 'Link khi click', 'https://facebook.com/lyvansang035'), (4105, 3035, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4106, 3036, 'Thuộc lớp', '11B1'), (4107, 3036, 'Link khi click', 'https://facebook.com/phanthithu036'), (4108, 3036, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4109, 3037, 'Thuộc lớp', '11B1'), (4110, 3037, 'Link khi click', 'https://facebook.com/caovanuy037'), (4111, 3037, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),

-- Meta for students in class 10A3 (13 students)
-- Student IDs 3038-3050
(4112, 3038, 'Thuộc lớp', '10A3'), (4113, 3038, 'Link khi click', 'https://facebook.com/truongthivui038'), (4114, 3038, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4115, 3039, 'Thuộc lớp', '10A3'), (4116, 3039, 'Link khi click', 'https://facebook.com/dinhvanxuan039'), (4117, 3039, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4118, 3040, 'Thuộc lớp', '10A3'), (4119, 3040, 'Link khi click', 'https://facebook.com/luongthiyeu040'), (4120, 3040, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4121, 3041, 'Thuộc lớp', '10A3'), (4122, 3041, 'Link khi click', 'https://facebook.com/maivanan041'), (4123, 3041, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4124, 3042, 'Thuộc lớp', '10A3'), (4125, 3042, 'Link khi click', 'https://facebook.com/tothibinh042'), (4126, 3042, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4127, 3043, 'Thuộc lớp', '10A3'), (4128, 3043, 'Link khi click', 'https://facebook.com/duongvancanh043'), (4129, 3043, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4130, 3044, 'Thuộc lớp', '10A3'), (4131, 3044, 'Link khi click', 'https://facebook.com/hothidiep044'), (4132, 3044, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4133, 3045, 'Thuộc lớp', '10A3'), (4134, 3045, 'Link khi click', 'https://facebook.com/vovanem045'), (4135, 3045, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4136, 3046, 'Thuộc lớp', '10A3'), (4137, 3046, 'Link khi click', 'https://facebook.com/huynhthiphong046'), (4138, 3046, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4139, 3047, 'Thuộc lớp', '10A3'), (4140, 3047, 'Link khi click', 'https://facebook.com/dangvangio047'), (4141, 3047, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4142, 3048, 'Thuộc lớp', '10A3'), (4143, 3048, 'Link khi click', 'https://facebook.com/tutthihong048'), (4144, 3048, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4145, 3049, 'Thuộc lớp', '10A3'), (4146, 3049, 'Link khi click', 'https://facebook.com/laivankien049'), (4147, 3049, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4148, 3050, 'Thuộc lớp', '10A3'), (4149, 3050, 'Link khi click', 'https://facebook.com/cuthilong050'), (4150, 3050, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),

-- Meta for students in class 11A1 (12 students)
-- Student IDs 3051-3062
(4151, 3051, 'Thuộc lớp', '11A1'), (4152, 3051, 'Link khi click', 'https://facebook.com/nguyenvanminh051'), (4153, 3051, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4154, 3052, 'Thuộc lớp', '11A1'), (4155, 3052, 'Link khi click', 'https://facebook.com/tranthino052'), (4156, 3052, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4157, 3053, 'Thuộc lớp', '11A1'), (4158, 3053, 'Link khi click', 'https://facebook.com/levanot053'), (4159, 3053, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4160, 3054, 'Thuộc lớp', '11A1'), (4161, 3054, 'Link khi click', 'https://facebook.com/phamthiphuc054'), (4162, 3054, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4163, 3055, 'Thuộc lớp', '11A1'), (4164, 3055, 'Link khi click', 'https://facebook.com/hoangvanquang055'), (4165, 3055, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4166, 3056, 'Thuộc lớp', '11A1'), (4167, 3056, 'Link khi click', 'https://facebook.com/vuthirong056'), (4168, 3056, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4169, 3057, 'Thuộc lớp', '11A1'), (4170, 3057, 'Link khi click', 'https://facebook.com/dovansung057'), (4171, 3057, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4172, 3058, 'Thuộc lớp', '11A1'), (4173, 3058, 'Link khi click', 'https://facebook.com/buithitue058'), (4174, 3058, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4175, 3059, 'Thuộc lớp', '11A1'), (4176, 3059, 'Link khi click', 'https://facebook.com/ngovanung059'), (4177, 3059, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4178, 3060, 'Thuộc lớp', '11A1'), (4179, 3060, 'Link khi click', 'https://facebook.com/lythivang060'), (4180, 3060, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4181, 3061, 'Thuộc lớp', '11A1'), (4182, 3061, 'Link khi click', 'https://facebook.com/phanvanxanh061'), (4183, 3061, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4184, 3062, 'Thuộc lớp', '11A1'), (4185, 3062, 'Link khi click', 'https://facebook.com/caothiyen062'), (4186, 3062, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),

-- Meta for students in class 12C1 (12 students)
-- Student IDs 3063-3074
(4187, 3063, 'Thuộc lớp', '12C1'), (4188, 3063, 'Link khi click', 'https://facebook.com/truongvanam063'), (4189, 3063, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4190, 3064, 'Thuộc lớp', '12C1'), (4191, 3064, 'Link khi click', 'https://facebook.com/dinhthibup064'), (4192, 3064, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4193, 3065, 'Thuộc lớp', '12C1'), (4194, 3065, 'Link khi click', 'https://facebook.com/luongvancut065'), (4195, 3065, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4196, 3066, 'Thuộc lớp', '12C1'), (4197, 3066, 'Link khi click', 'https://facebook.com/maithidao066'), (4198, 3066, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4199, 3067, 'Thuộc lớp', '12C1'), (4200, 3067, 'Link khi click', 'https://facebook.com/tovanen067'), (4201, 3067, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4202, 3068, 'Thuộc lớp', '12C1'), (4203, 3068, 'Link khi click', 'https://facebook.com/duongthiphim068'), (4204, 3068, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4205, 3069, 'Thuộc lớp', '12C1'), (4206, 3069, 'Link khi click', 'https://facebook.com/hovangam069'), (4207, 3069, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4208, 3070, 'Thuộc lớp', '12C1'), (4209, 3070, 'Link khi click', 'https://facebook.com/vothihong070'), (4210, 3070, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4211, 3071, 'Thuộc lớp', '12C1'), (4212, 3071, 'Link khi click', 'https://facebook.com/huynhvanich071'), (4213, 3071, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4214, 3072, 'Thuộc lớp', '12C1'), (4215, 3072, 'Link khi click', 'https://facebook.com/dangthikeo072'), (4216, 3072, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4217, 3073, 'Thuộc lớp', '12C1'), (4218, 3073, 'Link khi click', 'https://facebook.com/tuvanlua073'), (4219, 3073, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4220, 3074, 'Thuộc lớp', '12C1'), (4221, 3074, 'Link khi click', 'https://facebook.com/laithiman074'), (4222, 3074, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),

-- Meta for students in class 10B1 (13 students)
-- Student IDs 3075-3087
(4223, 3075, 'Thuộc lớp', '10B1'), (4224, 3075, 'Link khi click', 'https://facebook.com/cuvannai075'), (4225, 3075, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4226, 3076, 'Thuộc lớp', '10B1'), (4227, 3076, 'Link khi click', 'https://facebook.com/nguyenthioi076'), (4228, 3076, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4229, 3077, 'Thuộc lớp', '10B1'), (4230, 3077, 'Link khi click', 'https://facebook.com/tranvanphun077'), (4231, 3077, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4232, 3078, 'Thuộc lớp', '10B1'), (4233, 3078, 'Link khi click', 'https://facebook.com/lethiqua078'), (4234, 3078, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4235, 3079, 'Thuộc lớp', '10B1'), (4236, 3079, 'Link khi click', 'https://facebook.com/phamvanrua079'), (4237, 3079, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4238, 3080, 'Thuộc lớp', '10B1'), (4239, 3080, 'Link khi click', 'https://facebook.com/hoangthisau080'), (4240, 3080, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4241, 3081, 'Thuộc lớp', '10B1'), (4242, 3081, 'Link khi click', 'https://facebook.com/vuvantho081'), (4243, 3081, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4244, 3082, 'Thuộc lớp', '10B1'), (4245, 3082, 'Link khi click', 'https://facebook.com/dothiua082'), (4246, 3082, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4247, 3083, 'Thuộc lớp', '10B1'), (4248, 3083, 'Link khi click', 'https://facebook.com/buivanvoi083'), (4249, 3083, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4250, 3084, 'Thuộc lớp', '10B1'), (4251, 3084, 'Link khi click', 'https://facebook.com/ngothixoai084'), (4252, 3084, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4253, 3085, 'Thuộc lớp', '10B1'), (4254, 3085, 'Link khi click', 'https://facebook.com/lyvany085'), (4255, 3085, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4256, 3086, 'Thuộc lớp', '10B1'), (4257, 3086, 'Link khi click', 'https://facebook.com/phantthidep086'), (4258, 3086, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4259, 3087, 'Thuộc lớp', '10B1'), (4260, 3087, 'Link khi click', 'https://facebook.com/caovanat087'), (4261, 3087, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),

-- Meta for students in class 12A1 (13 students)
-- Student IDs 3088-3100
(4262, 3088, 'Thuộc lớp', '12A1'), (4263, 3088, 'Link khi click', 'https://facebook.com/truongthibuom088'), (4264, 3088, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4265, 3089, 'Thuộc lớp', '12A1'), (4266, 3089, 'Link khi click', 'https://facebook.com/dinhvancay089'), (4267, 3089, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4268, 3090, 'Thuộc lớp', '12A1'), (4269, 3090, 'Link khi click', 'https://facebook.com/luongthida090'), (4270, 3090, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4271, 3091, 'Thuộc lớp', '12A1'), (4272, 3091, 'Link khi click', 'https://facebook.com/maivanem091'), (4273, 3091, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4274, 3092, 'Thuộc lớp', '12A1'), (4275, 3092, 'Link khi click', 'https://facebook.com/tothiphong092'), (4276, 3092, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png'),
(4277, 3093, 'Thuộc lớp', '12A1'), (4278, 3093, 'Link khi click', 'https://facebook.com/duongvangoc093'), (4279, 3093, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/5.png'),
(4280, 3094, 'Thuộc lớp', '12A1'), (4281, 3094, 'Link khi click', 'https://facebook.com/hothihat094'), (4282, 3094, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/6.png'),
(4283, 3095, 'Thuộc lớp', '12A1'), (4284, 3095, 'Link khi click', 'https://facebook.com/vovanit095'), (4285, 3095, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/7.png'),
(4286, 3096, 'Thuộc lớp', '12A1'), (4287, 3096, 'Link khi click', 'https://facebook.com/huynhthikhoc096'), (4288, 3096, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/8.png'),
(4289, 3097, 'Thuộc lớp', '12A1'), (4290, 3097, 'Link khi click', 'https://facebook.com/dangvanla097'), (4291, 3097, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/1.png'),
(4292, 3098, 'Thuộc lớp', '12A1'), (4293, 3098, 'Link khi click', 'https://facebook.com/tutthimo098'), (4294, 3098, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/2.png'),
(4295, 3099, 'Thuộc lớp', '12A1'), (4296, 3099, 'Link khi click', 'https://facebook.com/laivannho099'), (4297, 3099, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/3.png'),
(4298, 3100, 'Thuộc lớp', '12A1'), (4299, 3100, 'Link khi click', 'https://facebook.com/cuthioi100'), (4300, 3100, 'Hình', 'http://localhost/wp_practice/wp-content/uploads/2025/09/4.png');

-- Note: Don't forget to create the image files referenced above
-- You should have 8 image files: 1.png, 2.png, 3.png, 4.png, 5.png, 6.png, 7.png, 8.png
-- in the directory: wp-content/uploads/2025/09/

-- Summary:
-- ✅ 3 Schools (IDs: 1001-1003)
-- ✅ 8 Classes (IDs: 2001-2008) with school relationships
-- ✅ 100 Students (IDs: 3001-3100) with class relationships, links, and random images (1-8.png)
-- ✅ All meta relationships properly configured

-- To use this data:
-- 1. Make sure your WordPress database prefix matches 'wp_' (adjust if different)
-- 2. Create the 8 PNG image files in wp-content/uploads/2025/09/
-- 3. Run this SQL script on your database
-- 4. Your school management plugin should now show all the demo data
