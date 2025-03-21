import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";

// https://vite.dev/config/
export default defineConfig({
  plugins: [
    react({
      // This tells Vite to apply JSX transformations to .js files too
      include: /\.(jsx|js)$/,
    }),
  ],
});
