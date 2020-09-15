<template>
    <v-snackbar
        v-model="visible"
        :bottom="y === 'bottom'"
        :color="color"
        :left="x === 'left'"
        :multi-line="mode === 'multi-line'"
        :right="x === 'right'"
        :timeout="-1"
        :top="y === 'top'"
        :vertical="mode === 'vertical'"
    >
        {{ text }}
        <v-btn dark text @click.native="$emit('resquest-case')">Close</v-btn>
    </v-snackbar>
</template>

<script>
export default {
    name: "Snackbar",
    props: ['visible', 'color', 'text'],
    data() {
        return {
            mode: '',
            timeout: -1,
            x: 'right',
            y: 'bottom'
        }
    },
}
</script>