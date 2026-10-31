<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use ZipArchive;
use DOMDocument;
use DOMXPath;

class PC01CourseSeeder extends Seeder
{
    public function run(): void
    {
        $course = Course::where('slug', 'kien-truc-may-tinh-va-quan-tri-mang')->first();
        if (!$course) return;

        $section1 = Section::where('course_id', $course->id)->orderBy('sort_order')->first();
        if (!$section1) {
            $section1 = Section::create([
                'course_id' => $course->id,
                'title' => 'Chương 1: Kiến trúc Phần cứng & Lắp ráp PC (Kỹ Thuật Viên PC01)',
                'sort_order' => 1
            ]);
        } else {
            $section1->title = 'Chương 1: Kiến trúc Phần cứng & Lắp ráp PC (Kỹ Thuật Viên PC01)';
            $section1->save();
        }

        $lessonsConfig = [
            'PC01.01' => [
                'docx' => 'PC01.01_Tong_Quan_Ve_May_Tinh_PC_Va_Cac_Loai_Pho_Bien.docx',
                'title' => 'Bài 1: Tổng quan về máy tính PC và các loại phổ biến',
                'slug' => 'pc01-01-tong-quan-ve-may-tinh-pc-va-cac-loai-pho-bien',
                'type' => 'video',
                'video_url' => '/courses/pc01/videos/PC01.01_Hieu_Nhanh_Nguyen_Ly_Hoat_Dong_Cua_Moi_Dong_PC.mp4',
                'image_url' => '/courses/pc01/images/PC01.01/PC IPOS cycle diagram.png',
                'attachment_url' => '/courses/pc01/docs/PC01.01_Tong_Quan_Ve_May_Tinh_PC_Va_Cac_Loai_Pho_Bien.docx',
                'is_free_preview' => true,
                'duration' => 15,
                'images' => [
                    'ipos' => [
                        'trigger' => '/IPOS|luồng xử lý dữ liệu/ui',
                        'url' => '/courses/pc01/images/PC01.01/PC IPOS cycle diagram.png',
                        'caption' => 'Sơ đồ chu trình xử lý dữ liệu chuẩn IPOS (Input - Process - Output - Storage)'
                    ],
                    'desktop' => [
                        'trigger' => '/Desktop PC/ui',
                        'url' => '/courses/pc01/images/PC01.01/Hinh_PC.jpg',
                        'caption' => 'Máy tính để bàn Desktop PC với vỏ Case rời và khả năng tản nhiệt tối ưu'
                    ],
                    'laptop' => [
                        'trigger' => '/Laptop/ui',
                        'url' => '/courses/pc01/images/PC01.01/Hinh_laptop.jpg',
                        'caption' => 'Máy tính xách tay Laptop tích hợp pin và thiết kế nhỏ gọn di động'
                    ],
                    'aio' => [
                        'trigger' => '/All-in-One|AIO/ui',
                        'url' => '/courses/pc01/images/PC01.01/Maytinh_AllInOne.jpg',
                        'caption' => 'Máy tính All-in-One (AIO) gom toàn bộ linh kiện vào mặt sau màn hình'
                    ],
                    'mini' => [
                        'trigger' => '/Mini PC/ui',
                        'url' => '/courses/pc01/images/PC01.01/Hinh_MaytinhMini.jpg',
                        'caption' => 'Máy tính siêu nhỏ gọn Mini PC tiết kiệm diện tích tối đa'
                    ]
                ]
            ],
            'PC01.02' => [
                'docx' => 'PC01.02_Tim_Hieu_Thung_May_Case_Va_Cau_Tao_Ben_Trong.docx',
                'title' => 'Bài 2: Tìm hiểu Thùng máy (Case) và Cấu tạo bên trong',
                'slug' => 'pc01-02-tim-hieu-thung-may-case-va-cau-tao-ben-trong',
                'type' => 'video',
                'video_url' => '/courses/pc01/videos/PC01.02_Giai_Ma_Cau_Tao_Thung_May_PC_Tieu_Chuan.mp4',
                'image_url' => '/courses/pc01/images/PC01.02/Bố trí bên trong thùng máy tính PC.jpg',
                'attachment_url' => '/courses/pc01/docs/PC01.02_Tim_Hieu_Thung_May_Case_Va_Cau_Tao_Ben_Trong.docx',
                'is_free_preview' => true,
                'duration' => 18,
                'images' => [
                    'layout' => [
                        'trigger' => '/Bố trí|cấu trúc bên trong|Khoang/ui',
                        'url' => '/courses/pc01/images/PC01.02/Bố trí bên trong thùng máy tính PC.jpg',
                        'caption' => 'Bố trí tổng thể các linh kiện và khu vực lắp ráp bên trong thùng máy PC'
                    ],
                    'airflow' => [
                        'trigger' => '/không khí|Airflow|luồng gió/ui',
                        'url' => '/courses/pc01/images/PC01.02/Sơ đồ luồng không khí bên trong thùng máy tính PC.jpg',
                        'caption' => 'Sơ đồ luồng đối lưu không khí làm mát (Airflow) chuẩn trong Case'
                    ],
                    'shroud' => [
                        'trigger' => '/Khoang che|PSU Shroud|nguồn/ui',
                        'url' => '/courses/pc01/images/PC01.02/Khoang che bộ nguồn trong thùng máy tính PC.png',
                        'caption' => 'Khoang che bộ nguồn (PSU Shroud) và vị trí giấu dây cáp gọn gàng'
                    ]
                ]
            ],
            'PC01.03' => [
                'docx' => 'PC01.03_Tim_Hieu_Mainboard_Bo_Mach_Chu.docx',
                'title' => 'Bài 3: Tìm hiểu Mainboard (Bo Mạch Chủ)',
                'slug' => 'pc01-03-tim-hieu-mainboard-bo-mach-chu',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.03/MainBoard.png',
                'attachment_url' => '/courses/pc01/docs/PC01.03_Tim_Hieu_Mainboard_Bo_Mach_Chu.docx',
                'is_free_preview' => true,
                'duration' => 20,
                'images' => [
                    'mb' => [
                        'trigger' => '/Bo Mạch Chủ|Mainboard|Tổng quan/ui',
                        'url' => '/courses/pc01/images/PC01.03/MainBoard.png',
                        'caption' => 'Cấu trúc linh kiện và các cổng cắm trên bo mạch chủ (Mainboard)'
                    ],
                    'socket' => [
                        'trigger' => '/LGA|PGA|Socket/ui',
                        'url' => '/courses/pc01/images/PC01.03/LGA vs PGA.png',
                        'caption' => 'So sánh cơ chế chân tiếp xúc Socket LGA (chân trên Main) và PGA (chân trên CPU)'
                    ],
                    'vrm' => [
                        'trigger' => '/VRM|nguồn điện|Mosfet/ui',
                        'url' => '/courses/pc01/images/PC01.03/Tản nhiệt VRM trên bo mạch chủ.png',
                        'caption' => 'Hệ thống cấp nguồn VRM và phiến tản nhiệt nhôm làm mát bo mạch'
                    ],
                    'pcie' => [
                        'trigger' => '/PCIe|khe cắm đồ họa|x16/ui',
                        'url' => '/courses/pc01/images/PC01.03/PCIe x16.png',
                        'caption' => 'Khe cắm mở rộng PCIe x16 băng thông lớn dành cho Card đồ họa rời VGA'
                    ],
                    'm2' => [
                        'trigger' => '/M\.2|NVMe|khe cắm SSD/ui',
                        'url' => '/courses/pc01/images/PC01.03/M2 NVMe.png',
                        'caption' => 'Khe cắm ổ cứng thể rắn chuẩn tốc độ cao M.2 NVMe PCIe'
                    ]
                ]
            ],
            'PC01.04' => [
                'docx' => 'PC01.04_Tim_Hieu_CPU_Bo_Vi_Xu_Ly.docx',
                'title' => 'Bài 4: Tìm hiểu CPU (Bộ Vi Xử Lý)',
                'slug' => 'pc01-04-tim-hieu-cpu-bo-vi-xu-ly',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.04/P-core E-core architecture.png',
                'attachment_url' => '/courses/pc01/docs/PC01.04_Tim_Hieu_CPU_Bo_Vi_Xu_Ly.docx',
                'is_free_preview' => false,
                'duration' => 25,
                'images' => [
                    'arch' => [
                        'trigger' => '/P-core|E-core|kiến trúc|nhân/ui',
                        'url' => '/courses/pc01/images/PC01.04/P-core E-core architecture.png',
                        'caption' => 'Kiến trúc nhân lai hiệu năng cao P-Core và nhân tiết kiệm điện E-Core'
                    ],
                    'ihs' => [
                        'trigger' => '/IHS|nắp kim loại|nhiệt/ui',
                        'url' => '/courses/pc01/images/PC01.04/CPU IHS.png',
                        'caption' => 'Nắp kim loại bảo vệ và tiếp xúc nhiệt CPU (Integrated Heat Spreader - IHS)'
                    ],
                    'vs' => [
                        'trigger' => '/Intel|AMD|Ryzen|Core/ui',
                        'url' => '/courses/pc01/images/PC01.04/Intel vs AMD processor.jpg',
                        'caption' => 'So sánh thiết kế vi xử lý của hai ông lớn Intel Core và AMD Ryzen'
                    ],
                    'macro' => [
                        'trigger' => '/die|silicon|bán dẫn|vi mạch/ui',
                        'url' => '/courses/pc01/images/PC01.04/CPU chip macro.png',
                        'caption' => 'Ảnh chụp siêu vi mô tấm bán dẫn đế vi mạch Silicon bên trong CPU'
                    ]
                ]
            ],
            'PC01.05' => [
                'docx' => 'PC01.05_Tim_Hieu_RAM_Bo_Nho_Tam.docx',
                'title' => 'Bài 5: Tìm hiểu RAM (Bộ Nhớ Tạm)',
                'slug' => 'pc01-05-tim-hieu-ram-bo-nho-tam',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.05/Lắp đặt RAM vào bo mạch chủ.png',
                'attachment_url' => '/courses/pc01/docs/PC01.05_Tim_Hieu_RAM_Bo_Nho_Tam.docx',
                'is_free_preview' => false,
                'duration' => 20,
                'images' => [
                    'install' => [
                        'trigger' => '/Lắp đặt|cách cắm|thao tác/ui',
                        'url' => '/courses/pc01/images/PC01.05/Lắp đặt RAM vào bo mạch chủ.png',
                        'caption' => 'Thao tác đưa thanh RAM vào khe cắm đúng ngàm khóa và nghe tiếng tách'
                    ],
                    'dual' => [
                        'trigger' => '/Dual Channel|kênh đôi|khe cắm/ui',
                        'url' => '/courses/pc01/images/PC01.05/Các khe cắm RAM hỗ trợ chế độ kênh đôi.png',
                        'caption' => 'Quy tắc cắm khe xen kẽ (Khe 2 và 4) để kích hoạt chế độ Dual-Channel RAM'
                    ],
                    'notch' => [
                        'trigger' => '/DDR4|DDR5|rãnh/ui',
                        'url' => '/courses/pc01/images/PC01.05/DDR4 vs DDR5 notch.png',
                        'caption' => 'Phân biệt vị trí rãnh khóa chân tiếp xúc giữa thế hệ DDR4 và DDR5'
                    ],
                    'xmp' => [
                        'trigger' => '/XMP|EXPO|BIOS|ép xung/ui',
                        'url' => '/courses/pc01/images/PC01.05/BIOS XMP EXPO.png',
                        'caption' => 'Bật cấu hình ép xung một chạm Intel XMP / AMD EXPO trong BIOS để đạt tốc độ Bus tối đa'
                    ]
                ]
            ],
            'PC01.06' => [
                'docx' => 'PC01.06_Tim_Hieu_HDD_SSD_Thiet_Bi_Luu_Tru.docx',
                'title' => 'Bài 6: Tìm hiểu HDD / SSD (Thiết Bị Lưu Trữ)',
                'slug' => 'pc01-06-tim-hieu-hdd-ssd-thiet-bi-luu-tru',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.06/NVMe M2 SSD vs SATA SSD.png',
                'attachment_url' => '/courses/pc01/docs/PC01.06_Tim_Hieu_HDD_SSD_Thiet_Bi_Luu_Tru.docx',
                'is_free_preview' => false,
                'duration' => 20,
                'images' => [
                    'nvme' => [
                        'trigger' => '/NVMe|SATA|tốc độ|băng thông/ui',
                        'url' => '/courses/pc01/images/PC01.06/NVMe M2 SSD vs SATA SSD.png',
                        'caption' => 'So sánh tốc độ băng thông vượt trội giữa chuẩn NVMe M.2 PCIe và SATA III'
                    ],
                    'hdd' => [
                        'trigger' => '/đĩa từ|HDD|cơ học/ui',
                        'url' => '/courses/pc01/images/PC01.06/HDD platter inside.png',
                        'caption' => 'Cấu tạo cơ học gồm đĩa từ và đầu đọc cơ học bên trong ổ đĩa HDD'
                    ],
                    'form' => [
                        'trigger' => '/3\.5|2280|kích thước/ui',
                        'url' => '/courses/pc01/images/PC01.06/3.5 inch HDD vs M2 2280.png',
                        'caption' => 'So sánh kích thước vật lý giữa ổ HDD 3.5 inch truyền thống và thanh M.2 2280'
                    ],
                    'tbw' => [
                        'trigger' => '/TBW|độ bền|tuổi thọ|NAND/ui',
                        'url' => '/courses/pc01/images/PC01.06/SSD TBW.png',
                        'caption' => 'Thông số độ bền ghi chép dữ liệu TBW (Terabytes Written) và tuổi thọ chip nhớ NAND Flash'
                    ]
                ]
            ],
            'PC01.07' => [
                'docx' => 'PC01.07_Tim_Hieu_PSU_Bo_Nguon_May_Tinh.docx',
                'title' => 'Bài 7: Tìm hiểu PSU (Bộ Nguồn Máy Tính)',
                'slug' => 'pc01-07-tim-hieu-psu-bo-nguon-may-tinh',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.07/Bộ nguồn máy tính PC.jpg',
                'attachment_url' => '/courses/pc01/docs/PC01.07_Tim_Hieu_PSU_Bo_Nguon_May_Tinh.docx',
                'is_free_preview' => false,
                'duration' => 22,
                'images' => [
                    'psu' => [
                        'trigger' => '/Bộ nguồn|PSU|Trái tim/ui',
                        'url' => '/courses/pc01/images/PC01.07/Bộ nguồn máy tính PC.jpg',
                        'caption' => 'Tổng quan bộ nguồn máy tính (PSU) - Trái tim cung cấp năng lượng toàn hệ thống'
                    ],
                    'atx' => [
                        'trigger' => '/24-pin|24 pin|dây nguồn chính/ui',
                        'url' => '/courses/pc01/images/PC01.07/ATX 24 pin cable.jpg',
                        'caption' => 'Cụm dây cáp nguồn chính 24-Pin ATX cấp nguồn cho toàn bộ bo mạch chủ'
                    ],
                    'modular' => [
                        'trigger' => '/Modular|dây cáp rời|cáp rời/ui',
                        'url' => '/courses/pc01/images/PC01.07/Các dây cáp nguồn rời hoàn toàn của bộ nguồn máy tính.png',
                        'caption' => 'Nguồn chuẩn Full Modular với các cáp rời cắm theo nhu cầu, tiết kiệm không gian'
                    ],
                    'plus80' => [
                        'trigger' => '/80 Plus|Gold|hiệu suất/ui',
                        'url' => '/courses/pc01/images/PC01.07/Logo chứng nhận hiệu suất năng lượng 80 Plus Gold của bộ nguồn máy tính.png',
                        'caption' => 'Hệ thống chứng nhận hiệu suất chuyển đổi năng lượng chuẩn quốc tế 80 Plus Gold'
                    ]
                ]
            ],
            'PC01.08' => [
                'docx' => 'PC01.08_Tim_Hieu_VGA_GPU_Bo_Xu_Ly_Do_Hoa.docx',
                'title' => 'Bài 8: Tìm hiểu VGA / GPU (Bộ Xử Lý Đồ Họa)',
                'slug' => 'pc01-08-tim-hieu-vga-gpu-bo-xu-ly-do-hoa',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.08/Graphics Card GPU teardown.png',
                'attachment_url' => '/courses/pc01/docs/PC01.08_Tim_Hieu_VGA_GPU_Bo_Xu_Ly_Do_Hoa.docx',
                'is_free_preview' => false,
                'duration' => 25,
                'images' => [
                    'teardown' => [
                        'trigger' => '/cấu tạo|giải phẫu|tản nhiệt card/ui',
                        'url' => '/courses/pc01/images/PC01.08/Graphics Card GPU teardown.png',
                        'caption' => 'Giải phẫu chi tiết cấu tạo bên trong của Card màn hình rời (VGA/GPU)'
                    ],
                    'brands' => [
                        'trigger' => '/Nvidia|Radeon|GeForce|AMD/ui',
                        'url' => '/courses/pc01/images/PC01.08/Nvidia RTX AMD Radeon.jpg',
                        'caption' => 'Hai đối thủ dẫn đầu thị trường chip đồ họa hiện nay: Nvidia GeForce & AMD Radeon'
                    ],
                    'vram' => [
                        'trigger' => '/VRAM|GDDR6|bộ nhớ đồ họa/ui',
                        'url' => '/courses/pc01/images/PC01.08/VRAM GDDR6.jpg',
                        'caption' => 'Các chip nhớ đồ họa băng thông cực cao GDDR6 hàn xung quanh GPU'
                    ],
                    'raytracing' => [
                        'trigger' => '/Ray Tracing|dò tia|ánh sáng/ui',
                        'url' => '/courses/pc01/images/PC01.08/BậtTắt công nghệ dò tia ánh sáng.png',
                        'caption' => 'So sánh chất lượng hình ảnh và phản chiếu ánh sáng khi Bật và Tắt Ray Tracing'
                    ]
                ]
            ],
            'PC01.09' => [
                'docx' => 'PC01.09_Tim_Hieu_He_Thong_Tan_Nhiet_Va_Quat_Lam_Mat.docx',
                'title' => 'Bài 9: Tìm hiểu Hệ Thống Tản Nhiệt Và Quạt Làm Mát',
                'slug' => 'pc01-09-tim-hieu-he-thong-tan-nhiet-va-quat-lam-mat',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.09/So sánh tản nhiệt đi kèm CPU, tản nhiệt khí dạng tháp và tản nhiệt nước AIO.png',
                'attachment_url' => '/courses/pc01/docs/PC01.09_Tim_Hieu_He_Thong_Tan_Nhiet_Va_Quat_Lam_Mat.docx',
                'is_free_preview' => false,
                'duration' => 20,
                'images' => [
                    'coolers' => [
                        'trigger' => '/tản nhiệt khí|AIO|nước|tháp/ui',
                        'url' => '/courses/pc01/images/PC01.09/So sánh tản nhiệt đi kèm CPU, tản nhiệt khí dạng tháp và tản nhiệt nước AIO.png',
                        'caption' => 'So sánh 3 giải pháp tản nhiệt: Tản Stock zin, Tản khí dạng tháp đơn/đôi và Tản nhiệt nước All-in-One (AIO)'
                    ],
                    'paste' => [
                        'trigger' => '/keo tản nhiệt|hạt đậu|tra keo/ui',
                        'url' => '/courses/pc01/images/PC01.09/Lượng keo tản nhiệt CPU có kích thước bằng hạt đậu.jpg',
                        'caption' => 'Kỹ thuật tra keo tản nhiệt kích thước hạt đậu (Pea-sized) chuẩn xác ở tâm CPU'
                    ],
                    'rgb' => [
                        'trigger' => '/ARGB|RGB|5V|12V/ui',
                        'url' => '/courses/pc01/images/PC01.09/5V 3pin ARGB vs 12V 4pin RGB.jpg',
                        'caption' => 'Lưu ý quan trọng: Phân biệt chân cắm đèn LED 5V 3-pin ARGB (kỹ thuật số) và 12V 4-pin RGB (analog)'
                    ]
                ]
            ],
            'PC01.10' => [
                'docx' => 'PC01.10_Tim_Hieu_Cong_Ket_Noi_Day_Cap_Va_Thiet_Bi_Ngoai_Vi.docx',
                'title' => 'Bài 10: Tìm hiểu Cổng Kết Nối, Dây Cáp Và Thiết Bị Ngoại Vi',
                'slug' => 'pc01-10-tim-hieu-cong-ket-noi-day-cap-va-thiet-bi-ngoai-vi',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.10/Sơ đồ các chân kết nối của bảng điều khiển mặt trước thùng máy tính PC.png',
                'attachment_url' => '/courses/pc01/docs/PC01.10_Tim_Hieu_Cong_Ket_Noi_Day_Cap_Va_Thiet_Bi_Ngoai_Vi.docx',
                'is_free_preview' => false,
                'duration' => 20,
                'images' => [
                    'frontpanel' => [
                        'trigger' => '/Front Panel|chân cắm|nút nguồn|Power SW/ui',
                        'url' => '/courses/pc01/images/PC01.10/Sơ đồ các chân kết nối của bảng điều khiển mặt trước thùng máy tính PC.png',
                        'caption' => 'Sơ đồ chi tiết chân cắm Front Panel (Nút nguồn Power SW, Reset SW, đèn HDD LED, Power LED)'
                    ],
                    'usb' => [
                        'trigger' => '/USB 2\.0|USB 3\.0|cổng USB/ui',
                        'url' => '/courses/pc01/images/PC01.10/USB 2.0 vs USB 3.0.png',
                        'caption' => 'Phân biệt chân cổng USB 2.0 (màu đen) và chuẩn USB 3.0 tốc độ cao (màu xanh dương có thêm chân tiếp xúc)'
                    ],
                    'display' => [
                        'trigger' => '/HDMI|DisplayPort|xuất hình/ui',
                        'url' => '/courses/pc01/images/PC01.10/HDMI vs DisplayPort port.jpg',
                        'caption' => 'So sánh hai chuẩn cổng xuất hình ảnh phổ biến nhất: HDMI và DisplayPort (DP)'
                    ]
                ]
            ],
            'PC01.11' => [
                'docx' => 'PC01.11_Thuc_Hanh_Nhan_Dang_Linh_Kien_Tren_Bo_PC_Thuc_Te.docx',
                'title' => 'Bài 11: Thực hành Nhận Dạng Linh Kiện Trên Bộ PC Thực Tế',
                'slug' => 'pc01-11-thuc-hanh-nhan-dang-linh-kien-tren-bo-pc-thuc-te',
                'type' => 'doc',
                'video_url' => null,
                'image_url' => '/courses/pc01/images/PC01.11/Thực hành lắp ráp và kiểm tra trực tiếp máy tính PC.png',
                'attachment_url' => '/courses/pc01/docs/PC01.11_Thuc_Hanh_Nhan_Dang_Linh_Kien_Tren_Bo_PC_Thuc_Te.docx',
                'is_free_preview' => false,
                'duration' => 30,
                'images' => [
                    'checklist' => [
                        'trigger' => '/Checklist|kiểm tra|danh sách linh kiện/ui',
                        'url' => '/courses/pc01/images/PC01.11/Danh sách kiểm tra các linh kiện phần cứng máy tính PC.jpg',
                        'caption' => 'Checklist kiểm kê đối chiếu linh kiện trước khi tiến hành quy trình lắp ráp'
                    ],
                    'esd' => [
                        'trigger' => '/ESD|tĩnh điện|chống sốc/ui',
                        'url' => '/courses/pc01/images/PC01.11/vòng tay chống tĩnh điện ESD.png',
                        'caption' => 'Quy chuẩn trang bị an toàn: Vòng tay tiếp đất chống phóng tĩnh điện làm hỏng chip vi mạch ESD'
                    ],
                    'build' => [
                        'trigger' => '/lắp ráp|thực tế|POST|kích nguồn/ui',
                        'url' => '/courses/pc01/images/PC01.11/Thực hành lắp ráp và kiểm tra trực tiếp máy tính PC.png',
                        'caption' => 'Quy trình thực hành lắp ráp thực tế, đi dây thẩm mỹ và kích nguồn kiểm tra POST thành công'
                    ]
                ]
            ]
        ];

        $docDir = public_path('courses/pc01/docs');
        $order = 1;

        foreach ($lessonsConfig as $code => $cfg) {
            $docxPath = "$docDir/{$cfg['docx']}";
            $html = '';
            if (file_exists($docxPath)) {
                $html = $this->parseDocx($docxPath, $code, $cfg['images']);
            }

            Lesson::updateOrCreate(
                ['slug' => $cfg['slug']],
                [
                    'section_id' => $section1->id,
                    'title' => $cfg['title'],
                    'type' => $cfg['type'],
                    'video_url' => $cfg['video_url'],
                    'image_url' => $cfg['image_url'],
                    'attachment_url' => $cfg['attachment_url'],
                    'content' => $html ?: null,
                    'duration' => $cfg['duration'],
                    'is_free_preview' => $cfg['is_free_preview'],
                    'sort_order' => $order++,
                ]
            );
        }
    }

    private function parseDocx(string $docxPath, string $lessonCode, array $imagesMap): string
    {
        $zip = new ZipArchive();
        if ($zip->open($docxPath) !== true) return '';
        $xml = $zip->getFromName('word/document.xml');
        $zip->close();
        if (!$xml) return '';

        $dom = new DOMDocument();
        @$dom->loadXML($xml);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $bodyNodes = $xpath->query('//w:body/*');
        $html = [];
        $inList = false;

        foreach ($bodyNodes as $node) {
            if ($node->nodeName === 'w:tbl') {
                if ($inList) { $html[] = '</ul>'; $inList = false; }
                $html[] = $this->parseTable($node, $xpath);
                continue;
            }

            if ($node->nodeName === 'w:p') {
                $pStyleNode = $xpath->query('.//w:pStyle/@w:val', $node)->item(0);
                $style = $pStyleNode ? $pStyleNode->nodeValue : '';
                $pContent = $this->parseRuns($node, $xpath);
                $trimmed = trim(strip_tags($pContent));

                if ($trimmed === '' || str_starts_with($trimmed, 'MÃ BÀI HỌC:')) continue;

                if ($style === 'Heading1') {
                    if ($inList) { $html[] = '</ul>'; $inList = false; }
                    $html[] = '<h2 class="text-2xl font-black text-white mt-2 mb-6 pb-3 border-b border-blue-500/30 flex items-center gap-3">'
                        . '<span class="px-3 py-1 bg-blue-500/20 text-blue-400 rounded-lg text-sm font-mono font-bold">' . htmlspecialchars($lessonCode) . '</span>'
                        . '<span>' . $pContent . '</span></h2>';
                    continue;
                }

                if ($style === 'Heading2' || preg_match('/^\d+\.\s+[A-ZÀ-Ỹ]/u', $trimmed)) {
                    if ($inList) { $html[] = '</ul>'; $inList = false; }
                    $html[] = '<h3 class="text-xl font-extrabold text-white mt-8 mb-4 pt-4 border-t border-slate-800 flex items-center gap-2">'
                        . '<span class="w-2 h-6 bg-blue-500 rounded-full inline-block"></span>'
                        . '<span>' . $pContent . '</span></h3>';
                    continue;
                }

                if ($style === 'Heading3' || preg_match('/^[A-Z]\.\s+[A-ZÀ-Ỹ]/u', $trimmed)) {
                    if ($inList) { $html[] = '</ul>'; $inList = false; }
                    $html[] = '<h4 class="text-lg font-bold text-sky-300 mt-6 mb-3 flex items-center gap-2">'
                        . '<span class="text-sky-500 font-mono">▸</span>'
                        . '<span>' . $pContent . '</span></h4>';

                    foreach ($imagesMap as $imgKey => $imgInfo) {
                        if (isset($imgInfo['trigger']) && preg_match($imgInfo['trigger'], $trimmed)) {
                            $html[] = $this->renderFigure($imgInfo['url'], $imgInfo['caption']);
                            unset($imagesMap[$imgKey]);
                        }
                    }
                    continue;
                }

                $isBullet = ($style === 'ListBullet') || str_starts_with($trimmed, '•') || str_starts_with($trimmed, '- ');
                if ($isBullet) {
                    if (!$inList) {
                        $html[] = '<ul class="space-y-2 my-4 list-disc list-inside text-slate-300 pl-2">';
                        $inList = true;
                    }
                    $cleanContent = preg_replace('/^[•\-\*]\s*/u', '', $pContent);
                    $html[] = '<li class="leading-relaxed">' . $cleanContent . '</li>';
                    continue;
                } else {
                    if ($inList) { $html[] = '</ul>'; $inList = false; }
                }

                if (preg_match('/^(📌|💡|⚠️|Mẹo KTV|CÂU HỎI|TÌNH HUỐNG)/ui', $trimmed)) {
                    $calloutClass = 'bg-blue-950/30 border-blue-500/30 text-blue-200 border-l-4 border-l-blue-500';
                    if (str_contains($trimmed, '⚠️')) {
                        $calloutClass = 'bg-amber-950/30 border-amber-500/30 text-amber-200 border-l-4 border-l-amber-500';
                    } elseif (str_contains($trimmed, '💡') || str_contains($trimmed, 'Mẹo KTV')) {
                        $calloutClass = 'bg-emerald-950/30 border-emerald-500/30 text-emerald-200 border-l-4 border-l-emerald-500';
                    }
                    $html[] = '<div class="my-5 p-4 rounded-xl border ' . $calloutClass . ' shadow-sm leading-relaxed">'
                        . $pContent . '</div>';
                    continue;
                }

                $html[] = '<p class="text-slate-300 leading-relaxed mb-4">' . $pContent . '</p>';

                foreach ($imagesMap as $imgKey => $imgInfo) {
                    if (isset($imgInfo['trigger']) && preg_match($imgInfo['trigger'], $trimmed)) {
                        $html[] = $this->renderFigure($imgInfo['url'], $imgInfo['caption']);
                        unset($imagesMap[$imgKey]);
                    }
                }
            }
        }

        if ($inList) $html[] = '</ul>';

        if (!empty($imagesMap)) {
            $html[] = '<div class="mt-8 pt-6 border-t border-slate-800">';
            $html[] = '<h4 class="text-base font-bold text-slate-300 mb-4 flex items-center gap-2"><span>🖼️</span> Sơ đồ và hình ảnh tham khảo mở rộng</h4>';
            $html[] = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            foreach ($imagesMap as $imgInfo) {
                $html[] = '<div class="bg-slate-950 p-2 rounded-xl border border-slate-800">'
                    . '<img src="' . htmlspecialchars($imgInfo['url']) . '" alt="' . htmlspecialchars($imgInfo['caption']) . '" class="w-full h-48 object-cover rounded-lg" />'
                    . '<p class="text-xs text-slate-400 mt-2 text-center font-medium">' . htmlspecialchars($imgInfo['caption']) . '</p>'
                    . '</div>';
            }
            $html[] = '</div></div>';
        }

        return implode("\n", $html);
    }

    private function parseRuns($pNode, $xpath): string
    {
        $runs = $xpath->query('.//w:r', $pNode);
        $text = '';
        foreach ($runs as $r) {
            $tNode = $xpath->query('.//w:t', $r)->item(0);
            if (!$tNode) continue;
            $val = htmlspecialchars($tNode->nodeValue);

            $isBold = $xpath->query('.//w:b', $r)->length > 0;
            $isItalic = $xpath->query('.//w:i', $r)->length > 0;
            $isUnderline = $xpath->query('.//w:u', $r)->length > 0;

            if ($isBold) $val = '<strong class="text-white font-semibold">' . $val . '</strong>';
            if ($isItalic) $val = '<em class="text-slate-200">' . $val . '</em>';
            if ($isUnderline) $val = '<u>' . $val . '</u>';

            $text .= $val;
        }
        return $text;
    }

    private function parseTable($tblNode, $xpath): string
    {
        $rows = $xpath->query('.//w:tr', $tblNode);
        if ($rows->length === 0) return '';

        $tableHtml = '<div class="overflow-x-auto my-6 rounded-xl border border-slate-800 shadow-lg">';
        $tableHtml .= '<table class="min-w-full divide-y divide-slate-800 bg-slate-900/40 text-left text-sm">';
        $isHeader = true;

        foreach ($rows as $tr) {
            $cells = $xpath->query('.//w:tc', $tr);
            if ($isHeader) {
                $tableHtml .= '<thead class="bg-slate-950 text-slate-200 font-bold tracking-wider"><tr>';
                foreach ($cells as $tc) {
                    $tableHtml .= '<th class="px-4 py-3 border-r border-slate-800/80 last:border-r-0 text-xs uppercase font-extrabold text-blue-300">' . $this->parseCell($tc, $xpath) . '</th>';
                }
                $tableHtml .= '</tr></thead><tbody class="divide-y divide-slate-800/60">';
                $isHeader = false;
            } else {
                $tableHtml .= '<tr class="hover:bg-slate-800/30 transition-colors">';
                foreach ($cells as $tc) {
                    $tableHtml .= '<td class="px-4 py-3 border-r border-slate-800/60 last:border-r-0 text-slate-300 leading-relaxed">' . $this->parseCell($tc, $xpath) . '</td>';
                }
                $tableHtml .= '</tr>';
            }
        }
        $tableHtml .= '</tbody></table></div>';
        return $tableHtml;
    }

    private function parseCell($tcNode, $xpath): string
    {
        $pNodes = $xpath->query('.//w:p', $tcNode);
        $parts = [];
        foreach ($pNodes as $p) {
            $text = $this->parseRuns($p, $xpath);
            if (trim($text) !== '') $parts[] = $text;
        }
        return implode('<br/>', $parts);
    }

    private function renderFigure($url, $caption): string
    {
        return '<figure class="my-6 text-center bg-slate-950/80 p-3 rounded-2xl border border-slate-800 shadow-xl">'
            . '<img src="' . htmlspecialchars($url) . '" alt="' . htmlspecialchars($caption) . '" class="rounded-xl mx-auto max-h-96 object-contain shadow-md" />'
            . '<figcaption class="text-xs text-slate-400 mt-2.5 font-medium flex items-center justify-center gap-1.5">'
            . '<span>🔍</span> <span>' . htmlspecialchars($caption) . '</span>'
            . '</figcaption></figure>';
    }
}
