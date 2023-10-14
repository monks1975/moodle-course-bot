# Moodle Block with Svelte Frontend Deployment Guide

This guide provides instructions on how to build and deploy a Moodle block with a Svelte frontend.

---

## Requirements

- Moodle 4+
- PHP 7.2+
- Node.js 18+

---

## Docker

1. **Install and run the docker containers**, if you don't have a local Moodle install.
   ```bash
   cd svelte-app
   docker-compose up -d
   ```

This command will start the Docker containers based on the configuration in your `docker-compose.yml`.

### **Docker Volumes and Exposed Directories**

Your Docker setup exposes certain directories outside of the container, which allows for easier development and data persistence. These directories should be:

- **MariaDB Data**: `/Users/[USERNAME]/documents/docker/mariadb-persistence`
- **Moodle Data**: `/Users/[USERNAME]/documents/docker/moodle-persistence`

These are mapped to `/bitnami/mariadb` and `/bitnami/moodle` inside their respective containers.

```yaml
version: '3'
services:
  mariadb:
    # ...
    volumes:
      - /Users/[USERNAME]/documents/docker/mariadb-persistence:/bitnami/mariadb
    # ...

  moodle:
    # ...
    volumes:
      - /Users/[USERNAME]/documents/docker/moodle-persistence:/bitnami/moodle
    # ...
```

## Building the Svelte App

1. **Navigate to the Svelte App Directory**

   ```bash
   cd frontend
   ```

2. **Install Dependencies**

   ```bash
   npm install
   ```

3. **Build the Project**
   ```bash
   npm run build
   ```
   This will generate the JavaScript and CSS files required for the Moodle block, typically under a `dist` directory.

---

## Download and Deploy the Moodle Plugin

1. **Move the Folder**: Upload or move the `block_openai_chat` folder to the `blocks/` directory in your Moodle installation.
   ```bash
   mv block_openai_chat /path/to/moodle/blocks/
   ```

---

## Enable the Plugin

1. **Log in to Moodle**: Log in to your Moodle Admin panel.

2. **Navigate to Notifications**: Go to `Site administration > Notifications`.

3. **Upgrade Database**: Click "Upgrade Moodle database now" when prompted about the new plugin.

---

## Configuration

1. **Navigate to Configuration**: Go to `Site administration > Plugins > Blocks > Jons OpenAI Block`.

2. **Set API Key**: Enter your OpenAI API key and save the changes.

---

## ENV

Environment variables on the Svelte side:

```
VITE_MOODLE_PROTOCOL
VITE_MOODLE_BASEURL
VITE_MOODLE_PORT
VITE_OPENAIPROXY_PROTOCOL
VITE_OPENAIPROXY_BASEURL
VITE_OPENAIPROXY_PORT
```

## Usage

After installation, the block should be available for adding to course pages via the Moodle interface.

---

## Developer Notes

- Svelte App: Make sure to build the Svelte app and place the output files where the Moodle block can find them.
- API Key: The API key is stored securely in Moodle's configuration database and injected into the Svelte app.
- Web services user mikmakmok@gmail.com - Waghorn197522.
