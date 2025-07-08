import React, { useState } from 'react';
import { Link, useLocation } from 'react-router-dom';

// Header Component
export const Header = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const location = useLocation();

  return (
    <header className="bg-white shadow-lg">
      {/* Top Bar */}
      <div className="bg-gray-100 text-sm">
        <div className="container mx-auto px-4 py-2 flex justify-between items-center">
          <div className="flex items-center space-x-4">
            <span>🌍 Nigeria</span>
            <span>📞 +234 812 364 7982</span>
            <span>📧 info@einspot.com.ng</span>
          </div>
          <div className="flex items-center space-x-4">
            <span>Warranties</span>
            <span>Resources</span>
            <span>Tax Credits</span>
            <span>Sustainability</span>
            <span>Careers</span>
          </div>
        </div>
      </div>

      {/* Main Navigation */}
      <nav className="container mx-auto px-4 py-4">
        <div className="flex justify-between items-center">
          <div className="flex items-center">
            <Link to="/" className="text-2xl font-bold text-gray-800">
              EINSPOT <span className="text-red-600">SOLUTIONS</span>
            </Link>
          </div>
          
          <div className="hidden md:flex items-center space-x-8">
            <Link to="/products" className={`font-medium ${location.pathname === '/products' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>Products</Link>
            <Link to="/services" className={`font-medium ${location.pathname === '/services' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>Services</Link>
            <Link to="/projects" className={`font-medium ${location.pathname === '/projects' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>Projects</Link>
            <Link to="/blog" className={`font-medium ${location.pathname === '/blog' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>Blog</Link>
            <Link to="/about" className={`font-medium ${location.pathname === '/about' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>About</Link>
            <Link to="/contact" className={`font-medium ${location.pathname === '/contact' ? 'text-red-600' : 'text-gray-700 hover:text-red-600'}`}>Contact</Link>
          </div>

          <div className="flex items-center space-x-4">
            <input 
              type="text" 
              placeholder="What are you looking for?" 
              className="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
            />
            <button className="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
              Find a Pro
            </button>
          </div>

          <button 
            className="md:hidden"
            onClick={() => setIsMenuOpen(!isMenuOpen)}
          >
            <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
        
        {/* Mobile Menu */}
        {isMenuOpen && (
          <div className="md:hidden mt-4 pb-4 border-t border-gray-200">
            <div className="flex flex-col space-y-2 pt-4">
              <Link to="/products" className="text-gray-700 hover:text-red-600 font-medium py-2">Products</Link>
              <Link to="/services" className="text-gray-700 hover:text-red-600 font-medium py-2">Services</Link>
              <Link to="/projects" className="text-gray-700 hover:text-red-600 font-medium py-2">Projects</Link>
              <Link to="/blog" className="text-gray-700 hover:text-red-600 font-medium py-2">Blog</Link>
              <Link to="/about" className="text-gray-700 hover:text-red-600 font-medium py-2">About</Link>
              <Link to="/contact" className="text-gray-700 hover:text-red-600 font-medium py-2">Contact</Link>
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