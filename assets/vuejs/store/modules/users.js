import Api from '../../../Api';

const users = {
    namespaced : true,
    state      : {
        users: []
    },
    mutations: {
        GET_USERS(state, users) {
            state.users = users;
        },
        SET_USER(state, users) {
            state.users.push(users);
        },
        UPDATE_USER(state, user) {
            let index = state.users.findIndex((c) => { return c.id === user.id; });
            if (index !== -1) {
                state.users.splice(index, 1, user);
            }
        },
        REMOVE_USER(state, user) {
            state.users = state.users.filter(t => { return user.id !== t.id; });
        }
    },
    actions: {
        getUsers({commit}) {
            Api.get(
                '/rest-api/all-users',
                (response) => {
                    commit('GET_USERS', response.data);
                }
            ).catch(error => {
                console.log(error.message);
            });
        },
        setUser({commit}, params) {
            console.log(params);
            Api.post(
                '/rest-api/user/add', {
                    params
                },
                (response) => {
                    commit('SET_USER', response.data);
                }
            ).catch(error => {
                console.log(error.message);
            });
        },
        updateUser({commit}, params) {
            Api.patch(
                `/rest-api/user/edit/${params.id}`, {
                    params
                },
                (response) => {
                    commit('UPDATE_USER', response.data);
                }
            ).catch(error => {
                console.log(error.message);
            });
        },
        removeUser({commit}, params) {
            Api.delete(
                `/rest-api/user/delete/${params.id}`, {
                    params
                },
                () => {
                    commit('REMOVE_USER', params);
                }
            ).catch(error => {
                console.log(error.message);
            });
        }
    },
    getters: {
        allUsers: state => {
            return state.users;
        }
    }
};
export default users;
