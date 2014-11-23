<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<?php
if (isset($header)) {
    echo $header;
}
?>
<title>
<?php
if (isset($title)) {
    echo $title;
}
?>
</title>
</head>
<body>
<?php
if (isset($content_header)) {
    echo $content_hedaer;
}
if (isset($content_body)) {
    echo $content_body;
}
if (isset($content_footer)) {
    echo $content_footer;
}
?>
</body>
<?php
if (isset($footer)) {
    echo $footer;
}
?>
</html>
