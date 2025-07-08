import React, { createContext, useContext, useReducer, useEffect } from 'react';

// Initial state
const initialState = {
  user: null,
  isAuthenticated: false,
  cart: [],
  wishlist: [],
  searchQuery: '',
  filters: {
    category: '',
    priceRange: '',
    brand: ''
  },
  isLoading: false,
  notifications: []
};

// Action types
const ACTIONS = {
  SET_USER: 'SET_USER',
  LOGOUT: 'LOGOUT',
  ADD_TO_CART: 'ADD_TO_CART',
  REMOVE_FROM_CART: 'REMOVE_FROM_CART',
  UPDATE_CART_QUANTITY: 'UPDATE_CART_QUANTITY',
  CLEAR_CART: 'CLEAR_CART',
  ADD_TO_WISHLIST: 'ADD_TO_WISHLIST',
  REMOVE_FROM_WISHLIST: 'REMOVE_FROM_WISHLIST',
  SET_SEARCH_QUERY: 'SET_SEARCH_QUERY',
  SET_FILTERS: 'SET_FILTERS',
  SET_LOADING: 'SET_LOADING',
  ADD_NOTIFICATION: 'ADD_NOTIFICATION',
  REMOVE_NOTIFICATION: 'REMOVE_NOTIFICATION'
};

// Reducer
function appReducer(state, action) {
  switch (action.type) {
    case ACTIONS.SET_USER:
      return {
        ...state,
        user: action.payload,
        isAuthenticated: !!action.payload
      };
    
    case ACTIONS.LOGOUT:
      localStorage.removeItem('einspot_token');
      localStorage.removeItem('einspot_user');
      return {
        ...state,
        user: null,
        isAuthenticated: false,
        cart: [],
        wishlist: []
      };
    
    case ACTIONS.ADD_TO_CART:
      const existingCartItem = state.cart.find(item => item.id === action.payload.id);
      if (existingCartItem) {
        return {
          ...state,
          cart: state.cart.map(item =>
            item.id === action.payload.id
              ? { ...item, quantity: item.quantity + 1 }
              : item
          )
        };
      }
      return {
        ...state,
        cart: [...state.cart, { ...action.payload, quantity: 1 }]
      };
    
    case ACTIONS.REMOVE_FROM_CART:
      return {
        ...state,
        cart: state.cart.filter(item => item.id !== action.payload)
      };
    
    case ACTIONS.UPDATE_CART_QUANTITY:
      return {
        ...state,
        cart: state.cart.map(item =>
          item.id === action.payload.id
            ? { ...item, quantity: action.payload.quantity }
            : item
        )
      };
    
    case ACTIONS.CLEAR_CART:
      return {
        ...state,
        cart: []
      };
    
    case ACTIONS.ADD_TO_WISHLIST:
      const existingWishlistItem = state.wishlist.find(item => item.id === action.payload.id);
      if (existingWishlistItem) return state;
      return {
        ...state,
        wishlist: [...state.wishlist, action.payload]
      };
    
    case ACTIONS.REMOVE_FROM_WISHLIST:
      return {
        ...state,
        wishlist: state.wishlist.filter(item => item.id !== action.payload)
      };
    
    case ACTIONS.SET_SEARCH_QUERY:
      return {
        ...state,
        searchQuery: action.payload
      };
    
    case ACTIONS.SET_FILTERS:
      return {
        ...state,
        filters: { ...state.filters, ...action.payload }
      };
    
    case ACTIONS.SET_LOADING:
      return {
        ...state,
        isLoading: action.payload
      };
    
    case ACTIONS.ADD_NOTIFICATION:
      return {
        ...state,
        notifications: [...state.notifications, { id: Date.now(), ...action.payload }]
      };
    
    case ACTIONS.REMOVE_NOTIFICATION:
      return {
        ...state,
        notifications: state.notifications.filter(notif => notif.id !== action.payload)
      };
    
    default:
      return state;
  }
}

// Context
const AppContext = createContext();

// Provider
export const AppProvider = ({ children }) => {
  const [state, dispatch] = useReducer(appReducer, initialState);

  // Load user from localStorage on app start
  useEffect(() => {
    const token = localStorage.getItem('einspot_token');
    const user = localStorage.getItem('einspot_user');
    
    if (token && user) {
      try {
        const userData = JSON.parse(user);
        dispatch({ type: ACTIONS.SET_USER, payload: userData });
      } catch (error) {
        localStorage.removeItem('einspot_token');
        localStorage.removeItem('einspot_user');
      }
    }
  }, []);

  // Actions
  const actions = {
    login: (userData, token) => {
      localStorage.setItem('einspot_token', token);
      localStorage.setItem('einspot_user', JSON.stringify(userData));
      dispatch({ type: ACTIONS.SET_USER, payload: userData });
    },
    
    logout: () => {
      dispatch({ type: ACTIONS.LOGOUT });
    },
    
    addToCart: (product) => {
      dispatch({ type: ACTIONS.ADD_TO_CART, payload: product });
      actions.addNotification({
        type: 'success',
        message: `${product.name} added to cart`
      });
    },
    
    removeFromCart: (productId) => {
      dispatch({ type: ACTIONS.REMOVE_FROM_CART, payload: productId });
    },
    
    updateCartQuantity: (productId, quantity) => {
      if (quantity <= 0) {
        actions.removeFromCart(productId);
      } else {
        dispatch({ type: ACTIONS.UPDATE_CART_QUANTITY, payload: { id: productId, quantity } });
      }
    },
    
    clearCart: () => {
      dispatch({ type: ACTIONS.CLEAR_CART });
    },
    
    addToWishlist: (product) => {
      dispatch({ type: ACTIONS.ADD_TO_WISHLIST, payload: product });
      actions.addNotification({
        type: 'success',
        message: `${product.name} added to wishlist`
      });
    },
    
    removeFromWishlist: (productId) => {
      dispatch({ type: ACTIONS.REMOVE_FROM_WISHLIST, payload: productId });
    },
    
    setSearchQuery: (query) => {
      dispatch({ type: ACTIONS.SET_SEARCH_QUERY, payload: query });
    },
    
    setFilters: (filters) => {
      dispatch({ type: ACTIONS.SET_FILTERS, payload: filters });
    },
    
    setLoading: (loading) => {
      dispatch({ type: ACTIONS.SET_LOADING, payload: loading });
    },
    
    addNotification: (notification) => {
      dispatch({ type: ACTIONS.ADD_NOTIFICATION, payload: notification });
      // Auto-remove notification after 5 seconds
      setTimeout(() => {
        actions.removeNotification(Date.now());
      }, 5000);
    },
    
    removeNotification: (id) => {
      dispatch({ type: ACTIONS.REMOVE_NOTIFICATION, payload: id });
    }
  };

  return (
    <AppContext.Provider value={{ state, actions }}>
      {children}
    </AppContext.Provider>
  );
};

// Hook to use context
export const useApp = () => {
  const context = useContext(AppContext);
  if (!context) {
    throw new Error('useApp must be used within an AppProvider');
  }
  return context;
};

export { ACTIONS };