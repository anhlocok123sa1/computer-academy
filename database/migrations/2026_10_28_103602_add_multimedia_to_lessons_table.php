<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->text('image_url')->nullable()->after('video_url');
            $table->text('attachment_url')->nullable()->after('image_url');
        });

        // Đổi cột type từ ENUM sang VARCHAR(50) để hỗ trợ linh hoạt các định dạng
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'multimedia'");
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'attachment_url']);
        });

        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('video', 'doc') NOT NULL DEFAULT 'video'");
    }
};
