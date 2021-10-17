import Vue from 'vue';
import Vuetify from 'vuetify';
import colors from 'vuetify/lib/util/colors';

Vue.use(Vuetify);

const opts = {
    theme: {
        themes: {
            primary: {
                base    : colors.indigo.base,
                darken1 : colors.purple.darken2
            },
            secondary : colors.grey.lighten3,
            tertiary  : colors.blue.darken1
        },
        icons: {
            iconfont: 'mdi'
        }
    }
};

export default new Vuetify(opts);
