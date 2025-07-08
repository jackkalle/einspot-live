import { VALIDATION_RULES } from '../config/constants';

export class Validator {
  static validateEmail(email) {
    if (!email) return 'Email is required';
    if (!VALIDATION_RULES.EMAIL_REGEX.test(email)) {
      return 'Please enter a valid email address';
    }
    return null;
  }
  
  static validatePassword(password) {
    if (!password) return 'Password is required';
    if (password.length < VALIDATION_RULES.PASSWORD_MIN_LENGTH) {
      return `Password must be at least ${VALIDATION_RULES.PASSWORD_MIN_LENGTH} characters`;
    }
    if (!/(?=.*[a-z])/.test(password)) {
      return 'Password must contain at least one lowercase letter';
    }
    if (!/(?=.*[A-Z])/.test(password)) {
      return 'Password must contain at least one uppercase letter';
    }
    if (!/(?=.*\d)/.test(password)) {
      return 'Password must contain at least one number';
    }
    return null;
  }
  
  static validatePhone(phone) {
    if (!phone) return null; // Phone is optional
    if (!VALIDATION_RULES.PHONE_REGEX.test(phone)) {
      return 'Please enter a valid phone number';
    }
    return null;
  }
  
  static validateName(name, fieldName = 'Name') {
    if (!name) return `${fieldName} is required`;
    if (name.trim().length < 2) {
      return `${fieldName} must be at least 2 characters`;
    }
    return null;
  }
  
  static validateMessage(message) {
    if (!message) return 'Message is required';
    if (message.length < VALIDATION_RULES.MESSAGE_MIN_LENGTH) {
      return `Message must be at least ${VALIDATION_RULES.MESSAGE_MIN_LENGTH} characters`;
    }
    if (message.length > VALIDATION_RULES.MESSAGE_MAX_LENGTH) {
      return `Message must not exceed ${VALIDATION_RULES.MESSAGE_MAX_LENGTH} characters`;
    }
    return null;
  }
  
  static validateRequired(value, fieldName) {
    if (!value || value.toString().trim() === '') {
      return `${fieldName} is required`;
    }
    return null;
  }
  
  static validateForm(formData, rules) {
    const errors = {};
    
    Object.keys(rules).forEach(field => {
      const rule = rules[field];
      const value = formData[field];
      
      if (rule.required) {
        const error = this.validateRequired(value, rule.label || field);
        if (error) {
          errors[field] = error;
          return;
        }
      }
      
      if (value && rule.type) {
        let error = null;
        
        switch (rule.type) {
          case 'email':
            error = this.validateEmail(value);
            break;
          case 'password':
            error = this.validatePassword(value);
            break;
          case 'phone':
            error = this.validatePhone(value);
            break;
          case 'name':
            error = this.validateName(value, rule.label);
            break;
          case 'message':
            error = this.validateMessage(value);
            break;
        }
        
        if (error) {
          errors[field] = error;
        }
      }
      
      if (value && rule.minLength && value.length < rule.minLength) {
        errors[field] = `${rule.label || field} must be at least ${rule.minLength} characters`;
      }
      
      if (value && rule.maxLength && value.length > rule.maxLength) {
        errors[field] = `${rule.label || field} must not exceed ${rule.maxLength} characters`;
      }
    });
    
    return {
      isValid: Object.keys(errors).length === 0,
      errors
    };
  }
}