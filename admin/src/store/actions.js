// actions.js
import axiosClient from '../axios';

export function getUser({ commit }, id) {
  return axiosClient.get(`/users/${id}`);
}


export function getCurrentUser({ commit }) {
    return axiosClient.get('/user').then((response) => {
        commit('setUser', response.data);
        return response;
    });
}

export function login({ commit }, data) {
    return axiosClient.post('/login', data).then(({ data }) => {
        commit('setUser', data.user);
        commit('setToken', data.token);
        return data;
    });
}

export function logout({ commit }) {
    return axiosClient
        .post('/logout')
        .then((response) => {
            commit('setToken', null);
            commit('setUser', null);
            return response;
        })
        .catch((error) => {
            console.error('Logout error:', error);
            throw error;
        });
}

export function getCountries({commit}){
   return axiosClient.get('countries')
        .then(({data})=>{
            commit('setCountries',data)
        })
}

export function getProducts({ commit }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
    commit('setProducts', [true]);
    url = url || '/product';

    return axiosClient
        .get(url, {
            params: {
                search,
                per_page: perPage,
                sort_field,
                sort_direction,
            },
        })
        .then((res) => {
            commit('setProducts', [false, res.data]);
        })
        .catch(() => {
            commit('setProducts', [false]);
        });
}






export function getOrders({ commit, state }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
    commit('setOrders', [true]);
    url = url || '/orders';

    const params = {
        per_page: perPage || (state.orders ? state.orders.limit : 10), // fallback if state.orders is undefined
        search,
        sort_field,
        sort_direction,
    };

    return axiosClient
        .get(url, { params })
        .then((res) => {
            commit('setOrders', [false, res.data]);
        })
        .catch(() => {
            commit('setOrders', [false]);
        });
}

export function getProduct({commit}, id) {
    return axiosClient.get(`/product/${id}`);
}



export function getOrder({ commit }, id) {
    return axiosClient.get(`/orders/${id}`);
}

/**
 * Create Product
 * Always send FormData, whether image exists or not
 */
export function createProduct({ commit }, product) {
    const formData = new FormData();
    formData.append('title', product.title);
    formData.append('description', product.description);
    formData.append('price', product.price);
    formData.append('published', product.published ? 1 : 0); // ✅ FIXED

    if (product.image instanceof File) {
        formData.append('image', product.image);
    }

    return axiosClient.post('/product', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });
}

export function getUsers({ commit }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
    commit('setUsers', [true]);
    url = url || '/users';

    return axiosClient
        .get(url, {
            params: {
                search,
                per_page: perPage,
                sort_field,
                sort_direction,
            },
        })
        .then((res) => {
            commit('setUsers', [false, res.data]);
        })
        .catch(() => {
            commit('setUsers', [false]);
        });
}
export function createUser({ commit }, user) {
    return axiosClient.post('/users', user);
}

export function updateUser({ commit }, user) {
    return axiosClient
        .put(`/users/${user.id}`, user)
        .then((response) => response)
        .catch((error) => {
            console.error('Update user error:', error.response?.data || error.message);
            throw error; // re-throw so component can handle it
        });
}


export function getCustomers({ commit }, { url = null, search = '', perPage = 10, sort_field, sort_direction } = {}) {
    commit('setCustomers', [true]);
    url = url || '/customers';

    return axiosClient
        .get(url, {
            params: {
                search,
                per_page: perPage,
                sort_field,
                sort_direction,
            },
        })
        .then((res) => {
            commit('setCustomers', [false, res.data]);
        })
        .catch(() => {
            commit('setCustomers', [false]);
        });
}

export function getCustomer({ commit }, id) {
    return axiosClient.get(`/customers/${id}`);
}

export function createCustomer({ commit }, customer) {
    return axiosClient.post('/customers', customer);
}

export function updateCustomer({ commit }, customer) {
    return axiosClient
        .put(`/customers/${customer.id}`, customer)
        .then((response) => response)
        .catch((error) => {
            console.error('Update user error:', error.response?.data || error.message);
            throw error; // re-throw so component can handle it
        });
}

export function deleteCustomer({commit},customer){
    return axiosClient.delete(`/customers/${user.customer}`,customer);
}



/**
 * Update Product
 * Always send FormData and add _method=PUT
 */
export function updateProduct({ commit }, product) {
    const formData = new FormData();
    formData.append('title', product.title);
    formData.append('description', product.description);
    formData.append('price', product.price);
    formData.append('published', product.published ? 1 : 0); // ✅ FIXED

    if (product.image instanceof File) {
        formData.append('image', product.image);
    }

    formData.append('_method', 'PUT');

    return axiosClient.post(`/product/${product.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
    });
}







/**
 * Delete Product
 */
export function deleteProduct({ commit }, id) {
    return axiosClient.delete(`/product/${id}`);
}
