// Performance optimization utilities

// Lazy loading for images
export const LazyImage = ({ src, alt, className, ...props }) => {
  const [isLoaded, setIsLoaded] = useState(false);
  const [isInView, setIsInView] = useState(false);
  const imgRef = useRef();

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsInView(true);
          observer.disconnect();
        }
      },
      { threshold: 0.1 }
    );

    if (imgRef.current) {
      observer.observe(imgRef.current);
    }

    return () => observer.disconnect();
  }, []);

  return (
    <div ref={imgRef} className={`${className} transition-opacity duration-300`} {...props}>
      {isInView && (
        <img
          src={src}
          alt={alt}
          loading="lazy"
          onLoad={() => setIsLoaded(true)}
          className={`w-full h-full object-cover transition-opacity duration-300 ${
            isLoaded ? 'opacity-100' : 'opacity-0'
          }`}
        />
      )}
      {!isLoaded && isInView && (
        <div className="w-full h-full bg-gray-200 animate-pulse flex items-center justify-center">
          <div className="text-gray-400">Loading...</div>
        </div>
      )}
    </div>
  );
};

// Debounce function for search inputs
export const useDebounce = (value, delay) => {
  const [debouncedValue, setDebouncedValue] = useState(value);

  useEffect(() => {
    const handler = setTimeout(() => {
      setDebouncedValue(value);
    }, delay);

    return () => {
      clearTimeout(handler);
    };
  }, [value, delay]);

  return debouncedValue;
};

// Performance monitoring
export const performanceMonitor = {
  // Measure page load time
  measurePageLoad: () => {
    if (window.performance && window.performance.timing) {
      const perfData = window.performance.timing;
      const pageLoadTime = perfData.loadEventEnd - perfData.navigationStart;
      console.log(`Page load time: ${pageLoadTime}ms`);
      return pageLoadTime;
    }
  },

  // Measure component render time
  measureRender: (componentName, renderFn) => {
    const startTime = performance.now();
    const result = renderFn();
    const endTime = performance.now();
    console.log(`${componentName} render time: ${endTime - startTime}ms`);
    return result;
  },

  // Track Core Web Vitals
  trackWebVitals: () => {
    if ('web-vital' in window) {
      import('web-vitals').then(({ getCLS, getFID, getFCP, getLCP, getTTFB }) => {
        getCLS(console.log);
        getFID(console.log);
        getFCP(console.log);
        getLCP(console.log);
        getTTFB(console.log);
      });
    }
  }
};

// Image optimization
export const optimizeImage = (url, width, height, quality = 80) => {
  // For production, you might use a service like Cloudinary or similar
  // For now, we'll return the original URL with some parameters
  if (url.includes('unsplash.com')) {
    return `${url}?w=${width}&h=${height}&q=${quality}&fit=crop`;
  }
  if (url.includes('pexels.com')) {
    return `${url}?w=${width}&h=${height}&auto=compress&cs=tinysrgb`;
  }
  return url;
};

// Code splitting helper
export const loadComponent = (importFunc) => {
  return React.lazy(() => {
    return new Promise(resolve => {
      setTimeout(() => {
        resolve(importFunc());
      }, 100); // Small delay to show loading state
    });
  });
};

// Preload critical resources
export const preloadResources = (resources) => {
  resources.forEach(resource => {
    const link = document.createElement('link');
    link.rel = 'preload';
    link.href = resource.href;
    link.as = resource.as || 'fetch';
    if (resource.crossorigin) link.crossOrigin = resource.crossorigin;
    document.head.appendChild(link);
  });
};

// Service Worker registration
export const registerServiceWorker = () => {
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register('/sw.js')
        .then((registration) => {
          console.log('SW registered: ', registration);
        })
        .catch((registrationError) => {
          console.log('SW registration failed: ', registrationError);
        });
    });
  }
};

// Cache strategies
export const cacheStrategies = {
  // Cache first, then network
  cacheFirst: async (request) => {
    const cache = await caches.open('einspot-cache-v1');
    const cachedResponse = await cache.match(request);
    
    if (cachedResponse) {
      return cachedResponse;
    }
    
    const networkResponse = await fetch(request);
    cache.put(request, networkResponse.clone());
    return networkResponse;
  },

  // Network first, then cache
  networkFirst: async (request) => {
    const cache = await caches.open('einspot-cache-v1');
    
    try {
      const networkResponse = await fetch(request);
      cache.put(request, networkResponse.clone());
      return networkResponse;
    } catch (error) {
      return await cache.match(request);
    }
  }
};