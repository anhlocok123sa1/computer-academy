<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import PaymentModal from '@/Components/PaymentModal.vue';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    transactions: {
        type: Array,
        default: () => []
    },
    bankConfig: {
        type: Object,
        default: () => ({
            bank_id: 'ICB',
            bank_name: 'VietinBank (Ngân hàng TMCP Công thương Việt Nam)',
            account_no: '102876275109',
            account_name: 'LE LAM TAN LOC',
        })
    },
    activeDeposit: Object,
});

const user = usePage().props.auth.user;
const activeTab = ref('wallet'); // wallet | profile | security

// Trạng thái Modal Thanh Toán VietQR
const showPaymentModal = ref(false);
const currentPaymentTx = ref(null);

// Lọc lịch sử giao dịch
const txFilter = ref('all'); // all | deposit | purchase

const filteredTransactions = computed(() => {
    if (txFilter.value === 'all') return props.transactions;
    return props.transactions.filter(t => t.type === txFilter.value);
});

// 1. Form Nạp tiền vào ví
const depositForm = useForm({
    amount: 200000,
    payment_method: 'Chuyển khoản Ngân hàng (VietQR)',
});

const quickAmounts = [100000, 200000, 500000, 1000000, 2000000, 5000000];

const submitDeposit = () => {
    depositForm.post(route('profile.deposit'), {
        preserveScroll: true,
        onSuccess: (page) => {
            if (page.props.activeDeposit) {
                currentPaymentTx.value = page.props.activeDeposit;
                showPaymentModal.value = true;
            }
        }
    });
};

const openPaymentModal = (tx) => {
    currentPaymentTx.value = tx;
    showPaymentModal.value = true;
};

const handlePaymentApproved = (data) => {
    // Thông báo nạp tiền thành công
};

const cancelPendingDeposit = (tx) => {
    if (confirm(`Bạn có chắc chắn muốn hủy yêu cầu nạp ${Number(tx.amount).toLocaleString('vi-VN')} đ (Mã: ${tx.code})?`)) {
        router.delete(route('profile.deposit.cancel', tx.id), {
            preserveScroll: true,
        });
    }
};

// Tự động mở modal thanh toán nếu có activeDeposit từ session flash
watch(() => props.activeDeposit, (newVal) => {
    if (newVal) {
        currentPaymentTx.value = newVal;
        showPaymentModal.value = true;
    }
}, { immediate: true });

// 2. Form Cập nhật thông tin cá nhân
const profileForm = useForm({
    name: user.name,
    email: user.email,
});

const submitProfile = () => {
    profileForm.patch(route('profile.update'), {
        preserveScroll: true,
    });
};

// 3. Form Đổi mật khẩu
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const submitPassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};

const getStatusBadge = (status) => {
    const map = {
        pending: { text: 'Chờ thanh toán', class: 'bg-amber-500/15 text-amber-400 border-amber-500/30' },
        approved: { text: 'Thành công', class: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30' },
        rejected: { text: 'Đã từ chối', class: 'bg-rose-500/15 text-rose-400 border-rose-500/30' },
    };
    return map[status] || map.pending;
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head title="Tài khoản & Ví học viên - Computer Academy" />

    <AppLayout>
        <!-- BANNER TÀI KHOẢN -->
        <section class="bg-gradient-to-b from-slate-900 via-[#0a1022] to-[#060913] border-b border-slate-800/80 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-2xl font-extrabold text-white shadow-xl shadow-blue-600/25">
                            {{ user.name.charAt(0).toUpperCase() }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-2xl sm:text-3xl font-extrabold text-white">{{ user.name }}</h1>
                                <span
                                    class="text-[11px] font-bold uppercase px-2.5 py-0.5 rounded-md border"
                                    :class="user.role === 'admin' ? 'bg-rose-500/20 text-rose-400 border-rose-500/30' : 'bg-blue-500/15 text-blue-400 border-blue-500/30'"
                                >
                                    {{ user.role === 'admin' ? 'Quản trị viên' : 'Học viên' }}
                                </span>
                            </div>
                            <p class="text-slate-400 text-sm mt-1">{{ user.email }}</p>
                        </div>
                    </div>

                    <!-- Box Số dư Ví nổi bật -->
                    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl px-6 py-4 flex items-center gap-6">
                        <div>
                            <span class="text-xs text-slate-400 uppercase tracking-wider font-semibold block">Số dư Ví hiện tại</span>
                            <span class="text-2xl sm:text-3xl font-extrabold text-emerald-400">
                                {{ Number($page.props.auth.user.balance || 0).toLocaleString('vi-VN') }} đ
                            </span>
                        </div>
                        <button
                            @click="activeTab = 'wallet'"
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs sm:text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition cursor-pointer"
                        >
                            + Nạp thêm tiền
                        </button>
                    </div>
                </div>

                <!-- Thông báo trạng thái -->
                <div v-if="status === 'insufficient-balance'" class="mt-6 p-4 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300 text-sm font-medium">
                    ⚠️ Số dư trong ví của bạn không đủ để đăng ký khóa học có phí. Vui lòng nạp thêm tiền vào ví bên dưới!
                </div>
                <div v-if="status === 'deposit-created'" class="mt-6 p-4 rounded-xl bg-blue-500/15 border border-blue-500/30 text-blue-300 text-sm font-medium flex items-center justify-between">
                    <span>⚡ Đã tạo lệnh nạp tiền! Vui lòng quét mã VietQR để hoàn tất giao dịch.</span>
                    <button
                        v-if="currentPaymentTx"
                        @click="showPaymentModal = true"
                        class="px-3 py-1 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg transition"
                    >
                        Mở mã QR thanh toán
                    </button>
                </div>
                <div v-if="status === 'deposit-approved' || status === 'deposit-simulated'" class="mt-6 p-4 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 text-sm font-medium">
                    ✓ Nạp tiền thành công! Số dư ví của bạn đã được cập nhật ngay lập tức.
                </div>
                <div v-if="status === 'deposit-cancelled'" class="mt-6 p-4 rounded-xl bg-slate-800/80 border border-slate-700 text-slate-300 text-sm font-medium">
                    Đã hủy yêu cầu nạp tiền đang chờ.
                </div>
                <div v-if="status === 'profile-updated'" class="mt-6 p-4 rounded-xl bg-blue-500/15 border border-blue-500/30 text-blue-300 text-sm font-medium">
                    ✓ Đã cập nhật thông tin hồ sơ cá nhân thành công!
                </div>
            </div>
        </section>

        <!-- NỘI DUNG CHÍNH -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
            <!-- Thanh chuyển Tab -->
            <div class="flex flex-wrap gap-2 mb-8 border-b border-slate-800 pb-4">
                <button
                    @click="activeTab = 'wallet'"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer"
                    :class="activeTab === 'wallet' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25' : 'bg-slate-900 text-slate-400 hover:text-white'"
                >
                    💳 Ví điện tử & Nạp tiền VietQR
                </button>
                <button
                    @click="activeTab = 'profile'"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer"
                    :class="activeTab === 'profile' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25' : 'bg-slate-900 text-slate-400 hover:text-white'"
                >
                    👤 Thông tin cá nhân
                </button>
                <button
                    @click="activeTab = 'security'"
                    class="px-5 py-2.5 rounded-xl text-sm font-bold transition cursor-pointer"
                    :class="activeTab === 'security' ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25' : 'bg-slate-900 text-slate-400 hover:text-white'"
                >
                    🔐 Đổi mật khẩu & Bảo mật
                </button>
            </div>

            <!-- TAB 1: VÍ ĐIỆN TỬ & NẠP TIỀN -->
            <div v-if="activeTab === 'wallet'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Cột trái: Tạo lệnh nạp tiền -->
                <div class="lg:col-span-5 bg-slate-900/80 border border-slate-800 rounded-2xl p-6 space-y-6">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 text-[11px] font-bold uppercase mb-2">
                            <span>●</span> Cổng nạp tiền VietQR 24/7
                        </div>
                        <h2 class="text-lg font-bold text-white">Nạp tiền vào Ví Học Viên</h2>
                        <p class="text-xs text-slate-400 mt-1">Sử dụng số dư ví để đăng ký khóa học ngay lập tức mà không cần chờ đợi.</p>
                    </div>

                    <form @submit.prevent="submitDeposit" class="space-y-5">
                        <!-- Chọn nhanh mệnh giá -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2.5">
                                Chọn nhanh mệnh giá nạp
                            </label>
                            <div class="grid grid-cols-3 gap-2">
                                <button
                                    v-for="amt in quickAmounts"
                                    :key="amt"
                                    type="button"
                                    @click="depositForm.amount = amt"
                                    class="py-2.5 px-3 rounded-xl text-xs font-bold border transition cursor-pointer"
                                    :class="Number(depositForm.amount) === amt
                                        ? 'bg-blue-600/20 border-blue-500 text-blue-400'
                                        : 'bg-slate-950 border-slate-800 text-slate-300 hover:border-slate-700'"
                                >
                                    {{ amt.toLocaleString('vi-VN') }}đ
                                </button>
                            </div>
                        </div>

                        <!-- Nhập số tiền tùy chọn -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                                Số tiền cần nạp (VNĐ)
                            </label>
                            <input
                                type="number"
                                step="10000"
                                min="10000"
                                max="100000000"
                                required
                                v-model="depositForm.amount"
                                class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-lg font-bold text-emerald-400 focus:outline-none focus:border-blue-500"
                            />
                            <p class="text-[11px] text-slate-400 mt-1">Số tiền tối thiểu: 10.000 đ - Tối đa: 100.000.000 đ</p>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                                Phương thức thanh toán
                            </label>
                            <select
                                v-model="depositForm.payment_method"
                                class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500"
                            >
                                <option value="Chuyển khoản Ngân hàng (VietQR)">Chuyển khoản Ngân hàng (VietQR 24/7)</option>
                                <option value="Ví điện tử MoMo / ZaloPay">Ví điện tử MoMo / ZaloPay</option>
                                <option value="Thẻ ATM / Internet Banking">Thẻ ATM / Internet Banking</option>
                            </select>
                        </div>

                        <!-- Hộp thông tin tài khoản ngân hàng tiếp nhận -->
                        <div class="bg-slate-950 border border-slate-800/90 rounded-2xl p-4 text-xs space-y-2.5 text-slate-300">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                                <span class="font-bold text-blue-400 uppercase tracking-wider text-[11px]">Tài khoản thụ hưởng:</span>
                                <span class="text-[11px] text-emerald-400 font-semibold">Tự động nhận diện 24/7</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Ngân hàng:</span>
                                <strong class="text-white">{{ bankConfig.bank_name }}</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Số tài khoản:</span>
                                <strong class="text-blue-400 font-mono text-sm">{{ bankConfig.account_no }}</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400">Chủ tài khoản:</span>
                                <strong class="text-white uppercase">{{ bankConfig.account_name }}</strong>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="depositForm.processing"
                            class="w-full py-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition cursor-pointer flex items-center justify-center gap-2"
                        >
                            <span>{{ depositForm.processing ? 'Đang tạo mã QR...' : 'Tạo Mã VietQR Để Thanh Toán' }}</span>
                            <span v-if="!depositForm.processing">→</span>
                        </button>
                    </form>
                </div>

                <!-- Cột phải: Lịch sử giao dịch -->
                <div class="lg:col-span-7 bg-slate-900/80 border border-slate-800 rounded-2xl p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                        <div>
                            <h2 class="text-lg font-bold text-white">Lịch sử Giao dịch & Biến động số dư</h2>
                            <p class="text-xs text-slate-400">Tất cả lệnh nạp tiền và mua khóa học của bạn</p>
                        </div>

                        <!-- Bộ lọc giao dịch -->
                        <div class="flex rounded-lg overflow-hidden border border-slate-800 text-xs font-semibold">
                            <button
                                @click="txFilter = 'all'"
                                class="px-3 py-1.5 transition"
                                :class="txFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-slate-950 text-slate-400 hover:text-white'"
                            >Tất cả</button>
                            <button
                                @click="txFilter = 'deposit'"
                                class="px-3 py-1.5 transition"
                                :class="txFilter === 'deposit' ? 'bg-blue-600 text-white' : 'bg-slate-950 text-slate-400 hover:text-white'"
                            >Nạp tiền</button>
                            <button
                                @click="txFilter = 'purchase'"
                                class="px-3 py-1.5 transition"
                                :class="txFilter === 'purchase' ? 'bg-blue-600 text-white' : 'bg-slate-950 text-slate-400 hover:text-white'"
                            >Mua khóa học</button>
                        </div>
                    </div>

                    <div v-if="filteredTransactions.length === 0" class="text-center py-12 text-slate-400 text-sm">
                        Chưa có giao dịch nào phù hợp.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs sm:text-sm">
                            <thead>
                                <tr class="border-b border-slate-800 text-slate-400 uppercase text-[11px]">
                                    <th class="py-3 px-3">Mã GD</th>
                                    <th class="py-3 px-3">Nội dung / Thời gian</th>
                                    <th class="py-3 px-3">Số tiền</th>
                                    <th class="py-3 px-3">Trạng thái</th>
                                    <th class="py-3 px-3 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/70">
                                <tr v-for="tx in filteredTransactions" :key="tx.id" class="hover:bg-slate-800/30">
                                    <td class="py-3.5 px-3 font-mono text-xs text-blue-400 font-bold">{{ tx.code }}</td>
                                    <td class="py-3.5 px-3">
                                        <div class="font-medium text-white">{{ tx.note }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            {{ formatDate(tx.created_at) }} • {{ tx.payment_method }}
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-3 font-bold" :class="tx.type === 'purchase' ? 'text-rose-400' : 'text-emerald-400'">
                                        {{ tx.type === 'purchase' ? '-' : '+' }}{{ Number(tx.amount).toLocaleString('vi-VN') }} đ
                                    </td>
                                    <td class="py-3.5 px-3">
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold border" :class="getStatusBadge(tx.status).class">
                                            {{ getStatusBadge(tx.status).text }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-3 text-right space-x-2">
                                        <template v-if="tx.status === 'pending'">
                                            <button
                                                @click="openPaymentModal(tx)"
                                                class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-lg transition cursor-pointer"
                                                title="Mở mã VietQR để thanh toán"
                                            >
                                                📱 Quét QR
                                            </button>
                                            <button
                                                @click="cancelPendingDeposit(tx)"
                                                class="px-2 py-1.5 text-rose-400 hover:text-rose-300 text-xs font-semibold hover:underline cursor-pointer"
                                                title="Hủy đơn này"
                                            >
                                                ✕ Hủy
                                            </button>
                                        </template>
                                        <span v-else class="text-xs text-slate-500">Hoàn tất</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: THÔNG TIN CÁ NHÂN -->
            <div v-if="activeTab === 'profile'" class="max-w-2xl bg-slate-900/80 border border-slate-800 rounded-2xl p-6 sm:p-8">
                <h2 class="text-xl font-bold text-white mb-2">Cập nhật Hồ sơ Cá nhân</h2>
                <p class="text-xs sm:text-sm text-slate-400 mb-6">Chỉnh sửa họ tên hiển thị trên chứng chỉ và địa chỉ email liên hệ của bạn.</p>

                <form @submit.prevent="submitProfile" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Họ và tên</label>
                        <input
                            type="text"
                            v-model="profileForm.name"
                            required
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500"
                        />
                        <p v-if="profileForm.errors.name" class="text-xs text-rose-400 mt-1">{{ profileForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Địa chỉ Email</label>
                        <input
                            type="email"
                            v-model="profileForm.email"
                            required
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500"
                        />
                        <p v-if="profileForm.errors.email" class="text-xs text-rose-400 mt-1">{{ profileForm.errors.email }}</p>
                    </div>

                    <button
                        type="submit"
                        :disabled="profileForm.processing"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/25 transition cursor-pointer"
                    >
                        Lưu thay đổi
                    </button>
                </form>
            </div>

            <!-- TAB 3: ĐỔI MẬT KHẨU -->
            <div v-if="activeTab === 'security'" class="max-w-2xl bg-slate-900/80 border border-slate-800 rounded-2xl p-6 sm:p-8">
                <h2 class="text-xl font-bold text-white mb-2">Đổi mật khẩu bảo mật</h2>
                <p class="text-xs sm:text-sm text-slate-400 mb-6">Sử dụng mật khẩu tối thiểu 8 ký tự để bảo vệ tài khoản học viên của bạn.</p>

                <form @submit.prevent="submitPassword" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Mật khẩu hiện tại</label>
                        <input
                            type="password"
                            v-model="passwordForm.current_password"
                            required
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500"
                        />
                        <p v-if="passwordForm.errors.current_password" class="text-xs text-rose-400 mt-1">{{ passwordForm.errors.current_password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Mật khẩu mới</label>
                        <input
                            type="password"
                            v-model="passwordForm.password"
                            required
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500"
                        />
                        <p v-if="passwordForm.errors.password" class="text-xs text-rose-400 mt-1">{{ passwordForm.errors.password }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nhập lại mật khẩu mới</label>
                        <input
                            type="password"
                            v-model="passwordForm.password_confirmation"
                            required
                            class="w-full px-4 py-3 bg-slate-950 border border-slate-800 rounded-xl text-sm text-white focus:outline-none focus:border-blue-500"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="passwordForm.processing"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-600/25 transition cursor-pointer"
                    >
                        Cập nhật mật khẩu
                    </button>
                </form>
            </div>
        </section>

        <!-- MODAL THANH TOÁN VIETQR NẠP TIỀN -->
        <PaymentModal
            :show="showPaymentModal"
            :transaction="currentPaymentTx"
            :bank-config="bankConfig"
            @close="showPaymentModal = false"
            @approved="handlePaymentApproved"
        />
    </AppLayout>
</template>
