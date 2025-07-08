import React, { useState, useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import { useApp } from '../../context/AppContext';
import { useDebounce } from '../../utils/performance';
import { updateMetaTags } from '../../utils/seo';
import { Header, Footer, WhatsAppButton } from '../index';

const SearchPage = () => {
  const location = useLocation();
  const { state, actions } = useApp();
  const [searchResults, setSearchResults] = useState([]);
  const [isLoading, setIsLoading] = useState(false);
  const [sortBy, setSortBy] = useState('relevance');
  const [viewMode, setViewMode] = useState('grid');

  // Get search query from URL params
  const queryParams = new URLSearchParams(location.search);
  const initialQuery = queryParams.get('q') || '';
  
  const [searchQuery, setSearchQuery] = useState(initialQuery);
  const debouncedSearchQuery = useDebounce(searchQuery, 300);

  useEffect(() => {
    updateMetaTags({
      title: `Search Results for "${searchQuery}" - EINSPOT SOLUTIONS NIG LTD`,
      description: `Find engineering products and services matching "${searchQuery}". HVAC, water heaters, fire safety, and more.`,
      keywords: `search, ${searchQuery}, engineering products, HVAC, water heaters, Nigeria`
    });
  }, [searchQuery]);

  useEffect(() => {
    if (debouncedSearchQuery) {
      performSearch(debouncedSearchQuery);
    }
  }, [debouncedSearchQuery, state.filters, sortBy]);

  const performSearch = async (query) => {
    setIsLoading(true);
    
    // Mock search results - replace with actual API call
    const mockResults = [
      {
        id: 1,
        name: 'Rheem Electric Water Heater 50L',
        category: 'Water Heaters',
        price: 280000,
        brand: 'Rheem',
        image: 'https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1',
        rating: 4.5,
        description: 'Energy-efficient electric water heater perfect for small to medium homes.',
        inStock: true
      },
      {
        id: 2,
        name: 'HVAC Installation Service',
        category: 'Services',
        price: 450000,
        brand: 'EINSPOT',
        image: 'https://images.unsplash.com/photo-1601520525418-4d7ff1314879',
        rating: 4.8,
        description: 'Professional HVAC system installation with 2-year warranty.',
        inStock: true
      },
      {
        id: 3,
        name: 'Fire Safety Sprinkler System',
        category: 'Fire Safety',
        price: 680000,
        brand: 'Honeywell',
        image: 'https://images.unsplash.com/photo-1606613816974-93057c2ad2b6',
        rating: 4.7,
        description: 'Complete fire suppression system for commercial buildings.',
        inStock: false
      },
      {
        id: 4,
        name: 'Smart Building Automation Kit',
        category: 'Building Automation',
        price: 920000,
        brand: 'Schneider',
        image: 'https://images.pexels.com/photos/7723554/pexels-photo-7723554.jpeg',
        rating: 4.6,
        description: 'IoT-enabled building management system with mobile app control.',
        inStock: true
      }
    ];

    // Filter results based on query and filters
    let filteredResults = mockResults.filter(item =>
      item.name.toLowerCase().includes(query.toLowerCase()) ||
      item.category.toLowerCase().includes(query.toLowerCase()) ||
      item.brand.toLowerCase().includes(query.toLowerCase()) ||
      item.description.toLowerCase().includes(query.toLowerCase())
    );

    // Apply filters
    if (state.filters.category) {
      filteredResults = filteredResults.filter(item => 
        item.category === state.filters.category
      );
    }

    if (state.filters.brand) {
      filteredResults = filteredResults.filter(item => 
        item.brand === state.filters.brand
      );
    }

    if (state.filters.priceRange) {
      const [min, max] = state.filters.priceRange.split('-').map(Number);
      filteredResults = filteredResults.filter(item => 
        item.price >= min && item.price <= max
      );
    }

    // Sort results
    switch (sortBy) {
      case 'price-low':
        filteredResults.sort((a, b) => a.price - b.price);
        break;
      case 'price-high':
        filteredResults.sort((a, b) => b.price - a.price);
        break;
      case 'rating':
        filteredResults.sort((a, b) => b.rating - a.rating);
        break;
      case 'name':
        filteredResults.sort((a, b) => a.name.localeCompare(b.name));
        break;
      default:
        // Keep original order for relevance
        break;
    }

    setTimeout(() => {
      setSearchResults(filteredResults);
      setIsLoading(false);
    }, 500);
  };

  const handleFilterChange = (filterType, value) => {
    actions.setFilters({ [filterType]: value });
  };

  const clearFilters = () => {
    actions.setFilters({ category: '', brand: '', priceRange: '' });
  };

  const renderStars = (rating) => {
    return Array.from({ length: 5 }, (_, i) => (
      <span key={i} className={i < Math.floor(rating) ? 'text-yellow-400' : 'text-gray-300'}>
        ⭐
      </span>
    ));
  };

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      <div className="container mx-auto px-4 py-8">
        {/* Search Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-800 mb-4">
            Search Results
            {searchQuery && <span className="text-red-600"> for "{searchQuery}"</span>}
          </h1>
          
          {/* Search Bar */}
          <div className="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div className="flex gap-4">
              <div className="flex-1 relative">
                <input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search products, services, brands..."
                  className="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                />
                <svg className="w-5 h-5 text-gray-400 absolute left-4 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
              <button 
                onClick={() => performSearch(searchQuery)}
                className="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold"
              >
                Search
              </button>
            </div>
          </div>

          {/* Results Info & Controls */}
          <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div className="flex items-center gap-4">
              <p className="text-gray-600">
                {isLoading ? 'Searching...' : `${searchResults.length} results found`}
              </p>
              {(state.filters.category || state.filters.brand || state.filters.priceRange) && (
                <button 
                  onClick={clearFilters}
                  className="text-red-600 hover:text-red-700 text-sm font-medium"
                >
                  Clear Filters
                </button>
              )}
            </div>
            
            <div className="flex items-center gap-4">
              {/* Sort By */}
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
              >
                <option value="relevance">Sort by Relevance</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="rating">Highest Rated</option>
                <option value="name">Name A-Z</option>
              </select>

              {/* View Mode */}
              <div className="flex border border-gray-300 rounded-lg overflow-hidden">
                <button
                  onClick={() => setViewMode('grid')}
                  className={`p-2 ${viewMode === 'grid' ? 'bg-red-600 text-white' : 'bg-white text-gray-600'}`}
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                  </svg>
                </button>
                <button
                  onClick={() => setViewMode('list')}
                  className={`p-2 ${viewMode === 'list' ? 'bg-red-600 text-white' : 'bg-white text-gray-600'}`}
                >
                  <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <div className="grid lg:grid-cols-4 gap-8">
          {/* Filters Sidebar */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-2xl shadow-lg p-6 sticky top-8">
              <h2 className="text-lg font-semibold text-gray-800 mb-6">Filters</h2>
              
              {/* Category Filter */}
              <div className="mb-6">
                <h3 className="font-medium text-gray-800 mb-3">Category</h3>
                <select
                  value={state.filters.category}
                  onChange={(e) => handleFilterChange('category', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  <option value="">All Categories</option>
                  <option value="Water Heaters">Water Heaters</option>
                  <option value="HVAC">HVAC Systems</option>
                  <option value="Fire Safety">Fire Safety</option>
                  <option value="Building Automation">Building Automation</option>
                  <option value="Services">Services</option>
                </select>
              </div>

              {/* Brand Filter */}
              <div className="mb-6">
                <h3 className="font-medium text-gray-800 mb-3">Brand</h3>
                <select
                  value={state.filters.brand}
                  onChange={(e) => handleFilterChange('brand', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  <option value="">All Brands</option>
                  <option value="Rheem">Rheem</option>
                  <option value="Honeywell">Honeywell</option>
                  <option value="Schneider">Schneider Electric</option>
                  <option value="LG">LG</option>
                  <option value="EINSPOT">EINSPOT</option>
                </select>
              </div>

              {/* Price Range Filter */}
              <div className="mb-6">
                <h3 className="font-medium text-gray-800 mb-3">Price Range (₦)</h3>
                <select
                  value={state.filters.priceRange}
                  onChange={(e) => handleFilterChange('priceRange', e.target.value)}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                >
                  <option value="">All Prices</option>
                  <option value="0-200000">Under ₦200,000</option>
                  <option value="200000-500000">₦200,000 - ₦500,000</option>
                  <option value="500000-1000000">₦500,000 - ₦1,000,000</option>
                  <option value="1000000-999999999">Above ₦1,000,000</option>
                </select>
              </div>

              {/* In Stock Filter */}
              <div className="mb-6">
                <label className="flex items-center">
                  <input type="checkbox" className="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500 mr-2" />
                  <span className="text-gray-700">In Stock Only</span>
                </label>
              </div>
            </div>
          </div>

          {/* Results Grid/List */}
          <div className="lg:col-span-3">
            {isLoading ? (
              <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                {Array.from({ length: 6 }).map((_, i) => (
                  <div key={i} className="bg-white rounded-2xl shadow-lg overflow-hidden animate-pulse">
                    <div className="h-48 bg-gray-200"></div>
                    <div className="p-6">
                      <div className="h-4 bg-gray-200 rounded mb-2"></div>
                      <div className="h-4 bg-gray-200 rounded w-3/4 mb-4"></div>
                      <div className="h-6 bg-gray-200 rounded w-1/2"></div>
                    </div>
                  </div>
                ))}
              </div>
            ) : searchResults.length === 0 ? (
              <div className="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div className="text-6xl mb-6">🔍</div>
                <h2 className="text-2xl font-bold text-gray-800 mb-4">No Results Found</h2>
                <p className="text-gray-600 mb-8">
                  We couldn't find any products matching your search criteria.
                </p>
                <div className="space-y-2 text-gray-600">
                  <p>Try:</p>
                  <ul className="list-disc list-inside space-y-1">
                    <li>Checking your spelling</li>
                    <li>Using different keywords</li>
                    <li>Removing some filters</li>
                  </ul>
                </div>
              </div>
            ) : (
              <div className={viewMode === 'grid' ? 'grid md:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-6'}>
                {searchResults.map((item) => (
                  <div 
                    key={item.id} 
                    className={`bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow ${
                      viewMode === 'list' ? 'flex' : ''
                    }`}
                  >
                    <div className={`${viewMode === 'list' ? 'w-48 h-48' : 'h-48'} bg-gray-200 relative`}>
                      <img 
                        src={item.image} 
                        alt={item.name}
                        className="w-full h-full object-cover"
                      />
                      {!item.inStock && (
                        <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                          <span className="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                            Out of Stock
                          </span>
                        </div>
                      )}
                      <button
                        onClick={() => actions.addToWishlist(item)}
                        className="absolute top-3 right-3 bg-white rounded-full p-2 shadow-lg hover:bg-gray-50 transition-colors"
                      >
                        ❤️
                      </button>
                    </div>
                    
                    <div className="p-6 flex-1">
                      <div className="flex items-start justify-between mb-2">
                        <span className="bg-gray-100 text-gray-700 px-2 py-1 rounded-full text-xs font-medium">
                          {item.category}
                        </span>
                        <div className="flex items-center gap-1">
                          {renderStars(item.rating)}
                          <span className="text-gray-600 text-sm ml-1">({item.rating})</span>
                        </div>
                      </div>
                      
                      <h3 className="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
                        {item.name}
                      </h3>
                      
                      <p className="text-gray-600 text-sm mb-3 line-clamp-2">
                        {item.description}
                      </p>
                      
                      <div className="flex items-center justify-between mb-4">
                        <span className="text-2xl font-bold text-red-600">
                          ₦{item.price.toLocaleString()}
                        </span>
                        <span className="text-gray-500 text-sm">{item.brand}</span>
                      </div>
                      
                      <div className="flex gap-2">
                        <button 
                          onClick={() => actions.addToCart(item)}
                          disabled={!item.inStock}
                          className="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                          {item.inStock ? 'Add to Cart' : 'Out of Stock'}
                        </button>
                        <button className="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                          View
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
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

export default SearchPage;