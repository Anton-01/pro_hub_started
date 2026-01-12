/**
 * GraphQL Client Configuration for Production
 *
 * Place this file in: src/lib/graphql-client.ts
 *
 * Install dependencies:
 * npm install graphql-request graphql
 */

import { GraphQLClient } from 'graphql-request';

// Get endpoint from environment variable
const endpoint = import.meta.env.PUBLIC_GRAPHQL_ENDPOINT || 'https://api.tu-dominio.com/graphql';

// Create GraphQL client with CORS credentials
export const client = new GraphQLClient(endpoint, {
  credentials: 'include',
  mode: 'cors',
});

/**
 * Set JWT token in Authorization header
 * Call this after successful login
 */
export function setAuthToken(token: string) {
  client.setHeader('Authorization', `Bearer ${token}`);
}

/**
 * Clear JWT token from Authorization header
 * Call this on logout
 */
export function clearAuthToken() {
  client.setHeader('Authorization', '');
}

/**
 * Get current token from header
 */
export function getAuthToken(): string | undefined {
  return client.requestConfig.headers?.['Authorization'] as string | undefined;
}

/**
 * Check if client has auth token
 */
export function isAuthenticated(): boolean {
  return !!getAuthToken();
}
