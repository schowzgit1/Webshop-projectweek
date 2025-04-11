// Validation plugin that works in both client and server environments
export default defineNuxtPlugin(() => {
  // Provide basic validation functions globally
  return {
    provide: {
      validateEmail: (email) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
        return emailRegex.test(email)
      },
      validatePassword: (password) => {
        return password && password.length >= 6
      },
      validateUsername: (username) => {
        return username && username.length >= 3
      }
    }
  }
}) 