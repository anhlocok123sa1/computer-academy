<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    show: Boolean,
    transaction: Object,
    bankConfig: {
        type: Object,
        default: () => ({
            bank_id: 'ICB',
            bank_name: 'VietinBank (Ngân hàng TMCP Công thương Việt Nam)',
            account_no: '102876275109',
            account_name: 'LE LAM TAN LOC',
        })
    }
});

const emit = defineEmits(['close', 'approved']);

const copiedField = ref(null);
const isPolling = ref(false);
const isSimulating = ref(false);
const paymentSuccess = ref(false);
let pollInterval = null;

// Sinh URL VietQR chuẩn EMVCo
const qrUrl = computed(() => {
    if (!props.transaction) return '';
    const bankId = props.bankConfig?.bank_id || 'MB';
    const accNo = props.bankConfig?.account_no || '0988776655';
    const amount = props.transaction.amount || 0;
    const memo = props.transaction.code || '';
    const name = encodeURIComponent(props.bankConfig?.account_name || 'COMPUTER ACADEMY');

    return `https://img.vietqr.io/image/${bankId}-${accNo}-compact2.png?amount=${amount}&addInfo=${memo}&accountName=${name}`;
});

const copyToClipboard = (text, fieldName) => {
    if (!navigator.clipboard) {
        // Fallback
        const el = document.createElement('textarea');
        el.value = text;
        document.body.appendChild(el);
        el.select();
        document.execCommand('copy');
        document.body.removeChild(el);
    } else {
        navigator.clipboard.writeText(text);
    }
    copiedField.value = fieldName;
    setTimeout(() => {
        if (copiedField.value === fieldName) {
            copiedField.value = null;
        }
    }, 2000);
};

// Polling kiểm tra trạng thái thanh toán từ backend
const checkStatus = async () => {
    if (!props.transaction || props.transaction.status !== 'pending' || paymentSuccess.value) return;

    try {
        const res = await fetch(route('profile.deposit.status', props.transaction.id), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        });
        if (res.ok) {
            const data = await res.json();
            if (data.status === 'approved') {
                paymentSuccess.value = true;
                stopPolling();
                emit('approved', data);
                // Cập nhật lại router để reload balance & transactions
                router.reload({ only: ['auth', 'transactions'] });
            } else if (data.status === 'rejected') {
                stopPolling();
                router.reload({ only: ['auth', 'transactions'] });
            }
        }
    } catch (e) {
        // Bỏ qua lỗi kết nối khi đang polling
    }
};

const startPolling = () => {
    stopPolling();
    isPolling.value = true;
    pollInterval = setInterval(checkStatus, 3500);
};

const stopPolling = () => {
    if (pollInterval) {
        clearInterval(pollInterval);
        pollInterval = null;
    }
    isPolling.value = false;
};

// Mô phỏng thanh toán tức thì (Sandbox Webhook)
const triggerSimulation = () => {
    if (!props.transaction || isSimulating.value) return;
    isSimulating.value = true;
    router.post(route('profile.deposit.simulate', props.transaction.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            isSimulating.value = false;
            paymentSuccess.value = true;
            stopPolling();
            emit('approved', props.transaction);
        },
        onError: () => {
            isSimulating.value = false;
        }
    });
};

// Hủy đơn nạp tiền
const cancelDeposit = () => {
    if (!props.transaction) return;
    if (confirm('Bạn có chắc chắn muốn hủy yêu cầu nạp tiền này?')) {
        stopPolling();
        router.delete(route('profile.deposit.cancel', props.transaction.id), {
            preserveScroll: true,
            onSuccess: () => {
                emit('close');
            }
        });
    }
};

watch(() => props.show, (newVal) => {
    if (newVal && props.transaction && props.transaction.status === 'pending') {
        paymentSuccess.value = false;
        startPolling();
    } else {
        stopPolling();
    }
}, { immediate: true });

onUnmounted(() => {
    stopPolling();
});
</script>

<template>
    <div
        v-if="show && transaction"
        class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto"
    >
        <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl relative">
            <!-- Nút đóng -->
            <button
                @click="emit('close')"
                class="absolute top-5 right-5 w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center text-sm transition cursor-pointer"
            >
                ✕
            </button>

            <!-- TRƯỜNG HỢP: THANH TOÁN THÀNH CÔNG -->
            <div v-if="paymentSuccess || transaction.status === 'approved'" class="text-center py-6 space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-500/20 border-2 border-emerald-500 text-emerald-400 flex items-center justify-center text-3xl mx-auto animate-bounce">
                    ✓
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-white">Nạp Tiền Thành Công!</h3>
                    <p class="text-sm text-slate-400 mt-1">
                        Hệ thống đã ghi nhận thanh toán và cộng tiền vào Ví học viên của bạn.
                    </p>
                </div>
                <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-4 max-w-xs mx-auto">
                    <span class="text-xs text-slate-400 block">Số tiền đã cộng vào Ví:</span>
                    <span class="text-2xl font-extrabold text-emerald-400">
                        +{{ Number(transaction.amount).toLocaleString('vi-VN') }} đ
                    </span>
                    <span class="text-[11px] font-mono text-slate-500 block mt-1">Mã GD: {{ transaction.code }}</span>
                </div>
                <button
                    type="button"
                    @click="emit('close')"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm rounded-xl transition cursor-pointer"
                >
                    Hoàn tất & Tiếp tục học
                </button>
            </div>

            <!-- TRƯỜNG HỢP: ĐANG CHỜ THANH TOÁN QUA VIETQR -->
            <template v-else>
                <!-- Tiêu đề & Trạng thái -->
                <div class="text-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-wider">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                        Cổng Chuyển Khoản Tự Động VietQR
                    </span>
                    <h3 class="text-xl sm:text-2xl font-extrabold text-white mt-2">
                        Quét Mã QR Để Nạp Tiền
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">
                        Mở bất kỳ ứng dụng Ngân hàng nào (MB, VCB, Techcombank, MoMo...) và quét mã bên dưới.
                    </p>
                </div>

                <!-- Khung hiển thị Mã QR VietQR -->
                <div class="bg-white p-4 rounded-2xl max-w-[280px] mx-auto shadow-xl border border-slate-200 text-center">
                    <img
                        :src="qrUrl"
                        alt="Mã QR VietQR"
                        class="w-full h-auto object-contain rounded-lg"
                        loading="eager"
                    />
                    <div class="mt-2 text-[11px] font-bold text-slate-700 flex items-center justify-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        VietQR 24/7 Tự Động Điền Nội Dung
                    </div>
                </div>

                <!-- Bảng chi tiết chuyển khoản thủ công có nút Copy -->
                <div class="bg-slate-950 border border-slate-800 rounded-2xl p-4 space-y-3 text-xs">
                    <!-- Ngân hàng -->
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Ngân hàng thụ hưởng:</span>
                        <span class="font-bold text-white">{{ bankConfig.bank_name }}</span>
                    </div>

                    <!-- Chủ tài khoản -->
                    <div class="flex items-center justify-between">
                        <span class="text-slate-400">Chủ tài khoản:</span>
                        <span class="font-bold text-white uppercase">{{ bankConfig.account_name }}</span>
                    </div>

                    <!-- Số tài khoản -->
                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                        <span class="text-slate-400">Số tài khoản (STK):</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-sm text-blue-400">{{ bankConfig.account_no }}</span>
                            <button
                                type="button"
                                @click="copyToClipboard(bankConfig.account_no, 'account_no')"
                                class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 transition cursor-pointer"
                            >
                                {{ copiedField === 'account_no' ? '✓ Đã chép' : 'Sao chép' }}
                            </button>
                        </div>
                    </div>

                    <!-- Số tiền -->
                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/80">
                        <span class="text-slate-400">Số tiền chính xác:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-sm text-emerald-400">
                                {{ Number(transaction.amount).toLocaleString('vi-VN') }} đ
                            </span>
                            <button
                                type="button"
                                @click="copyToClipboard(String(transaction.amount), 'amount')"
                                class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-[11px] text-slate-300 transition cursor-pointer"
                            >
                                {{ copiedField === 'amount' ? '✓ Đã chép' : 'Sao chép' }}
                            </button>
                        </div>
                    </div>

                    <!-- Nội dung chuyển khoản -->
                    <div class="flex items-center justify-between pt-1 border-t border-slate-800/80 bg-blue-500/5 -mx-4 -mb-4 p-3 rounded-b-2xl">
                        <div>
                            <span class="text-slate-300 font-bold block">Nội dung chuyển khoản (Bắt buộc):</span>
                            <span class="text-[11px] text-slate-400">Vui lòng điền đúng để hệ thống tự cộng tiền</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-extrabold text-sm text-amber-400 bg-amber-500/10 px-2 py-1 rounded border border-amber-500/30">
                                {{ transaction.code }}
                            </span>
                            <button
                                type="button"
                                @click="copyToClipboard(transaction.code, 'memo')"
                                class="px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-500 text-[11px] font-bold text-white transition cursor-pointer"
                            >
                                {{ copiedField === 'memo' ? '✓ Đã chép' : 'Sao chép' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Thanh trạng thái chờ -->
                <div class="flex items-center justify-between text-xs text-slate-400 px-1">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                        <span>Đang chờ nhận tiền chuyển khoản...</span>
                    </div>
                    <button
                        type="button"
                        @click="checkStatus"
                        class="text-blue-400 hover:underline cursor-pointer"
                    >
                        Kiểm tra ngay ↻
                    </button>
                </div>

                <!-- Nút thao tác -->
                <div class="space-y-2 pt-2 border-t border-slate-800">
                    <!-- Nút mô phỏng thanh toán Sandbox (giúp test thực tế ngay không cần tài khoản admin) -->
                    <button
                        type="button"
                        @click="triggerSimulation"
                        :disabled="isSimulating"
                        class="w-full py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs rounded-xl shadow-md transition flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <span>⚡</span>
                        <span>{{ isSimulating ? 'Đang kích hoạt giao dịch...' : 'Mô phỏng Quét mã & Nạp thành công (Test Instant)' }}</span>
                    </button>

                    <div class="flex items-center justify-between pt-1">
                        <button
                            type="button"
                            @click="cancelDeposit"
                            class="text-xs text-rose-400 hover:underline cursor-pointer"
                        >
                            Hủy yêu cầu nạp này
                        </button>
                        <button
                            type="button"
                            @click="emit('close')"
                            class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold cursor-pointer"
                        >
                            Đóng cửa sổ
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
