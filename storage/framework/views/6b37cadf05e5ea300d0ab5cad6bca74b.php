
<style>
    .secim-cipler { display: flex !important; flex-wrap: wrap; gap: .5rem; }
    .secim-cipler .fi-fo-checkbox-list-option-ctn { margin: 0; break-inside: auto; }
    .secim-cipler .fi-fo-checkbox-list-option {
        position: relative; display: inline-flex; align-items: center; gap: 0;
        min-width: 3rem; justify-content: center; cursor: pointer; user-select: none;
        padding: .45rem .9rem; border-radius: 999px;
        border: 1px solid var(--gray-300); background: #fff; color: var(--gray-700);
        font-size: .875rem; font-weight: 500; line-height: 1.2;
        transition: background-color .12s, border-color .12s, color .12s;
    }
    .secim-cipler .fi-fo-checkbox-list-option:hover { border-color: var(--primary-500); }
    .secim-cipler .fi-checkbox-input { position: absolute; opacity: 0; width: 1px; height: 1px; margin: 0; pointer-events: none; }
    .secim-cipler .fi-fo-checkbox-list-option:has(.fi-checkbox-input:checked) {
        background: var(--primary-600); border-color: var(--primary-600); color: #ffffff;
    }
    /* Seçili çipte renk noktası altın zeminde kaybolmasın */
    .secim-cipler .fi-fo-checkbox-list-option:has(.fi-checkbox-input:checked) .fi-fo-checkbox-list-option-label span span { box-shadow: 0 0 0 2px #ffffff; }
    .secim-cipler .fi-fo-checkbox-list-option:has(.fi-checkbox-input:focus-visible) {
        outline: 2px solid var(--primary-500); outline-offset: 2px;
    }
    .secim-cipler .fi-fo-checkbox-list-option:has(.fi-checkbox-input:disabled) { opacity: .5; cursor: not-allowed; }
    .secim-cipler .fi-fo-checkbox-list-option-label { font-weight: inherit; color: inherit; }
    .dark .secim-cipler .fi-fo-checkbox-list-option { background: transparent; border-color: var(--gray-600); color: var(--gray-200); }
    .dark .secim-cipler .fi-fo-checkbox-list-option:has(.fi-checkbox-input:checked) { background: var(--primary-600); border-color: var(--primary-600); color: #ffffff; }
</style>
<?php /**PATH F:\Yazılımlar\Schafer\resources\views/filament/cipler-stil.blade.php ENDPATH**/ ?>