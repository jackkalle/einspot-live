// SEO utility functions

export const updateMetaTags = (metaData) => {
  // Update document title
  if (metaData.title) {
    document.title = metaData.title;
  }

  // Update or create meta tags
  const updateMetaTag = (name, content, property = false) => {
    if (!content) return;
    
    const selector = property ? `meta[property="${name}"]` : `meta[name="${name}"]`;
    let metaTag = document.querySelector(selector);
    
    if (!metaTag) {
      metaTag = document.createElement('meta');
      if (property) {
        metaTag.setAttribute('property', name);
      } else {
        metaTag.setAttribute('name', name);
      }
      document.head.appendChild(metaTag);
    }
    
    metaTag.setAttribute('content', content);
  };

  // Standard meta tags
  updateMetaTag('description', metaData.description);
  updateMetaTag('keywords', metaData.keywords);
  updateMetaTag('author', metaData.author || 'EINSPOT SOLUTIONS NIG LTD');
  updateMetaTag('robots', metaData.robots || 'index, follow');
  
  // Open Graph tags
  updateMetaTag('og:title', metaData.title, true);
  updateMetaTag('og:description', metaData.description, true);
  updateMetaTag('og:type', metaData.type || 'website', true);
  updateMetaTag('og:url', metaData.url || window.location.href, true);
  updateMetaTag('og:image', metaData.image, true);
  updateMetaTag('og:site_name', 'EINSPOT SOLUTIONS NIG LTD', true);
  
  // Twitter Card tags
  updateMetaTag('twitter:card', 'summary_large_image');
  updateMetaTag('twitter:title', metaData.title);
  updateMetaTag('twitter:description', metaData.description);
  updateMetaTag('twitter:image', metaData.image);
  
  // Additional SEO tags
  updateMetaTag('viewport', 'width=device-width, initial-scale=1.0');
  updateMetaTag('theme-color', '#D7261E');
  
  // Canonical URL
  let canonicalLink = document.querySelector('link[rel="canonical"]');
  if (!canonicalLink) {
    canonicalLink = document.createElement('link');
    canonicalLink.setAttribute('rel', 'canonical');
    document.head.appendChild(canonicalLink);
  }
  canonicalLink.setAttribute('href', metaData.url || window.location.href);
};

// Page-specific SEO data
export const seoData = {
  home: {
    title: 'EINSPOT SOLUTIONS NIG LTD - Rheem Products & Engineering Services Nigeria',
    description: 'Leading engineering solutions provider in Nigeria. HVAC systems, water heaters, fire safety, building automation. Official Rheem distributor. Expert installation & maintenance.',
    keywords: 'HVAC Nigeria, Rheem water heaters, fire safety systems, building automation, engineering services Nigeria, air conditioning installation, Lagos engineering, Abuja HVAC',
    image: 'https://images.unsplash.com/photo-1657571484151-41be42fa72f5',
    type: 'website'
  },
  
  products: {
    title: 'Engineering Products - HVAC, Water Heaters, Fire Safety | EINSPOT SOLUTIONS',
    description: 'Browse our comprehensive range of engineering products. Rheem water heaters, HVAC systems, fire safety equipment, building automation solutions. Expert installation available.',
    keywords: 'Rheem products Nigeria, water heaters Lagos, HVAC systems Abuja, fire safety equipment, building automation products, engineering supplies Nigeria',
    image: 'https://images.unsplash.com/photo-1581720604719-ee1b1a4e44b1',
    type: 'website'
  },
  
  services: {
    title: 'Engineering Services - HVAC, Plumbing, Electrical, Fire Safety | EINSPOT',
    description: 'Professional engineering services across Nigeria. HVAC installation, water heater services, fire safety systems, electrical wiring, plumbing, building automation.',
    keywords: 'HVAC installation Nigeria, water heater installation Lagos, fire safety services, electrical engineering Nigeria, plumbing services, BMS installation',
    image: 'https://images.unsplash.com/photo-1601520525418-4d7ff1314879',
    type: 'website'
  },
  
  projects: {
    title: 'Engineering Projects Portfolio - EINSPOT SOLUTIONS NIG LTD',
    description: 'Explore our successful engineering projects across Nigeria. HVAC installations, fire safety systems, building automation, water heating solutions for hotels, offices, residences.',
    keywords: 'engineering projects Nigeria, HVAC installation projects, fire safety projects Lagos, building automation Nigeria, water heater installation cases',
    image: 'https://images.unsplash.com/photo-1606613816974-93057c2ad2b6',
    type: 'website'
  },
  
  blog: {
    title: 'Engineering Insights & News - EINSPOT SOLUTIONS Blog',
    description: 'Latest insights on HVAC technology, fire safety, building automation, and engineering trends in Nigeria. Expert tips and industry updates.',
    keywords: 'engineering blog Nigeria, HVAC technology, fire safety tips, building automation trends, engineering insights Nigeria',
    image: 'https://images.pexels.com/photos/7723554/pexels-photo-7723554.jpeg',
    type: 'blog'
  },
  
  about: {
    title: 'About EINSPOT SOLUTIONS NIG LTD - Leading Engineering Company Nigeria',
    description: 'Learn about EINSPOT SOLUTIONS NIG LTD, Nigerias premier engineering solutions provider. 10+ years of excellence in HVAC, fire safety, and building automation.',
    keywords: 'EINSPOT SOLUTIONS Nigeria, engineering company Lagos, HVAC company Nigeria, about us engineering services, Rheem distributor Nigeria',
    image: 'https://images.unsplash.com/photo-1573166801077-d98391a43199',
    type: 'website'
  },
  
  contact: {
    title: 'Contact EINSPOT SOLUTIONS NIG LTD - Engineering Services Nigeria',
    description: 'Contact EINSPOT SOLUTIONS for professional engineering services. Located in Lagos, Nigeria. Phone: +234 812 364 7982. Email: info@einspot.com.ng',
    keywords: 'contact EINSPOT Nigeria, engineering services contact, HVAC company Lagos contact, fire safety services Nigeria contact, building automation contact',
    image: 'https://images.unsplash.com/photo-1566446896748-6075a87760c1',
    type: 'website'
  }
};

// Generate structured data for SEO
export const generateStructuredData = (type, data = {}) => {
  const baseData = {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "EINSPOT SOLUTIONS NIG LTD",
    "url": "https://einspot.com.ng",
    "logo": "https://einspot.com.ng/logo.png",
    "contactPoint": {
      "@type": "ContactPoint",
      "telephone": "+234-812-364-7982",
      "contactType": "customer service",
      "areaServed": "NG",
      "availableLanguage": "English"
    },
    "address": {
      "@type": "PostalAddress",
      "addressLocality": "Lagos",
      "addressCountry": "NG"
    },
    "sameAs": [
      "https://facebook.com/einspot",
      "https://linkedin.com/company/einspot",
      "https://twitter.com/einspot"
    ]
  };

  let structuredData = baseData;

  switch (type) {
    case 'product':
      structuredData = {
        "@context": "https://schema.org",
        "@type": "Product",
        "name": data.name,
        "description": data.description,
        "brand": {
          "@type": "Brand",
          "name": data.brand || "Rheem"
        },
        "offers": {
          "@type": "Offer",
          "price": data.price,
          "priceCurrency": "NGN",
          "availability": "https://schema.org/InStock",
          "seller": {
            "@type": "Organization",
            "name": "EINSPOT SOLUTIONS NIG LTD"
          }
        }
      };
      break;
      
    case 'service':
      structuredData = {
        "@context": "https://schema.org",
        "@type": "Service",
        "name": data.name,
        "description": data.description,
        "provider": {
          "@type": "Organization",
          "name": "EINSPOT SOLUTIONS NIG LTD"
        },
        "areaServed": {
          "@type": "Country",
          "name": "Nigeria"
        },
        "serviceType": data.serviceType || "Engineering Services"
      };
      break;
      
    case 'article':
      structuredData = {
        "@context": "https://schema.org",
        "@type": "Article",
        "headline": data.title,
        "description": data.description,
        "author": {
          "@type": "Person",
          "name": data.author
        },
        "publisher": {
          "@type": "Organization",
          "name": "EINSPOT SOLUTIONS NIG LTD",
          "logo": {
            "@type": "ImageObject",
            "url": "https://einspot.com.ng/logo.png"
          }
        },
        "datePublished": data.datePublished,
        "dateModified": data.dateModified || data.datePublished
      };
      break;
  }

  return JSON.stringify(structuredData);
};

// Add structured data to page
export const addStructuredData = (structuredData) => {
  // Remove existing structured data
  const existingScript = document.querySelector('script[type="application/ld+json"]');
  if (existingScript) {
    existingScript.remove();
  }

  // Add new structured data
  const script = document.createElement('script');
  script.type = 'application/ld+json';
  script.textContent = structuredData;
  document.head.appendChild(script);
};