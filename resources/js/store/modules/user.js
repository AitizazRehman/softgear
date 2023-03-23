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
  async getUsers({
    commit
  }, payload) {
    return await UserAxios.getUsers(payload);
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
let UserAxios = class {

  static getUsers(payload) {
    return axios.get(api.path('user.getUsers'), {
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
}
