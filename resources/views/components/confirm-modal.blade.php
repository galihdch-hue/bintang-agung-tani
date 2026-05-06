<div x-data="{ 
    show: false, 
    title: '', 
    message: '', 
    confirmText: 'Ya, Hapus', 
    cancelText: 'Batal', 
    action: null,
    isDestructive: true,
    
    confirm(config) {
        this.title = config.title || 'Konfirmasi Tindakan';
        this.message = config.message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        this.confirmText = config.confirmText || 'Konfirmasi';
        this.cancelText = config.cancelText || 'Batal';
        this.action = config.action;
        this.isDestructive = config.isDestructive !== false;
        this.show = true;
    },
    
    handleConfirm() {
        if (this.action && typeof this.action === 'function') {
            this.action();
        }
        this.show = false;
    }
}" 
x-show="show" 
x-on:confirm-action.window="confirm($event.detail)"
x-cloak
class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm"
x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0"
x-transition:enter-end="opacity-100"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100"
x-transition:leave-end="opacity-0">
    
    <div @click.away="show = false" 
         class="bg-white rounded-3xl shadow-2xl w-full max-w-[400px] p-8 text-center mx-4 overflow-hidden relative"
         x-transition:enter="transition ease-out duration-300 delay-75"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95">
        
        <!-- Decoration -->
        <div class="absolute top-0 left-0 w-full h-1.5" :class="isDestructive ? 'bg-red-500' : 'bg-primary-500'"></div>

        <div class="mx-auto flex items-center justify-center w-20 h-20 rounded-full mb-5 relative"
             :class="isDestructive ? 'bg-red-50' : 'bg-primary-50'">
            <div class="absolute inset-0 rounded-full animate-ping opacity-20"
                 :class="isDestructive ? 'bg-red-100' : 'bg-primary-100'"></div>
            <i class="ph ph-duotone w-10 h-10" 
               :class="[isDestructive ? 'ph-trash text-red-500' : 'ph-check-circle text-primary-500']"></i>
        </div>
        
        <h3 class="text-2xl font-bold text-gray-900 mb-2" x-text="title"></h3>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed" x-html="message"></p>
        
        <div class="flex flex-col sm:flex-row justify-center gap-3">
            <button @click="show = false" class="w-full sm:w-1/2 btn-secondary justify-center shadow-sm h-11">
                <span x-text="cancelText"></span>
            </button>
            <button @click="handleConfirm()" 
                    class="w-full sm:w-1/2 font-bold py-2.5 px-4 rounded-xl transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 active:translate-y-px h-11"
                    :class="isDestructive ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-primary-600 hover:bg-primary-700 text-white'">
                <span x-text="confirmText"></span>
            </button>
        </div>
    </div>
</div>
