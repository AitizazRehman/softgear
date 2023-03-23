/**
 * Initial state
 */
export const state = {

}

/**
 * Mutations
 */
export const mutations = {

}

/**
 * Actions
 */
export const actions = {
  async getOrders ({
    commit
  }, payload) {
    return await OrderAxios.getOrders(payload);
  },
  async saveOrder({
    commit
  }, payload) {
    return await OrderAxios.saveOrder(payload);
  }, 
  async deleteOrder({
    commit
  }, payload) {
    return await OrderAxios.deleteOrder(payload);
  },
}

/**
 * Getters
 */
export const getters = {

}
/**
 * Axios
 */
let OrderAxios = class {

  static getOrders(payload) {
    return axios.get(api.path('order.getOrders'), {
        params: payload
      })
      .then(resp => {
        return resp.data.details;
      })
      .catch(err => {
        return {
          status: 400,
          data: err.response.data
        };
      });
  }
  static saveOrder(payload) {
    return axios.post(api.path('order.saveOrder'), payload)
      .then(resp => {
        return {
          status: 200,
          data: resp.data
        };
      })
      .catch(err => {
        return {
          status : 400,
          data : err.response.data
        };
      });
  }
   static deleteOrder(payload) {
    return axios.post(api.path('order.deleteOrder'), payload)
      .then(resp => {
        return {
          status: 200,
          data: resp.data
        };
      })
      .catch(err => {
        return {
          status : 400,
          data : err.response.data
        };
      });
  }
}
