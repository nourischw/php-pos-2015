<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS') }}common.css" />
<!--[if lt IE 9]>
    <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
    <script type='text/javascript' src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
<![endif]-->
<?php $css = (isset($css)) ? intval($css) : 0; ?>
@if ($css & Config::get('css.CSS_LIST'))
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS') }}list.css" />
@endif
@if ($css & Config::get('css.CSS_FILE'))
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS'),$current_page }}.css" />
@endif
@if (isset($page_css))
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.CSS'),$page_css }}.css" />
@endif