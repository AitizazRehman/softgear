import axios from 'axios'
import store from '~/store/index'
import { app } from '~/app'

axios.interceptors.request.use(config => {
  config.headers['X-Requested-With'] = 'XMLHttpRequest'

  const token = store.getters['auth/token']
  if (token) {
    config.headers['Authorization'] = 'Bearer ' + token
  }

  return config
}, error => {
  return Promise.reject(error)
})

axios.interceptors.response.use(response => {
  return response
}, async error => {

  return Promise.reject(error)
})
