// Export all components from a central location for better organization
export { 
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
} from '../components.js';

// Enhanced Components
export { default as LoginPage } from './Auth/LoginPage';
export { default as RegisterPage } from './Auth/RegisterPage';
export { default as UserDashboard } from './Dashboard/UserDashboard';
export { default as CartPage } from './ShoppingCart/CartPage';
export { default as SearchPage } from './Search/SearchPage';
export { default as NotificationSystem } from './Interactive/NotificationSystem';
export { default as LiveChat } from './Interactive/LiveChat';
export { default as SEOWrapper } from './SEO/SEOWrapper';
export { default as LazyImage } from './Performance/LazyImage';
export { default as EnhancedContactForm } from './Forms/EnhancedContactForm';