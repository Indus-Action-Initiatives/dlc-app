import api from './api.js';

export const otpService = {
  /**
   * Send OTP to mobile number via backend (MSG91 in production, 123456 in local/testing)
   * @param {string} mobile
   */
  async sendOtp(mobile) {
    const response = await api.post('otp/send', { mobile });
    return response.data;
  },

  /**
   * Verify OTP for mobile number
   * @param {string} mobile
   * @param {string} otp
   */
  async verifyOtp(mobile, otp) {
    const response = await api.post('otp/verify', { mobile, otp });
    return response.data;
  },
};
