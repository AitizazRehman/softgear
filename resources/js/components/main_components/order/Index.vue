<template>
  <v-container fluid grid-list-xl>
    <v-layout row justify-center wrap>
      <v-flex lg12 sm12 xs12>
        <transition name="fade" mode="out-in">
          <router-view></router-view>
        </transition>
      </v-flex>

    </v-layout>
  </v-container>
</template>

<script>

export default {
  components: {
  },
  data: () => ({
    items: []
  }),


}
</script>
