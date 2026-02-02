---
description: Setup CI/CD for Pressidium Deployment
---

# Setup CI/CD for Pressidium

This workflow guides you through setting up the GitHub Actions deployment pipeline for the Hands and Vision theme.

## 1. Configure GitHub Secrets

Go to your GitHub Repository -> Settings -> Secrets and variables -> Actions -> New repository secret.

Add the following secrets:

- `SFTP_HOST`: The hostname of your Pressidium SFTP server.
- `SFTP_PORT`: The port (usually 22 or 2222).
- `SFTP_PROD_USER`: SFTP Username for Production.
- `SFTP_PROD_PASS`: SFTP Password for Production.


## 2. Update Deployment Path

Open `.github/workflows/deploy.yml` and replace `YOUR_INSTALLATION_NAME` with your actual Pressidium installation name in the `DEPLOY_PATH` variables.

## 3. Verify Files

Ensure `.lftp_ignore` contains all files you want to exclude from the server (e.g., `node_modules`, `README.md`).

## 4. Push to GitHub

Push the `.github/workflows/deploy.yml` and `.lftp_ignore` files to your repository.
The action will trigger on the next push to `main`.
