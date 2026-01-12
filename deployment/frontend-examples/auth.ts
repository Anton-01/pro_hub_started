/**
 * Authentication Utilities for Production
 *
 * Place this file in: src/lib/auth.ts
 *
 * This replaces the mock authentication with real JWT authentication
 */

import { client, setAuthToken, clearAuthToken } from './graphql-client';
import { gql } from 'graphql-request';

// GraphQL Mutations
const LOGIN_MUTATION = gql`
  mutation Login($email: String!, $password: String!, $companyId: ID!) {
    login(email: $email, password: $password, companyId: $companyId) {
      user {
        id
        email
        name
        last_name
        phone
        role
        status
        company {
          id
          name
          slug
        }
      }
      access_token
      refresh_token
      expires_at
    }
  }
`;

const LOGOUT_MUTATION = gql`
  mutation Logout {
    logout
  }
`;

const REFRESH_TOKEN_MUTATION = gql`
  mutation RefreshToken($refreshToken: String!) {
    refreshToken(refreshToken: $refreshToken) {
      access_token
      expires_at
    }
  }
`;

const REQUEST_PASSWORD_RESET_MUTATION = gql`
  mutation RequestPasswordReset($email: String!) {
    requestPasswordReset(email: $email) {
      success
      message
    }
  }
`;

// Types
export interface User {
  id: string;
  email: string;
  name: string;
  last_name?: string;
  phone?: string;
  role: 'super_admin' | 'admin' | 'user';
  status: string;
  company: {
    id: string;
    name: string;
    slug: string;
  };
}

export interface AuthResponse {
  user: User;
  access_token: string;
  refresh_token: string;
  expires_at: string;
}

/**
 * Login with email, password and company ID
 */
export async function login(
  email: string,
  password: string,
  companyId: string
): Promise<AuthResponse> {
  try {
    const data: any = await client.request(LOGIN_MUTATION, {
      email,
      password,
      companyId,
    });

    const authData = data.login;

    // Set JWT token in client
    setAuthToken(authData.access_token);

    // Store tokens and user in localStorage
    localStorage.setItem('access_token', authData.access_token);
    localStorage.setItem('refresh_token', authData.refresh_token);
    localStorage.setItem('token_expires_at', authData.expires_at);
    localStorage.setItem('user', JSON.stringify(authData.user));

    return authData;
  } catch (error: any) {
    console.error('Login error:', error);

    // Parse GraphQL errors
    if (error.response?.errors) {
      const message = error.response.errors[0]?.message || 'Error de autenticación';
      throw new Error(message);
    }

    throw new Error('Credenciales inválidas. Por favor verifica tus datos.');
  }
}

/**
 * Logout current user
 */
export async function logout(): Promise<void> {
  try {
    // Call logout mutation (invalidates token on server)
    await client.request(LOGOUT_MUTATION);
  } catch (error) {
    console.error('Logout error:', error);
    // Continue with local logout even if server logout fails
  } finally {
    // Clear local storage
    clearAuthToken();
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    localStorage.removeItem('token_expires_at');
    localStorage.removeItem('user');
  }
}

/**
 * Refresh access token using refresh token
 */
export async function refreshAccessToken(): Promise<string | null> {
  try {
    const refreshToken = localStorage.getItem('refresh_token');

    if (!refreshToken) {
      return null;
    }

    const data: any = await client.request(REFRESH_TOKEN_MUTATION, {
      refreshToken,
    });

    const newAccessToken = data.refreshToken.access_token;
    const expiresAt = data.refreshToken.expires_at;

    // Update stored tokens
    setAuthToken(newAccessToken);
    localStorage.setItem('access_token', newAccessToken);
    localStorage.setItem('token_expires_at', expiresAt);

    return newAccessToken;
  } catch (error) {
    console.error('Token refresh error:', error);
    // If refresh fails, clear everything
    await logout();
    return null;
  }
}

/**
 * Request password reset email
 */
export async function requestPasswordReset(email: string): Promise<boolean> {
  try {
    const data: any = await client.request(REQUEST_PASSWORD_RESET_MUTATION, {
      email,
    });

    return data.requestPasswordReset.success;
  } catch (error) {
    console.error('Password reset request error:', error);
    throw new Error('No se pudo enviar el correo de recuperación. Intenta de nuevo.');
  }
}

/**
 * Get stored access token
 */
export function getStoredToken(): string | null {
  return localStorage.getItem('access_token');
}

/**
 * Get stored user
 */
export function getStoredUser(): User | null {
  const userJson = localStorage.getItem('user');
  return userJson ? JSON.parse(userJson) : null;
}

/**
 * Check if user is authenticated
 */
export function isAuthenticated(): boolean {
  const token = getStoredToken();
  const expiresAt = localStorage.getItem('token_expires_at');

  if (!token || !expiresAt) {
    return false;
  }

  // Check if token is expired
  const expiryDate = new Date(expiresAt);
  const now = new Date();

  if (now >= expiryDate) {
    // Token expired, try to refresh
    refreshAccessToken();
    return false;
  }

  return true;
}

/**
 * Initialize authentication on app load
 */
export function initAuth(): void {
  const token = getStoredToken();

  if (token && isAuthenticated()) {
    // Set token in GraphQL client
    setAuthToken(token);

    // Setup automatic token refresh before expiry
    setupTokenRefresh();
  } else {
    // Clear invalid tokens
    logout();
  }
}

/**
 * Setup automatic token refresh
 */
function setupTokenRefresh(): void {
  const expiresAt = localStorage.getItem('token_expires_at');

  if (!expiresAt) return;

  const expiryDate = new Date(expiresAt);
  const now = new Date();
  const timeUntilExpiry = expiryDate.getTime() - now.getTime();

  // Refresh token 5 minutes before expiry
  const refreshTime = timeUntilExpiry - 5 * 60 * 1000;

  if (refreshTime > 0) {
    setTimeout(() => {
      refreshAccessToken().then(() => {
        // Setup next refresh
        setupTokenRefresh();
      });
    }, refreshTime);
  }
}

// Initialize auth when module loads (client-side only)
if (typeof window !== 'undefined') {
  initAuth();
}
