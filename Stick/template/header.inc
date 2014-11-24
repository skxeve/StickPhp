<?php
if (isset($meta)) {
    if (!is_array($meta)) {
        $css = array($meta);
    }
    foreach ($meta as $item) {
        echo $item . "\n";
    }
}
if (isset($css)) {
    if (!is_array($css)) {
        $css = array($css);
    }
    for ($i = 0; $i < count($css); $i++) {
        echo '<link type="text/css" rel="stylesheet" href="' . $css[$i] . '" />' . "\n";
    }
}
if (isset($js)) {
    if (!is_array($js)) {
        $css = array($js);
    }
    for ($i = 0; $i < count($js); $i++) {
        echo '<script type="text/javascript" src="' . $js[$i] . '"></script>' . "\n";
    }
}
