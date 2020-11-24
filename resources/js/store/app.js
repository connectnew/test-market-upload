
import Vue from 'vue';
import Vuex from 'vuex';
import mixinProccess from './modules/mixin/proccess';

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        loading: {
            local: 0,
            global: 0,
        },
        modal: {},
        auth: false,
    },
    mutations: {
        setStore(state, { key, data, allowCreate = false }) {
            if (key.includes('.')) {
                let tmp = state;
                const arrKey = key.split('.');
                for (const i in arrKey) { // eslint-disable-line guard-for-in, no-restricted-syntax
                    const keyInStore = arrKey[i];
                    if (typeof tmp[keyInStore] !== 'undefined') {
                        if (keyInStore === arrKey[arrKey.length - 1]) {
                            tmp[keyInStore] = data;
                        }
                        tmp = tmp[keyInStore];
                    } else if (allowCreate) {
                        if (keyInStore === arrKey[arrKey.length - 1]) {
                            tmp[keyInStore] = data;
                        }
                        tmp = tmp[keyInStore];
                    }
                }
            } else {
                state[key] = data; // eslint-disable-line no-param-reassign
            }
        },
    },
    getters: {
        auth: (state) => state.auth,
        authRoles: (state) => state.roles,
        getStore: (state) => (key) => {
            if (key.includes('.')) {
                let tmp = state;
                const arrKey = key.split('.');
                for (const i in arrKey) { // eslint-disable-line guard-for-in, no-restricted-syntax
                    const keyInStore = arrKey[i];
                    if (typeof tmp[keyInStore] !== 'undefined') {
                        if (keyInStore === arrKey[arrKey.length - 1]) {
                            return tmp[keyInStore];
                        }
                        tmp = tmp[keyInStore];
                    }
                }
                return undefined;
            } else { // eslint-disable-line no-else-return
                return state[key];
            }
        },
    },
    actions: {
        /*
        actionName({state, dispatch, commit}, params) {
            dispatch(actionName, params);
            commit(mutationName, params);
        },
        */
        animateLoading({ getters, commit }, { type, action }) {
            let value = getters.getStore(`loading.${type}`);
            switch (action) {
            case 'start':
                value += 1;
                break;
            case 'finish':
            case 'error':
                value -= 1;
                break;
            default:
                console.log('error action animation: ', action);
                break;
            }

            if (value < 0) {
                value = 0;
            }
            commit('setStore', { key: `loading.${type}`, data: value });
        },
    },
    modules: {
        mixinProccess,
    },
});

export default store;
