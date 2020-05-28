<?php
namespace Kalturastream\Media\Renderer;

use Omeka\Api\Representation\MediaRepresentation;
use Zend\Uri\Http as HttpUri;
use Omeka\Media\Renderer\RendererInterface;
use Zend\View\Renderer\PhpRenderer;

class Kalturastream implements RendererInterface
{
	const WIDTH = 799;
    const HEIGHT = 449;
    const ALLOWFULLSCREEN = true;
	const AUTOPLAY = "";
	const CONTROLS = "controls";
	const START = 0;
 	const END = 18;
	const PARTNER = "1751071";
	const UICONF = "26683571";
   
    public function render(PhpRenderer $view, MediaRepresentation $media, array $options = [])
    {
		$item=$media->item();
		$Streamdata = $media->mediaData();
        if (isset($Streamdata['url'])) {
            $options['url'] = $Streamdata['url'];
        }
        if (isset($Streamdata['partner_id'])) {
            $options['partner_id'] = $Streamdata['partner_id'];
			}else{
			$options['partner_id'] = self::PARTNER;
        }
        if (isset($Streamdata['uiconf'])) {
            $options['uiconf'] = $Streamdata['uiconf'];
			}else{
			$options['uiconf'] = self::UICONF;
        }

        if (isset($Streamdata['start'])) {
            $options['start'] = $Streamdata['start'];
			}else{
			$options['start'] = $media->value('ebuc:start',['default' => $item->value('ebuc:start',['default' => self::START])]);
        }
        if (isset($Streamdata['end'])) {
            $options['end'] = $Streamdata['end'];
			}else{
			$options['end'] = $media->value('ebuc:end',['default' => $item->value('ebuc:end',['default' => self::END])]);
        }
        if (isset($Streamdata['width'])) {
            $options['width'] = $Streamdata['width'];
			}else{
			$options['width'] = $media->value('ebuc:width',['default' => $item->value('ebuc:width',['default' => self::WIDTH])]);
        }
        if (isset($Streamdata['height'])) {
            $options['height'] = $Streamdata['height'];
			}else{
			$options['height'] = $media->value('ebuc:height',['default' => $item->value('ebuc:height',['default' => self::HEIGHT])]);
        }
        if (!isset($options['allowfullscreen'])) {
            $options['allowfullscreen'] = self::ALLOWFULLSCREEN;
        }
        if (!isset($options['autoplay'])) {
            $options['autoplay'] = self::AUTOPLAY;
        }
        if (!isset($options['controls'])) {
            $options['controls'] = self::CONTROLS;
        }
//		$view->headLink()->prependStylesheet($view->assetUrl('css/videojs.css', 'Omeka'));
//		$view->headLink()->prependStylesheet($view->assetUrl('css/moo.css', 'Omeka'));
		$view->headScript()->appendFile($view->assetUrl('js/utils/pfUtils.js', 'Omeka'));
//        $view->headScript()->appendFile($view->assetUrl('js/videojs/video.js', 'Omeka'));
//        $prefixUrl = $view->assetUrl('js/openseadragon/images/', 'Omeka');
//		echo "<br/>".$item->value('ebuc:startNormalPlayTime',[]);
//		echo " -- ".$item->value('ebuc:endNormalPlayTime',[]);
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
		$video = $video.
			sprintf('<script src="https://cdnapisec.kaltura.com/p/%s/sp/%s00/embedIframeJs/uiconf_id/%s/partner_id/%s"></script>
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
						"autoPlay": false,
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
						//kdp.sendNotification("doPlay");
						kdp.setKDPAttribute("scrubber", "visible", true);	
						kdp.setKDPAttribute("durationLabel", "visible", false);
						kdp.sendNotification("doSeek", start);

						//kdp.sendNotification("doPlay");

					});

					// Add a binding for when seek is completed: 
					//kdp.kBind("playerSeekEnd", function(){
					//Pause player 
						//setTimeout(function(){
						//	kdp.sendNotification("doPause" );
						//},5000)
					//});

					kdp.kBind( "playerUpdatePlayhead", function(event){
						var curTime = Math.floor(JSON.stringify(event));
						var calcdate = new Date(null);
						calcdate.setSeconds(JSON.stringify(event));
						
						$("#pos-%s").html("<b>"+calcdate.toISOString().substr(11,8));

						if (curTime >= fend || curTime < start) {
							kdp.sendNotification("doSeek", start);
							//kdp.sendNotification("doPause");
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
				$options['url'],
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
}
