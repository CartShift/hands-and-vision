# Bluehost Theme Deployment

Use this checklist when updating the production theme.

Do not use WinSCP Synchronize/Mirror for this site. This is the failure mode that
can leave production with only a partial theme folder: if the local panel points
at a subdirectory such as `inc`, a mirror/sync operation can remove every remote
file that is not present in that partial local folder. WordPress then reports
`Stylesheet is missing` because `style.css` is gone from the active theme root.

## Build The Package

From the theme root:

```powershell
npm run package:theme
```

The script creates:

```text
deploy/hands-and-vision-theme-folder.zip
```

It verifies that the package contains the required WordPress theme files:

- `hands-and-vision/style.css`
- `hands-and-vision/functions.php`
- `hands-and-vision/index.php`
- `hands-and-vision/assets/`
- `hands-and-vision/inc/`
- `hands-and-vision/template-parts/`

If one of these is missing, the script stops and does not produce a package.

## Upload To Bluehost

1. Open Bluehost File Manager.
2. Go to `wp-content/themes`.
3. Rename the current `hands-and-vision` folder to a timestamped backup, for
   example `hands-and-vision-backup-20260825-1430`.
4. Upload `deploy/hands-and-vision-theme-folder.zip`.
5. Extract the zip inside `wp-content/themes`.
6. Confirm this exact path exists:
   `wp-content/themes/hands-and-vision/style.css`.
7. In WordPress Admin, go to Appearance > Themes and activate `hands-and-vision`
   if needed.
8. Clear Bluehost cache.

## FTP Safety Rule

If using FTP for a tiny hotfix, upload only the specific changed files. Before
uploading, confirm:

- Local side is the full theme root containing `style.css`.
- Remote side is `wp-content/themes/hands-and-vision`.
- You are using Upload, not Synchronize/Mirror.

Stop immediately if the remote `wp-content/themes/hands-and-vision` folder shows
only one subfolder, such as `inc`. That means the active theme has already been
partially overwritten and must be restored from the full zip package.

## Automated Deployments

The legacy Pressidium GitHub Action is disabled by default and must not be used
for Bluehost. The Bluehost deployment path is managed through the verified zip
package above.
