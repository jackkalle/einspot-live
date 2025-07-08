import React from 'react';
import "./App.css";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import { AppProvider } from './context/AppContext';
import { 
  Header, 
  HeroSection, 
  ServiceCategories, 
  SustainabilitySection, 
  LatestNewsSection,
  EnergyStarSection,
  ProductsSection,
  Footer,
  WhatsAppButton,
  ProductsPage,
  ServicesPage,
  AboutPage,
  ProjectsPage,
  BlogPage,
  ContactPage
} from './components';

// Enhanced Components
import LoginPage from './components/Auth/LoginPage';
import RegisterPage from './components/Auth/RegisterPage';
import UserDashboard from './components/Dashboard/UserDashboard';
import CartPage from './components/ShoppingCart/CartPage';
import SearchPage from './components/Search/SearchPage';
import NotificationSystem from './components/Interactive/NotificationSystem';
import LiveChat from './components/Interactive/LiveChat';
import SEOWrapper from './components/SEO/SEOWrapper';

const Home = () => {
  return (
    <SEOWrapper pageType="home">
      <div className="min-h-screen bg-white">
        <Header />
        <HeroSection />
        <ServiceCategories />
        <SustainabilitySection />
        <ProductsSection />
        <LatestNewsSection />
        <EnergyStarSection />
        <Footer />
        <WhatsAppButton />
        <LiveChat />
      </div>
    </SEOWrapper>
  );
};

function App() {
  return (
    <AppProvider>
      <div className="App">
        <BrowserRouter>
          <NotificationSystem />
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/products" element={
              <SEOWrapper pageType="products">
                <ProductsPage />
              </SEOWrapper>
            } />
            <Route path="/services" element={
              <SEOWrapper pageType="services">
                <ServicesPage />
              </SEOWrapper>
            } />
            <Route path="/about" element={
              <SEOWrapper pageType="about">
                <AboutPage />
              </SEOWrapper>
            } />
            <Route path="/projects" element={
              <SEOWrapper pageType="projects">
                <ProjectsPage />
              </SEOWrapper>
            } />
            <Route path="/blog" element={
              <SEOWrapper pageType="blog">
                <BlogPage />
              </SEOWrapper>
            } />
            <Route path="/contact" element={
              <SEOWrapper pageType="contact">
                <ContactPage />
              </SEOWrapper>
            } />
            <Route path="/login" element={<LoginPage />} />
            <Route path="/register" element={<RegisterPage />} />
            <Route path="/dashboard" element={<UserDashboard />} />
            <Route path="/cart" element={<CartPage />} />
            <Route path="/search" element={<SearchPage />} />
          </Routes>
        </BrowserRouter>
      </div>
    </AppProvider>
  );
}

export default App;