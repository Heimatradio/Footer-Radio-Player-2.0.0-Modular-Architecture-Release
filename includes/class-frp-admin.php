<?php

if (!defined('ABSPATH')) {
    exit;
}

class FRP_Admin {

    private static $instance = null;

    public static function instance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_enqueue_scripts', [$this, 'admin_assets']);
    }

    public function admin_assets($hook) {
        if ($hook !== 'settings_page_frp-settings') return;
        wp_enqueue_media();
    }

    public function register_settings() {

        $fields = [
            'frp_stream_url',
            'frp_server_url',
            'frp_stream_type',
            'frp_popup_url',
            'frp_station_name',
            'frp_fallback_text',
            'frp_color_bg',
            'frp_color_text',
            'frp_color_button',
            'frp_cover_url'
        ];

        foreach ($fields as $field) {
            register_setting('frp_settings_group', $field);
        }
    }

    public function add_settings_page() {
        add_options_page(
            'Footer Radio Player',
            'Footer Radio Player',
            'manage_options',
            'frp-settings',
            [$this, 'settings_page']
        );
    }

    public function settings_page() {
?>
<div class="wrap">
<h1>Footer Radio Player</h1>

<form method="post" action="options.php">
<?php settings_fields('frp_settings_group'); ?>

<table class="form-table">

<tr><th>Sender Name</th>
<td><input type="text" name="frp_station_name"
value="<?php echo esc_attr(get_option('frp_station_name','Live Radio')); ?>"
class="regular-text"></td></tr>

<tr><th>Stream URL</th>
<td><input type="text" name="frp_stream_url"
value="<?php echo esc_attr(get_option('frp_stream_url')); ?>"
class="regular-text"></td></tr>

<tr><th>Server Base URL</th>
<td><input type="text" name="frp_server_url"
value="<?php echo esc_attr(get_option('frp_server_url')); ?>"
class="regular-text"></td></tr>

<tr><th>Stream Type</th>
<td>
<select name="frp_stream_type">
<option value="shoutcast" <?php selected(get_option('frp_stream_type'),'shoutcast'); ?>>Shoutcast</option>
<option value="icecast" <?php selected(get_option('frp_stream_type'),'icecast'); ?>>Icecast</option>
</select>
</td></tr>

<tr><th>Fallback Text</th>
<td><input type="text" name="frp_fallback_text"
value="<?php echo esc_attr(get_option('frp_fallback_text','Live Stream')); ?>"
class="regular-text"></td></tr>

<tr><th>Popup URL</th>
<td><input type="text" name="frp_popup_url"
value="<?php echo esc_attr(get_option('frp_popup_url')); ?>"
class="regular-text"></td></tr>

<tr><th>Background Color</th>
<td><input type="color" name="frp_color_bg"
value="<?php echo esc_attr(get_option('frp_color_bg','#7c1212')); ?>"></td></tr>

<tr><th>Text Color</th>
<td><input type="color" name="frp_color_text"
value="<?php echo esc_attr(get_option('frp_color_text','#ffffff')); ?>"></td></tr>

<tr><th>Button Color</th>
<td><input type="color" name="frp_color_button"
value="<?php echo esc_attr(get_option('frp_color_button','#ffffff')); ?>"></td></tr>

<tr><th>Fallback Cover</th>
<td>
<input type="hidden" id="frp_cover_url" name="frp_cover_url"
value="<?php echo esc_attr(get_option('frp_cover_url')); ?>">

<div id="frp-cover-preview" style="margin-bottom:10px;">
<?php if (get_option('frp_cover_url')) : ?>
<img src="<?php echo esc_url(get_option('frp_cover_url')); ?>"
style="max-width:120px;height:auto;">
<?php endif; ?>
</div>

<button type="button" class="button" id="frp-upload-cover">
Bild auswählen
</button>
</td></tr>

</table>

<?php submit_button(); ?>
</form>

<hr>

<h2>Popup Player Code Generator</h2>
<p>Kopiere diesen Code in eine index.php ins Verzeichnis der Popup URL:</p>

<textarea id="frp-popup-code"
style="width:100%;height:300px;font-family:monospace;"><?php
echo esc_textarea($this->generate_popup_code());
?></textarea>

<p>
<button type="button" class="button button-primary" id="frp-copy-code">
Code kopieren
</button>
<span id="frp-copy-success" style="margin-left:10px;display:none;">
✔ Kopiert
</span>
</p>

</div>

<script>
document.addEventListener('DOMContentLoaded', function(){

var uploadBtn=document.getElementById('frp-upload-cover');
var preview=document.getElementById('frp-cover-preview');
var inputField=document.getElementById('frp_cover_url');

if(uploadBtn){
uploadBtn.addEventListener('click', function(e){
e.preventDefault();
var frame=wp.media({
title:'Fallback Cover auswählen',
button:{text:'Bild verwenden'},
multiple:false
});
frame.on('select', function(){
var attachment=frame.state().get('selection').first().toJSON();
inputField.value=attachment.url;
preview.innerHTML='<img src="'+attachment.url+'" style="max-width:120px;height:auto;">';
});
frame.open();
});
}

var copyBtn=document.getElementById('frp-copy-code');
var codeArea=document.getElementById('frp-popup-code');
var successMsg=document.getElementById('frp-copy-success');

if(copyBtn){
copyBtn.addEventListener('click', function(){
codeArea.select();
navigator.clipboard.writeText(codeArea.value).then(function(){
successMsg.style.display='inline';
setTimeout(function(){successMsg.style.display='none';},2000);
});
});
}

});
</script>

<?php
}

private function generate_popup_code(){

$stream   = esc_url(get_option('frp_stream_url'));
$server   = rtrim(get_option('frp_server_url'), '/');
$type     = esc_attr(get_option('frp_stream_type','shoutcast'));
$station  = esc_html(get_option('frp_station_name','Live Radio'));
$fallback = esc_html(get_option('frp_fallback_text','Live Stream'));
$cover    = esc_url(get_option('frp_cover_url'));
$bg       = esc_attr(get_option('frp_color_bg','#7c1212'));
$text     = esc_attr(get_option('frp_color_text','#ffffff'));
$button   = esc_attr(get_option('frp_color_button','#ffffff'));

if(empty($cover)){
$cover='https://via.placeholder.com/160x160?text=Cover';
}

return "<?php
if(isset(\$_GET['action']) && \$_GET['action'] === 'title'){

    \$server = '{$server}';
    \$type   = '{$type}';
    \$fallback = '{$fallback}';

    if(empty(\$server)){
        echo \$fallback;
        exit;
    }

    if(\$type === 'icecast'){
        \$json = @file_get_contents(\$server . '/status-json.xsl');
        if(\$json){
            \$data = json_decode(\$json, true);
            if(isset(\$data['icestats']['source']['title'])){
                echo trim(\$data['icestats']['source']['title']);
                exit;
            }
        }
    }else{
        \$xml = @simplexml_load_file(\$server . '/stats?sid=1');
        if(\$xml && isset(\$xml->SONGTITLE)){
            echo trim((string)\$xml->SONGTITLE);
            exit;
        }
    }

    echo \$fallback;
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<title>{$station}</title>
<style>
body{margin:0;background:{$bg};color:{$text};font-family:Arial;text-align:center;padding:20px;}
img{width:160px;height:160px;object-fit:cover;border-radius:12px;margin-bottom:15px;transition:opacity 0.4s ease;}
#title-wrapper{width:100%;overflow:hidden;height:22px;margin:10px 0 20px 0;}
#title{display:inline-block;white-space:nowrap;padding-left:100%;animation:scroll 18s linear infinite;}
@keyframes scroll{from{transform:translateX(0);}to{transform:translateX(-100%);}}
.controls{margin-top:20px;display:flex;justify-content:center;align-items:center;gap:40px;}
.play-btn,.volume-btn{background:none;border:none;cursor:pointer;}
.play-btn svg{width:70px;height:70px;}
.volume-btn svg{width:50px;height:50px;}
.volume-wrapper{position:relative;display:flex;flex-direction:column;align-items:center;}
#volumeSlider{writing-mode:bt-lr;-webkit-appearance:slider-vertical;width:8px;height:110px;position:absolute;bottom:55px;opacity:0;transition:opacity 0.3s ease;}
.volume-wrapper:hover #volumeSlider{opacity:1;}
@media (max-width:992px){.volume-wrapper{display:none;}}
</style>
</head>
<body>

<img id='cover' src='{$cover}' alt='Cover'>
<h2>{$station}</h2>

<div id='title-wrapper'>
<div id='title'>{$fallback}</div>
</div>

<div class='controls'>
<button id='playBtn' class='play-btn'>
<svg viewBox='0 0 24 24'>
<circle cx='12' cy='12' r='11' fill='{$button}' opacity='0.2'/>
<polygon id='playIcon' points='10,8 17,12 10,16' fill='{$button}'/>
</svg>
</button>

<div class='volume-wrapper'>
<button class='volume-btn'>
<svg viewBox='0 0 24 24'>
<polygon points='3,9 7,9 12,5 12,19 7,15 3,15' fill='{$button}'/>
<path d='M15 9 C17 11,17 13,15 15' stroke='{$button}' stroke-width='2' fill='none'/>
</svg>
</button>
<input type='range' id='volumeSlider' min='0' max='1' step='0.01' value='0.9'>
</div>
</div>

<audio id='audio' preload='none'>
<source src='{$stream}' type='audio/mpeg'>
</audio>

<script>
var cover=document.getElementById('cover');
var titleEl=document.getElementById('title');
var audio=document.getElementById('audio');
var playBtn=document.getElementById('playBtn');
var playIcon=document.getElementById('playIcon');
var volumeSlider=document.getElementById('volumeSlider');
var lastTitle='';

audio.volume=volumeSlider.value;

volumeSlider.addEventListener('input',function(){
audio.volume=this.value;
});

playBtn.addEventListener('click',function(){
if(audio.paused){
audio.play();
playIcon.setAttribute('points','9,8 11,8 11,16 9,16 13,8 15,8 15,16 13,16');
}else{
audio.pause();
playIcon.setAttribute('points','10,8 17,12 10,16');
}
});

function fetchCover(title){
if(title.indexOf(' - ')===-1)return;
var parts=title.split(' - ');
var artist=parts[0].trim();
var song=parts[1].trim();
var query=encodeURIComponent(artist+' '+song);

fetch('https://itunes.apple.com/search?term='+query+'&entity=song&limit=5')
.then(r=>r.json())
.then(data=>{
if(data.results&&data.results.length>0){
var match=data.results.find(item=>
item.artistName.toLowerCase().includes(artist.toLowerCase())
);
if(!match)match=data.results[0];
if(match.artworkUrl100){
var artwork=match.artworkUrl100.replace('100x100bb','400x400bb');
cover.style.opacity=0;
setTimeout(function(){
cover.src=artwork;
cover.style.opacity=1;
},200);
}
}
}).catch(()=>{});
}

function fetchTitle(){
fetch('?action=title')
.then(r=>r.text())
.then(t=>{
if(t!==lastTitle){
lastTitle=t;
titleEl.innerText=t;
fetchCover(t);
}
}).catch(()=>{});
}

fetchTitle();
setInterval(fetchTitle,15000);
</script>

</body>
</html>";
}

}