# EinSpot Solutions

EinSpot Solutions is a full-stack web application featuring a Python (FastAPI) backend and a React frontend. This document provides an overview of the project and guidance for development and production deployment.

## Project Structure

-   `/backend`: Contains the Python FastAPI backend application.
    -   `Dockerfile.prod`: Dockerfile for building the production backend image.
    -   `requirements.txt`: Python dependencies.
    -   `server.py`: Main application file.
    -   `config/`: Configuration files, including `production.py`.
-   `/frontend`: Contains the React frontend application.
    -   `Dockerfile.prod`: Dockerfile for building the production frontend image (if deploying with Docker).
    -   `package.json`: Node dependencies and scripts.
    -   `public/`: Static assets, including `index.html` and `_redirects` for Netlify.
    -   `src/`: Frontend source code.
-   `/docs`: (Proposed) Will contain detailed documentation.
    -   `DEPLOY_RENDER.md`: Guide for deploying on Render.
    -   `DEPLOY_NETLIFY.md`: Guide for deploying the frontend on Netlify.
-   `docker-compose.prod.yml`: Docker Compose file for production-like local environment setup.
-   `.env.production`: Template for production environment variables.
-   `README.production.md`: Detailed guide for manual production deployment on a dedicated server using Docker.

## Getting Started (Development)

(Instructions for setting up and running the project locally would go here. This is currently outside the scope of the deployment task but should be added for completeness.)

## Production Deployment

This application is designed to be deployed using Docker, but specific components can also be deployed to Platform-as-a-Service (PaaS) providers.

### Environment Variables

A full list of required and optional environment variables can be found in the `.env.production` file at the root of this repository. These variables need to be configured in your chosen deployment environment. Key variables include:

*   **Database:** `MONGODB_URI`
*   **Security:** `JWT_SECRET`, `ENCRYPTION_KEY`
*   **Service URLs:** `FRONTEND_URL`, `BACKEND_URL` (or `REACT_APP_BACKEND_URL` for the frontend build)
*   **Email:** `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`
*   **Payment Gateways:** Keys for Paystack, Flutterwave
*   **Analytics:** `GA_MEASUREMENT_ID` (for frontend)

Refer to `backend/config/production.py` for backend variables and `frontend/.env.production` for frontend build-time variables.

### Deployment Options

1.  **Manual Docker Deployment (Dedicated Server):**
    *   For detailed instructions on deploying to a server with Docker and Docker Compose, managing SSL certificates, and setting up Nginx, please refer to the [**Comprehensive Production Deployment Guide (README.production.md)**](./README.production.md).

2.  **Deploying on Render:**
    *   Render can host the backend (Dockerized Python service), the frontend (as a static site or Dockerized service), and the MongoDB database.
    *   For a step-by-step guide, see [**Deploying on Render (DEPLOY_RENDER.md)**](./DEPLOY_RENDER.md).

3.  **Deploying Frontend on Netlify:**
    *   Netlify is excellent for hosting static frontends like the React application. The backend and database would need to be hosted elsewhere (e.g., on Render).
    *   For a step-by-step guide, see [**Deploying Frontend on Netlify (DEPLOY_NETLIFY.md)**](./DEPLOY_NETLIFY.md).

### Choosing a Deployment Strategy

*   **All-in-one on Render:** Simplifies management by keeping all components (database, backend, frontend) on a single platform. Good for most use cases.
*   **Frontend on Netlify + Backend/DB on Render:** Leverages Netlify's strengths for global static content delivery for the frontend, while Render handles the dynamic backend and database. This can be a powerful combination for performance and scalability.
*   **Manual Docker Deployment:** Offers maximum control but requires more server management expertise.

## Important Considerations for PaaS Deployments (Render/Netlify)

*   **Database:** Use the managed database service provided by Render (or an external provider like MongoDB Atlas). Do not try to run MongoDB in a Docker container on these platforms for production if a managed service is available.
*   **SSL/TLS:** Render and Netlify provide automated SSL certificate management. You do not need to manually configure SSL as described in `README.production.md` for the Nginx service in Docker Compose.
*   **Environment Variables:** Securely configure all necessary environment variables through the dashboards of these platforms. Do not commit sensitive `.env` files to your repository.
*   **Build Process:**
    *   **Render (Backend):** Will use the `backend/Dockerfile.prod`.
    *   **Render (Frontend - Static Site):** Will use build commands like `yarn build` from `frontend/package.json`.
    *   **Render (Frontend - Docker):** Will use the `frontend/Dockerfile.prod`.
    *   **Netlify (Frontend):** Will use build commands like `yarn build` from `frontend/package.json`. The `frontend/Dockerfile.prod` is not used by Netlify.
*   **Networking:** Services on Render can communicate via their internal hostnames. Netlify frontends will communicate with your backend via its public URL.

## Contributing

(Details on how to contribute to the project, coding standards, etc. would go here.)

## License

(Project license information would go here.)
