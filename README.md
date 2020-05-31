# Kalturastream
Kaltura stream allows users to add Kaltura videos as media attached to Omeka Items.


## Setup ##
Navigate to `admin/module` and click the Configure button next to **Kalturastream**  
Enter your institution's Kaltura Partner ID and your chosen UI Config.  **45416971** is a good default for most 
instances. 

## Use ##

Kalturastream provides the abilty to stream videos already uploaded to the Kaltura server.
Log in to your account to see the listing of all available videos.  New videos may be added
by clicking the **CREATE** button and following the prompts.  Video may be uploaded
from your desktop, from a known URL, or a Bulk Upload process described 
[here](https://corp.kaltura.com/blog/kalturas-bulk-upload/).

The table display of available videos will show a thumbnail, a name, and an ID, a creation date, duration, number of 
plays, date and status.

To stream a Kaltura video though Omeka you'll need the **ID** and the **Duration**.

The default player is provided in the module configuration, but others may be seen by clicking on the **STUDIO** tab of the 
Kaltura list page. Note the **ID** number of the player you'd prefer.

___

- Create an Item in the normal way on the Omeka site, then attach a media object by editing the Item and clicking on the 
Media tab.  
- Select KalturaStream from the **Add Media** block on the right hand side of the page.  
Fill the required fields, entering the **ID** from Kaltura as the **Video ID**
- Save
___

The new Media will be shown on save and can be adjusted by clicking the **Edit Media** button, then clicking the 
**Media** tab.









