import React from "react";
import ReactDOM from "react-dom/client";
import "./index.css";
import App from "./App";
import ReactGA from 'react-ga4';

const GA_MEASUREMENT_ID = process.env.REACT_APP_GA_MEASUREMENT_ID;

if (GA_MEASUREMENT_ID && GA_MEASUREMENT_ID !== "G-XXXXXXXXXX" && GA_MEASUREMENT_ID.startsWith("G-")) {
  try {
    ReactGA.initialize(GA_MEASUREMENT_ID);
    // GA4's enhanced measurement usually handles initial pageviews and history changes.
    // If manual pageview sending is needed, it would be done elsewhere (e.g., RouteChangeTracker).
    console.log(`GA Initialized with ID: ${GA_MEASUREMENT_ID}`);
  } catch (error) {
    console.error("Error initializing Google Analytics:", error);
  }
} else {
  if (GA_MEASUREMENT_ID === "G-XXXXXXXXXX") {
    console.warn("GA Measurement ID is a placeholder (G-XXXXXXXXXX). Analytics not initialized.");
  } else if (GA_MEASUREMENT_ID && !GA_MEASUREMENT_ID.startsWith("G-")) {
    console.warn(`Invalid GA Measurement ID format: ${GA_MEASUREMENT_ID}. Analytics not initialized.`);
  }
  else {
    console.warn("GA Measurement ID not found. Analytics not initialized.");
  }
}

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>,
);
