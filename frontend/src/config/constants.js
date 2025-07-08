// Application constants
export const APP_CONFIG = {
  NAME: 'EINSPOT SOLUTIONS NIG LTD',
  VERSION: '1.0.0',
  DESCRIPTION: 'Leading engineering solutions provider in Nigeria',
  KEYWORDS: 'HVAC Nigeria, Rheem water heaters, fire safety, building automation, engineering services',
  AUTHOR: 'EINSPOT SOLUTIONS NIG LTD',
  WEBSITE: 'https://einspot.com.ng',
  EMAIL: 'info@einspot.com.ng',
  PHONE: '+234 812 364 7982',
  WHATSAPP: '2348123647982',
  ADDRESS: 'Lagos, Nigeria'
};

export const API_CONFIG = {
  BASE_URL: process.env.REACT_APP_BACKEND_URL || 'http://localhost:8001',
  TIMEOUT: 10000,
  RETRY_ATTEMPTS: 3
};

export const THEME_CONFIG = {
  PRIMARY_COLOR: '#D7261E',
  SECONDARY_COLOR: '#FFFFFF',
  ACCENT_COLOR: '#000000',
  SUCCESS_COLOR: '#10B981',
  WARNING_COLOR: '#F59E0B',
  ERROR_COLOR: '#EF4444'
};

export const VALIDATION_RULES = {
  EMAIL_REGEX: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
  PHONE_REGEX: /^\+?[1-9]\d{1,14}$/,
  PASSWORD_MIN_LENGTH: 8,
  MESSAGE_MIN_LENGTH: 10,
  MESSAGE_MAX_LENGTH: 500
};

export const STORAGE_KEYS = {
  AUTH_TOKEN: 'einspot_token',
  USER_DATA: 'einspot_user',
  CART_DATA: 'einspot_cart',
  PREFERENCES: 'einspot_preferences'
};

export const ROUTES = {
  HOME: '/',
  PRODUCTS: '/products',
  SERVICES: '/services',
  PROJECTS: '/projects',
  BLOG: '/blog',
  ABOUT: '/about',
  CONTACT: '/contact',
  LOGIN: '/login',
  REGISTER: '/register',
  DASHBOARD: '/dashboard',
  CART: '/cart',
  SEARCH: '/search'
};

export const HTTP_STATUS = {
  OK: 200,
  CREATED: 201,
  BAD_REQUEST: 400,
  UNAUTHORIZED: 401,
  FORBIDDEN: 403,
  NOT_FOUND: 404,
  INTERNAL_SERVER_ERROR: 500
};