<script> var ROOT = "{{ Config::get('path.ROOT') }}"; </script>
<script src="{{ Config::get('path.JS_LIBS') }}jquery.js" type="text/javascript"></script>
<script src="{{ Config::get('path.JS_LIBS') }}jquery-ui-1.11.4/jquery-ui.min.js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js" type="text/javascript"></script>
<script src="{{ Config::get('path.ROOT') }}app/js/libs/bootstrap-lightbox.js" type="text/javascript"></script>
<link media="all" type="text/css" rel="stylesheet" href="{{ Config::get('path.JS_LIBS') }}jquery-ui-1.11.4/jquery-ui.min.css" />
<script src="{{ Config::get('path.JS') }}common.js" type="text/javascript"></script>   

<?php $js = (isset($js)) ? $js : 0; ?>
@if ($js & Config::get('js.JS_FORM_VALIDATOR'))
<script src="{{ Config::get('path.JS') }}form_validator.js"></script>
@endif
@if ($js & Config::get('js.JS_LIST'))
<script src="{{ Config::get('path.JS') }}list.js"></script>
@endif
@if ($js & Config::get('js.JS_LIST_OBJ'))
<script src="{{ Config::get('path.JS') }}list_obj.js"></script>
@endif
@if ($js & Config::get('js.JS_JSSHA'))
<script src="{{ Config::get('path.JS_LIBS') }}jssha/src/sha.js"></script>
@endif
@if ($js & Config::get('js.JS_FILE'))
<script src="{{ Config::get('path.JS'),$current_page }}.js"></script>
@endif
@if (isset($page_js))
<script src="{{ Config::get('path.JS'),$page_js }}.js"></script>
@endif