# BTEC_APP
Feedback AI

## Crafticulum release

The self-hosted site, ready to upload. No config.php and no data folder inside, so your settings and keys are never touched.

- `crafticulum/`: every file of the current release (v138, 20 Nov 2026: the full ICO number and the backup location on the legal pages; v137 added a data processing agreement page at /dpa and where data is kept; v136 named the company on the legal pages; v135 added the 30-day refund; v134 added a first lesson with no setting up, a voice on video lessons, Classroom and Teams sharing, and public profiles for a whole institution). Upload the contents of this folder over the live site, including `.htaccess`.
- `crafticulum-v138.zip` and `crafticulum-release.zip`: the same files as one zip (2,602,478 bytes).

Read `crafticulum/README.txt` for what changed and how to set things up.

## Updating the live site

From v107 the site fetches its own releases from this repository (Settings, Site setup, Updates), so nothing needs uploading after v107 is on.

- `crafticulum-release.zip` and `crafticulum/version.json` are what the site reads. Keep both on `main`.
- `.github/workflows/deploy.yml` tells the site the moment a release is pushed. It needs one repository secret, `CRAFTICULUM_DEPLOY_URL`: the address shown in Crafticulum under Settings, Site setup, Updates. Without the secret the workflow does nothing.
- `.cpanel.yml` is still here for cPanel's "Deploy HEAD Commit" button, as a fallback.
