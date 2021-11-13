import Api from '../../../Api';

const categories = {
    namespaced : true,
    state      : {
        categories: []
    },
    mutations: {
        GET_CATEGORIES(state, categories) {
            state.categories = categories;
        },
        SET_CATEGORY(state, categories) {
            state.categories.push(categories);
        }
    },
    actions: {
        getCategories({commit}) {
            Api.get(
                'api/all-categories',
                (response) => {
                    console.log(response.data);
                    commit('GET_CATEGORIES', response.data);
                }
            ).catch(error => {
                console.log(error.message);
            });
        }
    },
    getters: {
        allCategories: state => {
            return state.categories;
        }
    }
};
export default categories;
