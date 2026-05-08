# WP Movie Showcase

**Contributors:** franciscogaray
**Tags:** movies, omdb, gutenberg, block, imdb
**Requires at least:** 6.4
**Tested up to:** 6.5
**Requires PHP:** 7.4
**Stable tag:** 1.0.0
**License:** GPLv2 or later
**License URI:** [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

Search and display movie details from the OMDb API using a Gutenberg block.

---

## Description

WP Movie Showcase lets editors embed rich movie information into any post or page through a dedicated Gutenberg block. Movie data is fetched from the OMDb API [OMDb API](https://www.omdbapi.com/?utm_source=chatgpt.com) and stored alongside the block, so the frontend renders instantly without runtime API calls.

### Features

* Secure admin settings page for the OMDb API key (encrypted at rest).
* Custom Gutenberg block with title-based movie search.
* Server-side rendered movie card with poster, plot, cast, director, runtime, genre, and IMDb rating.
* REST proxy endpoint that keeps the API key server-side at all times.
* 12-hour transient cache to respect OMDb rate limits.
* Translation-ready (text domain: `wp-movie-showcase`).

---

## How it works

1. The plugin exposes an authenticated REST endpoint (`/wp-json/wp-movie-showcase/v1/search`) used only inside the block editor.
2. When an editor searches a title, the block calls this endpoint, which proxies the request to OMDb using the stored API key.
3. The normalized movie payload is saved as a block attribute.
4. On the frontend, a `render.php` template outputs an escaped, semantic movie card.

---

## Installation

1. Upload the `wp-movie-showcase` folder to `/wp-content/plugins/`, or install the zip via **Plugins → Add New → Upload Plugin**.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Get a free API key from [OMDb API Key Registration](https://www.omdbapi.com/apikey.aspx?utm_source=chatgpt.com)
4. Go to **Settings → Movie Showcase** and paste your API key.
5. Edit any post or page, add the **Movie Showcase** block, and search for a title.

---

## Build from source

If you cloned this plugin from a repository (no `build/` folder), you need Node.js 16+ and run:

```bash
npm install && npm run build
```

---

## Frequently Asked Questions

### Where is my API key stored?

The key is stored in the `wp_options` table under `wpms_options`, encrypted with AES-256-CBC using a key derived from your site's `wp_salt('auth')`. It is never exposed via the REST API or rendered to the browser.

---

### Does this plugin make API calls on every page load?

No. Movie data is stored as a block attribute when an editor saves the post, so the frontend serves cached HTML without contacting OMDb. Editor-time searches are additionally cached as transients for 12 hours.

---

### Can I search movies on the frontend (live search)?

Not in this version. The search functionality is intentionally limited to the block editor for security and performance. The frontend always renders the movie that was selected at edit time.

---

### What happens if OMDb returns no result?

The REST endpoint returns a `404` with an error message. In a future version, this could surface as an inline editor notice.

---

### Why does my movie card show stale information?

Movie data is captured when the block is saved. To refresh it, edit the post, re-search the title, and update the post.

---

### Does this work with the Classic Editor?

No. This plugin requires the Block Editor (Gutenberg).

---

### Is the OMDb API free?

OMDb offers a free tier limited to 1,000 daily requests. Patron tiers are available for higher limits. See [OMDb API](https://www.omdbapi.com/?utm_source=chatgpt.com).

---

## Changelog

### 1.0.0

* Initial release.
* Admin settings page with encrypted API key storage.
* Movie Showcase Gutenberg block with title search.
* REST proxy endpoint with capability check.
* Server-side rendering with frontend and editor styles.
* 12-hour transient cache for OMDb responses.

---

## Upgrade Notice

### 1.0.0

First release.

---

## Privacy

This plugin sends movie titles entered by editors to the OMDb API [OMDb API](https://www.omdbapi.com/?utm_source=chatgpt.com) along with your configured API key. No visitor data, IP addresses, or personal information is transmitted. Movie data returned by OMDb is stored in your WordPress database as part of post content.

---

## Third-Party Services

This plugin relies on the OMDb API to fetch movie information.

* **Service:** OMDb API
* **Endpoint:** [OMDb API](https://www.omdbapi.com/?utm_source=chatgpt.com)
* **Terms of Service:** [OMDb Legal Terms](https://www.omdbapi.com/legal.htm?utm_source=chatgpt.com)
* **Data sent:** movie title (entered by editor) and your API key
* **Data received:** movie metadata (title, year, plot, cast, poster URL, etc.)

Editors must obtain their own API key. The plugin author is not affiliated with OMDb.
