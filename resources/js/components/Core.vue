<template>
    <div></div>
</template>

<script>
import { mapGetters, mapMutations } from 'vuex';

export default {
    name: 'Core',
    components: {
    },
    data() {
        return {
            globalLoading: false,
        };
    },
    computed: {
        ...mapGetters([
            'getStore',
        ]),
        hasLoadingLocal() {
            return this.getStore('loading.local');
        },
    },
    watch: {
        hasLoadingLocal(valNew, valOld) {
            if (valNew) {
                if (valOld) {
                    if (valNew > valOld) {
                        // this.$Progress.set(); =%
                        // this.$Progress.increase(); +%
                    } else {
                        // this.$Progress.set(); =%
                        // this.$Progress.decrease(); -%
                    }
                } else {
                    this.$Progress.start();
                }
            } else {
                this.$Progress.finish();
            }
        },
    },
    mounted() {},
    methods: {
        ...mapMutations([
            'setStore',
        ]),
    },
};
</script>
