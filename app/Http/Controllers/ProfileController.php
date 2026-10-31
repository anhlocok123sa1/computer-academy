<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    // Hiển thị trang Hồ sơ cá nhân & Ví nạp tiền
    public function edit(Request $request): Response
    {
        $transactions = Transaction::where('user_id', $request->user()->id)
            ->latest()
            ->take(30)
            ->get();

        $bankConfig = config('services.vietqr', [
            'bank_id' => 'ICB',
            'bank_name' => 'VietinBank (Ngân hàng TMCP Công thương Việt Nam)',
            'account_no' => '102876275109',
            'account_name' => 'LE LAM TAN LOC',
        ]);

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'transactions' => $transactions,
            'bankConfig' => $bankConfig,
            'activeDeposit' => session('activeDeposit'),
        ]);
    }

    // Học viên tạo đơn nạp tiền vào ví (sinh mã giao dịch VietQR)
    public function deposit(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:10000', 'max:100000000'],
            'payment_method' => ['required', 'string'],
        ]);

        // Tạo mã giao dịch duy nhất
        $code = 'NAP' . strtoupper(Str::random(6));

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'code' => $code,
            'amount' => (float) $request->amount,
            'type' => 'deposit',
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'note' => 'Nạp tiền vào ví qua ' . $request->payment_method,
        ]);

        return Redirect::route('profile.edit')
            ->with('status', 'deposit-created')
            ->with('activeDeposit', $transaction);
    }

    // Kiểm tra trạng thái nạp tiền theo thời gian thực (Polling API)
    public function depositStatus(Request $request, Transaction $transaction): JsonResponse
    {
        if ($transaction->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền xem thông tin giao dịch này.');
        }

        $transaction->refresh();
        $user = $request->user()->fresh();

        return response()->json([
            'status' => $transaction->status,
            'code' => $transaction->code,
            'amount' => (float) $transaction->amount,
            'balance' => (float) $user->balance,
        ]);
    }

    // Hủy yêu cầu nạp tiền đang chờ duyệt
    public function cancelDeposit(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        if ($transaction->status === 'pending') {
            $transaction->delete();
        }

        return Redirect::route('profile.edit')->with('status', 'deposit-cancelled');
    }

    // Mô phỏng thanh toán thành công (Sandbox / Demo Instant Webhook)
    public function simulateDeposit(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            abort(403);
        }

        if ($transaction->status === 'pending') {
            DB::transaction(function () use ($transaction) {
                $tx = Transaction::lockForUpdate()->find($transaction->id);
                if ($tx && $tx->status === 'pending') {
                    $tx->update([
                        'status' => 'approved',
                        'note' => 'Thanh toán thành công qua VietQR tự động',
                    ]);

                    $user = User::lockForUpdate()->find($tx->user_id);
                    $user->increment('balance', $tx->amount);
                }
            });
        }

        return Redirect::route('profile.edit')->with('status', 'deposit-approved');
    }

    // Cập nhật thông tin cá nhân
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    // Xóa tài khoản
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
