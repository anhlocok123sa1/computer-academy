<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import Ckeditor from '@/Components/Ckeditor.vue';

const props = defineProps({
    course: Object,
    currentLesson: Object,
    completedLessonIds: {
        type: Array,
        default: () => []
    },
    initialNote: {
        type: String,
        default: ''
    }
});

// Trạng thái Drawer Nội dung khóa học (Ẩn lúc học, mở bằng button)
const isCurriculumOpen = ref(false);
const curriculumSearch = ref('');

// Trạng thái Ghi chú cá nhân (Panel bên phải)
const isNotesOpen = ref(true);
const personalNote = ref(props.initialNote || '');
const saveStatus = ref('saved'); // 'saved' | 'saving' | 'unsaved'
let saveTimeout = null;

// Modal phóng to ảnh
const isImageModalOpen = ref(false);

const hasVideo = computed(() => Boolean(props.currentLesson?.video_url && props.currentLesson.video_url.trim()));
const hasImage = computed(() => Boolean(props.currentLesson?.image_url && props.currentLesson.image_url.trim()));
const hasAttachment = computed(() => Boolean(props.currentLesson?.attachment_url && props.currentLesson.attachment_url.trim()));
const hasContent = computed(() => Boolean(props.currentLesson?.content && props.currentLesson.content.trim()));
const hasExercises = computed(() => Boolean(props.currentLesson?.exercises && props.currentLesson.exercises.length > 0));

// Tương tác Bài tập & Trắc nghiệm củng cố
const userAnswers = ref({});
const showSolutions = ref({});
const checkedSteps = ref({});

const quizExercises = computed(() => {
    return (props.currentLesson?.exercises || []).filter(e => e.type === 'quiz');
});

const practicalExercises = computed(() => {
    return (props.currentLesson?.exercises || []).filter(e => e.type === 'practical');
});

const quizAnsweredCount = computed(() => {
    return quizExercises.value.filter(q => userAnswers.value[q.id] !== undefined).length;
});

const quizCorrectCount = computed(() => {
    return quizExercises.value.filter(q => userAnswers.value[q.id] === q.correct_index).length;
});

const selectAnswer = (exerciseId, optionIndex) => {
    userAnswers.value[exerciseId] = optionIndex;
};

const resetQuiz = () => {
    userAnswers.value = {};
};

const toggleSolution = (exerciseId) => {
    showSolutions.value[exerciseId] = !showSolutions.value[exerciseId];
};

const parseRequirements = (reqStr) => {
    if (!reqStr) return [];
    return reqStr
        .split('\n')
        .map(s => s.trim())
        .filter(s => s.length > 0)
        .map(s => s.replace(/^[•\-\*]\s*/, ''));
};

const isVideoFile = computed(() => {
    if (!props.currentLesson?.video_url) return false;
    const url = props.currentLesson.video_url.toLowerCase();
    return url.endsWith('.mp4') || url.endsWith('.webm') || url.endsWith('.ogg') || url.startsWith('/courses/') || url.startsWith('/storage/');
});

// Trải phẳng toàn bộ bài học để làm nút Bài Trước / Bài Tiếp Theo
const allLessons = computed(() => {
    if (!props.course.sections) return [];
    return props.course.sections.flatMap(sec => sec.lessons || []);
});

const currentIndex = computed(() => {
    return allLessons.value.findIndex(l => l.id === props.currentLesson.id);
});

const prevLesson = computed(() => {
    return currentIndex.value > 0 ? allLessons.value[currentIndex.value - 1] : null;
});

const nextLesson = computed(() => {
    return currentIndex.value < allLessons.value.length - 1 ? allLessons.value[currentIndex.value + 1] : null;
});

// Lọc bài học trong Drawer theo từ khóa tìm kiếm
const filteredSections = computed(() => {
    if (!props.course.sections) return [];
    if (!curriculumSearch.value.trim()) return props.course.sections;
    const q = curriculumSearch.value.toLowerCase().trim();
    return props.course.sections.map(section => {
        const matchingLessons = (section.lessons || []).filter(l =>
            l.title.toLowerCase().includes(q)
        );
        return {
            ...section,
            lessons: matchingLessons
        };
    }).filter(s => s.lessons.length > 0);
});

// Lưu trạng thái hoàn thành vào Database
const toggleComplete = (lessonId) => {
    router.post(route('lessons.progress', lessonId), {}, {
        preserveScroll: true
    });
};

const progressPercent = computed(() => {
    if (allLessons.value.length === 0) return 0;
    const completedInCourse = allLessons.value.filter(l => props.completedLessonIds.includes(l.id)).length;
    return Math.round((completedInCourse / allLessons.value.length) * 100);
});

// Helper lấy icon bài học
const getLessonIcon = (lesson) => {
    if (props.completedLessonIds.includes(lesson.id)) return '✓';
    const v = Boolean(lesson.video_url?.trim());
    const img = Boolean(lesson.image_url?.trim());
    const doc = Boolean(lesson.content?.trim()) || lesson.type === 'doc';

    if (v && (img || doc)) return '🎬';
    if (v) return '▶';
    if (img) return '🖼️';
    return '📄';
};

// Quản lý Ghi chú cá nhân (Auto-save + LocalStorage cache + Server API)
const getStorageKey = () => `lesson_note_${props.currentLesson?.id}`;

onMounted(() => {
    const cached = localStorage.getItem(getStorageKey());
    if (!props.initialNote && cached) {
        personalNote.value = cached;
    }

    window.addEventListener('keydown', handleGlobalKeydown);
});

onBeforeUnmount(() => {
    window.removeEventListener('keydown', handleGlobalKeydown);
    if (saveTimeout) clearTimeout(saveTimeout);
});

const handleGlobalKeydown = (e) => {
    if (e.key === 'Escape') {
        if (isCurriculumOpen.value) isCurriculumOpen.value = false;
        if (isImageModalOpen.value) isImageModalOpen.value = false;
    }
};

const saveNoteImmediately = async () => {
    if (saveTimeout) clearTimeout(saveTimeout);
    saveStatus.value = 'saving';
    localStorage.setItem(getStorageKey(), personalNote.value || '');

    try {
        if (window.axios) {
            await window.axios.post(route('lessons.note', props.currentLesson.id), {
                content: personalNote.value || ''
            });
        }
        saveStatus.value = 'saved';
    } catch (err) {
        console.warn('Lỗi khi đồng bộ ghi chú với máy chủ, dữ liệu đã được lưu an toàn tại trình duyệt.', err);
        saveStatus.value = 'saved';
    }
};

watch(() => props.currentLesson?.id, () => {
    userAnswers.value = {};
    showSolutions.value = {};
    checkedSteps.value = {};
});

watch(personalNote, (newVal) => {
    saveStatus.value = 'unsaved';
    localStorage.setItem(getStorageKey(), newVal || '');

    if (saveTimeout) clearTimeout(saveTimeout);
    saveTimeout = setTimeout(() => {
        saveNoteImmediately();
    }, 1200);
});

const clearNote = () => {
    if (confirm('Bạn có chắc chắn muốn xóa toàn bộ ghi chú của bài học này?')) {
        personalNote.value = '';
        saveNoteImmediately();
    }
};
</script>

<template>
    <Head :title="currentLesson.title + ' - ' + course.title" />

    <div class="min-h-screen bg-[#050811] text-slate-100 flex flex-col">
        <!-- TOP BAR PHÒNG HỌC -->
        <header class="h-16 bg-[#0a0f1d] border-b border-slate-800 px-4 sm:px-6 flex items-center justify-between shrink-0 sticky top-0 z-30">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <Link
                    :href="route('courses.show', course.slug)"
                    class="flex items-center gap-2 text-xs sm:text-sm font-medium text-slate-300 hover:text-white bg-slate-800/80 hover:bg-slate-800 px-3.5 py-2 rounded-xl border border-slate-700 transition shrink-0 cursor-pointer"
                >
                    <span>&larr; Khóa học</span>
                </Link>

                <div class="hidden sm:block h-5 w-px bg-slate-800"></div>

                <!-- NÚT MỞ NỘI DUNG KHÓA HỌC TRONG LÚC ĐANG HỌC -->
                <button
                    @click="isCurriculumOpen = true"
                    class="flex items-center gap-2 text-xs sm:text-sm font-semibold text-blue-400 hover:text-white bg-blue-600/15 hover:bg-blue-600/30 border border-blue-500/30 px-3.5 py-2 rounded-xl transition shrink-0 cursor-pointer shadow-sm shadow-blue-500/10"
                    title="Mở danh sách toàn bộ bài học của khóa"
                >
                    <span class="text-base leading-none">📚</span>
                    <span class="font-bold">Nội dung khóa học</span>
                    <span class="px-1.5 py-0.5 rounded bg-blue-500/20 text-[11px] font-mono font-bold text-blue-300">
                        {{ completedLessonIds.length }}/{{ allLessons.length }}
                    </span>
                </button>

                <h1 class="text-sm sm:text-base font-bold text-white truncate hidden xl:block">
                    {{ course.title }}
                </h1>
            </div>

            <!-- TIẾN ĐỘ & TOGGLE GHI CHÚ / THEME -->
            <div class="flex items-center gap-2.5 sm:gap-3 shrink-0">
                <div class="text-xs text-slate-400 hidden lg:block">
                    Tiến độ: <span class="text-emerald-400 font-bold">{{ completedLessonIds.length }}/{{ allLessons.length }}</span> bài ({{ progressPercent }}%)
                </div>
                <div class="w-24 h-2 bg-slate-800 rounded-full overflow-hidden hidden md:block">
                    <div class="h-full bg-emerald-500 transition-all duration-300" :style="{ width: progressPercent + '%' }"></div>
                </div>

                <!-- Nút bật/tắt khung Ghi chú bên phải -->
                <button
                    @click="isNotesOpen = !isNotesOpen"
                    class="flex items-center gap-1.5 text-xs font-semibold px-3 py-2 rounded-xl border transition cursor-pointer"
                    :class="isNotesOpen
                        ? 'bg-amber-500/15 text-amber-300 border-amber-500/30 hover:bg-amber-500/25'
                        : 'bg-slate-800/80 text-slate-300 border-slate-700 hover:bg-slate-800'"
                    :title="isNotesOpen ? 'Thu gọn khung ghi chú cá nhân' : 'Mở khung ghi chú cá nhân'"
                >
                    <span>📝</span>
                    <span class="hidden sm:inline">{{ isNotesOpen ? 'Ẩn ghi chú' : 'Ghi chú' }}</span>
                </button>

                <ThemeToggle />
            </div>
        </header>

        <!-- KHU VỰC CHÍNH: NỘI DUNG BÊN TRÁI + GHI CHÚ CKEDITOR BÊN PHẢI -->
        <div class="flex-1 flex flex-col lg:flex-row relative items-start">
            <!-- BÊN TRÁI: Nội dung bài học, Video & Lý thuyết -->
            <div class="flex-1 min-w-0 w-full">

                <!-- 1. PHẦN VIDEO (Nếu bài học có video) -->
                <div v-if="hasVideo" class="bg-black border-b border-slate-800">
                    <div class="max-w-5xl mx-auto aspect-video w-full flex items-center justify-center bg-black">
                        <video
                            v-if="isVideoFile"
                            :src="currentLesson.video_url"
                            controls
                            playsinline
                            controlsList="nodownload"
                            class="w-full h-full object-contain bg-black"
                        >
                            Trình duyệt của bạn không hỗ trợ phát video HTML5.
                        </video>
                        <iframe
                            v-else
                            :src="currentLesson.video_url"
                            class="w-full h-full"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                </div>

                <!-- 2. NẾU BÀI HỌC KHÔNG CÓ VIDEO NHƯNG CÓ HÌNH ẢNH HOẶC TÀI LIỆU -->
                <div v-else-if="hasImage && !hasVideo" class="border-b border-slate-800 bg-slate-950/60 py-8 px-4 sm:px-8">
                    <div class="max-w-4xl mx-auto">
                        <div class="flex items-center justify-between mb-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-400 text-xs font-semibold">
                                <span>🖼️</span>
                                <span>Sơ đồ & Hình ảnh minh họa bài học</span>
                            </span>
                            <button
                                @click="isImageModalOpen = true"
                                class="text-xs text-slate-400 hover:text-white flex items-center gap-1 cursor-pointer"
                            >
                                <span>🔍</span>
                                <span>Xem ảnh gốc phóng to</span>
                            </button>
                        </div>
                        <div class="rounded-2xl overflow-hidden border border-slate-800 bg-slate-900/90 shadow-2xl p-2 cursor-pointer" @click="isImageModalOpen = true">
                            <img :src="currentLesson.image_url" :alt="currentLesson.title" class="w-full max-h-[500px] object-contain rounded-xl mx-auto" />
                        </div>
                    </div>
                </div>

                <!-- THANH ĐIỀU KHIỂN BÀI HỌC & TIÊU ĐỀ -->
                <div class="max-w-5xl mx-auto p-6 sm:p-8 space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-semibold text-blue-400 uppercase tracking-wider">
                                    Bài {{ currentIndex + 1 }} / {{ allLessons.length }}
                                </span>
                                <span class="text-slate-600">•</span>
                                <span class="text-xs text-slate-400 font-medium">
                                    ⏱️ {{ currentLesson.duration }} phút
                                </span>
                                <!-- Badge hiển thị định dạng -->
                                <div class="flex items-center gap-1 ml-2">
                                    <span v-if="hasVideo" class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-500/15 text-blue-400 border border-blue-500/30">Video</span>
                                    <span v-if="hasImage" class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">Hình ảnh</span>
                                    <span v-if="hasAttachment" class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">File đính kèm</span>
                                    <span v-if="hasContent" class="px-2 py-0.5 rounded text-[10px] font-bold bg-violet-500/15 text-violet-400 border border-violet-500/30">Bài viết</span>
                                </div>
                            </div>

                            <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-2">
                                {{ currentLesson.title }}
                            </h2>
                        </div>

                        <!-- Nút Đánh dấu hoàn thành, Chuyển bài & Mở danh sách bài -->
                        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                            <!-- Nút mở Drawer danh sách bài -->
                            <button
                                @click="isCurriculumOpen = true"
                                class="px-3.5 py-2.5 bg-slate-800/80 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition cursor-pointer flex items-center gap-1.5"
                                title="Danh sách các bài học trong khóa"
                            >
                                <span>📚</span>
                                <span>Bài học</span>
                            </button>

                            <button
                                @click="toggleComplete(currentLesson.id)"
                                class="px-4 py-2.5 rounded-xl text-xs font-semibold border transition cursor-pointer flex items-center gap-1.5"
                                :class="completedLessonIds.includes(currentLesson.id)
                                    ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-400'
                                    : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-slate-600'"
                            >
                                <span>{{ completedLessonIds.includes(currentLesson.id) ? '✓' : '○' }}</span>
                                <span>{{ completedLessonIds.includes(currentLesson.id) ? 'Đã hoàn thành' : 'Đánh dấu hoàn thành' }}</span>
                            </button>

                            <Link
                                v-if="prevLesson"
                                :href="route('courses.learn', [course.slug, prevLesson.slug])"
                                class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded-xl transition cursor-pointer"
                            >
                                &larr; Bài trước
                            </Link>

                            <Link
                                v-if="nextLesson"
                                :href="route('courses.learn', [course.slug, nextLesson.slug])"
                                class="px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-blue-600/25 transition cursor-pointer"
                            >
                                Bài tiếp theo &rarr;
                            </Link>
                        </div>
                    </div>

                    <!-- THẺ TÀI LIỆU ĐÍNH KÈM (Nếu có attachment_url) -->
                    <div v-if="hasAttachment" class="attachment-card rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border transition-all duration-200 shadow-sm">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <div class="attachment-icon-wrapper w-11 h-11 rounded-xl flex items-center justify-center text-xl shrink-0">
                                📎
                            </div>
                            <div class="min-w-0">
                                <h4 class="attachment-title text-sm sm:text-base font-bold">Tài liệu & Tệp tin đính kèm bài học</h4>
                                <p class="attachment-path text-xs truncate max-w-md mt-0.5">{{ currentLesson.attachment_url }}</p>
                            </div>
                        </div>
                        <a
                            :href="currentLesson.attachment_url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-xl shadow transition cursor-pointer shrink-0 flex items-center justify-center gap-2"
                        >
                            <span>Tải xuống / Mở tài liệu</span>
                            <span>&nearr;</span>
                        </a>
                    </div>

                    <!-- HÌNH ẢNH MINH HỌA (Nếu bài có cả video và ảnh) -->
                    <div v-if="hasImage && hasVideo" class="bg-slate-900/60 border border-slate-800 rounded-2xl p-4 sm:p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                                <span>🖼️</span>
                                <span>Sơ đồ / Hình ảnh minh họa đi kèm</span>
                            </h4>
                            <button
                                @click="isImageModalOpen = true"
                                class="text-xs text-blue-400 hover:underline cursor-pointer"
                            >
                                Xem phóng to 🔍
                            </button>
                        </div>
                        <div class="rounded-xl overflow-hidden bg-slate-950 p-2 cursor-pointer border border-slate-800" @click="isImageModalOpen = true">
                            <img :src="currentLesson.image_url" :alt="currentLesson.title" class="max-h-80 w-auto mx-auto object-contain rounded-lg" />
                        </div>
                    </div>

                    <!-- KHU VỰC NỘI DUNG BÀI HỌC & LÝ THUYẾT (Hiển thị trực tiếp) -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                            <h3 class="text-base font-bold text-white flex items-center gap-2">
                                <span>📖</span>
                                <span>Nội dung bài học & Lý thuyết chuyên sâu</span>
                            </h3>
                            <span class="text-xs text-slate-400 hidden sm:inline">Tài liệu học tập chính thức</span>
                        </div>

                        <div
                            v-if="hasContent"
                            class="lesson-content-box prose prose-invert prose-slate max-w-none bg-[#0a0f1d] border border-slate-800/80 rounded-2xl p-6 sm:p-10 shadow-xl"
                            v-html="currentLesson.content"
                        ></div>

                        <div v-else class="text-center py-12 bg-slate-900/30 border border-slate-800 rounded-2xl text-slate-500">
                            <div class="text-3xl mb-2">📄</div>
                            <p class="text-sm">Bài học này chưa có nội dung văn bản mở rộng.</p>
                        </div>
                    </div>

                    <!-- KHU VỰC BÀI TẬP RIÊNG BIỆT DÀNH CHO HỌC VIÊN -->
                    <div v-if="hasExercises" class="mt-12 pt-8 border-t border-slate-800 space-y-6">
                        <!-- Header Bài Tập -->
                        <div class="exercise-header-card flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 sm:p-6 rounded-2xl transition-colors duration-200">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <span class="text-2xl sm:text-3xl">🎯</span>
                                    <div>
                                        <h3 class="exercise-header-title text-base sm:text-lg font-extrabold flex items-center gap-2">
                                            <span>Bài tập & Câu hỏi củng cố kiến thức</span>
                                            <span class="exercise-badge text-xs px-2.5 py-0.5 rounded-full font-mono font-bold">
                                                {{ currentLesson.exercises.length }} bài
                                            </span>
                                        </h3>
                                        <p class="exercise-header-desc text-xs mt-0.5">
                                            Luyện tập trực tiếp để đánh giá mức độ hiểu sâu kiến thức và nguyên lý vừa học.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Điểm số / Tiến độ trắc nghiệm -->
                            <div v-if="quizExercises.length > 0" class="flex items-center gap-3 shrink-0">
                                <div class="text-right">
                                    <div class="text-xs font-bold exercise-stat-score">
                                        Đúng: <span class="text-emerald-500 font-extrabold">{{ quizCorrectCount }}</span> / {{ quizExercises.length }} câu
                                    </div>
                                    <div class="text-[11px] exercise-stat-sub">
                                        Đã làm: {{ quizAnsweredCount }}/{{ quizExercises.length }}
                                    </div>
                                </div>
                                <button
                                    v-if="quizAnsweredCount > 0"
                                    @click="resetQuiz"
                                    class="px-2.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-lg border border-slate-700 transition cursor-pointer"
                                    title="Làm lại bài trắc nghiệm"
                                >
                                    🔄 Làm lại
                                </button>
                            </div>
                        </div>

                        <!-- 1. CÂU HỎI TRẮC NGHIỆM -->
                        <div v-if="quizExercises.length > 0" class="space-y-4">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-blue-400 flex items-center gap-2">
                                <span>📝</span>
                                <span>Phần 1: Câu hỏi trắc nghiệm kiểm tra nhanh ({{ quizExercises.length }} câu)</span>
                            </h4>

                            <div
                                v-for="(q, qIdx) in quizExercises"
                                :key="q.id || qIdx"
                                class="exercise-card p-5 sm:p-6 rounded-2xl space-y-4 transition-all duration-200"
                            >
                                <div class="flex items-start gap-3">
                                    <span class="exercise-q-number w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold shrink-0 mt-0.5 font-mono">
                                        {{ qIdx + 1 }}
                                    </span>
                                    <h5 class="exercise-q-title text-sm sm:text-base font-bold leading-relaxed">
                                        {{ q.question }}
                                    </h5>
                                </div>

                                <!-- Danh sách các phương án A, B, C, D -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                    <button
                                        v-for="(opt, optIdx) in q.options"
                                        :key="optIdx"
                                        type="button"
                                        @click="selectAnswer(q.id, optIdx)"
                                        class="quiz-option-btn p-3.5 rounded-xl border text-left text-xs sm:text-sm font-medium transition cursor-pointer flex items-center gap-3"
                                        :class="{
                                            'quiz-opt-unanswered': userAnswers[q.id] === undefined,
                                            'quiz-opt-correct': userAnswers[q.id] !== undefined && optIdx === q.correct_index,
                                            'quiz-opt-wrong': userAnswers[q.id] === optIdx && optIdx !== q.correct_index,
                                            'quiz-opt-dimmed': userAnswers[q.id] !== undefined && optIdx !== q.correct_index && userAnswers[q.id] !== optIdx
                                        }"
                                    >
                                        <span
                                            class="quiz-option-badge w-6 h-6 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 transition"
                                            :class="{
                                                'badge-unanswered': userAnswers[q.id] === undefined,
                                                'badge-correct': userAnswers[q.id] !== undefined && optIdx === q.correct_index,
                                                'badge-wrong': userAnswers[q.id] === optIdx && optIdx !== q.correct_index,
                                                'badge-dimmed': userAnswers[q.id] !== undefined && optIdx !== q.correct_index && userAnswers[q.id] !== optIdx
                                            }"
                                        >
                                            <span v-if="userAnswers[q.id] !== undefined && optIdx === q.correct_index">✓</span>
                                            <span v-else-if="userAnswers[q.id] === optIdx && optIdx !== q.correct_index">✕</span>
                                            <span v-else>{{ String.fromCharCode(65 + optIdx) }}</span>
                                        </span>
                                        <span class="flex-1 leading-snug">{{ opt }}</span>
                                    </button>
                                </div>

                                <!-- Kết quả & Giải thích chi tiết khi đã trả lời -->
                                <div
                                    v-if="userAnswers[q.id] !== undefined"
                                    class="p-3.5 rounded-xl border text-xs leading-relaxed"
                                    :class="userAnswers[q.id] === q.correct_index ? 'exercise-feedback-correct' : 'exercise-feedback-incorrect'"
                                >
                                    <div class="flex items-center gap-2 font-bold mb-1">
                                        <span v-if="userAnswers[q.id] === q.correct_index">🎉 Chính xác!</span>
                                        <span v-else>⚠️ Chưa chính xác. Đáp án đúng là: {{ String.fromCharCode(65 + q.correct_index) }}</span>
                                    </div>
                                    <p v-if="q.explanation">
                                        💡 <strong>Giải thích:</strong> {{ q.explanation }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- 2. BÀI TẬP THỰC HÀNH / TÌNH HUỐNG -->
                        <div v-if="practicalExercises.length > 0" class="space-y-4 pt-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-2">
                                <span>🛠️</span>
                                <span>Phần 2: Bài tập thực hành & Tình huống thực tế ({{ practicalExercises.length }} bài)</span>
                            </h4>

                            <div
                                v-for="(p, pIdx) in practicalExercises"
                                :key="p.id || pIdx"
                                class="exercise-card p-5 sm:p-6 rounded-2xl space-y-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <span class="exercise-practical-badge w-8 h-8 rounded-xl flex items-center justify-center text-sm font-bold shrink-0">
                                            🛠️
                                        </span>
                                        <div>
                                            <h5 class="exercise-practical-title text-base font-bold leading-tight">
                                                {{ p.title }}
                                            </h5>
                                            <p v-if="p.description" class="exercise-practical-desc text-xs mt-1.5 leading-relaxed">
                                                {{ p.description }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Checklist các bước yêu cầu -->
                                <div v-if="p.requirements" class="exercise-checklist-box p-4 rounded-xl space-y-2">
                                    <div class="text-xs font-bold uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <span>📋</span>
                                        <span>Các bước thực hiện (Tích chọn khi đã làm):</span>
                                    </div>
                                    <div
                                        v-for="(step, sIdx) in parseRequirements(p.requirements)"
                                        :key="sIdx"
                                        class="flex items-start gap-2.5 text-xs cursor-pointer"
                                        @click="checkedSteps[`${p.id}_${sIdx}`] = !checkedSteps[`${p.id}_${sIdx}`]"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="checkedSteps[`${p.id}_${sIdx}`]"
                                            class="mt-0.5 rounded focus:ring-0 cursor-pointer text-emerald-500"
                                            @click.stop
                                            @change="checkedSteps[`${p.id}_${sIdx}`] = $event.target.checked"
                                        />
                                        <span :class="{ 'line-through opacity-50': checkedSteps[`${p.id}_${sIdx}`] }">
                                            {{ step }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Nút xem hướng dẫn giải / Đáp án mẫu -->
                                <div v-if="p.solution">
                                    <button
                                        type="button"
                                        @click="toggleSolution(p.id)"
                                        class="text-xs font-semibold text-blue-400 hover:text-blue-300 flex items-center gap-1.5 cursor-pointer"
                                    >
                                        <span>{{ showSolutions[p.id] ? '▲ Thu gọn hướng dẫn' : '▼ Xem gợi ý & Hướng dẫn giải' }}</span>
                                    </button>

                                    <div
                                        v-if="showSolutions[p.id]"
                                        class="exercise-solution-box mt-3 p-4 rounded-xl text-xs leading-relaxed space-y-1.5"
                                    >
                                        <div class="font-bold flex items-center gap-1.5">
                                            <span>💡 Hướng dẫn giải & Đáp án mẫu:</span>
                                        </div>
                                        <p class="whitespace-pre-line">{{ p.solution }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BÊN PHẢI: KHUNG GHI CHÚ CÁ NHÂN (STICKY Ở ĐẦU MÀN HÌNH - SỬ DỤNG CKEDITOR) -->
            <aside
                v-show="isNotesOpen"
                class="w-full lg:w-[420px] xl:w-[460px] bg-[#0a0f1d] border-t lg:border-t-0 lg:border-l border-slate-800 flex flex-col shrink-0 lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] lg:self-start z-20 overflow-hidden shadow-2xl transition-all duration-200"
            >
                <!-- Header Khung Ghi Chú -->
                <div class="p-4 border-b border-slate-800 flex items-center justify-between bg-[#0a0f1d] shrink-0 sticky top-0 z-10">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-lg">📝</span>
                        <div class="min-w-0">
                            <h3 class="font-bold text-sm text-white flex items-center gap-2 truncate">
                                <span>Ghi chú cá nhân</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-400 border border-blue-500/30 shrink-0">
                                    Bài {{ currentIndex + 1 }}
                                </span>
                            </h3>
                            <p class="text-[11px] text-slate-400 truncate">
                                {{ currentLesson.title }}
                            </p>
                        </div>
                    </div>

                    <!-- Trạng thái lưu & Nút lưu ngay -->
                    <div class="flex items-center gap-2 shrink-0">
                        <span
                            v-if="saveStatus === 'saving'"
                            class="text-[11px] text-amber-400 flex items-center gap-1 font-semibold animate-pulse"
                        >
                            <span>⏳</span>
                            <span class="hidden sm:inline">Đang lưu...</span>
                        </span>
                        <span
                            v-else-if="saveStatus === 'saved'"
                            class="text-[11px] text-emerald-400 flex items-center gap-1 font-semibold"
                        >
                            <span>✓</span>
                            <span class="hidden sm:inline">Đã lưu</span>
                        </span>
                        <span
                            v-else-if="saveStatus === 'unsaved'"
                            class="text-[11px] text-slate-400 flex items-center gap-1"
                        >
                            <span>○</span>
                            <span class="hidden sm:inline">Chưa lưu</span>
                        </span>

                        <button
                            @click="saveNoteImmediately"
                            class="px-2.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-semibold transition cursor-pointer flex items-center gap-1 shadow-sm"
                            title="Lưu ghi chú vào tài khoản ngay"
                        >
                            <span>💾</span>
                            <span>Lưu</span>
                        </button>

                        <button
                            @click="isNotesOpen = false"
                            class="lg:hidden text-slate-400 hover:text-white p-1 rounded-lg"
                            title="Đóng ghi chú"
                        >
                            ✕
                        </button>
                    </div>
                </div>

                <!-- Thân Khung Ghi Chú: CKEditor -->
                <div class="flex-1 p-4 overflow-y-auto flex flex-col space-y-3">
                    <div class="flex-1 min-h-[360px]">
                        <Ckeditor
                            v-model="personalNote"
                            :placeholder="'Nhập ghi chú cá nhân, mẹo nhớ, câu lệnh hoặc bài tập của ' + currentLesson.title + '...'"
                            min-height="calc(100vh - 270px)"
                        />
                    </div>

                    <!-- Footer Khung Ghi Chú -->
                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-800/80 shrink-0">
                        <span class="flex items-center gap-1">
                            <span>💡</span>
                            <span>Tự động lưu theo từng bài học</span>
                        </span>
                        <button
                            v-if="personalNote && personalNote.trim()"
                            @click="clearNote"
                            class="text-rose-400 hover:text-rose-300 hover:underline cursor-pointer"
                        >
                            Xóa trắng
                        </button>
                    </div>
                </div>
            </aside>
        </div>

        <!-- DRAWER NỘI DUNG KHÓA HỌC (Ẩn trong lúc học, mở bằng button) -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-opacity duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isCurriculumOpen"
                    class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex justify-start"
                    @click.self="isCurriculumOpen = false"
                >
                    <Transition
                        enter-active-class="transform transition duration-300 ease-out"
                        enter-from-class="-translate-x-full"
                        enter-to-class="translate-x-0"
                        leave-active-class="transform transition duration-200 ease-in"
                        leave-from-class="translate-x-0"
                        leave-to-class="-translate-x-full"
                    >
                        <div
                            v-if="isCurriculumOpen"
                            class="w-full max-w-md bg-[#0a0f1d] border-r border-slate-800 h-full flex flex-col shadow-2xl overflow-hidden"
                        >
                            <!-- Drawer Header -->
                            <div class="p-4 border-b border-slate-800 flex items-center justify-between bg-[#0a0f1d] shrink-0">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xl">📚</span>
                                        <h3 class="font-bold text-base text-white">Nội dung khóa học</h3>
                                    </div>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        Tiến độ: <span class="text-emerald-400 font-bold">{{ completedLessonIds.length }}/{{ allLessons.length }}</span> bài ({{ progressPercent }}%)
                                    </p>
                                </div>
                                <button
                                    @click="isCurriculumOpen = false"
                                    class="text-slate-400 hover:text-white px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 transition cursor-pointer text-xs font-bold border border-slate-700"
                                >
                                    ✕ Đóng (ESC)
                                </button>
                            </div>

                            <!-- Ô tìm kiếm bài học nhanh -->
                            <div class="p-3 border-b border-slate-800/80 bg-slate-950/60 shrink-0">
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-slate-500 text-xs">🔍</span>
                                    <input
                                        v-model="curriculumSearch"
                                        type="text"
                                        placeholder="Tìm kiếm bài học theo tiêu đề..."
                                        class="w-full bg-slate-900 border border-slate-800 rounded-xl pl-8 pr-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500"
                                    />
                                </div>
                            </div>

                            <!-- Danh sách Chương & Bài học -->
                            <div class="flex-1 overflow-y-auto">
                                <div v-for="section in filteredSections" :key="section.id" class="border-b border-slate-800/80">
                                    <div class="px-4 py-3 bg-slate-900/90 text-xs font-bold text-slate-300 uppercase tracking-wider flex items-center justify-between sticky top-0 z-10 border-b border-slate-800/60">
                                        <span class="truncate">{{ section.title }}</span>
                                        <span class="text-[11px] text-slate-500 shrink-0 font-normal">({{ section.lessons?.length || 0 }})</span>
                                    </div>

                                    <ul class="divide-y divide-slate-800/40">
                                        <li v-for="lesson in section.lessons" :key="lesson.id">
                                            <Link
                                                :href="route('courses.learn', [course.slug, lesson.slug])"
                                                @click="isCurriculumOpen = false"
                                                class="px-4 py-3.5 flex items-start justify-between gap-3 hover:bg-slate-800/50 transition cursor-pointer"
                                                :class="lesson.id === currentLesson.id ? 'bg-blue-600/15 border-l-4 border-blue-500' : ''"
                                            >
                                                <div class="flex items-start gap-2.5 min-w-0">
                                                    <span
                                                        class="mt-0.5 text-xs font-bold shrink-0"
                                                        :class="completedLessonIds.includes(lesson.id)
                                                            ? 'text-emerald-400'
                                                            : (lesson.id === currentLesson.id ? 'text-blue-400' : 'text-slate-500')"
                                                    >
                                                        {{ getLessonIcon(lesson) }}
                                                    </span>
                                                    <span
                                                        class="text-xs sm:text-sm leading-snug"
                                                        :class="lesson.id === currentLesson.id ? 'font-bold text-blue-400' : 'text-slate-300'"
                                                    >
                                                        {{ lesson.title }}
                                                    </span>
                                                </div>
                                                <span class="text-[11px] text-slate-500 shrink-0">{{ lesson.duration }}p</span>
                                            </Link>
                                        </li>
                                    </ul>
                                </div>

                                <div v-if="filteredSections.length === 0" class="text-center py-10 text-slate-500 text-xs">
                                    Không tìm thấy bài học nào phù hợp.
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

        <!-- MODAL XEM HÌNH ẢNH PHÓNG TO (LIGHTBOX) -->
        <div
            v-if="isImageModalOpen"
            class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md flex items-center justify-center p-4"
            @click.self="isImageModalOpen = false"
        >
            <div class="relative max-w-6xl max-h-[92vh] w-full flex flex-col items-center">
                <button
                    @click="isImageModalOpen = false"
                    class="absolute -top-10 right-0 text-white hover:text-slate-300 text-sm font-bold bg-slate-800 px-3 py-1 rounded-lg cursor-pointer"
                >
                    ✕ Đóng (ESC)
                </button>
                <img :src="currentLesson.image_url" :alt="currentLesson.title" class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl" />
                <p class="text-xs text-slate-400 mt-2 text-center">{{ currentLesson.title }}</p>
            </div>
        </div>
    </div>
</template>
