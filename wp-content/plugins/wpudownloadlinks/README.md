Download links
=================

A Framework to create your WordPress plugin.

How to install :
---

* Put this folder to your wp-content/plugins/ folder.
* Activate the plugin in "Plugins" admin section.

How to configure :
---

* Go to the first submenu element of "Download links" admin section.
* Set the max number of downloads.
* Set the path for the file.

How to create a new link :
---

    global $wpuDownloadLinks;
    $link_id = $wpuDownloadLinks->generateLink( 'Infos. PayPal code, Firstname-name, etc' );

How to obtain a link URL :
---

    global $wpuDownloadLinks;
    $link_url = $wpuDownloadLinks->getLinkURLByID( 1 );
