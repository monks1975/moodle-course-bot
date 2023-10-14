# Moodle Block with Svelte Frontend Deployment Script
#
# This script automates the deployment process of a Moodle block with
# a Svelte frontend. It performs the build process for the Svelte app,
# increments the plugin version, and copies the necessary files to
# their respective locations in the Moodle directory structure.

import os
import shutil
import subprocess

# Set base directory for Moodle, residing in the docker persistence location
MOODLE_BASE_DIR = os.path.join(
    os.path.expanduser("~"), "documents", "docker", "moodle-persistence"
)

# Set the directories for the Moodle block and local JS files
BLOCK_DIR = os.path.join(MOODLE_BASE_DIR, "blocks", "openai_chat")
JS_DIR = os.path.join(MOODLE_BASE_DIR, "local", "js")

# Set the Svelte app's build directory and filename
SVELTE_BUILD_DIR = os.path.join(".", "svelte-app", "dist")
SVELTE_BUILD_FILENAME = "block_openai_chat.js"


# Increment the Moodle plugin's version in version.php
def increment_version(file_path):
    with open(file_path, "r") as f:
        lines = f.readlines()

    for i, line in enumerate(lines):
        if line.strip().startswith("$plugin->version"):
            parts = line.split("=")
            version = int(parts[1].strip().strip(";"))
            version += 1
            lines[i] = f"$plugin->version = {version};\n"
            break

    with open(file_path, "w") as f:
        f.writelines(lines)


# Build the Svelte app using npm
def run_npm_build():
    subprocess.run(["npm", "run", "build"], cwd="./svelte-app/")


# Copy the necessary files to the Moodle directory
def copy_files():
    shutil.copy(
        os.path.join(".", "moodle", "block_openai_chat", "version.php"), BLOCK_DIR
    )
    shutil.copy(
        os.path.join(".", "moodle", "block_openai_chat", "block_openai_chat.php"),
        BLOCK_DIR,
    )
    shutil.copy(os.path.join(SVELTE_BUILD_DIR, SVELTE_BUILD_FILENAME), JS_DIR)


# Main execution
if __name__ == "__main__":
    run_npm_build()  # Build Svelte App
    increment_version(
        os.path.join(".", "moodle", "block_openai_chat", "version.php")
    )  # Increment Moodle plugin version
    copy_files()  # Copy necessary files to Moodle directories
