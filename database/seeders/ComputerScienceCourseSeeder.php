<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;

class ComputerScienceCourseSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy hoặc tạo danh mục
        $category = Category::firstOrCreate(
            ['slug' => 'phan-cung-mang'],
            ['name' => 'Phần cứng & Mạng']
        );

        // Tạo khóa học
        $course = Course::create([
            'category_id'  => $category->id,
            'title'        => 'Máy tính Toàn thư - Học phần 1: Tổng quan & Phân loại Kiến trúc Máy tính',
            'slug'         => 'may-tinh-toan-thu-hoc-phan-1',
            'thumbnail'    => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=900&q=80',
            'description'  => 'Cung cấp hệ thống lý thuyết toàn diện về các dòng máy tính từ cá nhân, di động, hạ tầng doanh nghiệp đến siêu máy tính và máy tính lượng tử; phân tích bài học lịch sử từ các dòng máy tính bị đào thải. Đối tượng: Sinh viên Chuyên ngành CNTT, Kỹ thuật Máy tính & Đại cương Công nghệ.',
            'price'        => 0,
            'level'        => 'beginner',
            'is_published' => true,
        ]);

        // =====================================================================
        // CHƯƠNG 1: TỔNG QUAN & PHÂN LOẠI CÁC DÒNG MÁY TÍNH CÁ NHÂN
        // =====================================================================
        $ch1 = Section::create([
            'course_id'  => $course->id,
            'title'      => 'Chương 1: Tổng quan & Phân loại các dòng Máy tính Cá nhân (Personal Computers)',
            'sort_order' => 1,
        ]);

        Lesson::create([
            'section_id'      => $ch1->id,
            'title'           => 'Bài 1.1: Máy tính Để bàn (Desktop PC), Gaming PC & All-in-One (AiO)',
            'slug'            => 'hp1-desktop-pc-gaming-pc-aio',
            'type'            => 'doc',
            'content'         => '<h2>1.1. Máy tính Để bàn (Desktop PC), Gaming PC & All-in-One (AiO)</h2>

<p>Thuật ngữ <strong>Máy tính (Computer)</strong> là một trong những khái niệm rộng lớn nhất trong thế giới công nghệ hiện đại. <strong>Máy tính cá nhân (Personal Computer - PC)</strong> đại diện cho phân khúc thiết bị phục vụ trực tiếp công việc, học tập và giải trí của từng cá nhân. Khái niệm này bao hàm nhiều kiểu dáng và kiến trúc khác nhau, từ các hệ thống thùng máy để bàn modular truyền thống đến các thiết bị di động gấp gọn hoặc bỏ túi.</p>

<p><strong>Máy tính để bàn (Desktop PC)</strong> là mô hình kinh điển xuất hiện từ những năm 1980 và vẫn duy trì vị thế vững chắc nhờ khả năng tùy biến phần cứng không giới hạn. Cấu trúc cốt lõi của một Desktop PC hoạt động như một <em>hệ thống mô-đun</em> (tương tự các khối xếp hình Lego), trong đó từng linh kiện đảm nhận một chức năng chuyên biệt và kết nối thông qua <strong>Bo mạch chủ (Motherboard)</strong>:</p>

<ul>
<li><strong>Bộ vi xử lý trung tâm (CPU):</strong> Đảm nhận vai trò não bộ, tính toán logic và điều khiển toàn bộ luồng dữ liệu hệ thống.</li>
<li><strong>Bộ xử lý đồ họa (GPU):</strong> Xử lý hình ảnh, xuất tín hiệu ra màn hình và thực hiện các phép tính song song khối lượng lớn.</li>
<li><strong>Bộ nhớ truy xuất ngẫu nhiên (RAM):</strong> Lưu trữ dữ liệu ngắn hạn phục vụ đa nhiệm tức thì của hệ điều hành và ứng dụng.</li>
<li><strong>Bộ lưu trữ (SSD/HDD):</strong> Lưu trữ dữ liệu dài hạn, quyết định tốc độ khởi động hệ thống và truy xuất tệp tin.</li>
</ul>

<p>Ưu thế tuyệt đối của Desktop PC nằm ở <strong>tỷ lệ hiệu năng trên chi phí (Value per dollar)</strong> và <strong>khả năng nâng cấp độc lập (Upgradability)</strong>. Người dùng có thể dễ dàng thay thế card đồ họa, mở rộng RAM hoặc nâng cấp CPU mà không cần thay toàn bộ hệ thống.</p>

<h3>Hai biến thể nổi bật của Desktop PC</h3>

<p><strong>🎮 Gaming PC:</strong> Phiên bản nâng cấp cực đại của Desktop PC với linh kiện tối ưu cho tốc độ và khả năng tản nhiệt. Gaming PC sử dụng các card đồ họa rời mạnh mẽ như <em>Nvidia RTX 4070</em> hoặc <em>AMD RX 7800 XT</em> để xử lý công nghệ dò tia (ray tracing) theo thời gian thực. Hệ thống yêu cầu bộ nguồn công suất cao và hệ thống tản nhiệt khí/nước chuyên dụng để tránh hiện tượng <strong>nghẽn do quá nhiệt (thermal throttling)</strong>.</p>

<p><strong>🖥️ All-in-One (AiO):</strong> Dòng máy tính tích hợp toàn bộ bo mạch, CPU, RAM và ổ cứng ngay phía sau vỏ màn hình (tiêu biểu như Apple iMac, Dell, HP). AiO mang lại sự gọn gàng, thẩm mỹ tối đa với duy nhất một dây nguồn, nhưng đánh đổi bằng khả năng nâng cấp và sửa chữa rất hạn chế.</p>',
            'duration'        => 15,
            'is_free_preview' => true,
            'sort_order'      => 1,
        ]);

        Lesson::create([
            'section_id'      => $ch1->id,
            'title'           => 'Bài 1.2: Trạm Làm Việc Chuyên Dụng (Workstation) & Máy tính Mini (Mini PC)',
            'slug'            => 'hp1-workstation-va-mini-pc',
            'type'            => 'doc',
            'content'         => '<h2>1.2. Trạm Làm Việc Chuyên Dụng (Workstation) & Máy tính Mini (Mini PC)</h2>

<p>Dù có vẻ ngoài tương tự một chiếc Desktop PC thông thường, <strong>Trạm làm việc (Workstation)</strong> được thiết kế cho các tác vụ tính toán hạng nặng liên tục và đòi hỏi độ tin cậy tuyệt đối như dựng phim 3D, mô phỏng khoa học, thiết kế CAD và biên dịch mã nguồn quy mô lớn.</p>

<blockquote>
<p>💡 <strong>Điểm Khác biệt Cốt lõi giữa Workstation và Gaming PC</strong></p>
<p>Trong khi Gaming PC tối ưu cho tốc độ khung hình trò chơi, Workstation tối ưu cho <strong>tính ổn định liên tục</strong>. Workstation sử dụng vi xử lý <em>Intel Xeon</em> hoặc <em>AMD Threadripper</em> với hàng chục nhân xử lý, bộ nhớ <strong>RAM sửa lỗi tự động (ECC RAM)</strong> để ngăn ngừa xanh màn hình/sụp hệ thống, cùng card đồ họa chuyên dụng <em>Nvidia RTX A-series</em>.</p>
<p>Một sự cố sụp nguồn khi chơi game chỉ khiến người dùng thua một trận đấu, nhưng sự cố sụp nguồn khi đang render dự án phim có thể làm thiệt hại <strong>hàng nghìn USD</strong> của doanh nghiệp.</p>
</blockquote>

<p>Ở chiều ngược lại, <strong>Máy tính Mini (Mini PC)</strong> (do Intel NUC, Beelink, Minisforum sản xuất) cô đọng toàn bộ bộ vi xử lý cấp desktop vào một hộp kim loại chỉ nhỏ bằng một chiếc bánh mì sandwich. Mini PC đáp ứng tới <strong>90% nhu cầu văn phòng và giải trí gia đình</strong> trong khi chỉ chiếm 10% không gian so với thùng máy truyền thống.</p>',
            'duration'        => 12,
            'is_free_preview' => true,
            'sort_order'      => 2,
        ]);

        Lesson::create([
            'section_id'      => $ch1->id,
            'title'           => 'Bài 1.3: Máy tính Xách tay (Laptop), Chromebook & Máy tính Bảng (Tablet)',
            'slug'            => 'hp1-laptop-chromebook-tablet',
            'type'            => 'doc',
            'content'         => '<h2>1.3. Máy tính Xách tay (Laptop), Chromebook & Máy tính Bảng (Tablet)</h2>

<p><strong>Máy tính xách tay (Laptop)</strong> là phân khúc máy tính bán chạy nhất thế giới hiện nay. Laptop nén toàn bộ màn hình, bàn phím, trackpad, pin và bo mạch vào một thiết bị gập duy nhất. Sự phát triển của Laptop luôn là cuộc đấu tranh cân bằng giữa <strong>Hiệu năng (Performance)</strong> và <strong>Tính cơ động (Portability)</strong>. Mức RAM 16GB và SSD 512GB hiện được xem là cấu hình chuẩn cho người dùng hiện đại.</p>

<p><strong>Máy tính bảng (Tablet)</strong> — như iPad, Samsung Galaxy Tab — loại bỏ bàn phím vật lý để trở thành một tấm kính cảm ứng hoàn toàn. Tablet hoạt động ở khoảng không gian giữa Smartphone và Laptop, phát huy sức mạnh tối đa trong các tác vụ vẽ nghệ thuật, đọc tài liệu và giải trí phương tiện.</p>

<h3>📊 Bảng So sánh Hiệu năng vs. Tính Cơ động Các Dòng Máy tính</h3>

<table>
<thead>
<tr><th>Dòng Máy Tính</th><th>Linh Kiện Cốt Lõi</th><th>Ưu Điểm Chính</th><th>Hạn Chế Lớn Nhất</th><th>Mức Giá TB</th></tr>
</thead>
<tbody>
<tr><td>Desktop PC</td><td>CPU/GPU mô-đun, RAM chuẩn, SSD/HDD</td><td>Nâng cấp linh hoạt, P/P tối ưu</td><td>Không có tính di động</td><td>$400 – $1,500</td></tr>
<tr><td>Gaming PC</td><td>GPU rời (RTX 4070), Tản nhiệt lớn</td><td>Hiệu năng đồ họa & Ray tracing</td><td>Tiêu thụ điện năng cao</td><td>$1,000 – $3,500</td></tr>
<tr><td>Workstation</td><td>Xeon/Threadripper, ECC RAM, RTX A-series</td><td>Hoạt động 24/7 không sụp lỗi</td><td>Chi phí đầu tư rất cao</td><td>$2,000 – $10,000+</td></tr>
<tr><td>All-in-One</td><td>Tích hợp bo mạch sau màn hình</td><td>Thiết kế cực kỳ gọn gàng</td><td>Khó sửa chữa & nâng cấp</td><td>$800 – $2,500</td></tr>
<tr><td>Mini PC</td><td>CPU Desktop tích hợp, iGPU</td><td>Kích thước siêu nhỏ</td><td>Không gắn được GPU rời lớn</td><td>$150 – $600</td></tr>
<tr><td>Laptop</td><td>Nén Pin, Màn hình, Phím, Trackpad</td><td>Tính di động cao, dùng mọi nơi</td><td>Tản nhiệt hạn chế hơn PC</td><td>$500 – $2,000</td></tr>
<tr><td>Tablet</td><td>Màn hình cảm ứng, Chip di động</td><td>Thao tác bút/tay, rất nhẹ</td><td>Đa nhiệm chuyên sâu kém hơn</td><td>$300 – $1,200</td></tr>
</tbody>
</table>',
            'duration'        => 15,
            'is_free_preview' => false,
            'sort_order'      => 3,
        ]);

        // =====================================================================
        // CHƯƠNG 2: THIẾT BỊ DI ĐỘNG, THIẾT BỊ ĐEO & HĐH ĐÁM MÂY
        // =====================================================================
        $ch2 = Section::create([
            'course_id'  => $course->id,
            'title'      => 'Chương 2: Thiết bị Di động, Thiết bị Đeo & Hệ điều hành Đám mây',
            'sort_order' => 2,
        ]);

        Lesson::create([
            'section_id'      => $ch2->id,
            'title'           => 'Bài 2.1: Điện thoại Thông minh (Smartphone) & Thiết bị Đeo (Wearables)',
            'slug'            => 'hp1-smartphone-va-wearables',
            'type'            => 'doc',
            'content'         => '<h2>2.1. Điện thoại Thông minh (Smartphone) & Thiết bị Đeo (Wearables)</h2>

<p>Sự thu nhỏ của bộ vi xử lý và công nghệ năng lượng pin đã biến các thiết bị bỏ túi và đeo trên cơ thể thành những máy tính thực thụ với năng lực xử lý vượt xa các hệ thống máy tính để bàn của một thập kỷ trước.</p>

<h3>📱 Smartphone</h3>
<p><strong>Điện thoại thông minh (Smartphone)</strong> là dòng máy tính cá nhân phổ biến nhất mà con người sở hữu. Mặc dù tên gọi chứa từ "điện thoại", việc thực hiện cuộc gọi thoại chỉ đứng thứ 5 hoặc thứ 6 trong số các tác vụ được sử dụng nhiều nhất. Smartphone đảm nhận vai trò:</p>
<ul>
<li>Trung tâm lưu trữ danh tính</li>
<li>Giao dịch ngân hàng</li>
<li>Định vị GPS</li>
<li>Chụp ảnh chuyên nghiệp</li>
<li>Truy cập Internet mọi lúc mọi nơi nhờ công nghệ mạng di động <strong>LTE/5G</strong></li>
</ul>

<h3>⌚ Thiết bị Đeo Thông minh (Wearables)</h3>
<p><strong>Thiết bị đeo thông minh (Wearables)</strong> — như Apple Watch, Samsung Galaxy Watch — mở rộng hệ sinh thái máy tính lên cổ tay người dùng. Nhờ tích hợp các cảm biến sinh học, Wearables có khả năng:</p>
<ul>
<li>Đo nhịp tim và theo dõi chỉ số sức khỏe</li>
<li>Định vị GPS</li>
<li>Thực hiện cuộc gọi độc lập qua kết nối LTE tích hợp</li>
</ul>',
            'duration'        => 10,
            'is_free_preview' => false,
            'sort_order'      => 1,
        ]);

        Lesson::create([
            'section_id'      => $ch2->id,
            'title'           => 'Bài 2.2: Máy tính Hệ điều hành Đám mây (Chromebook)',
            'slug'            => 'hp1-chromebook-he-dieu-hanh-dam-may',
            'type'            => 'doc',
            'content'         => '<h2>2.2. Máy tính Hệ điều hành Đám mây (Chromebook)</h2>

<p><strong>Chromebook</strong> thay đổi hoàn toàn triết lý thiết kế máy tính truyền thống. Thay vì chạy hệ điều hành nặng cồng kềnh như Windows hay macOS, Chromebook vận hành <strong>Chrome OS</strong> — một hệ điều hành siêu nhẹ xây dựng xoay quanh trình duyệt web.</p>

<p>Mọi tác vụ như soạn thảo văn bản (Google Docs), gửi email (Gmail) hay xem video đều diễn ra trên môi trường <strong>điện toán đám mây</strong>.</p>

<h3>Ưu điểm nổi bật của Chromebook:</h3>
<ul>
<li>💰 <strong>Giá thành rất rẻ:</strong> $200 – $500</li>
<li>🔋 <strong>Thời lượng pin vượt trội</strong> nhờ yêu cầu cấu hình phần cứng thấp</li>
<li>🏫 Trở thành <strong>thiết bị tiêu chuẩn trong hệ thống giáo dục học đường</strong></li>
<li>🔒 Bảo mật cao nhờ cập nhật tự động từ Google</li>
</ul>

<blockquote>
<p>💡 <strong>Triết lý Chrome OS:</strong> Thay vì cài phần mềm lên máy, mọi thứ chạy trên đám mây — nghĩa là bạn có thể đăng nhập vào bất kỳ Chromebook nào và tiếp tục công việc ngay lập tức.</p>
</blockquote>',
            'duration'        => 8,
            'is_free_preview' => false,
            'sort_order'      => 2,
        ]);

        // =====================================================================
        // CHƯƠNG 3: HỆ THỐNG MÁY TÍNH QUY MÔ LỚN & HẠ TẦNG DOANH NGHIỆP
        // =====================================================================
        $ch3 = Section::create([
            'course_id'  => $course->id,
            'title'      => 'Chương 3: Hệ thống Máy tính Quy mô lớn & Hạ tầng Doanh nghiệp',
            'sort_order' => 3,
        ]);

        Lesson::create([
            'section_id'      => $ch3->id,
            'title'           => 'Bài 3.1: Máy tính Nhúng Đơn bo (Single Board Computer - SBC)',
            'slug'            => 'hp1-may-tinh-nhung-don-bo-sbc',
            'type'            => 'doc',
            'content'         => '<h2>3.1. Máy tính Nhúng Đơn bo (Single Board Computer - SBC)</h2>

<p>Đằng sau các giao dịch tài chính, video phát trực tuyến hay dự báo thời tiết hàng ngày là sự vận hành âm thầm của các hệ thống máy tính quy mô khổng lồ, từ các bo mạch kích thước thẻ tín dụng đến các cơ sở máy chủ chiếm trọn các tòa nhà.</p>

<p><strong>Máy tính nhúng đơn bo (SBC)</strong> — tiêu biểu là <strong>Raspberry Pi</strong> có giá từ $35 — tích hợp toàn bộ vi xử lý ARM, bộ nhớ RAM, cổng USB, Wi-Fi và các chân cắm giao tiếp ngoại vi (GPIO) trên một bảng mạch duy nhất kích thước bằng thẻ ATM.</p>

<p>SBC <strong>không phải là đồ chơi</strong>; chúng chạy hệ điều hành Linux hoàn chỉnh và được các doanh nghiệp ứng dụng rộng rãi trong:</p>
<ul>
<li>🌡️ Trạm thời tiết</li>
<li>🏠 Máy chủ gia đình</li>
<li>🌐 Hệ thống IoT (Internet of Things)</li>
<li>🏭 Tự động hóa nhà máy</li>
</ul>',
            'duration'        => 10,
            'is_free_preview' => false,
            'sort_order'      => 1,
        ]);

        Lesson::create([
            'section_id'      => $ch3->id,
            'title'           => 'Bài 3.2: Máy Chủ Hạ Tầng (Enterprise Network Server)',
            'slug'            => 'hp1-may-chu-ha-tang-enterprise-server',
            'type'            => 'doc',
            'content'         => '<h2>3.2. Máy Chủ Hạ Tầng (Enterprise Network Server)</h2>

<p><strong>Máy chủ (Server)</strong> là trung tâm cung cấp dữ liệu cho các máy tính khác trong mạng. Khác với máy tính cá nhân, Server đặt trong các <strong>trung tâm dữ liệu (Data Center)</strong>, hoạt động liên tục <strong>24/7/365</strong> với các linh kiện chuyên dụng:</p>

<ul>
<li><strong>Bộ vi xử lý song song:</strong> Chạy các dòng chip AMD EPYC hoặc Intel Xeon tối ưu cho tác vụ xử lý hàng nghìn luồng dữ liệu đồng thời.</li>
<li><strong>Bộ nhớ RAM sửa lỗi (ECC RAM):</strong> Dung lượng thường vượt quá 128GB, tự động phát hiện và sửa lỗi bit bộ nhớ để tránh sụp hệ thống.</li>
<li><strong>Nguồn điện & Mạng dự phòng:</strong> Trang bị bộ nguồn đôi (Redundant Power Supplies) tự động chuyển đổi khi có sự cố.</li>
</ul>',
            'duration'        => 12,
            'is_free_preview' => false,
            'sort_order'      => 2,
        ]);

        Lesson::create([
            'section_id'      => $ch3->id,
            'title'           => 'Bài 3.3: Máy tính Đại thể (Mainframe) & Siêu máy tính (Supercomputer)',
            'slug'            => 'hp1-mainframe-va-supercomputer',
            'type'            => 'doc',
            'content'         => '<h2>3.3. Máy tính Đại thể (Mainframe) & Siêu máy tính (Supercomputer)</h2>

<p>Nếu Server là một bếp ăn công nghiệp thì <strong>Mainframe</strong> là toàn bộ chuỗi cung ứng thực phẩm. Mainframe (do IBM thống trị với hệ điều hành <strong>z/OS</strong>) được thiết kế để xử lý <strong>hàng triệu giao dịch tài chính mỗi giây</strong> với độ tin cậy tuyệt đối trong hàng thập kỷ.</p>

<p>Hiện nay, khoảng <strong>70% dữ liệu giao dịch toàn cầu</strong> (rút tiền ATM, đặt vé máy bay, giao dịch ngân hàng) vẫn đang chạy trên các hệ thống Mainframe.</p>

<h3>🖥️ Siêu máy tính (Supercomputer)</h3>
<p>Ở cấp độ cao nhất của năng lực tính toán con người, <strong>Siêu máy tính (Supercomputer)</strong> liên kết hàng nghìn bộ vi xử lý làm việc song song để giải quyết các bài toán phức tạp nhất:</p>
<ul>
<li>☢️ Mô phỏng phản ứng hạt nhân</li>
<li>🌍 Dự báo biến đổi khí hậu</li>
<li>💊 Nghiên cứu cấu trúc phân tử thuốc</li>
</ul>

<blockquote>
<p>💡 <strong>Siêu máy tính Mạnh nhất Thế giới: Frontier</strong></p>
<p>Siêu máy tính Frontier đặt tại Phòng thí nghiệm Quốc gia Oak Ridge (Mỹ) đạt năng lực tính toán vượt mốc <strong>1 ExaFLOPS</strong> (>1 tỷ tỷ phép tính số thực phẩy động mỗi giây — 10<sup>18</sup> FLOPS).</p>
<p>Để so sánh: Nếu toàn bộ 8 tỷ người trên Trái Đất cùng thực hiện 1 phép tính mỗi giây không ngừng nghỉ, nhân loại sẽ mất khoảng <strong>4 năm</strong> để hoàn thành khối lượng tính toán mà Frontier thực hiện chỉ trong <strong>1 giây</strong>!</p>
</blockquote>

<h3>📊 Bảng So sánh Server vs. Mainframe vs. Supercomputer</h3>

<table>
<thead>
<tr><th>Tiêu Chí</th><th>Server</th><th>Mainframe</th><th>Supercomputer</th></tr>
</thead>
<tbody>
<tr><td><strong>Mục đích cốt lõi</strong></td><td>Cung cấp dịch vụ web, tệp & ứng dụng</td><td>Xử lý giao dịch tài chính khối lượng lớn</td><td>Tính toán khoa học & mô phỏng phức tạp</td></tr>
<tr><td><strong>Đơn vị đo năng lực</strong></td><td>Băng thông & Số lượng Request/giây</td><td>Số lượng Giao dịch/giây (TPS)</td><td>Số phép tính phẩy động/giây (FLOPS)</td></tr>
<tr><td><strong>Hệ điều hành tiêu biểu</strong></td><td>Linux Enterprise, Windows Server</td><td>IBM z/OS</td><td>Chuyên biệt hóa dựa trên Linux</td></tr>
<tr><td><strong>Đặc tính phần cứng</strong></td><td>Racks gắn khung, CPU Xeon/EPYC, ECC RAM</td><td>Hệ thống I/O băng thông cực lớn, độ bền hàng chục năm</td><td>Hàng nghìn node tính toán liên kết song song</td></tr>
<tr><td><strong>Ví dụ thực tế</strong></td><td>Máy chủ YouTube, Web Hosting</td><td>Hệ thống quản lý tài khoản Ngân hàng, ATM</td><td>Frontier (Oak Ridge National Lab)</td></tr>
</tbody>
</table>',
            'duration'        => 18,
            'is_free_preview' => false,
            'sort_order'      => 3,
        ]);

        // =====================================================================
        // CHƯƠNG 4: MÁY TÍNH NHÚNG & KỶ NGUYÊN MÁY TÍNH LƯỢNG TỬ
        // =====================================================================
        $ch4 = Section::create([
            'course_id'  => $course->id,
            'title'      => 'Chương 4: Máy tính Nhúng & Kỷ nguyên Máy tính Lượng tử',
            'sort_order' => 4,
        ]);

        Lesson::create([
            'section_id'      => $ch4->id,
            'title'           => 'Bài 4.1: Máy tính Nhúng (Embedded Systems) — Sự Hiện Diện Ẩn',
            'slug'            => 'hp1-may-tinh-nhung-embedded-systems',
            'type'            => 'doc',
            'content'         => '<h2>4.1. Máy tính Nhúng (Embedded Systems) — Sự Hiện Diện Ẩn</h2>

<p>Quan niệm phổ biến cho rằng máy tính bắt buộc phải có màn hình và bàn phím là <strong>hoàn toàn sai lầm</strong>.</p>

<p>Đại đa số máy tính đang tồn tại trên Trái Đất là <strong>Máy tính Nhúng (Embedded Systems)</strong> — các vi điều khiển giá rẻ chạy phần mềm cố định (<strong>Firmware</strong>) nằm ẩn bên trong các thiết bị gia dụng và công nghiệp:</p>

<ul>
<li>🧺 Máy giặt</li>
<li>🍲 Lò vi sóng</li>
<li>🚗 Hệ thống điều khiển động cơ ô tô (ECU)</li>
<li>🛗 Thang máy</li>
<li>🪥 Bàn chải đánh răng điện</li>
</ul>

<p>Embedded Systems không cần giao diện đồ họa phức tạp — chúng chỉ thực hiện <strong>một nhiệm vụ duy nhất</strong> với độ tin cậy tuyệt đối và chi phí sản xuất cực thấp.</p>',
            'duration'        => 10,
            'is_free_preview' => false,
            'sort_order'      => 1,
        ]);

        Lesson::create([
            'section_id'      => $ch4->id,
            'title'           => 'Bài 4.2: Máy tính Lượng tử (Quantum Computing) — Tương Lai Tính Toán',
            'slug'            => 'hp1-may-tinh-luong-tu-quantum-computing',
            'type'            => 'doc',
            'content'         => '<h2>4.2. Máy tính Lượng tử (Quantum Computing) — Tương Lai Tính Toán</h2>

<p><strong>Máy tính lượng tử</strong> đại diện cho một bước nhảy vọt về nguyên lý vận hành. Thay vì sử dụng các bit nhị phân truyền thống (chỉ nhận giá trị <strong>0</strong> hoặc <strong>1</strong>), máy tính lượng tử sử dụng <strong>Qubit</strong> dựa trên <em>nguyên lý chồng chập lượng tử (superposition)</em>, cho phép Qubit <strong>tồn tại ở cả trạng thái 0 và 1 cùng một lúc</strong>.</p>

<h3>🏢 Các tập đoàn tiên phong</h3>
<p>Các tập đoàn như <strong>IBM</strong>, <strong>Google</strong> và <strong>Microsoft</strong> đang đầu tư hàng tỷ USD vào công nghệ này.</p>

<blockquote>
<p>🚀 <strong>Thành tựu nổi bật:</strong> Bộ xử lý lượng tử của Google đã giải bài toán phức tạp trong <strong>vài phút</strong> mà siêu máy tính truyền thống phải mất <strong>hàng nghìn năm</strong>.</p>
</blockquote>

<h3>⚠️ Thách thức và Giới hạn</h3>
<ul>
<li>🌡️ Đòi hỏi môi trường làm việc ở nhiệt độ tiệm cận <strong>độ 0 tuyệt đối (-273°C)</strong></li>
<li>💻 Sẽ <strong>không thay thế</strong> laptop cá nhân</li>
<li>🔐 Đóng vai trò công cụ chuyên dụng cho <strong>mã hóa bảo mật</strong> và <strong>mô phỏng hóa học phân tử</strong></li>
</ul>',
            'duration'        => 12,
            'is_free_preview' => false,
            'sort_order'      => 2,
        ]);

        // =====================================================================
        // CHƯƠNG 5: LỊCH SỬ TIẾN HÓA & BÀI HỌC TỪ CÁC DÒNG MÁY TÍNH THẤT BẠI
        // =====================================================================
        $ch5 = Section::create([
            'course_id'  => $course->id,
            'title'      => 'Chương 5: Lịch sử Tiến hóa & Bài học từ các dòng Máy tính Thất bại',
            'sort_order' => 5,
        ]);

        Lesson::create([
            'section_id'      => $ch5->id,
            'title'           => 'Bài 5.1: Các Thiết Bị Bị Đào Thải Trong Lịch Sử',
            'slug'            => 'hp1-thiet-bi-bi-dao-thai-lich-su',
            'type'            => 'doc',
            'content'         => '<h2>5.1. Các Thiết Bị Bị Đào Thải Trong Lịch Sử</h2>

<p>Lịch sử phát triển máy tính không chỉ có những thành công rực rỡ mà còn ghi nhận nhiều dòng thiết bị bị đào thải do thiếu tính đa năng, trải nghiệm người dùng kém hoặc bị các công nghệ mới hấp thụ.</p>

<h3>📱 Netbook (Cuối những năm 2000)</h3>
<p>Khởi đầu bởi <strong>Asus Eee PC</strong>, Netbook là những chiếc laptop siêu nhỏ, giá rẻ phục vụ lướt web. Tuy nhiên, <strong>màn hình nhỏ, bàn phím chật chội và vi xử lý quá yếu</strong> khiến chúng nhanh chóng bị tiêu diệt khi iPad và Smartphone màn hình lớn xuất hiện.</p>

<h3>📋 Sổ tay Kỹ thuật số Cá nhân (PDA - Personal Digital Assistant)</h3>
<p>Các thiết bị như <strong>Palm Pilot</strong> nổi tiếng vào thập niên 1990 dùng bút cảm ứng để quản lý lịch làm việc và danh bạ. PDA hoàn toàn biến mất khi điện thoại thông minh tích hợp toàn bộ các chức năng này.</p>

<h3>💻 Tablet PC Sơ khai (Thập niên 2000)</h3>
<p>Thử nghiệm của Microsoft khi gắn màn hình cảm ứng và bút vào laptop chạy Windows truyền thống. Khả năng nhận diện chữ viết tay chậm và hệ điều hành không tối ưu cho cảm ứng khiến nó thất bại cho đến khi Apple ra mắt <strong>iPad năm 2010</strong>.</p>',
            'duration'        => 12,
            'is_free_preview' => false,
            'sort_order'      => 1,
        ]);

        Lesson::create([
            'section_id'      => $ch5->id,
            'title'           => 'Bài 5.2: Kiến Trúc Phụ Thuộc & Thiết Bị Thụ Động',
            'slug'            => 'hp1-kien-truc-phu-thuoc-thiet-bi-thu-dong',
            'type'            => 'doc',
            'content'         => '<h2>5.2. Kiến Trúc Phụ Thuộc & Thiết Bị Thụ Động</h2>

<h3>🖥️ Mainframe Terminals (1960s–1970s)</h3>
<p>Các thiết bị "ngu" chỉ gồm màn hình và bàn phím, <strong>không có bộ vi xử lý riêng</strong>, kết nối dây trực tiếp vào Mainframe trung tâm. Mọi sức mạnh tính toán nằm ở Mainframe — terminal chỉ là "cửa sổ" để nhìn vào.</p>

<h3>🔗 Thin Client</h3>
<p>Máy tính đóng vai trò <strong>thiết bị hiển thị kết quả từ máy chủ doanh nghiệp</strong>. Mọi thao tác bị đóng băng ngay lập tức nếu đường truyền mạng gặp sự cố. Đây là nhược điểm lớn nhất của mô hình phụ thuộc hoàn toàn vào mạng.</p>

<h3>📚 Stack Computer</h3>
<p>Kiến trúc vi xử lý tính toán dựa trên cấu trúc dữ liệu <strong>Ngăn xếp (Push/Pop)</strong> thay vì các <strong>thanh ghi (Registers)</strong>. Dù đơn giản hóa xử lý lệnh, nó không thể mở rộng cho phần mềm đa năng và bị các thiết bị dựa trên thanh ghi đánh bại.</p>

<blockquote>
<p>💡 <strong>Bài học chung:</strong> Mọi dòng máy tính thất bại đều chia sẻ một đặc điểm: chúng hoặc quá phụ thuộc vào một yếu tố bên ngoài (mạng, máy chủ), hoặc không đủ linh hoạt để đáp ứng nhu cầu đa dạng của người dùng.</p>
</blockquote>',
            'duration'        => 10,
            'is_free_preview' => false,
            'sort_order'      => 2,
        ]);

        // =====================================================================
        // PHỤ LỤC: CÂU HỎI ÔN TẬP & BÀI TẬP THỰC HÀNH
        // =====================================================================
        $phuluc = Section::create([
            'course_id'  => $course->id,
            'title'      => 'Phụ lục: Câu hỏi Ôn tập & Bài tập Thực hành Học phần 1',
            'sort_order' => 6,
        ]);

        Lesson::create([
            'section_id'      => $phuluc->id,
            'title'           => 'Câu hỏi Trắc nghiệm Củng cố Kiến thức (10 Câu)',
            'slug'            => 'hp1-cau-hoi-trac-nghiem-on-tap',
            'type'            => 'doc',
            'content'         => '<h2>A. Câu Hỏi Trắc Nghiệm Củng Cố Kiến Thức (10 Câu)</h2>

<h3>Câu 1</h3>
<p><strong>Ưu điểm lớn nhất của máy tính để bàn (Desktop PC) so với máy tính xách tay (Laptop) là gì?</strong></p>
<ol type="A">
<li>Tính cơ động cao hơn</li>
<li>Khả năng nâng cấp linh hoạt và tỷ lệ hiệu năng/chi phí tối ưu ✅</li>
<li>Tích hợp sẵn pin dung lượng lớn</li>
<li>Sử dụng hệ điều hành di động</li>
</ol>

<h3>Câu 2</h3>
<p><strong>Bộ nhớ RAM ECC thường được trang bị trên dòng máy tính nào để ngăn ngừa lỗi sụp hệ thống?</strong></p>
<ol type="A">
<li>Chromebook</li>
<li>Netbook</li>
<li>Workstation và Server ✅</li>
<li>Tablet PC</li>
</ol>

<h3>Câu 3</h3>
<p><strong>Năng lực tính toán của Siêu máy tính (Supercomputer) được đo bằng đơn vị nào?</strong></p>
<ol type="A">
<li>Transactions per second (TPS)</li>
<li>Floating-point operations per second (FLOPS) ✅</li>
<li>Gigahertz (GHz)</li>
<li>Revolutions per minute (RPM)</li>
</ol>

<h3>Câu 4</h3>
<p><strong>Hệ điều hành Chrome OS trên Chromebook hoạt động dựa trên triết lý thiết kế nào?</strong></p>
<ol type="A">
<li>Chạy phần mềm đồ họa nặng offline</li>
<li>Xây dựng xoay quanh trình duyệt web và điện toán đám mây ✅</li>
<li>Điều khiển thiết bị nhúng thời gian thực</li>
<li>Tương thích tuyệt đối với phần mềm Windows cổ điển</li>
</ol>

<h3>Câu 5</h3>
<p><strong>Thiết bị máy tính nào sau đây bị đào thải do bàn phím quá nhỏ và vi xử lý quá yếu?</strong></p>
<ol type="A">
<li>Mainframe</li>
<li>Workstation</li>
<li>Netbook ✅</li>
<li>Mini PC</li>
</ol>

<h3>Câu 6</h3>
<p><strong>Điểm khác biệt cốt lõi của Qubit trong máy tính lượng tử so với bit nhị phân truyền thống là gì?</strong></p>
<ol type="A">
<li>Qubit chỉ có giá trị 1</li>
<li>Qubit tồn tại đồng thời ở cả trạng thái 0 và 1 nhờ nguyên lý chồng chập ✅</li>
<li>Qubit không cần điện năng để hoạt động</li>
<li>Qubit có kích thước lớn hơn bit nhị phân</li>
</ol>

<h3>Câu 7</h3>
<p><strong>Khoảng bao nhiêu % dữ liệu giao dịch tài chính toàn cầu hiện nay vẫn chạy trên Mainframe?</strong></p>
<ol type="A">
<li>10%</li>
<li>30%</li>
<li>50%</li>
<li>70% ✅</li>
</ol>

<h3>Câu 8</h3>
<p><strong>Máy tính nhúng đơn bo Raspberry Pi ($35) sử dụng vi xử lý dựa trên kiến trúc nào?</strong></p>
<ol type="A">
<li>Intel Xeon</li>
<li>AMD Threadripper</li>
<li>ARM ✅</li>
<li>IBM z/Architecture</li>
</ol>

<h3>Câu 9</h3>
<p><strong>Nhược điểm lớn nhất của mô hình Thin Client trong môi trường doanh nghiệp là gì?</strong></p>
<ol type="A">
<li>Giá thành thiết bị quá đắt</li>
<li>Hoàn toàn phụ thuộc vào đường truyền mạng và độ ổn định của máy chủ ✅</li>
<li>Không thể kết nối màn hình ngoài</li>
<li>Tiêu thụ điện năng lớn</li>
</ol>

<h3>Câu 10</h3>
<p><strong>Thiết bị nào sau đây là ví dụ điển hình của Máy tính Nhúng (Embedded System)?</strong></p>
<ol type="A">
<li>Apple iMac</li>
<li>Vi điều khiển trong máy giặt / Bàn chải điện ✅</li>
<li>Siêu máy tính Frontier</li>
<li>Máy tính bảng iPad</li>
</ol>',
            'duration'        => 15,
            'is_free_preview' => false,
            'sort_order'      => 1,
        ]);

        Lesson::create([
            'section_id'      => $phuluc->id,
            'title'           => 'Bài tập Thảo luận & Tình huống Thực tế (3 Bài)',
            'slug'            => 'hp1-bai-tap-thao-luan-tinh-huong',
            'type'            => 'doc',
            'content'         => '<h2>B. Câu Hỏi Thảo Luận & Bài Tập Tình Huống Thực Tế</h2>

<h3>📝 Bài tập 1: Phân tích Chi phí Đầu tư</h3>
<p>Một công ty thiết kế kiến trúc cần trang bị máy tính cho <strong>10 kiến trúc sư dựng hình 3D</strong> và <strong>20 nhân viên văn phòng</strong>.</p>
<p>Dựa trên kiến thức Học phần 1, hãy đề xuất cấu hình loại máy tính phù hợp cho từng nhóm nhân sự và giải thích lý do lựa chọn (xét trên khía cạnh hiệu năng, ECC RAM, và ngân sách).</p>

<blockquote>
<p>💡 <strong>Gợi ý:</strong> Kiến trúc sư cần tác vụ render 3D liên tục → xem lại mục Workstation. Nhân viên văn phòng chỉ cần soạn thảo & email → xem lại mục Mini PC hoặc Desktop PC cơ bản.</p>
</blockquote>

<hr/>

<h3>📝 Bài tập 2: Bài học Thiết kế Sản phẩm</h3>
<p>Phân tích các nguyên nhân thất bại của <strong>Netbook</strong> và <strong>Tablet PC thập niên 2000</strong>.</p>
<p>Tại sao <strong>iPad</strong> và <strong>Laptop Ultrabook</strong> hiện đại lại thành công khi giải bài toán tương tự?</p>

<blockquote>
<p>💡 <strong>Gợi ý:</strong> So sánh trải nghiệm người dùng, hệ điều hành được tối ưu cho cảm ứng (iOS) vs. hệ điều hành desktop gắn thêm cảm ứng (Windows XP Tablet Edition).</p>
</blockquote>

<hr/>

<h3>📝 Bài tập 3: So sánh Hạ tầng Doanh nghiệp</h3>
<p>Phân biệt sự khác nhau về kiến trúc và nhiệm vụ giữa <strong>Server</strong>, <strong>Mainframe</strong> và <strong>Supercomputer</strong>.</p>
<p>Tại sao một ngân hàng lớn <strong>không thay thế Mainframe bằng một Siêu máy tính</strong>?</p>

<blockquote>
<p>💡 <strong>Gợi ý:</strong> Mainframe tối ưu cho giao dịch I/O (TPS), Supercomputer tối ưu cho tính toán khoa học (FLOPS). Hai bài toán hoàn toàn khác nhau.</p>
</blockquote>',
            'duration'        => 20,
            'is_free_preview' => false,
            'sort_order'      => 2,
        ]);
    }
}
