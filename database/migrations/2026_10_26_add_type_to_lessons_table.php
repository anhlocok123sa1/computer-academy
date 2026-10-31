<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // 'video': bài học có video embed (YouTube/Vimeo/MP4)
            // 'doc'  : bài học dạng tài liệu / bài viết thuần văn bản
            $table->enum('type', ['video', 'doc'])->default('video')->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
