import { HTTP_STATUS } from '../config/constants';

// Global error handler
export class ErrorHandler {
  static handle(error, context = '') {
    console.error(`Error in ${context}:`, error);
    
    // Log to external service in production
    if (process.env.NODE_ENV === 'production') {
      this.logToService(error, context);
    }
    
    return this.getErrorMessage(error);
  }
  
  static getErrorMessage(error) {
    if (error.response) {
      // Server responded with error status
      const { status, data } = error.response;
      
      switch (status) {
        case HTTP_STATUS.BAD_REQUEST:
          return data.message || 'Invalid request. Please check your input.';
        case HTTP_STATUS.UNAUTHORIZED:
          return 'Session expired. Please login again.';
        case HTTP_STATUS.FORBIDDEN:
          return 'You do not have permission to perform this action.';
        case HTTP_STATUS.NOT_FOUND:
          return 'The requested resource was not found.';
        case HTTP_STATUS.INTERNAL_SERVER_ERROR:
          return 'Server error. Please try again later.';
        default:
          return data.message || 'An unexpected error occurred.';
      }
    } else if (error.request) {
      // Network error
      return 'Network error. Please check your internet connection.';
    } else {
      // Other error
      return error.message || 'An unexpected error occurred.';
    }
  }
  
  static logToService(error, context) {
    // Implement external logging service (e.g., Sentry, LogRocket)
    try {
      // Example: Sentry.captureException(error, { context });
      console.log('Logging to external service:', { error, context });
    } catch (logError) {
      console.error('Failed to log error:', logError);
    }
  }
}

// React Error Boundary
export class ErrorBoundary extends React.Component {
  constructor(props) {
    super(props);
    this.state = { hasError: false, error: null };
  }
  
  static getDerivedStateFromError(error) {
    return { hasError: true, error };
  }
  
  componentDidCatch(error, errorInfo) {
    ErrorHandler.handle(error, 'ErrorBoundary');
  }
  
  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen flex items-center justify-center bg-gray-50">
          <div className="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <div className="text-6xl mb-4">⚠️</div>
            <h1 className="text-2xl font-bold text-gray-800 mb-4">
              Something went wrong
            </h1>
            <p className="text-gray-600 mb-6">
              We're sorry, but something unexpected happened. Please try refreshing the page.
            </p>
            <button
              onClick={() => window.location.reload()}
              className="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors"
            >
              Refresh Page
            </button>
          </div>
        </div>
      );
    }
    
    return this.props.children;
  }
}

// Async error handler for promises
export const handleAsyncError = (asyncFn) => {
  return async (...args) => {
    try {
      return await asyncFn(...args);
    } catch (error) {
      throw new Error(ErrorHandler.handle(error, asyncFn.name));
    }
  };
};