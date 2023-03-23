<template>
  <div>
    <v-card>
      <v-row align="center" class="mx-0">
          <v-col cols="12" sm="12" md="2" lg="2">
            <v-select v-model="keywords.order_id" :items="orders" chips solo item-text="order_name"
              item-value="id" label="Order">
            </v-select>
          </v-col>
          <v-col cols="12" sm="12" md="2" lg="2"
            :class="$vuetify.breakpoint.smAndDown ? 'mt-n6 text-center flex' : 'flex'">
            <v-btn :small="$vuetify.breakpoint.mdAndUp ? true : false"
              :x-small="$vuetify.breakpoint.smAndDown ? true : false" color="primary mb-3" dark @click="search()">
              Search
            </v-btn>
            <v-btn :small="$vuetify.breakpoint.mdAndUp ? true : false"
              :x-small="$vuetify.breakpoint.smAndDown ? true : false" color="error mb-3" dark @click="reset()"> Reset
            </v-btn>
          </v-col>
        </v-row>
      <v-data-table :headers="headers" :items="orders" class="elevation-1">
        <template v-slot:item.image="{ item }">
          <v-avatar>
            <v-img
              :src="'http://localhost/softgear/uploads/ProfilePicture/' + (item.upload ? item.upload.name : 'user_placeholder.png')"
              alt="John"></v-img>
          </v-avatar>
        </template>
        <template v-slot:top>
          <v-toolbar flat>
            <v-spacer></v-spacer>
            <v-dialog v-model="dialog" max-width="500px">
              <template v-slot:activator="{ on, attrs }">
                <v-btn color="primary" dark class="mb-2" v-bind="attrs" v-on="on" :small="$vuetify.breakpoint.mdAndUp"
                  :x-small="$vuetify.breakpoint.smAndDown">
                  Add New Order
                </v-btn>
              </template>
              <v-card>
                <v-card-title>
                  <span class="text-h5">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>
                  <v-form ref="form" @submit.prevent="save" lazy-validation v-model="valid">
                    <v-container>
                      <v-card-text>
                        <image-input v-model="file">
                          <div slot="activator">
                            <v-avatar size="175px" tile min-height="180" min-width="160" max-height="180" max-width="160">
                              <v-img referrerpolicy="no-referrer" class="white--text align-end" :src="uploadPicture"
                                alt="avatar">
                                <p class="placeholder_text pt-1 white--text">
                                  <v-icon class="body-2 d-inline white--text">mdi-camera</v-icon>
                                  <span class="caption">Edit</span>
                                </p>
                              </v-img>
                            </v-avatar>
                          </div>
                        </image-input>
                      </v-card-text>
                      <v-row>
                        <v-col cols="12" sm="12" md="12" lg="12">
                          <v-text-field label="Order Name" v-model="form.order_name"
                            :rules="[(v) => !!v || 'Order is required']" :disabled="loading"
                            autocomplete="off"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" lg="12">
                          <v-text-field label="Order Location" :rules="[(v) => !!v || 'Order Location is required']"
                            v-model="form.order_location" :disabled="loading" autocomplete="off"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" lg="12">
                          <v-text-field label="Total Payment" type="number" :rules="[(v) => !!v || 'Payment is required']"
                            v-model="form.total_payment" :disabled="loading" autocomplete="off"></v-text-field>
                        </v-col>
                        <v-col cols="12" sm="12" md="12" lg="12">
                          <v-text-field label="Description" :rules="[(v) => !!v || 'Description is required']"
                            v-model="form.description" :disabled="loading" autocomplete="off"></v-text-field>
                        </v-col>
                      </v-row>
                    </v-container>
                  </v-form>
                </v-card-text>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="blue darken-1" text @click="close">
                    Cancel
                  </v-btn>
                  <v-btn color="blue darken-1" text @click="save" :disabled="loading">
                    Save
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
            <v-dialog v-model="dialogDelete" max-width="260px">
              <v-card>
                <v-card-title class="text-h5"
                  >Are you sure you want to delete?</v-card-title
                >
                <v-card-text>You won't be able to revert this!</v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="primary" text @click="closeDelete"
                    >Cancel</v-btn
                  >
                  <v-btn color="primary" text @click="deleteItemConfirm"
                    >Yes</v-btn
                  >
                  <v-spacer></v-spacer>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-toolbar>
        </template>
        <template v-slot:item.actions="{ item }">
          <v-icon color="primary" small class="mr-2" @click="editItem(item)">
            mdi-pencil
          </v-icon>
          <v-icon color="error" small @click="deleteItem(item)" v-if="can('delete_order')"> mdi-delete </v-icon>
        </template>
        <template v-slot:no-data>
          No Data Found
        </template>
      </v-data-table>
    </v-card>
  </div>
</template>

<script>
import { mapGetters, mapActions } from "vuex";
import ImageInput from "./ImageInput";
import Form from "~/mixins/form";

export default {
  components: {
    ImageInput: ImageInput,
  },
  mixins: [Form],
  name: "dashboard",
  data: () => ({
    orders: [],
    dialog: false,
    upload: null,
    dialogDelete:false,
    keywords:{},
    file: {
      imageFile: null,
      imageURL: null,
    },
    headers: [
      { text: "#", align: "start", sortable: true, value: "sno" },
      { text: "Image", align: "start", sortable: true, value: "image" },
      { text: "Name", align: "start", sortable: true, value: "order_name" },
      { text: "Order Location", align: "start", sortable: true, value: "order_location" },
      { text: "Total Payment", align: "start", sortable: true, value: "total_payment" },
      { text: "Description", align: "start", sortable: true, value: "description" },
      { text: "Actions", value: "actions", sortable: false },
    ],
    editedIndex: -1,
    form: {
    },
    defaultItem: {
      name: null,
    }
  }),

  async created() {
    await this.initialize();
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "New Order" : "Edit Order";
    },
    uploadPicture() {
      if (this.file.imageURL) {
        return this.file.imageURL;
      }
      if (this.upload) {
        return (
          Laravel.siteUrl + "/uploads/ProfilePicture/" + this.upload.name
        );
      }
      return Laravel.siteUrl + "/uploads/ProfilePicture/user_placeholder.png";
    },
  },
  methods: {
    ...mapActions({
      saveOrder: "order/saveOrder",
      getOrders: "order/getOrders",
      deleteOrder: "order/deleteOrder",
    }),
    async initialize() {
      this.orders = await this.getOrders(this.keywords);
      this.orders = this.orders.map((d, index) => ({ ...d, sno: index + 1 }))
    },

    async save() {
      if (this.$refs.form.validate()) {
        let response = null;
        let formData = new FormData();
        let self = this;
        formData.append("data", JSON.stringify(self.form));
        if (self.file.imageFile) {
          formData.append("image", self.file.imageFile);
        }
        response = await this.saveOrder(formData);
        if (response.status == 200) {
          this.form = Object.assign({}, this.form, response.data.details);
          this.dialog = false;
          this.initialize();
        } else {
          this.handleErrors(response.data.errors);
        }
      }
    },
    deleteItem(item) {
      this.editedIndex = this.orders.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialogDelete = true;
    },

    async deleteItemConfirm() {
      let response = await this.deleteOrder(this.form);
      if (response.status == 200) {
        this.$toast.success(response.data.message);
      }
      this.closeDelete();
      await this.initialize();
    },
    closeDelete() {
      this.dialogDelete = false;
      this.$nextTick(() => {
         this.form =  Object.assign({}, this.defaultItem);
        this.editedIndex = -1;
      });
    },
    close() {
      this.dialog = false
    },
    async search() {
      await this.initialize();
    },
    reset() {
      this.keywords = {};
      this.initialize();
    },
    editItem(item) {
      this.editedIndex = this.orders.indexOf(item);
      this.upload = item.upload ? item.upload : null;
      this.form = Object.assign({}, item);
      this.dialog = true;
    },
  },
  watch: {
    dialog(val) {
    },
  },
};
</script>

<style scoped></style>
