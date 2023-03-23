<template>
  <v-navigation-drawer fixed app :permanent="$vuetify.breakpoint.mdAndUp" light
    :mini-variant.sync="$vuetify.breakpoint.mdAndUp && mini" :clipped="$vuetify.breakpoint.mdAndUp" :value="mini"
    :width="270">
    <v-row justify="space-around">
      <v-avatar color="grey lighten" size="85" class="mt-2">
        <v-img :src="uploadPicture" alt="avatar" class="pt-2">
        </v-img>
      </v-avatar>
    </v-row>
    <v-list class="py-0">
      <v-list-item>
        <v-list-item-icon v-show="$vuetify.breakpoint.mdAndUp && mini">
          <v-btn small icon @click.native.stop="navToggle" class="mx-0">
            <v-icon>chevron_right</v-icon>
          </v-btn>
        </v-list-item-icon>

        <v-list-item-content>
          <v-list-item-title id="item_tittle"><strong>{{ currentUser.name }}</strong></v-list-item-title>
          <v-list-item-title class="email">{{ currentUser.email }}</v-list-item-title>
        </v-list-item-content>

        <v-list-item-icon>
          <v-btn small icon @click.native.stop="navToggle" class="mx-0">
            <v-icon>chevron_left</v-icon>
          </v-btn>
        </v-list-item-icon>
      </v-list-item>
    </v-list>
    <v-list class="py-0" dense v-for="(group, index) in items" :key="index">

      <v-divider class="mb-2" :class="{ 'mt-0': !index, 'mt-2': index }"
        v-if="group.length && group[0].access"></v-divider>
      <template v-for="item in group" v-if="item.access">

        <v-list-group v-if="!!item.items" :prepend-icon="item.icon" no-action :key="item.title">

          <template v-slot:activator>
            <v-list-item-content>
              <v-list-item-title>{{ item.title }}</v-list-item-title>
            </v-list-item-content>
          </template>

          <v-list-item v-for="subItem in item.items" v-if="subItem.access" :key="subItem.title"
            @click="subItem.action ? subItem.action() : false" :to="subItem.to" :disabled="!subItem.access" ripple
            :exact="subItem.exact !== undefined ? subItem.exact : true ? subItem.access : true">
            <div v-if="subItem.access">
              <v-list-item-content class="pl-2">
                <v-list-item-title>{{ subItem.title }}</v-list-item-title>
              </v-list-item-content>
            </div>
            <v-list-item-icon>
              <v-icon small>{{ subItem.icon }}</v-icon>
            </v-list-item-icon>
          </v-list-item>
        </v-list-group>

        <v-list-item class="primary--text" v-else @click.native="item.action ? item.action() : false"
          href="javascript:void(0)" :to="item.to" ripple :exact="item.exact !== undefined ? item.exact : true"
          :key="item.title">
          <v-list-item-icon>
            <v-icon small>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>
    </v-list>
  </v-navigation-drawer>
</template>
<script>
import { mapGetters, mapActions } from "vuex";

export default {
  data: () => ({
    items: [],
    name: null,
    currentUser: [],
  }),

  props: ["mini"],

  computed: {
    ...mapGetters({
      auth: "auth/user",
    }),
    uploadPicture() {
      if (this.currentUser.upload != null) {
        return (
          Laravel.siteUrl + "/uploads/ProfilePicture/" + this.currentUser.upload.name
        );
      }
      return Laravel.siteUrl + "/uploads/ProfilePicture/user_placeholder.png";
    },
  },
  async created() {
    await this.fetchUser();
    this.currentUser = this.auth;
    this.navigation();
  },
  methods: {
    ...mapActions({
      fetchUser: "auth/fetchUser",
    }),
    navToggle() {
      this.$emit("nav-toggle");
    },

    async logout() {
      await this.$store.dispatch("auth/logout");

      this.$toast.info("You are logged out.");
      this.$router.push({ name: "login" });
    },
    navigation() {
      this.items = [
        [
          {
            title: "Dashboard",
            icon: "mdi-view-dashboard",
            to: { name: "dashboard" },
            exact: true,
            access: true,
          },
        ],
        [
          {
            title: "Order Section",
            icon: "mdi-chart-timeline",
            exact: false,
            access: true,
            items: [
              {
                title: "All Orders",
                to: { name: "order" },
                access: true,

              },
            ],
          },
        ],
        [{ title: "Logout", icon: "mdi-power", action: this.logout, access: true }],
      ];
    },
  },
};
</script>
<style>
#item_tittle {
  font-size: 15px !important;
  text-align: center !important;
}

.email {
  font-size: 12px !important;
  text-align: center !important;
  text-decoration: underline !important;
}
</style>
