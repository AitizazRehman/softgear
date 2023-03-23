<template>
    <v-dialog v-model="loading" max-width="300" persistent>
        <v-card height="28vh" style="overflow-y: hidden">
            <v-card-title class="text-h5">Loading....</v-card-title>
            <v-card-text>Please Wait</v-card-text>
            <v-spacer></v-spacer>
            <div class="text-center">
                <v-progress-circular :size="50" color="primary" center indeterminate>
                </v-progress-circular>
            </div>
        </v-card>
    </v-dialog>
</template>
<script>
  export default {
    props:{
      loading:false
    },
    data () {
      return {
      }
    },
  }
</script>