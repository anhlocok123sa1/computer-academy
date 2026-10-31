<script setup>
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Nhập nội dung bài học...',
    },
    minHeight: {
        type: String,
        default: '220px',
    },
});

const emit = defineEmits(['update:modelValue']);
const editorContainer = ref(null);
let editorInstance = null;
const isReady = ref(false);

const initEditor = () => {
    if (!window.ClassicEditor || !editorContainer.value || editorInstance) return;

    window.ClassicEditor.create(editorContainer.value, {
        placeholder: props.placeholder,
        toolbar: {
            items: [
                'heading',
                '|',
                'bold',
                'italic',
                'link',
                'bulletedList',
                'numberedList',
                '|',
                'outdent',
                'indent',
                '|',
                'insertTable',
                'blockQuote',
                'codeBlock',
                '|',
                'undo',
                'redo',
            ],
            shouldNotGroupWhenFull: true,
        },
        heading: {
            options: [
                { model: 'paragraph', title: 'Đoạn văn', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h2', title: 'Tiêu đề 1 (H2)', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h3', title: 'Tiêu đề 2 (H3)', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h4', title: 'Tiêu đề 3 (H4)', class: 'ck-heading_heading3' },
            ],
        },
        table: {
            contentToolbar: [
                'tableColumn',
                'tableRow',
                'mergeTableCells',
            ],
        },
    })
        .then((editor) => {
            editorInstance = editor;
            isReady.value = true;

            // Set initial data
            if (props.modelValue) {
                editor.setData(props.modelValue);
            }

            // Listen for change events
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                emit('update:modelValue', data);
            });
        })
        .catch((error) => {
            console.error('Lỗi khởi tạo CKEditor 5:', error);
        });
};

onMounted(() => {
    if (window.ClassicEditor) {
        initEditor();
    } else {
        // Nếu script chưa load xong, kiểm tra định kỳ hoặc tải script
        const interval = setInterval(() => {
            if (window.ClassicEditor) {
                clearInterval(interval);
                initEditor();
            }
        }, 100);

        setTimeout(() => clearInterval(interval), 5000);
    }
});

watch(
    () => props.modelValue,
    (newValue) => {
        if (editorInstance && isReady.value) {
            const currentData = editorInstance.getData();
            if (newValue !== currentData) {
                editorInstance.setData(newValue || '');
            }
        }
    }
);

onBeforeUnmount(() => {
    if (editorInstance) {
        editorInstance.destroy().catch((err) => console.error(err));
        editorInstance = null;
    }
});
</script>

<template>
    <div class="ckeditor-wrapper rounded-xl overflow-hidden border border-slate-800 bg-slate-950 text-slate-100">
        <div ref="editorContainer"></div>
    </div>
</template>

<style>
/* Tùy biến CKEditor 5 để tương thích Dark & Light Mode */
.ckeditor-wrapper .ck-editor__editable {
    min-height: v-bind('props.minHeight');
    max-height: 550px;
    font-size: 0.875rem;
    line-height: 1.6;
}

/* Dark Mode Theme cho CKEditor */
html.dark .ckeditor-wrapper .ck-toolbar {
    background-color: #0b1120 !important;
    border-color: #1e293b !important;
}

html.dark .ckeditor-wrapper .ck-button {
    color: #cbd5e1 !important;
}

html.dark .ckeditor-wrapper .ck-button:hover,
html.dark .ckeditor-wrapper .ck-button.ck-on {
    background-color: #1e293b !important;
    color: #ffffff !important;
}

html.dark .ckeditor-wrapper .ck-editor__editable {
    background-color: #030712 !important;
    color: #f1f5f9 !important;
    border-color: #1e293b !important;
}

html.dark .ckeditor-wrapper .ck-editor__editable:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
}

html.dark .ckeditor-wrapper .ck-dropdown__panel {
    background-color: #0b1120 !important;
    border-color: #1e293b !important;
}

html.dark .ckeditor-wrapper .ck-list__item button {
    color: #e2e8f0 !important;
}

html.dark .ckeditor-wrapper .ck-list__item button:hover {
    background-color: #1e293b !important;
}

/* Light Mode Theme cho CKEditor */
html:not(.dark) .ckeditor-wrapper {
    background-color: #ffffff !important;
    border-color: #cbd5e1 !important;
}

html:not(.dark) .ckeditor-wrapper .ck-toolbar {
    background-color: #f8fafc !important;
    border-color: #e2e8f0 !important;
}

html:not(.dark) .ckeditor-wrapper .ck-editor__editable {
    background-color: #ffffff !important;
    color: #0f172a !important;
    border-color: #e2e8f0 !important;
}

html:not(.dark) .ckeditor-wrapper .ck-editor__editable:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.15) !important;
}
</style>
