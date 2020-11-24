<template>
    <div>
        <div
            v-if="show.not_success"
            class="row pt-10"
        >
            <div class="col-lg-12 m-auto">
                <div
                    class="alert alert-warning fade mb-0 show"
                    role="alert"
                >
                    <strong>Error!</strong>&nbsp;
                    <span>Data has not been updated.</span>
                    <button
                        type="button"
                        class="close"
                        @click="hideError()"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
        <div
            v-if="show.success"
            class="row pt-10"
        >
            <div class="col-lg-12 m-auto">
                <div
                    class="alert alert-success fade mb-0 show"
                    role="alert"
                >
                    Data has been updated.
                    <button
                        type="button"
                        class="close"
                        @click="hideSuccess()"
                    >
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'MessageSuccessError',
    props: {
        show: {
            type: Object,
            default() {
                return {
                    not_success: false,
                    success: false,
                };
            },
        },
        fnHideMessage: {
            type: Function,
            default: () => {},
        }
    },
    methods: {
        hideError() {
            this.fnHideMessage({ key: 'not_success', value: false });
        },
        hideSuccess() {
            this.fnHideMessage({ key: 'success', value: false });
        },
    },
};
</script>

<style scoped>

</style>
