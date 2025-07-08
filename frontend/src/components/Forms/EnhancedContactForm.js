import React, { useState } from 'react';
import { useApp } from '../../context/AppContext';
import { contactAPI } from '../../services/api';

const EnhancedContactForm = ({ onSuccess }) => {
  const { actions } = useApp();
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    company: '',
    service: '',
    budget: '',
    message: '',
    urgency: 'normal',
    preferredContact: 'email'
  });
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errors, setErrors] = useState({});

  const validateForm = () => {
    const newErrors = {};
    
    if (!formData.name.trim()) {
      newErrors.name = 'Name is required';
    }
    
    if (!formData.email.trim()) {
      newErrors.email = 'Email is required';
    } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
      newErrors.email = 'Email is invalid';
    }
    
    if (!formData.message.trim()) {
      newErrors.message = 'Message is required';
    } else if (formData.message.length < 10) {
      newErrors.message = 'Message must be at least 10 characters';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));
    
    // Clear error when user starts typing
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    if (!validateForm()) {
      return;
    }

    setIsSubmitting(true);

    try {
      // Submit to backend API
      const response = await contactAPI.submitForm(formData);
      
      if (response.success) {
        actions.addNotification({
          type: 'success',
          title: 'Message Sent!',
          message: 'Thank you for contacting us. We\'ll get back to you within 24 hours.'
        });
        
        // Reset form
        setFormData({
          name: '',
          email: '',
          phone: '',
          company: '',
          service: '',
          budget: '',
          message: '',
          urgency: 'normal',
          preferredContact: 'email'
        });
        
        // Call success callback
        if (onSuccess) onSuccess();
        
      } else {
        throw new Error(response.message || 'Failed to send message');
      }
    } catch (error) {
      console.error('Contact form error:', error);
      actions.addNotification({
        type: 'error',
        title: 'Error',
        message: 'Failed to send message. Please try again or contact us directly.'
      });
    } finally {
      setIsSubmitting(false);
    }
  };

  const handleWhatsAppSubmit = () => {
    const message = encodeURIComponent(`Hello EINSPOT, I'd like to get in touch regarding your engineering services.

Name: ${formData.name}
Company: ${formData.company || 'N/A'}
Service Interest: ${formData.service || 'General Inquiry'}
Message: ${formData.message}

Please contact me via ${formData.preferredContact}.`);
    
    window.open(`https://wa.me/2348123647982?text=${message}`, '_blank');
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-6">
      {/* Personal Information */}
      <div className="grid md:grid-cols-2 gap-6">
        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Full Name *
          </label>
          <input
            type="text"
            name="name"
            value={formData.name}
            onChange={handleChange}
            className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors ${
              errors.name ? 'border-red-300 bg-red-50' : 'border-gray-300'
            }`}
            placeholder="Enter your full name"
          />
          {errors.name && (
            <p className="text-red-500 text-sm mt-1">{errors.name}</p>
          )}
        </div>

        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Email Address *
          </label>
          <input
            type="email"
            name="email"
            value={formData.email}
            onChange={handleChange}
            className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors ${
              errors.email ? 'border-red-300 bg-red-50' : 'border-gray-300'
            }`}
            placeholder="Enter your email address"
          />
          {errors.email && (
            <p className="text-red-500 text-sm mt-1">{errors.email}</p>
          )}
        </div>
      </div>

      <div className="grid md:grid-cols-2 gap-6">
        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Phone Number
          </label>
          <input
            type="tel"
            name="phone"
            value={formData.phone}
            onChange={handleChange}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
            placeholder="+234 xxx xxx xxxx"
          />
        </div>

        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Company/Organization
          </label>
          <input
            type="text"
            name="company"
            value={formData.company}
            onChange={handleChange}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
            placeholder="Company name (optional)"
          />
        </div>
      </div>

      {/* Service & Budget */}
      <div className="grid md:grid-cols-2 gap-6">
        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Service of Interest
          </label>
          <select
            name="service"
            value={formData.service}
            onChange={handleChange}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            <option value="">Select a service</option>
            <option value="hvac">HVAC Systems</option>
            <option value="water-heating">Water Heating Solutions</option>
            <option value="fire-safety">Fire Safety Systems</option>
            <option value="building-automation">Building Automation</option>
            <option value="electrical">Electrical Engineering</option>
            <option value="plumbing">Plumbing Systems</option>
            <option value="consultation">Consultation</option>
            <option value="maintenance">Maintenance Services</option>
          </select>
        </div>

        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Budget Range
          </label>
          <select
            name="budget"
            value={formData.budget}
            onChange={handleChange}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            <option value="">Select budget range</option>
            <option value="under-500k">Under ₦500,000</option>
            <option value="500k-1m">₦500,000 - ₦1,000,000</option>
            <option value="1m-2m">₦1,000,000 - ₦2,000,000</option>
            <option value="2m-5m">₦2,000,000 - ₦5,000,000</option>
            <option value="above-5m">Above ₦5,000,000</option>
          </select>
        </div>
      </div>

      {/* Contact Preferences */}
      <div className="grid md:grid-cols-2 gap-6">
        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Urgency Level
          </label>
          <select
            name="urgency"
            value={formData.urgency}
            onChange={handleChange}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            <option value="low">Low - General inquiry</option>
            <option value="normal">Normal - Within a week</option>
            <option value="high">High - Within 2-3 days</option>
            <option value="urgent">Urgent - Same day response needed</option>
          </select>
        </div>

        <div>
          <label className="block text-gray-700 font-medium mb-2">
            Preferred Contact Method
          </label>
          <select
            name="preferredContact"
            value={formData.preferredContact}
            onChange={handleChange}
            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
          >
            <option value="email">Email</option>
            <option value="phone">Phone Call</option>
            <option value="whatsapp">WhatsApp</option>
            <option value="any">Any method</option>
          </select>
        </div>
      </div>

      {/* Message */}
      <div>
        <label className="block text-gray-700 font-medium mb-2">
          Message *
        </label>
        <textarea
          name="message"
          value={formData.message}
          onChange={handleChange}
          rows="6"
          className={`w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors resize-none ${
            errors.message ? 'border-red-300 bg-red-50' : 'border-gray-300'
          }`}
          placeholder="Please describe your project requirements, timeline, and any specific needs..."
        ></textarea>
        {errors.message && (
          <p className="text-red-500 text-sm mt-1">{errors.message}</p>
        )}
        <p className="text-gray-500 text-sm mt-1">
          {formData.message.length}/500 characters
        </p>
      </div>

      {/* Privacy & Terms */}
      <div className="bg-gray-50 p-4 rounded-lg">
        <label className="flex items-start">
          <input
            type="checkbox"
            required
            className="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500 mt-1 mr-3"
          />
          <span className="text-gray-700 text-sm">
            I agree to the processing of my personal data for the purpose of handling my inquiry. 
            EINSPOT SOLUTIONS will use this information to respond to my request and may contact me 
            with relevant service information. I can withdraw consent at any time.
          </span>
        </label>
      </div>

      {/* Submit Buttons */}
      <div className="flex flex-col sm:flex-row gap-4">
        <button
          type="submit"
          disabled={isSubmitting}
          className="flex-1 bg-red-600 text-white py-3 px-6 rounded-lg hover:bg-red-700 transition-colors font-semibold disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
        >
          {isSubmitting ? (
            <>
              <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Sending...
            </>
          ) : (
            'Send Message'
          )}
        </button>

        <button
          type="button"
          onClick={handleWhatsAppSubmit}
          className="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition-colors font-semibold flex items-center justify-center"
        >
          <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
          </svg>
          Send via WhatsApp
        </button>
      </div>

      {/* Response Time Info */}
      <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div className="flex items-center">
          <svg className="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div className="text-blue-800">
            <p className="font-medium">Expected Response Time</p>
            <p className="text-sm">
              {formData.urgency === 'urgent' && 'Same day response'}
              {formData.urgency === 'high' && 'Within 2-3 business days'}
              {formData.urgency === 'normal' && 'Within 1 week'}
              {formData.urgency === 'low' && 'Within 2 weeks'}
            </p>
          </div>
        </div>
      </div>
    </form>
  );
};

export default EnhancedContactForm;