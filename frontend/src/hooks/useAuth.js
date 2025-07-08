import { useState } from 'react';
import { useApp } from '../context/AppContext';
import { authAPI } from '../services/api';

export const useAuth = () => {
  const { state, actions } = useApp();
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const login = async (credentials) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await authAPI.login(credentials);
      
      if (response.success) {
        actions.login(response.user, response.token);
        actions.addNotification({
          type: 'success',
          message: 'Login successful!'
        });
        return { success: true };
      } else {
        throw new Error(response.message || 'Login failed');
      }
    } catch (err) {
      const errorMessage = err.message || 'Login failed';
      setError(errorMessage);
      actions.addNotification({
        type: 'error',
        message: errorMessage
      });
      return { success: false, error: errorMessage };
    } finally {
      setLoading(false);
    }
  };

  const register = async (userData) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await authAPI.register(userData);
      
      if (response.success) {
        actions.login(response.user, response.token);
        actions.addNotification({
          type: 'success',
          message: 'Registration successful!'
        });
        return { success: true };
      } else {
        throw new Error(response.message || 'Registration failed');
      }
    } catch (err) {
      const errorMessage = err.message || 'Registration failed';
      setError(errorMessage);
      actions.addNotification({
        type: 'error',
        message: errorMessage
      });
      return { success: false, error: errorMessage };
    } finally {
      setLoading(false);
    }
  };

  const logout = () => {
    actions.logout();
    actions.addNotification({
      type: 'success',
      message: 'Logged out successfully'
    });
  };

  const forgotPassword = async (email) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await authAPI.forgotPassword(email);
      
      if (response.success) {
        actions.addNotification({
          type: 'success',
          message: 'Password reset link sent to your email'
        });
        return { success: true };
      } else {
        throw new Error(response.message || 'Request failed');
      }
    } catch (err) {
      const errorMessage = err.message || 'Request failed';
      setError(errorMessage);
      actions.addNotification({
        type: 'error',
        message: errorMessage
      });
      return { success: false, error: errorMessage };
    } finally {
      setLoading(false);
    }
  };

  return {
    user: state.user,
    isAuthenticated: state.isAuthenticated,
    loading,
    error,
    login,
    register,
    logout,
    forgotPassword
  };
};