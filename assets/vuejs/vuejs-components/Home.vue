<template>
    <v-container fluid>
        <v-card
            color="grey lighten-4"
            height="48"
            flat
            tile>
            <v-toolbar dense>
                <v-toolbar-items class="nav navLink">
                    <ul class="nav">
                        <router-link :to="{ name: 'users_crud'}">Users Crud</router-link>
                    </ul>
                    <ul class="nav">
                        <router-link :to="{ name: 'users_crud'}">Models Gallery</router-link>
                    </ul>
                </v-toolbar-items>
                <v-spacer></v-spacer>
                <v-btn icon>
                    <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
            </v-toolbar>
        </v-card>

        <v-main>
            <v-container class="mt-2">
                <v-layout column justify-space-around>
                    <v-flex md10>
                        <router-view name="top"></router-view>
                        <router-view name="main"></router-view>
                    </v-flex>
                </v-layout>
            </v-container>
        </v-main>
    </v-container>
</template>
<script>

export default {
    name  : 'Home',
    props : {},
    data  : () => {
        return {};
    },
    computed : {},
    mounted  : function () {

    },
    beforeDestroy() {

    },
    destroyed() {

    },

    created: function () {

    }
};
</script>

<style scoped>

</style>
