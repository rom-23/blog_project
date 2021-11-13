<template>
    <v-form class="p-4">
        <v-container class="bg-white">
            <v-row justify="center">
                <v-col cols="12" md="4">
                    <v-text-field
                        v-model="email"
                        label="E-mail"
                        required>
                    </v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <v-text-field
                        v-model="password"
                        label="Password"
                        required>
                    </v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                    <v-checkbox
                        v-model="verified"
                        label="Verified"
                        value=1>
                    </v-checkbox>
                </v-col>
                <v-col cols="12" md="4">
                    <v-file-input
                        accept="image/*"
                        v-model="image"
                        label="Image"
                        required
                        clearable>
                        <template v-slot:selection="{ text }">
                            <v-chip small label color="primary">{{ text }}</v-chip>
                        </template>
                    </v-file-input>
                </v-col>
                <v-col cols="12" md="4">
                    <v-col sm="6">
                        <v-checkbox
                            v-model="selected"
                            label="Admin"
                            value="ROLE_ADMIN">
                        </v-checkbox>
                        <v-checkbox
                            v-model="selected"
                            label="User"
                            value="ROLE_USER">
                        </v-checkbox>
                    </v-col>
                </v-col>
            </v-row>
            <v-btn class="grey lighten-2" load @click="this.save">Save</v-btn>
            <v-btn class="grey lighten-2" load @click="this.close">Cancel</v-btn>
        </v-container>
    </v-form>
</template>

<script>
import {mapGetters} from 'vuex';

export default {
    name : 'AddUser',
    data : () => {
        return {
            selected : [],
            email    : '',
            verified : 0,
            password : '',
            image    : null
        };
    },
    computed: {},
    mounted() {
    },
    created() {
        git commit -m "Front-end App Setup" git branch -M main    git push -u origin main



    },
    methods: {
        close() {
            this.$router.back();
        },
        save() {
            let payload = {
                email    : this.email,
                roles    : this.selected,
                password : this.password,
                verified : this.verified,
                image    : this.image.name
            };
            console.log(payload);
            this.$store.dispatch('users/setUser', payload);
            this.$router.push('/front-end-app/vue/users-crud');
        }
    }
};
</script>
