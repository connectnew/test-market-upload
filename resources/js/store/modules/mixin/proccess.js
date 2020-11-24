const data = {
    spinner: {
        save: false,
    },
    invalid: {},
    success: false,
    not_success: false,
};

const getters = {
    SPINNER: (state) => (btn) => {
        return typeof state.spinner[btn] !== 'undefined' ? state.spinner[btn] : false;
    },
    INVALID: (state) => state.invalid,
    SUCCESS: (state) => state.success,
    NOT_SUCCESS: (state) => state.not_success,
};

const mutations = {
    SET_SPINNER: (state, { btn, value }) => {
        if (typeof state.spinner[btn] !== 'undefined') {
            state.spinner[btn] = value;
        }
    },
    SET_INVALID: (state, payload) => { state.invalid = payload; },
    SET_SUCCESS: (state, payload) => { state.success = payload; },
    SET_NOT_SUCCESS: (state, payload) => { state.not_success = payload; },
};

const actions = {
    SHOW_PROCCESS_MESSAGE(context, { loading, btn }) {
        context.dispatch('animateLoading', { type: loading, action: 'start' }, { root: true });
        context.commit('SET_SPINNER', { btn, value: true });
    },
    SHOW_SUCCESS_MESSAGE(context, { loading, btn, notify }) {
        context.dispatch('animateLoading', { type: loading, action: 'finish' }, { root: true });
        context.commit('SET_SPINNER', { btn, value: false });
        if (notify) {
            context.commit('SET_SUCCESS', true);
            context.commit('SET_INVALID', {});
            setTimeout(() => { context.commit('SET_SUCCESS', false); }, 2000);
        }
    },
    SHOW_ERROR_MESSAGE(context, { loading, btn, notify, error }) {
        context.dispatch('animateLoading', { type: loading, action: 'error' }, { root: true });
        context.commit('SET_SPINNER', { btn, value: false });
        if (notify) {
            if (typeof error.response !== 'undefined'
                && typeof error.response.status !== 'undefined'
                && error.response.status === 422) {
                context.commit('SET_INVALID', error.response.data.errors);
            }
            context.commit('SET_NOT_SUCCESS', true);
        }
    },
    HIDE_RESULT_MESSAGE(context, { key, value }) {
        if (key === 'success') {
            context.commit('SET_SUCCESS', value);
        } else if (key === 'not_success') {
            context.commit('SET_NOT_SUCCESS', value);
        }
    },
    CLEAR_MIX_MODAL(context) {
        context.commit('SET_SPINNER', { btn: 'save', value: false });
        context.commit('SET_INVALID', {});
        context.commit('SET_SUCCESS', false);
        context.commit('SET_NOT_SUCCESS', false);
    },
};

export default {
    namespaced: true,
    state: data,
    mutations,
    getters,
    actions,
};
