import React, { useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import { updateMetaTags, seoData, generateStructuredData, addStructuredData } from '../../utils/seo';

const SEOWrapper = ({ children, pageType, customSEO = {} }) => {
  const location = useLocation();

  useEffect(() => {
    // Determine page type from pathname if not provided
    const currentPageType = pageType || getPageTypeFromPath(location.pathname);
    
    // Get SEO data for current page
    const pageSEO = seoData[currentPageType] || seoData.home;
    
    // Merge with custom SEO data
    const finalSEO = { ...pageSEO, ...customSEO };
    
    // Update meta tags
    updateMetaTags({
      ...finalSEO,
      url: window.location.href
    });

    // Add structured data
    let structuredData;
    switch (currentPageType) {
      case 'products':
        structuredData = generateStructuredData('service', {
          name: 'Engineering Products',
          description: finalSEO.description,
          serviceType: 'Engineering Products & Solutions'
        });
        break;
      case 'services':
        structuredData = generateStructuredData('service', {
          name: 'Engineering Services',
          description: finalSEO.description,
          serviceType: 'HVAC, Plumbing, Electrical Engineering'
        });
        break;
      case 'blog':
        structuredData = generateStructuredData('article', {
          title: finalSEO.title,
          description: finalSEO.description,
          author: 'EINSPOT Engineering Team',
          datePublished: new Date().toISOString()
        });
        break;
      default:
        structuredData = generateStructuredData('organization');
        break;
    }
    
    addStructuredData(structuredData);

    // Track page view (for analytics)
    if (window.gtag) {
      window.gtag('config', 'GA_MEASUREMENT_ID', {
        page_title: finalSEO.title,
        page_location: window.location.href
      });
    }

  }, [location.pathname, pageType, customSEO]);

  const getPageTypeFromPath = (pathname) => {
    if (pathname.includes('/products')) return 'products';
    if (pathname.includes('/services')) return 'services';
    if (pathname.includes('/projects')) return 'projects';
    if (pathname.includes('/blog')) return 'blog';
    if (pathname.includes('/about')) return 'about';
    if (pathname.includes('/contact')) return 'contact';
    return 'home';
  };

  return <>{children}</>;
};

export default SEOWrapper;