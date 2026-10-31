<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Thêm cột role (quyền) và balance (số dư ví) vào bảng users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('student')->after('email'); // 'admin' hoặc 'student'
            }
            if (!Schema::hasColumn('users', 'balance')) {
                $table->decimal('balance', 15, 2)->default(0)->after('role'); // Số dư ví (VNĐ)
            }
        });

        // 2. Tạo bảng lưu lịch sử nạp tiền & giao dịch
        Schema::create('transactions', function (Blueprint $table) {$table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();$table->string('code')->unique(); // Mã giao dịch (VD: NAP83921)
            $table->decimal('amount', 15, 2); // Số tiền
            $table->enum('type', ['deposit', 'purchase', 'admin_adjust'])->default('deposit');$table->string('payment_method')->default('Chuyển khoản VietQR');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('note')->nullable();$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::table('users', function (Blueprint $table) {$table->dropColumn(['role', 'balance']);
        });
    }
};
