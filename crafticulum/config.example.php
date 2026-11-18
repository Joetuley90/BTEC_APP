<?php
/*
 * Crafticulum: server settings.
 * FIRST INSTALL ONLY: copy this file to config.php, then edit the values below.
 * Updates never include config.php, so your key and password are never overwritten.
 * Keep this file private: never share it or commit it anywhere public.
 */

// Your Anthropic API key (console.anthropic.com > API keys)
const ANTHROPIC_API_KEY = 'sk-ant-REPLACE-ME';

// The admin password. It signs you in as a super admin until Google sign-in is set up.
const TEACHER_PASSWORD = 'change-this-password';

// Which Claude model writes the lessons
const CLAUDE_MODEL = 'claude-sonnet-5-5';

// Most requests one signed-in person can make per hour (stops runaway costs)
const MAX_REQUESTS_PER_HOUR = 60;

// Google sign-in: the Client ID from Google Cloud (see SIGN IN WITH GOOGLE in the README)
const GOOGLE_CLIENT_ID = '';
// The client secret from the same Google OAuth client. With it, Sign in with Google goes to Google and comes back
// (add the redirect URI https://your-site/auth.php?a=g_cb in Google Cloud). Without it, Google's pop-up button is used.
const GOOGLE_CLIENT_SECRET = '';

// Super admins: the Google account emails that can see and manage everything
const SUPER_ADMINS = ['you@example.com'];

// Keep the admin password working after Google sign-in is set up (true or false)
const PASSWORD_BACKUP = false;

// Microsoft sign-in (Entra ID): from your app registration (see SIGN IN WITH MICROSOFT in the README)
const MS_CLIENT_ID = '';
const MS_CLIENT_SECRET = '';
const MS_TENANT = 'common';

// Sending emails (account approvals, password resets). Leave SMTP_HOST empty to use the host's own mail.
const MAIL_FROM = '';
const SMTP_HOST = '';
const SMTP_PORT = 587;
const SMTP_USER = '';
const SMTP_PASS = '';
const SMTP_SECURE = 'tls';

// Any OpenID Connect sign-in (Okta, Auth0, Keycloak, OneLogin, college single sign-on). See the README.
const OIDC_ISSUER = '';
const OIDC_CLIENT_ID = '';
const OIDC_CLIENT_SECRET = '';
const OIDC_NAME = 'College login';

// The site's addresses. Both open the same site. Links in emails and links given to students use the main address
// as soon as the site answers there, and the address in use until then. You can also set these in Settings, Site setup,
// and what's saved there is used first.
const SITE_URL = 'https://crafticulum.co.uk';
const SITE_URL_TEMP = 'https://hopeful-lime-horse.185-199-220-70.cpanel.site';
