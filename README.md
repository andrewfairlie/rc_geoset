rc_geoset
=========

Uses CloudFlare's geolocation to determine the default language on BoldMinded's Publisher


## Requirements
- BoldMinded Publisher
- CloudFlare properly set up for your domain with 'IP Geolocation' enabled

## Configuration
1. Install the extension
2. Go to the extension's settings page
3. You will see a list languages set up in BoldMinded
4. Add piped strings to force visitors from certain regions (using 2-character country codes - http://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)

You can leave the field for languages as blank if you like, and if we can't find a language to force for the user's country we'll fall back to Publisher's judgement.

This example demonstrates forcing users from the US or Canada to see the en-us version of the site, while all other users will use Publisher's judgement.

![Example](/example.png)
