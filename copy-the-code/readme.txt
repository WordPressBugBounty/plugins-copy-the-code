=== Copy Anything to Clipboard for WordPress – Copy Button, Copy Text & Copy Code ===
Contributors: clipboardagency, freemius
Donate link: https://www.paypal.me/mwaghmare7/
Tags: copy to clipboard, copy button, copy text, copy code, clipboard
Tested up to: 6.9
Stable tag: 5.5.1
Requires PHP: 5.6
Requires at least: 4.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Copy Anything to Clipboard is the #1 WordPress copy-to-clipboard plugin trusted by 10,000+ active websites with 342,151+ downloads 🚀. Instantly add smart copy buttons to text, code, coupon codes, links, prompts, emails, or any content with built-in analytics and powerful Global Injector controls.

== Description ==

**Copy Anything to Clipboard** is a powerful **WordPress copy to clipboard plugin** that lets visitors copy text, links, coupon codes, code snippets, email addresses, commands, prompts, or any content with a single click.

Add smart **copy buttons anywhere on your WordPress website** and allow users to instantly copy content without manually selecting text. Perfect for blogs, documentation sites, coupon websites, SaaS tools, AI prompt libraries, developer tutorials, and marketing pages.

Whether you want to add a **copy button to code blocks**, allow users to **copy coupon codes**, share **ChatGPT prompts**, copy **email addresses**, or let visitors quickly grab **links, commands, or contact details**, this plugin makes copying content simple and user-friendly.

This flexible copy-to-clipboard plugin offers multiple integration methods including **automatic CSS selector targeting, shortcodes, Gutenberg blocks, and Elementor widgets**. Easily add copy buttons to code blocks, blockquotes, coupon codes, deals, contact information, and more.

---

**Key Features**

- **NEW: Global Injector** – Automatically inject copy buttons anywhere using CSS selectors with advanced display conditions
- **One-click copy to clipboard** functionality for any content
- **Automatic copy buttons for CSS selectors** (`pre`, `blockquote`, `code`, etc.)
- **Shortcode support** – `[copy]` and `[copy_inline]` with flexible attributes
- **Elementor widgets** for 25+ content types
- **Gutenberg blocks** for modern block-based WordPress sites
- **Smart display conditions** – load assets only on selected pages
- **Mobile-friendly and cross-browser compatible**
- **Lightweight and SEO-friendly**
- **Customizable copy button styles** (Button, Icon, Cover) and positions
- **Copy as HTML or plain text**
- **Works with all WordPress themes and plugins**

---

Perfect for **bloggers, developers, e-commerce stores, educational platforms, documentation websites, and marketing pages** that want to make copying content fast and effortless. 📋

== How to Use ==

You can add copy buttons anywhere on your WordPress site using one of the following methods:

1. **NEW: [Global Injector](https://docs.clipboard.agency/guides/global-injector/)**  
   Automatically inject copy buttons into any element using CSS selectors with advanced display conditions.

2. **[Automatic CSS Selector](https://docs.clipboard.agency/guides/css-selectors/)**  
   Automatically add copy buttons to elements like `pre`, `code`, `blockquote`, or any custom selector.

3. **[Shortcodes](https://docs.clipboard.agency/guides/shortcodes/)**  
   Manually add copy buttons using `[copy]` or `[copy_inline]` shortcodes anywhere in your content.

4. **Elementor Widgets**  
   Add copy buttons visually using dedicated Elementor widgets.

5. **Gutenberg Blocks**  
   Insert copy buttons directly inside the WordPress block editor using Gutenberg blocks.

=== METHOD 1 – Global Injector (NEW in v5.0.0) ===

The **Global Injector** is the most powerful way to automatically add copy buttons anywhere on your website.

It allows you to target elements using CSS selectors and control exactly where the copy buttons appear.

**Key Capabilities**

* **Display Conditions** – Show copy buttons only on specific pages, posts, categories, or custom conditions.
* **Multiple Style Presets** – Choose from **Button**, **Icon**, or **Cover** styles with live preview.
* **Advanced Targeting** – Precisely target elements using CSS selectors such as `pre`, `code`, `blockquote`, and more.
* **Performance Optimized** – Plugin assets load only on pages where copy buttons are used.

==== How to Use Global Injector ====

1. Go to **Copy to Clipboard → Global Injector** in your WordPress dashboard.
2. Click **Add New Injector**.
3. Enter the **CSS selector** you want to target (for example: `pre`, `code`, or `blockquote`).
4. Select a **style preset** – Button, Icon, or Cover.
5. (Optional) Configure **Display Conditions** to control where the copy button appears.
6. Click **Save** and preview the changes on your site.

=== METHOD 2 – Automatically with "CSS Selector" ===

This is the **quickest and most commonly used method**. It automatically adds copy buttons to all elements that match a specific CSS selector.

Use this method when you want copy buttons to appear **automatically across your entire website** without manually adding them to each element.

==== Examples ====

**Example 1 – Code Snippets**

If your posts contain code snippets inside the `<pre>` tag, simply set the selector to:

`pre`

The plugin will automatically add a **copy button to every `<pre>` element**, allowing visitors to copy code snippets with one click.

**Example 2 – Blockquotes**

If you want users to copy quotes from your blog posts, set the selector to:

`blockquote`

A copy button will automatically appear for **all blockquote elements** on your site.

You can target **any HTML element or custom class** using CSS selectors such as:

`pre`, `code`, `blockquote`, `.coupon-code`, `.email-address`, or `.copy-this`

==== How to Use with CSS Selector ====

1. Go to **Copy to Clipboard** in your WordPress dashboard.
2. Click the **Add New** button.
3. Enter the **CSS selector** you want to target (for example: `pre` or `blockquote`).
4. Click **Create** to save the selector.
5. Visit your page and you will see the **copy button automatically added** to the targeted elements.

For more detailed guides:

- [Add copy buttons to blockquotes](https://docs.clipboard.agency/guides/shortcodes/)
- [Add copy buttons to code snippets](https://clipboard.agency/blog/how-to-add-copy-buttons-to-code-blocks/)

=== METHOD 3 – Manually with "Shortcodes" ===

Use this method when you want to add a copy button **only to specific content** instead of applying it automatically across the entire site.

The plugin provides two shortcodes:

- `[copy]` – Wrap content inside the shortcode
- `[copy_inline]` – Copy inline text using attributes

==== Example – Wrapping Content ====

```

The Zoom meeting is scheduled on [copy]15 November 2022[/copy]. Please note it down.

```

This will add a copy button that allows users to copy **"15 November 2022"** with one click.

==== Example – Inline Content ====

You can use `[copy_inline]` to copy inline values such as emails, phone numbers, or links.

```

Contact us:

* Email: [copy_inline text="[contact@clipboard.agency](mailto:contact@clipboard.agency)"]
* Phone: [copy_inline text="+91 1234567890"]
* Address: [copy_inline text="123, Street, City, State, Country"]
* Website: [copy_inline text="[https://clipboard.agency/](https://clipboard.agency/)"]
* Facebook: [copy_inline text="[https://www.facebook.com/clipboard.agency/](https://www.facebook.com/clipboard.agency/)"]

```

==== Example – Coupon Code ====

```

Use coupon code [copy_inline text="COUPONCODE"] to get 10% discount.

```

==== Example – Deal Link ====

```

Get the deal: [copy_inline text="[https://clipboard.agency/deal/](https://clipboard.agency/deal/)"]

```

==== Example – Username and Password ====

```

Username: [copy_inline text="username"]
Password: [copy_inline text="password" display="********"]

```

This allows users to **copy sensitive values while displaying masked text**.
```

=== METHOD 4 – Manually with "Elementor Widgets" ===

The plugin includes **multiple Elementor widgets** that allow you to easily add copy buttons to specific content on your page.

Simply drag and drop a widget and configure the content you want visitors to copy.

These widgets are useful for many types of content including:

- Emails
- ChatGPT / AI prompts
- Coupon codes
- Deals and affiliate links
- Inspirational quotes
- Contact information
- Addresses
- Social media posts
- Commands and technical code
- Passwords and secure values
- Notes, reminders, and checklists
- Blog content and educational materials
- Hashtags and social media resources
- Research notes and documentation
- Travel information and packing lists
- Personal messages and quotes

You can explore **live examples for all widgets here:**

👉 [View Elementor Widget Demos](https://clipboard.agency/demos/)

---

==== How to Use Elementor Widgets ====

1. Install and activate the **Elementor** plugin.
2. Open a page and click **Edit with Elementor**.
3. Search for the widget (for example: **Copy**).
4. Drag and drop the widget onto the page.
5. Enter the content you want users to copy.
6. Publish the page and test the **copy button**.

For full examples and widget demos:

👉 [See All Live Demos](https://clipboard.agency/#demos)

---

== Key Benefits ==

- **Global Injector (NEW)** – Automatically inject copy buttons anywhere using CSS selectors with display conditions. 🎯  
- **Display Conditions** – Control exactly where copy buttons appear for better performance. ⚡  
- **Multiple Styles** – Choose from **Button, Icon, or Cover** styles. 🎨  
- **Copy with One Click** – No need for highlighting or manual copying. 🎉  
- **Multi-Purpose Usage** – Copy text, links, coupon codes, commands, prompts, and more. 🌐  
- **Seamless Integration** – Works with most WordPress themes and plugins. 💼  
- **Mobile Friendly** – Fully compatible with smartphones and tablets. 📱  
- **Cross-Browser Compatible** – Works across all modern browsers. 🌐  
- **Lightweight & Fast** – Designed to avoid slowing down your site. 🚀  
- **Developer Friendly** – Easy to integrate with custom code and layouts. 👨‍💻  
- **SEO Safe** – Does not impact search engine performance. 📈  
- **Customization (PRO)** – Customize styles, behavior, and advanced features. 🎨  

---

Upgrade to **PRO** and unlock advanced features, customization options, and analytics.

👉 https://clipboard.agency/pricing/

## Popular Copy to Clipboard Plugin

Over **10,000+ active websites** are using **Copy Anything to Clipboard** to easily add copy buttons across their content.

The plugin is **fully compatible with all WordPress themes and plugins**.

### Feature Requests

Have an idea or suggestion?

We welcome feedback and feature requests.

👉 https://clipboard.agency/contact/

---

## Frequently Asked Questions

### What is the Global Injector?

The **Global Injector** (introduced in v5.0.0) allows you to automatically add copy buttons anywhere on your website using **CSS selectors**.

Features include:

- Target any element using CSS selectors
- Choose style presets (Button, Icon, Cover)
- Configure display conditions
- Live preview while configuring

You can access it here:

**WordPress Dashboard → Copy to Clipboard → Global Injector**

---

### How do I add copy buttons to code snippets?

Use the **Global Injector** or CSS selector method.

Example selector:

```

pre

```

or

```

code

```

Steps:

1. Go to **Copy to Clipboard**
2. Click **Add New** or open **Global Injector**
3. Enter selector `pre` or `code`
4. Configure button style
5. Save settings

Copy buttons will automatically appear on all code blocks.

---

### Can I add copy buttons to specific content only?

Yes.

Use the following shortcodes:

**Block content**

```

[copy]Your text here[/copy]

```

**Inline content**

```

[copy_inline text="Text to copy"]

```

This allows you to add copy buttons only where needed.

---

### Does this plugin work with Elementor?

Yes.

The plugin provides **25+ Elementor widgets**, including:

- Copy Button
- Copy Icon
- Code Snippet
- Coupon Code
- Deal Link
- Email Address
- Phone Number
- Blockquote
- ChatGPT Prompt
- and many more.

Simply search for **"Copy"** in the Elementor widget panel.

---

### Does this plugin work with Gutenberg?

Yes.

We provide Gutenberg blocks such as:

- Copy Button
- Copy Icon
- Term Title
- Social Share

All blocks work seamlessly with the **WordPress Block Editor**.

---

### Can I customize the copy button appearance?

Yes.

You can customize:

- Button style
- Icon style
- Colors
- Position (inside or outside content)
- Button text

The **Global Injector includes a live preview** so you can instantly see style changes.

The **PRO version** unlocks additional customization options.

---

### How can I control where copy buttons appear?

Use **Display Conditions** inside the Global Injector.

You can target:

- Specific pages or posts
- Post types
- Categories
- Tags
- Custom taxonomies
- User roles

This helps optimize both **design and performance**.

---

### Is the plugin mobile friendly?

Yes.

Copy functionality works perfectly on **mobile phones, tablets, and desktops**.

---

### Does it work in all browsers?

Yes.

The plugin supports all modern browsers including:

- Chrome
- Firefox
- Safari
- Edge

---

### Will this plugin slow down my website?

No.

The plugin is **lightweight and optimized for performance** and will not slow down your WordPress site.

---

### Can the plugin copy HTML content?

Yes.

You can copy content as:

- **Plain text**
- **HTML format**

This is useful for copying code snippets, formatted content, or rich text.

=== Google Chrome Extension 🚀 ===

We also provide a **Google Chrome Extension** that allows users to quickly copy content from any website.

You can install it directly from the Chrome Web Store:

👉 https://chromewebstore.google.com/detail/copy-anything-to-clipboar/mdljigkhfeiobmhanibkgjkldnabeahl

The extension works perfectly alongside the **Copy Anything to Clipboard WordPress plugin**, making it easy to copy text, links, commands, code snippets, and more across the web.

---

=== Further Reading 📚 ===

Learn more about the plugin and explore additional resources:

- 🌐 Official website: https://clipboard.agency/
- 📖 Documentation & guides: https://clipboard.agency/doc/
- 🎬 Live demos: https://clipboard.agency/#demos
- 💬 Contact support: https://clipboard.agency/contact/
- 🔌 View all our WordPress plugins: https://wordpress.org/plugins/search/clipboardagency/

If you find the plugin helpful, consider supporting development with a small donation:

💖 https://www.paypal.me/mwaghmare7/

---

== Screenshots ==

1. **Global Injector – Copy Button Rules**  
Add and manage automatic copy button rules with **CSS selectors, display conditions, and style presets** like Button, Icon, and Cover.

2. **Copy Buttons on the Frontend**  
Copy buttons automatically appear on **code snippets, blockquotes, commands, coupon codes, or any content element**.

3. **Shortcodes and Gutenberg Blocks**  
Add copy buttons manually using `[copy]` and `[copy_inline]` shortcodes or use **Copy Button and Copy Icon blocks** in the WordPress block editor.

---

== Installation ==

1. Install the **Copy Anything to Clipboard** plugin from the WordPress plugin directory, or upload it to:

```

wp-content/plugins

```

2. Activate the plugin through the **Plugins** menu in WordPress.

3. Go to **WordPress Admin → Copy to Clipboard**.

4. Choose one of the following methods to add copy buttons:

- **Global Injector (Recommended)**  
  Automatically inject copy buttons using CSS selectors and display conditions.

- **CSS Selector**  
  Automatically add copy buttons to elements like `pre`, `code`, or `blockquote`.

- **Shortcodes**  
  Manually add copy buttons using `[copy]` or `[copy_inline]`.

- **Elementor Widgets**  
  Drag-and-drop widgets for Elementor page builder.

- **Gutenberg Blocks**  
  Use native WordPress blocks for copy buttons and icons.

For full setup instructions, read the documentation:

👉 https://clipboard.agency/doc/

---

== Blocks ==

This plugin provides **4 blocks for the WordPress block editor**:

### Copy Icon
Add a small icon that lets users quickly copy text or code to the clipboard.

### Copy Button
Add a visible button that allows users to copy content with one click.

### Term Title
Display the title of the current taxonomy term such as category or tag.

### Social Share
Allow visitors to quickly share content across social media platforms.

== Changelog ==

= 5.5.1 =

* **Fix: Highlight.js / code block copy formatting** – Copy buttons added via Global Injector to `pre` and `code` blocks (including themes using Highlight.js, such as the setup on zanglikun.com) now preserve indentation and whitespace when copying, so Java / PHP / shell snippets paste exactly as shown instead of being flattened into a single left-aligned block. Thanks to Likun Zang for reporting the issue.
* **Fix: [copy] shortcode in table cells** – `[copy]` and `[copy_inline]` inside table plugins (Data Tables Generator by Supsystic, wpDataTables) now work correctly. Copy script and styles load when the table shortcode is on the page, and copy buttons work with cached or dynamically rendered table content. Thanks to [@jayceezay](https://wordpress.org/support/users/jayceezay/) for reporting. See: https://wordpress.org/support/topic/copy-shortcode-in-table-cells-not-working-anymore/
* **Fix: Thrive Architect HTML block integration** – Copy buttons rendered inside a Thrive Architect HTML block (for example, custom blockquotes with `ctc-wrapper` markup) are now reliably detected and bound by the frontend script, so clicking the button correctly copies the content even when the HTML is pre-rendered or cached.
* **Improvement: Shortcode copy buttons (event delegation)** – Copy buttons from `[copy]` shortcodes now use document-level event delegation, so buttons added dynamically (e.g. by table plugins after page load) remain clickable.
* **Improvement: Code / pre support in Global Injector** – Global Injector rules targeting `pre` / `code` elements now integrate more safely with external highlighters and pre-rendered content, ensuring the “Copy Anything to Clipboard” behavior stays consistent without breaking existing theme or page builder output.

= 5.5.0 =

* **New: Gutenberg block analytics (Pro)** - Copy events from all Gutenberg copy blocks (Copy to Clipboard Icon, Copy Icon, and Copy Button) are now tracked as `source = 'gutenberg-block'` in the same analytics table. Pro Analytics dashboard shows a three-way source breakdown (Global Injector, Shortcodes, Gutenberg Blocks) and a Block by page view so you can see which pages drive the most Gutenberg block copy events.
* **Improvement: Gutenberg block tracking** - Gutenberg copy block output includes `data-ctc-analytics` and `data-ctc-source="gutenberg-block"`; frontend sends analytics to `POST /ctc/v1/analytics/events` after each copy (non-blocking). Block scripts and styles still load only when the relevant block is present on the page.

= 5.4.2 =

* **Fix: Icon block frontend scripts only load when block is present** - Icon block (Copy to Clipboard) styles and copy script are now enqueued only on pages that contain the Icon block, using `has_block( 'copy-the-code/icon' )`. This avoids loading assets on every page and improves performance when the block is not used.

= 5.4.1 =

* **New: Telemetry opt-in** - Anonymized usage data can be shared to help improve the plugin. Opt-in is available from the welcome notice and via Settings → Dashboard; data is sent only when enabled and is cleaned on uninstall.
* **Improvement: Copy-by-source analytics and telemetry summary (Pro)** - Analytics now includes a breakdown by copy source and a telemetry summary for opted-in usage insights.
* **Improvement: Uninstall cleanup** - Telemetry and related options are removed when the plugin is uninstalled.
* **Fix: Freemius not loading** - Resolved an issue where Freemius SDK could fail to load in some environments.

= 5.4.0 =

* **New: Shortcode analytics + per-shortcode opt-out (Pro)** - Shortcode-based copy events are now tracked as `source = 'shortcode'` with post and page context, alongside Global Injector events. You can disable tracking for specific shortcodes via an `analytics` attribute (for example, `[copy analytics="off"]Code[/copy]` still copies but does not record analytics for that instance).
* **New: Source breakdown & shortcode-by-page (Pro)** - The detailed Analytics endpoint now includes a Global Injector vs Shortcodes source breakdown and a shortcode-by-page view so you can see which pages and shortcodes drive the most copy events.
* **Improvement: Volume vs Prior card accuracy** - The Volume vs Prior card now shows `/ N prev` whenever previous-period data exists (including `0`), reserving `-- prev` only for truly missing data, and uses clearer trend labels for trending vs lagging periods.
* **Improvement: Free vs Pro previews in Analytics UI** - Free users see a blurred preview for Pro-only source breakdown data so the UI is no longer misleading; Pro users see real counts, percentages, and growth.
* **Improvement: Version consistency & cache-busting** - Unified plugin version across the header, `CTC_VER` constant, core class, composer, and readme. Release tooling (`npm run version:check` / `version:set`) now fails on mismatches and keeps all version fields in sync, preventing stale assets and updater drift.
* **Fix: Coupon widget copy button label** - The Elementor coupon widget now passes the correct config keys to the shared copy button helper, and the helper accepts both legacy (`copy_button_text`) and new (`button_text`) keys so the custom button text from widget settings always renders as expected.
* **Fix: Preserve zero-valued style settings** - Global Injector rule meta now treats saved `'0'` values (e.g. blur, border width, padding) as valid instead of falling back to defaults, so zero-style configurations are applied consistently after saving.
* **Security: Safer external links** - All plugin-generated links that open in a new tab (`target="_blank"`) now include `rel="noopener noreferrer"` to guard against reverse-tabnabbing and improve cross-window isolation.
* **Security: Pro analytics limited to admins** - Pro analytics REST endpoints now require the `manage_options` capability, preventing non-admin users from accessing analytics data even if they obtain a valid REST nonce.
* **Security: Analytics abuse protections** - The public analytics event endpoint (`POST /ctc/v1/analytics/events`) now enforces per-IP rate limiting and strict payload validation, returning `429` on abusive bursts and `400` for invalid event data to protect the `ctc_analytics` table.
* **Improvement: Analytics timezone normalization** - Analytics events and query windows now consistently use UTC for `created_at` and date ranges, preventing shifted 24h/7d/30d boundaries on non-UTC sites.
* **Improvement: Analytics retention & cleanup** - Older analytics events beyond roughly 13 months are now pruned in small batches by a daily scheduled cleanup job (retention days and batch size are filterable), keeping the `ctc_analytics` table fast while still supporting long-range comparisons.
* **Fix: Emojis in code blocks now copied correctly** - WordPress renders emojis as `<img class="emoji">` elements; the copy logic now converts these to their `alt` text so emojis are included in the copied content. [Thanks @jayceezay](https://wordpress.org/support/topic/emojis-in-code-blocks-not-being-copied/)

= 5.3.1 =

* **Fix: [copy] shortcode with formatted content** - When the shortcode encloses formatted text (e.g. bold), the front end now displays the formatting and copies plain text to the clipboard. Display uses allowed HTML (filterable); copy uses plain text unless `copy-as="html"`. [Thanks @jayceezay](https://wordpress.org/support/topic/copy-shortcode-is-broken/)
* **New (developer):** Filter `ctc/shortcode/display_allowed_html` to customize allowed HTML for shortcode display content (e.g. add custom markup support).

= 5.3.0 =

* **New: Analytics (Pro)** - See what gets copied. Copy tracking for Global Injector rules: track copy events per rule (rule ID, timestamp, success; no content stored). Analytics dashboard at Settings → Analytics: summary cards (total copies, active rules, top rule, % change), date range filter (24h, 7d, 30d, custom), activity chart, top rules table. Export CSV (Pro).
* **New: Activity column in Main Rule List** - Copy count and 24h % change per rule. Free: count + blurred trend + Pro CTA; Pro: real trend. Link from Activity to Analytics filtered by rule.
* **New: REST API for analytics** - `POST /ctc/v1/analytics/events` (public, for frontend tracking); GET endpoints and export for authenticated Pro users.
* **Improvement: Global Injector** - Analytics option/link in rule editor and dashboard to open Analytics page (filtered by rule when applicable).

= 5.2.0 =

* **New: Copy As (Clipboard Type)** - Choose what gets copied per rule: Text only, HTML, Text + HTML (default), Image (Pro), JSON (Pro), SVG (Pro). Global Injector Rule Editor includes "Copy as" section; shortcode and Gutenberg blocks support `copy-as` attribute. Uses CTC CopyEngine with Clipboard API; fallbacks for image (HTTPS/CORS) and legacy browsers.
* **New: Image format for Copy As Image (Pro)** - Choose PNG, JPEG, or WebP when copying as image. Pro adds Image format selector in Copy As section; free plugin provides `ctc.globalInjector.copyAs.after` hook for extensibility.
* **Improvement: Copy As extensibility** - Free plugin now uses `ctc.globalInjector.copyAs.after` hook; Pro plugin renders Image format UI (PNG/JPEG/WebP) when Copy As is Image Blob. Follows Rank Math free/Pro pattern.
* **Improvement: Pro extension points** - Added `ctc/global_injector/rule_enum_fields`, `ctc/global_injector/rest_rule_data` filters; `ctc.globalInjector.fieldsToCompare` for unsaved-changes detection. Pro adds image_format via extend_meta_mapping, extend_admin_rule_data, extend_frontend_rule_data.
* **New: ImageFormatSection (Pro)** - Pro component for Image format selection with ButtonGroup and docs link.
* **Fix: Image format persistence** - Image format selection now persists after save and page refresh (load_rules and REST get_rule_data include image_format when Pro is active).

= 5.1.0 =

* **New: Main Rule List** - Custom rules UI at Settings → Global Injector (`page=ctc-rules`): table with Status toggle, Rule Name/Target, Visual Style, Location; row actions Edit, Duplicate, Delete; search and status filter; empty state.
* **New: Dashboard (Home)** - Copy to Clipboard dashboard at `page=ctc` (Settings → Copy to Clipboard).
* **New: Shared admin components** - AdminHeader, ConfirmModal, Footer, Icons, ProBadge used by Dashboard, Main Rule List, and Rule Editor.
* **Improvement: Rule Editor** - Header uses shared AdminHeader with back link to Global Rules list.
* **Improvement: Legacy CPT redirects** - "Add New" and list edit links for `copy-to-clipboard` redirect to Global Injector.
* **Developer:** `get_admin_rules()` API for admin list data; Main Rule List and Dashboard use React + Tailwind under `.ctc-admin-root`.
* **Improvement: Global Injector CSS from PHP** - Global Injector styles (Button, Icon, Cover) are now output as minified inline CSS from PHP via the new Inline_CSS class. Only the styles and positions used by active rules are included; the `ctc/global_injector/inline_css` filter still applies to the final CSS.
* **Fix: Cover style fatal error** - Added missing `get_global_injector_css()` method to the Cover style class so inline CSS builds correctly when Cover style is used (fixes "Call to undefined method Cover::get_global_injector_css()").
* **New: Admin bar quick-edit for Global Injector** - When viewing a page where Global Injector rules apply, admins see a "CTC" item (with clipboard icon) in the admin bar. Sub-items list "Edit: [Rule name]" for each rule on the page and open the Global Injector settings with that rule selected.
* **Improvement: Global Injector URL support** - The settings page now supports a `?rule=ID` query parameter to open the editor with a specific rule selected (e.g. from the admin bar or bookmarks). Added `get_selected_rule_id()` helper.
* **New: Shortcode `redirect` attribute** - The `[copy]` shortcode now supports a `redirect` attribute for copy-then-redirect flows (e.g. `[copy redirect="https://store.com"]CODE10[/copy]`). After copying, the user is sent to the given URL. The existing `link` attribute remains supported for backward compatibility and has the same effect as `redirect`.
* **Fix: Shortcode display text with inner content** - When both `text` and inner content are present (e.g. `[copy text="Hello World"]Copy this[/copy]`), the visible label now uses the inner content ("Copy this") and `text` is used only for the copy payload. Previously the label showed the `text` value.
* **Fix: Native preset CSS when using tag="a"** - Using `tag="a"` for link-style copy buttons now correctly loads the native preset CSS so theme link styling is applied.
* **Fix: Shortcode `link` attribute redirect** - The "Copy & Redirect" / "Combining Multiple Attributes" flow now works: when `link="https://example.com"` is set, the shortcode outputs `data-ctc-link` and the frontend opens the URL in a new tab after a successful copy (e.g. after "Copied! Redirecting...").
* **Improvement: Shortcode performance** - Inline CSS for the shortcode is now output only for presets actually used on the page (inline, native, button, icon, cover), and the CSS is minified to reduce payload size.
* **Improvement: Global Injector rules sidebar** - When opening the settings page with a specific rule (e.g. `?rule=318`), the selected rule is now scrolled to the top of the rules list within the sidebar only, without scrolling the whole page or moving the admin header out of view.
* **Fix: Giant copy icons in tables** - Added explicit `width` and `height` attributes (24×24) to all Global Injector SVG icons (Button, Icon, and Cover styles) so theme CSS cannot scale them unexpectedly inside table cells. [Thanks @akashathu](https://wordpress.org/support/topic/giant-icons-in-table/)

= 5.0.1 =

**Bug fixes and support issues (post-5.0.0)** — Thanks to the users who reported these on the [support forum](https://wordpress.org/support/plugin/copy-the-code/).

* **Fix: 404 on ctc.js** - Moved script to `assets/frontend/js/lib/ctc.js` (renamed from vendor/ to avoid .gitignore conflict). Global Injector and shortcode enqueue the copy engine; fallbacks remain when script is unavailable. [Thanks @contemplate](https://wordpress.org/support/topic/404-js-file/)
* **Fix: Giant copy icons in tables** - Copy icons inside table cells no longer scale with table font-size. Added CSS constraints for shortcode and block copy icons when inside `table` elements.
* **Fix: Square brackets in shortcode content** - Legacy shortcodes using `content="... &#91;...&#93; ..."` now copy the correct `[` and `]` characters. The `content` attribute is decoded from HTML entities before use.
* **Fix: Shortcode script loading** - Shortcode frontend script no longer depends on the removed vendor script; it loads independently with Clipboard API fallbacks.

= 5.0.0 =

**🚀 Major Release - Global Injector & Complete Refactor**

* **New: Global Injector** - Advanced copy button injection system with:
  * Display conditions to control where copy buttons appear
  * Multiple style presets (Button, Icon, Cover)
  * Live preview in the editor
  * Performance-optimized asset loading
* **New: Refined Shortcode System** - Enhanced `[copy]` and `[copy_inline]` shortcodes with improved attributes and flexibility.
* **New: Style Presets** - Pre-built button, icon, and cover styles for quick setup.
* **Improvement: WordPress 6.7+ Compatibility** - Fixed `_load_textdomain_just_in_time` notices with proper plugin initialization timing.
* **Improvement: Complete Asset Restructure** - Reorganized frontend and admin assets for better performance.
* **Improvement: Gutenberg Block Enhancements** - Fixed block registration and optimized editor asset loading.
* **Improvement: Freemius SDK Integration** - Improved SDK initialization with proper hook timing.
* **Improvement: Code Quality** - Major codebase refactoring with improved naming conventions and standards.
* **Fix: Block Editor Assets** - Resolved "invalid category" warnings for Gutenberg blocks.
* **Fix: Frontend Script Loading** - Fixed 404 errors for clipboard scripts after asset restructure.
* **Fix: Square Brackets in Shortcode** - Fixed HTML entities (`&#91;` `&#93;`) not being decoded properly in `content` attribute. [Thanks @akashathu](https://wordpress.org/support/topic/issue-with-square-brackets/)
* **Fix: CSS var() Double Dashes** - Fixed WordPress converting `--` to en-dash inside CSS `var()` functions like `var(--wp--preset--color--bg)`. [Thanks @mikecargal](https://wordpress.org/support/topic/converted-to-2/)
* **Fix: Legacy Shortcode `content` Attribute** - Restored proper handling where `content` attribute contains the text to copy and `text` attribute is the display text.
* **New: Native Anchor Support** - Added `tag="a"` parameter to use theme's anchor styling instead of custom CTC styling.
* **New: Icon Toggle** - Added `show-icon="no"` parameter to hide the copy icon when not needed.
* **Developer: New Architecture** - Modular class-based architecture for better extensibility.

= 4.1.2 =

* Fixed: iOS 26 compatibility issue by migrating from deprecated `document.execCommand('copy')` to modern Clipboard API (`navigator.clipboard.writeText()`).
* Improvement: Added fallback support for older browsers that don't support the Clipboard API.
* Improvement: Enhanced clipboard functionality across all copy methods (CSS selector, shortcode, Elementor widgets, and Gutenberg blocks).

= 4.1.1 =

* Fix: Dependency build failed.

= 4.1.0 =

* Improvement: Updated Freemius SDK library with version 2.13.0.
* Improvement: Updated plugin metadata for compatibility with WordPress 6.8.
* Improvement: Enhanced readme content, tags, and FAQ for better search visibility.

= 4.0.5 =

* Improvement: Updated Freemius SDK library with version 2.11.0.
* Improvement: Compatibility to WordPress 6.7.2.

= 4.0.4 =

* Fixed: Disallow to add script for the contributor level user in the shortcode.
* Improvement: Updated Freemius SDK library with version 2.9.0.
* Improvement: Compatibility to WordPress 6.7.1.

= 4.0.3 =

* Hot fix: The dependency not works for post types.

= 4.0.2 =

* Fix: The js dependency not works for the shortcode.

= 4.0.0 =

* New: Added the Gutenberg block "Term Title".
* New: Added the Gutenberg block "Social Share".
* New: Added the Gutenberg block "Copy Button".
* New: Added the Gutenberg block "Copy Icon".
* Improvement: Updated Freemius SDK library with version 2.7.4.
* Improvement: Compatibility to WordPress 6.6.1.

= 3.8.3 =

* Improvement: Updated the recurring cron job registration logic.

= 3.8.2 =

* Fixed: Elementor copy button not works due to missing dependency.

= 3.8.1 =

* Fixed: The copy animation not works for the shortcode.
* Improvement: Compatibility to WordPress 6.5.3.

= 3.8.0 =

* New: Added display conditions for the copy to clipboard to load the assets only on selected pages.
* Improvement: Fixed the copy icon Gutenberg block title issue.
* Improvement: Loading the Gutenberg block assets only on the block used pages.
* Improvement: Added default copied button text.
* Improvement: Compatibility to WordPress 6.5.2.

= 3.7.0 =

* New: Added the copy icon Gutenberg block.

= 3.6.0 =

* New: Added the target selector support for the copy content in the Elementor widget.
* Improvement: Added to support to copy the markup content from the Copy Icon Elementor widget.

= 3.5.2 =

* Improvement: Added the dynamic content support for email, phone, and address Elementor widgets.
* Improvement: Compatibility to WordPress 6.4.3.

= 3.5.1 =

* Improvement: Added the background image support for all the Elementor widgets.
* Improvement: Updated Freemius SDK library with version 2.6.2.

= 3.5.0 =

* New: Added the "Contact Information" Elementor widget.
* Improvement: Compatibility to WordPress 6.4.2.

= 3.4.3 =

* Fix: The shortcode copy the display text instead of the content.

= 3.4.2 =

* Improvement: Added the shortcode support for the Elementor widget.
* Improvement: Added the missing table widget in the Elementor category.

= 3.4.1 =

* Fix: The button customization is not working in the dashboard screen.

= 3.4.0 =

* New: Added table Elementor widget to display content in horizontal, or vertical table and allow to copy the content.

= 3.3.0 =

* New: Added button styling support for Elementor widget Copy Icon.
* New: Added button styling support for Elementor widget Copy Button.
* New: Added button styling support for Elementor widget Email Sample.
* New: Added button styling support for Elementor widget Email Address.
* New: Added button styling support for Elementor widget Phone Number.
* New: Added button styling support for Elementor widget Blockquote.
* New: Added button styling support for Elementor widget Code Snippet.
* New: Added button styling support for Elementor widget Message.
* New: Added button styling support for Elementor widget Deal.
* New: Added button styling support for Elementor widget Coupon.
* New: Added button styling support for Elementor widget AI Prompt Generator.
* Improvement: Updated Freemius SDK version 2.6.0.

= 3.2.1 =

* Improvement: Improved the Elementor "Coupon Code" widget controls and structure.
* Improvement: Improved the Elementor "Deal" widget controls and structure.
* Improvement: Improved the Elementor "Email Address" widget controls and structure.
* Improvement: Improved the Elementor "Email Sample" widget controls and structure.
* Improvement: Improved the Elementor "Message" widget controls and structure.
* Improvement: Improved the Elementor "Phone Number" widget controls and structure.
* Improvement: Improved the Elementor "Shayari" widget controls and structure.
* Improvement: Improved the Elementor "SMS" widget controls and structure.
* Improvement: Improved the Elementor "Wish" widget controls and structure.

= 3.2.0 =

* New: Added the Elementor widget category "Copy Anything to Clipboard".
* Improvement: Compatibility to WordPress 6.4.1.
* Improvement: Improved the Elementor "AI Prompt Generator" widget controls and structure.
* Improvement: Improved the Elementor "Blockquote" widget controls and structure.
* Improvement: Improved the Elementor "Code Snippet" widget controls and structure.
* Improvement: Improved the Elementor "Copy to Clipboard Button" widget controls and structure.
* Improvement: Improved the Elementor "Copy to Clipboard Icon" widget controls and structure.

= 3.1.0 =

* New: Added new shortcode [copy_inline] which allow you to copy content from the inline element.
* New: Added Elementor widget Copy Icon.
* New: Added Elementor widget Copy Button.
* New: Added Elementor widget Email Sample.
* New: Added Elementor widget Email Address.
* New: Added Elementor widget Phone Number.
* New: Added Elementor widget Blockquote.
* New: Added Elementor widget Code Snippet.
* New: Added Elementor widget Message.
* New: Added Elementor widget Deal.
* New: Added Elementor widget Coupon.
* New: Added Elementor widget AI Prompt Generator.

= 3.0.0 =

* New: Improve the dashboard UI with the new design.
* Improvement: Added the support to edit the existing copy to clipboard post.
* Improvement: Compatibility to WordPress 6.3.2.
* Improvement: Updated Freemius SDK version 2.5.12.

= 2.6.5 =

* Improvement: Compatibility to WordPress 6.3.1.
* Improvement: Address a reflected Cross-Site Scripting vulnerability from `icon-color` shortcode parameter.

= 2.6.4 = 

* Improvement: Updated Freemius SDK version 2.5.10 to address a Reflected Cross-Site Scripting vulnerability via fs_request_get.

= 2.6.3 =

* Improvement: Added the shortcode attribute `color` to set the custom color for the text. E.g. `[copy content="Custom Text.." color="#2dcd78"]Copy me![/copy]`
* Improvement: Added the shortcode attribute `icon-color` to set the custom color for the icon. E.g. `[copy content="Custom Text.." style="icon" icon-color="#9437f6"][/copy]`
* Improvement: Added the shortcode attribute `style` to show the icon. E.g. `[copy content="Custom Text.." style="icon"][/copy]`

= 2.6.2 =

* Improvement: Compatibility to WordPress 6.1.1.
* Improvement: Updated Freemius SDK wit version 2.5.3.
* Improvement: Added the support to copy emojis from the content into the clipboard.

= 2.6.1 =

* Improvement: Avoided the button copy to clipboard for Google Docs and Email format.
* Improvement: Avoided the "Copy" text from the clipboard.

= 2.6.0 =

* New: Added the Copy Format setting to copy the content for Google Docs or Email.
* Improvement: Added filter `copy_the_code_shortcode_atts` for shortcode to redirect the user to another page after copying the content to the clipboard.
* Improvement: Added the support to Trim Lines with filter `copy_the_code_localize_vars`.
* Improvement: Added translation support for pt_BR language.

= 2.5.0 =

* New: Added a link to easily upgrade to the premium version which was recently not easily visible. 

* Improvement: Improve the default button style.
* Improvement: Improve the welcome message by adding the plugin name. Thanks @Andre

= 2.4.2 =

* Fix: The message "You are just one step away.." is not disappearing after clicking on button. Thanks @MingHong

= 2.4.1 =

* Improvement: Added the sub-menus page links into the post type screen.
* Improvement: Removed the sub-menus Dashboard, Contact Us, Upgrade, and Support Forum.

= 2.4.0 =

* New: Integrated the Freemius library for automatic updates, upgrade and for quick support.
* Improvement: Add new dashboard admin page for Copy Anything to Clipboard.
* Improvement: Deprecated the Shortcode admin page which is no longer used.

= 2.3.5 =

* Improvement: UI improvements in the shortcode information admin page.
* Improvement: Added the title tag support for the shortcode. So, Whenever the user hovers on shortcode text then it'll see the title.
* Improvement: Avoid the remove spaces support by setting false values to the parameter remove_spaces. Use filter from code snippet  https://gist.github.com/7c086cdf0837f5864596945086c603c8

= 2.3.4 =

* Improvement: Keep the welcome message only for first time user activate.

= 2.3.3 =

* Improvement: Added a welcome message to the user for smooth plugin onboarding.

= 2.3.2 =

* Improvement: Improve the code with PHPCS fixes.

= 2.3.1 =

* Improvement: Keep the tab spaces while copy to clipboard. Thanks @marius84
* Improvement: Users can now share non-personal usage data to help us test and develop better products. 

= 2.3.0 =

* New: Added support to redirect user after copy to clipboard. Thanks @zecke Read more https://wp.me/P4Ams0-aAq

= 2.2.2 =

* Improvement: Getting multiple white spaces in Gutenberg editor.
* Fix: The add new link was wrong which navigate to invalid page.

= 2.2.1 =

* Improvement: Move the parent menus as submenu in settings menu.

= 2.2.0 =

* New - Added shortcode [copy] to copy the content. E.g. [copy]12345[/copy]. Read more at https://clipboard.agency/doc/

= 2.1.1 =

* Improvement - Compatibility to WordPress 5.7.

= 2.1.0 =

* New: Added filter `copy_the_code_localize_vars` to allow to copy the content as HTMl instead of text.

= 2.0.0 =

* Tweak: Create a new post copy to clipboard post depends on the old user settings.
* Deprecated: Removed the filter `copy_the_code_enabled` which is no more useful.
* Deprecated: Removed the option `Copy the content` which is no more useful. Will add the support though filter if required.
* New: Added custom post type support to add multiple copy to clipboard buttons with different selectors and styles.
* New: Improve the UI with live preview.
* New: Added the new `SVG Icon` button style to show the SVG icon instead of button.
* New: Added inside and outside position support for the new style SVG Icon.
* New: Added the new `Cover` style to copy the small element in which we could not add the copy button. Such as Emoji and Symbols.

= 1.8.0 =

* New: Set the `Copy Content As` default option with `text`.
* Improvements: Converted the `<br>` tags into the new line if the option "Copy Content As" selected as `Text`.
* Improvements: Converted the `<div>` tags into the new line if the option "Copy Content As" selected as `Text`.
* Improvements: Converted the `<p>` tags into the new line if the option "Copy Content As" selected as `Text`.
* Improvements: Converted the `<li>` tags into the new line if the option "Copy Content As" selected as `Text`.
* Improvements: Remove the white spaces and trim the content if the option "Copy Content As" selected as `Text`.
* Fix: Copy the content as text works different on Chrome, Firefox and Internet Explorer browsers.

= 1.7.5 =

* Fix: The `<br>` tag converted into the next line. Select the `Text` from option `Copy Content As`. Reported by Konrad.
* Fix: Single level selector copies the selector in the clipboard. Reported by Seb.

= 1.7.4 =

* Fix: Nested selectors was not working due to mismatch the copy button position.

= 1.7.3 =

* Fix: The `<br>` tags was not copied as new line.  Reported by @psanger.

= 1.7.2 =

* Improvement: Removed unwanted code.

= 1.7.1 =

* Improvement: Updated Freemius SDK library with version 2.3.2.
* Improvement: Added the latest new section.
* Fix: The submit button is not visible form the settings page. Reported by Nicolas Tizio

= 1.7.0 =

* New: Added General & Style tabs.

= 1.6.1 =

* Improvement: Added WordPress 5.4 compatibility.

= 1.6.0 =

* New: Added filter `copy_the_code_default_page_settings` to change the default page settings.
* New: Added filter `copy_the_code_page_settings` to change the page settings.

= 1.5.0 =

* New: Added option 'Button Text' to set the default button text. Default 'Copy'.
* New: Added option 'Button Copy Text' to set the button text after click on copy. Default 'Copied!'.
* New: Added option 'Button Title' to set the default button title which appear on hover on button. Default 'Copy to Clipboard'.
* New: Added option 'Button Position' to set the button position. Inside or outside the selector. Default 'inside'.
* Improvement: Added support for Internet Explorer devices. Reported by @rambo3000

= 1.4.1 =

* Fix: Added support for IOS devices. Reported by @radiocure1

= 1.4.0 =
* New: Added option 'Copy Content As' to copy the content as either HTML or Text. 

= 1.3.1 =
* Improvement: Updated the strings and compatibility for WordPress 5.0.

= 1.3.0 =
* New: Added support, contact links.

= 1.2.0 =
* New: Added settings page for customizing the plugin. Added option `selector` to set the JS selector. Default its `<pre>` html tag.

= 1.1.0 =
* Fix: Removed `Copy` button markup from the copied content from the clipboard.

= 1.0.0 =
* Initial release.
