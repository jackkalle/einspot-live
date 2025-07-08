import React from 'react';
import "./App.css";
import { BrowserRouter, Routes, Route } from "react-router-dom";
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

const Home = () => {
  return (
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
    </div>
  );
};

function App() {
  return (
    <div className="App">
      <BrowserRouter>
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/products" element={<ProductsPage />} />
          <Route path="/services" element={<ServicesPage />} />
          <Route path="/about" element={<AboutPage />} />
          <Route path="/projects" element={<ProjectsPage />} />
          <Route path="/blog" element={<BlogPage />} />
          <Route path="/contact" element={<ContactPage />} />
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;