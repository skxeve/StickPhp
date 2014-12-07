<?php
if (isset($js)) {
    if (!is_array($js)) {
        $css = array($js);
    }
    for ($i = 0; $i < count($js); $i++) {
        echo '<script type="text/javascript" src="' . $js[$i] . '"></script>' . "\n";
    }
}
