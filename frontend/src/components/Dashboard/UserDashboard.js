import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useApp } from '../../context/AppContext';
import { updateMetaTags } from '../../utils/seo';
import { Header, Footer, WhatsAppButton } from '../index';

const UserDashboard = () => {
  const { state } = useApp();
  const [activeTab, setActiveTab] = useState('overview');
  const [orders, setOrders] = useState([]);
  const [wishlist, setWishlist] = useState([]);

  useEffect(() => {
    updateMetaTags({
      title: 'Dashboard - EINSPOT SOLUTIONS NIG LTD',
      description: 'Manage your EINSPOT account, view orders, track projects, and access exclusive features.',
      keywords: 'user dashboard, account management, order tracking, EINSPOT account'
    });

    // Mock data - replace with API calls
    setOrders([
      {
        id: 'ORD-001',
        date: '2025-01-15',
        status: 'Processing',
        total: 450000,
        items: ['Rheem Water Heater', 'Installation Service']
      },
      {
        id: 'ORD-002', 
        date: '2025-01-10',
        status: 'Delivered',
        total: 280000,
        items: ['HVAC Maintenance Kit']
      }
    ]);

    setWishlist(state.wishlist);
  }, [state.wishlist]);

  const getStatusColor = (status) => {
    switch (status) {
      case 'Processing': return 'bg-yellow-100 text-yellow-800';
      case 'Shipped': return 'bg-blue-100 text-blue-800';
      case 'Delivered': return 'bg-green-100 text-green-800';
      case 'Cancelled': return 'bg-red-100 text-red-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const DashboardCard = ({ title, value, icon, color = "red" }) => (
    <div className="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-shadow">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-gray-600 text-sm mb-1">{title}</p>
          <p className="text-2xl font-bold text-gray-800">{value}</p>
        </div>
        <div className={`bg-${color}-100 p-3 rounded-full`}>
          <span className="text-2xl">{icon}</span>
        </div>
      </div>
    </div>
  );

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      <div className="container mx-auto px-4 py-8">
        {/* Welcome Section */}
        <div className="bg-white rounded-2xl shadow-lg p-8 mb-8">
          <div className="flex items-center justify-between">
            <div>
              <h1 className="text-3xl font-bold text-gray-800 mb-2">
                Welcome back, {state.user?.firstName || 'User'}!
              </h1>
              <p className="text-gray-600">
                Manage your engineering solutions and track your projects
              </p>
            </div>
            <div className="hidden md:block">
              <img 
                src="https://images.unsplash.com/photo-1573166801077-d98391a43199" 
                alt="Dashboard"
                className="w-24 h-24 rounded-full object-cover"
              />
            </div>
          </div>
        </div>

        {/* Stats Cards */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <DashboardCard title="Total Orders" value={orders.length} icon="📦" color="blue" />
          <DashboardCard title="Cart Items" value={state.cart.length} icon="🛒" color="green" />
          <DashboardCard title="Wishlist" value={state.wishlist.length} icon="❤️" color="red" />
          <DashboardCard title="Active Projects" value="3" icon="🏗️" color="purple" />
        </div>

        {/* Navigation Tabs */}
        <div className="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
          <div className="border-b border-gray-200">
            <div className="flex space-x-8 px-6">
              {[
                { id: 'overview', label: 'Overview', icon: '📊' },
                { id: 'orders', label: 'Orders', icon: '📦' },
                { id: 'wishlist', label: 'Wishlist', icon: '❤️' },
                { id: 'profile', label: 'Profile', icon: '👤' },
                { id: 'projects', label: 'Projects', icon: '🏗️' }
              ].map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  className={`py-4 px-2 border-b-2 font-medium text-sm transition-colors ${
                    activeTab === tab.id
                      ? 'border-red-500 text-red-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700'
                  }`}
                >
                  <span className="mr-2">{tab.icon}</span>
                  {tab.label}
                </button>
              ))}
            </div>
          </div>

          {/* Tab Content */}
          <div className="p-6">
            {activeTab === 'overview' && (
              <div>
                <h2 className="text-2xl font-bold text-gray-800 mb-6">Account Overview</h2>
                <div className="grid md:grid-cols-2 gap-6">
                  <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-gray-800">Recent Activity</h3>
                    <div className="space-y-3">
                      <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span className="text-green-600">✓</span>
                        <span className="text-gray-700">Order #ORD-002 delivered</span>
                        <span className="text-gray-500 text-sm ml-auto">2 days ago</span>
                      </div>
                      <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <span className="text-blue-600">📦</span>
                        <span className="text-gray-700">Order #ORD-001 processing</span>
                        <span className="text-gray-500 text-sm ml-auto">5 days ago</span>
                      </div>
                    </div>
                  </div>
                  
                  <div className="space-y-4">
                    <h3 className="text-lg font-semibold text-gray-800">Quick Actions</h3>
                    <div className="grid grid-cols-2 gap-3">
                      <Link to="/products" className="bg-red-600 text-white p-4 rounded-lg hover:bg-red-700 transition-colors text-center">
                        <div className="text-2xl mb-2">🛍️</div>
                        <div className="font-medium">Shop Products</div>
                      </Link>
                      <Link to="/services" className="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 transition-colors text-center">
                        <div className="text-2xl mb-2">🔧</div>
                        <div className="font-medium">Book Service</div>
                      </Link>
                      <Link to="/projects" className="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition-colors text-center">
                        <div className="text-2xl mb-2">🏗️</div>
                        <div className="font-medium">View Projects</div>
                      </Link>
                      <Link to="/contact" className="bg-purple-600 text-white p-4 rounded-lg hover:bg-purple-700 transition-colors text-center">
                        <div className="text-2xl mb-2">💬</div>
                        <div className="font-medium">Contact Support</div>
                      </Link>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'orders' && (
              <div>
                <h2 className="text-2xl font-bold text-gray-800 mb-6">My Orders</h2>
                <div className="space-y-4">
                  {orders.map((order) => (
                    <div key={order.id} className="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                      <div className="flex items-center justify-between mb-4">
                        <div>
                          <h3 className="text-lg font-semibold text-gray-800">Order {order.id}</h3>
                          <p className="text-gray-600">Placed on {new Date(order.date).toLocaleDateString()}</p>
                        </div>
                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(order.status)}`}>
                          {order.status}
                        </span>
                      </div>
                      <div className="flex items-center justify-between">
                        <div>
                          <p className="text-gray-700 mb-2">Items: {order.items.join(', ')}</p>
                          <p className="text-xl font-bold text-red-600">₦{order.total.toLocaleString()}</p>
                        </div>
                        <div className="flex gap-2">
                          <button className="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            View Details
                          </button>
                          {order.status === 'Delivered' && (
                            <button className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                              Reorder
                            </button>
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {activeTab === 'wishlist' && (
              <div>
                <h2 className="text-2xl font-bold text-gray-800 mb-6">My Wishlist</h2>
                {state.wishlist.length === 0 ? (
                  <div className="text-center py-12">
                    <div className="text-6xl mb-4">❤️</div>
                    <h3 className="text-xl font-semibold text-gray-800 mb-2">Your wishlist is empty</h3>
                    <p className="text-gray-600 mb-6">Save items you love for later</p>
                    <Link to="/products" className="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
                      Browse Products
                    </Link>
                  </div>
                ) : (
                  <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {state.wishlist.map((item) => (
                      <div key={item.id} className="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                        <div className="h-48 bg-gray-200">
                          {item.image && (
                            <img src={item.image} alt={item.name} className="w-full h-full object-cover" />
                          )}
                        </div>
                        <div className="p-4">
                          <h3 className="text-lg font-semibold text-gray-800 mb-2">{item.name}</h3>
                          <p className="text-red-600 font-bold text-xl mb-4">₦{item.price?.toLocaleString()}</p>
                          <div className="flex gap-2">
                            <button 
                              onClick={() => actions.addToCart(item)}
                              className="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors"
                            >
                              Add to Cart
                            </button>
                            <button 
                              onClick={() => actions.removeFromWishlist(item.id)}
                              className="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors"
                            >
                              ❌
                            </button>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}

            {activeTab === 'profile' && (
              <div>
                <h2 className="text-2xl font-bold text-gray-800 mb-6">Profile Settings</h2>
                <div className="grid md:grid-cols-2 gap-8">
                  <div>
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    <div className="space-y-4">
                      <div>
                        <label className="block text-gray-700 font-medium mb-2">First Name</label>
                        <input 
                          type="text" 
                          value={state.user?.firstName || ''} 
                          className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        />
                      </div>
                      <div>
                        <label className="block text-gray-700 font-medium mb-2">Last Name</label>
                        <input 
                          type="text" 
                          value={state.user?.lastName || ''} 
                          className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        />
                      </div>
                      <div>
                        <label className="block text-gray-700 font-medium mb-2">Email</label>
                        <input 
                          type="email" 
                          value={state.user?.email || ''} 
                          className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                        />
                      </div>
                      <button className="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition-colors">
                        Update Profile
                      </button>
                    </div>
                  </div>
                  
                  <div>
                    <h3 className="text-lg font-semibold text-gray-800 mb-4">Security</h3>
                    <div className="space-y-4">
                      <button className="w-full bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 transition-colors text-left px-4">
                        🔒 Change Password
                      </button>
                      <button className="w-full bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 transition-colors text-left px-4">
                        📱 Enable Two-Factor Authentication
                      </button>
                      <button className="w-full bg-gray-100 text-gray-700 py-3 rounded-lg hover:bg-gray-200 transition-colors text-left px-4">
                        📧 Email Preferences
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'projects' && (
              <div>
                <h2 className="text-2xl font-bold text-gray-800 mb-6">My Projects</h2>
                <div className="grid md:grid-cols-2 gap-6">
                  {[
                    { name: 'Home HVAC Installation', status: 'In Progress', progress: 75 },
                    { name: 'Office Fire Safety System', status: 'Planning', progress: 25 },
                    { name: 'Water Heater Maintenance', status: 'Completed', progress: 100 }
                  ].map((project, index) => (
                    <div key={index} className="border border-gray-200 rounded-lg p-6">
                      <h3 className="text-lg font-semibold text-gray-800 mb-2">{project.name}</h3>
                      <p className="text-gray-600 mb-4">Status: {project.status}</p>
                      <div className="mb-4">
                        <div className="flex justify-between text-sm text-gray-600 mb-1">
                          <span>Progress</span>
                          <span>{project.progress}%</span>
                        </div>
                        <div className="w-full bg-gray-200 rounded-full h-2">
                          <div 
                            className="bg-red-600 h-2 rounded-full transition-all"
                            style={{ width: `${project.progress}%` }}
                          ></div>
                        </div>
                      </div>
                      <button className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        View Details
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        </div>
      </div>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};

export default UserDashboard;