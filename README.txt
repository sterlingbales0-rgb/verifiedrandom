VerifiedRandom.org — Updated Website Package
============================================

This package contains the updated website with all requested changes:

  1. Multiple numbers can be drawn in one session; every number is recorded
     with its own UTC timestamp and all of them are saved under ONE
     verification code when Save is pressed. The /verify page shows every
     number with its timestamp under that code, plus a VERIFIED status badge.
  2. After Save, the button turns into "Copy Code" which copies the
     verification code to the clipboard.
  3. Mobile layout fixed — the old fixed pixel/vh heights (70vh generator
     box inside a 500px container, 16vh result rows) caused the generator
     to overflow and overlap the text below. All fixed heights are gone;
     the page now uses natural flow and a single copy of the text.
  4. Timestamps now come from the SERVER clock in UTC (not the visitor's
     device clock), so the time shown at draw time always matches the time
     stored and displayed on /verify.
  5. Hidden admin page at /tghbusagd123 (password: Msb5f9123!):
     - noindex/nofollow (meta tag + X-Robots-Tag header), obscure title
     - add entries: Name + Max Number + Desired Result
     - when a homepage drawing uses a Name and Max number that both match
       an entry exactly, the desired number is returned instead of a random
       one, and it behaves as a normal drawing (code, UTC timestamps,
       VERIFIED status on /verify)
     - entries can be drawn again while they remain listed; delete an entry
       to stop it from being duplicated
     - change the password in tghbusagd123.php (VR_ADMIN_PASSWORD constant)

WHAT'S IN THIS PACKAGE
----------------------
  index.php             Homepage (generator + Save/Copy Code)
  verify.php            Verification page (supports /verify?code=XXXXXX links)
  api.php               JSON API (draw / save / verify) — server-side UTC time
  tghbusagd123.php      Hidden admin page (desired results)
  inc/db.php            SQLite database bootstrap (auto-creates tables)
  contact.html          Contact page (still posts to your existing contactForm.php)
  about.html            About page
  terms.html            Terms of Service page
  js/                   Front-end scripts (api-client, frontPage, verifyPage, adminPage)
  styles/               Stylesheets (base, front, verify, contact, admin)
  .htaccess             Makes /verify and /tghbusagd123 work as clean URLs,
                        blocks web access to data/ and inc/
  data/                 SQLite database folder (auto-created file lives here;
                        a .htaccess inside blocks direct downloads)

UPLOAD INSTRUCTIONS (cPanel File Manager on GoDaddy)
----------------------------------------------------
1. In cPanel File Manager, open public_html for verifiedrandom.org.
2. Upload this whole package (Upload the zip, right-click, Extract).
   If asked to overwrite existing files with the same names, allow it.
3. KEEP your existing contactForm.php (the contact form still posts to it).
4. After confirming the new site works, these OLD files can be deleted:
      frontPage.js        (old version, at the web root)
      verifyPage.js       (old version, at the web root)
      styles/stylesFront.css, styles/stylesFooter.css,
      styles/stylesHeader.css, styles/stylesVerify.css,
      styles/stylesContact.css
   The new site does not use them.
5. Visit https://verifiedrandom.org and test:
      - draw a few numbers, press Save, press Copy Code
      - check the code at https://verifiedrandom.org/verify
      - test the hidden page at https://verifiedrandom.org/tghbusagd123
        (password: Msb5f9123!)
      - check the homepage on your phone (overlap is fixed)

REQUIREMENTS / NOTES
--------------------
- PHP with the pdo_sqlite extension (enabled by default on cPanel hosting).
  The database file (data/verifiedrandom.db) is created automatically on
  first use. If you ever want a fresh database, delete that file.
- If PHP cannot write to data/, set the data folder permissions to 775
  (right-click > Change Permissions in File Manager).
- The database starts empty — verification codes created on the OLD site
  (stored in your old MySQL database) are not carried over. If you need the
  old codes to keep working, keep the old verify system in place alongside,
  or let me know and the package can be adapted to your existing MySQL
  database instead of SQLite.
- The admin password is stored as a constant in tghbusagd123.php — change it
  there if you ever need to.
