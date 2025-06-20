# Laravel Passport + Sanctum: First-Party Application OAuth Guide

This guide explains how to use Laravel Passport with Sanctum for first-party applications (your own frontend applications).

## Overview

Your Laravel API now supports:
- **Sanctum**: For session-based authentication (SPAs, mobile apps)
- **Passport**: For OAuth2 authentication (first-party and third-party apps)

## Setup Required

### 1. Environment Configuration

Add these variables to your `.env` file:

```env
# Passport Configuration
PASSPORT_PRIVATE_KEY=
PASSPORT_PUBLIC_KEY=
PASSPORT_PERSONAL_ACCESS_CLIENT_ID=
PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET=

# Token Expiration (in minutes)
PASSPORT_ACCESS_TOKEN_EXPIRE=60
PASSPORT_REFRESH_TOKEN_EXPIRE=20160
PASSPORT_PERSONAL_ACCESS_TOKEN_EXPIRE=60

# Optional: Enable client secret hashing
PASSPORT_HASH_CLIENT_SECRETS=false
```

### 2. Run Passport Setup Commands

```bash
# Generate encryption keys
php artisan passport:keys

# Create personal access client (for first-party apps)
php artisan passport:client --personal

# Optional: Create password grant client
php artisan passport:client --password
```

### 3. Database Migration

Ensure Passport tables are migrated:

```bash
php artisan migrate
```

## OAuth Endpoints

Your API now exposes these OAuth endpoints:

### Token Endpoint (Password Grant)
```
POST /api/oauth/token
```

### Authorization Endpoint
```
GET /api/oauth/authorize
```

### User Info Endpoint
```
GET /api/oauth/userinfo
```

### Token Revocation
```
POST /api/oauth/revoke
```

### Client Information
```
GET /api/oauth/client/{client_id}
```

## First-Party Application Usage

### 1. Password Grant Flow (Recommended for First-Party Apps)

This is the simplest flow for your own applications:

```javascript
// Frontend JavaScript Example
async function authenticateUser(email, password) {
    try {
        const response = await fetch('/api/oauth/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                grant_type: 'password',
                client_id: 'your-client-id',
                client_secret: 'your-client-secret',
                username: email,
                password: password,
                scope: 'read-user read-profile write-profile read-files'
            })
        });

        const data = await response.json();
        
        if (response.ok) {
            // Store tokens securely
            localStorage.setItem('access_token', data.access_token);
            localStorage.setItem('refresh_token', data.refresh_token);
            localStorage.setItem('token_expires_in', data.expires_in);
            
            return data;
        } else {
            throw new Error(data.error_description || 'Authentication failed');
        }
    } catch (error) {
        console.error('Authentication error:', error);
        throw error;
    }
}
```

### 2. Making Authenticated Requests

```javascript
// Function to make authenticated API requests
async function makeAuthenticatedRequest(url, options = {}) {
    const token = localStorage.getItem('access_token');
    
    const headers = {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        ...options.headers
    };

    try {
        const response = await fetch(url, {
            ...options,
            headers
        });

        if (response.status === 401) {
            // Token expired, try to refresh
            const refreshed = await refreshAccessToken();
            if (refreshed) {
                // Retry request with new token
                return makeAuthenticatedRequest(url, options);
            } else {
                // Redirect to login
                window.location.href = '/login';
                return;
            }
        }

        return response;
    } catch (error) {
        console.error('API request error:', error);
        throw error;
    }
}

// Examples of authenticated requests
async function getUserInfo() {
    const response = await makeAuthenticatedRequest('/api/oauth/userinfo');
    return response.json();
}

async function getUserProfile() {
    const response = await makeAuthenticatedRequest('/api/oauth/profile');
    return response.json();
}

async function getUserFiles() {
    const response = await makeAuthenticatedRequest('/api/oauth/files');
    return response.json();
}
```

### 3. Token Refresh

```javascript
async function refreshAccessToken() {
    const refreshToken = localStorage.getItem('refresh_token');
    
    if (!refreshToken) {
        return false;
    }

    try {
        const response = await fetch('/api/oauth/token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                grant_type: 'refresh_token',
                client_id: 'your-client-id',
                client_secret: 'your-client-secret',
                refresh_token: refreshToken
            })
        });

        const data = await response.json();
        
        if (response.ok) {
            localStorage.setItem('access_token', data.access_token);
            localStorage.setItem('refresh_token', data.refresh_token);
            localStorage.setItem('token_expires_in', data.expires_in);
            return true;
        } else {
            // Refresh failed, user needs to login again
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            localStorage.removeItem('token_expires_in');
            return false;
        }
    } catch (error) {
        console.error('Token refresh error:', error);
        return false;
    }
}
```

### 4. Logout / Token Revocation

```javascript
async function logout() {
    const token = localStorage.getItem('access_token');
    
    if (token) {
        try {
            await fetch('/api/oauth/revoke', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    token: token,
                    token_type_hint: 'access_token'
                })
            });
        } catch (error) {
            console.error('Token revocation error:', error);
        }
    }

    // Clear stored tokens
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    localStorage.removeItem('token_expires_in');
    
    // Redirect to login
    window.location.href = '/login';
}
```

## Mobile App Example (React Native)

```javascript
import AsyncStorage from '@react-native-async-storage/async-storage';

class ApiService {
    constructor() {
        this.baseURL = 'https://your-api-domain.com/api';
        this.clientId = 'your-client-id';
        this.clientSecret = 'your-client-secret';
    }

    async login(email, password, scopes = 'read-user read-profile') {
        try {
            const response = await fetch(`${this.baseURL}/oauth/token`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    grant_type: 'password',
                    client_id: this.clientId,
                    client_secret: this.clientSecret,
                    username: email,
                    password: password,
                    scope: scopes
                })
            });

            const data = await response.json();
            
            if (response.ok) {
                await AsyncStorage.setItem('access_token', data.access_token);
                await AsyncStorage.setItem('refresh_token', data.refresh_token);
                await AsyncStorage.setItem('token_expires_at', 
                    String(Date.now() + (data.expires_in * 1000))
                );
                
                return data;
            } else {
                throw new Error(data.error_description || 'Authentication failed');
            }
        } catch (error) {
            console.error('Login error:', error);
            throw error;
        }
    }

    async makeRequest(endpoint, options = {}) {
        const token = await AsyncStorage.getItem('access_token');
        
        const headers = {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...options.headers
        };

        try {
            const response = await fetch(`${this.baseURL}${endpoint}`, {
                ...options,
                headers
            });

            if (response.status === 401) {
                const refreshed = await this.refreshToken();
                if (refreshed) {
                    return this.makeRequest(endpoint, options);
                } else {
                    throw new Error('Authentication required');
                }
            }

            return response;
        } catch (error) {
            console.error('API request error:', error);
            throw error;
        }
    }

    async refreshToken() {
        const refreshToken = await AsyncStorage.getItem('refresh_token');
        
        if (!refreshToken) {
            return false;
        }

        try {
            const response = await fetch(`${this.baseURL}/oauth/token`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    grant_type: 'refresh_token',
                    client_id: this.clientId,
                    client_secret: this.clientSecret,
                    refresh_token: refreshToken
                })
            });

            const data = await response.json();
            
            if (response.ok) {
                await AsyncStorage.setItem('access_token', data.access_token);
                await AsyncStorage.setItem('refresh_token', data.refresh_token);
                await AsyncStorage.setItem('token_expires_at', 
                    String(Date.now() + (data.expires_in * 1000))
                );
                return true;
            } else {
                await this.clearTokens();
                return false;
            }
        } catch (error) {
            console.error('Token refresh error:', error);
            return false;
        }
    }

    async clearTokens() {
        await AsyncStorage.removeItem('access_token');
        await AsyncStorage.removeItem('refresh_token');
        await AsyncStorage.removeItem('token_expires_at');
    }
}

// Usage
const apiService = new ApiService();

// Login
await apiService.login('user@example.com', 'password');

// Make authenticated requests
const userInfo = await apiService.makeRequest('/oauth/userinfo');
const profile = await apiService.makeRequest('/oauth/profile');
```

## Available Scopes

Your API defines these scopes:

- `read-user`: Read basic user information
- `read-profile`: Read detailed user profile data
- `write-profile`: Update user profile data
- `read-files`: Read user files
- `write-files`: Upload and manage user files
- `read-certificates`: Read user certificates
- `write-certificates`: Request and manage certificates
- `read-training`: Read training information
- `write-training`: Register for training
- `read-payments`: Read payment information
- `admin`: Full administrative access

## Security Best Practices

1. **Store tokens securely**:
   - Web: Use `httpOnly` cookies when possible
   - Mobile: Use secure storage (Keychain/Keystore)
   - Never store in plain localStorage in production

2. **Implement token refresh**:
   - Always implement automatic token refresh
   - Handle refresh failures gracefully

3. **Use appropriate scopes**:
   - Request only the scopes you need
   - Different parts of your app may need different scopes

4. **Handle errors properly**:
   - Implement proper error handling for all OAuth flows
   - Provide user-friendly error messages

5. **Token expiration**:
   - Monitor token expiration
   - Refresh tokens before they expire when possible

## Testing Your Implementation

Use these curl commands to test your OAuth implementation:

```bash
# Get access token
curl -X POST http://your-domain.com/api/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "password",
    "client_id": "your-client-id",
    "client_secret": "your-client-secret",
    "username": "user@example.com",
    "password": "password",
    "scope": "read-user read-profile"
  }'

# Use access token
curl -X GET http://your-domain.com/api/oauth/userinfo \
  -H "Authorization: Bearer your-access-token"

# Refresh token
curl -X POST http://your-domain.com/api/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "refresh_token",
    "client_id": "your-client-id",
    "client_secret": "your-client-secret",
    "refresh_token": "your-refresh-token"
  }'
```

## Troubleshooting

### Common Issues:

1. **"Client not found"**:
   - Run `php artisan passport:client --password`
   - Update your client ID and secret

2. **"Invalid credentials"**:
   - Check user exists and password is correct
   - Ensure user is verified (if required)

3. **"Insufficient scope"**:
   - Request appropriate scopes in your token request
   - Check scope middleware configuration

4. **"Token expired"**:
   - Implement token refresh logic
   - Check token expiration settings

This setup provides a robust OAuth2 implementation that works alongside your existing Sanctum authentication, giving you flexibility for different types of client applications. 