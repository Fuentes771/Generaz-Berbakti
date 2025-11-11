# Assets Directory

## Structure

```
assets/
├── audio/          → Audio files for alerts (optional)
├── css/            → Stylesheets (layered architecture)
└── img/            → Images (organized by purpose)
```

## Audio Files

**Location:** `assets/audio/`

| File | Purpose | Used In | Size |
|------|---------|---------|------|
| `alert.mp3` | Alert notification sound | monitoring.php | ~4MB |
| `siren.mp3` | Emergency siren sound | monitoring.php | ~4MB |

**Note:** Audio files are optional. If files are missing, the monitoring page will still work but without sound alerts. The JavaScript handles missing audio gracefully with error suppression.

**Recommendation:** For production, consider using smaller audio files or CDN-hosted sounds to reduce repo size.

---

## CSS Files

**Location:** `assets/css/`

Layered architecture for maintainability:

| File | Purpose | Used By |
|------|---------|---------|
| `core.css` | Shared variables & utilities | All pages |
| `styles.css` | Landing page styles | index.php |
| `bmkg.css` | BMKG earthquake page | bmkg.php |
| `monitoring.css` | Sensor dashboard | monitoring.php |

**Load Order:** Always load `core.css` first, then page-specific CSS.

Example:
```html
<link rel="stylesheet" href="assets/css/core.css">
<link rel="stylesheet" href="assets/css/styles.css">
```

See `/assets/css/` for detailed CSS documentation.

---

## Image Files

**Location:** `assets/img/`

Organized by purpose for clarity:

### `hero/` - Main landing page images
- `tsunami-detection-system.png` - Hero section illustration

### `gallery/` - Equipment documentation photos
- `monitoring-equipment.jpg` - Seismic monitoring equipment
- `underwater-sensor.jpg` - Underwater vibration sensor
- `solar-power-unit.jpg` - Solar power installation

### `decorative/` - UI decorations & animations
- `dolphin-left.png` - Left floating dolphin animation
- `dolphin-right.png` - Right floating dolphin animation

### `unused/` - Legacy/backup files
Files not currently referenced in templates. Safe to delete if disk space needed.

---

## File Naming Convention

**Images:**
- Use descriptive kebab-case names
- Format: `{purpose}-{description}.{ext}`
- Examples: `tsunami-detection-system.png`, `monitoring-equipment.jpg`

**CSS:**
- Lowercase, hyphenated for multi-word
- Format: `{page|scope}.css`
- Examples: `core.css`, `bmkg.css`

**Audio:**
- Lowercase, descriptive
- Format: `{type}.mp3`
- Examples: `alert.mp3`, `siren.mp3`

---

## Maintenance

**Adding new images:**
1. Place in appropriate subfolder (`hero/`, `gallery/`, or `decorative/`)
2. Use descriptive filename following naming convention
3. Update this README if adding new category

**Removing unused files:**
- Check `unused/` folder periodically
- Verify files are not referenced anywhere: `grep -r "filename" .`
- Safe to delete if no references found

**Optimizing:**
- Compress images before committing (use tools like TinyPNG, ImageOptim)
- Consider WebP format for better compression
- Audio files can be converted to lower bitrate if needed

---

## Size Guidelines

**Images:**
- Hero images: max 500KB
- Gallery photos: max 300KB each
- Decorative elements: max 100KB each

**Audio:**
- Alert sounds: ideally < 100KB
- Current files are oversized (~4MB) - consider optimization

**CSS:**
- Keep individual files under 50KB unminified
- Use comments for documentation but avoid excessive
