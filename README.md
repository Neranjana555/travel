# Travenzo Travels — PHP + SQLite

Requirements: PHP 8.1+ with PDO SQLite, mbstring and OpenSSL. No Composer or Node build is needed.

Run locally from this directory:

    php -S 127.0.0.1:8080 router.php

Open http://127.0.0.1:8080. Admin: http://127.0.0.1:8080/admin.php

Fixed account defaults:

- Username: admin
- Password: Travenzo@2026!

Before public hosting, change the fixed account in config.php or set TRAVENZO_ADMIN_USER and TRAVENZO_ADMIN_HASH. Generate a hash with PHP password_hash. Never publish the default password.

The database is created automatically in storage/site.sqlite, seeded from the existing site and the attached 10-day poster. The 8-day itinerary and Gold/Premium packages are preserved. The 10-day group prices are USD 1,100 / 1,600 / 1,800 for 2 / 4 / 6 people. The poster’s “Horton Place” wording is preserved for admin verification.

Gmail delivery: set TRAVENZO_GMAIL_APP_PASSWORD to a Gmail app password for Travenzotravels@gmail.com. SMTP uses TLS on smtp.gmail.com:465; the From and To addresses are both Travenzotravels@gmail.com, and Reply-To is the visitor. No password is shipped. Without SMTP configuration or when delivery fails, enquiries are still stored and the visitor sees an accurate saved status. The admin dashboard shows delivery status. SMTP failure does not automatically retry.

Admin tools: update website data through normal fields for home, about, packages, destinations, gallery and contact. Upload JPG, PNG or WebP photos up to 5 MB, edit numeric package prices, and update both itineraries using destination/activity fields with add/remove day buttons. No HTML or JSON editing is required. The data is stored in SQLite site_fields and tours tables. Reviews can be approved, hidden or deleted; enquiries and subscribers can be viewed. Public text is escaped by the renderer. There are no invented published reviews.

Deployment: use Apache with AllowOverride enabled so .htaccess protects storage and internal files and chooses index.php as the default. For Nginx, deny storage, seed.json and all internal PHP files; only index.php, admin.php, reviews.php and contact.php are public entry points. Prefer TRAVENZO_DATA_DIR outside the web root for the SQLite database. Enable HTTPS and grant PHP write access to the data directory. Keep config.php private. Back up the SQLite file before updates.

The original index.html is preserved for reference, and redirects to index.php. Existing external photographs, fonts and icons remain dependent on their providers. The new itinerary poster is local.

Mobile and animations: tested in Edge at desktop, 390px and 320px widths. Touch navigation includes a backdrop and keyboard controls. Hero photos have a slow zoom; tour panels, expandable days and admin panels animate on interaction. Reduced-motion preferences disable animations. Admin photo uploads have previews.
`nTour management: Admin → Tour plans → Add tour plan creates and publishes a complete itinerary with its own name, description, group prices and days. Existing plans can be updated or deleted; deletion asks for confirmation. Removing every tour displays a personalised-trip enquiry prompt on the public site. Itinerary posters are attached to their original tours and are not inherited by newly created tours.

Itinerary posters: Admin → Tour plans → Itinerary poster. Choose a JPG, PNG or WebP file and click Save poster to add or replace it. Remove poster removes it from that tour after confirmation. New tours can include a poster during creation. Uploads have previews and are limited to 5 MB; configure PHP upload_max_filesize to at least 5M and post_max_size to at least 10M on the hosting server. Unused uploaded tour posters are deleted when replaced, removed, or their tour is deleted. The original bundled poster remains available.
