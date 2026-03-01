# 🎧 Footer Radio Player

A modern, lightweight and customizable sticky footer radio player for WordPress.

Supports **Shoutcast** and **Icecast** streams with live title updates, scrolling radio text, dynamic cover artwork and a standalone popup player generator.

---

## ✨ Features

- Sticky footer audio player
- Shoutcast support
- Icecast support
- Live title via AJAX
- Scrollable radio text
- Dynamic Cover Art (iTunes API)
- Customizable colors
- Popup player generator
- Desktop-only volume control
- Copy-to-clipboard generator button
- Modular 2.0 architecture

---

## 🏗 Architecture (since 2.0)

footer-radio-player/
│
├── footer-radio-player.php

├── includes/

│ ├── class-frp-core.php

│ ├── class-frp-admin.php

│ ├── class-frp-frontend.php

│

├── assets/

│ ├── player.css

│ ├── player.js

│ └── default-cover.png


### Core
Loads and initializes Admin + Frontend modules.

### Admin
Handles:
- Settings page
- Media uploader
- Popup generator
- Copy functionality

### Frontend
Handles:
- Player rendering
- AJAX title fetching
- Shoutcast & Icecast parsing

---

## 🚀 Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin in WordPress
3. Go to **Settings → Footer Radio Player**
4. Enter:
   - Stream URL
   - Server Base URL
   - Stream Type (Shoutcast or Icecast)
5. Configure colors and popup options

---

## 🎵 Popup Player

The plugin includes a popup generator.

1. Enter a Popup URL
2. Copy generated code
3. Create `index.php` in that directory
4. Paste the code

The popup includes:
- Live title AJAX
- Scroll animation
- Play button
- Desktop-only volume slider
- Dynamic cover art

---

## 🔐 Requirements

- WordPress 5.8+
- PHP 7.4+
- Shoutcast or Icecast server

---

## 📜 License

GPLv2 or later  
https://www.gnu.org/licenses/gpl-2.0.html

---

## 👤 Author

Martin Sievers

---

## 🛠 Roadmap

- Mountpoint selector for Icecast
- Title caching (transients)
- Optional no-PHP popup endpoint
- WordPress.org release preparation
- REST API endpoint option

---

## 🤝 Contributing

Pull requests are welcome.

For major changes, please open an issue first to discuss what you would like to change.
