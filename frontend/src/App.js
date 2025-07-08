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
  WhatsAppButton
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
        </Routes>
      </BrowserRouter>
    </div>
  );
}

export default App;