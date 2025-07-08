import React, { useState, useRef, useEffect } from 'react';

const LazyImage = ({ 
  src, 
  alt, 
  className = '', 
  placeholder = null,
  width,
  height,
  quality = 80,
  ...props 
}) => {
  const [isLoaded, setIsLoaded] = useState(false);
  const [isInView, setIsInView] = useState(false);
  const [error, setError] = useState(false);
  const imgRef = useRef();

  useEffect(() => {
    const observer = new IntersectionObserver(
      ([entry]) => {
        if (entry.isIntersecting) {
          setIsInView(true);
          observer.disconnect();
        }
      },
      { 
        threshold: 0.1,
        rootMargin: '50px' // Start loading 50px before image comes into view
      }
    );

    if (imgRef.current) {
      observer.observe(imgRef.current);
    }

    return () => observer.disconnect();
  }, []);

  const optimizeImageUrl = (url) => {
    if (!url) return '';
    
    // For Unsplash images
    if (url.includes('unsplash.com')) {
      const params = new URLSearchParams();
      if (width) params.set('w', width);
      if (height) params.set('h', height);
      params.set('q', quality);
      params.set('fit', 'crop');
      params.set('auto', 'format');
      return `${url}?${params.toString()}`;
    }
    
    // For Pexels images
    if (url.includes('pexels.com')) {
      const params = new URLSearchParams();
      if (width) params.set('w', width);
      if (height) params.set('h', height);
      params.set('auto', 'compress');
      params.set('cs', 'tinysrgb');
      return `${url}?${params.toString()}`;
    }
    
    return url;
  };

  const handleLoad = () => {
    setIsLoaded(true);
  };

  const handleError = () => {
    setError(true);
    setIsLoaded(true);
  };

  return (
    <div 
      ref={imgRef} 
      className={`relative overflow-hidden ${className}`} 
      {...props}
    >
      {/* Placeholder */}
      {!isLoaded && (
        <div className="absolute inset-0 bg-gray-200 animate-pulse flex items-center justify-center">
          {placeholder || (
            <div className="flex flex-col items-center justify-center text-gray-400">
              <svg className="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <span className="text-xs">Loading...</span>
            </div>
          )}
        </div>
      )}

      {/* Error State */}
      {error && (
        <div className="absolute inset-0 bg-gray-100 flex items-center justify-center">
          <div className="flex flex-col items-center justify-center text-gray-400">
            <svg className="w-8 h-8 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span className="text-xs">Failed to load</span>
          </div>
        </div>
      )}

      {/* Actual Image */}
      {isInView && !error && (
        <img
          src={optimizeImageUrl(src)}
          alt={alt}
          loading="lazy"
          onLoad={handleLoad}
          onError={handleError}
          className={`w-full h-full object-cover transition-opacity duration-300 ${
            isLoaded ? 'opacity-100' : 'opacity-0'
          }`}
          width={width}
          height={height}
        />
      )}

      {/* Blur effect while loading */}
      {isInView && !isLoaded && !error && (
        <div className="absolute inset-0 backdrop-blur-sm bg-white/10"></div>
      )}
    </div>
  );
};

export default LazyImage;