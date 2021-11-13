<template>
    <v-container fluid>
        <v-app>
            <v-row justify="center">
                <v-col sm="12">
                    <v-card max-width="400px" flat class="ml-5">
                        <v-card-title>
                            <v-text-field
                                v-model="search"
                                append-icon="mdi-magnify"
                                label="Search"
                                single-line
                                hide-details
                            ></v-text-field>
                        </v-card-title>
                    </v-card>
                    <template>
                        <router-link :to="{name: 'add_user'}">
                            <v-btn v-if="visible" class="grey lighten-2" depressed load @click="this.hideMe">Add User</v-btn>
                        </router-link>
                    </template>
                    <v-data-table
                        :headers="headers"
                        :search="search"
                        :items="all_users"
                        sort-by="id"
                        class="elevation-1 p-1">
                        <template v-slot:top>
                            <v-toolbar flat>
                                <v-dialog v-model="dialog" max-width="500px">
                                    <v-card>
                                        <v-card-title>
                                            <span class="text-h6">{{ formTitle }}</span>
                                        </v-card-title>
                                        <v-card-text>
                                            <v-container>
                                                <v-row>
                                                    <v-col v-if="editedIndex !== -1" cols="12" sm="8" md="6">
                                                        <v-text-field
                                                            v-model="editedItem.id"
                                                            label="id"
                                                            disabled
                                                        ></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" sm="8" md="6">
                                                        <v-text-field
                                                            v-model="editedItem.email"
                                                            label="email"
                                                            clearable
                                                        ></v-text-field>
                                                    </v-col>
                                                    <v-col cols="12" sm="8" md="6">
                                                        <v-text-field
                                                            v-model="editedItem.roles"
                                                            label="roles"
                                                            clearable
                                                        ></v-text-field>
                                                    </v-col>
                                                    <v-col v-if="editedIndex === -1" cols="12" sm="12" md="12">
                                                        <v-text-field
                                                            v-model="editedItem.password"
                                                            label="Password"
                                                            clearable
                                                        ></v-text-field>
                                                    </v-col>
                                                </v-row>
                                            </v-container>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="blue darken-1" text @click="close">Cancel</v-btn>
                                            <v-btn color="blue darken-1" text @click="save">Save</v-btn>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                                <v-dialog v-model="dialogDelete" max-width="500px">
                                    <v-card>
                                        <v-card-title class="text-body-1">Are you sure you want to delete this user?</v-card-title>
                                        <v-card-actions>
                                            <v-spacer></v-spacer>
                                            <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                                            <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                                            <v-spacer></v-spacer>
                                        </v-card-actions>
                                    </v-card>
                                </v-dialog>
                            </v-toolbar>
                        </template>
                        <template v-slot:item.image="{ item }">
                            <img :src='require("/public/uploads/user-image/" + item.image)' style="width: 50px; height: 50px"/>
                        </template>
                        <template v-slot:[`item.posts`]="{ item }">
                            {{ item.posts.length }}
                        </template>
                        <template v-slot:[`item.opinions`]="{ item }">
                            {{ item.opinions.length }}
                        </template>
                        <template v-slot:[`item.notes`]="{ item }">
                            {{ item.notes.length }}
                        </template>
                        <template v-slot:[`item.addresses`]="{ item }">
                            {{ item.addresses.length }}
                        </template>
                        <template v-slot:[`item.actions`]="{ item }">
                            <v-icon small class="mr-2" @click="editItem(item)">mdi-pencil</v-icon>
                            <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
                        </template>
                        <template v-slot:no-data>
                            <v-container>No data available...</v-container>
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
        </v-app>
    </v-container>
</template>
<style>
.style-chooser .vs__selected {
    text-transform: lowercase;
    font-variant: traditional;
}

.style-chooser {
    margin-top: 1em;
}

.style-chooser .vs__search::placeholder {
    color: #394066;
    text-transform: lowercase;
    font-variant: traditional;
}

.style-chooser .vs__dropdown-menu {
    padding: 0;
    margin-top: 0.1em;
    background: #ffffff;
    border: none;
    color: #394066;
    text-transform: lowercase;
    font-variant: traditional;
}

.style-chooser .vs__clear {
    fill: #c42265;
}

.style-chooser .vs__open-indicator {
    fill: rgba(22, 184, 61, 0.67);
}

.style-chooser .vs__dropdown-option--highlight {
    background: rgba(22, 184, 61, 0.67);;
}
</style>
<script>
import {mapGetters} from 'vuex';

export default {
    data: () => {
        return {
            visible  : true,
            selected : null,
            search   : '',
            roles    : [
                'ROLE_USER',
                'ROLE_ADMIN'
            ],
            dialog       : false,
            dialogDelete : false,
            headers      : [
                {
                    text  : 'id',
                    value : 'id',
                    align : 'start'
                },
                {
                    text     : 'email',
                    value    : 'email',
                    align    : 'start',
                    sortable : true
                },
                {
                    text     : 'Roles',
                    value    : 'roles',
                    align    : 'start',
                    sortable : true
                },
                {
                    text     : 'Verified',
                    value    : 'isVerified',
                    align    : 'start',
                    sortable : false
                },
                {
                    text     : 'Password',
                    value    : 'password',
                    align    : 'start',
                    sortable : false
                },
                {
                    text     : 'Image',
                    value    : 'image',
                    align    : 'start',
                    sortable : false
                },
                {
                    text     : 'Posts',
                    value    : 'posts',
                    align    : 'start',
                    sortable : true
                },
                {
                    text     : 'Opinions',
                    value    : 'opinions',
                    align    : 'start',
                    sortable : true
                },
                {
                    text     : 'Notes',
                    value    : 'notes',
                    align    : 'start',
                    sortable : true
                },
                {
                    text     : 'Address',
                    value    : 'addresses',
                    align    : 'start',
                    sortable : true
                },
                {
                    text     : 'Actions',
                    value    : 'actions',
                    align    : 'start',
                    sortable : false
                }
            ],
            editedIndex : -1,
            editedItem  : {
                id    : 0,
                email : '',
                roles : ''
            },
            defaultItem: {
                email    : '',
                roles    : '',
                password : ''
            }
        };
    },

    computed: {
        ...mapGetters({
            all_users: 'users/allUsers'
        }),
        formTitle() {
            return this.editedIndex === -1 ? 'New User' : 'Edit User';
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        },
        dialogDelete(val) {
            val || this.closeDelete();
        }
    },

    mounted() {
        this.$store.dispatch('users/getUsers');
    },

    created() {
    },

    methods: {
        hideMe() {
            this.visible = false;
        },
        editItem(item) {
            this.editedIndex = this.all_users.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },

        deleteItem(item) {
            this.editedIndex = this.all_users.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialogDelete = true;
        },

        deleteItemConfirm() {
            let payload = {
                id: this.editedItem.id
            };
            this.$store.dispatch('users/removeUser', payload);
            this.closeDelete();
        },

        close() {
            this.dialog = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },

        closeDelete() {
            this.dialogDelete = false;
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            });
        },

        save() {
            if (this.editedIndex > -1) {
                let payload = {
                    id    : this.editedItem.id,
                    roles : [this.editedItem.roles],
                    email : this.editedItem.email
                };
                console.log(payload);
                this.$store.dispatch('users/updateUser', payload);
            } else {
                let payload = {
                    email    : this.editedItem.email,
                    roles    : [this.editedItem.roles],
                    password : this.editedItem.password
                };
                this.$store.dispatch('users/setUser', payload);
            }
            this.close();
        }
    }
};
</script>
