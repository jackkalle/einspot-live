import axios from 'axios';

const API_BASE_URL = process.env.REACT_APP_BACKEND_URL + '/api';

// Create axios instance
const api = axios.create({
  baseURL: API_BASE_URL,
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
  },
});

// Add auth token to requests
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('einspot_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Handle response errors
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('einspot_token');
      localStorage.removeItem('einspot_user');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

// Auth API
export const authAPI = {
  login: async (credentials) => {
    try {
      const response = await api.post('/auth/login', credentials);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Login failed' };
    }
  },

  register: async (userData) => {
    try {
      const response = await api.post('/auth/register', userData);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Registration failed' };
    }
  },

  forgotPassword: async (email) => {
    try {
      const response = await api.post('/auth/forgot-password', { email });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Request failed' };
    }
  },

  resetPassword: async (token, password) => {
    try {
      const response = await api.post('/auth/reset-password', { token, password });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Reset failed' };
    }
  }
};

// Payments API
export const paymentsAPI = {
  verifyFlutterwave: async (verificationData) => {
    try {
      const response = await api.post('/payments/verify/flutterwave', verificationData);
      return response.data;
    } catch (error) {
      throw error.response?.data || { success: false, message: 'Flutterwave verification request failed' };
    }
  },

  verifyPaystack: async (verificationData) => {
    try {
      const response = await api.post('/payments/verify/paystack', verificationData);
      return response.data;
    } catch (error) {
      throw error.response?.data || { success: false, message: 'Paystack verification request failed' };
    }
  }
};

// Products API
export const productsAPI = {
  getAll: async (params = {}) => {
    try {
      const response = await api.get('/products', { params });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch products' };
    }
  },

  getById: async (id) => {
    try {
      const response = await api.get(`/products/${id}`);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch product' };
    }
  },

  getCategories: async () => {
    try {
      const response = await api.get('/products/categories');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch categories' };
    }
  },

  search: async (query, filters = {}) => {
    try {
      const response = await api.get('/products/search', {
        params: { q: query, ...filters }
      });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Search failed' };
    }
  }
};

// Orders API
export const ordersAPI = {
  create: async (orderData) => {
    try {
      const response = await api.post('/orders', orderData);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to create order' };
    }
  },

  getByUser: async () => {
    try {
      const response = await api.get('/orders/my-orders');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch orders' };
    }
  },

  getById: async (id) => {
    try {
      const response = await api.get(`/orders/${id}`);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch order' };
    }
  }
};

// Contact API
export const contactAPI = {
  submitForm: async (formData) => {
    try {
      const response = await api.post('/contact', formData);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to submit form' };
    }
  },

  subscribeNewsletter: async (email) => {
    try {
      const response = await api.post('/newsletter/subscribe', { email });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Subscription failed' };
    }
  },

  requestQuote: async (quoteData) => {
    try {
      const response = await api.post('/quotes', quoteData);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to submit quote request' };
    }
  }
};

// Blog API
export const blogAPI = {
  getAll: async (params = {}) => {
    try {
      const response = await api.get('/blog', { params });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch posts' };
    }
  },

  getById: async (id) => {
    try {
      const response = await api.get(`/blog/${id}`);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch post' };
    }
  },

  getCategories: async () => {
    try {
      const response = await api.get('/blog/categories');
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch categories' };
    }
  }
};

// Projects API
export const projectsAPI = {
  getAll: async (params = {}) => {
    try {
      const response = await api.get('/projects', { params });
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch projects' };
    }
  },

  getById: async (id) => {
    try {
      const response = await api.get(`/projects/${id}`);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to fetch project' };
    }
  }
};

// Admin API (could be a separate file or part of this)
export const adminAPI = {
  // Product Admin
  updateProduct: async (productId, productData) => {
    try {
      const response = await api.put(`/admin/products/${productId}`, productData);
      return response.data;
    } catch (error) {
      throw error.response?.data || { message: 'Failed to update product' };
    }
  },

  deleteProduct: async (productId) => {
    try {
      const response = await api.delete(`/admin/products/${productId}`);
      return response.data; // Or handle 204 No Content status
    } catch (error) {
      throw error.response?.data || { message: 'Failed to delete product' };
    }
  },

  // TODO: Add other admin API calls here (e.g., for orders, users, etc.)
  // Example:
  // getAllUsers: async () => { ... api.get('/admin/users') ... }
  // updateUserRole: async (userId, role) => { ... api.put(`/admin/users/${userId}/role`, { role }) ... }
  // getAdminOrders: async () => { ... api.get('/admin/orders') ... }
  // updateOrderStatus: async (orderId, status) => { ... api.put(`/admin/orders/${orderId}/status`, { status }) ... }
};


export default api;