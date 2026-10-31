<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo tài khoản Quản trị viên (Admin)
        \App\Models\User::create([
            'name' => 'Quản Trị Viên Hệ Thống',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'admin',
            'balance' => 10000000, // 10.000.000 đ
        ]);

        // Tạo tài khoản Học viên mẫu
        \App\Models\User::create([
            'name' => 'Học Viên CNTT',
            'email' => 'hocvien@gmail.com',
            'password' => bcrypt('12345678'),
            'role' => 'student',
            'balance' => 500000, // 500.000 đ
        ]);

        $catHardware = Category::create(['name' => 'Phần cứng & Mạng', 'slug' => 'phan-cung-mang']);
        $catServer = Category::create(['name' => 'Linux & Quản trị Server', 'slug' => 'linux-server']);
        $catWeb = Category::create(['name' => 'Lập trình Phần mềm', 'slug' => 'lap-trinh-phan-mem']);
        $catSecurity = Category::create(['name' => 'An ninh mạng & Bảo mật', 'slug' => 'an-ninh-mang']);

        // 1. Khóa học Phần cứng & Mạng
        $c1 = Course::create([
            'category_id' => $catHardware->id,
            'title' => 'Kiến trúc Máy tính, Lắp ráp PC & Quản trị Mạng căn bản',
            'slug' => 'kien-truc-may-tinh-va-quan-tri-mang',
            'thumbnail' => 'https://images.unsplash.com/photo-1587202372775-e229f172b9d7?w=900&q=80',
            'description' => 'Làm chủ nguyên lý hoạt động của CPU, Mainboard, RAM, NVMe SSD, quy trình lắp ráp PC hiệu năng cao và thiết lập hạ tầng mạng LAN, Wi-Fi Mesh, Access Point chuyên nghiệp.',
            'price' => 0,
            'level' => 'beginner',
            'is_published' => true,
        ]);

        $s1_1 = Section::create(['course_id' => $c1->id, 'title' => 'Chương 1: Kiến trúc Phần cứng & Lắp ráp PC (Kỹ Thuật Viên PC01)', 'sort_order' => 1]);
        $this->call(PC01CourseSeeder::class);

        $s1_2 = Section::create(['course_id' => $c1->id, 'title' => 'Chương 2: Thiết kế Hạ tầng Mạng & Wi-Fi Mesh', 'sort_order' => 2]);
        Lesson::create([
            'section_id' => $s1_2->id,
            'title' => 'Bài 12: Quy hoạch địa chỉ IPv4, Subnet Mask, Gateway & DNS',
            'slug' => 'quy-hoach-ip-subnet-gateway-dns',
            'video_url' => 'https://www.youtube.com/embed/keeqnciDVOo',
            'content' => '<p>Thực hành chia dải mạng nội bộ, cấu hình Static IP cho thiết bị quan trọng và xử lý xung đột IP trong mạng LAN.</p>',
            'duration' => 22,
            'is_free_preview' => false,
            'sort_order' => 1,
        ]);
        Lesson::create([
            'section_id' => $s1_2->id,
            'title' => 'Bài 13: Cấu hình Access Point & Hệ thống Wi-Fi Mesh Ethernet Backhaul',
            'slug' => 'cau-hinh-access-point-wifi-mesh',
            'video_url' => 'https://www.youtube.com/embed/rvk4F5B4U8A',
            'content' => '<p>Triển khai hệ thống mạng không dây liền mạch (Seamless Roaming) sử dụng dây cáp mạng làm trục kết nối chính (Ethernet Backhaul).</p>',
            'duration' => 30,
            'is_free_preview' => false,
            'sort_order' => 2,
        ]);

        // 2. Khóa học Linux Server
        $c2 = Course::create([
            'category_id' => $catServer->id,
            'title' => 'Làm chủ Linux Server, Nginx, Docker & Triển khai Web thực chiến',
            'slug' => 'lam-chu-linux-server-deploy-web',
            'thumbnail' => 'https://images.unsplash.com/photo-1629654297299-c8506221ca97?w=900&q=80',
            'description' => 'Hướng dẫn quản trị máy chủ Ubuntu từ con số 0: cấu hình SSH bảo mật, Nginx Reverse Proxy, MySQL, Docker Compose, Plesk/cPanel và tự động hóa SSL.',
            'price' => 499000,
            'level' => 'intermediate',
            'is_published' => true,
        ]);

        $s2_1 = Section::create(['course_id' => $c2->id, 'title' => 'Chương 1: Quản trị Hệ điều hành Ubuntu Server', 'sort_order' => 1]);
        Lesson::create([
            'section_id' => $s2_1->id,
            'title' => 'Bài 1: Kết nối SSH Key, Phân quyền chmod/chown & Tường lửa UFW',
            'slug' => 'ket-noi-ssh-phan-quyen-linux',
            'video_url' => 'https://www.youtube.com/embed/sWbUDq4S6Y8',
            'content' => '<p>Thiết lập xác thực SSH bằng khóa RSA/Ed25519, vô hiệu hóa đăng nhập bằng mật khẩu root và mở port an toàn trên Ubuntu.</p>',
            'duration' => 20,
            'is_free_preview' => true,
            'sort_order' => 1,
        ]);
        Lesson::create([
            'section_id' => $s2_1->id,
            'title' => 'Bài 2: Cấu hình LEMP Stack (Linux, Nginx, MariaDB, PHP-FPM)',
            'slug' => 'cau-hinh-lemp-stack-production',
            'video_url' => 'https://www.youtube.com/embed/9t9Mp0BGnyI',
            'content' => '<p>Tối ưu hóa worker_processes trong Nginx và cấu hình pool PHP-FPM chịu tải cao.</p>',
            'duration' => 35,
            'is_free_preview' => false,
            'sort_order' => 2,
        ]);

        // 3. Khóa học Fullstack Laravel + Vue
        $c3 = Course::create([
            'category_id' => $catWeb->id,
            'title' => 'Phát triển Web Fullstack với Laravel, Vue.js 3 & Inertia.js',
            'slug' => 'phat-trien-web-fullstack-laravel-vue',
            'thumbnail' => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=900&q=80',
            'description' => 'Xây dựng ứng dụng Single Page Application (SPA) hiện đại kết hợp sức mạnh của Laravel Backend và sự mượt mà của Vue 3 + Tailwind CSS.',
            'price' => 650000,
            'level' => 'intermediate',
            'is_published' => true,
        ]);

        $s3_1 = Section::create(['course_id' => $c3->id, 'title' => 'Chương 1: Kiến trúc Monolith SPA với Inertia.js', 'sort_order' => 1]);
        Lesson::create([
            'section_id' => $s3_1->id,
            'title' => 'Bài 1: Cơ chế hoạt động của Inertia Protocol & Shared Data',
            'slug' => 'co-che-inertia-protocol',
            'video_url' => 'https://www.youtube.com/embed/zg3bb1ddbcg',
            'content' => '<p>Hiểu rõ cách Inertia thay thế REST API truyền thống giúp giảm 50% thời gian viết code giữa Backend và Frontend.</p>',
            'duration' => 24,
            'is_free_preview' => true,
            'sort_order' => 1,
        ]);

        // 4. Khóa học Tối ưu Cơ sở dữ liệu
        $c4 = Course::create([
            'category_id' => $catWeb->id,
            'title' => 'Thiết kế & Tối ưu hóa Cơ sở dữ liệu MySQL / SQL Server triệu bản ghi',
            'slug' => 'thiet-ke-toi-uu-co-so-du-lieu-mysql',
            'thumbnail' => 'https://images.unsplash.com/photo-1544383835-bda2bc66a55d?w=900&q=80',
            'description' => 'Nắm vững chuẩn hóa CSDL (3NF), cơ chế B-Tree Indexing, phân tích Slow Query với EXPLAIN, Transaction Isolation và chống Deadlock.',
            'price' => 390000,
            'level' => 'advanced',
            'is_published' => true,
        ]);

        $s4_1 = Section::create(['course_id' => $c4->id, 'title' => 'Chương 1: Kiến trúc InnoDB & Đánh chỉ mục (Indexing)', 'sort_order' => 1]);
        Lesson::create([
            'section_id' => $s4_1->id,
            'title' => 'Bài 1: Cấu trúc B+ Tree Index và Composite Index thực chiến',
            'slug' => 'cau-truc-btree-index-mysql',
            'video_url' => 'https://www.youtube.com/embed/HubezKbFL7E',
            'content' => '<p>Khi nào Index phát huy tác dụng và các sai lầm khiến câu truy vấn bị Full Table Scan.</p>',
            'duration' => 28,
            'is_free_preview' => true,
            'sort_order' => 1,
        ]);

        // 5. Khóa học Kiểm thử Tự động (Automation Testing)
        $c5 = Course::create([
            'category_id' => $catWeb->id,
            'title' => 'Kiểm thử Phần mềm (QA/QC) & Automation Testing toàn diện',
            'slug' => 'kiem-thu-phan-mem-automation-testing',
            'thumbnail' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=900&q=80',
            'description' => 'Từ viết Test Case, Test Plan, kiểm thử API với Postman đến tự động hóa quy trình kiểm thử giao diện Web/Mobile và viết kịch bản JS Console.',
            'price' => 0,
            'level' => 'beginner',
            'is_published' => true,
        ]);

        $s5_1 = Section::create(['course_id' => $c5->id, 'title' => 'Chương 1: Tư duy QA/QC & Thiết kế Test Case chuẩn', 'sort_order' => 1]);
        Lesson::create([
            'section_id' => $s5_1->id,
            'title' => 'Bài 1: Phân tích yêu cầu (SRS) và kỹ thuật phân vùng tương đương',
            'slug' => 'phan-tich-srs-thiet-ke-testcase',
            'video_url' => 'https://www.youtube.com/embed/T3q6QcCQZQg',
            'content' => '<p>Cách phát hiện các trường hợp biên (Edge Cases) và viết báo cáo lỗi (Bug Report) rõ ràng cho Developer.</p>',
            'duration' => 19,
            'is_free_preview' => true,
            'sort_order' => 1,
        ]);

        // 6. Khóa học An ninh mạng
        $c6 = Course::create([
            'category_id' => $catSecurity->id,
            'title' => 'An ninh mạng Ứng dụng Web (OWASP Top 10) & Phòng chống tấn công',
            'slug' => 'an-ninh-mang-ung-dung-web-owasp',
            'thumbnail' => 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=900&q=80',
            'description' => 'Phân tích cơ chế tấn công và cách phòng thủ SQL Injection, XSS, CSRF, Broken Access Control, cấu hình WAF Cloudflare và bảo vệ Server.',
            'price' => 550000,
            'level' => 'advanced',
            'is_published' => true,
        ]);

        $s6_1 = Section::create(['course_id' => $c6->id, 'title' => 'Chương 1: Khai thác & Phòng thủ lỗ hổng Web phổ biến', 'sort_order' => 1]);
        Lesson::create([
            'section_id' => $s6_1->id,
            'title' => 'Bài 1: Giải phẫu lỗ hổng SQL Injection, XSS và CSRF',
            'slug' => 'giai-phau-sqli-xss-csrf',
            'video_url' => 'https://www.youtube.com/embed/wc1_2C7_q2Q',
            'content' => '<p>Thực hành kiểm tra bảo mật trên môi trường Lab và cách sử dụng Prepared Statements, CSP Header để vá lỗi triệt để.</p>',
            'duration' => 27,
            'is_free_preview' => true,
            'sort_order' => 1,
        ]);

        // 7. Khóa học Máy tính Toàn thư - Học phần 1
        $this->call(ComputerScienceCourseSeeder::class);
    }
}
