document.addEventListener('alpine:init', () => {

    Alpine.data('deleteModal', () => ({

        open: false,

        show() {
            this.open = true
        },

        close() {
            this.open = false
        }

    }))

})