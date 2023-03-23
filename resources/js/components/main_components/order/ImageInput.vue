<template>
    <div style="text-align: center;">
        <!-- slot for parent component to activate the file changer -->
        <div @click="launchFilePicker()">
            <slot name="activator"></slot>
        </div>
        <!-- image input: style is set to hidden and assigned a ref so that it can be triggered -->
        <input type="file"
               ref="file"
               :name="uploadFieldName"
               @change="onFileChange(
          $event.target.name, $event.target.files)"
               style="display:none">
        <!-- error dialog displays any potential error messages -->
        <v-dialog v-model="errorDialog" max-width="300">
            <v-card>
                <v-card-text class="subheading">{{errorText}}</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn @click="errorDialog = false" flat>Got it!</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    export default {
        name: 'image-input',
        data: ()=> ({
            errorDialog: null,
            errorText: '',
            uploadFieldName: 'file',
        }),
        props: {

        },
        methods: {
            launchFilePicker() {
                this.$refs.file.click();
            },
            onFileChange(fieldName, file) {
                let imageFile = this.$refs.file.files[0]

                if (file.length>0) {
                    let imageURL = URL.createObjectURL(imageFile)
                    this.$emit('input', { imageFile, imageURL })
                }
            }
        }
    }
</script>
