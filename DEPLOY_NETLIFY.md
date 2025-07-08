# Deploying EinSpot Solutions Frontend on Netlify

This guide provides step-by-step instructions for deploying the **frontend** of the EinSpot Solutions application on Netlify. Netlify excels at hosting static sites, which is perfect for your React frontend.

## Prerequisites

1.  **A Netlify Account:** Sign up at [https://www.netlify.com/](https://www.netlify.com/).
2.  **GitHub Repository:** Your application code should be in a GitHub repository that Netlify can access.
3.  **Backend Deployed:** Your backend service (Python/FastAPI) and database (MongoDB) must be deployed and accessible via a public URL (e.g., on Render as per `DEPLOY_RENDER.md`).
4.  **Environment Variables:** Have your frontend-specific production environment variables ready.

## Deployment Steps

1.  **Log in to Netlify and Add New Site:**
    *   Go to your Netlify dashboard.
    *   Click "Add new site" and choose "Import an existing project".

2.  **Connect to Git Provider:**
    *   Select your Git provider (e.g., GitHub).
    *   Authorize Netlify to access your repositories.
    *   Choose your EinSpot Solutions application repository.

3.  **Configure Site Settings:**
    *   **Branch to deploy:** Select the branch you want to deploy (e.g., `main`, `master`, or a specific production branch).
    *   **Base directory:** Set this to `frontend/`. This tells Netlify to look for your frontend code and `package.json` in the `frontend` subdirectory of your repository.
    *   **Build command:** Your `frontend/package.json` likely has a `build` script. This is typically `yarn build` or `npm run build`. Your project uses `yarn`. So, set this to:
        ```
        yarn build
        ```
    *   **Publish directory:** After the build command, Create React App outputs static files to a `build` directory *within your base directory*. So, set this to:
        ```
        frontend/build
        ```
        (If Base directory was empty, it would be `frontend/build`. Since Base directory is `frontend/`, Publish directory is just `build` relative to that base).
        *Correction based on Netlify's behavior:* If "Base directory" is `frontend/`, then "Publish directory" should be `build/` (relative to the base directory).

4.  **Environment Variables:**
    *   Before deploying, or immediately after, go to "Site settings" > "Build & deploy" > "Environment".
    *   Click "Edit variables" and add the following:
        *   `REACT_APP_BACKEND_URL`: This is crucial. Set it to the public URL of your deployed backend service (e.g., `https://your-backend-name.onrender.com`).
        *   `REACT_APP_GA_MEASUREMENT_ID`: Your Google Analytics Measurement ID (e.g., `G-XXXXXXXXXX`), if you use analytics.
        *   `REACT_APP_PAYSTACK_PUBLIC_KEY`: Your Paystack public key, if used directly by the frontend.
        *   `REACT_APP_FLUTTERWAVE_PUBLIC_KEY`: Your Flutterwave public key, if used directly by the frontend.
        *   `NODE_ENV`: `production` (Netlify often sets this by default for builds).
        *   `CI`: Netlify sets `CI=true` by default, which is standard for build environments.
        *   `GENERATE_SOURCEMAP`: `false` (as per your `frontend/.env.production` to disable sourcemaps in production).
    *   Create React App automatically bundles these `REACT_APP_` prefixed variables into your static build.

5.  **Deploy Site:**
    *   Click the "Deploy site" button (or "Deploy [your-repo-name]").
    *   Netlify will start the build and deployment process. You can monitor the progress in the "Deploys" tab.

## Post-Deployment

1.  **Preview URL:**
    *   Once deployed, Netlify will provide you with a unique URL (e.g., `your-site-name.netlify.app`). Test your site thoroughly.

2.  **Custom Domain:**
    *   To use your own domain (e.g., `www.einspot.com.ng` if you choose Netlify for frontend):
        *   Go to "Site settings" > "Domain management".
        *   Click "Add custom domain" and follow the instructions to configure your DNS records. Netlify provides free SSL (HTTPS) for custom domains.

3.  **Redirects and Rewrites (for Single Page Applications):**
    *   For React (which is a Single Page Application), you need to ensure that all routes are handled by your `index.html` file so that client-side routing works correctly.
    *   Create a file named `_redirects` in your **publish directory** (`frontend/build/`). Since Netlify copies the contents of the publish directory, it's often easier to put this file in your `frontend/public/` directory, so it gets copied into `frontend/build/` automatically during the build process.
    *   Add the following line to `frontend/public/_redirects`:
        ```
        /*    /index.html    200
        ```
    *   This rule tells Netlify to serve `index.html` for any path that doesn't match a static file, with a 200 status code, allowing React Router to handle the routing.
    *   Commit this `_redirects` file to your repository.

4.  **Troubleshooting:**
    *   If the deployment fails, check the deploy logs on Netlify for error messages. Common issues include incorrect build commands, publish directory settings, or missing environment variables.

## Backend and Database

*   **Important Reminder:** Netlify only hosts your frontend. Your backend API (Python/FastAPI) and MongoDB database **must be hosted elsewhere** (e.g., on Render, AWS, Heroku, etc.).
*   Ensure the `REACT_APP_BACKEND_URL` environment variable in your Netlify frontend settings correctly points to your live backend API.

## Environment Variable Summary for Netlify (Frontend)

Set these in "Site settings" > "Build & deploy" > "Environment" on Netlify:

*   `REACT_APP_BACKEND_URL`: (e.g., `https://your-backend-service.onrender.com`)
*   `REACT_APP_GA_MEASUREMENT_ID` (optional)
*   `REACT_APP_PAYSTACK_PUBLIC_KEY` (if used by frontend)
*   `REACT_APP_FLUTTERWAVE_PUBLIC_KEY` (if used by frontend)
*   `NODE_ENV`: `production`
*   `GENERATE_SOURCEMAP`: `false`

This setup will get your EinSpot Solutions frontend live on Netlify, communicating with your separately hosted backend.
Now, I'll create the `_redirects` file as it's a crucial part of deploying SPAs on Netlify.
