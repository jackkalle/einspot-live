import React, { useState } from 'react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useApp } from '../context/AppContext';

// Header Component
export const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const location = useLocation();
  const navigate = useNavigate();
  const { state, actions } = useApp();

  const handleSearch = (e) => {
    e.preventDefault();
    if (searchQuery.trim()) {
      navigate(`/search?q=${encodeURIComponent(searchQuery.trim())}`);
    }
  };

  const getTotalCartItems = () => {
    return state.cart.reduce((total, item) => total + item.quantity, 0);
  };

  return (
    <header className="bg-white shadow-lg relative z-50">
      {/* Top Bar */}
      <div className="bg-gray-100 text-sm">
        <div className="container mx-auto px-4 py-2 flex justify-between items-center">
          <div className="flex items-center space-x-4">
            <span>🌍 Nigeria</span>
            <span>📞 +234 812 364 7982</span>
            <span>📧 info@einspot.com.ng</span>
          </div>
          <div className="hidden md:flex items-center space-x-4">
            <span className="hover:text-red-600 cursor-pointer transition-colors">Warranties</span>
            <span className="hover:text-red-600 cursor-pointer transition-colors">Resources</span>
            <span className="hover:text-red-600 cursor-pointer transition-colors">Tax Credits</span>
            <span className="hover:text-red-600 cursor-pointer transition-colors">Sustainability</span>
            <span className="hover:text-red-600 cursor-pointer transition-colors">Careers</span>
          </div>
        </div>
      </div>

      {/* Main Navigation */}
      <nav className="container mx-auto px-4 py-4">
        <div className="flex justify-between items-center">
          <div className="flex items-center">
            <Link to="/" className="text-2xl font-bold text-gray-800 hover:text-red-600 transition-colors">
              EINSPOT <span className="text-red-600">SOLUTIONS</span>
            </Link>
          </div>
          
          <div className="hidden lg:flex items-center space-x-8">
            <Link to="/products" className={`font-medium transition-colors ${location.pathname === '/products' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>
              Products
            </Link>
            <Link to="/services" className={`font-medium transition-colors ${location.pathname === '/services' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>
              Services
            </Link>
            <Link to="/projects" className={`font-medium transition-colors ${location.pathname === '/projects' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>
              Projects
            </Link>
            <Link to="/blog" className={`font-medium transition-colors ${location.pathname === '/blog' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>
              Blog
            </Link>
            <Link to="/about" className={`font-medium transition-colors ${location.pathname === '/about' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>
              About
            </Link>
            <Link to="/contact" className={`font-medium transition-colors ${location.pathname === '/contact' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>
              Contact
            </Link>
          </div>

          <div className="flex items-center space-x-4">
            {/* Search Bar */}
            <form onSubmit={handleSearch} className="hidden md:flex items-center">
              <div className="relative">
                <input 
                  type="text" 
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search products & services..." 
                  className="w-64 px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
                />
                <svg className="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </form>

            {/* User Account */}
            {state.isAuthenticated ? (
              <div className="relative group">
                <button className="flex items-center space-x-2 text-gray-700 hover:text-red-600 transition-colors">
                  <div className="w-8 h-8 bg-red-600 text-white rounded-full flex items-center justify-center font-semibold">
                    {state.user?.firstName?.charAt(0) || 'U'}
                  </div>
                  <span className="hidden md:block">{state.user?.firstName || 'User'}</span>
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                
                {/* User Dropdown */}
                <div className="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                  <div className="py-2">
                    <Link to="/dashboard" className="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                      🏠 Dashboard
                    </Link>
                    <Link to="/dashboard" className="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                      📦 My Orders
                    </Link>
                    <Link to="/dashboard" className="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                      ❤️ Wishlist
                    </Link>
                    <Link to="/dashboard" className="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors">
                      ⚙️ Settings
                    </Link>
                    <hr className="my-2" />
                    <button 
                      onClick={actions.logout}
                      className="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 transition-colors"
                    >
                      🚪 Logout
                    </button>
                  </div>
                </div>
              </div>
            ) : (
              <div className="flex items-center space-x-2">
                <Link to="/login" className="text-gray-700 hover:text-red-600 font-medium transition-colors">
                  Login
                </Link>
                <Link to="/register" className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium">
                  Sign Up
                </Link>
              </div>
            )}

            {/* Shopping Cart */}
            <Link to="/cart" className="relative text-gray-700 hover:text-red-600 transition-colors">
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5h2.5M14 13l2.5 5" />
              </svg>
              {getTotalCartItems() > 0 && (
                <span className="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                  {getTotalCartItems()}
                </span>
              )}
            </Link>

            {/* Find a Pro Button */}
            <button className="hidden md:block bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium">
              Find a Pro
            </button>

            {/* Mobile Menu Toggle */}
            <button 
              className="lg:hidden"
              onClick={() => setIsMenuOpen(!isMenuOpen)}
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
          </div>
        </div>
        
        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="lg:hidden mt-4 pb-4 border-t border-gray-200">
            <div className="flex flex-col space-y-2 pt-4">
              {/* Mobile Search */}
              <form onSubmit={handleSearch} className="mb-4">
                <div className="relative">
                  <input 
                    type="text" 
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    placeholder="Search..." 
                    className="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                  />
                  <svg className="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </div>
              </form>
              
              <Link to="/products" className="text-gray-700 hover:text-red-600 font-medium py-2 transition-colors">Products</Link>
              <Link to="/services" className="text-gray-700 hover:text-red-600 font-medium py-2 transition-colors">Services</Link>
              <Link to="/projects" className="text-gray-700 hover:text-red-600 font-medium py-2 transition-colors">Projects</Link>
              <Link to="/blog" className="text-gray-700 hover:text-red-600 font-medium py-2 transition-colors">Blog</Link>
              <Link to="/about" className="text-gray-700 hover:text-red-600 font-medium py-2 transition-colors">About</Link>
              <Link to="/contact" className="text-gray-700 hover:text-red-600 font-medium py-2 transition-colors">Contact</Link>
              
              {!state.isAuthenticated && (
                <div className="flex flex-col space-y-2 pt-4 border-t border-gray-200">
                  <Link to="/login" className="text-gray-700 hover:text-red-600 font-medium py-2 transition-colors">Login</Link>
                  <Link to="/register" className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors font-medium text-center">Sign Up</Link>
                </div>
              )}
            </div>
          </div>
        )}
      </nav>
    </header>
  );
};

// Hero Section
export const HeroSection = () => {
  return (
    <section className="relative h-screen bg-cover bg-center" style={{
      backgroundImage: 'linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url(https://images.unsplash.com/photo-1657571484151-41be42fa72f5)'
    }}>
      <div className="absolute inset-0 flex items-center justify-center">
        <div className="text-center text-white max-w-4xl mx-auto px-4">
          <h1 className="text-6xl font-bold mb-6">
            Engineered for <span className="text-red-500">Life</span>
          </h1>
          <p className="text-xl mb-8">
            Trusted HVAC, Water Heating, and Engineering Solutions for Nigeria
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button className="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition text-lg font-semibold">
              Explore Products
            </button>
            <button className="border-2 border-white text-white px-8 py-3 rounded-lg hover:bg-white hover:text-red-600 transition text-lg font-semibold">
              Find a Professional
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};

// Service Categories
export const ServiceCategories = () => {
  const categories = [
    {
      title: "Homeowners",
      description: "Browse Rheem products designed for energy efficiency, reliability, performance and comfort.",
      image: "https://images.unsplash.com/photo-1601520525418-4d7ff1314879",
      buttons: ["Shop", "Learn", "Buy"]
    },
    {
      title: "Commercial", 
      description: "Browse Rheem commercial products tailored to provide durable and high-capacity solutions.",
      image: "https://images.pexels.com/photos/8469943/pexels-photo-8469943.jpeg",
      buttons: ["Shop", "Learn"]
    },
    {
      title: "Professionals",
      description: "Find Rheem training, marketing and support for independent contractors, planners and installers.",
      image: "https://images.unsplash.com/photo-1573166801077-d98391a43199",
      buttons: ["Get Started"]
    }
  ];

  return (
    <section className="py-16 bg-gray-50">
      <div className="container mx-auto px-4">
        <div className="grid md:grid-cols-3 gap-8">
          {categories.map((category, index) => (
            <div key={index} className="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
              <div className="h-48 bg-cover bg-center" style={{backgroundImage: `url(${category.image})`}}>
                <div className="h-full bg-black bg-opacity-40 flex items-end">
                  <div className="p-6 text-white">
                    <h3 className="text-2xl font-bold mb-2">{category.title}</h3>
                  </div>
                </div>
              </div>
              <div className="p-6">
                <p className="text-gray-600 mb-4">{category.description}</p>
                <div className="flex gap-2">
                  {category.buttons.map((button, btnIndex) => (
                    <button key={btnIndex} className="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                      {button}
                    </button>
                  ))}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

// Sustainability Section
export const SustainabilitySection = () => {
  return (
    <section className="py-16 bg-gradient-to-r from-blue-50 to-green-50">
      <div className="container mx-auto px-4">
        <div className="grid md:grid-cols-2 gap-12 items-center">
          <div>
            <h2 className="text-sm font-semibold text-gray-600 mb-2">SUSTAINABILITY</h2>
            <h3 className="text-4xl font-bold text-gray-800 mb-6">
              big thinking,<br/>
              bigger solutions
            </h3>
            <p className="text-gray-600 mb-8">
              We continuously innovate products with sustainability in mind to reduce negative environmental impacts, energy use and customer safety costs.
            </p>
            <button className="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition">
              Learn more
            </button>
          </div>
          <div className="relative">
            <img 
              src="https://images.unsplash.com/photo-1580943795425-a4d2f4101c76" 
              alt="Sustainability" 
              className="rounded-lg shadow-lg"
            />
            <div className="absolute top-4 right-4 bg-blue-900 text-white p-4 rounded-lg">
              <h4 className="text-xl font-bold">INNOVATION</h4>
              <p className="text-sm">same comfort<br/>more savings</p>
              <p className="text-xs mt-2">The Triton Water Heater is designed to save more money.</p>
              <button className="bg-white text-blue-900 px-4 py-2 rounded mt-2 text-sm">
                Learn more
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

// Latest News Section
export const LatestNewsSection = () => {
  const news = [
    {
      title: "Celebrating 100 Years of Innovation and Partnership at Rheem",
      date: "April 15, 2025",
      excerpt: "This week, we're celebrating 100 years of innovation, reliability, and partnership at Rheem. Over the past century, Rheem has built a product innovation. And while we celebrate where we've been, we're also looking forward to all that comes next. The next 100 years of Rheem [...]",
      image: "https://images.unsplash.com/photo-1566446896748-6075a87760c1"
    },
    {
      title: "How Rheem Achieved Zero Waste to Landfill",
      date: "June 2, 2025", 
      excerpt: "In 2022, Rheem set a bold goal: achieve zero waste to landfill by 2030. Now, three years later, we're proud to say we've achieved this goal years ahead of schedule. Since 2021, we've diverted 100% of our waste from landfills by [...]",
      image: "https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1"
    },
    {
      title: "Understanding Rheem Hybrid Electric Heat Pump Water Heater Operating Modes",
      date: "May 24, 2025",
      excerpt: "When it comes to energy efficiency, heat pump water heaters offer the best of both worlds. Our Hybrid Electric Heat Pump Water Heater HPWH were designed for this very reason, offering four distinct operating modes to adapt to household hot water needs: how each mode works, when [...]",
      image: "https://images.pexels.com/photos/7078360/pexels-photo-7078360.jpeg"
    }
  ];

  return (
    <section className="py-16 bg-white">
      <div className="container mx-auto px-4">
        <h2 className="text-3xl font-bold text-gray-800 mb-2">The Latest from Rheem</h2>
        <p className="text-gray-600 mb-12">Browse the latest news and events from Rheem</p>
        
        <div className="grid md:grid-cols-3 gap-8">
          {news.map((article, index) => (
            <div key={index} className="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
              <div className="h-48 bg-cover bg-center" style={{backgroundImage: `url(${article.image})`}}></div>
              <div className="p-6">
                <h3 className="text-lg font-semibold mb-2 text-gray-800">{article.title}</h3>
                <p className="text-gray-600 text-sm mb-4">{article.excerpt}</p>
                <p className="text-red-600 text-sm font-medium">{article.date}</p>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

// Energy Star Section
export const EnergyStarSection = () => {
  return (
    <section className="py-16 bg-gradient-to-r from-blue-100 to-white">
      <div className="container mx-auto px-4">
        <div className="text-center max-w-4xl mx-auto">
          <h2 className="text-sm font-semibold text-gray-600 mb-2">INNOVATION</h2>
          <h3 className="text-4xl font-bold text-gray-800 mb-6">
            Energy Star Certified ⭐
          </h3>
          <p className="text-gray-600 mb-8 text-lg">
            Rheem offers a wide assortment of EnergyStar-certified heating, cooling and water heating solutions designed to help you use less energy—and save more money.
          </p>
          <button className="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition text-lg font-semibold">
            Learn more
          </button>
        </div>
      </div>
    </section>
  );
};

// Products Section
export const ProductsSection = () => {
  const products = [
    {
      name: "Water Heaters",
      description: "Reliable hot water solutions for residential and commercial use",
      image: "https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1",
      features: ["Tank & Tankless", "Energy Efficient", "10-Year Warranty"]
    },
    {
      name: "HVAC Systems", 
      description: "Complete heating and cooling solutions for optimal comfort",
      image: "https://images.unsplash.com/photo-1601520525418-4d7ff1314879",
      features: ["High SEER Rating", "Smart Controls", "Quiet Operation"]
    },
    {
      name: "Fire Safety Systems",
      description: "Advanced fire protection and safety equipment",
      image: "https://images.unsplash.com/photo-1606613816974-93057c2ad2b6",
      features: ["Sprinkler Systems", "Fire Alarms", "Emergency Lighting"]
    },
    {
      name: "Building Automation",
      description: "Smart building management and control systems",
      image: "https://images.pexels.com/photos/7723554/pexels-photo-7723554.jpeg",
      features: ["Smart Controls", "Energy Management", "Remote Monitoring"]
    }
  ];

  return (
    <section className="py-16 bg-gray-50">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <h2 className="text-3xl font-bold text-gray-800 mb-4">Our Products</h2>
          <p className="text-gray-600 text-lg">Discover our comprehensive range of engineering solutions</p>
        </div>
        
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          {products.map((product, index) => (
            <div key={index} className="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
              <div className="h-48 bg-cover bg-center" style={{backgroundImage: `url(${product.image})`}}></div>
              <div className="p-6">
                <h3 className="text-xl font-semibold mb-2 text-gray-800">{product.name}</h3>
                <p className="text-gray-600 mb-4">{product.description}</p>
                <ul className="text-sm text-gray-600 mb-4">
                  {product.features.map((feature, idx) => (
                    <li key={idx} className="flex items-center mb-1">
                      <span className="text-red-600 mr-2">✓</span>
                      {feature}
                    </li>
                  ))}
                </ul>
                <button className="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                  Request Quote
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

// Footer
export const Footer = () => {
  return (
    <footer className="bg-gray-800 text-white py-16">
      <div className="container mx-auto px-4">
        <div className="grid md:grid-cols-4 gap-8">
          <div>
            <h3 className="text-xl font-bold mb-4">FAQ</h3>
            <p className="text-gray-400">
              Frequently asked questions about Heating, Cooling & Water Heating Systems
            </p>
          </div>
          <div>
            <h3 className="text-xl font-bold mb-4">Find a Part</h3>
            <p className="text-gray-400">
              Find replacement parts to help your Rheem product run smoothly
            </p>
          </div>
          <div>
            <h3 className="text-xl font-bold mb-4">Financing</h3>
            <p className="text-gray-400">
              Explore financing options to help make your project more affordable
            </p>
          </div>
          <div>
            <h3 className="text-xl font-bold mb-4">Warranties</h3>
            <p className="text-gray-400">
              Register or verify your Rheem warranty terms and coverage
            </p>
          </div>
        </div>
        
        <div className="border-t border-gray-700 mt-12 pt-8">
          <div className="grid md:grid-cols-3 gap-8">
            <div>
              <h4 className="text-lg font-semibold mb-4">EINSPOT SOLUTIONS NIG LTD</h4>
              <p className="text-gray-400 mb-2">📍 Lagos, Nigeria</p>
              <p className="text-gray-400 mb-2">📞 +234 812 364 7982</p>
              <p className="text-gray-400 mb-2">📧 info@einspot.com.ng</p>
              <p className="text-gray-400">📧 info@einspot.com</p>
            </div>
            <div>
              <h4 className="text-lg font-semibold mb-4">Quick Links</h4>
              <ul className="text-gray-400 space-y-2">
                <li><a href="#" className="hover:text-white">About Us</a></li>
                <li><a href="#" className="hover:text-white">Products</a></li>
                <li><a href="#" className="hover:text-white">Services</a></li>
                <li><a href="#" className="hover:text-white">Projects</a></li>
                <li><a href="#" className="hover:text-white">Blog</a></li>
              </ul>
            </div>
            <div>
              <h4 className="text-lg font-semibold mb-4">Connect With Us</h4>
              <div className="flex space-x-4">
                <a href="#" className="text-gray-400 hover:text-white">
                  <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                  </svg>
                </a>
                <a href="#" className="text-gray-400 hover:text-white">
                  <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                  </svg>
                </a>
                <a href="#" className="text-gray-400 hover:text-white">
                  <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                  </svg>
                </a>
              </div>
            </div>
          </div>
        </div>
        
        <div className="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2025 EINSPOT SOLUTIONS NIG LTD. All rights reserved. | Privacy Policy | Terms of Service</p>
        </div>
      </div>
    </footer>
  );
};

// WhatsApp Chat Button
export const WhatsAppButton = () => {
  const handleWhatsAppClick = () => {
    const message = encodeURIComponent("Hello Einspot, I'd like to request a quote for your services.");
    window.open(`https://wa.me/2348123647982?text=${message}`, '_blank');
  };

  return (
    <div className="fixed bottom-6 right-6 z-50">
      <button 
        onClick={handleWhatsAppClick}
        className="bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-lg transition-all duration-300 hover:scale-110"
      >
        <svg className="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
          <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
        </svg>
      </button>
    </div>
  );
};

// Products Page
export const ProductsPage = () => {
  const productCategories = [
    {
      name: "Water Heaters",
      description: "Reliable hot water solutions for every need",
      image: "https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1",
      products: [
        { name: "Tank Water Heaters", specs: "40-80 gallon capacity", price: "From ₦450,000" },
        { name: "Tankless Water Heaters", specs: "On-demand heating", price: "From ₦680,000" },
        { name: "Heat Pump Water Heaters", specs: "Energy efficient", price: "From ₦850,000" },
        { name: "Solar Water Heaters", specs: "Eco-friendly solution", price: "From ₦920,000" }
      ]
    },
    {
      name: "HVAC Systems",
      description: "Complete heating and cooling solutions",
      image: "https://images.unsplash.com/photo-1601520525418-4d7ff1314879",
      products: [
        { name: "Split AC Units", specs: "1-5 HP capacity", price: "From ₦280,000" },
        { name: "Central Air Systems", specs: "Commercial grade", price: "From ₦1,200,000" },
        { name: "Heat Pumps", specs: "All-season comfort", price: "From ₦750,000" },
        { name: "Ductless Mini-Splits", specs: "Zone control", price: "From ₦420,000" }
      ]
    },
    {
      name: "Fire Safety Systems",
      description: "Advanced fire protection solutions",
      image: "https://images.unsplash.com/photo-1606613816974-93057c2ad2b6",
      products: [
        { name: "Sprinkler Systems", specs: "Automatic activation", price: "From ₦350,000" },
        { name: "Fire Alarms", specs: "Smart detection", price: "From ₦150,000" },
        { name: "Emergency Lighting", specs: "Battery backup", price: "From ₦85,000" },
        { name: "Fire Extinguishers", specs: "Various types", price: "From ₦25,000" }
      ]
    },
    {
      name: "Building Automation",
      description: "Smart building management systems",
      image: "https://images.pexels.com/photos/7723554/pexels-photo-7723554.jpeg",
      products: [
        { name: "BMS Controllers", specs: "Central control", price: "From ₦580,000" },
        { name: "Smart Sensors", specs: "Environmental monitoring", price: "From ₦45,000" },
        { name: "Control Panels", specs: "User interface", price: "From ₦320,000" },
        { name: "Automation Software", specs: "Cloud-based", price: "From ₦180,000" }
      ]
    }
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      {/* Hero Section */}
      <section className="bg-red-600 text-white py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl font-bold mb-4">Our Products</h1>
          <p className="text-xl">Discover our comprehensive range of engineering solutions</p>
        </div>
      </section>

      {/* Product Categories */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          {productCategories.map((category, index) => (
            <div key={index} className="mb-16">
              <div className="grid md:grid-cols-2 gap-12 items-center mb-8">
                <div>
                  <h2 className="text-3xl font-bold text-gray-800 mb-4">{category.name}</h2>
                  <p className="text-gray-600 text-lg">{category.description}</p>
                </div>
                <div className="relative">
                  <img 
                    src={category.image} 
                    alt={category.name}
                    className="w-full h-64 object-cover rounded-lg shadow-lg"
                  />
                </div>
              </div>
              
              <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                {category.products.map((product, idx) => (
                  <div key={idx} className="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition">
                    <h3 className="text-xl font-semibold mb-2 text-gray-800">{product.name}</h3>
                    <p className="text-gray-600 mb-4">{product.specs}</p>
                    <p className="text-red-600 font-bold text-lg mb-4">{product.price}</p>
                    <button className="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition">
                      Request Quote
                    </button>
                  </div>
                ))}
              </div>
            </div>
          ))}
        </div>
      </section>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};

// Services Page
export const ServicesPage = () => {
  const services = [
    {
      title: "HVAC System Design & Installation",
      description: "We specialize in the design and professional installation of energy-efficient Heating, Ventilation, and Air Conditioning systems for homes, hotels, commercial buildings, and industries across Nigeria.",
      image: "https://images.unsplash.com/photo-1601520525418-4d7ff1314879",
      icon: "🏠",
      features: ["Energy-efficient systems", "Commercial & residential", "Professional installation", "Maintenance included"],
      whatsappText: "HVAC System Design & Installation"
    },
    {
      title: "Water Heater Supply & Installation (Rheem Certified)",
      description: "As official partners and distributors of Rheem water heating systems, we provide fast delivery, expert installation, and maintenance of electric, gas, and solar water heaters tailored for Nigerian conditions.",
      image: "https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1",
      icon: "💧",
      features: ["Rheem certified distributor", "Electric, gas & solar options", "Fast delivery", "Expert installation"],
      whatsappText: "Water Heater Supply & Installation (Rheem Certified)"
    },
    {
      title: "Fire Safety & Protection Systems",
      description: "Design, supply, and installation of certified fire detection and suppression systems including sprinklers, fire alarms, extinguishers, and hose reels compliant with global safety standards.",
      image: "https://images.unsplash.com/photo-1606613816974-93057c2ad2b6",
      icon: "🚨",
      features: ["Fire detection systems", "Sprinkler installation", "Safety compliance", "Emergency equipment"],
      whatsappText: "Fire Safety & Protection Systems"
    },
    {
      title: "Electrical Wiring & Smart Lighting",
      description: "Professional electrical wiring for buildings using fire-rated materials, circuit protection systems, and modern smart lighting automation setups.",
      image: "https://images.pexels.com/photos/7078360/pexels-photo-7078360.jpeg",
      icon: "💡",
      features: ["Fire-rated materials", "Smart automation", "Circuit protection", "Modern lighting"],
      whatsappText: "Electrical Wiring & Smart Lighting"
    },
    {
      title: "Plumbing & Drainage Systems",
      description: "Complete plumbing design and installation for residential and commercial structures, including piping, fittings, pressure systems, and water treatment units.",
      image: "https://images.unsplash.com/photo-1566446896748-6075a87760c1",
      icon: "🔧",
      features: ["Complete plumbing design", "Pressure systems", "Water treatment", "Quality fittings"],
      whatsappText: "Plumbing & Drainage Systems"
    },
    {
      title: "Building Automation & Smart Controls (BMS)",
      description: "We deploy advanced Building Management Systems (BMS) for automating HVAC, lighting, security, and access control using IoT-enabled systems for energy efficiency and monitoring.",
      image: "https://images.pexels.com/photos/7723554/pexels-photo-7723554.jpeg",
      icon: "🏢",
      features: ["IoT-enabled systems", "Energy efficiency", "Remote monitoring", "Smart automation"],
      whatsappText: "Building Automation & Smart Controls (BMS)"
    },
    {
      title: "Pipes, Valves & Fittings (Industrial & Domestic)",
      description: "Supply and distribution of high-quality industrial and residential pipework systems, valves, connectors, and fittings with corrosion resistance.",
      image: "https://images.pexels.com/photos/8469943/pexels-photo-8469943.jpeg",
      icon: "⚙️",
      features: ["High-quality materials", "Corrosion resistance", "Industrial grade", "Domestic solutions"],
      whatsappText: "Pipes, Valves & Fittings"
    },
    {
      title: "Consultation & Project Supervision",
      description: "Technical consultation and supervisory services for engineering installations, procurement, and quality control. From design review to contractor evaluation and installation supervision.",
      image: "https://images.unsplash.com/photo-1573166801077-d98391a43199",
      icon: "📋",
      features: ["Technical consultation", "Quality control", "Design review", "Installation supervision"],
      whatsappText: "Consultation & Project Supervision"
    }
  ];

  const handleWhatsAppClick = (serviceName) => {
    const message = encodeURIComponent(`Hello EINSPOT, I'd like a quote for ${serviceName}.
Name: [ ]
Company: [ ]
Location: [ ]
Details: [ ]`);
    window.open(`https://wa.me/2348123647982?text=${message}`, '_blank');
  };

  return (
    <div className="min-h-screen bg-white">
      <Header />
      
      {/* Hero Section */}
      <section className="bg-red-600 text-white py-20">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold mb-6">Our Services</h1>
          <p className="text-xl max-w-3xl mx-auto">Professional engineering solutions for all your building needs across Nigeria</p>
        </div>
      </section>

      {/* Services Grid */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12">
            {services.map((service, index) => (
              <div key={index} className={`group hover:shadow-2xl transition-all duration-500 ${index % 2 === 0 ? 'md:translate-y-8' : ''}`}>
                <div className="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                  <div className="relative h-64 overflow-hidden">
                    <img 
                      src={service.image} 
                      alt={service.title}
                      className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    />
                    <div className="absolute inset-0 bg-black bg-opacity-40 group-hover:bg-opacity-20 transition-all duration-300"></div>
                    <div className="absolute top-4 left-4 bg-red-600 text-white p-3 rounded-full">
                      <span className="text-2xl">{service.icon}</span>
                    </div>
                  </div>
                  
                  <div className="p-8">
                    <h3 className="text-2xl font-bold text-gray-800 mb-4 group-hover:text-red-600 transition-colors">
                      {service.title}
                    </h3>
                    <p className="text-gray-600 mb-6 leading-relaxed">
                      {service.description}
                    </p>
                    
                    <div className="mb-6">
                      <h4 className="text-lg font-semibold mb-3 text-gray-800">Key Features:</h4>
                      <div className="grid grid-cols-2 gap-2">
                        {service.features.map((feature, idx) => (
                          <div key={idx} className="flex items-center text-gray-600">
                            <span className="text-red-600 mr-2 font-bold">✓</span>
                            <span className="text-sm">{feature}</span>
                          </div>
                        ))}
                      </div>
                    </div>
                    
                    <button 
                      onClick={() => handleWhatsAppClick(service.whatsappText)}
                      className="w-full bg-red-600 text-white py-4 rounded-lg hover:bg-red-700 transition-all duration-300 hover:shadow-lg hover:scale-105 font-semibold text-lg"
                    >
                      Request a Quote
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Why Choose Us Section */}
      <section className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-800 mb-6">Why Choose EINSPOT?</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              We combine technical expertise with reliable service delivery to ensure your projects succeed
            </p>
          </div>
          
          <div className="grid md:grid-cols-4 gap-8">
            {[
              { icon: "🏆", title: "10+ Years Experience", desc: "Proven track record in Nigerian market" },
              { icon: "🔧", title: "Expert Team", desc: "Certified engineers and technicians" },
              { icon: "⚡", title: "Fast Delivery", desc: "Quick turnaround on all projects" },
              { icon: "🛡️", title: "Quality Guarantee", desc: "100% satisfaction guaranteed" }
            ].map((item, index) => (
              <div key={index} className="text-center group hover:scale-105 transition-transform duration-300">
                <div className="bg-white rounded-2xl p-8 shadow-lg group-hover:shadow-xl">
                  <div className="text-4xl mb-4">{item.icon}</div>
                  <h3 className="text-xl font-bold text-gray-800 mb-2">{item.title}</h3>
                  <p className="text-gray-600">{item.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};

// About Page
export const AboutPage = () => {
  const teamMembers = [
    {
      name: "Engr. John Adebayo",
      position: "Managing Director",
      image: "https://images.unsplash.com/photo-1573166801077-d98391a43199",
      bio: "20+ years experience in HVAC and building engineering"
    },
    {
      name: "Engr. Sarah Okonkwo",
      position: "Chief Technical Officer",
      image: "https://images.unsplash.com/photo-1580943795425-a4d2f4101c76",
      bio: "Expert in fire safety and building automation systems"
    },
    {
      name: "Engr. Michael Taiwo",
      position: "Project Manager",
      image: "https://images.pexels.com/photos/8469943/pexels-photo-8469943.jpeg",
      bio: "Specialized in large-scale commercial installations"
    }
  ];

  return (
    <div className="min-h-screen bg-white">
      <Header />
      
      {/* Hero Section */}
      <section className="bg-red-600 text-white py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl font-bold mb-4">About EINSPOT SOLUTIONS</h1>
          <p className="text-xl">Engineering Excellence Since 2015</p>
        </div>
      </section>

      {/* Company Story */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl font-bold text-gray-800 mb-6">Our Story</h2>
              <p className="text-gray-600 mb-4">
                Founded in 2015, EINSPOT SOLUTIONS NIG LTD has been at the forefront of engineering excellence in Nigeria. 
                We specialize in providing comprehensive HVAC, water heating, fire safety, and building automation solutions.
              </p>
              <p className="text-gray-600 mb-4">
                As authorized distributors of Rheem products, we bring world-class engineering solutions to Nigerian homes and businesses. 
                Our commitment to quality, reliability, and customer satisfaction has made us a trusted partner in the industry.
              </p>
              <p className="text-gray-600">
                Today, we continue to innovate and expand our services, ensuring that every project meets the highest standards 
                of engineering excellence and customer satisfaction.
              </p>
            </div>
            <div>
              <img 
                src="https://images.unsplash.com/photo-1657571484151-41be42fa72f5" 
                alt="Our facility"
                className="w-full h-96 object-cover rounded-lg shadow-lg"
              />
            </div>
          </div>
        </div>
      </section>

      {/* Mission & Vision */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12">
            <div className="bg-white p-8 rounded-lg shadow-lg">
              <h3 className="text-2xl font-bold text-gray-800 mb-4">Our Mission</h3>
              <p className="text-gray-600">
                To provide innovative, reliable, and sustainable engineering solutions that improve the quality of life 
                for our customers while contributing to Nigeria's infrastructural development.
              </p>
            </div>
            <div className="bg-white p-8 rounded-lg shadow-lg">
              <h3 className="text-2xl font-bold text-gray-800 mb-4">Our Vision</h3>
              <p className="text-gray-600">
                To be the leading engineering solutions provider in West Africa, known for excellence, innovation, 
                and unwavering commitment to customer satisfaction.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold text-gray-800 text-center mb-12">Our Core Values</h2>
          <div className="grid md:grid-cols-4 gap-8">
            {[
              { title: "Quality", icon: "⭐", description: "We never compromise on the quality of our products and services" },
              { title: "Reliability", icon: "🔧", description: "Our solutions are built to last and perform consistently" },
              { title: "Innovation", icon: "💡", description: "We embrace new technologies and continuously improve our offerings" },
              { title: "Integrity", icon: "🤝", description: "We conduct business with honesty, transparency, and ethical practices" }
            ].map((value, index) => (
              <div key={index} className="text-center">
                <div className="text-4xl mb-4">{value.icon}</div>
                <h3 className="text-xl font-semibold text-gray-800 mb-2">{value.title}</h3>
                <p className="text-gray-600">{value.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Team */}
      <section className="py-16 bg-gray-50">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold text-gray-800 text-center mb-12">Our Leadership Team</h2>
          <div className="grid md:grid-cols-3 gap-8">
            {teamMembers.map((member, index) => (
              <div key={index} className="bg-white rounded-lg shadow-lg overflow-hidden">
                <div className="h-64 bg-cover bg-center" style={{backgroundImage: `url(${member.image})`}}></div>
                <div className="p-6">
                  <h3 className="text-xl font-semibold text-gray-800 mb-2">{member.name}</h3>
                  <p className="text-red-600 font-medium mb-3">{member.position}</p>
                  <p className="text-gray-600">{member.bio}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Certifications */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold text-gray-800 text-center mb-12">Certifications & Partnerships</h2>
          <div className="grid md:grid-cols-4 gap-8 text-center">
            <div className="bg-gray-50 p-6 rounded-lg">
              <h3 className="font-semibold text-gray-800 mb-2">ISO 9001:2015</h3>
              <p className="text-gray-600">Quality Management System</p>
            </div>
            <div className="bg-gray-50 p-6 rounded-lg">
              <h3 className="font-semibold text-gray-800 mb-2">Rheem Certified</h3>
              <p className="text-gray-600">Authorized Distributor</p>
            </div>
            <div className="bg-gray-50 p-6 rounded-lg">
              <h3 className="font-semibold text-gray-800 mb-2">COREN Registered</h3>
              <p className="text-gray-600">Council for Regulation of Engineering</p>
            </div>
            <div className="bg-gray-50 p-6 rounded-lg">
              <h3 className="font-semibold text-gray-800 mb-2">NFPA Member</h3>
              <p className="text-gray-600">National Fire Protection Association</p>
            </div>
          </div>
        </div>
      </section>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};

// Projects Page
export const ProjectsPage = () => {
  const projects = [
    {
      title: "HVAC System – Almat Farms Hotel",
      client: "Almat Farms & Resort",
      location: "Abuja, Nigeria",
      duration: "3 Months",
      status: "Completed",
      type: "HVAC System (Design & Installation)",
      image: "https://images.unsplash.com/photo-1601520525418-4d7ff1314879",
      description: "Installed a complete HVAC system for 5-star guest comfort at Almat Farms Hotel. Used energy-efficient ducted split units, zoning controls, and ceiling diffuser outlets.",
      brandsUsed: ["Rheem", "LG VRF"],
      technologies: ["Ducted Split Units", "Zoning Controls", "Ceiling Diffusers", "Energy Management"],
      size: "large",
      whatsappText: "HVAC System similar to Almat Farms Hotel project"
    },
    {
      title: "Rheem Water Heater Setup – Private Residence",
      client: "Mr. Emeka Okafor",
      location: "Lekki, Lagos",
      duration: "2 Weeks",
      status: "Completed",
      type: "Water Heater Installation",
      image: "https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1",
      description: "Installed 3 Rheem Electric Tankless Water Heaters in a duplex. Delivered within 48 hours, full electrical compatibility check, and post-installation testing.",
      brandsUsed: ["Rheem"],
      technologies: ["Tankless Water Heaters", "Electrical Integration", "Performance Testing"],
      size: "small",
      whatsappText: "Rheem Water Heater Installation similar to Lekki project"
    },
    {
      title: "Fire Suppression System – Microfinance HQ",
      client: "RoyalMicro Bank HQ",
      location: "Port Harcourt",
      duration: "1 Month",
      status: "Ongoing",
      type: "Fire Safety System",
      image: "https://images.unsplash.com/photo-1606613816974-93057c2ad2b6",
      description: "Deploying a full fire suppression system including smoke detectors, fire alarm panel, pressurized hydrants, and sprinkler network for HQ building safety compliance.",
      brandsUsed: ["Honeywell", "Tyco"],
      technologies: ["Smoke Detectors", "Fire Alarm Panel", "Sprinkler Network", "Pressurized Hydrants"],
      size: "small",
      whatsappText: "Fire Safety System similar to RoyalMicro Bank project"
    },
    {
      title: "Smart Lighting & BMS – Prime Apartments",
      client: "Prime Apartments Ltd",
      location: "Gwarinpa, Abuja",
      duration: "5 Weeks",
      status: "Completed",
      type: "Smart Lighting / BMS",
      image: "https://images.pexels.com/photos/7723554/pexels-photo-7723554.jpeg",
      description: "Installed motion-triggered lights, smart switches, mobile app controls, and IoT building monitoring dashboard. Reduced energy waste by 35%.",
      brandsUsed: ["Schneider Electric", "Zigbee"],
      technologies: ["Motion Sensors", "Smart Switches", "IoT Dashboard", "Mobile App Control"],
      size: "large",
      whatsappText: "Smart Lighting & BMS similar to Prime Apartments project"
    },
    {
      title: "Industrial Plumbing – Manufacturing Complex",
      client: "Delta Manufacturing Ltd",
      location: "Kano, Nigeria",
      duration: "4 Weeks",
      status: "Completed",
      type: "Industrial Plumbing",
      image: "https://images.pexels.com/photos/8469943/pexels-photo-8469943.jpeg",
      description: "Large-scale industrial plumbing system for manufacturing facility with specialized pipe networks, pressure systems, and water treatment integration.",
      brandsUsed: ["Grundfos", "Pentair"],
      technologies: ["Industrial Piping", "Pressure Systems", "Water Treatment", "Quality Control"],
      size: "small",
      whatsappText: "Industrial Plumbing similar to Delta Manufacturing project"
    },
    {
      title: "Comprehensive Building Services – Tech Hub",
      client: "Innovation Hub Lagos",
      location: "Victoria Island, Lagos",
      duration: "6 Months",
      status: "Completed",
      type: "Multi-System Integration",
      image: "https://images.unsplash.com/photo-1657571484151-41be42fa72f5",
      description: "Complete building services including HVAC, electrical, plumbing, fire safety, and smart building automation for modern tech hub facility.",
      brandsUsed: ["Rheem", "Schneider", "Johnson Controls"],
      technologies: ["HVAC Integration", "Smart Controls", "Fire Safety", "Building Automation"],
      size: "small",
      whatsappText: "Multi-System Integration similar to Innovation Hub project"
    }
  ];

  const handleWhatsAppClick = (projectText) => {
    const message = encodeURIComponent(`Hello EINSPOT, I'd like a quote for ${projectText}.
Name: [ ]
Company: [ ]
Location: [ ]
Details: [ ]`);
    window.open(`https://wa.me/2348123647982?text=${message}`, '_blank');
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      {/* Hero Section */}
      <section className="bg-red-600 text-white py-20">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold mb-6">Our Projects</h1>
          <p className="text-xl max-w-3xl mx-auto">Showcasing our engineering excellence across Nigeria with real projects and satisfied clients</p>
        </div>
      </section>

      {/* Projects Statistics */}
      <section className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-4 gap-8 text-center">
            {[
              { number: "150+", label: "Projects Completed", icon: "🏆" },
              { number: "50+", label: "Happy Clients", icon: "😊" },
              { number: "10+", label: "Years Experience", icon: "📅" },
              { number: "24/7", label: "Support Available", icon: "🚀" }
            ].map((stat, index) => (
              <div key={index} className="bg-gray-50 rounded-2xl p-8 hover:shadow-lg transition-shadow">
                <div className="text-4xl mb-4">{stat.icon}</div>
                <div className="text-3xl font-bold text-red-600 mb-2">{stat.number}</div>
                <div className="text-gray-600 font-medium">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Bento Grid Projects */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-800 mb-6">Featured Projects</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Real projects, real results. See how we've helped businesses across Nigeria achieve their engineering goals.
            </p>
          </div>
          
          {/* Bento Grid Layout */}
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 auto-rows-fr">
            {projects.map((project, index) => (
              <div 
                key={index} 
                className={`group relative overflow-hidden rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:scale-105 ${
                  project.size === 'large' ? 'lg:col-span-2 lg:row-span-2' : ''
                }`}
              >
                <div className="relative h-full min-h-[400px]">
                  <img 
                    src={project.image} 
                    alt={project.title}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-80"></div>
                  
                  {/* Status Badge */}
                  <div className="absolute top-4 right-4">
                    <span className={`px-4 py-2 rounded-full text-sm font-bold ${
                      project.status === 'Completed' 
                        ? 'bg-green-500 text-white' 
                        : 'bg-yellow-500 text-black'
                    }`}>
                      {project.status}
                    </span>
                  </div>
                  
                  {/* Project Info */}
                  <div className="absolute bottom-0 left-0 right-0 p-6 text-white">
                    <div className="mb-2">
                      <span className="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                        {project.type}
                      </span>
                    </div>
                    
                    <h3 className="text-xl font-bold mb-2 group-hover:text-red-400 transition-colors">
                      {project.title}
                    </h3>
                    
                    <div className="flex items-center text-sm text-gray-300 mb-3">
                      <span>📍 {project.location}</span>
                      <span className="mx-2">•</span>
                      <span>🏢 {project.client}</span>
                    </div>
                    
                    <p className="text-gray-200 mb-4 leading-relaxed line-clamp-3">
                      {project.description}
                    </p>
                    
                    <div className="flex flex-wrap gap-2 mb-4">
                      {project.brandsUsed.map((brand, idx) => (
                        <span key={idx} className="bg-white/20 text-white px-3 py-1 rounded-full text-xs">
                          {brand}
                        </span>
                      ))}
                    </div>
                    
                    <div className="flex gap-2">
                      <button 
                        onClick={() => handleWhatsAppClick(project.whatsappText)}
                        className="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors font-medium"
                      >
                        Request Similar Service
                      </button>
                      <button className="bg-white/20 text-white py-2 px-4 rounded-lg hover:bg-white/30 transition-colors">
                        View Details
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Technologies & Brands */}
      <section className="py-20 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-16">
            <h2 className="text-4xl font-bold text-gray-800 mb-6">Technologies & Brands We Work With</h2>
            <p className="text-xl text-gray-600">We partner with leading global brands to deliver excellence</p>
          </div>
          
          <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8">
            {[
              "Rheem", "Schneider Electric", "LG VRF", "Honeywell", 
              "Johnson Controls", "Grundfos", "Pentair", "Tyco", 
              "Zigbee", "Siemens", "ABB", "Mitsubishi"
            ].map((brand, index) => (
              <div key={index} className="bg-gray-50 rounded-lg p-6 text-center hover:shadow-lg transition-shadow">
                <div className="text-red-600 font-bold text-lg">{brand}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Call to Action */}
      <section className="py-20 bg-red-600 text-white">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl font-bold mb-6">Ready to Start Your Project?</h2>
          <p className="text-xl mb-8 max-w-3xl mx-auto">
            Join our growing list of satisfied clients. Let's discuss how we can bring your engineering vision to life with the same excellence shown in our featured projects.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button 
              onClick={() => handleWhatsAppClick("a new project consultation")}
              className="bg-white text-red-600 px-8 py-4 rounded-lg hover:bg-gray-100 transition-colors font-bold text-lg"
            >
              Get Project Quote
            </button>
            <Link to="/contact" className="border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-white hover:text-red-600 transition-colors font-bold text-lg">
              Contact Our Team
            </Link>
          </div>
        </div>
      </section>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};

// Blog Page
export const BlogPage = () => {
  const blogPosts = [
    {
      title: "The Future of Smart Building Automation in Nigeria",
      excerpt: "Exploring how building automation systems are revolutionizing energy efficiency and comfort in Nigerian commercial and residential buildings.",
      date: "June 15, 2025",
      author: "Engr. Sarah Okonkwo",
      image: "https://images.pexels.com/photos/7723554/pexels-photo-7723554.jpeg",
      category: "Technology",
      readTime: "5 min read"
    },
    {
      title: "Choosing the Right Water Heater for Your Home",
      excerpt: "A comprehensive guide to selecting between tank, tankless, and heat pump water heaters based on your family's needs and budget.",
      date: "June 10, 2025",
      author: "Engr. Michael Taiwo",
      image: "https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1",
      category: "Home Solutions",
      readTime: "7 min read"
    },
    {
      title: "Fire Safety Regulations in Nigerian Buildings",
      excerpt: "Understanding the latest fire safety requirements and how to ensure your building meets all necessary compliance standards.",
      date: "June 5, 2025",
      author: "Engr. John Adebayo",
      image: "https://images.unsplash.com/photo-1606613816974-93057c2ad2b6",
      category: "Safety",
      readTime: "8 min read"
    },
    {
      title: "Energy Efficiency: HVAC Systems That Save Money",
      excerpt: "Discover how modern HVAC systems can significantly reduce your energy bills while maintaining optimal comfort levels.",
      date: "May 28, 2025",
      author: "Engr. Sarah Okonkwo",
      image: "https://images.unsplash.com/photo-1601520525418-4d7ff1314879",
      category: "Energy",
      readTime: "6 min read"
    },
    {
      title: "Maintenance Tips for Long-lasting Equipment",
      excerpt: "Essential maintenance practices that extend the life of your HVAC and water heating systems while ensuring optimal performance.",
      date: "May 22, 2025",
      author: "Engr. Michael Taiwo",
      image: "https://images.pexels.com/photos/7078360/pexels-photo-7078360.jpeg",
      category: "Maintenance",
      readTime: "4 min read"
    },
    {
      title: "Sustainable Engineering Solutions for Modern Nigeria",
      excerpt: "How sustainable engineering practices are shaping the future of construction and infrastructure development in Nigeria.",
      date: "May 15, 2025",
      author: "Engr. John Adebayo",
      image: "https://images.unsplash.com/photo-1580943795425-a4d2f4101c76",
      category: "Sustainability",
      readTime: "9 min read"
    }
  ];

  const categories = ["All", "Technology", "Home Solutions", "Safety", "Energy", "Maintenance", "Sustainability"];
  const [selectedCategory, setSelectedCategory] = useState("All");

  const filteredPosts = selectedCategory === "All" 
    ? blogPosts 
    : blogPosts.filter(post => post.category === selectedCategory);

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      {/* Hero Section */}
      <section className="bg-red-600 text-white py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl font-bold mb-4">Engineering Insights</h1>
          <p className="text-xl">Stay updated with the latest trends and tips in engineering solutions</p>
        </div>
      </section>

      {/* Category Filter */}
      <section className="py-8 bg-white">
        <div className="container mx-auto px-4">
          <div className="flex flex-wrap justify-center gap-4">
            {categories.map((category) => (
              <button
                key={category}
                onClick={() => setSelectedCategory(category)}
                className={`px-6 py-2 rounded-full font-medium transition ${
                  selectedCategory === category
                    ? 'bg-red-600 text-white'
                    : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                }`}
              >
                {category}
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Blog Posts */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {filteredPosts.map((post, index) => (
              <article key={index} className="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                <div className="h-48 bg-cover bg-center" style={{backgroundImage: `url(${post.image})`}}>
                  <div className="h-full bg-black bg-opacity-40 flex items-start justify-end p-4">
                    <span className="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                      {post.category}
                    </span>
                  </div>
                </div>
                <div className="p-6">
                  <h2 className="text-xl font-semibold text-gray-800 mb-3 line-clamp-2">{post.title}</h2>
                  <p className="text-gray-600 mb-4 line-clamp-3">{post.excerpt}</p>
                  <div className="flex items-center text-sm text-gray-500 mb-4">
                    <span>{post.author}</span>
                    <span className="mx-2">•</span>
                    <span>{post.date}</span>
                    <span className="mx-2">•</span>
                    <span>{post.readTime}</span>
                  </div>
                  <button className="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    Read More
                  </button>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>

      {/* Newsletter Signup */}
      <section className="py-16 bg-red-600 text-white">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-3xl font-bold mb-4">Stay Updated</h2>
          <p className="text-xl mb-8">Subscribe to our newsletter for the latest engineering insights and industry updates</p>
          <div className="max-w-md mx-auto flex">
            <input
              type="email"
              placeholder="Enter your email"
              className="flex-1 px-4 py-3 rounded-l-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-white"
            />
            <button className="bg-white text-red-600 px-6 py-3 rounded-r-lg hover:bg-gray-100 transition font-semibold">
              Subscribe
            </button>
          </div>
        </div>
      </section>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};

// Contact Page
export const ContactPage = () => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    company: '',
    service: '',
    message: ''
  });

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    // Handle form submission here
    console.log('Form submitted:', formData);
    alert('Thank you for your message! We will get back to you soon.');
    setFormData({
      name: '',
      email: '',
      phone: '',
      company: '',
      service: '',
      message: ''
    });
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      {/* Hero Section */}
      <section className="bg-red-600 text-white py-16">
        <div className="container mx-auto px-4 text-center">
          <h1 className="text-4xl font-bold mb-4">Contact Us</h1>
          <p className="text-xl">Get in touch with our engineering experts</p>
        </div>
      </section>

      {/* Contact Form & Info */}
      <section className="py-16">
        <div className="container mx-auto px-4">
          <div className="grid md:grid-cols-2 gap-12">
            {/* Contact Form */}
            <div className="bg-white rounded-lg shadow-lg p-8">
              <h2 className="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">Name *</label>
                    <input
                      type="text"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                  </div>
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">Email *</label>
                    <input
                      type="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                  </div>
                </div>
                
                <div className="grid md:grid-cols-2 gap-4">
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">Phone</label>
                    <input
                      type="tel"
                      name="phone"
                      value={formData.phone}
                      onChange={handleChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                  </div>
                  <div>
                    <label className="block text-gray-700 font-medium mb-2">Company</label>
                    <input
                      type="text"
                      name="company"
                      value={formData.company}
                      onChange={handleChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    />
                  </div>
                </div>
                
                <div>
                  <label className="block text-gray-700 font-medium mb-2">Service Interest</label>
                  <select
                    name="service"
                    value={formData.service}
                    onChange={handleChange}
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                  >
                    <option value="">Select a service</option>
                    <option value="hvac">HVAC Systems</option>
                    <option value="water-heating">Water Heating</option>
                    <option value="fire-safety">Fire Safety</option>
                    <option value="building-automation">Building Automation</option>
                    <option value="electrical">Electrical Engineering</option>
                    <option value="plumbing">Plumbing Systems</option>
                  </select>
                </div>
                
                <div>
                  <label className="block text-gray-700 font-medium mb-2">Message *</label>
                  <textarea
                    name="message"
                    value={formData.message}
                    onChange={handleChange}
                    required
                    rows="5"
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                  ></textarea>
                </div>
                
                <button
                  type="submit"
                  className="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition font-semibold"
                >
                  Send Message
                </button>
              </form>
            </div>

            {/* Contact Information */}
            <div className="space-y-8">
              <div className="bg-white rounded-lg shadow-lg p-8">
                <h2 className="text-2xl font-bold text-gray-800 mb-6">Contact Information</h2>
                <div className="space-y-4">
                  <div className="flex items-center">
                    <div className="bg-red-600 text-white p-3 rounded-full mr-4">
                      <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                    </div>
                    <div>
                      <p className="font-medium text-gray-800">Address</p>
                      <p className="text-gray-600">Lagos, Nigeria</p>
                    </div>
                  </div>
                  
                  <div className="flex items-center">
                    <div className="bg-red-600 text-white p-3 rounded-full mr-4">
                      <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                      </svg>
                    </div>
                    <div>
                      <p className="font-medium text-gray-800">Phone</p>
                      <p className="text-gray-600">+234 812 364 7982</p>
                    </div>
                  </div>
                  
                  <div className="flex items-center">
                    <div className="bg-red-600 text-white p-3 rounded-full mr-4">
                      <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <div>
                      <p className="font-medium text-gray-800">Email</p>
                      <p className="text-gray-600">info@einspot.com.ng</p>
                      <p className="text-gray-600">info@einspot.com</p>
                    </div>
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-lg shadow-lg p-8">
                <h3 className="text-xl font-bold text-gray-800 mb-4">Business Hours</h3>
                <div className="space-y-2">
                  <div className="flex justify-between">
                    <span className="text-gray-600">Monday - Friday:</span>
                    <span className="text-gray-800">8:00 AM - 6:00 PM</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Saturday:</span>
                    <span className="text-gray-800">9:00 AM - 4:00 PM</span>
                  </div>
                  <div className="flex justify-between">
                    <span className="text-gray-600">Sunday:</span>
                    <span className="text-gray-800">Closed</span>
                  </div>
                </div>
              </div>

              <div className="bg-white rounded-lg shadow-lg p-8">
                <h3 className="text-xl font-bold text-gray-800 mb-4">Emergency Services</h3>
                <p className="text-gray-600 mb-4">
                  We offer 24/7 emergency support for critical systems. 
                  Call us anytime for urgent repairs and maintenance.
                </p>
                <button className="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition font-semibold">
                  Emergency Contact
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Map Section */}
      <section className="py-16 bg-gray-200">
        <div className="container mx-auto px-4">
          <h2 className="text-3xl font-bold text-gray-800 text-center mb-8">Our Location</h2>
          <div className="bg-white rounded-lg shadow-lg p-4">
            <div className="h-96 bg-gray-300 rounded-lg flex items-center justify-center">
              <p className="text-gray-600">Interactive Map - Lagos, Nigeria</p>
            </div>
          </div>
        </div>
      </section>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};