import React, { useEffect } from 'react';
import { useApp } from '../../context/AppContext';

const NotificationSystem = () => {
  const { state, actions } = useApp();

  useEffect(() => {
    // Auto-remove notifications after 5 seconds
    state.notifications.forEach(notification => {
      setTimeout(() => {
        actions.removeNotification(notification.id);
      }, 5000);
    });
  }, [state.notifications, actions]);

  const getNotificationIcon = (type) => {
    switch (type) {
      case 'success': return '✅';
      case 'error': return '❌';
      case 'warning': return '⚠️';
      case 'info': return 'ℹ️';
      default: return '🔔';
    }
  };

  const getNotificationColors = (type) => {
    switch (type) {
      case 'success': return 'bg-green-50 border-green-200 text-green-800';
      case 'error': return 'bg-red-50 border-red-200 text-red-800';
      case 'warning': return 'bg-yellow-50 border-yellow-200 text-yellow-800';
      case 'info': return 'bg-blue-50 border-blue-200 text-blue-800';
      default: return 'bg-gray-50 border-gray-200 text-gray-800';
    }
  };

  if (state.notifications.length === 0) return null;

  return (
    <div className="fixed top-4 right-4 z-50 space-y-2">
      {state.notifications.map((notification) => (
        <div
          key={notification.id}
          className={`max-w-sm p-4 rounded-lg border shadow-lg transform transition-all duration-300 ease-in-out ${getNotificationColors(notification.type)}`}
          style={{
            animation: 'slideInRight 0.3s ease-out'
          }}
        >
          <div className="flex items-start">
            <span className="text-2xl mr-3 flex-shrink-0">
              {getNotificationIcon(notification.type)}
            </span>
            <div className="flex-1">
              {notification.title && (
                <h4 className="font-semibold mb-1">{notification.title}</h4>
              )}
              <p className="text-sm">{notification.message}</p>
            </div>
            <button
              onClick={() => actions.removeNotification(notification.id)}
              className="ml-2 text-gray-400 hover:text-gray-600 flex-shrink-0"
            >
              <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      ))}
      
      <style jsx>{`
        @keyframes slideInRight {
          from {
            transform: translateX(100%);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
      `}</style>
    </div>
  );
};

export default NotificationSystem;