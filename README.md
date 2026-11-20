# BTEC_APP
Feedback AI

## Crafticulum release

The self-hosted site, ready to upload. No config.php and no data folder inside, so your settings and keys are never touched.

- `crafticulum/`: every file of the current release (v139, 20 Nov 2026: a Business dashboard with monthly recurring revenue, invoices for institutions that pay against a purchase order, a sample lesson and a security page, screenshots on problem reports, adding several people at once, and a What's new note; v135 to v138 were the legal pages; v134 added the first lesson with no setting up, a voice on video lessons, and Classroom and Teams sharing). Upload the contents of this folder over the live site, including `.htaccess`.
- `crafticulum-v139.zip` and `crafticulum-release.zip`: the same files as one zip (2,636,007 bytes).

Read `crafticulum/README.txt` for what changed and how to set things up.

## Updating the live site

From v107 the site fetches its own releases from this repository (Settings, Site setup, Updates), so nothing needs uploading after v107 is on.

- `crafticulum-release.zip` and `crafticulum/version.json` are what the site reads. Keep both on `main`.
- `.github/workflows/deploy.yml` tells the site the moment a release is pushed. It needs one repository secret, `CRAFTICULUM_DEPLOY_URL`: the address shown in Crafticulum under Settings, Site setup, Updates. Without the secret the workflow does nothing.
- `.cpanel.yml` is still here for cPanel's "Deploy HEAD Commit" button, as a fallback.
