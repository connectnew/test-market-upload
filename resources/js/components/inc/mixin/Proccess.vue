<script>
export default {
    name: 'mixinProccess',
    data() {
        return {
            spinner: {
                save: false,
            },
            invalid: {},
            not_success: false,
            success: false,
        };
    },
    methods: {
        showProcessMessage(loading, btn) {
            this.$store.dispatch('animateLoading', { type: loading, action: 'start' });
            if (typeof this.spinner[btn] !== 'undefined') {
                this.spinner[btn] = true;
            }
        },
        showSuccessMessage(loading, btn, notify) {
            this.$store.dispatch('animateLoading', { type: loading, action: 'finish' });
            if (typeof this.spinner[btn] !== 'undefined') {
                this.spinner[btn] = false;
            }
            if (notify) {
                this.success = true;
                this.invalid = {};
                setTimeout(() => { this.success = false; }, 2000);
            }
        },
        showErrorMessage(loading, btn, notify, e) {
            this.$store.dispatch('animateLoading', { type: loading, action: 'error' });
            if (typeof this.spinner[btn] !== 'undefined') {
                this.spinner[btn] = false;
            }
            if (notify) {
                if (typeof e.response !== 'undefined'
                    && typeof e.response.status !== 'undefined'
                    && e.response.status === 422) {
                    this.invalid = e.response.data.errors;
                }
                this.not_success = true;
            }
        },
        hideResultMessage({ key, value }) {
            if (key === 'success') {
                this.success = value;
            } else if (key === 'not_success') {
                this.not_success = value;
            }
        },
        clearMixModal() {
            this.spinner.save = false;
            this.success = false;
            this.not_success = false;
            this.invalid = {};
        },
    },
};
</script>
