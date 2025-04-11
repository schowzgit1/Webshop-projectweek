export const validateEmail = (email: string): boolean => {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

export const validatePassword = (password: string): boolean => {
  return password.length >= 6
}

export const validateUsername = (username: string): boolean => {
  return username.length >= 3
}

export const getValidationErrors = (field: string, value: string): string => {
  switch (field) {
    case 'email':
      return !validateEmail(value) ? 'Voer een geldig e-mailadres in' : ''
    case 'password':
      return !validatePassword(value) ? 'Wachtwoord moet minimaal 6 tekens lang zijn' : ''
    case 'username':
      return !validateUsername(value) ? 'Gebruikersnaam moet minimaal 3 tekens lang zijn' : ''
    default:
      return ''
  }
} 