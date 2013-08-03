<?php
$ua_analytics = get_option('wputh_ua_analytics');
if ($ua_analytics !== false && !empty($ua_analytics) && !in_array($ua_analytics, array('UA-XXXXX-X'))) {
?><script type="text/javascript">
var _gaq=_gaq || [];
_gaq.push(['_setAccount','<?php echo $ua_analytics; ?>']);
_gaq.push(['_trackPageview']);
(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:' == document.location.protocol ? 'https://ssl' : 'http://www')+'.google-analytics.com/ga.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(ga,s);})();
</script><?php
}
