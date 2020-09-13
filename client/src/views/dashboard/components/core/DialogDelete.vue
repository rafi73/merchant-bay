<template>
    <v-dialog v-model="dialogConfirmDelete" max-width="500">
        <v-card>
            <v-card-title class="headline">{{ $t('delete_confirm_title') }}</v-card-title>
            <v-card-text>{{ $t('delete_confirm_text') }}</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                class="mx-2"
                                fab
                                small
                                dark
                                color="primary"
                                v-on="on"
                                @click="onClickCancel()"
                            >
                                <v-icon dark>mdi-close</v-icon>
                            </v-btn>
                        </template>
                        <span>{{$t('close')}}</span>
                    </v-tooltip>
                    <v-tooltip bottom>
                        <template v-slot:activator="{ on }">
                            <v-btn
                                class="mx-2"
                                fab
                                dark
                                color="error"
                                v-on="on"
                                @click="onClickDelete()"
                            >
                                <v-icon dark>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        <span>{{$t('delete')}}</span>
                    </v-tooltip>
                </v-card-actions>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "DialogDelete",
    props: {
        dialogConfirmDelete: {
            default: false,
        },
    },

    data() {
        return {
            dialogStatus: this.dialogConfirmDelete
        }
    },

    methods: {
        onClickDelete(event) {
            this.$emit('onClickDelete', 'someValue')
        },
        onClickCancel(event) {
            this.$emit('onClickCancel', 'someValue')
        },
    }
};
</script>