import React, { useState, useEffect } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useApp } from '../../context/AppContext';
import { updateMetaTags } from '../../utils/seo';
import { Header, Footer, WhatsAppButton } from '../index';

const CartPage = () => {
  const { state, actions } = useApp();
  const [promoCode, setPromoCode] = useState('');
  const [isPromoApplied, setIsPromoApplied] = useState(false);
  const [discount, setDiscount] = useState(0);
  const navigate = useNavigate();

  useEffect(() => {
    updateMetaTags({
      title: 'Shopping Cart - EINSPOT SOLUTIONS NIG LTD',
      description: 'Review your engineering products and services in your shopping cart. Secure checkout available.',
      keywords: 'shopping cart, engineering products, HVAC, water heaters, checkout'
    });
  }, []);

  const subtotal = state.cart.reduce((total, item) => total + (item.price * item.quantity), 0);
  const tax = subtotal * 0.075; // 7.5% VAT in Nigeria
  const shipping = subtotal > 500000 ? 0 : 25000; // Free shipping over ₦500,000
  const total = subtotal + tax + shipping - discount;

  const handleQuantityChange = (itemId, newQuantity) => {
    if (newQuantity < 1) {
      actions.removeFromCart(itemId);
    } else {
      actions.updateCartQuantity(itemId, newQuantity);
    }
  };

  const handlePromoCode = () => {
    // Mock promo code logic
    const validCodes = {
      'EINSPOT10': 0.1,
      'NEWCLIENT': 0.15,
      'HVAC20': 0.2
    };
    
    if (validCodes[promoCode.toUpperCase()]) {
      const discountPercent = validCodes[promoCode.toUpperCase()];
      setDiscount(subtotal * discountPercent);
      setIsPromoApplied(true);
      actions.addNotification({
        type: 'success',
        message: `Promo code applied! ${discountPercent * 100}% discount`
      });
    } else {
      actions.addNotification({
        type: 'error',
        message: 'Invalid promo code'
      });
    }
  };

  const handleWhatsAppQuote = () => {
    const cartItems = state.cart.map(item => 
      `- ${item.name} (Qty: ${item.quantity}) - ₦${(item.price * item.quantity).toLocaleString()}`
    ).join('\n');
    
    const message = encodeURIComponent(`Hello EINSPOT, I'd like to request a quote for these items:

${cartItems}

Subtotal: ₦${subtotal.toLocaleString()}
Total: ₦${total.toLocaleString()}

Name: [ ]
Company: [ ]
Location: [ ]
Preferred delivery date: [ ]`);
    
    window.open(`https://wa.me/2348123647982?text=${message}`, '_blank');
  };

  if (state.cart.length === 0) {
    return (
      <div className="min-h-screen bg-gray-50">
        <Header />
        
        <div className="container mx-auto px-4 py-20">
          <div className="text-center max-w-md mx-auto">
            <div className="bg-white rounded-2xl shadow-lg p-12">
              <div className="text-6xl mb-6">🛒</div>
              <h1 className="text-2xl font-bold text-gray-800 mb-4">Your Cart is Empty</h1>
              <p className="text-gray-600 mb-8">
                Looks like you haven't added any products to your cart yet.
              </p>
              <Link 
                to="/products" 
                className="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold inline-block"
              >
                Continue Shopping
              </Link>
            </div>
          </div>
        </div>

        <Footer />
        <WhatsAppButton />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <Header />
      
      <div className="container mx-auto px-4 py-20">
        <h1 className="text-3xl font-bold text-gray-800 mb-8">Shopping Cart</h1>
        
        <div className="grid lg:grid-cols-3 gap-8">
          {/* Cart Items */}
          <div className="lg:col-span-2">
            <div className="bg-white rounded-2xl shadow-lg overflow-hidden">
              <div className="p-6 border-b border-gray-200">
                <h2 className="text-xl font-semibold text-gray-800">
                  Cart Items ({state.cart.length})
                </h2>
              </div>
              
              <div className="divide-y divide-gray-200">
                {state.cart.map((item) => (
                  <div key={item.id} className="p-6 hover:bg-gray-50 transition-colors">
                    <div className="flex items-center gap-4">
                      <div className="w-20 h-20 bg-gray-200 rounded-lg overflow-hidden">
                        {item.image && (
                          <img 
                            src={item.image} 
                            alt={item.name}
                            className="w-full h-full object-cover"
                          />
                        )}
                      </div>
                      
                      <div className="flex-1">
                        <h3 className="text-lg font-semibold text-gray-800 mb-1">
                          {item.name}
                        </h3>
                        <p className="text-gray-600 text-sm mb-2">{item.category}</p>
                        <p className="text-red-600 font-bold text-lg">
                          ₦{item.price.toLocaleString()}
                        </p>
                      </div>
                      
                      <div className="flex items-center gap-3">
                        <div className="flex items-center border border-gray-300 rounded-lg">
                          <button
                            onClick={() => handleQuantityChange(item.id, item.quantity - 1)}
                            className="px-3 py-2 hover:bg-gray-100 transition-colors"
                          >
                            -
                          </button>
                          <span className="px-4 py-2 border-x border-gray-300 min-w-[3rem] text-center">
                            {item.quantity}
                          </span>
                          <button
                            onClick={() => handleQuantityChange(item.id, item.quantity + 1)}
                            className="px-3 py-2 hover:bg-gray-100 transition-colors"
                          >
                            +
                          </button>
                        </div>
                        
                        <button
                          onClick={() => actions.removeFromCart(item.id)}
                          className="text-red-600 hover:text-red-700 p-2"
                        >
                          <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                        </button>
                      </div>
                      
                      <div className="text-right">
                        <p className="text-lg font-bold text-gray-800">
                          ₦{(item.price * item.quantity).toLocaleString()}
                        </p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
          
          {/* Order Summary */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-2xl shadow-lg p-6 sticky top-24">
              <h2 className="text-xl font-semibold text-gray-800 mb-6">Order Summary</h2>
              
              {/* Promo Code */}
              <div className="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 className="font-medium text-gray-800 mb-3">Promo Code</h3>
                <div className="flex gap-2">
                  <input
                    type="text"
                    value={promoCode}
                    onChange={(e) => setPromoCode(e.target.value)}
                    placeholder="Enter code"
                    className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    disabled={isPromoApplied}
                  />
                  <button
                    onClick={handlePromoCode}
                    disabled={isPromoApplied || !promoCode}
                    className="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50"
                  >
                    Apply
                  </button>
                </div>
                {isPromoApplied && (
                  <p className="text-green-600 text-sm mt-2">✓ Promo code applied!</p>
                )}
              </div>
              
              {/* Price Breakdown */}
              <div className="space-y-3 mb-6">
                <div className="flex justify-between">
                  <span className="text-gray-600">Subtotal</span>
                  <span className="font-medium">₦{subtotal.toLocaleString()}</span>
                </div>
                
                {discount > 0 && (
                  <div className="flex justify-between text-green-600">
                    <span>Discount</span>
                    <span>-₦{discount.toLocaleString()}</span>
                  </div>
                )}
                
                <div className="flex justify-between">
                  <span className="text-gray-600">VAT (7.5%)</span>
                  <span className="font-medium">₦{tax.toLocaleString()}</span>
                </div>
                
                <div className="flex justify-between">
                  <span className="text-gray-600">Shipping</span>
                  <span className="font-medium">
                    {shipping === 0 ? 'Free' : `₦${shipping.toLocaleString()}`}
                  </span>
                </div>
                
                <div className="border-t border-gray-200 pt-3">
                  <div className="flex justify-between text-lg font-bold">
                    <span>Total</span>
                    <span className="text-red-600">₦{total.toLocaleString()}</span>
                  </div>
                </div>
              </div>
              
              {/* Action Buttons */}
              <div className="space-y-3">
                <button
                  onClick={handleWhatsAppQuote}
                  className="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition-colors font-semibold flex items-center justify-center gap-2"
                >
                  <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                  </svg>
                  Request Quote via WhatsApp
                </button>
                
                <button
                  onClick={() => navigate('/checkout')}
                  className="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 transition-colors font-semibold"
                >
                  Proceed to Checkout
                </button>
                
                <Link 
                  to="/products"
                  className="w-full border border-gray-300 text-gray-700 py-3 rounded-lg hover:bg-gray-50 transition-colors font-medium text-center block"
                >
                  Continue Shopping
                </Link>
              </div>
              
              {/* Security Badge */}
              <div className="mt-6 pt-6 border-t border-gray-200">
                <div className="flex items-center justify-center gap-2 text-gray-600 text-sm">
                  <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                  Secure Checkout
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <Footer />
      <WhatsAppButton />
    </div>
  );
};

export default CartPage;