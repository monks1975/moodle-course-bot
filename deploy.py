import os
import shutil
import subprocess

MOODLE_BASE_DIR = os.path.join(
    os.path.expanduser("~"), "documents", "docker", "moodle-persistence"
)
BLOCK_DIR = os.path.join(MOODLE_BASE_DIR, "blocks", "openai_chat")
JS_DIR = os.path.join(MOODLE_BASE_DIR, "local", "js")

SVELTE_BUILD_DIR = os.path.join(".", "svelte-app", "dist")
SVELTE_BUILD_FILENAME = "block_openai_chat.js"


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


def run_npm_build():
    subprocess.run(["npm", "run", "build"], cwd="./svelte-app/")


def copy_files():
    shutil.copy(
        os.path.join(".", "moodle", "block_openai_chat", "version.php"), BLOCK_DIR
    )
    shutil.copy(
        os.path.join(".", "moodle", "block_openai_chat", "block_openai_chat.php"),
        BLOCK_DIR,
    )
    shutil.copy(os.path.join(SVELTE_BUILD_DIR, SVELTE_BUILD_FILENAME), JS_DIR)


if __name__ == "__main__":
    run_npm_build()
    increment_version(os.path.join(".", "moodle", "block_openai_chat", "version.php"))
    copy_files()
