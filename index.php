<?php
error_reporting(0);
header('Content-Type: text/html; charset=utf-8');
ini_set('zlib.output_compression', 'On');
if (empty($_GET['error'])) $url = explode('/', htmlspecialchars(rawurldecode($_SERVER['REQUEST_URI'])));
else { $url[1] = 'posts'; $url[2] = '404'; }
if (!empty($url[1])) {
    if (empty($url[2])) $title = 'all posts';
    else $title = ucwords(str_replace('-', ' ', $url[2]));
} else $title = '';

$fortune = fopen('../protected/fortune.txt', 'r');
$line_number = rand(1, 4100);   // number of lines
for ($i = 1; ($line = fgets($fortune)) !== false; $i++) {
    if ($i == $line_number) {
        $fortune = rtrim($line);
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="description" content="The blog of a British computer science student." />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
        <title>Shobute <?php if(!empty($title)) echo "- $title"; ?></title>
        <link href="/static/style.css" rel="stylesheet" type="text/css" media="screen" />
        <link rel="icon" type="image/png" href="/static/favicon.gif" />
        <script type="text/javascript" src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS_HTML"></script>
        <script type="text/javascript">
            MathJax.Hub.Config({ "HTML-CSS": { linebreaks: { automatic: true } } });

          var _gaq = _gaq || [];
          _gaq.push(['_setAccount', 'UA-36440084-1']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

        </script>
    </head>
    <body>
        <div id="wrapper">
            <header>
                <h1>
                    <a href="/" class="logo">Shobute</a>
                    <a href="/posts/" id="flair" title="<?php
                        if (empty($_GET['error'])) echo $fortune;
                        else echo 'Uh oh, a 404 error.';
                    ?>">&#9752;</a>
                </h1>
                <hr />
            </header><?php
                if (empty($url[2])) {
                    $files = scandir('posts');
                    $postfiles = array();
                    $i = 0;
                    foreach ($files as $file) {
                        if ($file[0] != '.') {
                            $mtime = filemtime("posts/$file");
                            if (array_key_exists($mtime, $postfiles)) {
                                $mtime += ++$i;
                            }
                            $postfiles[$mtime] = $file;
                        }
                    }
                    krsort($postfiles);
                    $i = 1;
                    if (!empty($url[1])) echo '<h2>All Posts</h2>';
                    foreach ($postfiles as $t => $post) {
                        $time[0] = date('Y-m-d\TH:i:s\Z', $t);
                        $time[1] = date('Y-m-d', $t);
                        $title = ucwords(str_replace('-', ' ', $post));
                        if (empty($url[1])) {
                            echo "<article><h2><a href=\"/posts/$post\">$title</a></h2>";
                            echo file_get_contents("posts/$post");
                            echo "<footer><p>Last updated on <time title=\"$time[0]\">$time[1]</time>.</p></footer></article>";
                            if (++$i > 8) break;    # number of posts to show
                            echo '<hr />';
                        } else
                            echo "<p><time title=\"$time[0]\">$time[1]</time> <a href=\"/posts/$post\">$title</a></p>";
                    }
                } elseif (file_exists("posts/$url[2]")) {
                    $t = filemtime("posts/$url[2]");
                    $time[0] = date('Y-m-d\TH:i:s\Z', $t);
                    $time[1] = date('Y-m-d', $t);
                    $title = ucwords(str_replace('-', ' ', $url[2]));
                    echo "<article><h2><a href=\"/posts/$url[2]\">$title</a></h2>";
                    echo file_get_contents("posts/$url[2]");
                    echo "<footer><p>Last updated on <time title=\"$time[0]\">$time[1]</time>.</p></footer></article>";

                } else echo "<article><h2>404</h2><p>$fortune</p></article>";
            ?><footer><hr /><p>All content is <a href="http://creativecommons.org/publicdomain/zero/1.0/">public domain</a> unless otherwise stated. <a href="/posts/about-shobute" class="logo">Shobute</a> is powered by procrastination.</p></footer>
        </div>
    </body>
</html>
