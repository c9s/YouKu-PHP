<?php
require_once( 'phpQuery/phpQuery.php' );
mb_internal_encoding('UTF-8');

class YouKu
{

    public $url;

    function __construct( $url ) { 
        $this->url  = $url;
    }

    /*
     * @method getEmbeddedFlash
     *
        Looking for
        <div class="item"><span class="label">html代码: </span> <input  id="link3" 
            type="text" value='<embed 
            src="http://player.youku.com/player.php/Type/Folder/Fid/16157772/Ob/1/Pt/0/sid/XMzA5Mjk1NTM2/v.swf" 
            quality="high" width="480" height="400" align="middle" 
            allowScriptAccess="always" allowFullScreen="true" mode="transparent" 
            type="application/x-shockwave-flash"></embed>' /><button 
            onclick="javascript:copyToClipboard('link3');">复制</button></div>
        </div>
    */
    function getEmbeddedFlash()
    {
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $this->url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        $html = curl_exec($ch);
        phpQuery::newDocument( $html );
        $elem = pq('#link3'); // in jQuery, this would return a jQuery object.  I'm guessing something similar is happening here with pq.
        $html = $elem->val();
        return $html;
    }

}

function get_youku_flash_html($url,$cache = false)
{
    if( preg_match( '/(\w+).html$/' , $url , $regs ) ) {
        $videoId = $regs[1];
        $videoCache = 'cache' . DIRECTORY_SEPARATOR . $videoId;
        if( $cache && file_exists( $videoCache ) )
            return file_get_contents( $videoCache );

        $youku = new YouKu( $url );
        $html = $youku->getEmbeddedFlash();
        file_put_contents( $videoCache , $html );
        return $html;
    }
}

