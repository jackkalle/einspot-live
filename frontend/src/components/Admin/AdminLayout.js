import React from 'react';
import { Link, Outlet, Navigate } from 'react-router-dom';
import { useApp } from '../../context/AppContext';
import { Header } from '../index'; // Using the main header for now

const AdminLayout = () => {
  const { state } = useApp();

  if (!state.isAuthenticated) {
    return <Navigate to="/login?redirect=/admin" replace />;
  }

  if (!state.user?.isAdmin) {
    // Optional: show a "Not Authorized" page or redirect to home
    // For now, redirecting to home and showing a notification (App context should handle this)
    // actions.addNotification({ type: 'error', message: 'You are not authorized to access this page.' });
    console.warn("User is not admin, redirecting from AdminLayout.");
    return <Navigate to="/" replace />;
  }

  return (
    <div className="min-h-screen bg-gray-100">
      <Header /> {/* Or a dedicated AdminHeader */}
      <div className="flex">
        <aside className="w-64 bg-gray-800 text-white p-6 space-y-4 min-h-screen">
          <h2 className="text-xl font-semibold mb-6">Admin Panel</h2>
          <nav>
            <ul>
              <li>
                <Link to="/admin/dashboard" className="block py-2 px-3 rounded hover:bg-gray-700 transition-colors">Dashboard</Link>
              </li>
              <li>
                <Link to="/admin/products" className="block py-2 px-3 rounded hover:bg-gray-700 transition-colors">Products</Link>
              </li>
              <li>
                <Link to="/admin/orders" className="block py-2 px-3 rounded hover:bg-gray-700 transition-colors">Orders</Link>
              </li>
              <li>
                <Link to="/admin/users" className="block py-2 px-3 rounded hover:bg-gray-700 transition-colors">Users</Link>
              </li>
              {/* Add more links as other admin sections are built */}
            </ul>
          </nav>
        </aside>
        <main className="flex-1 p-10">
          <Outlet /> {/* Nested admin routes will render here */}
        </main>
      </div>
    </div>
  );
};

export default AdminLayout;
