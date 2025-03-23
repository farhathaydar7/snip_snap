const fs = require("fs");
const path = require("path");
const { execSync } = require("child_process");

// Define source and destination directories
const sourceDir = path.join(__dirname, "dist");
const destDir = path.join(__dirname, "..");

// Function to copy files recursively
function copyFilesRecursively(source, destination) {
  // Create destination directory if it doesn't exist
  if (!fs.existsSync(destination)) {
    fs.mkdirSync(destination, { recursive: true });
  }

  // Get all files and directories in the source directory
  const entries = fs.readdirSync(source, { withFileTypes: true });

  // Process each entry
  for (const entry of entries) {
    const sourcePath = path.join(source, entry.name);
    const destPath = path.join(destination, entry.name);

    if (entry.isDirectory()) {
      // Recursively copy directories
      copyFilesRecursively(sourcePath, destPath);
    } else {
      // Copy files
      fs.copyFileSync(sourcePath, destPath);
      console.log(`Copied: ${sourcePath} -> ${destPath}`);
    }
  }
}

// Main function
function deploy() {
  console.log("Starting deployment...");

  try {
    // Build the application first
    console.log("Building application...");
    execSync("npm run build", { stdio: "inherit" });

    // Copy files
    console.log("Copying files to deployment directory...");
    copyFilesRecursively(sourceDir, destDir);

    console.log("Deployment completed successfully!");
  } catch (error) {
    console.error("Deployment failed:", error);
    process.exit(1);
  }
}

// Run the deployment
deploy();
