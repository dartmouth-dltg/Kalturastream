<?php

namespace Kalturastream\Media\Renderer;

use Omeka\Api\Representation\MediaRepresentation;
use Omeka\Media\Renderer\RendererInterface;
use Zend\View\Renderer\PhpRenderer;

class Kalturastream implements RendererInterface {
  const WIDTH = 799; // Deprecated
  const HEIGHT = 449; // Deprecated
  const ALLOWFULLSCREEN = true;
  const AUTOPLAY = "";
  const CONTROLS = "controls";
  const START = 0;
  const END = 72000; // 20 hours as temp fix for end issue
  const PARTNER = "1751071";
  const UICONF = "26683571";

  public function render(PhpRenderer $view, MediaRepresentation $media, array $options = []) {
    $item = $media->item();
    $Streamdata = $media->mediaData();
    if (isset($Streamdata['o:source'])) {
      $options['o:source'] = $Streamdata['o:source'];
    }
    if (isset($Streamdata['partner_id'])) {
      $options['partner_id'] = $Streamdata['partner_id'];
    } else {
      $options['partner_id'] = self::PARTNER;
    }
    if (isset($Streamdata['uiconf'])) {
      $options['uiconf'] = $Streamdata['uiconf'];
    } else {
      $options['uiconf'] = self::UICONF;
    }

    if (isset($Streamdata['start'])) {
      $options['start'] = $this->minutes_to_seconds($Streamdata['start']);
    } else {
      $options['start'] = $media->value('ebuc:start', ['default' => $item->value('ebuc:start', ['default' => self::START])]);
    }
    if (isset($Streamdata['end'])) {
      $options['end'] = $this->minutes_to_seconds($Streamdata['end']);
    } else {
      $options['end'] = $media->value('ebuc:end', ['default' => $item->value('ebuc:end', ['default' => self::END])]);
    }
    if (isset($Streamdata['width'])) {
      $options['width'] = $Streamdata['width'];
    } else {
      $options['width'] = $media->value('ebuc:width', ['default' => $item->value('ebuc:width', ['default' => ''])]);
    }
    if (isset($Streamdata['height'])) {
      $options['height'] = $Streamdata['height'];
    } else {
      $options['height'] = $media->value('ebuc:height', ['default' => $item->value('ebuc:height', ['default' => self::HEIGHT])]);
    }
    if (!isset($options['allowfullscreen'])) {
      $options['allowfullscreen'] = self::ALLOWFULLSCREEN;
    }


    if (isset($Streamdata['autoplay'])) {
      $options['autoplay'] = $Streamdata['autoplay'];
    } else {
      $options['autoplay'] = self::AUTOPLAY;
    }

    if (!isset($options['controls'])) {
      $options['controls'] = self::CONTROLS;
    }

    $view->headScript()->appendFile($view->assetUrl('js/pfUtils.js','Kalturastream'));
    
    if (!empty($options['width'])) {
      $video =
        sprintf(
          '<div id="vid_player%s" style="width:%spx; height:%spx" itemprop="video" itemscope itemtype="http://schema.org/VideoObject"><span itemprop="duration" content="120"></span></div>
  			<div class="debug">Playback: <span id="pos-%s"> 0:00:00 </span> - <span id="finalend-%s"> 0:00:00 </span></div>
  			<script>jQuery(".debug").hide();</script>	',
          $options['start'],
          $options['width'],
          $options['height'],
          $options['start'],
          $options['end']
        );
    } else {
      // Create a responsive container
      $video =
        sprintf(
          '<div style="width:100%%; position:relative; display: inline-block;">
              <div style="margin-top: 56.25%%;"></div>
              <div id="vid_player%s" style="position:absolute; top:0; left:0; right: 0; bottom: 0;" itemprop="video" itemscope itemtype="http://schema.org/VideoObject"><span itemprop="duration" content="120"></span></div>
          </div>
  			<div class="debug">Playback: <span id="pos-%s"> 0:00:00 </span> - <span id="finalend-%s"> 0:00:00 </span></div>
  			<script>jQuery(".debug").hide();</script>	',
          $options['start'],
          $options['width'],
          $options['height'],
          $options['start'],
          $options['end']
        );
    }
    $video .= sprintf('<script src="https://cdnapisec.kaltura.com/p/%s/sp/%s00/embedIframeJs/uiconf_id/%s/partner_id/%s"></script>
			<script>
		  	kWidget.embed({
			 	"targetId": "vid_player%s",
			 	"wid": "_%s",
				"uiconf_id" : "%s",
				"entry_id" : "%s",
				"flashvars":{ 		"controlBarContainer": {
			        	"plugin": true,
			        	"hover": true,
			   	 		},    
						"autoPlay": "%s",
				},

				readyCallback: function( playerId ){
					var kdp = document.getElementById( playerId );
					var startx = new Date(null);
					startx.setSeconds(%s);
					var endx = new Date(null);
					endx.setSeconds(%s);
					var start = %s;
					var fend = %s;
					// Wait for "media ready" before starting playback: 
					$("#pos-%s").html(startx.toISOString().substr(11,8));
					$("#finalend-%s").html(endx.toISOString().substr(11,8));

					kdp.kBind("mediaReady", function(){
						kdp.setKDPAttribute("scrubber", "visible", true);	
						kdp.setKDPAttribute("durationLabel", "visible", false);
						kdp.sendNotification("doSeek", start);
					});


					kdp.kBind( "playerUpdatePlayhead", function(event){
						var curTime = Math.floor(JSON.stringify(event));
						var calcdate = new Date(null);
						calcdate.setSeconds(JSON.stringify(event));
						
						$("#pos-%s").html("<b>"+calcdate.toISOString().substr(11,8));

						if (curTime >= fend || curTime < start) {
							kdp.sendNotification("doSeek", start);
						} 
					});
					}
					});

			</script>',
        $options['partner_id'],
        $options['partner_id'],
        $options['uiconf'],
        $options['partner_id'],

        $options['start'],
        $options['partner_id'],
        $options['uiconf'],

        $options['o:source'],
        $options['autoplay'],
        $options['start'],
        $options['end'],
        $options['start'],
        $options['end'],
        $options['start'],
        $options['end'],
        $options['start']

      );
    return $video;
  }
  public function minutes_to_seconds($input){
    if (strpos($input, ':') !== FALSE) {
      $timeparts = \explode(':', $input);
      return strval(60 * $timeparts[0] + $timeparts[1]);
    }
    else{
      return $input;
    }

  }

}
