<?php
/*AudioShortCode*/
function mtheme_gen_audio( $atts ) {
   extract( shortcode_atts( array(
		'id' => "1",
		'title' 	=> "",
		'mp3' 	=> "",
		'm4a' 	=> "",
		'oga' => "",
      ), $atts ) );

$mp3_file= $mp3;
$m4a_file= $m4a;
$oga_file= $oga;

if ($mp3_file) { $mp3_ext ="mp3"; if ($m4a_file || $oga_file){ $mp3_sep=",";} }
if ($m4a_file) { $m4a_ext ="m4a"; if ($oga_file){ $m4a_sep=",";} }
if ($oga_file) { $oga_ext ="oga";  }

$files_used=$mp3_ext.$mp3_sep.$m4a_ext.$m4a_sep.$oga_ext;
$AudioID = dechex(time()).dechex(mt_rand(1,65535));
if ($files_used) {
ob_start();
?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function(){
	jQuery("#jquery_jplayer_<?php echo $AudioID; ?>").jPlayer({
		ready: function () {
			jQuery(this).jPlayer("setMedia", {
				<?php if ($mp3_file) echo 'mp3: "'.$mp3_file.'",'; ?>
				<?php if ($m4a_file) echo 'm4a: "'.$m4a_file.'",'; ?>
				<?php if ($oga_file) echo 'oga: "'.$oga_file.'",'; ?>
				end: ""
			}).jPlayer("stop");
		},
		play: function() {
			jQuery(this).jPlayer("pauseOthers");
		},
		swfPath: "<?php echo get_stylesheet_directory_uri(); ?>/js/html5player/",
		supplied: "<?php echo $files_used; ?>",
		cssSelectorAncestor: "#jp_interface_<?php echo $AudioID; ?>"
	});
});
//]]>
</script>

<div id="jquery_jplayer_<?php echo $AudioID; ?>" class="jp-jplayer"></div>
<div class="jp-audio jplayer-shortcode">
	<h3 class="jplayer-title"><?php echo $title; ?></h3>
	<div class="jp-type-single">	
		<div id="jp_interface_<?php echo $AudioID; ?>" class="jp-gui jp-interface">
			<ul class="jp-controls">
		          <li><a href="#" class="jp-play" tabindex="1">&#xe052;</a></li>
		          <li><a href="#" class="jp-pause" tabindex="1">&#xe053;</a></li>
		          <li><a href="#" class="jp-mute" tabindex="1" title="mute">&#xe098;</a></li>
		          <li><a href="#" class="jp-unmute" tabindex="1" title="unmute">&#xe099;</a></li>
			</ul>
			<div class="jp-progress">
				<div class="jp-seek-bar">
					<div class="jp-play-bar"></div>
				</div>
			</div>
			<div class="jp-time-holder">
			  <div class="jp-current-time"></div>
			</div>
			<div class="jp-volume-bar">
			  <div class="jp-volume-bar-value"></div>
			</div>
		</div>
	</div>
</div>
<?php
$content = ob_get_contents();
ob_end_clean();
return $content;
}
}
add_shortcode( 'audioplayer', 'mtheme_gen_audio' );
?>